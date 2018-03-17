<?php

require_once "../lib/BudgetDB.php";

// Pull POST Variables
$amount = $_POST['amount'];
$budgetType = $_POST['budgetType'];

// Insert Into DB
BudgetDB::setBudget($budgetType, $amount);

// Redirect To Weekly
header( "Location: http://localhost/xampp/WeeklyBudget/php/set-weekly.php" );
exit();