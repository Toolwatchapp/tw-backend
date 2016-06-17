$( document ).ready(function() {
    var canvas = document.getElementById("canvas_animated_watch");
    var ctx = canvas.getContext("2d");

    var dimensions = {
        max_height : 250,
        max_width  : 250,
        width  : 250, // this will change
        height : 250, // this will change
        largest_property : function () {
            return this.height > this.width ? "height" : "width";
        },
        read_dimensions : function (img) {
            this.width = img.width;
            this.height = img.height;
            return this;
        },
        scaling_factor : function (original, computed) {
            return computed / original;
        },
        scale_to_fit : function () {
            var x_factor = this.scaling_factor(this.width,  this.max_width),
                y_factor = this.scaling_factor(this.height, this.max_height),

                largest_factor = Math.min(x_factor, y_factor);

            this.width  *= largest_factor;
            this.height *= largest_factor;
        }
    };

    setInterval(drawClock, 50);

    function drawClock() {
            drawFace(ctx);

            var radius = canvas.height / 2;
            ctx.translate(radius, radius);
            radius = radius * 0.90;

            ctx.beginPath();
            ctx.arc(0, 0, radius*0.03, 0, 2*Math.PI);
            ctx.fillStyle = '#4777a7';
            ctx.fill();

            drawTime(ctx, radius);
    }

    function drawFace(ctx) {

        var img=document.getElementById("watch");
        dimensions.read_dimensions(img).scale_to_fit();

        canvas.width  = dimensions.width;
        canvas.height = dimensions.height;
        ctx.drawImage(img, 4, 7, dimensions.width, dimensions.height);



    }

    function drawTime(ctx, radius){
        var now = (window.syncedDate === null) ? new Date() : getAccurateTime();
        var hour = now.getHours();
        var minute = now.getMinutes();
        var second = now.getSeconds()-1;
        var ms = now.getMilliseconds();

        //hour
        hour=hour%12;
        hour=(hour*Math.PI/6)+(minute*Math.PI/(6*60))+(second*Math.PI/(360*60));
        drawHand(ctx, hour, radius*0.2, radius*0.02);
        //minute
        minute=(minute*Math.PI/30)+(second*Math.PI/(30*60));
        drawHand(ctx, minute, radius*0.4, radius*0.02);

        // second
        second=((second + ms /1000)*Math.PI/30);
        drawHand(ctx, second, radius*0.5, radius*0.01, true);

    }

    function drawHand(ctx, pos, length, width, isSecond) {
        ctx.beginPath();
        if(isSecond === true){
            ctx.strokeStyle='#4777a7';
        }else{
           ctx.strokeStyle='#2d639a';
        }

        ctx.lineWidth = width;
        ctx.lineCap = "square";
        ctx.moveTo(0,0);
        ctx.rotate(pos);
        ctx.lineTo(0, -length);
        ctx.stroke();
        ctx.rotate(-pos);
    }
});
