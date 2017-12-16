<div class="row">
    <div class="col-md-4 col-md-offset-4">
        <div class="login-panel panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Welcome to AROMA. Please Sign In.</h3>
            </div>
            <div class="panel-body">
                <?php if(validation_errors()): ?>
                    <div class="alert alert-danger"><?php print validation_errors(); ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php print $error; ?></div>
                <?php endif; ?>
                <form role="form" method="post">
                    <fieldset>
                        <div class="form-group">
                            <input class="form-control" placeholder="E-mail" name="username" type="email" autofocus>
                        </div>
                        <div class="form-group">
                            <input class="form-control" placeholder="Password" name="password" type="password" value="">
                        </div>
                        
                        <!-- Change this to a button or input when using this as a form -->
                        <button type="submit" name="do" value="Login" class="btn btn-lg btn-success btn-block">Login</button>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>