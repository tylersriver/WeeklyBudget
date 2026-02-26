<?php

declare(strict_types=1);

use Slim\App;
use App\Reporting\Infrastructure\Action\DashboardAction;
use App\Transaction\Infrastructure\Action\HistoryAction;
use App\Budget\Infrastructure\Action\BudgetAction;
use App\Transaction\Infrastructure\Action\TransactionAction;
use App\Estimator\Infrastructure\Action\EstimatorAction;

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

    // Estimator
    $app->get('/estimator', [EstimatorAction::class, 'index'])->setName('estimator');
    $app->post('/estimator/income', [EstimatorAction::class, 'addIncome'])
        ->setName('estimator.income.store');
    $app->post('/estimator/income/delete', [EstimatorAction::class, 'removeIncome'])
        ->setName('estimator.income.delete');
    $app->post('/estimator/expense', [EstimatorAction::class, 'addExpense'])
        ->setName('estimator.expense.store');
    $app->post('/estimator/expense/delete', [EstimatorAction::class, 'removeExpense'])
        ->setName('estimator.expense.delete');
};
