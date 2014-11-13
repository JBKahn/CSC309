<h2>Product Details</h2>
<table class="table">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Description</th>
        <th>Price</th>
        <th>Photo</th>
    </tr>
    <tr>
        <td><?=$product->id?></td>
        <td><?=$product->name?></td>
        <td><?=$product->description?></td>
        <td><?=$product->price?></td>
        <td><img src="<?= base_url();?>images/product/<?= $product->photo_url?>" width='100px'/></td>
    </tr>
</table>