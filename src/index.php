<?php
session_start();

// PHP Includes
// ---------------------------------------------------------------------
define("MYSQL_SERVER", "localhost");
define("MYSQL_USER", "dev_user");
define("MYSQL_PASSWORD", "ILoveJennifer712");
define("MYSQL_DB", "WeeklyBudget");

require_once "../vendor/tylersriver/php-simple-sql/src/SimpleSQL.php";
use SimpleSQL\SQL as SQL;
require_once "../vendor/tylersriver/php-simple-sql/src/SimpleORM.php";

require_once "models/BudgetDB.php"; 
require_once "models/User.php"; 
require_once "lib/SimpleTable.php"; 

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

require_once('views/layout.php');