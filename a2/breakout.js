(function() {
"use strict";
// Variables needed by more than 1 function.
var interval,
    // Constants
    //
    // ball
    BALLL_RADIUS = 5,
    BALL_COLOR = "#C84848",
    BALLY_START = 145,
    // brick
    SPACE_ABOVE_BRICKS = 50,
    ROW_COUNT = 8,
    COLUMN_COUNT = 14,
    BRICK_HEIGHT = 10,
    // Score
    SPEED_MULTIPLIER = 1.25,
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
    ballX, 
    ballY,
    dxBall,
    dyBall,
    // brick
    bricks,
    brick_width,
    // Canvas
    canvasContext,
    canvasWidth, 
    canvasHeight,
    canvasRightBoundary, 
    // Score
    score = 0, 
    // paddle position
    paddleX,
    paddleWidth,
    // arrow keys
    rightKeyDown = false,
    leftKeyDown = false;

// Canvas Helpers
//

function drawBall(x, y) {
    canvasContext.fillStyle = BALL_COLOR;
    canvasContext.beginPath();
    canvasContext.arc(x, y, BALLL_RADIUS, 0, Math.PI * 2, true);
    canvasContext.closePath();
    canvasContext.fill();
}

function drawPaddle(x, y, w, h) {
    canvasContext.fillStyle = PADDLE_COLOR;
    drawRectangle(x, y, w, h);
}

function drawRectangle(x, y, w, h) {
    canvasContext.beginPath();
    canvasContext.rect(x, y, w, h);
    canvasContext.closePath();
    canvasContext.fill();
}

function clearCanvas() {
    canvasContext.clearRect(0, 0, canvasWidth, canvasHeight);
}

// TODO: make this work if the gains are worth it.
// function smartClearCanvas() {
//     // clear the paddle
//     canvasContext.clearRect(0, canvasHeight - PADDLE_HEIGHT, canvasWidth, canvasHeight);
//     // clear the ball
//     canvasContext.save();
//     canvasContext.globalCompositeOperation = 'destination-out';
//     canvasContext.beginPath();
//     canvasContext.arc(ballX-dxBall, ballY-dyBall, 11, 0, Math.PI * 2, true);
//     canvasContext.closePath();
//     canvasContext.fill();
//     canvasContext.restore();
// }

// Canvas
//

function initializeBricks() {
    var row, column;

    bricks = [];
    for (row = 0; row < ROW_COUNT; row += 1) {
        bricks[row] = [];
        for (column = 0; column < COLUMN_COUNT; column += 1) {
            bricks[row].push(1);
        }
    }
}

function drawBricks() {
    var row, column;
    
    for (row = 0; row < ROW_COUNT; row += 1) {
        canvasContext.fillStyle = BRICK_COLORS[row/2];
        for (column = 0; column < COLUMN_COUNT; column += 1) {
            if (bricks[row][column] === 1) {
                drawRectangle(
                    (column * brick_width),
                    (row * BRICK_HEIGHT) + SPACE_ABOVE_BRICKS,
                    brick_width,
                    BRICK_HEIGHT
                );
            }
        }
    }
}

function handleHitBrick() {
    var row = Math.floor((ballY - SPACE_ABOVE_BRICKS)/ BRICK_HEIGHT),
        column = Math.floor(ballX / brick_width);

    if (ballY < ((ROW_COUNT * BRICK_HEIGHT) + SPACE_ABOVE_BRICKS) && (ballY > SPACE_ABOVE_BRICKS) && bricks[row][column] === 1) {
        // bounce
        dyBall = -dyBall

        // brick was hit
        bricks[row][column] = 0;

        setSpeed(row);

        // increase the score
        incrimentScore();
    }
}

function handlePaddleMovement() {
    if (ballY + dyBall < 0) {
        paddleWidth = INITIAL_PADDLE_WIDTH/2;
    }
    paddleX += (rightKeyDown && 5) || (leftKeyDown && -5) || 0;
    paddleX = Math.min(canvasWidth - paddleWidth, paddleX);
    paddleX = Math.max(0, paddleX);
    drawPaddle(paddleX, canvasHeight - PADDLE_HEIGHT, paddleWidth, PADDLE_HEIGHT);
}

function handleBallMovement() {
    var ballAtPaddleHeight,
        vallAbovePaddle;
    // bounce off left and right walls 
    dxBall = dxBall * (((ballX + dxBall > canvasWidth || ballX + dxBall < 0) && -1) || 1);
    dyBall = dyBall * (((ballY + dyBall < 0) && -1) || 1);

    // Check if player hit the paddle (the ball is at the right height and above the paddle)
    ballAtPaddleHeight = ballY + dyBall > canvasHeight - PADDLE_HEIGHT;
    vallAbovePaddle = ballX > paddleX && ballX < paddleX + paddleWidth;

    if (ballAtPaddleHeight && vallAbovePaddle) {
        // reverse direction and change angle.
        dxBall = 6 * (((ballX - paddleX)/paddleWidth) - .5);
        dyBall = -dyBall;
    }
}

function handleGameStateChanges() {
    var ballHitBittom,
        lost;
    // Check if the level is beaten
    if (score % 448 === 0 && score > 0) {
        handleBeatLevel();
        return;
    }

    // Check if player has lost a life
    ballHitBittom = ballY + dyBall > canvasHeight;
    if (ballHitBittom) {
        lost = loseLife();
        clearInterval(interval);
        if (lost >= 0) {
            continueGame()
        } else {
            canvasContext.fillStyle="White"
            canvasContext.font='30px "PressStart2P"';
            canvasContext.fillText("You Have Lost!",85,200);
        }
    }
}

function updateCanvas() {
    // If we hit a brick then do stuff.
    handleHitBrick();

    // redraw!
    clearCanvas();
    drawBricks();
    drawBall(ballX, ballY);

    // Paddle updates
    handlePaddleMovement();
    
    // Ball updates
    handleBallMovement();

    // Changes to state i.e. lost life or won level
    handleGameStateChanges();

    // move the ball
    ballX += dxBall;
    ballY += dyBall;
}

// Soeed
//

function setSpeed(rowHit) {

    var numOrangeLeft = 0,
        numRedLeft = 0,
        numBricksLeft=0,
        row,
        column;
    for (row = 0; row < ROW_COUNT; row++) {
        for (column = 0; column < COLUMN_COUNT; column++) {
            numBricksLeft += bricks[row][column];
            if (row === 2 || row === 3) {
                numOrangeLeft += bricks[row][column];
            } else if (row === 1|| row === 0) {
                numRedLeft += bricks[row][column];
            }
        }
    }

    var numBricksHit = ROW_COUNT * COLUMN_COUNT - numBricksLeft;
    var numOrangeHit = 2*COLUMN_COUNT - numOrangeLeft;
    var numRedHit = 2*COLUMN_COUNT - numRedLeft;

    if ((numBricksHit == 4) || (numBricksHit == 12) || (rowHit == 3  && numOrangeHit === 1) || (rowHit == 1 && numRedHit === 1)) {
        dyBall = dyBall * SPEED_MULTIPLIER;
    }
}

// Game logic
//


function handleBeatLevel() {
    clearInterval(interval);
    if (score/448 == 2) {
        canvasContext.fillStyle="White"
        canvasContext.font='30px "PressStart2P"';
        canvasContext.fillText("You Have Won!",85,200);
    } else {
        initializeBricks();
        startGame()
    }
}

function continueGame() {
    startGame()
}

function restartGame() {
    paddleWidth = INITIAL_PADDLE_WIDTH
    score = 0;
    dyBall = 4;
    document.getElementById("livesField").innerHTML = 3;
    initializeBricks();
    startGame()
}

function startGame() {
    ballX = (canvasWidth - BALLL_RADIUS)/ 2;
    ballY = BALLY_START;
    paddleX = (canvasWidth - paddleWidth)/ 2;
    // reset scoreboard 
    updateScore();

    // reset horizontal velocity to something random
    dxBall = (Math.random() -.5) * 4;

    // reset arrow keys to false
    leftKeyDown = false;
    rightKeyDown = false;
        
    clearInterval(interval);

    interval = setInterval(updateCanvas, 12);
};

function initializeCanvas() {
    // get a reference to the canvas
    canvasContext = document.getElementById("canvas").getContext("2d");
    
    // set height and width variables from DOM
    canvasWidth = document.getElementById("canvas").offsetWidth;
    canvasHeight = document.getElementById("canvas").offsetHeight;

    canvasContext.fillStyle="White"
    // Font not working here, but is everywehre else. Due to loading after initialize is called.
    canvasContext.font='30px "PressStart2P"';
    canvasContext.fillText("Click On The Game To Start",85,200);
    canvasContext.fillText("Or Restart At Any Time",120,250);

    // draw bricks
    brick_width = (canvasWidth / COLUMN_COUNT);
    paddleWidth = INITIAL_PADDLE_WIDTH;

    initializeBricks();
    drawBricks();

    // draw the ball and paddle
    ballX = (canvasWidth - BALLL_RADIUS)/ 2;;
    ballY = BALLY_START;
    paddleX = (canvasWidth - paddleWidth)/ 2;

    drawBall(ballX, ballY);
    drawPaddle(paddleX, canvasHeight - PADDLE_HEIGHT, paddleWidth, PADDLE_HEIGHT);

    // start the game when the player clicks the button
    document.getElementById("canvas").addEventListener("click" , restartGame);

}

// Event Handlers
//

function movePaddleWithKeyboard(e) {
    rightKeyDown = rightKeyDown || e.keyCode === 39;
    leftKeyDown = leftKeyDown || e.keyCode === 37;
}

function stopMovePaddleWithKeyboard(e) {
    if (rightKeyDown && e.keyCode === 39) {
        rightKeyDown = false
    }
    if (leftKeyDown && e.keyCode === 37) {
        leftKeyDown = false
    }
}

function movePaddleWithMouse(e) {
    paddleX = e.pageX - document.getElementById("canvas").offsetLeft - (paddleWidth / 2);
}

document.addEventListener("mousemove", movePaddleWithMouse);
document.addEventListener("keydown", movePaddleWithKeyboard);
document.addEventListener("keyup", stopMovePaddleWithKeyboard);

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
    switch (Math.floor((ballY - SPACE_ABOVE_BRICKS)/ BRICK_HEIGHT)) {
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

}());
