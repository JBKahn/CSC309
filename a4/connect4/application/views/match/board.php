<!DOCTYPE html>
<html>
    <head>
    <script src="http://code.jquery.com/jquery-latest.js"></script>
    <script src="<?= base_url() ?>/js/jquery.timers.js"></script>
    <link rel="stylesheet" href="<?=  base_url(); ?>css/template.css">
    <script>
        var otherUser = "<?= $otherUser->login ?>";
        var user = "<?= $user->login ?>";
        var status = "<?= $status ?>";
        var playerNum = "<?= $playerNum ?>";
        var board = new Array();
        for(var i = 0; i < 42; i++) {
            board.push(0);
        }
        board.push(1);
        var match_status = 1;

        $(function(){
            $('body').everyTime(2000,function(){
                if (status == 'waiting') {
                    $.getJSON('<?= base_url() ?>arcade/checkInvitation',function(data, text, jqZHR){
                        if (data && data.status == 'rejected') {
                            alert("Sorry, your invitation to play was declined!");
                            window.location.href = '<?= base_url() ?>arcade/index';
                        }
                        if (data && data.status == 'accepted') {
                            status = 'playing';
                            $('#status').html('Playing ' + otherUser);
                        }

                    });
                }

                $.getJSON('<?= base_url() ?>board/getGameState',function(data, text, jqZHR){
                    if (data && data.status == 'success') {
                        match_status = Number(JSON.parse(data.match_status));
                        board = JSON.parse(data.board_state) || board;
                        updateGameBoard();

                        switch(match_status) {
                            case 1:
                                break;
                            case 4:
                                $('#status').html('Tie Game!');
                                break;
                            case (Number(playerNum) + 1):
                                $('#status').html('You won!');
                                break;
                            default:
                                $('#status').html('You lost!');
                        }
                    }
                });

                var url = "<?= base_url() ?>board/getMsg";
                $.getJSON(url, function (data,text,jqXHR){
                    if (data && data.status == 'success') {
                        var conversation = $('[name=conversation]').val();
                        var msg = data.message;
                        if (msg && msg.length > 0)
                            $('[name=conversation]').val(conversation + "\n" + otherUser + ": " + msg);
                    }
                });
            });

            $('form').submit(function(){
                var args = $(this).serialize();
                var url = "<?= base_url() ?>board/postMsg";
                $.post(url,args, function (data,textStatus,jqXHR){
                    var conversation = $('[name=conversation]').val();
                    var msg = $('[name=msg]').val();
                    $('[name=conversation]').val(conversation + "\n" + user + ": " + msg);
                });
                return false;
            });

            $('.slot').click(function(){
                if(board[42] == playerNum && match_status == 1) {
                    $('.hover-column').removeClass('hover-column');
                    // Update the current players screen
                    var index = $('.slot').index(this);
                    board[index] = playerNum;

                    // post the move
                    var column = index % 7;
                    var url = "<?= base_url() ?>board/postGameState";
                    var clicked = this;
                    $.post(url,{column_clicked : column},function(data,textStatus,jqXHR){});
                }
            });

            $('.slot').hover(function(){
                if(board[42] == playerNum && match_status == 1)  {
                    var column = $('.slot').index(this) % 7;
                    for(var row = 0;row < 6; row++) {
                        $('.slot').eq(row * 7 + column).addClass('hover-column');
                    }
                }
            },function(){
                $('.hover-column').removeClass('hover-column');
            });

            function updateGameBoard() {
                for(var slot = 0; slot < 42; slot++) {
                    if(board[slot] == 1) {
                        $('.slot').eq(slot).addClass('red');
                    }
                    if(board[slot] == 2) {
                        $('.slot').eq(slot).addClass('yellow');
                    }
                }
            }
        });
    </script>
    </head>
<body>
    <h1>Game Area</h1>
    <div>
        Hello <?= $user->fullName() ?>  <?= anchor('account/logout', '(Logout)') ?>
    </div>

    <div id='status'>
        <?php
            if ($status == "playing") {
                echo "Playing " . $otherUser->login;
            } else {
                echo "Wating on " . $otherUser->login;
            }
        ?>
    </div>

    <div id='CheckersBoard'>
        <?php
            for ($row = 0; $row < 6; $row++) {
                echo '<div class="row">';
                for ($column = 0; $column < 7; $column++) {
                    echo '<div class="slot"></div>';
                }
                echo '</div>';
            }
        ?>
    </div>

    <?php
        echo form_textarea('conversation');

        echo form_open();
        echo form_input('msg');
        echo form_submit('Send', 'Send');
        echo form_close();
    ?>
</body>
</html>