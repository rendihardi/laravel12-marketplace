# AGENTS.md

Laravel 12 marketplace REST API (PHP 8.2+). API-only: routes live in `routes/api.php`, all endpoints are prefixed `/api`. There is no meaningful web UI.

## Commands

- `composer test` — clears config then runs `php artisan test` (PHPUnit, not Pest). Test env uses in-memory SQLite (`phpunit.xml`).
- Run one test: `php artisan test --filter=SomeTest`
- `composer dev` — runs server + queue listener + `pail` logs + vite concurrently.
- `composer setup` — full bootstrap (install, .env, key:generate, migrate, npm build).
- `./vendor/bin/pint` — code style (Laravel Pint). Run before finishing PHP changes.
- Frontend: `npm run dev` / `npm run build` (Vite + Tailwind v4). Minimal; app is API-first.

## Architecture

Repository pattern, strictly enforced:
- Controllers depend on interfaces in `app/Interface/*` (note singular `Interface`, not `Interfaces`).
- Concrete implementations in `app/Repositories/*`.
- Bindings registered in `app/Providers/RepositoryServiceProvider.php`. Adding a new repository means adding an interface, an implementation, AND a binding here.
- Controllers are thin: validate → call repository → wrap in a Resource.

Key conventions:
- All JSON responses go through `App\Helpers\ResponseHelper::jsonResponse($success, $message, $data, $statusCode)`. Do not hand-roll `response()->json(...)`.
- Paginated endpoints use `App\Http\Resources\PaginatedResource::make($paginator, SomeResource::class)` and require a `row_per_page` query param.
- Models use UUID primary keys (`HasUuids`). Never assume integer IDs.
- Auth is Sanctum with a custom token model: `App\Models\PersonalAccessToken` (UUID), wired in `RepositoryServiceProvider::boot()`.
- Authorization uses spatie/laravel-permission. Controllers implement `HasMiddleware` and gate actions with `PermissionMiddleware::using([...])`. Permissions/roles come from `PermissionSeeder` + `RoleSeeder`.
- Helpers live in `app/Helpers/` (`ResponseHelper`, `SlugHelper`, `CodeTrxHelper`, `ImageHelper`). Image uploads go to storage; run `php artisan storage:link` once.
- Payments: Midtrans (`midtrans/midtrans-php`). Callback route is public: `POST /api/midtrans-callback` → `MidtransController`.

The full endpoint contract (request/response shapes, roles, query params) is documented in `api_contract/README.md` — consult it before adding or changing endpoints.

## Environment / infra

- Local dev DB is MySQL (see `.env`), but `.env.example` and the test suite default to SQLite. Match `.env` when debugging DB behavior.
- Production/container runtime uses Laravel Octane on FrankenPHP (not `php artisan serve`). See `.docker/php/Dockerfile.local` and `.docker/etc/supervisor.d/supervisord.conf` (supervisor runs `octane:start` + `queue:work`).
- Queue driver is `database`; cache/session are `database`. Redis is available via phpredis.
- Docker: `docker compose up -d` brings up web + mysql + redis. Rebuild flow noted in `notes.txt`.
- Telescope is installed but excluded from auto-discovery (`composer.json` `dont-discover`) and only registered in `local`.

## Gotchas

- `.env` contains real Midtrans credentials — never commit it or echo secret values.
- Some routes in `routes/api.php` are commented out and re-declared as public further down (e.g. product, product-category, product-review). Check the whole file; the active definition may not be the first one.
- App locale is configurable; validation/response messages may be Indonesian (`php artisan lang:publish`).
- `_ide_helper.php` and `dump.rdb` are generated artifacts, not source.
