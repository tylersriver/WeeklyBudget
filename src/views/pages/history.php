<div class="container">
    <div class="row">
        <div class="baseContainer col-12">
            <h2>View History</h2>
            <form class="form-horizontal" action="?controller=pages&action=monthHistory" method='POST'>
                <div class="form-row align-items-center">
                    <div class="col-sm-4">
                        <select name='month' class="form-control" id="month">
                            <option value='1'>January</option>
                            <option value='2'>February</option>
                            <option value='3'>March</option>
                            <option value='4'>April</option>
                            <option value='5'>May</option>
                            <option value='6'>June</option>
                            <option value='7'>July</option>
                            <option value='8'>August</option>
                            <option value='9'>September</option>
                            <option value='10'>October</option>
                            <option value='11'>November</option>
                            <option value='12'>December</option>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <select name='year' class="form-control" id="year">
                            <?php 
                            foreach($years as $year) {
                                echo "<option value = '$year'>$year</option>";
                            } ?>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <button type="submit" class="btn btn-primary">View</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="baseContainer col-12">
            <h2><?= $title ?></h2>
            <div class='col-12'>
                <?php echo $table; ?>
            </div>
        </div>
    </div>
</div>