<div class="container">
    <div class="row">
        <div class="baseContainer col-12">
            <h2>Remaining</h2>
            <div class="col-12">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th>Budget</th>
                            <th style='width: 60%'>Spent</th>
                            <th>Remaining</th>
                        </tr>
                        <tr>
                            <td>This Week</td>
                            <td>
                                <div class="progress" style="height: 30px;">
                                <div class="progress-bar budget-progress" style="width: <?= ($weeklySpent/$weeklyBudget*100); ?>%;">$<?= $weeklySpent; ?></div>
                            </div>
                            </td>
                            <td>$<?= $weeklyRemaining; ?> of $<?= $weeklyBudget ?></td>
                        </tr>
                        <tr>
                            <td>Monthly</td>
                            <td>
                                <div class="progress" style="height: 30px;">
                                <div class="progress-bar budget-progress bg-success" style="width: <?= ($monthlySpent/($monthlyBudget)*100); ?>%;">$<?= $monthlySpent; ?></div>
                            </div>
                            </td>
                            <td>$<?= $monthlyRemaining ?> of $<?= $monthlyBudget ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row ">
        <div class="baseContainer col-12">
            <h2>Transactions This Week</h2>
            <?= $transactionsTable; ?>
        </div>
    </div>
    <div class="row">
        <div class="baseContainer col-12">
            <h2>Add Transaction</h2>
            <form class="form-horizontal" action="?controller=transactions&action=insert" method='POST'>
                <div class="form-group row">
                    <label for="type" class="col-sm-2 col-form-label col-form-label-sm">Type</label>
                    <div class="col-sm-10">
                        <select name='type' class="form-control form-control-sm" id="type">
                            <option>Food</option>
                            <option>Groceries</option>
                            <option>Gas</option>
                            <option>Shopping</option>
                            <option>Other</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="description" class="col-sm-2 col-form-label col-form-label-sm">Description</label>
                    <div class="col-sm-10">
                        <input type="text" name='description' class="form-control form-control-sm" id="description" placeholder="Description">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="date" class="col-sm-2 col-form-label col-form-label-sm">Amount</label>
                    <div class="col-sm-10">
                        <input type="number" name='amount' step="0.01" class="form-control form-control-sm" id="amount" placeholder="Amount">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="date" class="col-sm-2 col-form-label col-form-label-sm">Date</label>
                    <div class="col-sm-10">
                        <input type="date" name='date' class="form-control form-control-sm" id="date" placeholder="Date">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
            </form>
        </div>
    </div>
</div>