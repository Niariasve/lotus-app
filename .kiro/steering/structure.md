# Project Structure

## Backend (`app/`)
```
app/
├── Actions/Fortify/          # Auth actions (CreateNewUser, ResetUserPassword)
├── Concerns/                 # Shared validation rule traits
├── Http/
│   ├── Controllers/          # RESTful controllers; Settings/ for profile/password/2FA/locale
│   ├── Middleware/           # Inertia, locale, appearance middleware
│   └── Requests/             # Form request validation, organized by resource (e.g. Customers/StoreRequest.php)
├── Models/                   # Eloquent models with $fillable and casts()
└── Providers/                # AppServiceProvider, FortifyServiceProvider
```

## Frontend (`resources/js/`)
```
resources/js/
├── pages/                    # Inertia page components, organized by feature (customers/, products/, etc.)
├── components/               # Shared Vue components
│   └── ui/                   # Reka UI-based component library
├── layouts/                  # AppLayout and other layout wrappers
├── features/                 # Feature-scoped types and table column definitions
│   └── {feature}/types/      # TypeScript interfaces + TanStack column configs
├── composables/              # Vue composables (useAppearance, useFlashToast, etc.)
├── routes/                   # Auto-generated Wayfinder route helpers (do not edit manually)
├── actions/                  # Auto-generated Inertia action helpers (do not edit manually)
├── wayfinder/                # Wayfinder internals (do not edit manually)
├── types/                    # Shared TypeScript types; re-exports from features + auth/nav/ui
├── lib/                      # Utility functions (cn for class merging, formatDate, etc.)
├── app.ts                    # Client entry point
└── ssr.ts                    # SSR entry point
```

## Database (`database/`)
```
database/
├── migrations/               # Timestamped anonymous-class migrations
├── factories/                # Model factories for testing
└── seeders/                  # DatabaseSeeder + resource-specific seeders
```

## Routes
- `routes/web.php` — main resource routes (customers, products, suppliers, etc.)
- `routes/settings.php` — profile, password, 2FA, locale settings

## Key Conventions

### PHP
- Controllers follow RESTful naming: `index`, `create`, `store`, `edit`, `update`, `destroy`
- Always use Form Requests for validation; place in `Http/Requests/{Resource}/`
- Models declare `$fillable` and a `casts(): array` method
- Use `Inertia::render('feature/Page', [...])` in controllers; page path matches `pages/` folder
- Use `Inertia::flash([...])` for success/error messages; consumed by `useFlashToast`
- Return `to_route('route.name')` after mutations

### Vue / TypeScript
- Use `<script setup lang="ts">` with `defineProps<Type>()` and `defineEmits<Events>()`
- Page components live in `pages/{feature}/` and match the Inertia render path
- Feature-specific types go in `features/{feature}/types/`; export from `types/index.ts`
- Use `cn()` from `@/lib/utils` for conditional Tailwind class merging
- Composables are prefixed with `use`
- Use Wayfinder-generated helpers from `@/routes/{feature}` for type-safe URLs — never hardcode URLs
- Regenerate Wayfinder after adding/changing routes: `composer run wayfinder:generate`

### Database
- Table names: `snake_case` plural
- Column names: `snake_case`
- Foreign keys: `{singular_table}_id`
- Add indexes on frequently filtered/sorted columns
- Migrations use anonymous class syntax: `return new class extends Migration { ... }`

### i18n
- Translation keys live in `lang/en.json` and `lang/es.json`
- Use `$t('key')` in templates; add keys to both language files when adding new UI strings
