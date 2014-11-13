<h2>Order Completed: View Receipt</h2>
<?php
    echo "<p>" . anchor('store/index','Back to Home') . "</p>";
    echo "<p> Thanks for your purchase, an email has been sent with a copy of the reciept.</p>";
?>

<form>
    <input type=button value="View/Print Reciept" onClick="writeReciept()">
</form>

<script language="JavaScript">
    function writeReciept() {
        top.wRef=window.open('','myconsole',
            'width=550, height=550, left=10, top=10, menubar=1, toolbar=0, status=1, scrollbars=1, resizable=1'
        );

        var order_id = <?php echo json_encode($order_id); ?>,
            total = <?php echo json_encode($total); ?>,
            quantity = <?php echo json_encode($quantity); ?>,
            name = <?php echo json_encode($name); ?>,
            price = <?php echo json_encode($price); ?>,
            buffer = '';

        top.wRef.document.writeln(
            '<html>'
            +   '<head><title>Order Reciept</title></head>'
            +   '<body onLoad="self.focus()">'
            +       '<center>'
            +           '<span><a href=# onclick="window.print();return false;">print receipt</a> or use Ctrl+P</span>'
            +           '<h3>Order Reciept for order ' + order_id + '</h3>'
            +           '<h2>Total: $' + parseFloat(total).toFixed(2) + '</h2>'
            +           '<table border=0 cellspacing=3 cellpadding=3>'
        );

        buffer+=            '<tr><th>Quantity</th><th>Name</th><th>Price (each)</th><th>Item Subtotal</th></tr>';

        for (i = 0; i<quantity.length; i++) {
            buffer+=        '<tr>'
            buffer+=            '<td>'+quantity[i]+'</td>';
            buffer+=            '<td>'+name[i]+'</td>';
            buffer+=            "<td style='text-align:right;'>$"+parseFloat(price[i]).toFixed(2)+'</td>';
            buffer+=            "<td style='text-align:right;'>$"+parseFloat(price[i]*quantity[i]).toFixed(2)+'</td>';
            buffer+=        '</tr>';
        }

        buffer +=       '</table>'


        buffer+=    '</center>'
        buffer+='</body></html>'
        top.wRef.document.writeln(buffer)
        top.wRef.document.close()
}
</script>
