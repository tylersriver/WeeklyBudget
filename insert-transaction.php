<?php

require_once "MySQL_Tool.php";

// Pull POST Variables
$date = $_POST['date'];
$description = $_POST['description'];
$amount = $_POST['amount'];

// Insert Into DB
$conn = new MySQL_Tool();

$sql = "INSERT INTO Transactions (dateOccured, description, amount) VALUES (?,?,?)";
$params = array($date, $description, $amount);
$conn->query($sql, $params);
$conn->close();

// Redirect Home
header( "Location: http://localhost/weeklybudget/home.php" );
exit();