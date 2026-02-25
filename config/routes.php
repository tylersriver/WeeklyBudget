<?php

declare(strict_types=1);

use Slim\App;
use App\Action\DashboardAction;
use App\Action\HistoryAction;
use App\Action\BudgetAction;
use App\Action\TransactionAction;

return function (App $app): void {
    // Dashboard
    $app->get('/', DashboardAction::class)->setName('dashboard');

    // History
    $app->get('/history', [HistoryAction::class, 'index'])->setName('history');
    $app->post('/history', [HistoryAction::class, 'filter'])->setName('history.filter');

    // Budgets
    $app->get('/budgets', [BudgetAction::class, 'index'])->setName('budgets');
    $app->post('/budgets', [BudgetAction::class, 'update'])->setName('budgets.update');

    // Transactions
    $app->post('/transactions', TransactionAction::class)->setName('transactions.store');
};
