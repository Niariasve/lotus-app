# AGENTS.md

This file gives coding agents the repo-specific rules for working safely and efficiently in `lotus-app`.

## Stack Snapshot

- Backend: Laravel 12, PHP 8.4, Inertia.js, Fortify; for application/runtime/database work, assume MySQL-oriented behavior unless `.env` or runtime config shows another driver.
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
- `phpunit.xml` sets `DB_CONNECTION=sqlite` and `DB_DATABASE=:memory:` for tests; treat this in-memory SQLite setup as test/CI-only fallback, not the preferred database assumption for application or runtime decisions.
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
- Keep each file focused on a single responsibility.
- Prefer the smallest file that still reads clearly; split large files before they become monoliths.
- Use `@/` alias for imports from `resources/js`.
- ESLint enforces ordered imports and separate `type` imports.
- Prefer `import type` / inline `type` specifiers for types.
- Existing code usually groups imports as external, internal alias, then relative.
- Prefer shadcn-vue/Reka-based components as the default UI component system.
- Prefer Inertia `Form` for frontend forms instead of `useForm`; follow the existing pattern of `<Form v-bind="controller().form(...)" v-slot="{ processing, errors }">` with named inputs and hidden inputs for custom controls such as shadcn `Select`.
- Extract UI logic into focused components or composables when that meaningfully reduces responsibility in the parent file.
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
- Test environment is configured through `phpunit.xml`; keep that SQLite setup scoped to tests/CI and do not replace it with local-only hacks.
- For normal application/runtime/database work, prefer MySQL-oriented assumptions, but verify `.env` or deployment/runtime config before relying on any DB-specific behavior.
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

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.4
- inertiajs/inertia-laravel (INERTIA_LARAVEL) - v2
- laravel/fortify (FORTIFY) - v1
- laravel/framework (LARAVEL) - v12
- laravel/prompts (PROMPTS) - v0
- laravel/wayfinder (WAYFINDER) - v0
- larastan/larastan (LARASTAN) - v3
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- pestphp/pest (PEST) - v4
- phpunit/phpunit (PHPUNIT) - v12
- @inertiajs/vue3 (INERTIA_VUE) - v2
- tailwindcss (TAILWINDCSS) - v4
- vue (VUE) - v3
- @laravel/vite-plugin-wayfinder (WAYFINDER_VITE) - v0
- eslint (ESLINT) - v9
- prettier (PRETTIER) - v3

## Skills Activation

This project has domain-specific skills available. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

