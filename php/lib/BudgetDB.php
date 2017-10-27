<?php

require_once "MySQL_Tool.php";

/**
 * Class BudgetDB
 *
 * This class adds functionality to the 
 * bas MySQL Tool to add specific methods
 * used by the WeeklyBudget
 */
class BudgetDB extends MySQL_Tool
{
    public function __construct()
    {
        parent::__construct();
    }


    // ** Select Functions ** //
    // ******************************************************************************************************************* //
    // ******************************************************************************************************************* //

    /**
     * Get the total sum of transactions for the week
     *
     * @return float
     */
     public function getWeeklyTotal()
     {   
         $sql = "SELECT sum(amount) from Transactions where WEEKOFYEAR(dateOccured) = WEEKOFYEAR(NOW())";
         $result = $this->query($sql);
         $arr = $result->fetch_row();
         $weeklyTotal = $arr[0];
         return $weeklyTotal;
     }
 
     /**
      * Get the remaining amount for the
      * given description
      * 
      * @param string $description
      *
      * @return float
      */
     public function getRemaining($description)
     {
         $total = $this->getWeeklyTotal();
         
         $sql = 'SELECT amount from weekMaxValues where description = ?';
         $result = $this->query($sql, array($description));
         $arr = $result->fetch_row();
         $budget = $arr[0];
 
         return $budget - $total;
     }

     /**
      * Get the budget setting for the week 
      *
      * @return string
      */
     public function getWeeklyBudgetSetting()
     {
         $sql = "SELECT amount from weekMaxValues where description = 'spending'";
         $result = $this->query($sql);
         $arr = $result->fetch_row();
         $budget = $arr[0];

         return $budget;
     }


    // ** Insert/Update Functions ** //
    // ******************************************************************************************************************* //
    // ******************************************************************************************************************* //
}