<?php 

/**
 * Calls the associated action from the controller
 * 
 * @param string $controller
 * @param string $action
 */
function call($controller, $action)
{
    switch($controller) {
      case 'pages':
        $controller = new PagesController();
        break;
      case 'transactions':
        $controller = new TransactionsController();
        break;
      case 'budgets':
        $controller = new BudgetsController();
        break;
      case 'user':
        $controller = new UserController();
        break;
    }

    $controller->{ $action }();
}

  // we're adding an entry for the new controller and its actions
  $controllers = array(
    'pages' => [
      'overview', 
      'error', 
      'budgets', 
      'monthHistory',
      'login'
    ],
    'transactions' => [
      'insert'
    ],
    'budgets' => [
      'update'
    ],
    'user' => [
      'login',
      'logout'
    ]
  );

  // Handling calling the controller
  if (array_key_exists($controller, $controllers)) {
    if (in_array($action, $controllers[$controller])) {
      call($controller, $action);
    } else {
      call('pages', 'error');
    }
  } else {
    call('pages', 'error');
  }