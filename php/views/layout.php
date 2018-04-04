<DOCTYPE html>
<html>
  <head>
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    <!-- JQuery -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    
    <!-- Popper -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>

    <!-- Main Styles -->
    <link rel="stylesheet" href="/xampp/WeeklyBudget/styles/global-styles.css">

  </head>
  <body>
    <h1 class="budget-h">Budget</h1>
    <header>
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" href='/xampp/WeeklyBudget'>Overview</a>
            </li>
            <li>
                <a class="nav-link" href='/xampp/WeeklyBudget?controller=pages&action=history'>History</a>
            </li>
            <li>
                <a class="nav-link" href='/xampp/WeeklyBudget?controller=pages&action=budget'>Budget</a>
            </li>
        </ul>
    </header>

    <?php require_once('routes.php'); ?>

    <footer>
        <div class="footer"> Copyright Tyler Sriver | 2018 </div
    </footer>
  <body>
<html>