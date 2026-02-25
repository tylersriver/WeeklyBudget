# WeeklyBudget Modernization Plan

## Stack

- **Runtime**: FrankenPHP (worker mode, single binary, HTTP/3, Early Hints)
- **Framework**: Slim 4 (micro-framework, PSR-15 middleware)
- **ORM**: Cycle ORM v2 (safe for long-running processes, DataMapper pattern)
- **Templating**: Twig 3 via `slim/twig-view`
- **DI Container**: PHP-DI 7
- **CSS**: Tailwind CSS v4 (standalone CLI, no Node.js required) + DaisyUI 5
- **JS**: Alpine.js 3 (lightweight interactivity) + Chart.js 4 (budget visualization)
- **Database**: MySQL 8
- **PHP**: 8.3+

## Directory Structure

```
WeeklyBudget/
├── public/
│   ├── index.php                    # Front controller + FrankenPHP worker loop
│   └── css/
│       └── app.css                  # Compiled Tailwind output
├── config/
│   ├── container.php                # PHP-DI service definitions
│   ├── routes.php                   # Route registration
│   ├── middleware.php               # Middleware stack
│   └── settings.php                 # App settings (reads .env)
├── src/
│   ├── Action/                      # Route handlers (thin controllers)
│   │   ├── DashboardAction.php      # GET / — overview with budget progress + transaction form
│   │   ├── HistoryAction.php        # GET /history — month/year transaction filter
│   │   ├── BudgetAction.php         # GET /budgets — view budgets; POST /budgets — update
│   │   └── TransactionAction.php    # POST /transactions — insert new transaction
│   ├── Entity/                      # Cycle ORM entities
│   │   ├── Transaction.php
│   │   └── Budget.php
│   ├── Repository/                  # Query logic (replaces BudgetDB static methods)
│   │   ├── TransactionRepository.php
│   │   └── BudgetRepository.php
│   └── Enum/
│       ├── TransactionType.php      # PHP 8.1 enum: Food, Groceries, Gas, Shopping, Other
│       └── BudgetType.php           # PHP 8.1 enum: Weekly, Monthly
├── templates/
│   ├── layout.html.twig             # Base layout: navbar, footer, CDN scripts, Tailwind
│   ├── dashboard.html.twig          # Budget cards + progress bars + transaction form + weekly table
│   ├── history.html.twig            # Month/year filter + transaction table
│   └── budgets.html.twig            # Budget cards + update form
├── resources/
│   └── input.css                    # Tailwind source (@import "tailwindcss")
├── var/
│   ├── cache/                       # Twig compiled templates
│   └── log/
├── .env                             # DB_HOST, DB_NAME, DB_USER, DB_PASS, APP_ENV
├── .env.example
├── composer.json
├── tailwind.config.js               # Content paths: templates/**/*.twig
├── Dockerfile
├── docker-compose.yml
└── CLAUDE.md                        # Updated project documentation
```

## Step-by-Step Implementation

### Step 1: Project Scaffold

Create `composer.json` and install dependencies:

```bash
composer require slim/slim:^4.14
composer require slim/psr7:^1.7
composer require php-di/php-di:^7.0
composer require slim/twig-view:^3.5
composer require twig/twig:^3.14

composer require cycle/orm:^2.9
composer require cycle/annotated:^4.3
composer require cycle/database:^2.15
composer require cycle/schema-builder:^2.11

composer require vlucas/phpdotenv:^5.6
```

PSR-4 autoloading in `composer.json`:
```json
{
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        }
    }
}
```

### Step 2: FrankenPHP Entry Point

`public/index.php` — boots the app once, then handles requests in a loop:

```php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;

// --- Boot phase (runs once) ---
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$container = require __DIR__ . '/../config/container.php';
AppFactory::setContainer($container);
$app = AppFactory::create();

(require __DIR__ . '/../config/middleware.php')($app);
(require __DIR__ . '/../config/routes.php')($app);

// --- Worker mode (FrankenPHP) ---
if (function_exists('frankenphp_handle_request')) {
    $handler = static function () use ($app) {
        $app->run();
        gc_collect_cycles();
    };
    do {
        $running = \frankenphp_handle_request($handler);
    } while ($running);
} else {
    // Standard PHP-FPM fallback
    $app->run();
}
```

### Step 3: DI Container

`config/container.php` — service wiring:

