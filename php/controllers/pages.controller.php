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
        $remaining = BudgetDB::getRemaining('weekly');
        $spent = BudgetDB::getWeeklySpent();
        $budget = BudgetDB::getBudgetSetting('weekly');
        $budgetMonth = BudgetDB::getBudgetSetting('month');

        // Get Transactions table
        $transactionsTable = TransactionsViewModel::getThisWeeksTransactionsTable();

        // Show view
        require_once('php/views/pages/overview.php');
    }

    /** 
     * Show error page
     */
    public function error() 
    {
        require_once('php/views/pages/error.php');
    }

}

