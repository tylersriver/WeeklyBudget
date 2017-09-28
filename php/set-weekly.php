<?php
require_once "lib/lib-includes.php";

$conn = new BudgetDB();
$weeklyTotal = $conn->query("Select amount from from weekMaxValues where description = 'spending'")

?>
<div>
    <h2>Weekly Budget Total</h2>
    <p><?php echo "$".$weeklyTotal; ?> </p>
</div>

<div>
    <h2>Update Weekly Amount</h2>
    <form action="proc_pages/insert-transaction.php" method="post">
        <table>
            <tr>
                <td valign="top">
                    <label for="amount">Amount</label>
                </td>
                <td valign="top">
                    <input type="number" name="amount">
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