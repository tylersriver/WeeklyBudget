<?php

// PHP Includes
require_once('php/models/BudgetDB.php'); 

if(isset($_GET['controller']) && isset($_GET['action']) ) {
    $controller = $_GET['controller'];
    $action = $_GET['action'];
} else {
    $controller = 'pages';
    $action = 'overview';
}

require_once('php/views/layout.php');