```php
<?php

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Slim\Views\Twig;
use Cycle\Database\Config\DatabaseConfig;
use Cycle\Database\DatabaseManager;
use Cycle\ORM\ORM;
use Cycle\ORM\Factory;

$builder = new ContainerBuilder();

$builder->addDefinitions([

    // Twig
    Twig::class => function () {
        return Twig::create(__DIR__ . '/../templates', [
            'cache' => $_ENV['APP_ENV'] === 'production'
                ? __DIR__ . '/../var/cache'
                : false,
        ]);
    },

    // Database
    DatabaseManager::class => function () {
        return new DatabaseManager(new DatabaseConfig([
            'default'     => 'default',
            'databases'   => ['default' => ['connection' => 'mysql']],
            'connections'  => [
                'mysql' => new \Cycle\Database\Config\MySQLDriverConfig(
                    connection: new \Cycle\Database\Config\MySQL\TcpConnectionConfig(
                        database: $_ENV['DB_NAME'],
                        host:     $_ENV['DB_HOST'],
                        port:     (int) ($_ENV['DB_PORT'] ?? 3306),
                        user:     $_ENV['DB_USER'],
                        password: $_ENV['DB_PASS'],
                    ),
                ),
            ],
        ]));
    },

    // Cycle ORM
    ORM::class => function (ContainerInterface $c) {
        $dbal = $c->get(DatabaseManager::class);
        // Schema compiled from annotated entities
        $schema = (new \Cycle\Schema\Compiler())->compile(
            new \Cycle\Schema\Registry($dbal),
            [/* generators for annotated entities */]
        );
        return new ORM(new Factory($dbal), new \Cycle\ORM\Schema($schema));
    },

    // Repositories
    \App\Repository\TransactionRepository::class => \DI\autowire(),
    \App\Repository\BudgetRepository::class => \DI\autowire(),
]);

return $builder->build();
```

### Step 4: Entities (Cycle ORM)

**`src/Entity/Transaction.php`**:
```php
<?php
namespace App\Entity;

use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Column;

#[Entity(table: 'transactions')]
class Transaction
{
    #[Column(type: 'primary')]
    public int $id;

    #[Column(type: 'date')]
    public \DateTimeImmutable $dateAdded;

    #[Column(type: 'string')]
    public string $type;          // matches TransactionType enum ->value

    #[Column(type: 'string')]
    public string $description;

    #[Column(type: 'decimal(10,2)')]
    public float $amount;
}
```

**`src/Entity/Budget.php`**:
```php
<?php
namespace App\Entity;

use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Column;

#[Entity(table: 'budgets')]
class Budget
{
    #[Column(type: 'primary')]
    public int $id;

    #[Column(type: 'string')]
    public string $budgetType;    // matches BudgetType enum ->value

    #[Column(type: 'integer')]
    public int $amount;
}
```

### Step 5: Enums

**`src/Enum/TransactionType.php`**:
```php
<?php
namespace App\Enum;

enum TransactionType: string
{
    case Food      = 'Food';
    case Groceries = 'Groceries';
    case Gas       = 'Gas';
    case Shopping  = 'Shopping';
    case Other     = 'Other';
}
```

**`src/Enum/BudgetType.php`**:
```php
<?php
namespace App\Enum;

enum BudgetType: string
{
    case Weekly  = 'weekly';
    case Monthly = 'monthly';
}
```

### Step 6: Repositories

**`src/Repository/TransactionRepository.php`** — replaces `BudgetDB` static methods:

