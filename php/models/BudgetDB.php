<?php

/**
 * Class BudgetDB
 *
 * This class adds functionality to the 
 * bas MySQL Tool to add specific methods
 * used by the WeeklyBudget
 */
class BudgetDB extends SimpleORM
{
    protected static $table = "transactions";
    protected static $key = "id";
    protected static $fields = "id, budgetType, amount";

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
      * Get the sum of transactions for the current month
      *
      * @return float
      */
     public static function getMonthlySpent()
     {
         $sql = "SELECT 
                    sum(amount) as total 
                from Transactions 
                where MONTH(dateAdded) = MONTH(now()) 
                    and YEAR(dateAdded) = YEAR(now())";
        $result = query($sql);
        return ($result[0]['total'] == null) ? 0 : $result[0]['total'];
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
        // Get Total spend for budget type
        switch ($description) {
            case 'weekly': 
                $total = BudgetDB::getWeeklySpent();
                break;
            case 'monthly':
                $total = BudgetDB::getMonthlySpent();
                break;
            default:
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
      * @param string $type
      * @return string
      */
     public static function getBudgetSetting($type)
     {
         $sql = "SELECT amount from budgets where budgetType = ?";
         $result = query($sql, array($type));
         return $result[0]['amount'];
     }

     /**
      * Retrieves data for each transaction for this week
      * @return array
      */
     public static function getTransactionsThisWeek()
     {
         $sql = "select 
                    id as ID, 
                    date_format(dateAdded, '%m/%d/%Y') as `Date`,
                    type as Type, 
                    description as Description, 
                    amount as Amount
                from transactions 
                where WEEKOFYEAR(dateAdded) = WEEKOFYEAR(NOW())";
        return query($sql);
     }

     /**
      * Get all current budgets
      * @return array
      */
     public static function getCurrentBudgets()
     {
         $sql = 'select budgetType as Budget, amount as Amount from budgets';
         return query($sql);
     }

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