<form class="form-horizontal" role="form" action='<?= base_url();?>store/login' method="post">
    <div class="form-group">
        <label class="col-sm-2 control-label"><?php echo "User Name" ?></label>
        <div class="col-sm-10">
            <input type="text" name="login" value="" class="form-control" size="50"/>
            <?php echo form_error('login', '<div class="text-danger">', '</div>'); ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label"><?php echo "Password" ?></label>
        <div class="col-sm-10">
            <input type="password" name="password" value="" class="form-control" size="50"/>
            <?php echo form_error('password', '<div class="text-danger">', '</div>'); ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-lg-offset-3 col-lg-10">
            <button type="submit" class="btn btn-success">Login</button> or <a href="<?= base_url();?>store/signUp" class="btn btn-primary">Register</a>
        </div>
    </div>
</form>