# AGENTS.md

This file gives coding agents the repo-specific rules for working safely and efficiently in `lotus-app`.

## Stack Snapshot

- Backend: Laravel 12, PHP 8.4, Inertia.js, Fortify, SQLite by default.
- Frontend: Vue 3, TypeScript, Vite 7, Tailwind CSS 4, Reka UI, TanStack Vue Table.
- Testing: Pest 4 on top of Laravel's test runner.
- Static analysis / style: PHPStan + Larastan, Laravel Pint, ESLint, Prettier, `vue-tsc`.
- Route helpers: Laravel Wayfinder generates typed client routes.

## Package Managers And Tooling

- Use `npm`, not `pnpm`, `yarn`, or `bun`.
- Evidence: root `package-lock.json` exists and GitHub Actions use `npm ci`.
- Use `composer` for PHP workflows.
- Do not invent alternative commands when a script already exists in `package.json` or `composer.json`.

## Install / Setup

- PHP deps: `composer install`
- Node deps: `npm ci`
- First-time app setup: `composer run setup`
- Local env copy/key/migrate are handled by setup scripts; do not hand-roll replacements unless needed.

## Development Commands

- Full local dev loop: `composer run dev`
  - Starts Laravel server, queue listener, and Vite together via `concurrently`.
- SSR dev flow: `composer run dev:ssr`
- Frontend-only dev server: `npm run dev`
- Production frontend build: `npm run build`
- SSR build: `npm run build:ssr`

## Test And Quality Commands

- Full default test workflow: `composer run test`
  - Clears config, runs Pint in test mode, then runs `php artisan test`.
- PHP tests only: `php artisan test`
- Single test file: `php artisan test tests/Feature/DashboardTest.php`
- Filter to one Pest test name: `php artisan test --filter="authenticated users can visit the dashboard"`
- File + filter together: `php artisan test tests/Feature/Auth/AuthenticationTest.php --filter="users can logout"`
- Direct Pest invocation also works: `./vendor/bin/pest tests/Feature/DashboardTest.php`
- PHP formatting fix: `composer run lint`
- PHP formatting check only: `composer run test:lint`
- PHP static analysis: `composer run analyse`
- Frontend lint with autofix: `npm run lint`
- Frontend lint check: `npm run lint:check`
- Frontend typecheck: `npm run typecheck`
- Prettier write: `npm run format`
- Prettier check: `npm run format:check`
- Security audit: `composer run security:audit` and `npm audit --package-lock-only --omit=dev --audit-level=high`

## What Is Actually Tested Here

- PHP feature and unit tests live in `tests/Feature/` and `tests/Unit/`.
- Pest is configured in `tests/Pest.php`.
- Feature tests automatically use `RefreshDatabase`.
- `phpunit.xml` sets `DB_CONNECTION=sqlite` and `DB_DATABASE=:memory:` for tests.
- No frontend unit test runner is configured in this repo right now.
- Do not invent `vitest`, `jest`, or `playwright` commands unless you add and wire them first.

## CI Expectations

- PR CI runs on Node 22 and PHP 8.4.
- Tests workflow builds assets before `php artisan test`.
- Frontend typecheck workflow runs `composer wayfinder:generate` before `npm run typecheck`.
- Static analysis workflow runs `composer run analyse`.
- A push workflow auto-formats PHP with Pint on `dev` / `develop`.

## File And Folder Conventions

- Backend app code lives under `app/`.
- Controllers are in `app/Http/Controllers/`.
- Form requests are grouped by resource in `app/Http/Requests/{Resource}/`.
- Models are in `app/Models/`.
- Inertia pages live in `resources/js/pages/`.
- Shared Vue components live in `resources/js/components/`.
- UI primitives live in `resources/js/components/ui/`.
- Feature-specific TS types live in `resources/js/features/{feature}/types/`.
- Shared TS re-exports live in `resources/js/types/index.ts`.
- Auto-generated route helpers live in `resources/js/routes/`.
- Other generated Wayfinder artifacts live in `resources/js/actions/` and `resources/js/wayfinder/`.

## Generated Files

- Treat `resources/js/routes/*`, `resources/js/actions/*`, and `resources/js/wayfinder/*` as generated.
- Regenerate them with `composer run wayfinder:generate` after route/controller signature changes.
- `resources/js/components/ui/*` is ignored by ESLint and Prettier; many files are generator-managed shadcn-vue/Reka wrappers.
- Prefer minimal, compatible edits there; do not reformat generated output just because it looks inconsistent.

## PHP Coding Conventions

