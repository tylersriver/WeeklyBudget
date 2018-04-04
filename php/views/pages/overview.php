<div class="row m-1">
    <div class="col-12">
        <div class="container baseContainer">
            <h2>Remaining</h2>
            <div class="remaining-group">
                <div class="row remaining-row">
                    <label class="col-2">This Week</label>
                    <div class="col-10">$150</div>
                </div>
                <div class="row remaining-row">
                    <label class="col-2">This Month</label>
                    <div class="col-10">$400</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="container baseContainer">
            <h2>Add Transaction</h2>
            <form action="">
                <div class="form-group row">
                    <label for="type" class="col-sm-2 col-form-label">Type</label>
                    <div class="col-sm-10">
                        <select class="form-control" id="type">
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
                        <input type="text" class="form-control" id="description" placeholder="Description">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="date" class="col-sm-2 col-form-label">Amount</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" id="amount" placeholder="Amount">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="amount" class="col-sm-2 col-form-label">Date</label>
                    <div class="col-sm-10">
                        <input type="date" class="form-control" id="date" placeholder="Date">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
            </form>
        </div>
    </div>
</div>
<div class="row m-1">
    <div class="container baseContainer">
        <h2>Transactions This Week</h2>
    </div>
</div>