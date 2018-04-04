<?php

require_once "SimpleSQL.php";

/**
 * Class BudgetDB
 *
 * This class adds functionality to the 
 * bas MySQL Tool to add specific methods
 * used by the WeeklyBudget
 */
class BudgetDB
{
    // ** Select Functions ** //
    // ******************************************************************************************************************* //
    // ******************************************************************************************************************* //

    /**
     * Get the total sum of transactions for the week
     *
     * @return float
     */
     public static function getWeeklyTotal()
     {   
         $sql = "SELECT sum(amount) as total from Transactions where WEEKOFYEAR(dateAdded) = WEEKOFYEAR(NOW())";
         $result = query($sql);
         return $result[0]['total'];
     }
 
     /**
      * Get the remaining amount for the
      * given description
      * 
      * @param string $description
      *
      * @return float
      */
     public static function getRemaining($description)
     {
         $total = BudgetDB::getWeeklyTotal();
         if($total = null){
             $total = 0;
         }
         
         $sql = 'SELECT amount from budgets where budgetType = ?';
         $result = query($sql, array($description));
         $budget = $result[0]['amount'];
 
         return $budget - $total;
     }

     /**
      * Get the budget setting for the week 
      *
      * @return string
      */
     public static function getWeeklyBudgetSetting()
     {
         $sql = "SELECT amount from budgets where budgetType = 'weekly'";
         $result = query($sql);
         $arr = $result->fetch_row();
         $budget = $arr[0];

         return $budget;
     }


    // ** Insert/Update Functions ** //
    // ******************************************************************************************************************* //
    // ******************************************************************************************************************* //

    /**
     * Set a budget value
     * 
     * @param string $budgetType
     * @param int $amount
     */
    public static function setBudget($budgetType, $amount)
    {
        $sql = "Update budgets set amount = ? where budgetType = ?";
        $params = array($amount, $budgetType);
        query($sql, $params);
    }
}