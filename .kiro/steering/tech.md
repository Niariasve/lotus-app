# Tech Stack

## Backend
- PHP 8.4+, Laravel 12
- Inertia.js 2.0 (SSR-capable)
- Laravel Fortify (auth + 2FA)
- SQLite (default); supports MySQL/PostgreSQL/MariaDB
- Pest 4.4 (testing), PHPStan/Larastan 3.9 (static analysis), Laravel Pint (code style)

## Frontend
- Vue 3 (Composition API, `<script setup>`)
- TypeScript 5.2
- Vite 7 (build tool)
- Tailwind CSS 4.1
- Reka UI (component primitives)
- TanStack Vue Table (data tables)
- Lucide Vue Next (icons)
- Vue Sonner (toast notifications)
- Laravel Vue i18n (i18n)
- Laravel Wayfinder (type-safe route helpers, auto-generated)

## Common Commands

### Development
```bash
composer run dev          # Start Laravel + queue + Vite concurrently
composer run dev:ssr      # Start with SSR enabled
```

### Build
```bash
npm run build             # Build frontend assets
npm run build:ssr         # Build frontend + SSR bundle
```

### Testing & Quality
```bash
composer run test         # Pint lint check + PHPUnit (full suite)
php artisan test          # Run PHP tests only
composer run analyse      # PHPStan static analysis
composer run lint         # Auto-fix PHP code style with Pint
npm run lint              # Auto-fix JS/TS with ESLint
npm run lint:check        # ESLint check (no fix)
npm run typecheck         # Vue TypeScript type check
npm run format            # Prettier format
npm run format:check      # Prettier check
```

### Database
```bash
php artisan migrate       # Run migrations
php artisan db:seed       # Seed database
```

### Wayfinder
```bash
composer run wayfinder:generate   # Regenerate type-safe route helpers
```