- Follow Laravel conventions and Pint's `laravel` preset.
- Use 4-space indentation and LF line endings.
- Prefer resourceful controllers: `index`, `create`, `store`, `edit`, `update`, `destroy`.
- Use Form Requests for validation instead of inline controller validation.
- Keep request classes scoped by feature, e.g. `app/Http/Requests/Products/StoreRequest.php`.
- Return validated payloads from `$request->validated()` and pass them into model writes.
- After mutations, prefer `to_route('route.name')`.
- Success feedback is flashed with `Inertia::flash([...])`.
- Models commonly define `$fillable` and a `casts(): array` method.
- Routes use named routes and resource controllers in `routes/web.php`.

## Frontend Coding Conventions

- Use Vue SFCs with `<script setup lang='ts'>`.
- Use TypeScript everywhere in app code.
- TS config is strict; keep types explicit enough to satisfy `vue-tsc`.
- Use `@/` alias for imports from `resources/js`.
- ESLint enforces ordered imports and separate `type` imports.
- Prefer `import type` / inline `type` specifiers for types.
- Existing code usually groups imports as external, internal alias, then relative.
- Prefer shadcn-vue/Reka-based components as the default UI component system.
- Before creating or editing a feature UI component, check whether an appropriate component already exists in the project.
- If no local component fits, check the shadcn-vue docs/site for an equivalent and integrate it following existing project patterns.
- Only implement a custom component when no suitable local or upstream shadcn-vue component exists.
- Keep edits to `resources/js/components/ui/*` minimal and compatible because many files there are generator-managed wrappers.
- Use Wayfinder helpers from `@/routes/...` instead of hardcoded URLs.
- Use shared helpers from `@/lib/utils`, especially `cn()` for class merging.
- Before adding a new utility function, check for an existing equivalent in this order: the current feature directory first, then shared helpers / global utilities such as `@/lib/utils`, then utility files in other feature directories for similar logic.
- Reuse a utility when it already exists in the current feature or in the shared/global helpers.
- If similar logic exists in another feature and it is broadly reusable rather than tied to that feature, extract or promote it into the shared/global utilities area and update callers instead of duplicating it.
- If the logic is tightly coupled to one feature's domain, data shape, or UI behavior, keep it feature-local and create or adapt a local utility there instead of globalizing it.
- Use feature folders for feature-specific types and table column definitions.
- Re-export cross-feature types through `resources/js/types/index.ts` when they are broadly shared.

## Formatting Rules

- JS/TS/Vue/JSON formatting comes from Prettier.
- Prettier uses 4 spaces, semicolons, single quotes, width 80.
- Tailwind classes are automatically sorted by `prettier-plugin-tailwindcss`.
- YAML uses 2 spaces.
- Do not manually fight the formatter.

## Naming Conventions

- PHP classes: PascalCase.
- PHP namespaces mirror directories.
- Database tables and columns: snake_case.
- Foreign keys: `{singular}_id`.
- Vue components: PascalCase filenames.
- Composables: `useXxx`.
- Inertia page names must match controller render strings, e.g. `Inertia::render('products/Index')` -> `resources/js/pages/products/Index.vue`.

## Error Handling And Validation

- Put validation rules in Form Requests.
- Let Laravel validation drive user-facing field errors.
- Prefer framework exceptions / validation responses over custom ad-hoc error payloads.
- For nullable date/number formatting on the frontend, existing helpers return a fallback rather than throwing.

## Testing Patterns

- Write tests in Pest style using `test('...', function () { ... })`.
- Follow existing naming: describe behavior in plain English.
- Use factories like `User::factory()->create()`.
- Use Laravel helpers like `actingAs`, `route`, `assertRedirect`, `assertOk`, `assertGuest`, `assertAuthenticated`.
- Keep feature tests focused on HTTP behavior, redirects, auth, and persistence side effects.

## Environment And Config Guidance

- Never commit secrets from `.env`.
- Test environment is configured through `phpunit.xml`; do not replace it with local-only hacks.
- Shared Inertia props are defined in `app/Http/Middleware/HandleInertiaRequests.php`.
- Middleware registration lives in `bootstrap/app.php`.
- Localization is active; when adding UI strings, update both `lang/en.json` and `lang/es.json`.

## Practical Agent Workflow

- Before changing routes or controller method signatures, check whether Wayfinder output will need regeneration.
- Before changing frontend types that come from route helpers or shared props, run `npm run typecheck` if verification is needed.
- Before changing backend behavior, run the smallest relevant Pest command first, not the whole suite.
- If touching only PHP formatting, prefer `composer run lint`.
- If touching only frontend formatting, prefer `npm run format` or `npm run lint` depending on the file type.
- Do not build the project unless explicitly asked.

## Things To Avoid

- Do not add new package managers.
- Do not hardcode app URLs in Vue pages when Wayfinder helpers exist.
- Do not move validation logic back into controllers.
- Do not edit generated Wayfinder files manually unless there is a very specific reason.
- Do not assume a frontend test runner exists.
