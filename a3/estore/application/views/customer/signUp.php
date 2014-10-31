<h2>Register As a Customer On Our Site</h2>
<form class="form-horizontal" role="form" action='<?= base_url();?>store/addCustomer' method="post">
    <div class="form-group">
        <label class="col-sm-2 control-label"><?php echo "First Name" ?></label>
        <div class="col-sm-10">
            <input type="text" name="first" value="" class="form-control" size="50"/>
            <?php echo form_error('first', '<div class="text-danger">', '</div>'); ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label"><?php echo "Last Name" ?></label>
        <div class="col-sm-10">
            <input type="text" name="last" value="" class="form-control" size="50"/>
            <?php echo form_error('last', '<div class="text-danger">', '</div>'); ?>
        </div>
    </div>
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
        <label class="col-sm-2 control-label"><?php echo "Email" ?></label>
        <div class="col-sm-10">
            <input type="email" name="email" value="" class="form-control" size="50"/>
            <?php echo form_error('email', '<div class="text-danger">', '</div>'); ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-lg-offset-3 col-lg-10">
            <button type="submit" class="btn btn-primary">Create</button>
        </div>
    </div>
</form>