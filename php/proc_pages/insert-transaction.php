<?php

require_once "../lib/BudgetDB.php";

// Pull POST Variables
$date = $_POST['date'];
$description = $_POST['description'];
$amount = $_POST['amount'];

// Insert Into DB
$sql = "INSERT INTO Transactions (dateOccured, description, amount) VALUES (?,?,?)";
$params = array($date, $description, $amount);
BudgetDB::query($sql, $params);

// Redirect Home
header( "Location: http://localhost/weeklybudget/php/home.php" );
exit();