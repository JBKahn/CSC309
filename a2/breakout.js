(function() {
    "use strict";
    // Variables needed by more than 1 function.
    var interval,
        // Constants
        //

        // brick
        SPACE_ABOVE_BRICKS = 50,
        ROW_COUNT = 8,
        COLUMN_COUNT = 14,
        // Score
        SPEED_MULTIPLIER = 1.20,
        // paddle position
        PADDLE_HEIGHT = 10,
        INITIAL_PADDLE_WIDTH = 100,
        PADDLE_COLOR = "#C84848",
        // bricks
        BRICK_COLORS = ["#C84848", "#C66C3A", "#48A048", "#A2A22A"],
        //
        // Variiables
        //
        // Ball
        ball,
        // brick
        bricks,
        brick_width,
        brick_height,
        // Canvas
        canvasContext,
        canvasWidth,
        canvasHeight,
        canvasRightBoundary,
        // Score
        score = 0,
        running = false,
        scaledBy = 1,
        // paddle position
        paddleX,
        paddleWidth,
        // arrow keys
        rightKeyDown = false,
        leftKeyDown = false;

    // Canvas Helpers
    //



    function drawPaddle(x, w) {
        canvasContext.fillStyle = PADDLE_COLOR;
        drawRectangle(x, canvasHeight - PADDLE_HEIGHT, w, PADDLE_HEIGHT);
    }

    function drawRectangle(x, y, w, h) {
        canvasContext.beginPath();
        canvasContext.rect(x, y, w, h);
        canvasContext.closePath();
        canvasContext.fill();
    }


    function initializeBricks() {
        var row, column;
        bricks = [];
        for (row = 0; row < ROW_COUNT; row += 1) {
            bricks[row] = [];
            for (column = 0; column < COLUMN_COUNT; column += 1) {
                bricks[row][column] = new Brick(
                    (column * brick_width), (row * brick_height) + SPACE_ABOVE_BRICKS,
                    brick_width,
                    brick_height);
            }
        }
    }

    function drawBricks() {
        var row, column;
        for (row = 0; row < ROW_COUNT; row += 1) {
            canvasContext.fillStyle = BRICK_COLORS[row / 2];
            for (column = 0; column < COLUMN_COUNT; column += 1) {
                if (bricks[row][column].exists) {
                    bricks[row][column].draw(canvasContext);
                }
            }
        }
    }



    function handlePaddleMovement() {
        if (ball.y + ball.dy < 0) {
            paddleWidth = INITIAL_PADDLE_WIDTH / 2;
        }
        paddleX += (rightKeyDown && 5) || (leftKeyDown && -5) || 0;
        paddleX = Math.min(canvasWidth - paddleWidth, paddleX);
        paddleX = Math.max(0, paddleX);
        drawPaddle(paddleX, paddleWidth);
    }

    function handleBallMovement() {
        var ballAtPaddleHeight,
            vallAbovePaddle;
        // bounce off left and right walls
        ball.dx = ball.dx * (((ball.x + ball.dx > canvasWidth || ball.x + ball.dx < 0) && -1) || 1);
        ball.dy = ball.dy * (((ball.y + ball.dy < 0) && -1) || 1);

        // Check if player hit the paddle (the ball is at the right height and above the paddle)
        ballAtPaddleHeight = ball.y + ball.dy > canvasHeight - PADDLE_HEIGHT;
        vallAbovePaddle = ball.x > paddleX && ball.x < paddleX + paddleWidth;

        if (ballAtPaddleHeight && vallAbovePaddle) {
            // reverse direction and change angle.
            ball.dx = 6 * (((ball.x - paddleX) / paddleWidth) - 0.5);
            ball.dy = -ball.dy;
        }
        // move the ball
        ball.x += ball.dx;
        ball.y += ball.dy;
    }

    function handleGameStateChanges(hitBrick) {
        var ballHitBittom,
            lost;
        // Check if the level is beaten
        if (hitBrick && score % 448 === 0 && score > 0) {
            handleBeatLevel();
            return;
        }

        // Check if player has lost a life
        ballHitBittom = ball.y + ball.dy > canvasHeight;
        if (ballHitBittom) {
            lost = loseLife();
            clearInterval(interval);
            if (lost >= 0) {
                continueGame();
            } else {
                canvasContext.fillStyle = "White";
                canvasContext.font = '' + sc(30) + 'px "PressStart2P"';
                canvasContext.fillText("You Have Lost!", 85, 200);
            }
        }
    }

    function updateCanvas() {
        // If we hit a brick then do stuff.
        var hitBrick = ball.handleHitBrick();

        // redraw!
        canvasContext.clearRect(0, 0, canvasWidth, canvasHeight);
        drawBricks();
        ball.draw(canvasContext);

        // Paddle updates
        handlePaddleMovement();

        // Ball updates
        handleBallMovement();

        // Changes to state i.e. lost life or won level
        handleGameStateChanges(hitBrick);
    }

    function handleBeatLevel() {
        clearInterval(interval);
        if (score / 448 == 2) {
            canvasContext.fillStyle = "White";
            canvasContext.font = '' + sc(30) + 'px "PressStart2P"';
            canvasContext.fillText("You Have Won!", sc(85), sc(200));
        } else {
            initializeBricks();
            startGame();
        }
    }

    function continueGame() {
        startGame();
    }

    function restartGameHandler(e) {
        if (e.keyCode === 82) {
            restartGame();
        }

    }

    function restartGame() {
        clearInterval(interval);
        running = true;
        paddleWidth = INITIAL_PADDLE_WIDTH;
        score = 0;
        document.getElementById("livesField").innerHTML = 3;
        initializeBricks();
        startGame();
        ball.dy = 4;
    }

    function pauseGame(e) {
        if (e.keyCode === 80) {
            if (running) {
                canvasContext.font = '' + sc(30) + 'px "PressStart2P"';
                canvasContext.fillText("PAUSED", sc(150), sc(200));
                clearInterval(interval);
            } else {
                interval = setInterval(updateCanvas, 12/Math.max(sc(1,1)));
            }
            running = !running;
        }
    }

    function startGame() {
        ball = new Ball((canvasWidth) / 2, (canvasHeight) / 2, (Math.random() - .5) * 4, ball.dy);
        // reset scoreboard
        updateScore();

        // reset arrow keys to false
        leftKeyDown = false;
        rightKeyDown = false;

        clearInterval(interval);
        interval = setInterval(updateCanvas, 12/Math.max(sc(1,1)));
    }

    function maximizeCanvas(context) {
        var widthRatio = window.innerWidth / context.canvas.width;
        var heightRatio = (window.innerHeight * 0.9) / context.canvas.height;

        var ratio = Math.min(widthRatio, heightRatio);
        context.canvas.width = context.canvas.width * ratio;
        context.canvas.height = context.canvas.height * ratio;
        return ratio;
    }



    function initializeCanvas() {
        // get a reference to the canvas
        canvasContext = document.getElementById("canvas").getContext("2d");
        scaledBy = maximizeCanvas(canvasContext);
        // set height and width variables from DOM
        canvasWidth = document.getElementById("canvas").offsetWidth;
        canvasHeight = document.getElementById("canvas").offsetHeight;

        // draw bricks
        brick_width = (canvasWidth / COLUMN_COUNT);
        brick_height = (canvasHeight / 30);
        SPACE_ABOVE_BRICKS = sc(SPACE_ABOVE_BRICKS);
        INITIAL_PADDLE_WIDTH = sc(INITIAL_PADDLE_WIDTH);
        PADDLE_HEIGHT = sc(PADDLE_HEIGHT);
        PADDLE_HEIGHT = sc(PADDLE_HEIGHT);
        paddleWidth = INITIAL_PADDLE_WIDTH;

        initializeBricks();
        drawBricks();

        canvasContext.fillStyle = "White";
        // Font not working here, but is everywehre else. Due to loading after initialize is called.
        canvasContext.font = '' + sc(30) + 'px "PressStart2P"';
        canvasContext.fillText("Press 'r' to start the game,", sc(85), sc(115));
        canvasContext.fillText("or to Restart it at any time", sc(120), sc(150));
        canvasContext.fillText("To pause, hit 'p'", sc(155), sc(190));



        // draw the ball and paddle
        ball = new Ball((canvasWidth) / 2, (canvasHeight) / 2, (Math.random() - 0.5) * 4, 4);
        paddleX = (canvasWidth - paddleWidth) / 2;

        ball.draw(canvasContext);
        drawPaddle(paddleX, paddleWidth);

        // start the game when the player clicks the button
        //document.getElementById("canvas").addEventListener("click", restartGame);

    }

    // Event Handlers
    //

    function movePaddleWithKeyboard(e) {
        rightKeyDown = rightKeyDown || e.keyCode === 39;
        leftKeyDown = leftKeyDown || e.keyCode === 37;
    }

    function stopMovePaddleWithKeyboard(e) {
        if (rightKeyDown && e.keyCode === 39) {
            rightKeyDown = false;
        }
        if (leftKeyDown && e.keyCode === 37) {
            leftKeyDown = false;
        }
    }

    function movePaddleWithMouse(e) {
        paddleX = e.pageX - document.getElementById("canvas").offsetLeft - (paddleWidth / 2);
    }

    document.addEventListener("mousemove", movePaddleWithMouse);
    document.addEventListener("keydown", movePaddleWithKeyboard);
    document.addEventListener("keyup", stopMovePaddleWithKeyboard);
    document.addEventListener("keyup", pauseGame);
    document.addEventListener("keyup", restartGameHandler);

    // Scaore and life handling
    //
    function updateScore() {
        document.getElementById("scoreBoardField").innerHTML = score;
    }

    function loseLife() {
        if (document.getElementById("livesField").innerHTML - 1 < 0) {
            return -1;
        }
        document.getElementById("livesField").innerHTML = document.getElementById("livesField").innerHTML - 1;
        return document.getElementById("livesField").innerHTML;
    }

    function incrimentScore() {
        switch (Math.floor((ball.y - SPACE_ABOVE_BRICKS) / brick_height)) {
            case 6:
            case 7:
                score = score + 1;
                break;
            case 4:
            case 5:
                score = score + 3;
                break;
            case 3:
            case 2:
                score = score + 5;
                break;
            case 1:
            case 0:
                score = score + 7;
                break;
        }
        updateScore();
    }

    initializeCanvas();

    function Brick(x, y, w, h) {
        this.x = x;
        this.y = y;
        this.w = w;
        this.h = h;
        this.exists = true;

        this.draw = function(context) {
            context.beginPath();
            context.rect(this.x - 1, this.y - 1, this.w + 2, this.h + 2);
            context.closePath();
            context.fill();
        };
    }

    function Ball(x, y, dx, dy) {
        this.r = sc(5);
        this.x = x - (this.r / 2);
        this.y = y;
        this.dx = dx;
        this.dy = dy;
        this.exists = true;
        this.colour = "#C84848";
        this.numOrangeHit = 0;
        this.numRedHit = 0;
        this.numBricksHit = 0;

        this.draw = function(context) {
            context.fillStyle = this.colour;
            context.beginPath();
            context.arc(this.x, this.y, this.r, 0, Math.PI * 2, true);
            context.closePath();
            context.fill();
        };

        this.handleHitBrick = function() {
            var row = Math.floor((this.y - SPACE_ABOVE_BRICKS) / brick_height),
                column = Math.floor(this.x / brick_width);

            if (ball.y < ((ROW_COUNT * brick_height) + SPACE_ABOVE_BRICKS) && (this.y > SPACE_ABOVE_BRICKS) && bricks[row][column].exists === true) {
                // bounce
                this.dy = -this.dy;

                // brick was hit
                bricks[row][column].exists = false;

                this.setSpeed(row);

                // increase the score
                incrimentScore();
                return true;
            }
            return false;
        };


        this.setSpeed = function(rowHit) {
            if (rowHit === 0 || rowHit === 1) {
                this.numRedHit++;
            } else if (rowHit === 2 || rowHit === 3) {
                this.numOrangeHit++;
            }
            this.numBricksHit++;

            if ((this.numBricksHit === 4) ||
                (this.numBricksHit === 12) ||
                (rowHit === 3 && this.numOrangeHit === 1) ||
                (rowHit === 1 && this.numRedHit === 1)) {
                this.dy = this.dy * SPEED_MULTIPLIER;
            }
        };
    }

    // scale all the values
    function sc(x) {
        return x * scaledBy;
    }

}());
