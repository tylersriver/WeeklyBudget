<?php

class PagesController 
{
    /**
     * Lands on the home page
     */
    public function overview() 
    {
        // Add Includes used in View
        require_once('php/viewmodels/transactions.viewmodel.php');

        // Setup View Variables
        $weeklyRemaining = BudgetDB::getRemaining('weekly');
        $monthlyRemaining = BudgetDB::getRemaining('monthly');
        $weeklySpent = BudgetDB::getWeeklySpent();
        $monthlySpent = BudgetDB::getMonthlySpent();
        $weeklyBudget = BudgetDB::getBudgetSetting('weekly');
        $monthlyBudget = BudgetDB::getBudgetSetting('monthly');

        // Get Transactions table
        $transactionsTable = TransactionsViewModel::getThisWeeksTransactionsTable();

        // Show view
        require_once('php/views/pages/overview.php');
    }

    /**
     * Show history page
     */
    public function history()
    {
        // Show view
        require_once('php/views/pages/history.php');
    }

    /** 
     * Show error page
     */
    public function error() 
    {
        require_once('php/views/pages/error.php');
    }

    /**
     * Show budgets page
     */
    public function budgets()
    {
        // Current Budgets Table
        $currentBudgets = BudgetDB::getCurrentBudgets();
        $budgetTable = new SimpleTable('budgets_table');
        $budgetTable->setTableClasses(array(BootStrapTableClasses::Hover, BootStrapTableClasses::Striped, BootStrapTableClasses::Bordered));
        $budgetTable->setData($currentBudgets);
        $budgetTable = $budgetTable->display(true);

        // Show view
        require_once('php/views/pages/budgets.php');
    }

}

