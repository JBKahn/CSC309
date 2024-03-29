<!DOCTYPE html>
<html>
    <head>
        <script src="http://code.jquery.com/jquery-latest.js"></script>
        <link rel="stylesheet" href="<?=  base_url(); ?>css/template.css">
        <script>
            function checkPassword() {
                var p1 = $("#pass1"); 
                var p2 = $("#pass2");
                
                if (p1.val() == p2.val()) {
                    p1.get(0).setCustomValidity("");  // All is well, clear error message
                    return true;
                }   
                else     {
                    p1.get(0).setCustomValidity("Passwords do not match");
                    return false;
                }
            }
            function generateCapchaCode() {
                var newUrl = "<?=base_url()?>" + "securimage/securimage_show.php?" + Math.random();
                document.getElementById("signupcapcha").src = newUrl;
                return false;
            }
        </script>
    </head> 
    <body>
        <h1>New Account</h1>
<?php
        echo form_open('account/createNew');
        echo form_label('Username');
        echo form_error('username');
        echo form_input('username', set_value('username'), "required");
        echo form_label('Password');
        echo form_error('password');
        echo form_password('password', '', "id='pass1' required");
        echo form_label('Password Confirmation');
        echo form_error('passconf');
        echo form_password('passconf', '', "id='pass2' required oninput='checkPassword();'");
        echo form_label('First');
        echo form_error('first');
        echo form_input('first', set_value('first'), "required");
        echo form_label('Last');
        echo form_error('last');
        echo form_input('last', set_value('last'), "required");
        echo form_label('Email');
        echo form_error('email');
        echo form_input('email', set_value('email'), "required");
        echo form_label('Captcha Code');
        echo form_error('captcha_code');
        echo '<br><img id="signupcapcha" src="'.base_url().'/securimage/securimage_show.php" alt="CAPTCHA Image"></img>';
        echo '<input type="text" name="captcha_code" size="10" maxlength="6" />';
        echo '<a href="#" onclick="generateCapchaCode()">[ Generate a new code ]</a>';
        echo form_submit('submit', 'Register');
        echo form_close();
?>  
    </body>

</html>