- `wayfinder-development` — Activates whenever referencing backend routes in frontend components. Use when importing from @/actions or @/routes, calling Laravel routes from TypeScript, or working with Wayfinder route functions.
- `pest-testing` — Use this skill for Pest PHP testing in Laravel projects only. Trigger whenever any test is being written, edited, fixed, or refactored — including fixing tests that broke after a code change, adding assertions, converting PHPUnit to Pest, adding datasets, and TDD workflows. Always activate when the user asks how to write something in Pest, mentions test files or directories (tests/Feature, tests/Unit, tests/Browser), or needs browser testing, smoke testing multiple pages for JS errors, or architecture tests. Covers: it()/expect() syntax, datasets, mocking, browser testing (visit/click/fill), smoke testing, arch(), Livewire component tests, RefreshDatabase, and all Pest 4 features. Do not use for factories, seeders, migrations, controllers, models, or non-test PHP code.
- `inertia-vue-development` — Develops Inertia.js v2 Vue client-side applications. Activates when creating Vue pages, forms, or navigation; using <Link>, <Form>, useForm, or router; working with deferred props, prefetching, or polling; or when user mentions Vue with Inertia, Vue pages, Vue forms, or Vue navigation.
- `tailwindcss-development` — Always invoke when the user's message includes 'tailwind' in any form. Also invoke for: building responsive grid layouts (multi-column card grids, product grids), flex/grid page structures (dashboards with sidebars, fixed topbars, mobile-toggle navs), styling UI components (cards, tables, navbars, pricing sections, forms, inputs, badges), adding dark mode variants, fixing spacing or typography, and Tailwind v3/v4 work. The core use case: writing or fixing Tailwind utility classes in HTML templates (Blade, JSX, Vue). Skip for backend PHP logic, database queries, API routes, JavaScript with no HTML/CSS component, CSS file audits, build tool configuration, and vanilla CSS.
- `developing-with-fortify` — Laravel Fortify headless authentication backend development. Activate when implementing authentication features including login, registration, password reset, email verification, two-factor authentication (2FA/TOTP), profile updates, headless auth, authentication scaffolding, or auth guards in Laravel applications.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan Commands

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`, `php artisan tinker --execute "..."`).
- Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.

## URLs

- Whenever you share a project URL with the user, you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain/IP, and port.

## Debugging

- Use the `database-query` tool when you only need to read from the database.
- Use the `database-schema` tool to inspect table structure before writing migrations or models.
- To execute PHP code for debugging, run `php artisan tinker --execute "your code here"` directly.
- To read configuration values, read the config files directly or run `php artisan config:show [key]`.
- To inspect routes, run `php artisan route:list` directly.
- To check environment variables, read the `.env` file directly.

## Reading Browser Logs With the `browser-logs` Tool

- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)

- Boost comes with a powerful `search-docs` tool you should use before trying other approaches when working with Laravel or Laravel ecosystem packages. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic-based queries at once. For example: `['rate limiting', 'routing rate limiting', 'routing']`. The most relevant results will be returned first.
- Do not add package names to queries; package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'.
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit".
3. Quoted Phrases (Exact Position) - query="infinite scroll" - words must be adjacent and in that order.
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit".
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms.

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.

## Constructors

- Use PHP 8 constructor property promotion in `__construct()`.
    - `public function __construct(public GitHub $github) { }`
- Do not allow empty `__construct()` methods with zero parameters unless the constructor is private.

## Type Declarations

- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

<!-- Explicit Return Types and Method Params -->
```php
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
```

## Enums

- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.

## Comments

- Prefer PHPDoc blocks over inline comments. Never use comments within the code itself unless the logic is exceptionally complex.

## PHPDoc Blocks

- Add useful array shape type definitions when appropriate.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

=== inertia-laravel/core rules ===

# Inertia

- Inertia creates fully client-side rendered SPAs without modern SPA complexity, leveraging existing server-side patterns.
- Components live in `resources/js/pages` (unless specified in `vite.config.js`). Use `Inertia::render()` for server-side routing instead of Blade views.
- ALWAYS use `search-docs` tool for version-specific Inertia documentation and updated code examples.
- IMPORTANT: Activate `inertia-vue-development` when working with Inertia Vue client-side patterns.

# Inertia v2

- Use all Inertia features from v1 and v2. Check the documentation before making changes to ensure the correct approach.
- New features: deferred props, infinite scroll, merging props, polling, prefetching, once props, flash data.
- When using deferred props, add an empty state with a pulsing or animated skeleton.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

## Database

- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries.
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

### APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## Controllers & Validation

- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

## Authentication & Authorization

- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Queues

- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

## Configuration

- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== laravel/v12 rules ===

# Laravel 12

- CRITICAL: ALWAYS use `search-docs` tool for version-specific Laravel documentation and updated code examples.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

## Laravel 12 Structure

- In Laravel 12, middleware are no longer registered in `app\Http/Kernel.php`.
- Middleware are configured declaratively in `bootstrap/app.php` using `Application::configure()->withMiddleware()`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- The `app\Console/Kernel.php` file no longer exists; use `bootstrap/app.php` or `routes/console.php` for console configuration.
- Console commands in `app\Console/Commands/` are automatically available and do not require manual registration.

## Database

- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 12 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models

- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.

=== wayfinder/core rules ===

# Laravel Wayfinder

Wayfinder generates TypeScript functions for Laravel routes. Import from `@/actions/` (controllers) or `@/routes/` (named routes).

- IMPORTANT: Activate `wayfinder-development` skill whenever referencing backend routes in frontend components.
- Invokable Controllers: `import StorePost from '@/actions/.../StorePostController'; StorePost()`.
- Parameter Binding: Detects route keys (`{post:slug}`) — `show({ slug: "my-post" })`.
- Query Merging: `show(1, { mergeQuery: { page: 2, sort: null } })` merges with current URL, `null` removes params.
- Inertia: Use `.form()` with `<Form>` component or `form.submit(store())` with useForm.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

## Pest

- This project uses Pest for testing. Create tests: `php artisan make:test --pest {name}`.
- Run tests: `php artisan test --compact` or filter: `php artisan test --compact --filter=testName`.
- Do NOT delete tests without approval.

=== inertia-vue/core rules ===

# Inertia + Vue

Vue components must have a single root element.
- IMPORTANT: Activate `inertia-vue-development` when working with Inertia Vue client-side patterns.

=== laravel/fortify rules ===

# Laravel Fortify

- Fortify is a headless authentication backend that provides authentication routes and controllers for Laravel applications.
- IMPORTANT: Always use the `search-docs` tool for detailed Laravel Fortify patterns and documentation.
- IMPORTANT: Activate `developing-with-fortify` skill when working with Fortify authentication features.

</laravel-boost-guidelines>
