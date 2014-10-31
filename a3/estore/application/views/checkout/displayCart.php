<h2>Your Shopping Cart</h2>
<?php
    if (!$items) {
        echo "<p> Your cart is empty, please add items :) </p>";
        return;
    }
?>

<table class="table table-hover">
    <tr>
        <th>Quantity</th>
        <th>Name</th>
        <th>Description</th>
        <th>Price</th>
        <th>Photo</th>
        <th>Actions</th>
    </tr>
<?php
    for ($i = 0; $i<count($items); ++$i) {
        $product = $products[$i];
        $item = $items[$i];
?>
        <tr>
            <td>
                <form class="form-horizontal" role="form" action='<?= base_url();?>store/addOneProductToCart/<?= $product->id ?>' method="post">
                    <div class="form-group">
                        <div class="col-lg-offset-1 col-lg-10">
                            <button type="submit" class="btn btn-primary">+</button>
                        </div>
                    </div>
                </form>
                <p><?=$item->quantity?></p>
                <form class="form-horizontal" role="form" action='<?= base_url();?>store/removeOneProductFromCart/<?= $product->id ?>' method="post">
                    <div class="form-group">
                        <div class="col-lg-offset-1 col-lg-10">
                            <button type="submit" class="btn btn-primary">-</button>
                        </div>
                    </div>
                </form>
            </td>
            <td><?=$product->name?></td>
            <td><?=$product->description?></td>
            <td><?=number_format((float)$product->price, 2, '.', '')?></td>
            <td><img src="<?= base_url();?>images/product/<?= $product->photo_url?>" width='100px'/></td>
            <td><a href="<?= base_url();?>store/removeProductFromCart/<?= $product->id?>">Remove All of the product from the cart</a></td>
        </tr>
<?php
    }
?>
</table>
<p>Total: $<?=number_format((float)$total, 2, '.', '')?></p>
<a href="<?= base_url();?>store/checkout">
    <button type="button" class="btn btn-success btn-lg">Checkout</button>
</a>