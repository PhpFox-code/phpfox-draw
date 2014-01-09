var initManageDrawPaint = false; 
var contextDrawing = false; 
function initializeDraw(option) {
    if(typeof(option) == "undefined")
    {
        return false;
    }
    initManageDrawPaint = true;
    var sigCanvas = document.getElementById("canvasDraw");
    if(!contextDrawing)
    {
        console.log(option);
        contextDrawing = sigCanvas.getContext("2d");    
    }else{
        console.log(option);
        contextDrawing.closePath();
        contextDrawing.lineWidth = option.line;
        if (option.erase) {
            contextDrawing.strokeStyle = "#FFFFFF";
        } else {
            var color = document.getElementById("color").value;
            contextDrawing.strokeStyle = color;
        }
    }
    if(option.clear == true)
    {
        if(confirm(oTranslations['core.are_you_sure']))
        {
            contextDrawing.clearRect(0, 0, sigCanvas.width, sigCanvas.height);
            return;
        }    
    }
     $(sigCanvas).unbind("mousemove")
            .unbind("mouseup")
            .unbind("mouseout")
            .unbind("mousedown");
    contextDrawing.strokeStyle = "#000000";
    var is_touch_device = 'ontouchstart' in document.documentElement;
    if (is_touch_device) {
        // create a drawer which tracks touch movements
        var drawer = {
            isDrawing: false,
            touchstart: function(coors) {
                contextDrawing.beginPath();
                contextDrawing.moveTo(coors.x, coors.y);
                this.isDrawing = true;
            },
            touchmove: function(coors) {
                if (this.isDrawing) {
                    contextDrawing.lineTo(coors.x, coors.y);
                    contextDrawing.stroke();
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
        $("#canvasDraw").mousedown(function(mouseEvent) {
            var position = getPosition(mouseEvent, sigCanvas);
            if (option.erase) {
                contextDrawing.lineWidth = 20;
                contextDrawing.strokeStyle = "#FFFFFF";
            } else {
                contextDrawing.lineWidth = option.line;
                var color = document.getElementById("color").value;
                contextDrawing.strokeStyle = color;
            }
            contextDrawing.beginPath();
            $(this).mousemove(function(mouseEvent) {
                drawLine(mouseEvent, sigCanvas, contextDrawing);
            }).mouseup(function(mouseEvent) {
                finishDrawing(mouseEvent, sigCanvas, contextDrawing);
            }).mouseout(function(mouseEvent) {
                finishDrawing(mouseEvent, sigCanvas, contextDrawing);
            });
        });
    }
}
function drawLine(mouseEvent, sigCanvas, context) {
    var position = getPosition(mouseEvent, sigCanvas);
    context.lineTo(position.X, position.Y);
    context.stroke();
}
function finishDrawing(mouseEvent, sigCanvas, context) {
    drawLine(mouseEvent, sigCanvas, context);
    context.closePath();
    $(sigCanvas).unbind("mousemove")
            .unbind("mouseup")
            .unbind("mouseout");
    document.getElementById('png').value = canvasDraw.toDataURL('image/png');
}
function getPosition(mouseEvent, sigCanvas) {
    var rect = sigCanvas.getBoundingClientRect();
    var x, y;
    x = mouseEvent.clientX - rect.left;
    y = mouseEvent.clientY - rect.top;
    return {X: x, Y: y};
}
var bFirstLoadding = false;
Drawing = {
    initPanel:function(){ 
        loadInitDrawColorpicker();
        var h = $(window).height();
        if($('#canvasDraw').length <=0)
        {
            return false;
        }
        var line,size,erase,clear;
        var option = {
            'line':1,
            'size':1,
            'erase':false,
            'clear':false,
        };
        if(bFirstLoadding == false){
            $('#draw_slider_holder').css('height',h);
            $('#canvasDraw').attr('height',h-13);     
        }
        $('#drawpop').click(function() {
            $('#draw_slider_holder').show();
            $('#draw_slider_holder').animate({
            right: "0px",
            }, 500,function(){
               
            });
        });
        $('#draw_slider_holder .color_picker').ColorPicker({
            onSubmit: function(hsb, hex, rgb) {
                option.erase = false;
                option.clear = false;
                $('.getColor').val("#" + hex);
                $('#canvasDraw').removeClass('erase');
                initializeDraw(option); 
            },
            onChange: function(hsb, hex, rgb) {
                $('.getColor').val("#" + hex);
            },
            onHide:function(e,b){
                option.erase = false;
                option.clear = false;
                var hex = $(e).find('.colorpicker_hex input[type="text"]').val();
                $('.getColor').val("#" + hex);
                $('#canvasDraw').removeClass('erase');
                initializeDraw(option); 
            }
        });
        $('.tool_close').click(function() {
             $('#draw_slider_holder').animate({
                 right:"-1000px",
             },function(){
                 $('#draw_slider_holder').hide();
             });
        });
        $('.tool_save').click(function(){
             $('.holder_content_description').toggle("fast");
         });
        $('#hide_drawing_input').click(function(){
            Drawing.hideSave();
            return false;
        });
        $('#submit_drawing_input').click(function(){
            $('#drawing_submit_form').ajaxCall('draw.postDrawing');
            Drawing.showLoading();
            return false;
        });
        $('#canvasDraw').removeClass('erase');
        $('#draw_size').change(function() {
            size = $("#draw_size").val();
            console.log(size);
            option.size = size;
            option.line = size;
            initializeDraw(option);
        });
        $('#draw_slider_holder .tool').click(function() {//erase
            option.erase = true;
            option.clear = false;
            $('#canvasDraw').addClass('erase');
            initializeDraw(option);
        });
        $('#draw_slider_holder .color').click(function() {//erase
            option.erase = false;
            option.clear = false;
            initializeDraw(option);
        });
        $('.clear_paint').click(function(){
            option.clear = true;
            initializeDraw(option);
        });
        initializeDraw(option);    
        bFirstLoadding = true;
    },
    submitDrawing:function(){
        
    },
    showLoading:function(){
        $('#submit_drawing_submit').hide(); 
        $('#submit_drawing_loading').show(); 
        $('#submit_drawing_message').hide();
    },
    hideLoading:function(){
        $('#submit_drawing_submit').show(); 
        $('#submit_drawing_loading').hide(); 
    },
    hideContent:function(){
        setTimeout('Drawing.hideSave();',300);  
    },
    showMessage:function(msg,c){
        $('#submit_drawing_message').html(msg).show().addClass(c);
    },
    hideSave:function(){
        $('.holder_content_description').toggle("fast");
    },
    reset:function(){
        $('#title').val("");
        $('#description').val("");
        
    }
}
$Behavior.initDraw = function() {
     Drawing.initPanel();
}
