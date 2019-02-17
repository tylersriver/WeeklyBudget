<?php
session_start();

// PHP Includes
// ---------------------------------------------------------------------
define("MYSQL_SERVER", "localhost");
define("MYSQL_USER", "dev_user");
define("MYSQL_PASSWORD", "ILoveJennifer712");
define("MYSQL_DB", "WeeklyBudget");

require_once __DIR__ . '/lib/vendor/autoload.php';

// Register Autoload for classes
spl_autoload_register(function($class) {
    $directories = [
        'lib/' => '.php',
        'models/' => '.php',
        'viewmodels/' => '.php',
        'controllers/' => '.php'
    ];

    foreach($directories as $directory => $fileAppend) {
        $filePath = __DIR__ . '/' . $directory . $class . $fileAppend;
        if(file_exists($filePath)) {
            require $filePath;
            return;
        }
    }
});

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