Key methods (use Cycle ORM's `Select` or raw `DatabaseManager` queries):
- `getWeeklySpent(): float` — `SUM(amount) WHERE WEEKOFYEAR(dateAdded) = WEEKOFYEAR(NOW())`
- `getMonthlySpent(): float` — `SUM(amount) WHERE MONTH/YEAR = current`
- `getTransactionsThisWeek(): array`
- `getTransactionsForMonth(int $year, int $month): array`
- `insert(TransactionType $type, string $description, float $amount, \DateTimeImmutable $date): void`

**`src/Repository/BudgetRepository.php`**:
- `getBudgetSetting(BudgetType $type): int`
- `getRemaining(BudgetType $type): float` — budget amount minus spent
- `getAll(): array`
- `update(BudgetType $type, int $amount): void`

### Step 7: Routes

`config/routes.php`:

```php
<?php

use Slim\App;

return function (App $app) {
    // Dashboard
    $app->get('/', \App\Action\DashboardAction::class)->setName('dashboard');

    // History
    $app->get('/history', \App\Action\HistoryAction::class)->setName('history');

    // Budgets
    $app->get('/budgets', [\App\Action\BudgetAction::class, 'index'])->setName('budgets');
    $app->post('/budgets', [\App\Action\BudgetAction::class, 'update'])->setName('budgets.update');

    // Transactions
    $app->post('/transactions', \App\Action\TransactionAction::class)->setName('transactions.store');
};
```

### Step 8: Actions (Controllers)

**`src/Action/DashboardAction.php`** — example pattern:

```php
<?php
namespace App\Action;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use App\Repository\TransactionRepository;
use App\Repository\BudgetRepository;

final class DashboardAction
{
    public function __construct(
        private Twig $view,
        private TransactionRepository $transactions,
        private BudgetRepository $budgets,
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        $weeklyBudget    = $this->budgets->getBudgetSetting(BudgetType::Weekly);
        $monthlyBudget   = $this->budgets->getBudgetSetting(BudgetType::Monthly);
        $weeklySpent     = $this->transactions->getWeeklySpent();
        $monthlySpent    = $this->transactions->getMonthlySpent();

        return $this->view->render($response, 'dashboard.html.twig', [
            'weeklyBudget'     => $weeklyBudget,
            'monthlyBudget'    => $monthlyBudget,
            'weeklySpent'      => $weeklySpent,
            'monthlySpent'     => $monthlySpent,
            'weeklyRemaining'  => $weeklyBudget - $weeklySpent,
            'monthlyRemaining' => $monthlyBudget - $monthlySpent,
            'transactions'     => $this->transactions->getTransactionsThisWeek(),
            'transactionTypes' => TransactionType::cases(),
        ]);
    }
}
```

Other actions follow the same pattern: inject repos + Twig, render template.

### Step 9: Twig Templates + DaisyUI

**`templates/layout.html.twig`** — base layout:
```twig
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{% block title %}WeeklyBudget{% endblock %}</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5/themes.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5/full.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    {% block head %}{% endblock %}
</head>
<body class="min-h-screen bg-base-200">

    {# Navbar #}
    <div class="navbar bg-base-100 shadow-lg">
        <div class="flex-1">
            <a href="{{ url_for('dashboard') }}" class="btn btn-ghost text-xl">WeeklyBudget</a>
        </div>
        <div class="flex-none">
            <ul class="menu menu-horizontal px-1">
                <li><a href="{{ url_for('dashboard') }}">Dashboard</a></li>
                <li><a href="{{ url_for('history') }}">History</a></li>
                <li><a href="{{ url_for('budgets') }}">Budgets</a></li>
            </ul>
            {# Dark mode toggle #}
            <label class="swap swap-rotate ml-2">
                <input type="checkbox" class="theme-controller" value="dark" />
                <svg class="swap-on w-6 h-6" ...><!-- sun --></svg>
                <svg class="swap-off w-6 h-6" ...><!-- moon --></svg>
            </label>
        </div>
    </div>

    {# Page content #}
    <main class="container mx-auto px-4 py-8 max-w-5xl">
        {% block content %}{% endblock %}
    </main>

    {# Footer #}
    <footer class="footer footer-center p-4 bg-base-300 text-base-content mt-auto">
        <p>WeeklyBudget &copy; {{ "now"|date("Y") }}</p>
    </footer>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.js"></script>
    {% block scripts %}{% endblock %}
</body>
</html>
```

**`templates/dashboard.html.twig`** — redesigned dashboard:

```twig
{% extends "layout.html.twig" %}

{% block content %}
<div x-data="dashboard()" class="space-y-6">

    {# Budget summary cards #}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {# Weekly budget card #}
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">Weekly Budget</h2>
                <p class="text-3xl font-bold">${{ weeklyRemaining|number_format(2) }} remaining</p>
                <p class="text-sm opacity-70">${{ weeklySpent|number_format(2) }} of ${{ weeklyBudget }} spent</p>
                <progress class="progress progress-primary w-full"
                    value="{{ weeklySpent }}" max="{{ weeklyBudget }}"></progress>
            </div>
        </div>

        {# Monthly budget card #}
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">Monthly Budget</h2>
                <p class="text-3xl font-bold">${{ monthlyRemaining|number_format(2) }} remaining</p>
                <p class="text-sm opacity-70">${{ monthlySpent|number_format(2) }} of ${{ monthlyBudget }} spent</p>
                <progress class="progress progress-secondary w-full"
                    value="{{ monthlySpent }}" max="{{ monthlyBudget }}"></progress>
            </div>
        </div>
    </div>

    {# Spending chart #}
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title">Spending by Category</h2>
            <canvas id="categoryChart" class="max-h-64"></canvas>
        </div>
    </div>

    {# Add transaction form #}
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title">Add Transaction</h2>
            <form method="POST" action="{{ url_for('transactions.store') }}"
                  class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <select name="type" class="select select-bordered w-full" required>
                    <option disabled selected>Type</option>
                    {% for type in transactionTypes %}
                        <option value="{{ type.value }}">{{ type.value }}</option>
                    {% endfor %}
                </select>
                <input name="description" type="text" placeholder="Description"
                       class="input input-bordered w-full" required />
                <input name="amount" type="number" step="0.01" placeholder="Amount"
                       class="input input-bordered w-full" required />
                <input name="date" type="date" value="{{ "now"|date("Y-m-d") }}"
                       class="input input-bordered w-full" required />
                <button type="submit" class="btn btn-primary">Add</button>
            </form>
        </div>
    </div>

    {# This week's transactions table #}
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title">This Week's Transactions</h2>
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th>#</th><th>Date</th><th>Type</th>
                            <th>Description</th><th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        {% for txn in transactions %}
                        <tr>
                            <td>{{ loop.index }}</td>
                            <td>{{ txn.dateAdded|date("m/d/Y") }}</td>
                            <td><span class="badge badge-outline">{{ txn.type }}</span></td>
                            <td>{{ txn.description }}</td>
                            <td>${{ txn.amount|number_format(2) }}</td>
                        </tr>
                        {% else %}
                        <tr><td colspan="5" class="text-center opacity-50">No transactions this week</td></tr>
                        {% endfor %}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
{% endblock %}

{% block scripts %}
<script>
function dashboard() {
    return {
        init() {
            const ctx = document.getElementById('categoryChart');
            if (!ctx) return;
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: {{ categories|json_encode|raw }},
                    datasets: [{ data: {{ categoryTotals|json_encode|raw }} }]
                },
                options: { responsive: true }
            });
        }
    }
}
</script>
{% endblock %}
```

The history and budgets templates follow the same DaisyUI card/table pattern.

### Step 10: Tailwind Build

Download the standalone CLI (no Node.js):

```bash
curl -sLO https://github.com/tailwindlabs/tailwindcss/releases/latest/download/tailwindcss-linux-x64
chmod +x tailwindcss-linux-x64
mv tailwindcss-linux-x64 tailwindcss
```

`tailwind.config.js`:
```js
export default {
    content: ['./templates/**/*.twig'],
    plugins: [require('daisyui')],
    daisyui: { themes: ['light', 'dark'] },
}
```

`resources/input.css`:
```css
@import "tailwindcss";
```

Build: `./tailwindcss -i resources/input.css -o public/css/app.css --watch`

> **Note**: During development, the DaisyUI CDN + Tailwind CDN in the layout template is sufficient. The standalone CLI build is for production optimization. The templates above use CDN for simplicity.

### Step 11: Docker Setup

**`Dockerfile`**:
```dockerfile
FROM dunglas/frankenphp:latest-php8.3-bookworm

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN install-php-extensions pdo_mysql

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

COPY . .
RUN mkdir -p var/cache var/log

EXPOSE 80
ENV FRANKENPHP_CONFIG="worker ./public/index.php"
```

**`docker-compose.yml`**:
```yaml
services:
  app:
    build: .
    ports: ["8080:80"]
    env_file: .env
    environment:
      FRANKENPHP_CONFIG: "worker ./public/index.php"
    volumes:
      - .:/app
    depends_on:
      mysql:
        condition: service_healthy

  mysql:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: ${DB_PASS}
      MYSQL_DATABASE: ${DB_NAME}
    ports: ["3306:3306"]
    volumes:
      - mysql_data:/var/lib/mysql
      - ./Schema/weeklyBudget.sql:/docker-entrypoint-initdb.d/init.sql
    healthcheck:
      test: ["CMD", "mysqladmin", "ping", "-h", "localhost"]
      interval: 5s
      retries: 5

volumes:
  mysql_data:
```

### Step 12: Environment Config

**`.env.example`**:
```
APP_ENV=development

DB_HOST=mysql
DB_PORT=3306
DB_NAME=WeeklyBudget
DB_USER=root
DB_PASS=secret
```

### Step 13: Update CLAUDE.md

Update the project documentation to reflect the new stack, directory structure, conventions, and development workflow.

## Implementation Order

| # | Task | Files | Notes |
|---|------|-------|-------|
| 1 | Remove old PHP files, keep Schema/ | Root cleanup | Preserve git history |
| 2 | Create composer.json + install deps | `composer.json` | All packages from Step 1 |
| 3 | Create .env + settings | `.env`, `config/settings.php` | DB credentials via env |
| 4 | Bootstrap entry point | `public/index.php` | FrankenPHP worker loop |
| 5 | DI container | `config/container.php` | Twig, DB, ORM, repos |
| 6 | Middleware stack | `config/middleware.php` | Error handling, Twig |
| 7 | Enums | `src/Enum/` | TransactionType, BudgetType |
| 8 | Entities | `src/Entity/` | Transaction, Budget |
| 9 | Repositories | `src/Repository/` | Port BudgetDB query logic |
| 10 | Routes | `config/routes.php` | 4 GET + 2 POST |
| 11 | Actions | `src/Action/` | 4 action classes |
| 12 | Base layout template | `templates/layout.html.twig` | DaisyUI navbar, dark mode, CDNs |
| 13 | Dashboard template | `templates/dashboard.html.twig` | Cards, progress, form, table, chart |
| 14 | History template | `templates/history.html.twig` | Filter form + table |
| 15 | Budgets template | `templates/budgets.html.twig` | Budget cards + update form |
| 16 | Docker setup | `Dockerfile`, `docker-compose.yml` | FrankenPHP + MySQL |
| 17 | Tailwind production build | `tailwind.config.js`, CSS | Standalone CLI |
| 18 | Update CLAUDE.md | `CLAUDE.md` | New stack docs |

## Migration Mapping

| Old (current) | New |
|---|---|
| `index.php` + `routes.php` | `public/index.php` + `config/routes.php` |
| `SimpleSQL.php` (singleton + global `query()`) | `Cycle\Database\DatabaseManager` in DI container |
| `SimpleORM.php` (base CRUD) | Cycle ORM `EntityManager` + repositories |
| `BudgetDB.php` (static domain methods) | `TransactionRepository` + `BudgetRepository` |
| `pages.controller.php` | `DashboardAction`, `HistoryAction`, `BudgetAction` |
| `transactions.controller.php` | `TransactionAction` |
| `budgets.controller.php` | `BudgetAction::update()` |
| `transactions.viewmodel.php` + `SimpleTable.php` | Twig templates with DaisyUI `<table>` |
| `layout.php` (Bootstrap 4 CDN) | `layout.html.twig` (DaisyUI + Tailwind CDN) |
| `overview.php` | `dashboard.html.twig` |
| `history.php` | `history.html.twig` |
| `budgets.php` | `budgets.html.twig` |
| Hardcoded DB creds in `SimpleSQL.php` | `.env` file with `vlucas/phpdotenv` |
| `require_once` chains | PSR-4 autoloading via Composer |
| `$_GET['controller']` + `$_GET['action']` routing | Named Slim routes: `/`, `/history`, `/budgets` |
| Bootstrap 4 progress bars | DaisyUI `<progress>` + Chart.js doughnut |
| jQuery | Alpine.js (where needed) |

## UI Redesign Highlights

**Compared to current app:**

1. **Dark mode toggle** — DaisyUI theme-controller swap in navbar
2. **Card-based layout** — each section in a `card bg-base-100 shadow-xl` instead of raw Bootstrap containers
3. **Spending chart** — Chart.js doughnut showing category breakdown (new feature)
4. **Category badges** — transaction types shown as `badge badge-outline` in tables
5. **Responsive grid** — `grid-cols-1 md:grid-cols-2` for budget cards, `md:grid-cols-5` for form fields
6. **Zebra-striped tables** — DaisyUI `table-zebra` replaces Bootstrap striped tables
7. **Inline date picker** — native `<input type="date">` styled with DaisyUI
8. **Empty states** — "No transactions this week" message instead of blank table
9. **Clean URLs** — `/`, `/history`, `/budgets` instead of `?controller=pages&action=overview`
10. **Progress bars** — DaisyUI colored progress (primary/secondary) with remaining amount text
