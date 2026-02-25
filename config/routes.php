<?php

declare(strict_types=1);

use Slim\App;
use App\Reporting\Infrastructure\Action\DashboardAction;
use App\Transaction\Infrastructure\Action\HistoryAction;
use App\Budget\Infrastructure\Action\BudgetAction;
use App\Transaction\Infrastructure\Action\TransactionAction;

return function (App $app): void {
    // Dashboard
    $app->get('/', DashboardAction::class)->setName('dashboard');

    // History
    $app->get('/history', [HistoryAction::class, 'index'])->setName('history');
    $app->post('/history', [HistoryAction::class, 'filter'])->setName('history.filter');

    // Budgets
    $app->get('/budgets', [BudgetAction::class, 'index'])->setName('budgets');
    $app->post('/budgets', [BudgetAction::class, 'update'])->setName('budgets.update');
    $app->post('/budgets/activate', [BudgetAction::class, 'activate'])->setName('budgets.activate');

    // Categories
    $app->post('/categories', [BudgetAction::class, 'addCategory'])->setName('categories.store');
    $app->post('/categories/delete', [BudgetAction::class, 'deleteCategory'])->setName('categories.delete');

    // Transactions
    $app->post('/transactions', TransactionAction::class)->setName('transactions.store');
};
