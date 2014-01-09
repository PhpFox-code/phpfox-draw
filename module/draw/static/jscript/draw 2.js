/*$(document).ready(function() {
    initialize();
});*/
$Behavior.manageDraw = function(){
    initialize();
};
// works out the X, Y position of the click inside the canvas from the X, Y position on the page
function getPosition(mouseEvent, sigCanvas) {
    var rect = sigCanvas.getBoundingClientRect();
    var x, y;
    x = mouseEvent.clientX - rect.left;
    y = mouseEvent.clientY - rect.top;
    return {X: x, Y: y};
}

function initialize() {
    // get references to the canvas element as well as the 2D drawing context
    var sigCanvas = document.getElementById("canvasSignature");
    var colorCanvas = document.getElementById("canvasColor");
   // var edit = document.getElementById("png2");
    var context = sigCanvas.getContext("2d");
    var color = colorCanvas.getContext("2d");
    //if (edit.value !== "") { 
    //    var img = new Image();
    //    img.src = edit.value;
    //    context.drawImage(img, 0, 0);
   // }
    $("#canvasColor").mousedown(function(mouseEvent) {
        var position = getPosition(mouseEvent, sigCanvas);
        if ((position.X) < 20)
            context.strokeStyle = "#FF0000";
        else if ((position.X < 40))
            context.strokeStyle = "#00FF00";
        else if ((position.X < 60))
            context.strokeStyle = "#0000FF";
        else if ((position.X < 80))
            context.strokeStyle = "#FFFFFF";
        else if ((position.X < 100))
            context.strokeStyle = "#000000";
        else if ((position.X < 120))
            context.strokeStyle = "#FFFF00";
        else if ((position.X < 140))
            context.strokeStyle = "#00FFFF";
        else if ((position.X < 160))
            context.strokeStyle = "#FF00FF";
        else if ((position.X < 180))
            context.strokeStyle = "#FF9900";
        else if ((position.X < 200))
            context.strokeStyle = "#005500";
        else if ((position.X < 220))
            context.strokeStyle = "#550000";
        else if ((position.X < 240))
            context.strokeStyle = "#000055";
        else if (position.X < 265)
            context.lineWidth = 1;
        else if (position.X < 290)
            context.lineWidth = 2;
        else if (position.X < 315)
            context.lineWidth = 3;
        else if (position.X < 340)
            context.lineWidth = 4;
        else if (position.X < 365)
            context.lineWidth = 5;
        else if (position.X < 390)
            context.lineWidth = 7;
        else if (position.X < 415)
            context.lineWidth = 10;
        else if (position.X < 450) {
            context.lineWidth = 20;
            context.strokeStyle = "#FFFFFF";
        }
    });

    // This will be defined on a TOUCH device such as iPad or Android, etc.
    var is_touch_device = 'ontouchstart' in document.documentElement;

    drawTool(color);
    if (is_touch_device) {
        // create a drawer which tracks touch movements
        var drawer = {
            isDrawing: false,
            touchstart: function(coors) {
                context.beginPath();
                context.moveTo(coors.x, coors.y);
                this.isDrawing = true;
            },
            touchmove: function(coors) {
                if (this.isDrawing) {
                    context.lineTo(coors.x, coors.y);
                    context.stroke();
                }
            },
            touchend: function(coors) {
                if (this.isDrawing) {
                    this.touchmove(coors);
                    this.isDrawing = false;
                }
            }
        };

        // create a function to pass touch events and coordinates to drawer
        function draw(event) {

            // get the touch coordinates.  Using the first touch in case of multi-touch
            var coors = {
                x: event.targetTouches[0].pageX,
                y: event.targetTouches[0].pageY
            };

            // Now we need to get the offset of the canvas location
            var obj = sigCanvas;

            if (obj.offsetParent) {
                // Every time we find a new object, we add its offsetLeft and offsetTop to curleft and curtop.
                do {
                    coors.x -= obj.offsetLeft;
                    coors.y -= obj.offsetTop;
                }
                // The while loop can be "while (obj = obj.offsetParent)" only, which does return null
                // when null is passed back, but that creates a warning in some editors (i.e. VS2010).
                while ((obj = obj.offsetParent) != null);
            }

            // pass the coordinates to the appropriate handler
            drawer[event.type](coors);
        }


        // attach the touchstart, touchmove, touchend event listeners.
        sigCanvas.addEventListener('touchstart', draw, false);
        sigCanvas.addEventListener('touchmove', draw, false);
        sigCanvas.addEventListener('touchend', draw, false);

        // prevent elastic scrolling
        sigCanvas.addEventListener('touchmove', function(event) {
            event.preventDefault();
        }, false);
    }
    else {

        // start drawing when the mousedown event fires, and attach handlers to
        // draw a line to wherever the mouse moves to
        $("#canvasSignature").mousedown(function(mouseEvent) {
            var position = getPosition(mouseEvent, sigCanvas);
            //context.moveTo(position.X, position.Y);
            context.beginPath();

            // attach event handlers
            $(this).mousemove(function(mouseEvent) {
                drawLine(mouseEvent, sigCanvas, context);
            }).mouseup(function(mouseEvent) {
                finishDrawing(mouseEvent, sigCanvas, context);
            }).mouseout(function(mouseEvent) {
                finishDrawing(mouseEvent, sigCanvas, context);
            });
        });

    }
}

