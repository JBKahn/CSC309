<h2>Finalized Orders</h2>

<table class="table">
    <tr>
        <th>Order ID</th>
        <th>Customer ID</th>
        <th>Order Date</th>
        <th>Order Time</th>
        <th>Order Total</th>
    </tr>
<?php
        foreach ($orders as $order) {
?>
    <tr>
        <td><?=$order->id?></td>
        <td><?=$order->customer_id?></td>
        <td><?=$order->order_date?></td>
        <td><?=$order->order_time?></td>
        <td><?=$order->total?></td>
    </tr>
<?php
        }
?>
</table>