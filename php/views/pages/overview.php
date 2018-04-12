<div class="container">
    <div class="row">
        <div class="baseContainer col-12">
            <h2>Remaining</h2>
            <div class="col-12">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th>Budget</th>
                            <th style='width: 65%'>Spent</th>
                            <th>Remaining</th>
                        </tr>
                        <tr>
                            <td>Weekly</td>
                            <td>
                                <div class="progress" style="height: 30px;">
                                <div class="progress-bar budget-progress" style="width: <?php echo ($spent/$budget*100); ?>%;">$<?php echo $spent; ?></div>
                            </div>
                            </td>
                            <td>$<?php echo $remaining; ?> of $<?php echo $budget ?></td>
                        </tr>
                        <tr>
                            <td>Monthly</td>
                            <td>
                                <div class="progress" style="height: 30px;">
                                <div class="progress-bar budget-progress bg-success" style="width: <?php echo ($spent/($budget*4)*100); ?>%;">$<?php echo $spent; ?></div>
                            </div>
                            </td>
                            <td>$<?php echo $budget*4 - $spent; ?> of $<?php echo $budget*4 ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="baseContainer col-12">
            <h2>Add Transaction</h2>
            <form class="form-horizontal" action="?controller=transactions&action=insert" method='POST'>
                <div class="form-group row">
                    <label for="type" class="col-sm-2 col-form-label">Type</label>
                    <div class="col-sm-10">
                        <select name='type' class="form-control" id="type">
                            <option>Food</option>
                            <option>Groceries</option>
                            <option>Gas</option>
                            <option>Shopping</option>
                            <option>Other</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="description" class="col-sm-2 col-form-label">Description</label>
                    <div class="col-sm-10">
                        <input type="text" name='description' class="form-control" id="description" placeholder="Description">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="date" class="col-sm-2 col-form-label">Amount</label>
                    <div class="col-sm-10">
                        <input type="number" name='amount' step="0.01" class="form-control" id="amount" placeholder="Amount">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="date" class="col-sm-2 col-form-label">Date</label>
                    <div class="col-sm-10">
                        <input type="date" name='date' class="form-control" id="date" placeholder="Date">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
            </form>
        </div>
    </div>
    <div class="row ">
        <div class="baseContainer col-12">
            <h2>Transactions This Week</h2>
            <?php echo $transactionsTable; ?>
        </div>
    </div>
</div>