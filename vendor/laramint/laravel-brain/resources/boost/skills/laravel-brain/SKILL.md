---
name: laravel-brain
description: "Analyze a Laravel application for complexity hotspots, security surface, and database access patterns using laravel-brain. Activate when: the user asks what needs work, what should be improved, what to work on next, where to focus, what is the most complex code, what are the security issues, audit the codebase, analyze code quality, find technical debt, find refactoring targets, what are the worst parts of the codebase, or when user mentions: brain, hotspots, cyclomatic, complexity audit, security audit, codebase health, codebase review."
---

# Laravel Brain Analysis

`laravel-brain` scans the application graph and surfaces cyclomatic complexity hotspots, security surface area, and database access patterns across every controller, command, and Livewire component. Use it to answer "what needs work" before writing code, not after.

EXECUTE steps 0–5 IMMEDIATELY without asking the user. Scans are read-only and safe. Only pause at step 6 to present findings.

---

## 0. Pre-flight

Ensure the package is installed before scanning:

```bash
php artisan brain:scan --help > /dev/null 2>&1 \
  || composer require laramint/laravel-brain --dev --no-interaction 2>&1
```

If composer ran, commit `composer.json` and `composer.lock` afterwards — otherwise the package disappears on the next branch switch.

---

## 1. Scan

Build a fresh application graph. This takes 20–40 seconds on larger projects:

```bash
php artisan brain:scan --memory-limit=1024M --no-interaction 2>&1
```

Read the summary output carefully. Note three things:

- **Security surface** — the total count of flagged patterns. It is NOT broken down by type in the scan output; use step 2.5 to break it down.
- **Nodes / Edges** — graph size; use this to calibrate the export budget in step 2.
- **Viewer URL** — the scan prints `Open the viewer: https://[project].test/_laravel-brain`. Capture this URL for step 2.5.

---

## 2. Export Context

Export the full analysis. The default budget (6000 tokens) silently truncates the hotspot table on any project with more than ~50 controllers — always override it:

```bash
php artisan brain:export-context \
  --budget=20000 \
  --format=markdown \
  --output=/tmp/brain-analysis.md \
  --force \
  --no-interaction 2>&1
```

Budget calibration based on node count from step 1:

| Nodes | Budget |
|-------|--------|
| < 300 | 8000 |
| 300–800 | 15000 |
| 800+ | 20000 |

> **Critical limitation:** The `--node` and `--route` filters do NOT restrict the Complexity Hotspots table. They only change the focal node for the Call Chain section at the top. The full project hotspot list always appears regardless of which filter you use. There is no way to get a node-scoped hotspot export via the CLI.

---

## 2.5. Security Category Breakdown

The scan surface count is a single number. Before reading any source, fetch the per-category breakdown from the viewer's JSON API — this determines which categories dominate and whether the count is real signal or noise:

```bash
VIEWER_URL="https://[project].test"   # replace with actual URL from step 1
curl -sk "${VIEWER_URL}/_laravel-brain/api/context?format=json" \
  | python3 -c "
import sys, json
from collections import Counter
data = json.load(sys.stdin)
types = Counter()
for n in data['nodes']:
    for i in n.get('data', {}).get('security', {}).get('issues', []):
        types[i['type']] += 1
for t, c in types.most_common():
    print(f'{c:4d}  {t}')
"
```

**Interpreting the output — known issue types:**

| Type | Meaning | False-positive rate |
|---|---|---|
| `XSS_BLADE_UNESCAPED` | `{!! !!}` output in Blade | **High** — form builder packages (`kris/laravel-form-builder`, `rdx/laravelcollective-html`) render HTML intentionally; check whether the call is a form builder helper before flagging |
| `PUBLIC_WRITE` | POST/PUT/PATCH/DELETE route without detected auth middleware | **Medium** — the scanner reads middleware from the route definition; middleware applied in controllers or via route groups it cannot see will produce false positives. Cross-check with `php artisan route:list --method=POST --path=<prefix>` |
| `MISSING_THROTTLE` | Auth or write route without a rate-limit middleware | **Low** — nearly always a real gap |
| `UNVALIDATED_INPUT` | Controller accepting user input directly without validation | **Low** — nearly always a real gap |

**Decision rule:** if `XSS_BLADE_UNESCAPED` dominates, discount it and focus on `PUBLIC_WRITE` and `UNVALIDATED_INPUT`. If `PUBLIC_WRITE` is large (≥ 20), start with routes on **non-admin/non-superadmin prefixes** — admin routes often have middleware applied in controller constructors or route groups that the scanner cannot see, producing false positives. Use `php artisan route:list --path=<prefix> --columns=method,uri,middleware` to cross-check specific suspicious routes before flagging them.

---

## 3. Parse the Export

Read `/tmp/brain-analysis.md`. Focus on three sections in order of signal quality:

### 3a. Complexity Hotspots

The most important section. A table of all callables sorted by cyclomatic complexity descending, with line count alongside.

**Scoring thresholds:**

| Cyclomatic | Meaning | Default action |
|---|---|---|
| ≥ 16 | Critical — many independent paths, high bug surface | Must refactor |
| 10–15 | High — deep nesting or long if/elseif chains | Schedule refactor |
| 7–9 | Moderate | Inspect before touching |
| ≤ 6 | Normal | No action needed |

**Use both columns together.** Cyclomatic alone does not tell the full story:

- High cyclomatic + high lines (≥ 80) → almost always a real problem
- High cyclomatic + low lines → often a `match`/`switch` on an enum; verify before flagging
- Low cyclomatic + very high lines (≥ 150) → structural smell even without branching

