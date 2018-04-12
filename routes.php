<?php 
function call($controller, $action)
{
    require_once('php/controllers/' . $controller . '.controller.php');

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
    }

    $controller->{ $action }();
}

  // we're adding an entry for the new controller and its actions
  $controllers = array('pages' => ['overview', 'error', 'budgets', 'history'],
                       'transactions' => ['insert'],
                       'budgets' => ['update']);

  if (array_key_exists($controller, $controllers)) {
    if (in_array($action, $controllers[$controller])) {
      call($controller, $action);
    } else {
      call('pages', 'error');
    }
  } else {
    call('pages', 'error');
  }