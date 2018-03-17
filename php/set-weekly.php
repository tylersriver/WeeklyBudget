<?php
require_once "lib/lib-includes.php";

$weeklyTotal = BudgetDB::getWeeklyBudgetSetting();

?>
<div>
    <h2>Weekly Budget Total</h2>
    <p><?php echo "$".$weeklyTotal; ?> </p>
</div>

<div>
    <h2>Update Weekly Amount</h2>
    <form name="budgetForm" action="proc_pages/update-weekly-budget.php" method="post">
        <table>
        <tr>
                <td valign="top">
                    <label for="budgetType">Type</label>
                </td>
                <td valign="top">
                    <select name="budgetType" form="budgetForm">
                        <option value='weekly'>Weekly</option>
                    </select>
                </td>
            </tr>
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