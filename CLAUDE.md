# CLAUDE.md — WeeklyBudget

## Project Overview

WeeklyBudget is a PHP web application for tracking personal weekly and monthly budget transactions. Users can add expenses, view spending summaries with progress bars and charts, browse transaction history by month, and update budget limits.

## Tech Stack

- **Language**: PHP 8.5+ (strict types, pipe operator, readonly classes, `#[\NoDiscard]`, enums, constructor promotion)
- **Framework**: Slim 4 (PSR-7 / PSR-15 micro-framework)
- **Runtime**: FrankenPHP (worker mode for persistent-process performance)
- **ORM**: Cycle ORM v2 (annotated entities, DataMapper pattern)
- **Database**: MySQL 8 via Cycle DBAL
- **Templating**: Twig 3 via `slim/twig-view`
- **DI Container**: PHP-DI 7 (PSR-11, autowiring enabled)
- **Frontend**: Tailwind CSS v4 + DaisyUI 5 (CDN), Alpine.js 3, Chart.js 4
- **Environment**: `vlucas/phpdotenv` — credentials in `.env` (never committed)
- **Autoloading**: PSR-4 via Composer (`App\` → `src/`)

## Directory Structure

```
WeeklyBudget/
├── public/
│   └── index.php                    # Front controller + FrankenPHP worker loop
├── config/
│   ├── container.php                # PHP-DI service definitions
│   ├── routes.php                   # Slim route registration
│   ├── middleware.php               # Middleware stack (body parsing, routing, Twig, errors)
│   └── settings.php                 # App settings (reads .env)
├── src/
│   ├── Action/                      # Route handlers (thin controllers)
│   │   ├── DashboardAction.php      # GET /
│   │   ├── HistoryAction.php        # GET|POST /history
│   │   ├── BudgetAction.php         # GET|POST /budgets
│   │   └── TransactionAction.php    # POST /transactions
│   ├── Entity/                      # Cycle ORM entities
│   │   ├── Transaction.php
│   │   └── Budget.php
│   ├── Repository/                  # Query logic
│   │   ├── TransactionRepository.php
│   │   └── BudgetRepository.php
│   └── Enum/
│       ├── TransactionType.php      # Food, Groceries, Gas, Shopping, Other
│       └── BudgetType.php           # Weekly, Monthly
├── templates/
│   ├── layout.html.twig             # Base layout (navbar, dark mode, CDN scripts)
│   ├── dashboard.html.twig          # Budget cards, chart, transaction form, weekly table
│   ├── history.html.twig            # Month/year filter + transaction table
│   └── budgets.html.twig            # Budget table + update form
├── resources/
│   └── input.css                    # Tailwind source (for standalone CLI builds)
├── Schema/
│   └── weeklyBudget.sql             # MySQL schema + seed data
├── var/
│   ├── cache/                       # Twig compiled templates (production)
│   └── log/
├── .env.example                     # Environment template
├── composer.json
├── tailwind.config.js               # Tailwind / DaisyUI config
├── Dockerfile                       # FrankenPHP production image
├── docker-compose.yml               # App + MySQL services
└── CLAUDE.md
```

## Architecture & Request Flow

1. `public/index.php` — loads `.env`, builds DI container, creates Slim app, registers middleware + routes
2. In FrankenPHP worker mode, the app boots **once** then handles requests in a `frankenphp_handle_request()` loop
3. Slim routes dispatch to Action classes (invokable or method-based)
4. Actions inject repositories via constructor (PHP-DI autowiring) and render Twig templates
5. Repositories use `Cycle\Database\DatabaseManager` for raw SQL queries against MySQL

### Routes

| Method | Path            | Action                        | Name               |
|--------|-----------------|-------------------------------|---------------------|
| GET    | `/`             | `DashboardAction::__invoke`   | `dashboard`         |
| GET    | `/history`      | `HistoryAction::index`        | `history`           |
| POST   | `/history`      | `HistoryAction::filter`       | `history.filter`    |
| GET    | `/budgets`      | `BudgetAction::index`         | `budgets`           |
| POST   | `/budgets`      | `BudgetAction::update`        | `budgets.update`    |
| POST   | `/transactions` | `TransactionAction::__invoke` | `transactions.store`|

## Key Components

### Entities (`src/Entity/`)
`readonly` classes with Cycle ORM `#[Entity]` and `#[Column]` attributes. Map directly to the existing `transactions` and `budgets` MySQL tables.

### Repositories (`src/Repository/`)
- **TransactionRepository** — `getWeeklySpent()`, `getMonthlySpent()`, `getTransactionsThisWeek()`, `getTransactionsForMonth()`, `getYearsForTransactions()`, `getMonthlySpendingByCategory()`, `insert()`
- **BudgetRepository** — `getBudgetSetting()`, `getAll()`, `update()`

Both inject `DatabaseManager` and use raw SQL via `$this->dbal->database()->query(...)`.

### Enums (`src/Enum/`)
- `TransactionType` — backed string enum: Food, Groceries, Gas, Shopping, Other
- `BudgetType` — backed string enum: weekly, monthly

### Actions (`src/Action/`)
Thin controllers that inject `Twig` + repositories. Return `$this->view->render(...)` responses. `TransactionAction` does a POST-redirect-GET to the dashboard.

## Database Schema

**Database**: `WeeklyBudget`

**Table `transactions`**: `id` (int PK auto), `dateAdded` (date), `type` (text), `description` (text), `amount` (decimal 4,2)

**Table `budgets`**: `id` (int PK auto), `budgetType` (text), `amount` (int) — seeded with weekly=200, monthly=800

Schema file: `Schema/weeklyBudget.sql`

## Code Conventions

- **Strict types** declared in every PHP file
- **PSR-4** namespacing: `App\Action`, `App\Entity`, `App\Repository`, `App\Enum`
- **Class names**: PascalCase (`DashboardAction`, `TransactionRepository`)
- **Methods**: camelCase (`getWeeklySpent`, `insert`)
- **Constructor promotion** with `readonly` for dependency injection
- **Backed enums** for type-safe domain values
- **Pipe operator (`|>`)** for data transformation chains in repositories
- **`#[\NoDiscard]`** on repository read methods to prevent silent discard
- **`readonly` entities** — immutable data objects
- **PHPDoc blocks** on all public repository methods
- **Twig templates**: `*.html.twig` with DaisyUI component classes
- **No `echo`/`print`** — all output via Twig rendering

## Build & Run

### Docker (recommended)

```bash
cp .env.example .env    # edit credentials if needed
docker compose up       # app at http://localhost:8080
```

The MySQL container auto-imports `Schema/weeklyBudget.sql` on first run.

### Local development

Requirements: PHP 8.5+ with pdo_mysql, MySQL server, Composer

```bash
composer install
cp .env.example .env
# Edit .env with your local DB credentials
php -S localhost:8080 -t public
```

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

No automated tests, test framework, or CI/CD pipeline exists in this project.

## Linting & Formatting

No linter, formatter, or pre-commit hooks are configured.

## Important Notes for AI Assistants

- **Dependencies managed via Composer** — run `composer install` after cloning. No npm required (frontend is CDN).
- **Database credentials** are in `.env` (git-ignored). Never commit `.env`. Use `.env.example` as template.
- **PSR-4 autoloading** — no `require_once` needed. Add new classes under `src/` with correct namespace.
- **Adding routes** — register in `config/routes.php`, create corresponding Action class in `src/Action/`.
- **Twig auto-escapes** HTML by default — safer than raw PHP views. Use `|raw` only for trusted content.
- **Repositories use raw SQL** via Cycle DBAL (not the Cycle ORM query builder) for compatibility with the existing schema. Queries are parameterized.
- **FrankenPHP worker mode** — the app boots once. All services (repositories, actions) are stateless singletons safe for reuse across requests. Avoid storing request-scoped state in static properties or global variables.
- **Worker-mode DB reconnect** — the worker loop pings MySQL before each request and reconnects on failure (guards against idle timeout drops). The MySQL driver is configured with `reconnect: true`.
- **No ORM identity map** — repositories use raw DBAL queries, not the Cycle ORM entity manager. This avoids heap accumulation across worker requests.
- **DaisyUI components** — use DaisyUI class names (`card`, `table`, `btn`, `badge`, etc.) in templates. Refer to https://daisyui.com/components/.
- **Alpine.js** — used for client-side interactivity (chart init, toast auto-dismiss). Directives like `x-data`, `x-init`, `x-show` are in Twig templates.
- **PHP 8.5 features** — use pipe operator (`|>`) for chained data transforms, `#[\NoDiscard]` for methods whose results must be consumed, `readonly` classes for immutable entities, and `static` closures in DI definitions for worker safety.
