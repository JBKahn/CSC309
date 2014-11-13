<h2>Product Table</h2>
<?php
    if ($loggedInAs === 'admin') {
        echo "<p>" . anchor('card/newForm','Add New Product') . "</p>";
        echo "<p>" . anchor('admin/viewOrders','View Past Finalized Orders') . "</p>";
        echo "<p>" . anchor('admin/deleteAll', 'Delete all customer and order information', array('onClick' => "return confirm('Are you sure? This action cannot be undone.')"))  . "</p>";
    }
?>
<table class="table table-hover">
    <tr>
        <th>Name</th>
        <th>Description</th>
        <th>Price</th>
        <th>Photo</th>
        <th>Actions</th>
    </tr>
    <?php
        foreach ($products as $product) {
    ?>
        <tr>
            <td><?=$product->name?></td>
            <td><?=$product->description?></td>
            <td><?=$product->price?></td>
            <td><img src="<?= base_url();?>images/product/<?= $product->photo_url?>" width='100px'/></td>
            <td>
    <?php
                if ($loggedInAs === 'admin') {
                    echo anchor("card/read/$product->id",'View');
                    echo anchor("card/editForm/$product->id",'Edit');
                    echo anchor("card/delete/$product->id",'Delete',"onClick='return confirm(\"Do you really want to delete this product?\");'");
                }
                if ($loggedInAs !== 'admin') {
                    echo anchor("cart/addOneProductToCart/$product->id",'Add to Cart');
                }
    ?>
            </td>
        </tr>
    <?php
        }
    ?>
</table>