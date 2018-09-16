<?php 

class TransactionsController 
{
    /**
     * Handles inserting new transaction and redirecting back home
     */
    public function insert()
    {
        // Get Post Variables
        $type = ( isset($_POST['type']) ) ? $_POST['type'] : '';
        $description = ( isset($_POST['description']) ) ? $_POST['description'] : '';
        $amount = ( isset($_POST['amount']) ) ? $_POST['amount'] : 0;
        $date = ( isset($_POST['date']) ) ? $_POST['date'] : '';

        BudgetDB::insertTransaction($type, $description, $amount, $date);

        require_once 'pages.controller.php';
        $home = new PagesController();
        $home->overview();
    }
}