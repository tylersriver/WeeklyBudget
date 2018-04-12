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
        $table->setTableClasses(array(BootStrapTableClasses::Hover, BootStrapTableClasses::Striped));
        $table->setData($data);
        return $table->display(true);
    }
}