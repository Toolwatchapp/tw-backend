/**
 * MODULE SWIPE
 * ========================================================================== */
(function($) {

    // Kiem tra bien module
    if( !window.rt01MODULE ) window.rt01MODULE = {};

    // Bien toan cuc
    var that, o, cs, va, is, ti, M, VIEW, POSITION, PAG,
        varibleModule = function(self) {
            that = self;
            o    = self.o;
            cs   = self.cs;
            va   = self.va;
            is   = self.is;
            ti   = self.ti;

            // Lay Module hoa`n toan co san~ trong Code
            M        = self.M;
            VIEW     = self.VIEW;
            POSITION = self.POSITION;
            // Lay Module o ngoai` Code
            PAG      = $.extend({}, rt01MODULE.PAG, self);
        };

    /**
     * MODULE SWIPE
     */
    rt01MODULE.SWIPE = {

        /**
         * TOGGLE EVENTS SWIPE KHI SWAP SLIDE
         */
        toggleEvent : function() {
            varibleModule(this);

            // Trang thai swipe events tren Slide hien tai
            var optsCur = va.$s.eq(cs.idCur).data('optsSlide');

            // Cap nhat thuoc tinh va bien cua Swipe event
            that.properties(optsCur);


            /**
             * TOGGLE EVENT SWIPE GESTURES
             */
            // Event Swipe toan bo Code
            if( is.swipeCur != is.swipeLast ) {    
                that.events( is.swipeCur ? true : false );
            }

            // Event Swipe tren Viewport
            var fnBodyToggle = function(isSwipeCur, isSwipeLast) {

                if( isSwipeLast != undefined && isSwipeCur != isSwipeLast ) {
                    that.events( isSwipeCur ? 'onBody' : 'offBody' );
                }
            };
            fnBodyToggle(is.swipeOnBodyCur, is.swipeOnBodyLast);
            fnBodyToggle(is.swipeOnSlideCur, is.swipeOnSlideLast);

            // Luu tru trang thai SwipeCur vao` SwipeLast
            is.swipeLast = is.swipeCur;
            is.swipeOnBodyLast = is.swipeOnBodyCur;
            is.swipeOnSlideLast = is.swipeOnSlideCur;
        },

        /**
         * CAP NHAT THUOC TINH VA BIEN SWIPE CUA SLIDE HIEN TAI
         */
        properties : function(optsCur) {
            varibleModule(this);

            // Setup luc ban dau
            is.swipeCur       = optsCur.isSwipe;
            is.swipeOnBodyCur = optsCur.swipe.isBody;


            // Setup hanh dong swipe --> Phan biet thanh swipeBody va swipePag
            if( optsCur.isSwipe ) {
                var swipe = optsCur.swipe;

                // Swipe tren Pagination
                is.swipeOnPag = true;
 
                // Swipe tren slide current
                is.swipeOnSlideCur = va.fxType == 'cssThree';

                // Tat Swipe tren body ne co swipe tren Slide
                if( is.swipeOnSlideCur ) is.swipeOnBodyCur = false;
            }

            else {
                is.swipeOnBodyCur = is.swipeOnSlideCur = is.swipeOnPag = false;
            }
        },
        
        /**
         * SETUP CAC EVENTS SWIPE
         */
        events : function(status) {
            var that = this;
            varibleModule(that);

            // Dang ki lai event tren cac doi tuong
            var isSwipeSupport = is.swipeSupport,
                evMouse = va.ev.mouse,
                evSwipe = va.ev.swipe;


            /**
             * FUNCTION CLASSES
             * Cac function loai bo doi tuo.ng
             */
            var fn = {

                // Loai bo event swipe 'Start' tren doi tuo.ng
                offStart : function($swipe) {

                    // Loai bo class 'swipe-on' --> ho tro nhan biet 'swipe gesture' va fix swipe trong IE mobile
                    // Loai bo event Drag tren cac Images trong Swipe
                    $swipe
                        .removeClass(va.ns +'swipe-on')
                        .off(va.ev.mouse.start +' '+ va.ev.swipe.start)
                        .off(va.ev.drag);
                },
                // Loai bo event swipe 'Move' va 'End' tren Document
                offMoveEnd : function() {
                    var ev = va.ev;
                    $(document)
                        .off(ev.mouse.move +' '+ ev.mouse.end +' '+ ev.swipe.move +' '+ ev.swipe.end);
                },


                /**
                 * LOAI BO EVENT SWIPE TREN CAC DOI TUONG
                 */
                offBody : function() {

                    // Loai bo class 'grab' khoi Viewport
                    // Tra lai vi tri cua slide truoc loai bo events
                    M.toggleClass('grab', -1);
                    that.setupEnd({}, va.swipeTypeCur, true);
                    fn.offStart(va.$viewport);
                },
                offPag : function() {

                    // Loai bo class 'grab' khoi Pagination
                    is.swipePagCur = false;
                    M.toggleClass('grab', -1, va.$pag);
                    is.pag && fn.offStart(va.$pag);
                },

                /**
                 * DANG KI EVENT SWIPE TREN CAC DOI TUONG
                 */
                onBody : function() {
                    if( is.swipeOnBodyCur || is.swipeOnSlideCur ) {

                        // Loai bo va dang ki lai swipe event tren DOC
                        fn.offMoveEnd();
                        fn.offBody();

                        // Dang ki swipe event cho doi tuong Viewport
                        M.toggleClass('grab', 0);
                        that.eventStart(va.$viewport, va.$canvas, evMouse);
                        isSwipeSupport && that.eventStart(va.$viewport, va.$canvas, evSwipe);
                    }
                },
                onPag : function() {
                    if( is.swipeOnPag && is.pag ) {

                        // Loai bo va dang ki lai swipe event tren DOC
                        fn.offMoveEnd();
                        fn.offPag();

                        // Dang ki swipe event cho doi tuong Pagination
                        is.swipePagCur = true;
                        M.toggleClass('grab', 0, va.$pag);
                        that.eventStart(va.$pag, va.$pagInner, evMouse);
                        isSwipeSupport && that.eventStart(va.$pag, va.$pagInner, evSwipe);
                    }
                }
            };



            /**
             * PHAN LOAI STATUS
             *  --> De chay function tuong u'ng
             *  --> phuc vu loai bo/dang ki rieng le DOI TUONG
             */
            if( status === true ) {
                fn.onBody();

                // Setup swipe on pagination luc' ban dau
                if( o.swipe.isAutoOnPag )
                    !va.pag.isViewLarge && fn.onPag();
                else
                    fn.onPag();
            }
            else if( status === false ) {
                fn.offBody();
                fn.offPag();
            }
            else fn[status]();
        },





        /**
         * SETUP EVENT VA THUOC TINH CHO DOI TUONG SWIPE ['VIEWPORT', 'PAGINATION']
         */
        eventStart : function($swipe, $swipeCanvas, evName) {
            varibleModule(this);
            var that = this, ns = va.ns;


            /**
             * THEM CLASS 'SWIPE-ON'
             *  + Nhan biet doi tuong co 'swipe gestures'
             *  + Fix swipe trong IE mobile
             */
            $swipe.addClass(ns +'swipe-on');


            /**
             * LOAI BO HANH DONG DRAG IMAGE TRONG CODE
             */
            $swipe
                .off(va.ev.drag)
                .on(va.ev.drag, function(e) { return false });




            /**
             * EVENT BAT DAU SWIPE - DRAG
             * swipeType --> ho tro swipe gestures cung luc event 'swipe' va 'mouse'
             * Touchmouse dung de phan bien swipe 'Code' hay scroll 'page'
             */
            $swipe.on(evName.start, { 'swipeType': evName.type }, function(e) {
                varibleModule(that);

                /**
                 * SETUP KHOI TAO
                 */
                // Huo'ng va Type cua Swipe gesture
                va.swipeDirs = null;
                var evSwipeType = e.data.swipeType;
                if( va.swipeTypeCur === null ) va.swipeTypeCur = evSwipeType;



                /**
                 * KIEM TRA DOI TUONG TARGET KHI SWIPE START DUOC CHO PHEP
                 */
                var tagSpecial    = ['input', 'textarea', 'label', 'a'],
                    eTarget       = e.target,
                    targetTag     = eTarget.tagName.toLowerCase(),
                    isTargetAllow = tagSpecial.indexOf(targetTag) == -1;

                // Loai bo Swipe start khi Target thuoc doi tuong 'Swipe Prevent'
                if( isTargetAllow ) {

                    // Class Prevent bao gom: 'swipe-preview', 'nav-prev', 'nav-next'
                    var classPrevent =  '.'+ ns +'swipe-prevent' +
                                        ', .'+ ns + o.namePrev +
                                        ', .'+ ns + o.nameNext,

                        $swipePrevent = $(eTarget).closest(classPrevent);

                    if( $swipePrevent.length ) {
                        isTargetAllow = false;

                        // Toggle event Drag tren $swipe de duoc select duoc text
                        that.eventDragToggle($swipe, va.ev[evSwipeType]);
                    }
                }

                // Loai bo event Swipe start khi Target duoc chua trong doi tuo.ng Link <a></a> && Nested
                if( isTargetAllow ) {
                    var $target     = $(eTarget),
                        $linkParent = $target.closest('a');

                    // Neu ton tai doi tuo.ng Parent la Link tag -> Kiem tra co nam trong Viewport hay khong
                    if( $linkParent.length ) {
                        var $viewportCheck = $linkParent.closest('.'+ ns + o.nameViewport);
                        if( $viewportCheck.length && $viewportCheck.is(va.$viewport) ) {
                            isTargetAllow = false;
                        }
                    }

                    // Kiem tra Target co phai doi tuo.ng la Code Nested hay khong
                    if( isTargetAllow ) {
                        var $code        = $target.closest('.'+ rt01VA.namespace),
                            $codeParent  = $code.parent().closest('.'+ rt01VA.namespace);

                        if( va.$self.is($codeParent) ) isTargetAllow = false;
                    }
                }




                /**
                 * DIEU KIEN TIEP TUC EVENT
                 */
                if( !(isTargetAllow && !is.lockSwipe && va.swipeTypeCur == evSwipeType) ) return;
                

                /**
                 * BAT DAU DANG KI EVENT SWIPE TREN DOCUMENT KHI BAT DAU SWIPE
                 */
                that.eventMoveEnd(va.ev[evSwipeType]);


                /**
                 * SWIPE END
                 * loai bo 'mouseleave' --> vi khong can thiet va giup Code don Gian
                 */
                $(document).one(evName.end, { 'swipeType': evName.type }, function(e) {

                    // Them Timer de fixed iOS touchEnd cha.m
                    // setTimeout(function() {
                    //     that.setupEnd(e, e.data.swipeType, false);
                    // }, 4);

                    that.setupEnd(e, e.data.swipeType, false);
                });
                




                /**
                 * SETUP CAC BIEN LUC BAT DAU
                 * Lay thoi gian luc bat dau darg
                 */
                var isCanvas = $swipeCanvas.is(va.$canvas);
                va.tDrag0 = va.tDrag1 = +new Date();

                // Luu thuoc tinh doi tuong nao dang swipe --> chi duoc 1 doi tuong duy nhat hoat dong
                if( is.swipeOnSlideCur && isCanvas ) va.$swipeCur = va.$s.eq(cs.idCur);
                else va.$swipeCur = $swipeCanvas;

                // Canvas: loai bo thuoc tinh transition --> di chuyen bang 'tap' event
                M.tsRemove($swipeCanvas);

                // Lay gia tri cac bien trong 'va.can' hoac 'va.pag'
                var p = M.swapVaOnSwipe();

                

                /**
                 * SETUP VI TRI BAT DAU SWIPE
                 *  + X0: get value --> lay vi tri ban dau, khi di chuyen lay vi tri hien tai tru` di vi tri goc
                 *  + x0Fix --> vi tri ban dau khi swipe, khong thay doi khi chuyen sang slide khac
                 *  + pageX1 --> ho tro khi swipe 'tap' moi bat dau --> pageX0 lay gia tri pageX o day
                 */
                var i = that.EVENTS.getEventRight(e);
                va.x0 = va.x0Fix = va.pageX1 = M.r( i[p.pageX] );

                // Y0 get value --> su dung de nhan biet swipe Code hay swipe page
                va.y0 = i.pageY;

                // xOffset, xBuffer : reset value
                va.xOffset = va.xBuffer = 0;

                // xBuffer bat dau bang xCanvas --> khi di chuyen chi viec +/- gia thi hien thoi
                if( is.swipeOnSlideCur && isCanvas ) va.xBuffer = 0;
                else                                 va.xBuffer = p.xCanvas;

                // Bien reset lai dragBegin --> bien voi muc dich thuc hien 1 lan ban dau trong luc 'mouseMove'
                is.swipeBegin = true;

                // Reset gia tri so luong event move swipe thuc thi --> ho tro cho event trigger 'swipeBegin'
                va.nMoveEvent = 0;

                // Canvas grabbing cursor
                va.$swipeCur.is(va.$canvas.add(va.$s)) && M.toggleClass('grab', 1);



                /**
                 *  + Fixed loi cursor hien thi lai 'default' sau khi click
                 *  + Khong thuc hien trong mobile --> khong scroll page duoc
                 */
                evSwipeType == 'mouse' && e.preventDefault();
            });
        },

        eventMoveEnd : function(evName) {
            var that = this;

            /**
             * EVENT SWIPE MOVE
             */
            $(document).on(evName.move, { 'swipeType': evName.type }, function(e) {
                varibleModule(that);
                var evSwipeType = e.data.swipeType;


                /**
                 * DIEU KIEN DE TIEP TUC EVENT - DI CHUYEN TAM THOI
                 */
                if( !(!is.lockSwipe && va.swipeTypeCur == evSwipeType) ) return;



                /**
                 * TIEP TUC EVENT - DI CHUYEN TAM THOI
                 * Trigger event 'swipeBegin'
                 */
                !va.nMoveEvent && cs.ev.trigger('swipeBegin');
                va.nMoveEvent++;

                // Lay dung' doi tuong Event
                var i = that.EVENTS.getEventRight(e);

                // Luu tru pageX cu va lay pageX moi --> de tim dang swipe 'trai' hay 'phai'
                var p = M.swapVaOnSwipe();
                va.pageX0 = va.pageX1;
                va.pageX1 = M.r( i[p.pageX] );



                /**
                 * Chi tinh toan khi pageX0 khac pageX1 --> tiet kiem CPU
                 */
                // $('.log').text( $('.log').text() + evSwipeType +' '+ va.pageX1 + ' - ');
                if( va.pageX0 != va.pageX1 ) {

                   // Gia tri di chuyen offset tam thoi
                    va.xOffset = va.pageX1 - va.x0;

                    // Phan biet swipe sang trai hay phai --> su dung cho swipe limit
                    is.swipeNav = (va.pageX1 > va.pageX0) ? 'right' : 'left';


                    /**
                     * DI CHUYEN TAM THOI TREN THIET BI MOBILE
                     * Phan biet scroll page hay la swipe Code
                     * Scroll page: vi khong co e.preventDefault() o trong touchstart va touchmove
                     * --> chi thuc hien 1 lan touchmove va ko co touchend
                     */
                    if( evSwipeType == 'swipe' ) {

                        va.y = M.a(va.y0 - i.pageY);
                        if( va.swipeDirs === null && M.a(va.xOffset) >= va.y ) va.swipeDirs = 'chieuX';
                        if( va.swipeDirs === null && va.y > 5 )                va.swipeDirs = 'chieuY';


                        // Truong hop swipe theo chieu Ngang X
                        if( va.swipeDirs === null || va.swipeDirs == 'chieuX' ) {

                            // Ngan ca?n di chuyen scroll page huo'ng Y cho Android
                            // Thu nghiem tren Chrome mobile simulate luc' duoc luc khong
                            e.preventDefault();

                            // Di chuyen tam thoi
                            that.xBuffer(va.pageX1);
                        }

                        // Truong hop chieu Doc Y
                        // Loai bo? event swipe 'Move' 'End' cua Document
                        else {
                            that.events('offMoveEnd');
                        }
                    }

                    // Mac dinh browser tren 'Desktop'
                    else that.xBuffer(va.pageX1);
                }

                // Pagination Grabbing Cursor: toggle class
                va.$swipeCur.is(va.$pagInner) && M.toggleClass('grab', 1, va.$pag);

                // Khoa event Tap, kiem offset de ho tro click de dang neu swipe it
                if( M.a(va.xOffset) > 10 && is.tapEnable ) is.tapEnable = false;     // Tap event tro nen cham chap
            });
        },

        /**
         * SETUP CAC THANH PHAN KHI KET THUC HANH DONG SWIPE
         */
        setupEnd : function(e, evSwipeType, isScrollPage) {
            varibleModule(this);

            if( !is.lockSwipe && va.swipeTypeCur == evSwipeType ) {

                // Ngan can event mouseUp o tren thiet bi ho tro touch event
                // Neu la scrollpage trong androidNative khong cho prevent --> khong scrollpage dc
                if( evSwipeType == 'swipe' && !isScrollPage ) {
                    e.preventDefault();
                }


                // Callback event end swipe
                !is.swipeBegin && cs.ev.trigger('swipeEnd');

                // Get thoi gian luc swipe out --> tinh toan nhanh hay cham
                va.tDrag1 = +new Date();

                // Tinh toan vi tri di chuyen sau khi swipe
                that.xNear();


                /**
                 * TOGGLE CLASS CURSOR
                 *  + Canvas --> phuc hoi lai cursor swipe
                 *  + Pagination --> xoa bo class cursor
                 */
                va.$swipeCur.is(va.$canvas.add(va.$s))
                ? M.toggleClass('grab', (is.swipeOnBodyCur || is.swipeOnSlideCur) ? 0 : -1)
                : M.toggleClass('grab', -1, va.$pag);

                // Loai bo class GrabStop khi swipe leave
                o.isViewGrabStop && M.toggleClass('stop', -1);
            }


            /**
             * OTHERS SETUP
             */
            // Reset lai gia tri swipeTypeCur o cuoi event
            // Phai co so sanh --> boi vi co 2 events mouse va touch trong mobile
            if( va.swipeTypeCur == evSwipeType ) va.swipeTypeCur = null;

            // Loai bo? Tap event trong luc Swipe gestures
            if( is.mobile ) is.tapEnable = true;
            else            setTimeout(function() { is.tapEnable = true }, 10);

            // Loai bo event Swipe 'Move' va 'End' tren Document khi ket thuc hanh dong swipe
            that.events('offMoveEnd');
        },

        /**
         * SETUP LOAI BO EVENT DRAG CUA SWIPE CURRENT -> HO TRO SELECT TEXT
         */ 
        eventDragToggle : function($swipe, evName) {
            var that = this;
            varibleModule(that);

            // Loai bo event Drag tren Swipe
            $swipe.off(va.ev.drag);

            // Phuc hoi lai event Drag da~ loai bo khi Tap ket thuc
            var evNameEndCur = evName.end +'stopDrag';
            $(document).on(evNameEndCur, function(e) {
                varibleModule(that);

                $swipe.on(va.ev.drag, function() { return false });
                $(document).off(evNameEndCur);
            });
        },




        /**
         * SETUP DI CHUYEN TAM KHI SWIPE LIEN TUC
         */
        xBuffer : function(xCur) {
            varibleModule(this);

            // Bien shortcut va khoi tao ban dau
            var layout     = va.layout,
                view       = va.view,
                idCur      = cs.idCur,
                isRight    = is.swipeNav == 'right',
                isLeft     = is.swipeNav == 'left',

                isCanvas   = va.$swipeCur.is(va.$canvas.add(va.$s)),
                p          = isCanvas ? va.can : va.pag,
                sTranslate = p.sTranslate,

                // Thuoc tinh luu tru su khac nhau khi di chuyen 'next' hay 'prev'
                sign = va.xOffset < 0 ? 1 : -1,

                // Khoang cach xe dich khi swipe move
                pageX = va.pageX1 - va.pageX0;



            /**
             * SETUP BIEN CO QUYEN DI CHUYEN TAM THOI HAY KHONG DUA TREN HIEU U'NG
             */
            var isBufferReduce = true,
                isBufferMove   = true;

            if( isCanvas ) {
                if( va.fxType == 'cssThree' ) isBufferReduce = false;
                if( va.fxType == 'fade' )     isBufferReduce = isBufferMove = false;
            }





            /**
             * GIAM TI LE GIA TRI DI CHUYEN --> SWIPE OUT VIEWPORT
             * TRUONG HOP LAYOUT LINE
             * Chi ap dung cho Canvas co isLoop-0 va pagination
             */
            var fnTranslateReduce1 = function() {

                /**
                 * DIEU KIEN DE GIAM TI LE
                 * Swipe limit chi ap dung khi swipe phai va swipe trai Canvas o ngoai Viewport
                 */
                if( (isRight && va.xBuffer > p.xMin)
                ||  (isLeft  && va.xBuffer < p.xMax) ) {

                    // Giam ti le xuong 8 lan cho desktop, mobile thi nho hon
                    var nRate1 = is.mobile ? 4 : 8;
                    pageX /= nRate1;
                }
            },

            fnTranslateReduce2 = function() {

                // Giam ti le di chuyen mac dinh tren layout dot
                var nRate2 = is.mobile ? 3 : 6;
                pageX /= nRate2;

                // Tiep tuc giam ti le neu isloop false
                if( !is.loop
                &&  (  (idCur <= 0 && isRight)
                    || (idCur >= num-1 && isLeft) ) ) {

                    pageX /= 4;
                }
            };

            // Khong thuc hien gia?m Buffer tren Canvas co bufferReduce false
            if( isBufferReduce ) {

                // Truong hop SwipeCur la Body Canvas
                if( isCanvas ) {
                    if( layout == 'line' && !is.loop ) fnTranslateReduce1();
                    if( layout == 'dot' )              fnTranslateReduce2();

                    /**
                     * GRAB STOP VIEW
                     */
                    if( !is.loop && o.isViewGrabStop ) {

                        if     ( isRight && va.xBuffer > 0 )      M.toggleClass('stop', 0);
                        else if( isLeft  && va.xBuffer < p.xMax ) M.toggleClass('stop', 1);
                    }
                }

                // Truong hop SwipeCur la Pag Inner
                else {
                    if( is.pag ) {
                        fnTranslateReduce1();

                        /**
                         * SETUP OTHER
                         */
                        // Pag Arrow kiem tra Toggle actived
                        // Di chuyen tam thoi cho Pag Mark
                        o.pag.isArrow && PAG.arrowActived(va.xBuffer);
                        o.pag.isMark  && PAG.xBufferOnMark(pageX);
                    }
                }
            }
            


            /**
             * DI CHUYEN BUFFER CHO CANVAS
             */
            if( isCanvas && (view == 'coverflow' || view == 'scale') )
                va.xBuffer += pageX * sTranslate / va.wSlideFull;
            else
                va.xBuffer += pageX;

            // Di chuyen doi tuo.ng Swipe tam thoi
            // Di chuyen x/y tuy theo huong swipe
            if( isBufferMove ) {

                var dirsTf = (p.dirs == 'hor') ? 'tlx' : 'tly',
                    xCur   = M.r(va.xBuffer),
                    tf     = {};

                tf[p.cssTf] = M[dirsTf](xCur);
                va.$swipeCur.css(tf);
            }

            // UPDATE TRANSFORM CAC SLIDE CHINH GIUA
            // Truyen tham so a: phan biet swipe 'next'/'prev'
            var bufferName = 'buffer'+ va.View;
            isCanvas && !!VIEW[bufferName] && VIEW[bufferName](sign);



            /**
             * SETUP CHUYEN TIEP SLIDE KHI SWIPE LIEN TUC TRONG LAYOUT 'LINE'
             * Next/prev cung cong thuc voi nhau nhung khac bien so 'a.s'
             * Vi so sanh cua 'next' la '>', con so sanh cua 'prev' la '<' nen nhan hai ve cho -1 de dao nguoc lai tu '<' sang '>'
             * @param int p.xCanvas
             */
            if( isCanvas && layout == 'line' ) {
                var posNext = p.xCanvas - (sTranslate * sign);

                // Swipe 'next' slide (so am) --> Swipe 'prev' tuong tu nhu 'next'
                if( va.xBuffer * sign < posNext * sign ) {

                    // Reset action chi thuc hien 1 lan trong luc drag lien tuc
                    is.swipeBegin = true;

                    // Update va.x0 --> su dung cho event dragmove --> de khi dragOut thi Canvas chi di chuyen toi da 1 slide nua
                    va.x0 = va.pageX1;

                    // Update xCanvas
                    p.xCanvas -= sTranslate * sign;

                    /**
                     * Update cac thanh phan khac khi next 1 slide
                     *  + Them option isContinuity --> ngan can setup 1 so options, trong do co POSITION.xAnimate
                     *  + Boi vi xCanvas da update o tren
                     */
                    that.TOSLIDE.run(sign, false, true);
                }
            }



            /**
             * SETUP CHUYEN TIEP SLIDE KE TIEP KHI SWIPE LIEN TUC TRONG HIEU UNG 'FADE'
             * @param int va.xBuffer
             */
            else if( isCanvas && view == 'fade' ) {
                var posNext = - (va.wSlide * sign);

                // Swipe 'next' slide (so am) --> Swipe 'prev' tuong tu nhu 'next'
                if( va.xBuffer * sign < posNext * sign ) {

                    // Reset action chi thuc hien 1 lan trong luc drag lien tuc
                    is.swipeBegin = true;

                    // Update va.x0 --> su dung cho event dragmove --> de khi dragOut thi Canvas chi di chuyen toi da 1 slide nua
                    va.x0 = va.pageX1;

                    // Reset vi tri xBuffer --> Ngan cha.n setup fn nay` lien tuc
                    va.xBuffer = 0;

                    // Update cac thanh phan khac khi next 1 slide
                    that.TOSLIDE.run(sign, false, true);
                }
            }




            /**
             * SEUP OTHERS
             *  + is.swipeBegin --> voi muc dich function chi chay 1 lan trong di drag move
             */
            if( is.swipeBegin ) {
                is.swipeBegin = false;

                view == 'mask' && VIEW.cloneImgbackInMask();
            }
        },

        /**
         * SETUP DI CHUYEN DEN SLIDE GAN DO KHI SWIPE KET THUC
         */
        xNear : function() {
            varibleModule(this);

            // Vi tri va kich thuoc doi tuong dang swipe
            var isCanvas = va.$swipeCur.is(va.$canvas.add(va.$s)),
                layout   = va.layout,
                num      = cs.num,
                p        = isCanvas ? va.can : va.pag,
                xOffset  = va.xOffset;  // Da~ di chuyen bao nhieu px

            // Setup Easing chuyen dong khi Swipe ket thuc
            va.moveBy = 'swipe';


            /**
             * SETUP BIEN QUYEN DUOC DI CHUYEN HOAC PHUC HOI VI TRI DUA TREN HIEU U'NG
             */
            var isNearRestore = true;
            if( isCanvas ) {
               if( va.fxType == 'fade' ) isNearRestore = false;  
            }



            /**
             * SETUP TREN BODY CANVAS
             */
            if( isCanvas ) {
                var wSlide = !!va.pa.left ? va.wSlideFull - (va.pa.left * 2) : va.wSlideFull,
                    tFast  = is.mobile ? 600 : 400,
                    isFast = va.tDrag1 - va.tDrag0 < tFast;


                // Width drag: select
                // Xac dinh di chuyen nhanh/cham cua 1 slide
                var w3  = M.r(wSlide/3),
                    w20 = M.r(wSlide/20),
                    wLimit = isFast ? w20 : w3,

                    // Thoi gian layout dot phuc hoi vi tri cu khi di chuyen sang slide moi
                    tGo = 100,
                    // Thoi gian khi slide phuc hoi lai vi tri cu
                    tRestore = 400;



                /**
                 * SETUP DI CHUYEN NEXT - PREV - RESET
                 */
                // Di chuyen toi Slide Next
                if( xOffset < -wLimit && (is.loop || (!is.loop && cs.idCur < num - 1)) && !!(num - 1) ) {

                    (layout == 'dot') && POSITION.xAnimate(null, 0, false, false, tGo);
                    that.TOSLIDE.run(1);
                }

                // Di chuyen toi Slide Prev
                else if( xOffset > wLimit && (is.loop || (!is.loop && cs.idCur > 0)) && !!(num - 1) ) {

                    (layout == 'dot') && POSITION.xAnimate(null, 0, false, false, tGo);
                    that.TOSLIDE.run(-1);
                }

                // Phuc hoi vi tri
                else if( xOffset != 0 ) {
                    var isPosFixed  = is.swipeOnSlideCur ? true : false,
                        restoreName = 'restore'+ va.View;

                    // Di chuyen toi vi tri ban dau --> View 'fade' Khong can thiet phuc hoi vi tri
                    isNearRestore && POSITION.xAnimate(null, 0, false, isPosFixed, tRestore);

                    // Phuc hoi lai vi tri va transform sau khi di chuyen tam thoi
                    !!VIEW[restoreName] && VIEW[restoreName]();
                }

                
                // Slideshow: setup bien --> reset timer khi di chuyen next/prev toi slide khac
                if( (xOffset < -wLimit || xOffset > wLimit) && o.isSlideshow ) is.hoverAction = true;
            }



            /**
             * SETUP TREN PAGINATION INNER
             */
            else {
                if( is.pag && xOffset != 0 ) {

                    // Update gia tri xCanvas
                    p.xCanvas = va.xBuffer;

                    // Phuc hoi lai vi tri chinh giua cho PagInner
                    var sp = o.pag.speed;
                    if( p.align == 'center' || p.align == 'end' ) {
                        p.xCanvas != p.xMin && POSITION.xAnimate(null, p.xMin, false, true, sp);
                    }

                    // Phuc hoi lai vi tri dau/cuoi neu Canvas o ngoai Viewport
                    else {
                        if( p.xCanvas > 0 )           { POSITION.xAnimate(null, 0, false, true, sp) }
                        else if( p.xCanvas < p.xMax ) { POSITION.xAnimate(null, p.xMax, false, true, sp) }
                    }


                    // Kiem tra actived tren Pag Arrow
                    o.pag.isArrow && PAG.arrowActived(p.xCanvas);

                    // Loai bo Transition Duration tren Pag Mark
                    // Update lai vi tri cua Pag Mark
                    if( o.pag.isMark ) {
                        is.ts && M.tsRemove(va.$pagMarkItem);
                        PAG.sizePosOfMark();
                    }
                }
            }



            /**
             * Other setup
             *  + Flywheel (banh da): tiep tuc di chuyen
             */
            POSITION.flywheel();
        }
    };
})(jQuery);