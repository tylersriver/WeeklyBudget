<?php
class PagesController 
{
    /**
     * Lands on the home page
     */
    public function overview() 
    {
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
        call('pages', 'overview');
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
        call('pages', 'history');
    }

    /** 
     * Show error page
     */
    public function error() 
    {
        call('pages', 'error');
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
        call('pages', 'budgets');
    }

    /** 
     * Show login page
     */
    public function login() 
    {
        call('pages', 'login');
    }
}

