<?php 
class TransactionsViewModel
{
    /**
     * Build and display a table with all the transactions
     * for this week
     */
    public static function getThisWeeksTransactionsTable()
    {
        // Get Data
        $data = BudgetDB::getTransactionsThisWeek();

        // Build Table
        $table = new SimpleTable('table1');
        $table->setTableClasses(array(BootStrapTableClasses::Hover, BootStrapTableClasses::Small));
        $table->setData($data);
        return $table->display(true);
    }

    /**
     * Build table for the given months transaction
     */
    public static function getMonthsTransactionsTable($month, $year)
    {
        $data = BudgetDB::getTransactionsForMonth($year, $month);
        $table = new SimpleTable('transactionsTable');
        $table->setTableClasses(array(BootStrapTableClasses::Hover, BootStrapTableClasses::Small));
        $table->setData($data);
        return $table->display(true);        
    }
}