**Scanner line numbers may drift.** The export shows line numbers from the last scan. If files were edited after `brain:scan` ran, those numbers will be off. Use `grep -n` to find exact positions rather than jumping to the reported line directly.

**Identify false positives before flagging.** These score high legitimately and should be excluded from your candidate list:

- Filament schema methods (`columns()`, `filters()`, `form()`, `table()`) — cyclomatic comes from chained `->when()` calls on field configuration, not from decision logic
- Custom query builder methods with many `->when()` clauses — same pattern
- Artisan command argument parsing — can legitimately branch; weight controller actions higher than commands

**Build your candidate list:** extract the top 5 entries that are NOT false positives, sorted by `cyclomatic × lines` as a combined risk score.

### 3b. Database Operations

Scan every line for these two high-signal patterns:

1. **`raw query` entries** — any `DB::` call bypassing Eloquent. Raw queries skip model events, observers, and the auditing package. Every raw query entry must be investigated in source.
2. **Same table appearing in ≥ 5 unrelated controllers** — missing encapsulation; candidate for a repository or service class.

Secondary signal: controllers using `save()` vs `saveOrFail()` — bare `save()` silently swallows failures in some contexts.

### 3c. Security Surface Count (from step 1)

The count is a smoke signal, not a finding list. Correlate it with the hotspot and database operation sections: a controller that ranks high on complexity AND touches many tables AND has a raw query is the highest-risk intersection to investigate first.

See [references/security-patterns.md](references/security-patterns.md) for the specific anti-patterns to look for in source code.

---

## 4. Verify via Source

For each of the top 5 complexity candidates, **read the actual source file**. The score tells you where to look; the code tells you why it matters.

Locate files with:

```bash
find app -name "ControllerName.php" 2>/dev/null | head -3
```

When reading, check for these anti-patterns (full descriptions and fixes in [references/security-patterns.md](references/security-patterns.md)):

| Anti-pattern | Risk |
|---|---|
| `if/elseif` chain on `$request->input('form_type')` or similar | God controller — split into one invokable per action |
| `$model->fill($requestData)->save()` without a FormRequest | Unvalidated mass assignment |
| `$model->setAttribute($request->input('field'), ...)` | Arbitrary attribute name from user input |
| `$request->ajax()` as the sole authorization check | AJAX header is trivially forgeable |
| `$e->getTraceAsString()` rendered in a response | Stack trace exposure |
| `DB::table(...)->insert(...)` inside a controller | Bypasses model events and auditing |
| Large private method only called from `__invoke` | Hidden complexity — hard to test in isolation |
| `foreach ($request->input('ids') as $id => $value)` without ownership check | Unvalidated pivot/relation update |

---

## 4.5. Drill Down with `--node`

Before fixing any finding, run a focused call chain export for that specific controller. This shows what the target calls downstream and — critically — what routes and middleware lead into it:

```bash
php artisan brain:export-context \
  --node=ControllerName@method \
  --budget=8000 \
  --format=markdown \
  --output=/tmp/brain-node.md \
  --force --no-interaction 2>&1
```

Read the **Call Chain** section at the top of `/tmp/brain-node.md`. It shows the lifecycle from route → middleware → controller → downstream calls, up to 3 levels deep. Use this to confirm what authorization middleware is actually applied before writing the fix.

> **Reminder:** `--node` only affects the Call Chain section. The Complexity Hotspots table in this output still covers the full project — ignore it here.

---

## 6. Report

Present findings using this structure. Only include entries you have verified by reading source — do not copy the full hotspot table.

```
## Codebase Analysis

Security surface: [N] issues flagged by scanner.
Interactive breakdown: [viewer URL from step 1]

### Priority 1 — Security Concerns

- `ControllerName@method` (`path/to/File.php:line`) — [one-sentence description of the specific issue]

### Priority 2 — Complexity Hotspots

| Controller | Cyclomatic | Lines | Issue |
|---|---|---|---|
| ControllerName@method | N | N | one-line description |

### Priority 3 — Database Operation Concerns

- Raw query in `ControllerName@method` — [why it matters]
- Table `name` touched by N unrelated controllers — [candidate for extraction]

### Recommended Focus

1. [Highest-priority item with file reference]
2. [Second]
3. [Third]
```

Cap each section at 5 items. Group additional findings as "and N more" with a brief characterization.

---

## STOP

Do NOT start writing code, creating specs, or making changes. Present the report and wait for the user to direct next steps.

If the user wants to create a spec for a finding, activate the `write-spec` skill.
If the user wants detail on any security anti-pattern, read [references/security-patterns.md](references/security-patterns.md).
If the output sections are unclear, read [references/interpreting-output.md](references/interpreting-output.md).

---

## Permissions and Installation Errors

If `brain:scan` exits with a permissions error about writing to `storage/`, the web server user and CLI user may differ. Fix with:

```bash
chmod -R 775 storage bootstrap/cache
```

If the package installs but `brain:scan` still fails to find the command, check that `laravel-brain` is in the `autoload-dev` section of `composer.json` and run `composer dump-autoload`.

---

## Notes on the Package

- `brain:generate-rules` generates static context files for specific agents (Cursor, Windsurf, JetBrains Junie, OpenAI Codex). This skill is different — it provides structured workflow guidance distributed via the Laravel Boost skill system (`boost:update`) to whichever AI tools the developer has configured.
- Re-scan whenever PHP files have changed since the last scan. Within a single session, one scan is sufficient — you can re-export from the cached graph as many times as needed without re-scanning.
- The browser viewer (URL printed by `brain:scan`) is the only place the security count is broken down by category. It also provides interactive call chain exploration that is not available via the CLI export.
