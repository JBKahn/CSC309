<h2>Enter Your Credit Card Information</h2>

<form class="form-horizontal" role="form" action='<?= base_url();?>store/payForm' method="post">
    <div class="form-group">
        <label class="col-sm-2 control-label"><?php echo "Credit Card Number" ?></label>
        <div class="col-sm-10">
            <input type="text" name="creditcard_number" value="" class="form-control" maxlength="16" size="50"/>
            <?php echo form_error('creditcard_number', '<div class="text-danger">', '</div>'); ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label"><?php echo "Expiry Month (MM)" ?></label>
        <div class="col-sm-10">
            <input type="text" name="creditcard_month" value="" class="form-control" maxlength="2" size="50"/>
            <?php echo form_error('creditcard_month', '<div class="text-danger">', '</div>'); ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label"><?php echo "Expiry Year (YY)" ?></label>
        <div class="col-sm-10">
            <input type="text" name="creditcard_year" value="" class="form-control" maxlength="2" size="50"/>
            <?php echo form_error('creditcard_year', '<div class="text-danger">', '</div>'); ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-lg-offset-3 col-lg-10">
            <button type="submit" class="btn btn-primary">Pay Now</button> or <a href="<?= base_url();?>store/cart" class="btn btn-primary">Go Back to Cart</a>
        </div>
    </div>
</form>