# Security Patterns to Investigate

After identifying high-complexity controllers from the hotspot table, look for these specific anti-patterns in the source. Each entry describes what it looks like, why it is dangerous, and the correct fix.

---

## 1. Arbitrary Attribute Setting

**What it looks like:**

```php
if ($request->filled('attributeName') && $request->filled('attributeValue')) {
    $model->setAttribute($request->attributeName, $request->attributeValue);
}
```

**Why it is dangerous:** The user controls both the column name and the value. Even when the parent session is authorized via a policy, this allows setting sensitive attributes the caller was never meant to touch — `user_id`, `company_id`, `is_admin`, or any other column on the model.

**Fix:** Maintain an explicit allowlist of settable attribute names and validate against it:

```php
$validated = $request->validate([
    'attributeName' => ['required', 'string', 'in:coverage_id,manually_chosen'],
    'attributeValue' => ['nullable'],
]);
$model->setAttribute($validated['attributeName'], $validated['attributeValue']);
```

---

## 2. Unvalidated Mass Fill from Request

**What it looks like:**

```php
foreach ($riskDataCollection as $riskResultData) {
    $result->fill($riskResultData)->save();
}
```

**Why it is dangerous:** Every field in the request payload is written to the model. Even behind admin middleware, this allows callers to corrupt model state by injecting attributes the endpoint was not designed to accept. It also makes the contract of the endpoint implicit rather than explicit.

**Fix:** Either use a FormRequest to define the exact shape of each item, or be explicit about which fields are accepted:

```php
$result->fill(Arr::only($riskResultData, [
    'overview_text',
    'conditions_text',
    'effective_from',
    'points',
]))->save();
```

---

## 3. AJAX-Only Authorization

**What it looks like:**

```php
if (! $request->ajax()) {
    abort(Response::HTTP_FORBIDDEN, 'Access denied');
}
```

**Why it is dangerous:** The `ajax()` check only reads the `X-Requested-With` request header, which any HTTP client can set. This is not authentication or authorization — it is header inspection. Any user who can reach the route can forge this header.

**Fix:** Add a real policy or gate check in addition to, or instead of, the AJAX check:

```php
Gate::authorize('update', $model);
// or with a policy constant:
// Gate::authorize(ModelPolicy::UPDATE, $model);
```

---

## 4. Unvalidated IDs in a Pivot Loop

**What it looks like:**

```php
foreach ($request->input('related_ids') as $relatedId => $value) {
    $model->relatedItems()->updateExistingPivot((int) $relatedId, [
        'some_field' => $value,
    ]);
}
```

**Why it is dangerous:** The user supplies the pivot key directly. Without checking that each ID belongs to this model, a caller can update pivot records for models they are not authorized to modify.

**Fix:** Validate submitted IDs against the model's actual related set before iterating:

```php
$allowedIds = $model->relatedItems()->pluck('related_items.id');

foreach ($request->input('related_ids') as $relatedId => $value) {
    if (! $allowedIds->contains((int) $relatedId)) {
        abort(Response::HTTP_FORBIDDEN);
    }

    $model->relatedItems()->updateExistingPivot((int) $relatedId, [
        'some_field' => $value,
    ]);
}
```

---

## 5. Exception Stack Trace in Response

**What it looks like:**

```php
catch (Throwable $e) {
    return view('app.plain', [
        'content' => $e->getMessage() . $e->getTraceAsString(),
    ]);
}
```

**Why it is dangerous:** Stack traces reveal file paths, class names, method names, line numbers, and sometimes argument values — all useful for an attacker mapping the system. Even behind admin or superadmin middleware, this is unnecessary information exposure.

**Fix:** Log the full trace server-side; return only a sanitized message to the client:

```php
catch (Throwable $e) {
    Log::error('Operation failed', ['exception' => $e]);

    return view('app.plain', [
        'content' => 'An error occurred. Check the application logs.',
    ]);
}
```

---

## 6. Raw DB Query Bypassing Eloquent Events and Auditing

**Brain export signal:** `raw query (via ControllerName@action)`

**What it looks like:**

```php
DB::table($relation->getTable())->insert(
    $coveragesData->map(fn (array $row): array => [...$row])->all()
);
```

**Why it is dangerous:** Raw `DB::table()` calls bypass:
- Eloquent model events (`creating`, `created`, `updating`, etc.)
- Observer classes
- Audit logging packages (e.g. `owen-it/laravel-auditing`) — if auditing is installed, raw inserts do not produce audit log entries

**Before flagging as a problem:** Check whether the raw query is an intentional workaround. Common legitimate uses:
- Bulk pivot inserts inside a `DB::transaction()` where `attach()` / `sync()` would not produce the right rows (e.g. duplicate company IDs per pivot)
- Backfill migrations where firing model events would have unintended side effects

If intentional, verify there is a comment explaining why and that audit gaps are acceptable for this data.

**Fix:** Use Eloquent model `create()` / `save()` so the full lifecycle fires. If a bulk insert is genuinely needed for performance, consider whether you can manually fire the audit event, or at minimum add a comment explaining why Eloquent was bypassed and what the consequences are.

---

## 7. Unvalidated Foreign Key

**What it looks like:**

```php
$company->insurance_company_id = $request->filled('insurance_company_id')
    ? (int) $request->input('insurance_company_id')
    : null;
```

**Why it is dangerous:** An arbitrary integer is cast and written directly to a foreign key column without verifying that the referenced record exists or is accessible to the current user. If there is no database-level FK constraint, this can silently write dangling references.

**Fix:** Validate the FK value before assigning it:

```php
$validated = $request->validate([
    'related_model_id' => ['nullable', 'integer', 'exists:related_models,id'],
]);

$model->related_model_id = $validated['related_model_id'];
```

---

## Quick Grep Commands

Use these to scan hotspot files for the patterns above without reading every line:

```bash
# Arbitrary attribute setting
grep -n 'setAttribute\s*(\s*\$request' app/Http/Controllers/**/*.php

# Mass fill from request
grep -n '->fill(\$' app/Http/Controllers/**/*.php

# AJAX-only auth
grep -n 'ajax()' app/Http/Controllers/**/*.php

# getTraceAsString in response
grep -rn 'getTraceAsString' app/

# Raw DB queries in controllers
grep -rn 'DB::table' app/Http/Controllers/

# Unvalidated FK assignment
grep -n "input\('.*_id'\)" app/Http/Controllers/**/*.php
```

---

## Scanner Line-Number Drift

The brain export lists line numbers from when the graph was last built. If the file has been edited since the last `brain:scan`, those line numbers will be off. Before navigating to a reported line:

```bash
# Find the actual line of a pattern rather than trusting the export
grep -n 'setAttribute\s*(\$request' app/Http/Controllers/Path/To/Controller.php
```

Always verify the line number by grepping for the specific pattern rather than jumping directly to the reported line. The anti-pattern search commands in the Quick Grep Commands section above are the reliable way to find exact positions.

---

## Checking Authorization Coverage

To quickly assess which routes lack middleware protection:

```bash
php artisan route:list \
  --except-vendor \
  --columns=method,uri,action,middleware \
  2>/dev/null | grep -v 'auth\|can:' | head -40
```

Routes in `superadmin` or `admin` prefixes that appear without `auth` or `can:` in the middleware column warrant individual inspection.
