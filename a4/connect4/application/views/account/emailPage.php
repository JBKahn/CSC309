<!DOCTYPE html>

<html>
    <head>
        <link rel="stylesheet" href="<?=  base_url(); ?>css/template.css">
    </head>
    <body>
        <h1>Password Recovery</h1>
        <p>Please check your email for your new password.</p>
<?php 
            if (isset($errorMsg)) {
                echo "<p>" . $errorMsg . "</p>";
            }

        echo "<p>" . anchor('account/index', 'Login') . "</p>";
?>
    </body>
</html>