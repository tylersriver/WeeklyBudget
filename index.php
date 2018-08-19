<?php
session_start();

// PHP Includes
// ---------------------------------------------------------------------
require_once "php/models/SimpleSQL.php";
require_once "php/models/SimpleORM.php";

require_once "php/models/BudgetDB.php"; 
require_once "php/models/User.php"; 
require_once "php/lib/SimpleTable.php"; 

// Handle Routing
// ---------------------------------------------------------------------
if(isset($_GET['controller']) && isset($_GET['action']) ) {
    $controller = $_GET['controller'];
    $action = $_GET['action'];
} else {
    $controller = 'pages';
    $action = 'overview';
}

// Handle Login
// ---------------------------------------------------------------------
if(!isset($_SESSION['login_user']) and $controller != 'user' and $action != 'login') {
    $controller = 'pages';
    $action = 'login';
    $_GET['error'] = '';
}

require_once('php/views/layout.php');