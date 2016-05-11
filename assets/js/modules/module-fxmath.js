/**
 * MODULE MATH EFFECTS
 * ========================================================================== */
(function($) {

    // Kiem tra bien module
    if( !window.rt01MODULE ) window.rt01MODULE = {};

    // Bien toan cuc
    var that, o, va, is, ti, M, FX, i, j, idCur, speed, cssTf,
        varibleModule = function(self) {
            that  = self;
            o     = self.o;
            va    = self.va;
            is    = self.is;
            ti    = self.ti;
            M     = self.M;
            FX    = self.FX;

            idCur = self.cs.idCur;
            speed = va.speed;
            cssTf = va.cssTf
        };

    /**
     * MODULE HIEU UNG MATH
     */
    rt01MODULE.FXMATH = {

        /**
         * KIEM TRA HIEU U'NG TRUOC KHI SETUP BEGIN
         */
        check : function(f) {
            varibleModule(this);
            var fxIdCur = va.fx[idCur];
            

            /**
             * SETUP HIEU UNG RANDOM 
             */
            // Neu fxIdCur la 'randomMath' --> thi Random hieu u'ng trong Mang Fx Math
            if( fxIdCur == 'randomMath' ) {
                fxIdCur = M.randomInArray2(o.fxMathName, va.fxMathRandom);
            }
            // Neu fxIdCur la Mang? --> random trong Ma?ng
            else if( $.isArray(fxIdCur) ) {
                fxIdCur = M.randomInArray(fxCur, va.fxLast);
            }

            // Luu tru va lay ten hieu ung hien tai
            var fxCur = va.fxLast = fxIdCur;



            // Setup truong hop co image background
            f.$imgSlCur = f.$slCur.data('$imgback');

            if( !!f.$imgSlCur && !!that[fxCur] ) that[fxCur](f);
            else FX.end(null);
        },


        /**
         * SETUP BAN DAU KHI THUC HIEN HIEU U'NG
         */
        setupBegin : function(f, isNoInvert, isSizeSquare, isImgFading) {
            varibleModule(this);
            var ns = va.ns;

            /**
             * LOAI BO NHUNG THAN PHAN SOT LAI CUA HIEU UNG CU~
             *  + Loai bo css 'visibility' tren Slide Current cua hieu u'ng cu
             *  + Loai bo fxOverlay cua hieu u'ng cu~
             */
            clearTimeout(ti.fxEnd);
            !!va.$fxSlCur && va.$fxSlCur.css('visibility', '');
            !!va.$fxOverlay && va.$fxOverlay.remove();

            

            /**
             * SETUP BAN DAU TUY THEO HUONG NEXT - PREV
             */
            if( isNoInvert ) f.isNext = true;

            // Truong hop di chuyen theo huo'ng Next
            if( f.isNext ) {
                f.mark       = -1;
                f.opacity    = 1;
                f.opaReverse = 0;
            }

            // Truong hop di chuyen theo huo'ng Prev
            else {
                f.mark       = 1;
                f.opacity    = 0;
                f.opaReverse = 1;
            }



            /**
             * SETUP CAC BIEN BAN DAU
             */
            var nCur      = idCur,
                isNext    = f.isNext,
                wCode     = va.wCode,
                wSlide    = va.wSlide,
                div       = '<div/>';

            f.$imgSlLast     = f.$slLast.data('$imgback');
            f.$imgItemSlLast = f.$imgSlLast.find('img');
            f.$imgItemSlCur  = f.$imgSlCur.find('img');
            f.wImgItemLast   = f.$imgItemSlLast.outerWidth(true);
            f.wImgItemCur    = f.$imgItemSlCur.outerWidth(true);    // Shortcut true width width image background current
            f.hCur           = f.$imgSlCur.outerHeight(true);       // Shortcut true width height image background current
            f.leftImgItemCur = M.pInt( f.$imgItemSlCur.css('left') );

            f.wLast          = (f.wImgItemLast < wSlide) ? f.wImgItemLast : wSlide;
            f.wCur           = (f.wImgItemCur < wSlide) ? f.wImgItemCur : wSlide;

            f.$imgFxBack     = isNext ? f.$imgSlLast.clone() : f.$imgSlCur.clone();
            f.$imgFxFront    = isNext ? f.$imgSlCur.clone() : f.$imgSlLast.clone();
            f.$imgItemFxFront = f.$imgFxFront.find('img');

            va.$fxOverlay    = $(div, {'class': ns +'fx-overlay'});
            f.$fxBack        = $(div, {'class': ns +'fx-back'});
            f.$fxFrontWrap   = $(div, {'class': ns +'fx-front-wrap'});
            f.$fxFront       = $(div, {'class': ns +'fx-front'});


            /**
             * CHEN CAC THANH PHAN HIEU U'NG LUC BAN DAU
             */
            // Chen FxBack vao FxOverlay
            f.$fxBack
                .append(f.$imgFxBack)
                .appendTo(va.$fxOverlay);


            // Luu tru va hidden Slide Current
            va.$fxSlCur = f.$slCur;
            va.$fxSlCur.css('visibility', 'hidden');

            // Chen fxFrontWrap vao fxOverlay
            va.$fxOverlay.append(f.$fxFrontWrap);

            // FxFrontWrap: add css height in height-fixed mode
            // Neu khong thi height la height cua Image background target
            var hFrontWrap = is.heightFixed ? va.hCode : f.hCur;
            f.$fxFrontWrap.css('height', hFrontWrap);

            // fxFront: chen cac Image vao
            f.$imgFxFront.appendTo(f.$fxFront);

            // Luu tru vi tri Left - Top hien tai cua Image Item Fx Front va`o Data
            // Ho tro. setup vi tri khac nhau trong Image position Tile
            f.$imgItemFxFront.each(function() {
                var $imgCur = $(this);

                $imgCur.data({
                    'left' : M.pInt( $imgCur.css('left') ),
                    'top'  : M.pInt( $imgCur.css('top') )
                });
            });




            /**
             * SETUP HIEU U'NG FADING CHO IMAGE BACK
             */
            if( isImgFading ) {
                var opacityBegin = isNext ? 1 : 0,
                    opacityEnd   = isNext ? 0.25 : 1,
                    styleBegin   = { 'opacity': opacityBegin };

                // Setup Image Item fading
                f.$fxBack
                    .find('img')
                    .css(styleBegin)
                    .animate({ 'opacity' : opacityEnd }, { 'duration' : speed[idCur] });
            }




            /**
             * SETUP KICH THUOC SLOT CUA SIZE SQUARE
             *  + Hoan doi gia tri slot giua width/height slides
             *  + Lam theo chuan: width > height
             */
            var fnGetSlot = function(w, h, nameVer, nameHor) {
                    var a = {};

                    // Store slot vertical
                    a[nameVer] = f.slot;

                    // Height value: get
                    a['height'] = M.c(h / f.slot);

                    // Number slot at horizontal, get width-slide larger
                    a[nameHor] = M.c(w / a['height']);

                    // Width front: combine slotHor and width-slide
                    var nRemain = w - (a['height'] * a[nameHor]);           // Number remainder, so voi number slotHor va width slide
                    a['width'] = a['height'] + M.c(nRemain / a[nameHor]);

                    return a;
                };


            // Slot: setup number
            if( isSizeSquare ) {

                // Height of wrapFront. Lay tong chieu cao cua slide -> trong height-fixed, lay hCode
                var height = is.heightFixed ? va.hCode : f.hCur;

                // f.slot convert to {} --> muc dich: gan f.slot cho gia tri nho nhat cua width var height slide
                // Truong hop mac dinh, width-slide > height -slide
                if( wSlide > height ) {
                    f.slot   = fnGetSlot(wSlide, height, 'ver', 'hor');
                    f.wFront = f.slot['width'];
                    f.hFront = f.slot['height'];
                }

                // Truong hop nguoc lai, dao nguoc number slot
                else {
                    f.slot   = fnGetSlot(height, wSlide, 'hor', 'ver');
                    f.wFront = f.slot['height'];
                    f.hFront = f.slot['width'];
                }


                // Front: setup size, kich thuoc gan bang hinh vuong
                f.$fxFront.css({ 'width' : f.wFront, 'height' : f.hFront });
                f.$imgFxFront.css({ 'width': '100%', 'height' : '100%' });
            }
            else {
                f.wFront = M.c(wSlide / f.slot);
                f.$fxFront.css({ 'width': f.wFront, 'height': '100%' });
                f.$imgFxFront.css({ 'width': f.wFront });
            }



            /**
             * SETUP VI TRI TOP KHI CHIEU CAO CUA 2 SLIDE KHAC NHAU
             *  + Trong height-auto: slideCur & slideCur, height khac nhau
             *  + Trong height-fixed: slideCur & slideCur, height giong nhau
             */
            f.top = M.r( (f.$slCur.outerHeight(true) - f.$slLast.outerHeight(true)) / 2 );
            if( !is.heightFixed ) {

                if( isSizeSquare ) {
                    if( isNext ) { f.$fxBack.css('top', f.top); f.top = 0; }
                }
                else {
                    if( isNext ) f.$fxBack.css('top', f.top);
                    else         f.$imgFxFront.css('top', f.top);
                }
            }


            // WrapFront: clear top value trong sizeSquare && height-fixed
            // css top value la gia tri de center slide trong height-fixed
            if( isSizeSquare && is.heightFixed ) {
                f.tImg = f.$imgFxFront.css('top');

                // Check top value: neu khong co gia tri, trong chrome tra ve '', con trong ie tra ve 'auto'
                if( f.tImg != '' && f.tImg != 'auto' ) {

                    // Cong vao var f.top
                    f.top += M.pInt(f.tImg);

                    // WrapFront: clear top value
                    f.$imgFxFront.css('top', '');
                }
            }
        },

        /**
         * VI TRI CUA IMAGE ITEM LUC BAT DAU - DANH CHO HIEU U'NG RECT
         */
        posBeginImgItem : function(f, i, j) {

            f.$imgItemFxFront.each(function() {
                var $imgCur = $(this);

                // Setup vi tri Left
                $imgCur.css('left', -(i * f.wFront) + $imgCur.data('left'));
                
                // Setup vi tri Top
                if( j != undefined ) $imgCur.css('top', -(j * f.hFront) + f.top + $imgCur.data('top'));
            });
        },


        /**
         * SETUP TRANSFORM END CHO FX FRONT
         */
        transformEnd : function(f, transformBy) {
            var that = this;
            varibleModule(that);


            // Dragstart stop
            f.$fxFrontWrap.on(va.ev.drag, function(e) { return false });
            va.fxTime0 = +new Date();

            // Easing: setup
            var esIn  = f.easeIn ? f.easeIn : 'easeOutCubic',
                esOut = f.easeOut ? f.easeOut : 'easeInCubic',
                es    = f.isNext ? M.easeName(esIn) : M.easeName(esOut);

            // Function setup Transform va Transition voi Timer
            var fnAnimation = function($ele, ts, tf, sp) {
                if( that.is.ts ) {
                    $ele.css(ts);
                    // Bat buoc phai co timer
                    setTimeout(function() { $ele.css(tf) }, 10);
                }
                else $ele.animate(tf, sp);
            };


            /**
             * SETUP CHUYEN DONG KET THUC CHO FX FRONT
             */
            f.$fxFront = f.$fxFrontWrap.find('.'+ va.ns +'fx-front');
            f.$fxFront.each(function() {

                var $eleCur = $(this),
                    sp      = ~~ $eleCur.data('speed'),
                    tf      = $eleCur.data('tfEnd'),
                    ts      = (typeof tf['opacity'] != 'number') ? M.ts(cssTf, sp, es) : M.ts('opacity', sp, es),
                    $obj;

                if( transformBy == 'self' ) $obj = $eleCur;
                if( transformBy == 'wrap' ) $obj = $eleCur.find('.'+ va.ns +'imgback');

                // Setup timer neu co opt Delay
                var delay = ~~ $eleCur.data('delay');
                if( delay == 0 ) fnAnimation($obj, ts, tf, sp);
                else             setTimeout(function() { fnAnimation($obj, ts, tf, sp) }, delay);
            });


            /**
             * SETUP OTHERS
             */
            // Chen toan bo fx DOM vao slide sau cung --> de tang toc do
            va.$fxOverlay.appendTo(va.$canvas);

            // Fx animation end
            FX.end();
        },




        /**
         * SETUP HIEU U'NG RECT
         */
        rectMove : function(f, slot) {
            varibleModule(this);
            
            /**
             * LAY SLOT VA SETUP BAN DAU CHO HIEU U'NG
             */
            if( slot === undefined ) {
                var slotMin = (va.wCode > 768) ? 5 : 3,
                    slotCur = va.slot[idCur];

                slot = (slotCur == 'auto') ? M.r( M.rm(2, slotMin + 3) ) : M.pInt(slotCur);
            }
            f.slot = slot;

            // Setup ban dau cho hieu u'ng
            that.setupBegin(f, true, false, false);



            /**
             * TRANSFORM BAN DAU DOI TUONG FX FRONT
             */
            var tfBegin = {},
                tfEnd   = {};

            tfBegin[cssTf] = M.tlx(f.mark * f.wFront);
            tfEnd[cssTf]   = M.tlx(0);

            // Di chuyen Wrap Front truoc
            f.$imgFxFront.css(tfBegin);

            // Slot position start & Image Slot position
            for( i = 0; i < f.slot; i++ ) {

                // Vi tri luc dau cua Image Item
                that.posBeginImgItem(f, i);


                f.$fxFront.clone()
                    .css({ 'left' : i * f.wFront, 'top' : 0 })
                    .data({ 'speed': speed[idCur], 'delay': 0, 'tfEnd': tfEnd })
                    .appendTo(f.$fxFrontWrap);
            }


            /**
             * TRANSFORM BAN DAU DOI TUONG FX BACK
             */
            var tfEndBack = {};
            tfEndBack[cssTf] = M.tlx(-f.mark * f.wFront);

            if( is.ts ) {
                // Easing: set
                f.easeIn  = 'easeOutCubic';
                f.easeOut = 'easeInCubic';
                var es = f.isNext ? M.easeName(f.easeIn) : M.easeName(f.easeOut),
                    ts = M.ts(cssTf, speed[idCur], es);

                setTimeout(function() { f.$imgFxBack.css(ts) }, 5);
                setTimeout(function() { f.$imgFxBack.css(tfEndBack) }, 10);
            }
            else {
                // Di chuyen vi tri Left cua $FxBack
                f.$fxBack.animate(tfEndBack, speed[idCur]);
            }

            // Ket thuc Transform
            that.transformEnd(f, 'wrap');
        },

        rectRun : function(f) {
            varibleModule(this);

            /**
             * LAY SLOT VA SETUP BAN DAU CHO HIEU U'NG
             */
            var slotCur = va.slot[idCur];
            f.slot = (slotCur == 'auto') ? M.r(M.rm(3,6)) : M.pInt(slotCur);

            // Setup ban dau cho hieu u'ng
            that.setupBegin(f, false, false, true);

            // Setup thoi gian Speed va Delay
            var speedCur = speed[idCur] / 4,
                delayAll = speed[idCur] - speedCur,
                delayOne = delayAll / (f.slot - 1);     // -1 : -> Lam` fxFront dau` tien chay ngay lap tuc



            /**
             * SETUP TRANSFORM CHO FX FRONT
             */
            var tfBegin, tfEnd, delay;
            for( i = 0; i < f.slot; i++ ) {

                // Vi tri luc dau cua Image Item
                that.posBeginImgItem(f, i);

                // Thoi gian delay va vi tri bat dau - ket thuc
                var delayCur = delayAll - (i * delayOne),
                    xBegin   = f.isNext ? -(f.wFront + 1) : i * f.wFront,
                    xEnd     = f.isNext ? M.r(i * f.wFront) : va.wSlide;


                // Transform ban dau cho fxFront
                tfBegin = {};
                tfBegin[cssTf] = M.tlx(xBegin);
                f.$fxFront.css(tfBegin);

                // Luu tru options tren data cua fxFront
                tfEnd = {};
                tfEnd[cssTf] = M.tlx(xEnd);

                f.$fxFront.clone()
                    .data({ 'speed': speedCur, 'delay' : delayCur, 'tfEnd': tfEnd })
                    .appendTo(f.$fxFrontWrap);
            }

            // Setup transform ket thuc cho fxFront
            that.transformEnd(f, 'self');
        },

        rectSlice : function(f) {
            varibleModule(this);

            /**
             * LAY SLOT VA SETUP BAN DAU CHO HIEU U'NG
             */
            var slotCur = va.slot[idCur];
            f.slot = (slotCur == 'auto') ? M.r(M.rm(4,10)) : M.pInt(slotCur);

            // Setup ban dau cho hieu u'ng
            that.setupBegin(f, false, false, true);


            // Setup thoi gia Speed va Delay
            var speedCur = speed[idCur] / 4,
                delayAll = speed[idCur] - speedCur,
                delayOne = delayAll / f.slot;


            /**
             * SETUP TRANSFORM CHO FX FRONT
             */
            var tfBegin, tfEnd, delayCur,
                yName = is.ts ? cssTf : 'top';

            for( i = 0; i < f.slot; i++ ) {

                // Vi tri luc dau cua Image Item
                that.posBeginImgItem(f, i);

                // Timer delay cho tung Item
                delayCur = f.isNext ? i * delayOne : (delayAll - (i * delayOne));


                // Transform ban dau cho fxFront
                var y      = (M.r(i / 2) > i / 2) ? 100 : -100,
                    yBegin = f.isNext ? y : 0,
                    yEnd   = f.isNext ? 0 : y;

                tfBegin = {};
                tfBegin[yName] = M.tly(yBegin, '%');
                f.$fxFront.css({ 'left': i * f.wFront }).css(tfBegin);


                // Luu tru options tren data cua fxFront
                tfEnd = {};
                tfEnd[yName] = M.tly(yEnd, '%');

                f.$fxFront.clone()
                    .data({ 'speed': speedCur , 'delay' : delayCur, 'tfEnd': tfEnd })
                    .appendTo(f.$fxFrontWrap);
            }

            // Setup transform ket thuc cho fxFront
            that.transformEnd(f, 'self');
        },




        /**
         * SETUP HIEU U'NG RUBY
         */
        rubyFade : function(f) {
            varibleModule(this);

            /**
             * LAY SLOT VA SETUP BAN DAU CHO HIEU U'NG
             */
            var slotCur = va.slot[idCur];
            f.slot = (slotCur == 'auto') ? M.r(M.rm(2, 4)) : M.pInt(slotCur);

            // Setup ban dau cho hieu u'ng
            that.setupBegin(f, false, true, false);


            // FxSlot: set Opacity
            f.$fxFront.css('opacity', f.opaReverse);


            // FxSlot & Image Slot: Position | Timer setup
            for( i = 0; i < f.slot.ver; i++ ) {
                for( j = 0; j < f.slot.hor; j++ ) {

                    // Vi tri luc bat dau cua Image Item
                    that.posBeginImgItem(f, j, i);

                    // Setup thoi gia Speed va Delay cua Item
                    var speedCur = M.r( M.rm(100, speed[idCur]) ),
                        delayCur = speed[idCur] - speedCur;


                    // Transform End ban dau va Luu tru options tren data cua fxFront
                    var tfEnd = {};
                    tfEnd['opacity'] = f.opacity;

                    f.$fxFront.clone()
                        .css({ 'left' : j * f.wFront, 'top' : i * f.hFront })
                        .data({ 'speed' : speedCur, 'delay' : delayCur, 'tfEnd' : tfEnd })
                        .appendTo(f.$fxFrontWrap);
                }
            }

            // Setup transform ket thuc cho fxFront
            f.easeOut = 'easeOutCubic';
            that.transformEnd(f, 'self');
        },

        rubyMove : function(f) {
            varibleModule(this);

            /**
             * LAY SLOT VA SETUP BAN DAU CHO HIEU U'NG
             */
            var slotCur = va.slot[idCur];
            f.slot = (slotCur == 'auto') ? M.r(M.rm(2, 4)) : M.pInt(slotCur);

            // Setup ban dau cho hieu u'ng
            that.setupBegin(f, false, true, false);


            // Function Tra ve vi tri Tu do
            var fnPosRandom = function(v) {
                var x, y, a = {};
                switch (v) {
                    case 0: a.x = 0;    a.y = -100; break;
                    case 1: a.x = 100;  a.y = 0;    break;
                    case 2: a.x = 0;    a.y = 100;  break;
                    case 3: a.x = -100; a.y = 0;    break;
                }
                return a;
            }


            // FxSlot & Image Slot: Position | Timer setup
            var xy, tfBegin, tfEnd;
            for( i = 0; i < f.slot.ver; i++ ) {
                for( j = 0; j < f.slot.hor; j++ ) {

                    // Vi tri luc bat dau cua Image Item
                    that.posBeginImgItem(f, j, i);


                    // Transform Ban dau cho Image fxFront
                    xy = fnPosRandom(M.r(M.ra()*3));
                    if( is.ts ) { 
                        tfBegin = {};
                        tfEnd   = {};
                        tfBegin[cssTf] = M.tl(xy.x, xy.y, '%');
                        tfEnd[cssTf]   = M.tl(0, 0, '%'); 
                    }
                    else {
                        tfBegin = {};
                        tfBegin['left'] = xy.x;
                        tfBegin['top']  = xy.y;
                        
                        tfEnd = {};
                        tfEnd['left'] = 0;
                        tfEnd['top']  = 0;
                    }
                    f.$imgFxFront.css( f.isNext ? tfBegin : tfEnd);


                    // Setup thoi gia Speed va Delay cua Item
                    var speedCur = M.rm(100, speed[idCur] / 2),
                        delayCur = M.ra() * (speed[idCur] - speedCur);

                    // Luu tru options tren data cua fxFront
                    f.$fxFront.clone()
                        .css({ 'left' : j * f.wFront, 'top' : i * f.hFront })
                        .data({ 'speed' : speedCur, 'delay' : delayCur, 'tfEnd' : f.isNext ? tfEnd : tfBegin })
                        .appendTo(f.$fxFrontWrap);
                }
            }

            // Setup transform ket thuc cho fxFront
            that.transformEnd(f, 'wrap');
        },

        rubyRun : function(f) {
            varibleModule(this);

            /**
             * LAY SLOT VA SETUP BAN DAU CHO HIEU U'NG
             */
            var slotCur = va.slot[idCur];
            f.slot = (slotCur == 'auto') ? M.r(M.rm(2,4)) : M.pInt(slotCur);

            // Setup ban dau cho hieu u'ng
            that.setupBegin(f, false, true, true);


            // FxSlot & Image Slot: Position | Timer setup
            var xy = {}, tfBegin, tfEnd;
            for( i = 0; i < f.slot.ver; i++ ) {
                for( j = 0; j < f.slot.hor; j++ ) {

                    // Vi tri luc bat dau cua Image Item
                    that.posBeginImgItem(f, j, i);


                    // Luu chon ngau nhien vi tri xuat hien
                    switch ( M.r(M.ra() * 3) ) {
                        case 0:
                            xy.x = j * f.wFront;
                            xy.y = -f.hFront;
                            break;
                        case 1:
                            xy.x = va.wSlide;
                            xy.y = i * f.hFront;
                            break;
                        case 2:
                            xy.x = j * f.wFront;
                            xy.y = f.hCur;
                            break;
                        case 3:
                            xy.x = -f.wFront;
                            xy.y = i * f.hFront;
                            break;
                    }

                    // Transform Ban dau cho fxFront
                    if( is.ts ) {
                        tfBegin = {};
                        tfEnd   = {};
                        tfBegin[cssTf] = M.tl(xy.x, xy.y);
                        tfEnd[cssTf]   = M.tl(j * f.wFront, i * f.hFront);
                    }
                    else {
                        tfBegin = {};
                        tfBegin['left'] = xy.x;
                        tfBegin['top']  = xy.y;

                        tfEnd = {};
                        tfEnd['left'] = j * f.wFront;
                        tfEnd['top']  = i * f.hFront;
                    }
                    f.$fxFront.css( f.isNext ? tfBegin : tfEnd );


                    // Setup thoi gia Speed va Delay
                    var speedCur = M.rm(100, 300),   // Hieu u'ng nhi`n dep hon so o? tren
                        delayCur = M.ra() * (speed[idCur] - speedCur);

                    // Luu tru options tren data cua fxFront
                    f.$fxFront.clone()
                        .data({ 'speed' : speedCur, 'delay' : delayCur, 'tfEnd' : f.isNext ? tfEnd : tfBegin })
                        .appendTo(f.$fxFrontWrap);
                }
            }

            // Setup transform ket thuc cho fxFront
            that.transformEnd(f, 'self');
        },

        rubyScale : function(f) {
            varibleModule(this);

            /**
             * LAY SLOT VA SETUP BAN DAU CHO HIEU U'NG
             */
            var slotCur = va.slot[idCur];
            f.slot = (slotCur == 'auto') ? M.r(M.rm(2,4)) : M.pInt(slotCur);

            // Setup ban dau cho hieu u'ng
            that.setupBegin(f, false, true, false);


            /**
             * SETUP TRANSFORM BAN DAU & KET THUC HO IMAGE FXFRONT
             * @param mixed scaleEnd
             */
            var scaleBegin = f.isNext ? 0 : 1,
                scaleEnd   = f.isNext ? 1 : 0;

            if( is.ts ) {
                var tf = {};
                tf[cssTf] = 'scale(' + scaleBegin + ')';
                f.$imgFxFront.css({'width': '100%', 'height' : '100%'}).css(tf);

                var tf = {};
                tf[cssTf] = 'scale(' + scaleEnd + ')';
                scaleEnd  = tf;
            }
            else {
                f.$imgFxFront.css({
                    'width' : scaleBegin * 100 +'%',
                    'height': scaleBegin * 100 +'%',
                    'left'  : scaleEnd * 50 +'%',
                    'top'   : scaleEnd * 50 +'%'
                });

                var tf = {};
                tf['width']  = scaleEnd * 100 +'%';
                tf['height'] = scaleEnd * 100 +'%';
                tf['left']   = scaleBegin * 50 +'%';
                tf['top']    = scaleBegin * 50 +'%';
                scaleEnd = tf;
            }



            // FxSlot & Image Slot: Position | Timer setup
            for( i = 0; i < f.slot.ver; i++ ) {
                for( j = 0; j < f.slot.hor; j++ ) {

                    // Vi tri luc bat dau cua Image Item
                    that.posBeginImgItem(f, j, i);
                    
                    // Setup thoi gia Speed va Delay cho Item
                    var speedCur = M.rm(100, 300),
                        delayCur = M.ra() * (speed[idCur] - speedCur);

                    // Setup Vi tri va Luu tru options tren data cua fxFront
                    f.$fxFront.clone()
                        .css({ 'left': j * f.wFront, 'top': i * f.hFront })
                        .data({ 'speed' : speedCur, 'delay' : delayCur, 'tfEnd': scaleEnd })
                        .appendTo(f.$fxFrontWrap);
                }
            }

            // Setup transform ket thuc cho fxFront
            that.transformEnd(f, 'wrap');
        },




        /**
         * SETUP HIEU U'NG ZIGZAG
         */
        zigzagRun : function(f) {
            varibleModule(this);

            /**
             * LAY SLOT VA SETUP BAN DAU CHO HIEU U'NG
             */
            var slotCur = va.slot[idCur];
            f.slot = (slotCur == 'auto') ? M.r(M.rm(2,5)) : M.pInt(slotCur);

            // Setup ban dau cho hieu u'ng
            that.setupBegin(f, false, true, true);

            // Setup thoi gia Speed va Delay cho FxFront
            var speedCur = ~~(speed[idCur] / (f.slot.ver * f.slot.hor) - 0.5),
                delayCur = speedCur;



            // FxSlot & Image Slot: Position | Timer setup
            var slotVer = f.slot.ver,
                slotHor = f.slot.hor,
                itemID  = 0,
                j0, j0, tfBegin, tfEnd, xBegin, xEnd;

            for( i = 0; i < f.slot.ver; i++ ) {
                for( j = 0; j < f.slot.hor; j++ ) {

                    // Vi tri luc bat dau cua Image Item
                    that.posBeginImgItem(f, j, i);


                    // Setup ID cua Item hien tai
                    j0 = slotHor - j;
                    j0 =  (M.r(j0 / 2) > j0 / 2) ? i : (slotVer - i - 1);
                    itemID = j0 + (slotVer * (slotHor - j - 1));


                    // Transform Ban dau cho Image fxFront
                    tfBegin = {};
                    xBegin  = f.isNext ? -(f.wFront + 1) : j * f.wFront;
                    tfBegin[cssTf] = M.tlx(xBegin);
                    f.$fxFront.css(tfBegin);


                    // Luu tru options tren data cua fxFront
                    tfEnd = {};
                    xEnd  = f.isNext ? j * f.wFront : va.wSlide;
                    tfEnd[cssTf] = M.tlx(xEnd);

                    f.$fxFront.clone()
                        .css({ 'top': i * f.hFront })
                        .data({ 'speed' : speedCur, 'delay' : delayCur * itemID, 'tfEnd' : tfEnd })
                        .appendTo(f.$fxFrontWrap);
                }
            }

            // Setup transform ket thuc cho fxFront
            that.transformEnd(f, 'self');
        }
    };
})(jQuery);