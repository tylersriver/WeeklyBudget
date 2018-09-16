<?php

class BudgetsController
{
    /**
     * Update a budget setting
     */
    public function update()
    {
        $type = (isset($_POST['type'])) ? $_POST['type'] : '';
        $amount = (isset($_POST['amount'])) ? $_POST['amount'] : '';

        // Update budget
        BudgetDB::setBudget($type, $amount);

        // Redirect to budgets page
        require_once 'pages.controller.php';
        $home = new PagesController();
        $home->budgets();
    }
}