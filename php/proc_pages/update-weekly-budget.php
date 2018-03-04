<?php

require_once "../lib/BudgetDB.php";

// Pull POST Variables
$amount = $_POST['amount'];
$budgetType = $_POST['budgetType'];

// Insert Into DB
$conn = new BudgetDB();
$conn->setBudget($budgetType, $amount);
$conn->close();

// Redirect To Weekly
header( "Location: http://localhost/xampp/WeeklyBudget/php/set-weekly.php" );
exit();