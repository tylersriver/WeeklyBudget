<div class="container">
    <div class="row">
        <div class="baseContainer col-12">
            <h2>Login</h2>
            <form class="form-horizontal" action="?controller=user&action=login" method='POST'>
                <div class="form-group row">
                    <label for="username" class="col-sm-2 col-form-label col-form-label-sm">Username</label>
                    <div class="col-sm-10">
                        <input type="text" name='username' class="form-control form-control-sm" id="username" placeholder="Username">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="password" class="col-sm-2 col-form-label col-form-label-sm">Password</label>
                    <div class="col-sm-10">
                        <input type="password" name='password' step="0.01" class="form-control form-control-sm" id="password" placeholder="Password">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Login</button><span style='color: red'> <?= $_GET['error'] ?></span>
            </form>
            <br>
        </div>
    </div>
</div>