// draws a line to the x and y coordinates of the mouse event inside
// the specified element using the specified context
function drawTool(color) {
    color.fillStyle = "#FF0000";
    color.fillRect(0, 0, 20, 15);

    color.fillStyle = "#00FF00";
    color.fillRect(20, 0, 20, 15);

    color.fillStyle = "#0000FF";
    color.fillRect(40, 0, 20, 15);

    color.fillStyle = "#FFFFFF";
    color.fillRect(60, 0, 20, 15);

    color.fillStyle = "#000000";
    color.fillRect(80, 0, 20, 15);

    color.fillStyle = "#FFFF00";
    color.fillRect(100, 0, 20, 15);

    color.fillStyle = "#00FFFF";
    color.fillRect(120, 0, 20, 15);

    color.fillStyle = "#FF00FF";
    color.fillRect(140, 0, 20, 15);

    color.fillStyle = "#FF9900";
    color.fillRect(160, 0, 20, 15);

    color.fillStyle = "#005500";
    color.fillRect(180, 0, 20, 15);

    color.fillStyle = "#550000";
    color.fillRect(200, 0, 20, 15);

    color.fillStyle = "#000055";
    color.fillRect(220, 0, 20, 15);

    color.strokeStyle = "#003300";
    color.font = "11px Arial";
    color.strokeRect(240, 0, 25, 15);
    color.fillText(" 1px", 240, 11);

    color.strokeRect(265, 0, 25, 15);
    color.fillText(" 2px", 265, 11);

    color.strokeRect(290, 0, 25, 15);
    color.fillText(" 3px", 290, 11);

    color.strokeRect(315, 0, 25, 15);
    color.fillText(" 4px", 315, 11);

    color.strokeRect(340, 0, 25, 15);
    color.fillText(" 5px", 340, 11);

    color.strokeRect(365, 0, 25, 15);
    color.fillText(" 7px", 365, 11);

    color.strokeRect(390, 0, 25, 15);
    color.fillText("10px", 390, 11);

    color.strokeRect(415, 0, 35, 15);
    color.fillText(" Eraser", 415, 11);
    //color.fillText("Eraser",450,11);

}
function drawLine(mouseEvent, sigCanvas, context) {
    var position = getPosition(mouseEvent, sigCanvas);
    context.lineTo(position.X, position.Y);
    context.stroke();
}

// draws a line from the last coordiantes in the path to the finishing
// coordinates and unbind any event handlers which need to be preceded
// by the mouse down event
function finishDrawing(mouseEvent, sigCanvas, context) {
    // draw the line to the finishing coordinates
    drawLine(mouseEvent, sigCanvas, context);

    context.closePath();

    // unbind any events which could draw
    $(sigCanvas).unbind("mousemove")
            .unbind("mouseup")
            .unbind("mouseout");
    document.getElementById('png').value = canvasSignature.toDataURL('image/png');
}
function checkForm() {
    var y = document.forms["form"]["png"].value;
    var x = document.forms["form"]["title"].value;
    if (x.length < 1)
    {
        alert("Title could not empty");
        return false;
    }
    if (y.length < 1)
    {
        alert("You must draw something");
        return false;
    }
}