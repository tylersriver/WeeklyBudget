<?php

declare(strict_types=1);

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Auth\Infrastructure\Action\LandingAction;
use App\Auth\Infrastructure\Action\LoginAction;
use App\Auth\Infrastructure\Action\RegisterAction;
use App\Auth\Infrastructure\Action\LogoutAction;
use App\Auth\Infrastructure\Middleware\AuthMiddleware;
use App\Reporting\Infrastructure\Action\DashboardAction;
use App\Transaction\Infrastructure\Action\HistoryAction;
use App\Budget\Infrastructure\Action\BudgetAction;
use App\Transaction\Infrastructure\Action\TransactionAction;
use App\Estimator\Infrastructure\Action\EstimatorAction;

return function (App $app): void {
    // ── Public routes (no auth required) ──
    $app->get('/', LandingAction::class)->setName('landing');
    $app->get('/login', [LoginAction::class, 'showForm'])->setName('login');
    $app->post('/login', [LoginAction::class, 'login'])->setName('login.submit');
    $app->get('/register', [RegisterAction::class, 'showForm'])->setName('register');
    $app->post('/register', [RegisterAction::class, 'register'])->setName('register.submit');
    $app->post('/logout', LogoutAction::class)->setName('logout');

    // ── Protected routes (auth required) ──
    $app->group('', function (RouteCollectorProxy $group): void {
        // Dashboard
        $group->get('/dashboard', DashboardAction::class)->setName('dashboard');

        // History
        $group->get('/history', [HistoryAction::class, 'index'])->setName('history');
        $group->post('/history', [HistoryAction::class, 'filter'])->setName('history.filter');

        // Budgets
        $group->get('/budgets', [BudgetAction::class, 'index'])->setName('budgets');
        $group->post('/budgets', [BudgetAction::class, 'update'])->setName('budgets.update');
        $group->post('/budgets/activate', [BudgetAction::class, 'activate'])->setName('budgets.activate');

        // Categories
        $group->post('/categories', [BudgetAction::class, 'addCategory'])->setName('categories.store');
        $group->post('/categories/delete', [BudgetAction::class, 'deleteCategory'])->setName('categories.delete');

        // Transactions
        $group->post('/transactions', TransactionAction::class)->setName('transactions.store');

        // Estimator
        $group->get('/estimator', [EstimatorAction::class, 'index'])->setName('estimator');
        $group->post('/estimator/income', [EstimatorAction::class, 'addIncome'])
            ->setName('estimator.income.store');
        $group->post('/estimator/income/delete', [EstimatorAction::class, 'removeIncome'])
            ->setName('estimator.income.delete');
        $group->post('/estimator/expense', [EstimatorAction::class, 'addExpense'])
            ->setName('estimator.expense.store');
        $group->post('/estimator/expense/delete', [EstimatorAction::class, 'removeExpense'])
            ->setName('estimator.expense.delete');
    })->add(AuthMiddleware::class);
};
