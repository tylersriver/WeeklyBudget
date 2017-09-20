<?php

// Pull Total For Week
require_once "MySQL_Tool.php";
$conn = new MySQL_Tool();
$weeklyTotal = $conn->getWeeklyTotal();
$remaining = $conn->getRemaining('spending');

?>
<html>
<!-- Created by Tyler Sriver on 4/10/2017 -->
<head>
    <title>Weekly Budget</title>
    <link rel="stylesheet" type="text/css" href="styles/global-styles.css">
</head>
<body>
<header>
    <h1>Sriver Budget</h1>
    <ul>
        <li><a href="home.php" class="button">Home</a></li>
        <li><a href="set-weekly.php" class="button">Budget</a></li>
    </ul>
</header>

<div>
    <h2>This Week</h2>
    <p>Spent: <?php echo $weeklyTotal; ?> </p>
    <p>Remaining: <?php echo $remaining ?> </p>
</div>

<div>
    <h2>Insert Purchase</h2>
    <form action="insert-transaction.php" method="post">
        <table>
            <tr>
                <td valign="top">
                    <label for="date">Date</label>
                </td>
                <td valign="top">
                    <input type="date" name="date">
                </td>
            </tr>
            <tr>
                <td valign="top">
                    <label for="description">Description</label>
                </td>
                <td valign="top">
                    <input type="text" name="description">
                </td>
            </tr>
            <tr>
                <td valign="top">
                    <label for="amount">Amount</label>
                </td>
                <td valign="top">
                    <input type="decimal" step="0.01" name="amount">
                </td>
            </tr>
            <tr>
                <td style="text-align:center">
                    <input type="submit" value="Insert">
                </td>
            </tr>
        </table>
    </form>
</div>

</body>