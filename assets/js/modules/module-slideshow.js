/**
 * MODULE SLIDESHOW
 * ========================================================================== */
(function($) {
    
    // Kiem tra bien module
    if( !window.rt01MODULE ) window.rt01MODULE = {};

    // Bien toan cuc
    var that, o, oo, cs, va, is, ti, M, TIMER,

        /**
         * CAP NHAP BIEN TOAN CUC
         */
        varibleModule = function(self) {
            that  = self;
            o     = self.o;
            oo    = self.oo;
            cs    = self.cs;
            va    = self.va;
            is    = self.is;
            ti    = self.ti;
            M     = self.M;
            TIMER = $.extend({}, rt01MODULE.TIMER, self);
        };


    /**
     * MODULE SLIDESHOW
     * @param int va.tDelay
     */
    rt01MODULE.SLIDESHOW = {

        /**
         * RENDER BUTTON PLAY PAUSE
         */
        renderPlayPause : function() {
            varibleModule(this);

            // Navigation: search DOM
            var classes   = '.'+ va.ns + o.namePlay,
                $playHTML = that.RENDER.searchDOM(classes);

            if( $playHTML.length ) va.$playpause = $playHTML;
            else {

                va.$playpause = $('<div/>', {'class' : va.ns + o.namePlay, 'text' : 'play-pause'});

                // Add playpause vao markup
                that.RENDER.into(o.markup.playInto, va.$playpause);
            }

            // Add class actived vao playpause neu isAutoRun false
            if( !is.autoRun ) {
                is.ssPauseAbsolute = true;
                va.$playpause.addClass(va.actived);
            }
        },


        /**
         * KHOI TAO SLIDESHOW
         */
        init : function() {
            varibleModule(this);

            // Dieu thuc hien function
            // So luong cua Slide phai lo'n hon 2 slides
            if( cs.num < 2 ) return;

            // Tiep tuc setup function
            is.hoverAction = false;

            M.scroll.setup();
            that.eventHover();
            o.slideshow.isPlayPause && that.tapOnPlayPause();

            // Thuoc tinh danh cho button stop
            is.stop = false;

            // Play Slideshow luc dau tien
            that.go('init');
        },


        /**
         * DIE`U HUO'NG SLIDESHOW
         */
        go : function(status) {
            varibleModule(this);
            var actionCur = null,
                PLAY      = 'play',
                PAUSE     = 'pause';

            /**
             * KHONG THUC HIEN LENH NAO KHI STOP ACTIVED
             */
            if( is.stop ) return;
            // console.log('SLIDESHOW.GO', status);


            /**
             * DUNG SLIDESHOW KHI CO LENH DUNG
             */
            if( is.ssPauseAbsolute ) {

                // Chi du`ng khi slideshow dang playing
                if( is.ssPlaying ) {
                    that.pause();
                    actionCur = PAUSE;
                }
            }
            else {

                /**
                 * DUNG SLIDESHOW KHI CO :
                 *  + Code nam ngoai vung Window Browser
                 *  + Dang MouseHover tren Code
                 *  + Slide hien tai co' Video hoac Map dang play
                 *  + Khi hieu u'ng dang chay co' lenh 'go' goi toi
                 */
                if( (is.ssRunInto && !is.into) || is.mouseHover || va.nVideoOpen || va.nMapOpen || is.fxRun ) {

                    // is.ssPlaying --> the hien timer co chay hay khong
                    if( is.ssPlaying ) {
                        that.pause();
                        actionCur = PAUSE;
                    }
                }

                /**
                 * TRUONG HOP CO LENH 'GO' MA KHONG CO SWAP SLIDE
                 */
                else if( !is.fxRun ) {

                    // Danh cho truong hop luc dau khong 'autoRun' slideshow
                    if( !$.isNumeric(va.tDelay)) {
                        that.resetPropThenPlay();
                        actionCur = PLAY;
                    }
                    else if( is.hoverAction ) {
                        if( !is.ssPlaying ) {
                            that.resetPropThenPlay();
                            actionCur = PLAY;
                        }
                    }
                    else {
                        that.play();
                        actionCur = PLAY;
                    }
                }
            }



            /**
             * SETUP BANG STATUS
             */
            if( status == 'slideToBegin' ) {

                // Ket thuc timer slideshow bang hieu u'ng 'fade' 
                is.TIMER && TIMER[va.timer +'AnimationEnd']();
            }
        },



        /**
         * CAP NHAT LAI TOAN BO SLIDESHOW & TIMER
         */
        updateAll : function() {
            varibleModule(this);

            // Timer toggle markup
            var auto0 = oo.slideshow,
                auto1 = o.slideshow;

            // Kiem tra co Option slideshow luu tru hay khong
            if( auto0 === undefined ) return;


            if( auto0.timer != auto1.timer ) {
                clearInterval(ti.timer);
                that.RENDER.timer();
                !!va.tTimer0 && that.pause();      // no check if first auto SLIDESHOW
                that.play();
            }


            // Timer arc update properties
            is.timer && (va.timer == 'arc') && TIMER.arcSetupInit();


            // Slideshow toggle --> after timer update
            if( oo.isSlideshow != o.isSlideshow ) {

                // Khoi tao Slideshow
                if( o.isSlideshow ) that.init();

                // Dung Slideshow
                else {
                    that.pause(true);

                    $(window).off(va.ev.scroll);
                    va.$self.off('mouseenter.code mouseleave.code');
                }
            }

            // Hoverstop toggle
            (auto0.isHoverPause != auto1.isHoverPause) && that.eventHover();
        },



        /**
         * TI'NH TOAN LAI THUOC TINH CUA TIMER -> TIEP TUC PLAYING SLIDESHOW
         */
        resetPropThenPlay : function() {
            varibleModule(this);

            // Reset lai gia tri cac bien
            if( va.tDelay != va.delay[cs.idCur] ) va.tDelay = va.delay[cs.idCur];

            if( is.TIMER ) {
                if     ( va.timer == 'line' && va.xTimer != 100 )  va.xTimer     = 100;
                else if( va.timer == 'arc' )                       va.arc.angCur = 0;
            }
            
            // Tiep tuc tinh toan trong fn Play
            that.play();
        },


        /**
         * PLAY NEXT SLIDE IN SLIDESHOW
         * @param int va.tDelay Phan quan trong
         */
        play : function() {
            varibleModule(this);
            var that = this,
                cs   = that.cs,
                va   = that.va,
                is   = that.is,
                ti   = that.ti;

            // console.log('SLIDESHOW.PLAY');
            va.tTimer0 = +new Date();
            is.ssPlaying = true;
            is.timer && TIMER[va.timer +'Animation']();


            // Setup di chuyen toi slide ke tiep
            clearTimeout(ti.play);
            ti.play = setTimeout(function() {
                varibleModule(that);

                var num      = cs.num,
                    idCur    = cs.idCur,
                    isRandom = o.slideshow.isRandom && num >= 1,
                    idNext   = isRandom ? M.randomInArray2(va.idMap, va.ssIDRandom, idCur)
                                        : (idCur >= num-1 ? 0 : idCur + 1),

                    $slNext  = va.$s.eq(idNext);


                // SLIDE da load xong --> di chuyen toi slide
                // if( $slNext.data('isLoaded') ) {
                //     if     ( isRandom )                   that.TOSLIDE.run(idNext, true);
                //     else if( !is.loop && idCur == num-1 ) that.TOSLIDE.run(0, true);
                //     else                                  that.EVENTS.next(1);
                // }

                // // SLIDE chua load xong --> cho` load xong
                // else {
                //     $slNext.data({ 'isPlayNext' : true });
                //     cs.stop();
                // }

                if     ( isRandom )                   that.TOSLIDE.run(idNext, true);
                else if( !is.loop && idCur == num-1 ) that.TOSLIDE.run(0, true);
                else                                  that.EVENTS.next(1);

            }, va.tDelay);
        },


        /**
         * DUNG HOAC NGUNG HANG SLIDESHOW
         */
        pause : function(isStop) {
            varibleModule(this);
            var idCur = cs.idCur;
            // console.log('SLIDESHOW.PAUSE');

            // Chuyen doi thuoc tinh cua cac bien ngay luc dau
            is.ssPlaying = is.hoverAction = false;


            /**
             * SETUP KHI TIMER DU`NG HA?NG
             */
            if( !!isStop ) {
                va.tDelay = va.delay[idCur];
            }

            /**
             * SETUP KHI TIMER TA.M D`UNG
             */
            else {

                var t0 = va.tDelay;
                va.tTimer1 = +new Date();
                va.tDelay  = va.delay[idCur] - (va.tTimer1 - va.tTimer0);

                if( va.delay[idCur] != t0 ) va.tDelay -= va.delay[idCur] - t0;
                if( va.tDelay < 0 ) {
                    va.tDelay = 0;

                    // !important to solve hover slideshow when fxRunning
                    is.hoverAction = true;
                }
            }

            
            /**
             * SETUP OTHERS
             * Dung hoan toan va loai bo timer playing
             */
            is.timer && TIMER.stop();
            clearTimeout(ti.play);
            clearTimeout(ti.timerLineAnimEnd);
        },



        eventHover : function() {
            var that = this;
            varibleModule(that);

            if( o.slideshow.isHoverPause ) {
                is.mouseHover = false;

                va.$self
                    .off('mouseenter.code mouseleave.code')
                    .on('mouseenter.code', function() {
                        that.is.mouseHover = true;
                        that.go('mouseenter');
                    })
                    .on('mouseleave.code', function() {
                        that.is.mouseHover = false;
                        that.go('mouseleave');
                    });
            }

            else va.$self.off('mouseenter.code mouseleave.code');
        },


        tapOnPlayPause : function() {
            var that = this,
                va   = that.va,
                evName = va.ev.click;       // Khong co event TOUCH --> xung dot voi event CLICK trong IE10+


            // Events
            va.$playpause.off(evName);
            va.$playpause.on(evName, function(e) {
                varibleModule(that);
                
                // Tinh toan vi tri cua Code
                M.scroll.check(true);

                // Thuc hien lenh Play - Pause dua. tren class 'actived'
                if( va.$playpause.hasClass(va.actived) ) that.api('play');
                else that.api('pause');

                return false;
            });
        },


        /**
         * LENH API PLAY - PAUSE - STOP
         */
        api : function(action) {
            var that = this;
            varibleModule(that);

            /**
             * LENH API PLAY
             *  + Chi hoat do.ng khi hieu u'ng khong chay
             */
            if( action == 'play' ) {

                // Khoi tao slideshow neu option khong co 
                if( !o.isSlideshow ) {
                    o.isSlideshow = true;
                    that.init();
                }

                // Them dieu kien 'is.ssPlaying' -> tranh khi tam. du`ng va~n co lenh Play
                if( is.ssPauseAbsolute ) {

                   // Loai bo class 'actived' tren button PlayPause
                    !!va.$playpause && va.$playpause.removeClass(va.actived);
                    // Loai bo bie'n ngan ca?n play slideshow
                    is.stop = is.ssPauseAbsolute = false;
                    // Thuc hien lenh go chi khi hieu u'ng khong chay. 
                    !is.fxRun && that.go('apiPlay');
                    // Tra ve event 'slideshowPlay'
                    cs.ev.trigger('slideshowPlay');
                }
            }


            /**
             * LENH API PAUSE
             */
            else if( action == 'pause' ) {
                if( !is.ssPauseAbsolute ) {

                    // Them class 'actived' tren button PlayPause
                    !!va.$playpause && va.$playpause.addClass(va.actived);
                    // Setup phan khac
                    is.ssPauseAbsolute = true;
                    that.go('apiPause');
                    // Tra ve event 'slideshowPause'
                    cs.ev.trigger('slideshowPause');
                }
            }


            /**
             * LENH API STOP
             */
            else if( action == 'stop' ) {
                if( !is.stop ) {
                    
                    // Them class 'actived' tren button PlayPause
                    !!va.$playpause && va.$playpause.addClass(va.actived);

                    // Bien thong bao dung ha?ng
                    is.stop = is.ssPauseAbsolute = true;
                    that.pause(true);
                    // Tra ve event 'slideshowStop'
                    cs.ev.trigger('slideshowStop');
                }
            }
        }
    };
})(jQuery);