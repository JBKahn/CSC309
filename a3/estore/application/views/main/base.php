<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title><?php echo "CSC309 A3"; ?></title>
        <link rel="stylesheet" href="<?=  base_url(); ?>assets/bootstrap.min.css">
    </head>
    <body>
        <heading>
            <h1>Crazy Ernie's Amazing Emporium Of Total Bargain Madness</h1>
            <h2>Buy The Best Baseball Cards For The Best Value!</h2>
            <ul class="nav nav-pills" role="tablist">
              <li role="presentation" class="active"><a href="<?= base_url();?>store/index">Home</a></li>
                <?php
                    if (isset($loggedInAs) && $loggedInAs === 'customer') {
                ?>
                <li role="presentation" class="active"><a href="<?= base_url();?>store/cart">View Your Cart</a></li>
                <?php
                    }
                    if (isset($loggedInAs) && !empty($loggedInAs)) {
                ?>
                <li role="presentation" class="active"><a href="<?= base_url();?>store/logout">Logout</a></li>
                <?php
                    }
                ?>
            </ul>
        </heading>
        <main>
            <?php $this->load->view($main);?>
        </main>
    </body>
</html>