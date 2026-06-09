# Interpreting brain:export-context Output

The exported markdown has five sections. This reference explains what each contains, what is worth reading versus skimming, and where the limitations are.

---

## Section: Call Chain (depth ≤ 3)

Shows the call graph for the **focal node** — the entry point brain chose or that you specified via `--node`. Appears at the very top of the export.

**Read this when:** You are investigating a specific controller and want to understand what it calls downstream, or what calls into it.

**Skip this when:** You are doing a broad audit. The default focal node (often something like `UserController@getStoreUser`) is rarely the most important thing in a general health scan.

> **The `--node` filter only affects this section.** Using `--node=ControllerName@method` shifts the focal node for the Call Chain, but the Complexity Hotspots and Database Operations sections still cover the full project. There is no way to export a node-scoped hotspot list.

---

## Section: Complexity Hotspots

The most important section for identifying work targets. Columns:

| Column | Meaning |
|---|---|
| Label | `ClassName@method`, an Artisan command signature, or a Filament schema method name |
| Cyclomatic | Number of independent code paths (decision branches). Higher = harder to test and reason about. |
| Lines | Physical line count of the method body. |

**How to read the table:**

The table is sorted by cyclomatic descending. Scan the top — but treat the two columns as a combined signal, not cyclomatic alone.

The practical risk ranking is:

1. High cyclomatic + high lines → almost certainly a real problem
2. High cyclomatic + low lines → may be a match/switch on an enum; verify before flagging
3. Low cyclomatic + very high lines → structural smell; logic may be dense even without many branches

**What looks alarming but is not:**

- **Filament table/filter/form methods** (e.g. `ListUsers@columns`, `SomeResource@filters`, `SomeResource@form`) — these score high because Filament's fluent builder API uses heavy `->when()` chaining for conditional column/filter configuration. The cyclomatic count reflects the number of `->when()` calls, not branching decision logic. These methods are intentionally long; they are not candidates for refactoring unless the underlying conditions themselves are wrong.
- **Query builder scope methods** — same pattern; `->when()` on every optional filter adds to the cyclomatic count without representing logic complexity.
- **Artisan commands with argument parsing** — commands that handle multiple `{--option}` flags can legitimately branch per flag. Weight controller actions higher than commands when prioritizing.

**What is almost always a real problem:**

- Controller actions (especially `__invoke`, `store`, `update`) with cyclomatic ≥ 10 and lines ≥ 80 that are NOT Filament schema methods
- Private helper methods called only from `__invoke` with high cyclomatic — hidden complexity that belongs in a service class
- `if/elseif` chains keyed on `$request->input('form_type')` or similar — the god controller anti-pattern

---

## Section: Database Operations

Lists every Eloquent and raw DB call grouped by the controller method that triggers it. Format:

```
- eloquent [method] [table] (via ControllerClass@action)
- raw query  (via ControllerClass@action)
```

**High-signal patterns:**

| Pattern | What to do |
|---|---|
| `raw query` | Read the source immediately — bypasses model events, observers, and the auditing package |
| Same table in ≥ 5 unrelated controllers | Candidate for a repository or service class |
| Controller with ≥ 8 different tables | May be doing too much; check whether it is a coordinator (acceptable) or a god class (not) |
| `eloquent delete` without a soft-delete model | Check whether cascades are handled |

**Secondary signals:**

- `saveOrFail` is better than `save()` — bare `save()` silently swallows failures in some contexts
- `firstOrCreate` appearing in a controller action (rather than in a factory or seeder) can be a sign of missing session management

---

## Section: Backend Packages

The full `composer.json` dependency list. Useful for cross-referencing installed packages against what you find in source.

Most relevant during a security review — for example: if `owen-it/laravel-auditing` is installed and you find a `raw query` entry in the database operations section, that controller is bypassing the audit log. Check the Backend Packages section to see whether an auditing package is present before treating raw queries as audit gaps.

---

## Section: Frontend Packages

The full `package.json` list. Rarely relevant for backend complexity or security analysis. Scan only when doing a frontend-specific audit.

---

## What the Export Does NOT Contain

These things are absent from the export and must be investigated separately:

| Missing information | How to get it |
|---|---|
| Route middleware (auth, can:admin, etc.) | `php artisan route:list --path=your-path --columns=method,uri,action,middleware` |
| Blade view content (XSS, unescaped output) | Grep for `{!! !!}` in `resources/views/` |
| Model `$fillable` / `$guarded` settings | Read the model files directly |
| Policy and Gate registrations | Read `app/Policies/` and `AuthServiceProvider` |
| Specific security findings breakdown | Browser viewer at `https://[project].test/_laravel-brain` |
| JavaScript/TypeScript analysis | Not scanned; use ESLint for JS security patterns |
