<?php
require_once "lib/lib-includes.php";

// Pull remaining for week
$conn = new BudgetDB();
$weeklyTotal = $conn->getWeeklyTotal();
$remaining = $conn->getRemaining('spending');

?>

<div>
    <h2>This Week</h2>
    <p>Spent: <?php echo "$".$weeklyTotal; ?> </p>
    <p>Remaining: <?php echo "$".$remaining; ?> </p>
</div>

<div>
    <h2>Insert Purchase</h2>
    <form action="proc_pages/insert-transaction.php" method="post">
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