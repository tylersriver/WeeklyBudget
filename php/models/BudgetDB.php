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
     public static function getWeeklySpent()
     {   
         $sql = "SELECT sum(amount) as total from Transactions where WEEKOFYEAR(dateAdded) = WEEKOFYEAR(NOW())";
         $result = query($sql);

        $val = ($result[0]['total'] == null) ? 0 : $result[0]['total'];

         return $val;
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
         $total = BudgetDB::getWeeklySpent();
         
         $sql = 'SELECT amount from budgets where budgetType = ?';
         $result = query($sql, array($description));
         $budget = $result[0]['amount'];
 
         return $budget - $total;
     }

     /**
      * Get the budget setting for the week 
      *
      * @param string $type
      * @return string
      */
     public static function getBudgetSetting($type)
     {
         $sql = "SELECT amount from budgets where budgetType = ?";
         $result = query($sql, array($type));
         return $result[0]['amount'];
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

    /**
     * Insert the transaction into the DB
     * 
     * @param string $type
     * @param string $description
     * @param string amount
     * @param string date
     */
    public static function insertTransaction($type, $description, $amount, $date)
    {
        $sql = 'INSERT INTO transactions (type, description, amount, dateAdded) values (?,?,?,?)';
        query($sql, array($type, $description, $amount, $date));
    }
}