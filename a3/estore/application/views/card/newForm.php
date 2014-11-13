<h2>Create a New Product</h2>
<form class="form-horizontal" role="form" action='<?= base_url();?>card/create' method="post" accept-charset="utf-8" enctype="multipart/form-data">
    <div class="form-group">
        <label class="col-sm-2 control-label"><?php echo "Product Name" ?></label>
        <div class="col-sm-10">
            <input type="text" name="name" value="" class="form-control" size="50"/>
            <?php echo form_error('name', '<div class="text-danger">', '</div>'); ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label"><?php echo "Description" ?></label>
        <div class="col-sm-10">
            <input type="text" name="description" value="" class="form-control" size="50"/>
            <?php echo form_error('description', '<div class="text-danger">', '</div>'); ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label"><?php echo "Price" ?></label>
        <div class="col-sm-10">
            <input type="text" name="price" value="" class="form-control" size="50"/>
            <?php echo form_error('price', '<div class="text-danger">', '</div>'); ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label"><?php echo "Photo" ?></label>
        <div class="col-sm-10">
            <input type="file" name="userfile" size="20" />
            <?php if(isset($fileerror)) echo $fileerror; ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-lg-offset-3 col-lg-10">
            <button type="submit" class="btn btn-primary">Create</button>
        </div>
    </div>
</form>