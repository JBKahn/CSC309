<center>
    <h3>Order Reciept for Order <?=$order_id?></h3>
    <h2>Order Total: $<?=number_format((float)$total, 2, '.', '')?></h2>

    <table class="table receipt">
        <tr>
            <th>Quantity</th>
            <th>Name</th>
            <th>Price (each)</th>
            <th>Subtotal</th>
        </tr>
    <?php
        for ($i = 0; $i<count($quantity); ++$i) {
    ?>
            <tr>
                <td><?=$quantity[$i]?></td>
                <td><?=$name[$i]?></td>
                <td><?=number_format((float)$price[$i], 2, '.', '')?></td>
                <td><?=number_format((float)$quantity[$i]*$price[$i], 2, '.', '')?></td>
            </tr>
    <?php
        }
    ?>
    </table>
</center>