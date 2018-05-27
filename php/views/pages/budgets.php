<div class="container">
    <div class='row'>
        <div class="baseContainer col-12">
            <h2>Current Budgets</h2>
            <div class='col-12'>
                <?php echo $budgetTable; ?>
            </div>
        </div>
    </div>
    <div class='row'>
        <div class="baseContainer col-12">
            <h2>Update Budgets</h2>
            <form class="form-horizontal" action="?controller=budgets&action=update" method='POST'>
                <div class="form-group row">
                    <label for="type" class="col-sm-2 col-form-label">Type</label>
                    <div class="col-sm-10">
                        <select name='type' class="form-control" id="type">
                            <option value='weekly'>Weekly</option>
                            <option value='monthly'>Monthly</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="amount" class="col-sm-2 col-form-label">New Amount</label>
                    <div class="col-sm-10">
                        <input type="number" name='amount' class="form-control" id="amount" placeholder="Amount">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
            </form>
        </div>
    </div>
</div>