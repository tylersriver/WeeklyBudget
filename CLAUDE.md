# CLAUDE.md — WeeklyBudget

## Project Overview

WeeklyBudget is a PHP web application for tracking personal weekly and monthly budget transactions. Users can add expenses, view spending summaries with progress bars and charts, browse transaction history by month, and update budget limits.

## Tech Stack

- **Language**: PHP 8.5+ (strict types, pipe operator, readonly classes, `#[\NoDiscard]`, enums, constructor promotion)
- **Framework**: Slim 4 (PSR-7 / PSR-15 micro-framework)
- **Runtime**: FrankenPHP (worker mode for persistent-process performance)
- **Architecture**: Domain-Driven Design (DDD) with CQRS at the service layer
- **Database**: SQLite via Cycle DBAL (raw SQL, parameterized queries)
- **Templating**: Twig 3 via `slim/twig-view`
- **DI Container**: PHP-DI 7 (PSR-11, autowiring enabled)
- **Frontend**: Tailwind CSS v4 + DaisyUI 5 (CDN), Alpine.js 3, Chart.js 4
- **Environment**: `vlucas/phpdotenv` — credentials in `.env` (never committed)
- **Autoloading**: PSR-4 via Composer (`App\` → `src/`)

## Directory Structure

```
WeeklyBudget/
├── public/
│   └── index.php                                  # Front controller + FrankenPHP worker loop
├── config/
│   ├── container.php                              # PHP-DI service definitions (port → adapter bindings)
│   ├── routes.php                                 # Slim route registration
│   ├── middleware.php                             # Middleware stack
│   └── settings.php                               # App settings (reads .env)
├── src/
│   ├── Shared/
│   │   ├── Domain/
│   │   │   └── AggregateRoot.php                  # Abstract base — identity + equality
│   │   ├── Application/
│   │   │   └── CommandBusInterface.php            # Port — dispatch commands to handlers
│   │   └── Infrastructure/
│   │       └── ContainerCommandBus.php            # Adapter — resolves handlers from DI container
│   ├── Budget/
│   │   ├── Domain/
│   │   │   ├── Budget.php                         # Aggregate Root — invariants, remaining(), percentUsed()
│   │   │   ├── BudgetType.php                     # Value Object (backed enum)
│   │   │   ├── MoneyAmount.php                    # Value Object — wraps decimal amounts
│   │   │   └── BudgetRepositoryInterface.php      # Port (persistence contract)
│   │   ├── Application/
│   │   │   ├── Command/
│   │   │   │   ├── UpdateBudgetCommand.php        # Command DTO
│   │   │   │   └── UpdateBudgetHandler.php        # Command handler
│   │   │   └── Query/
│   │   │       ├── GetAllBudgetsQuery.php         # Query handler
│   │   │       └── GetBudgetByTypeQuery.php       # Query handler
│   │   └── Infrastructure/
│   │       ├── CycleBudgetRepository.php          # Adapter — raw SQL via Cycle DBAL
│   │       └── Action/
│   │           └── BudgetAction.php               # HTTP adapter (GET|POST /budgets)
│   ├── Transaction/
│   │   ├── Domain/
│   │   │   ├── Transaction.php                    # Aggregate Root — record() factory + invariants
│   │   │   ├── TransactionType.php                # Value Object (backed enum)
│   │   │   ├── TransactionDescription.php         # Value Object — validates non-empty string
│   │   │   └── TransactionRepositoryInterface.php # Port
│   │   ├── Application/
│   │   │   ├── Command/
│   │   │   │   ├── RecordTransactionCommand.php   # Command DTO
│   │   │   │   └── RecordTransactionHandler.php   # Command handler
│   │   │   └── Query/
│   │   │       ├── GetWeeklyTransactionsQuery.php # Query handler
│   │   │       └── GetMonthlyTransactionsQuery.php# Query handler
│   │   └── Infrastructure/
│   │       ├── CycleTransactionRepository.php     # Adapter — raw SQL via Cycle DBAL
│   │       └── Action/
│   │           ├── TransactionAction.php          # HTTP adapter (POST /transactions)
│   │           └── HistoryAction.php              # HTTP adapter (GET|POST /history)
│   └── Reporting/
│       ├── Domain/
│       │   └── SpendingSummary.php                # Value Object — budget vs spent snapshot
│       ├── Application/
│       │   └── Query/
│       │       ├── DashboardData.php              # Readonly DTO with toTemplateVars()
│       │       └── GetDashboardQuery.php          # Cross-context query handler
│       └── Infrastructure/
│           └── Action/
│               └── DashboardAction.php            # HTTP adapter (GET /)
├── templates/
│   ├── layout.html.twig                           # Base layout (navbar, dark mode, CDN scripts)
│   ├── dashboard.html.twig                        # Budget cards, chart, transaction form, weekly table
│   ├── history.html.twig                          # Month/year filter + transaction table
│   └── budgets.html.twig                          # Budget table + update form
├── resources/
│   └── input.css                                  # Tailwind source (for standalone CLI builds)
├── Schema/
│   └── weeklyBudget.sql                           # SQLite schema + seed data
├── var/
│   ├── cache/                                     # Twig compiled templates (production)
│   ├── data/                                      # SQLite database file (git-ignored)
│   └── log/
├── .env.example                                   # Environment template
├── composer.json
├── tailwind.config.js                             # Tailwind / DaisyUI config
├── Dockerfile                                     # FrankenPHP production image
├── docker-entrypoint.sh                           # Entrypoint — PORT binding + SQLite init
├── compose.yml                                    # Local dev (Docker Compose)
└── CLAUDE.md
```

## Architecture & Request Flow

### DDD Layering

Each bounded context (`Budget/`, `Transaction/`, `Reporting/`) follows three layers:

1. **Domain** — Aggregate Roots, Value Objects, repository interfaces (ports). Zero framework dependencies.
2. **Application** — CQRS Command/Query handlers. Orchestrate domain objects. No HTTP or DB knowledge.
3. **Infrastructure** — Adapters: Cycle DBAL repositories, Slim HTTP actions. Implement ports.

### Request Flow

1. `public/index.php` — boots container (once in worker mode), creates Slim app
2. Slim routes dispatch to **Infrastructure Actions** (HTTP adapters)
3. Actions dispatch **Command DTOs** via the **Command Bus** or invoke **Query handlers** directly
4. **Command Bus** resolves the handler from the DI container (convention: `{Name}Command` → `{Name}Handler`)
5. **Command handlers** build Aggregate Roots via domain factories, call repository ports
6. **Query handlers** call repository ports, assemble DTOs
7. **Infrastructure repositories** (adapters) execute raw SQL via Cycle DBAL
8. Actions render Twig templates with query results

### CQRS Pattern

**Commands** (write side): `RecordTransactionCommand` → `RecordTransactionHandler`, `UpdateBudgetCommand` → `UpdateBudgetHandler`

**Queries** (read side): `GetDashboardQuery`, `GetAllBudgetsQuery`, `GetBudgetByTypeQuery`, `GetWeeklyTransactionsQuery`, `GetMonthlyTransactionsQuery`

Command DTOs are `readonly class`es. Handlers are `__invoke()`-able. Commands are dispatched via `CommandBusInterface` — a port implemented by `ContainerCommandBus`, which resolves handlers from the DI container using a naming convention (`{Name}Command` → `{Name}Handler` in the same namespace). HTTP actions depend only on the bus abstraction, not concrete handlers.

### Bounded Contexts

| Context | Responsibility | Aggregates | Value Objects |
|---------|----------------|------------|---------------|
| **Budget** | Budget limits and spending calculations | `Budget` | `BudgetType`, `MoneyAmount` |
| **Transaction** | Recording and querying expenses | `Transaction` | `TransactionType`, `TransactionDescription` |
| **Reporting** | Cross-context dashboard assembly | — | `SpendingSummary` |

### Routes

| Method | Path            | Action                        | Name               |
|--------|-----------------|-------------------------------|---------------------|
| GET    | `/`             | `DashboardAction::__invoke`   | `dashboard`         |
| GET    | `/history`      | `HistoryAction::index`        | `history`           |
| POST   | `/history`      | `HistoryAction::filter`       | `history.filter`    |
| GET    | `/budgets`      | `BudgetAction::index`         | `budgets`           |
| POST   | `/budgets`      | `BudgetAction::update`        | `budgets.update`    |
| POST   | `/transactions` | `TransactionAction::__invoke` | `transactions.store`|

## Key Domain Components

### Aggregate Roots

**Budget** (`Budget/Domain/Budget.php`):
- Private constructor; created via `Budget::create()` or `Budget::reconstitute()`
- `updateAmount(MoneyAmount)` — returns new instance (immutable)
- `remaining(MoneyAmount $spent)` — domain calculation
- `percentUsed(MoneyAmount $spent)` — domain calculation
- Invariant: amount must be positive

**Transaction** (`Transaction/Domain/Transaction.php`):
- Factory: `Transaction::record(TransactionType, TransactionDescription, MoneyAmount, DateTimeImmutable)`
- Invariants: non-empty description, positive amount, valid type

### Value Objects

- **MoneyAmount** — wraps decimal string; `fromString()`, `fromFloat()`, `zero()`, `subtract()`, `isPositive()`, `toFloat()`, `toString()`
- **TransactionDescription** — wraps non-empty trimmed string; `fromString()`, `toString()`
- **SpendingSummary** — readonly snapshot: `remaining()`, `percentUsed()`
- **BudgetType** / **TransactionType** — backed string enums

### Repository Interfaces (Ports)

**BudgetRepositoryInterface**: `findByType()`, `findAll()`, `save()`

**TransactionRepositoryInterface**: `save()`, `weeklySpent()`, `monthlySpent()`, `transactionsThisWeek()`, `transactionsForMonth()`, `yearsWithTransactions()`, `monthlySpendingByCategory()`

## Database Schema

**Database**: SQLite — file at `var/data/weeklybudget.sqlite`

**Table `transactions`**: `id` (INTEGER PK AUTOINCREMENT), `dateAdded` (TEXT), `type` (TEXT), `description` (TEXT), `amount` (REAL)

**Table `budgets`**: `id` (INTEGER PK AUTOINCREMENT), `budgetType` (TEXT), `amount` (INTEGER) — seeded with weekly=200, monthly=800

Schema file: `Schema/weeklyBudget.sql`

## Code Conventions

- **Strict types** declared in every PHP file
- **DDD namespacing**: `App\{Context}\{Layer}\{Class}` (e.g., `App\Budget\Domain\Budget`)
- **Class names**: PascalCase (`Budget`, `RecordTransactionHandler`)
- **Methods**: camelCase (`weeklySpent`, `toFloat`)
- **Constructor promotion** with `readonly` for dependency injection
- **Backed enums** for type-safe domain values
- **Pipe operator (`|>`)** for data transformation chains in infrastructure repositories
- **`#[\NoDiscard]`** on query methods and domain calculations to prevent silent discard
- **`readonly` Value Objects** — immutable, equality by value
- **Aggregate Roots** — private constructors, named factory methods, invariant enforcement
- **Command DTOs** — `readonly class` with public properties
- **Query handlers** — `__invoke()`-able, return DTOs or arrays
- **PHPDoc blocks** on all public methods
- **Twig templates**: `*.html.twig` with DaisyUI component classes
- **No `echo`/`print`** — all output via Twig rendering

## Build & Run

### Docker (recommended)

```bash
cp .env.example .env    # edit credentials if needed
docker compose up       # app at http://localhost:8080
```

The entrypoint script initialises the SQLite database from `Schema/weeklyBudget.sql` on first run.

### Local development

Requirements: PHP 8.5+ with pdo_sqlite, Composer, sqlite3 CLI

```bash
composer install
cp .env.example .env
mkdir -p var/data
sqlite3 var/data/weeklybudget.sqlite < Schema/weeklyBudget.sql
php -S localhost:8080 -t public
```

### Railway deployment

Railway auto-detects the Dockerfile. Attach a volume mounted at `/app/var/data` to persist the SQLite database across deploys. The entrypoint script reads Railway's dynamic `PORT` env var and initialises the database on first boot.

### Tailwind CSS (production build)

Templates use CDN for development. For production:

```bash
# Download standalone CLI
curl -sLO https://github.com/tailwindlabs/tailwindcss/releases/latest/download/tailwindcss-linux-x64
chmod +x tailwindcss-linux-x64

# Build
./tailwindcss-linux-x64 -i resources/input.css -o public/css/app.css --minify
```

## Testing

PestPHP v4 with Mockery. 44 unit tests covering domain Value Objects, Aggregate Roots, command handlers, and the Command Bus.

```bash
composer test          # or: vendor/bin/pest
```

## Linting & Static Analysis

- **PHPStan** at max level (9): `composer analyse`
- **PHPCS** with PSR-12: `composer lint` (auto-fix: `composer lint:fix`)
- **All checks**: `composer check`

CI runs all three via GitHub Actions on PRs to master.

## Important Notes for AI Assistants

- **Dependencies managed via Composer** — run `composer install` after cloning. No npm required (frontend is CDN).
- **Database path** is configured via `DB_PATH` in `.env` (git-ignored). Defaults to `var/data/weeklybudget.sqlite`. Never commit `.env`. Use `.env.example` as template.
- **PSR-4 autoloading** — no `require_once` needed. Add new classes under `src/` with correct namespace.
- **Adding a new feature** — identify the bounded context, add domain objects first, then application handlers, then infrastructure (repository adapter + HTTP action). Register routes in `config/routes.php`.
- **Adding a new bounded context** — create `src/{Context}/Domain/`, `Application/Command/`, `Application/Query/`, `Infrastructure/Action/`. Bind repository interfaces in `config/container.php`.
- **Repository interfaces live in Domain** — implementations in Infrastructure. DI container binds port → adapter.
- **Twig auto-escapes** HTML by default — safer than raw PHP views. Use `|raw` only for trusted content.
- **Repositories use raw SQL** via Cycle DBAL (not the Cycle ORM query builder). Queries are parameterized.
- **FrankenPHP worker mode** — the app boots once. All services (repositories, handlers, actions) are stateless singletons safe for reuse across requests. Avoid storing request-scoped state in static properties or global variables.
- **SQLite in worker mode** — SQLite uses a local file, so there are no idle TCP connection drops. The Cycle DBAL driver is configured with `reconnect: true` as a safety net. No health-check ping is needed in the worker loop.
- **Railway deployment** — the `docker-entrypoint.sh` binds FrankenPHP to `$PORT` (Railway-injected) and initialises the SQLite database at runtime (Railway volumes aren't mounted at build time). Attach a volume at `/app/var/data` in Railway's dashboard.
- **No ORM** — repositories use raw DBAL queries, not the Cycle ORM entity manager. Only `cycle/database` is installed. This avoids heap accumulation across worker requests.
- **DaisyUI components** — use DaisyUI class names (`card`, `table`, `btn`, `badge`, etc.) in templates. Refer to https://daisyui.com/components/.
- **Alpine.js** — used for client-side interactivity (chart init, toast auto-dismiss). Directives like `x-data`, `x-init`, `x-show` are in Twig templates.
- **PHP 8.5 features** — use pipe operator (`|>`) for chained data transforms, `#[\NoDiscard]` for methods whose results must be consumed, `readonly` classes for immutable value objects and entities, and `static` closures in DI definitions for worker safety.
