var angle = 1; //angle of rotation
var height = 50; //shape height
var width = 250; //shape width
var canvasWidth; //get from html
var canvasHeight; //get from html
var ctx;
var interval;
var offset = 0;
var img;


init = function () {
    //[0] is self
    img = document.getElementById("logo");
    ctx = document.getElementById("canvas").getContext("2d");
    canvasWidth = document.getElementById("canvas").width;
    canvasHeight = document.getElementById("canvas").height;
    document.getElementById("startRotate").onclick = startRotate;
    document.getElementById("stopRotate").onclick = stopRotate;
    vmark(50, 50);
    interval = setInterval(vmark, 10);
}

function vmark() {
    if (offset <= 0) {
        offset = img.height;
    }
    // context.drawImage(img,sx,sy,swidth,sheight,x,y,width,height);

    ctx.clearRect(0, 0, canvasWidth, canvasHeight);
    ctx.save();

    //ctx.scale(0.6, 0.6);
    ctx.translate(350 + img.height, 50);
    ctx.rotate(Math.PI / 2);
    drawLogo(0, 0);
    ctx.restore();
    ctx.save();
    ctx.translate(50, 50);
    ctx.rotate(Math.PI/4);
    ctx.scale(.6,.6);
    drawLogo(50, 0);

    ctx.restore();
    offset -= 1;

}

function drawLogo(x, y) {
    ctx.drawImage(img, 0, img.height - offset, img.width, offset, x, y, img.width, offset);
    ctx.drawImage(img, 0, 0, img.width, img.height - offset, x, y + offset, img.width, img.height - offset);
}

function startRotate(event) {
    if (!interval) {
        //1000 = 1 second
        interval = setInterval(vmark, 10);
    }
}

function stopRotate(event) {
    if (interval) {
        clearInterval(interval);
    }
    interval = null;
}

window.onload = init;
