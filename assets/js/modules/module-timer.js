/**
 * MODULE TIMER
 * ========================================================================== */
(function($) {
    
    // Kiem tra bien module
    if( !window.rt01MODULE ) window.rt01MODULE = {};

    // Bien toan cuc
    var that, o, cs, va, is, ti, M,

        /**
         * CAP NHAP BIEN TOAN CUC
         */
        varibleModule = function(self) {
            that  = self;
            o     = self.o;
            cs    = self.cs;
            va    = self.va;
            is    = self.is;
            ti    = self.ti;
            M     = self.M;
        };


    /**
     * MODULE TIMER FOR SLIDESHOW
     */
    rt01MODULE.TIMER = {

        /**
         * RENDER MARKUP CUA TIMER
         */
        render : function() {
            varibleModule(this);

            // Timer: remove last timer
            !!va.$timer && va.$timer.remove();
            if( !is.timer ) return;


            /**
             * TIM KIEM DOM VA` THEM TIMER VAO CODE
             */
            // Timer: search DOM
            var className  = va.ns + o.nameTimer,                   // Class name
                classType  = className +'-'+ va.timer,              // Class type
                divdiv     = '<div/>',
                $timerHTML = that.RENDER.searchDOM('.'+ className);


            // Timer: them vao markup
            if( $timerHTML.length ) va.$timer = $timerHTML.addClass(classType);
            else {
                va.$timer = $(divdiv, {'class' : className +' '+ classType});

                // Add timer vao markup
                that.RENDER.into(o.markup.timerInto, va.$timer);
            }



            /**
             * SETUP MARKUP VA` THUOC TINH CUA TIMER ITEM
             */
            // TIMER LINE
            if( va.timer == 'line' ) {
                va.$timerItem = $(divdiv, {'class' : className +'item'});
                va.$timer.append(va.$timerItem);

                // Properties init
                that.lineSetupInit();
            }

            // TIMER ARC
            else if( va.timer == 'arc' ) {
                va.$timerItem = $('<canvas></canvas>');
                va.$timer.append(va.$timerItem);

                // Setup init
                that.arcSetupInit();
            }
        },




        /**
         * SETUP TIMER LINE LUC BAT DAU KHI RENDER
         */
        lineSetupInit : function() {
            var that = this;
            varibleModule(that);

            // Setup gia tri transform tren Timer
            var tf = {};
            tf[va.cssTf] = M.tlx(-100, '%');
            setTimeout(function() { that.va.$timerItem.css(tf) }, 1);
        },

        /**
         * SETUP TIMER ARC LUC BAT DAU RENDER
         */
        arcSetupInit : function() {
            varibleModule(this);
            var timerArc = o.timerArc;

            // Arc setup properties
            var propSetup = {
                    angCur : (!!va.arc && !!va.arc.angCur) ? va.arc.angCur : 0,     // Angle Current, get angle last if update by api
                    pi     : Math.PI / 180,
                    width  : (timerArc.width === null)  ? va.$timer.width()  : timerArc.width,
                    height : (timerArc.height === null) ? va.$timer.height() : timerArc.height,
                    speed  : ~~(1000 / timerArc.fps)
                };

            // API update: all properties extend to va.arc
            va.arc = $.extend(o.timerArc, propSetup);

            // Arc size
            va.$timerItem.attr({'width' : va.arc.width, 'height' : va.arc.height});
            

            // Arc: style draw
            va.tContext = va.$timerItem[0].getContext('2d');
            var arcSet = function() {
                var c = va.tContext;
                c.setTransform(1,0,0,1,0,0);
                c.translate(va.arc.width/2, va.arc.height/2);
                c.rotate(-va.arc.pi*(90-va.arc.rotate));

                c.strokeStyle = va.arc.stroke;
                c.fillStyle   = va.arc.fill;
                c.lineWidth   = va.arc.weight;
            };
            arcSet();
        },




        /**
         * SETUP ANIMATION TREN TIMER LINE
         */
        lineAnimation : function() {
            var that = this,
                cs   = that.cs,
                va   = that.va,
                is   = that.is,
                ti   = that.ti,
                M    = that.M;


            /**
             * SETUP ANIMATION KHI BAT DAU
             */
            var
            fnAnimationBegin = function() {
                var tf = {}; tf[va.cssTf] = M.tlx(0);

                // Loai bo thuoc css truoc tien
                va.$timerItem.css({ 'opacity': '', 'visibility': '' });
                
                // Phan biet ho tro Css Transition
                if( is.ts ) {
                    // var ts = {}; ts[va.cssD] = va.tDelay +'ms';
                    var ts = {}; ts = M.ts(va.cssTf, va.tDelay, 'linear');

                    /**
                     * THEM TIMER DE FIXED IE
                     *  + Lam moi lai timer
                     *  + Cai dat timer de fixed trong IE 10+ khong chiu di chuyen luc ban dau
                     */
                    // va.$timerItem.hide().show();
                    va.$timerItem.css(ts);
                    setTimeout(function() { va.$timerItem.css(tf) }, 1);
                }
                else va.$timerItem.animate(tf, { duration: va.tDelay, easing: 'linear' });
            };


            // Truoc tien Loai bo Transition va reset lai vi tri cho Timer
            that.lineAnimationReset(va.xTimer);
            // Bau dau Reset transform cho Timer
            setTimeout(fnAnimationBegin, 1);
        },

        /**
         * LOAI BO TRANSTION VA RESET LAI VI TRI CHO TIMER LINE
         */
        lineAnimationReset : function(xReset) {
            var that = this,
                va   = that.va,
                is   = that.is,
                M    = that.M;

            // Tinh toan gia tri bien Transform
            var tf = {}; tf[va.cssTf] = M.tlx(-xReset.toFixed(2), '%');
            
            // Loai bo transition cua Timer
            va.$timerItem.stop(true);
            if( is.ts ) M.tsRemove(va.$timerItem);
            
            // Setup Transform cho Timer sau khi loai bo transition
            setTimeout(function() { va.$timerItem.css(tf) }, 1);
        },

        /**
         * SETUP ANIMATION KHI KET THUC TIMER
         */
        lineAnimationEnd : function() {
            var that = this,
                cs   = that.cs,
                va   = that.va;


            // Setup ID Next slide -> De lay speed cua Slide next
            var idNext = cs.idCur + 1;
            if( idNext > cs.num - 1 ) idNext = 0;

            // Setup animate fade
            va.$timerItem
                .stop(true)
                .animate({ 'opacity': 0 }, {

                    duration : va.speed[idNext] - 100,
                    complete : function() {

                        // Them css 'visibility' -> fixed sau khi 'fading' thi tranform vi tri 0
                        va.$timerItem.css({ 'opacity': '', 'visibility': 'hidden' });

                        // Loai bo Transition va reset lai vi tri cho Timer
                        that.lineAnimationReset(100);
                    }
                });
        },




        /**
         * SETUP ANIMATION TREN TIMER ARC
         */
        arcAnimation : function(isRunOne) {
            var that = this,
                va   = that.va,
                ti   = that.ti,
                cs   = that.cs,

                // Setup goc' cong them trong vong lap
                angPlus   = va.arc.speed * 360 / va.delay[cs.idCur],

                // Fn ve cac duong arc trong Timer
                ctx       = va.tContext,
                ARC       = va.arc,
                inFill    = Math.ceil((ARC.radius - ARC.weight) / 2),
                fnArcDraw = function() {

                    // Xoa vu`ng ve~ Canvas truoc tien
                    ctx.clearRect(-ARC.width/2, -ARC.height/2, ARC.width, ARC.height);
                    ctx.globalAlpha = 1;
                    
                    // Ve~ duong arc o ngoai`
                    ctx.beginPath();
                    ctx.lineCap = 'round';
                    ctx.arc(0, 0, ARC.radiusOuter, 0, ARC.pi*360, false);
                    ctx.lineWidth   = ARC.weightOuter;
                    ctx.strokeStyle = ARC.strokeOuter;
                    ctx.fillStyle   = ARC.fillOuter;
                    ctx.stroke();
                    ctx.fill();

                    // Ve~ duong Fill arc o trong
                    ctx.beginPath();
                    ctx.arc(0, 0, inFill + 1, 0, ARC.pi * Math.ceil(ARC.angCur*10)/10, false);
                    ctx.lineWidth   = inFill * 2 + 2;
                    ctx.strokeStyle = ARC.fill;
                    ctx.stroke();

                    // Ve~ duong Stroke arc o trong
                    ctx.beginPath();
                    ctx.arc(0, 0, ARC.radius, 0, ARC.pi * ARC.angCur, false);
                    ctx.lineWidth   = ARC.weight;
                    ctx.strokeStyle = ARC.stroke;
                    ctx.stroke();

                    // Setup goc cua arc hien tai
                    va.arc.angCur += angPlus;
                    if( va.arc.angCur > 370 ) {
                        clearInterval(ti.timer);
                    }
                };


            /**
             * SETUP VONG LAP DE VE~ TIMER ARC
             *  + Truoc tien loai bo timer ve~ duong arc fading
             */
            clearInterval(ti.timer);
            is.enableTimerAnimEnd = true;

            if( !!isRunOne ) fnArcDraw();
            else             ti.timer = setInterval(fnArcDraw, va.arc.speed);
        },

        /**
         * SETUP ANIMATION TIMER ARC SAU KHI KET THUC
         */
        arcAnimationEnd : function() {
            var that = this,
                va   = that.va,
                ti   = that.ti,
                cs   = that.cs,

                // Setup chi? so' alpha can` phai tru trong vong lap
                fps        = 30,
                delay      = 1000 / fps,
                speedMinus = va.speed[cs.idCur] >= 600 ? 400 : 100,     // Thoi gian gia?m them
                speedCur   = va.speed[cs.idCur] - speedMinus,
                nStep      = speedCur / delay - 1,
                alphaCur   = 1,
                delayCur   = 0,
                alphaMinus,

                // Fn ve cac duong arc trong Timer
                ctx       = va.tContext,
                ARC       = va.arc,
                inFill    = Math.ceil((ARC.radius - ARC.weight) / 2),
                fnArcDraw = function() {

                    // Xoa vu`ng ve~ Canvas truoc tien
                    ctx.clearRect(-ARC.width/2, -ARC.height/2, ARC.width, ARC.height);
                    
                    // Ve~ duong arc o ngoai`
                    ctx.globalAlpha = 1;
                    ctx.beginPath();
                    ctx.arc(0, 0, ARC.radiusOuter, 0, ARC.pi*360, false);
                    ctx.lineWidth   = ARC.weightOuter;
                    ctx.strokeStyle = ARC.strokeOuter;
                    ctx.fillStyle   = ARC.fillOuter;
                    ctx.stroke();
                    ctx.fill();

                    // Ve~ duong Fill arc o trong
                    ctx.globalAlpha = parseFloat(alphaCur.toFixed(3));
                    ctx.beginPath();
                    ctx.arc(0, 0, inFill + 1, 0, ARC.pi * Math.ceil(ARC.angCur*10)/10, false);
                    ctx.lineWidth   = inFill * 2 + 2;
                    ctx.strokeStyle = ARC.fill;
                    ctx.stroke();

                    // Ve~ duong Stroke arc o trong
                    ctx.beginPath();
                    ctx.arc(0, 0, ARC.radius, 0, ARC.pi * ARC.angCur, false);
                    ctx.lineWidth   = ARC.weight;
                    ctx.strokeStyle = ARC.stroke;
                    ctx.stroke();

                    // Setup alpha can phai tru`
                    delayCur  += delay;
                    alphaMinus = $.easing.easeOutQuad(null, delayCur, 0, 1, speedCur);
                    alphaCur   = 1 - alphaMinus;
                    nStep--;

                    // Setup alpha cua duong arc
                    if( alphaCur <= 0.01 || nStep < 0  ) {
                        clearInterval(ti.timer);
                        is.enableTimerAnimEnd = true;
                        va.arc.angCur = 0;
                    }
                };


            /**
             * VONG LAP DE ANIMATION
             *  + Bien 'enableTimerAnimEnd' -> fixed khi swap slide lien tuc nhung Animation End cua timer van lap lai.
             */
            if( is.enableTimerAnimEnd ) {
                is.enableTimerAnimEnd = false;

                clearInterval(ti.timer);
                ti.timer = setInterval(fnArcDraw, delay);
            }
        },




        /**
         * SETUP TIMER WHEN STOP
         */
        stop : function() {
            var that = this, tf = {};
            varibleModule(that);

            /**
             * SETUP STOP SLIDESHOW TREN CAC LOAI TIMER
             */
            switch(va.timer) {
                case 'line' :

                    // Loai bo chuyen dong Animation
                    if( is.ts ) va.$timerItem.css(va.cssDEmpty);
                    else        va.$timerItem.stop(true);
                    
                    // Setup gia tri Transform
                    va.xTimer = va.tDelay / va.delay[cs.idCur] * 100;
                    tf[va.cssTf] = M.tlx(-va.xTimer.toFixed(2), '%');
                    
                    // Setup transform len Timer
                    setTimeout(function() { that.va.$timerItem.css(tf) }, 1);
                    break;


                case 'arc' :
                    // Setup ve goc hien tai cua Timer
                    va.arc.angCur = 360 - (va.tDelay / va.delay[cs.idCur] * 360);
                    // Ve lai Timer
                    that.arcAnimation(true);
                    // Xoa bo vong lap de ve duong arc trong Timer
                    clearInterval(ti.timer);
                    break;
            };
        }
    };
})(jQuery);