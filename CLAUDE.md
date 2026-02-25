# CLAUDE.md — WeeklyBudget

## Project Overview

WeeklyBudget is a PHP web application for tracking personal weekly and monthly budget transactions. Users can add expenses, view spending summaries with progress bars, browse transaction history by month, and update budget limits.

## Tech Stack

- **Language**: PHP (procedural + OOP, no framework)
- **Database**: MySQL via MySQLi with parameterized queries
- **Frontend**: Bootstrap 4.0.0, jQuery 3.2.1, Popper.js 1.12.9 (all CDN)
- **Custom CSS**: `styles/global-styles.css`
- **No package manager** (no Composer, no npm)
- **No build step** — PHP files are served directly by a web server

## Directory Structure

```
WeeklyBudget/
├── index.php                          # Entry point — loads models, resolves controller/action from query string
├── routes.php                         # Router — dispatches to controller methods, validates allowed routes
├── Schema/
│   └── weeklyBudget.sql              # MySQL schema (transactions + budgets tables)
├── php/
│   ├── controllers/
│   │   ├── pages.controller.php      # PagesController — overview, monthHistory, budgets, error
│   │   ├── transactions.controller.php # TransactionsController — insert
│   │   └── budgets.controller.php    # BudgetsController — update
│   ├── models/
│   │   ├── SimpleSQL.php             # DB connection singleton + query() function with auto param binding
│   │   ├── SimpleORM.php             # Generic ORM base class (Get, GetList, Add, Update, Delete, etc.)
│   │   └── BudgetDB.php             # Domain ORM — extends SimpleORM with budget/transaction queries
│   ├── viewmodels/
│   │   └── transactions.viewmodel.php # TransactionsViewModel — builds SimpleTable objects for views
│   ├── views/
│   │   ├── layout.php               # Main HTML layout (navbar, footer, CDN includes)
│   │   └── pages/
│   │       ├── overview.php         # Dashboard — budget progress bars + transaction form + weekly table
│   │       ├── history.php          # Month/year transaction history filter
│   │       ├── budgets.php          # View/update budget limits
│   │       └── error.php            # Error page
│   └── lib/
│       └── SimpleTable.php          # Bootstrap HTML table generator + BootStrapTableClasses enum
├── styles/
│   └── global-styles.css            # Custom styles
└── .vscode/
    └── launch.json                  # XDebug config (port 9000)
```

## Architecture & Request Flow

1. `index.php` — includes all models, reads `controller` and `action` from `$_GET`, defaults to `pages`/`overview`
2. `routes.php` — whitelist of valid controller/action pairs, calls `call($controller, $action)`
3. Controller method — fetches data via `BudgetDB` static methods, sets view variables, then `require_once` the view
4. View — renders HTML with inline PHP using the variables set by the controller

Routing is query-string based: `?controller=pages&action=budgets`. Forms use POST for data, GET params for routing.

## Key Classes

### SimpleSQL (`php/models/SimpleSQL.php`)
- Singleton MySQL connection (`SimpleSQL::getInstance()`)
- Global `query($sql, $params)` function — handles prepared statements with auto type detection
- Helper functions: `makeValuesReferenced()`, `buildTypeStringFromArray()`

### SimpleORM (`php/models/SimpleORM.php`)
- Abstract base with static CRUD: `Get($id)`, `GetList($where)`, `GetOne($where)`, `Add($values)`, `Update($id, $set)`, `UpdateMany($set, $where)`, `Delete($id)`, `DeleteMany($where)`
- Subclasses define `$table`, `$key`, `$fields` as protected static properties

### BudgetDB (`php/models/BudgetDB.php`)
- Extends SimpleORM for the `transactions` table
- Domain methods: `getWeeklySpent()`, `getMonthlySpent()`, `getRemaining($type)`, `getBudgetSetting($type)`, `getTransactionsThisWeek()`, `getTransactionsForMonth($year, $month)`, `getCurrentBudgets()`, `getYearsForTransactions()`, `setBudget($type, $amount)`, `insertTransaction($type, $description, $amount, $date)`

### SimpleTable (`php/lib/SimpleTable.php`)
- Generates Bootstrap `<table>` HTML from associative arrays
- Supports table classes via `BootStrapTableClasses` constants (Striped, Bordered, Hover, Small, Dark)
- Auto-detects column headers from the first data row

## Database Schema

**Database**: `WeeklyBudget`

**Table `Transactions`**: `id` (int PK auto), `dateAdded` (date), `type` (text), `description` (text), `amount` (decimal 4,2)

**Table `budgets`**: `id` (int PK auto), `budgetType` (text), `amount` (int) — seeded with weekly=$200, monthly=$800

Schema file: `Schema/weeklyBudget.sql`

## Code Conventions

- **Class names**: PascalCase (`BudgetDB`, `SimpleTable`, `PagesController`)
- **Methods**: camelCase (`getWeeklySpent`, `insertTransaction`)
- **Controllers**: `{name}.controller.php` — class named `{Name}Controller`
- **Models**: PascalCase filenames matching class names
- **Views**: lowercase filenames in `php/views/pages/`
- **Static methods** for all ORM/database operations
- **Ternary operators** for null/default handling on `$_POST`/`$_GET` values
- **PHPDoc blocks** on all public methods
- **No namespaces** — files loaded via `require_once`

## Build & Run

No build step. Requirements:
- PHP 5.4+ with MySQLi extension
- MySQL server
- Web server (Apache/Nginx) pointed at the project root

Setup:
1. Import `Schema/weeklyBudget.sql` into MySQL
2. Configure DB credentials in `php/models/SimpleSQL.php` (hardcoded)
3. Point web server document root to the project directory
4. Access via browser at the web server URL

## Testing

No automated tests, test framework, or CI/CD pipeline exists in this project.

## Linting & Formatting

No linter, formatter, or pre-commit hooks are configured.

## Important Notes for AI Assistants

- **No dependency manager**: There is no `composer.json` or `package.json`. All PHP is vanilla; frontend libs are CDN-linked.
- **Database credentials are hardcoded** in `SimpleSQL.php`. Do not commit real credentials.
- **No autoloading**: All files are loaded via explicit `require_once` chains starting from `index.php`. When adding new files, add corresponding `require_once` statements.
- **No input validation layer**: SQL injection is mitigated by parameterized queries in `query()`, but there is no HTML escaping or CSRF protection. Be mindful when outputting user data in views.
- **The `query()` function** is global (not a class method) and is the sole interface for all DB queries.
- **SimpleORM uses late static binding** (`static::$table`, `static::$key`) — subclasses must define these protected static properties.
- **Views use variables set directly by controllers** — there is no template engine. Variables are in-scope because controllers `require_once` the view file after setting them.
- **Routing whitelist** in `routes.php` — new controller actions must be registered in the `$controllers` array.
