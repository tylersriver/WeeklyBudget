<?php

class PagesController 
{
    /**
     * Lands on the home page
     */
    public function overview() 
    {
        // Add Includes used in View
        require_once('viewmodels/transactions.viewmodel.php');

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
        require_once('views/pages/overview.php');
    }

    /**
     * Show history page
     */
    public function monthHistory()
    {
        // Add Includes used in View
        require_once('viewmodels/transactions.viewmodel.php');
        
        // Transactions table
        $month = ( isset($_POST['month']) ) ? $_POST['month'] : date('n');
        $year = ( isset($_POST['year']) ) ? $_POST['year'] : date('Y');
        $table = TransactionsViewModel::getMonthsTransactionsTable($month, $year);
        $title = "Month Transaction History";

        $years = BudgetDB::getYearsForTransactions();

        // Show view
        require_once('views/pages/history.php');
    }

    /** 
     * Show error page
     */
    public function error() 
    {
        require_once('views/pages/error.php');
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
        require_once('views/pages/budgets.php');
    }

    /** 
     * Show login page
     */
    public function login() 
    {
        require_once('views/pages/login.php');
    }
}

