/**
 * MODULE VIEW ADVANCED
 * ========================================================================== */
(function($) {

    // Kiem tra bien module
    if( !window.rt01MODULE ) window.rt01MODULE = {};

    // Bien toan cuc
    var that, o, cs, va, is, ti, M, i, j,
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
     * MODULE VIEW ADVANCED
     */
    rt01MODULE.VIEW = {

        /**
         * SETUP THUOC TINH KHI RESIZE TRONG FN SIZE
         */
        // sizeMask : function() {
        //     varibleModule(this);

        //     // Setup thuoc tinh giong nhu VIEW.basic
        //     that.sizeBasic();

        //     /**
        //      * RESET LAI DOI TUONG IMAGE BACK NEU CO
        //      */
        //     var $imgback, $imgClone;
        //     for( i = 0; i < cs.num; i++ ) {

        //         $imgback = that.imgbackOfSlide(i);
        //         if( $imgback.length ) {
        //             $imgClone = $imgback.data('$imgClone');

        //             // Neu wrap Clone ton thi loai bo va tao cai moi
        //             !!$imgClone && $imgClone.remove();
        //             that.createImgClone(i);
        //         }
        //     }
        // },


        sizeMask : function() {
            varibleModule(this);

            // Setup thuoc tinh giong nhu VIEW.basic
            that.sizeBasic();


            /**
             * LUU TRU IMAGE BACK CUA SLIDE NEXT - PREV TREN SLIDE HIEN TAI
             */
            for( i = 0; i < cs.num; i++ ) {
                var $slideCur = va.$s.eq(i);

                // Setup idNext va idPrev
                var idMap      = va.idMap,
                    indexIDCur = idMap.indexOf(i), 
                    idPrev     = idMap[indexIDCur - 1],
                    idNext     = idMap[indexIDCur + 1];

                if( i == idMap[0] )         idPrev = idMap[cs.num - 1];
                if( i == idMap[cs.num - 1]) idNext = idMap[0];

                // Luu tru tren Slide hien tai
                $slideCur
                    .data('idPrev', idPrev)
                    .data('idNext', idNext);
            }
        },


        /**
         * SAO CHEP IMAGE BACK CUA SLIDE PREV-NEXT DEN SLIDE HIEN TAI
         */
        cloneImgbackInMask : function() {
            var that = this;
            varibleModule(that);

            var $slideCur = va.$s.eq(cs.idCur),
                slideData = $slideCur.data(),
                isImgbackPrevNextAdd = slideData['is']['imgbackPrevNextAdded'];


            if( !slideData.is.imgbackPrevNextAdded ) {
                var $imgCur  = $slideCur.data('$imgback'),
                    $imgPrev = va.$s.eq(slideData.idPrev).data('$imgback').parent(),
                    $imgNext = va.$s.eq(slideData.idNext).data('$imgback').parent();

                if( !!$imgPrev ) $imgCur.after( $imgPrev.clone().removeClass(va.ns +'imgback').addClass(va.ns +'imgback-prev') );
                if( !!$imgNext ) $imgCur.after( $imgNext.clone().removeClass(va.ns +'imgback').addClass(va.ns +'imgback-next') );

                // Luu tru vao data : da~ chen` Image back cua Slide Prev-Next vao Slide hien tai
                $slideCur.data( 'isImgbackPrevNextAdded', (!!$imgPrev && !!$imgNext ? true : false) );
            }
        },


        sizeCoverflow : function() {
            varibleModule(this);

            // Transform cua slide
            // Muc dich tim duoc transform cua tung slide --> roi gian vao cac slide
            var center  = va.center,
                cover   = o.coverflow,
                space   = cover.space,                  // Khoang cach giua cac slide
                rotate  = cover.rotate,                 // Goc xoay cua cac slide 2 ben left/right
                xRight  = va.wSlideFull - space,        // Vi tri right ke tiep slide o giua
                xLeft   = - xRight;                     // Vi tri left ke tiep slide o giua

            // Update gia tri sTranslate cho view Coverflow
            va.can.sTranslate = space;


            // Cac gia tri ban dau cua slide
            va.pBegin = [xLeft, 0, xRight];
            va.tfMap  = [that.tf1(xLeft, rotate), that.tf1(0, 0), that.tf1(xRight, - rotate)];
            va.zMap   = [ 0, 1, 0];

            
            // Vi tri cua cac slide left --> dao nguoc --> vi tri slide right --> vi tri hoan chinh cua cac slides
            // Transform, z-index cua cac slide tuong tu nhu vi tri
            var tf, p;
            for (i = 1; i < center.nLeft; i++)  {
                p  = xLeft - (i * space);
                tf = that.tf1(p, rotate);
                va.pBegin.unshift(p);       // Unshift() se return UNDEFINED trong IE8- --> kiem tra lai
                va.tfMap.unshift(tf);
                va.zMap.unshift(-i);
            }

            for( i = 1; i < center.nRight; i++ ) {
                p  = xRight + (i * space);
                tf = that.tf1(p, - rotate);
                va.pBegin.push(p);
                va.tfMap.push(tf);
                va.zMap.push(-i);
            }



            // Setup transform len cac slide voi cac gia tri vua tim duoc
            for( i = 0; i < cs.num; i++ ) {
                tf = {};
                tf = va.tfMap[i];
                tf['z-index'] = va.zMap[i];

                va.$s.eq(va.idMap[i]).css(tf);
            }


            // Canvas setup thuoc tinh perspective/origin transfrom 3d
            var perName = va.prefix + 'perspective',
                tf = {};

            // Update origin
            va.origin = M.r(va.wSlide / 2);

            tf[perName] = cover.perspective + 'px';
            tf[perName + '-origin'] = va.origin + 'px';
            va.$canvas.css(tf);
        },


        sizeScale : function() {

            // Tim kiem kich thuoc cua slide sau khi add transform --> ho tro translate/move/buffer slide
            // Co the tiet kiem bang cach khac --> * ti le
            var tf = {},
                intensity = o.scale.intensity,
                ints = intensity !== 0 ? intensity/100 : 0.1;         // Cuong do transform scale

            tf[cssTf] = 'scale(' + ints +')';

            // Tao slide ghost de lay kich thuoc --> lay xong roi xoa
            var $slGhost = $s.eq(cs.idCur).clone().addClass(va.ns + 'ghost').css(tf).appendTo($canvas),
                rect     = $slGhost[0].getBoundingClientRect();

            // Xoa slide ghost
            $slGhost.remove();



            // Tap hop cac gia tri cua slide --> tuong tung view coverflow
            var sTranslate = ~~(rect.width),
                xRight     = ~~( va.wSlide - (va.wSlide - sTranslate)/2 ),
                xLeft      = - xRight;

            // Update gia tri cua sTranslate
            va.can.sTranslate = sTranslate;
            va.gapBegin = xRight;       // Update khoang ban dau slide giua voi slide ke tiep --> ho tro buffer



            // Vi tri va tf luc dau
            va.pBegin = [xLeft, 0, xRight];
            va.tfMap  = [ that.tf2(xLeft, ints), that.tf2(0, 1), that.tf2(xRight, ints)];

            // Vi tri va tf con lai cua cac slide
            var tf, x;
            for (i = 1; i < va.center.nLeft; i++)  {
                x = xLeft - (i * sTranslate);
                tf = that.tf2(x, ints);
                va.pBegin.unshift(x);       // Su dung unshift() --> xem co tuong thich voi IE8-
                va.tfMap.unshift(tf);
            }

            for (i = 1; i < va.center.nRight; i++) {
                x  = xRight + (i * sTranslate);
                tf = that.tf2(x, ints);
                va.pBegin.push(x);
                va.tfMap.push(tf);
            }


            // Setup transform moi vua tim duoc len cac slide
            for (i = 0; i < num; i++) {

                var tf = va.tfMap[i];
                $s.eq(va.idMap[i]).css(tf);
            }
        },



        /**
         * SETUP CAC SLIDE KHI CHUYEN TAM THOI
         */
        bufferMask0 : function(sign) {
            varibleModule(this);

            // Xac dinh id cua slide chinh giua current va last
            var idMap        = va.idMap,
                classImage   = '.'+ va.ns +'imgback',
                isSwipeBegin = is.swipeBegin,
                idLast       = va.center.nLeft,
                idCur        = idLast + sign;

            // Cac id cua slide setup giong nhau
            var fnSetupID = function(id, xPlus, isCur) {

                var $imgback = va.$s.eq(idMap[id]).find(classImage),
                    x        = - va.xOffset - xPlus,         // Vi tri tam thoi cua imgback
                    tf       = that.tf1(x);

                if( isSwipeBegin ) {
                    // Loai bo transition cua imgback truoc khi setup transform
                    M.tsRemove($imgback);
                    // Tao va append imgClone vao imgback
                    that.updateImgClone(id, sign, isCur);
                }

                // Setup transform tam thoi len imgback
                $imgback.css(tf);
            };

            fnSetupID(idCur, va.can.sTranslate * sign, 1);
            fnSetupID(idLast, 0, 0);


            // Dong thoi loai bo transition cua imgback slide prev
            isSwipeBegin && M.tsRemove( va.$s.eq(idMap[idLast - sign]).find(classImage) );
        },


        bufferMask : function(sign) {
            varibleModule(this);

            // Xac dinh id cua slide chinh giua current va last
            var idMap        = va.idMap,
                classImage   = '.'+ va.ns +'imgback',
                isSwipeBegin = is.swipeBegin,
                idLast       = va.center.nLeft,
                idCur        = idLast + sign;

            // Cac id cua slide setup giong nhau
            var fnSetupID = function(id, xPlus, isCur) {

                var $imgback = va.$s.eq(idMap[id]).find(classImage),
                    x        = - va.xOffset - xPlus,         // Vi tri tam thoi cua imgback
                    tf       = that.tf1(x);

                if( isSwipeBegin ) {
                    // Loai bo transition cua imgback truoc khi setup transform
                    M.tsRemove($imgback);
                    // Tao va append imgClone vao imgback
                    // that.updateImgClone(id, sign, isCur);
                }

                // Setup transform tam thoi len imgback
                $imgback.css(tf);
            };

            // fnSetupID(idCur, va.can.sTranslate * sign, 1);
            // fnSetupID(idLast, 0, 0);


            // Dong thoi loai bo transition cua imgback slide prev
            // isSwipeBegin && M.tsRemove( va.$s.eq(idMap[idLast - sign]).find(classImage) );
        },


        bufferCoverflow : function(sign) {
            varibleModule(this);

            // Bien shortcut va khoi tao ban dau
            var sTranslate = va.can.sTranslate,
                wSlideFull = va.wSlideFull,
                cover      = o.coverflow,
                offset     = va.xOffset,
                
                // Xac dinh id cua slide chinh giua current va last
                idLast = va.center.nLeft,
                idCur  = idLast + sign;

            // Ho tro tim vi tri va goc xoay hien tai cua slide
            var gap     = wSlideFull - cover.space - sTranslate,       // Khoang cach slide chinh giua di chuyen duoc
                xOffset = offset / wSlideFull * gap,
                ratio   = - offset / wSlideFull;                  // Ti le di chuyen tu 0 -> 1, lam tron so do goc


            // Setup tren tung slide
            var fnSetupID = function(id, nDegPlus) {

                var slide = va.$s.eq(va.idMap[id]),
                    x     = va.pBegin[id] + xOffset,                // Khoang cach tam thoi cua slide chinh giua
                    nDeg  = cover.rotate * (ratio + nDegPlus),      // Goc xoay tam thoi cua slide chinh giua
                    tf    = that.tf1(x, nDeg);

                // Loai bo transition luc bat dau drag --> thuc hien chi 1 lan --> toi uu tinh toan
                is.swipeBegin && M.tsRemove(slide);

                // Add transform moi tinh toan dc vao slide
                slide.css(tf);
            };

            fnSetupID(idCur, -sign);
            fnSetupID(idLast, 0);


            // Ho tro them: loai bo slide doi dien idLast --> drag toi drag lui thi 3 slide bi anh huong
            if( is.swipeBegin ) {
                var $slSymmetry = va.$s.eq(va.idMap[idLast - sign]);
                M.tsRemove($slSymmetry);
            }


            // Perspective origin tam thoi cua Canvas
            var originX = va.origin - M.r(sTranslate * (offset / wSlideFull)),
                tf = {};
            tf[va.prefix + 'perspective-origin'] = originX + 'px 50%';
            va.$canvas.css(tf);
        },


        bufferScale : function(sign) {

            // Shortcut varible
            var intensity = o.scale.intensity,
                idMap     = va.idMap;

            // Xac dinh id cua slide chinh giua current va last
            var idLast = va.center.nLeft,
                idCur  = idLast + sign,

                slCur  = $s.eq(idMap[idCur]),
                slLast = $s.eq(idMap[idLast]);


            // Khoang cach tam thoi cua slide chinh giua
            var gap     = va.gapBegin - va.can.sTranslate,
                xOffset = va.xOffset / va.wSlideFull * gap,        // Khoang cach slide chinh giua di chuyen duoc
                xCur    = va.pBegin[idCur] + xOffset,
                xLast   = va.pBegin[idLast] + xOffset;

            // Scale tam thoi khi di chuyen
            var ratio = - va.xOffset / va.wSlideFull * sign,         // Ti le di chuyen tu 0 -> 1, lam tron so do goc
                ints  = intensity/100,
                iCur  = ints + ((1-ints) * ratio),
                iLast = 1 - ((1-ints) * ratio);


            // Assign transform tam thoi vao slide chinh giua
            var tfCur  = that.tf2(xCur, iCur),
                tfLast = that.tf2(xLast, iLast);

            // Remove transition luc bat dau drag --> thuc hien chi 1 lan --> toi uu tinh toan
            // Remove ca 3 slide chinh giua cung luc --> drag toi drag lui thi 3 slide bi anh huong
            if( is.swipeBegin ) {
                M.tsRemove(slCur);
                M.tsRemove(slLast);

                var slLast2 = $s.eq(idMap[idLast - sign]);
                M.tsRemove(slLast2);
            }
            
            // Assign transform moi tinh toan dc vao slide chinh giua
            slCur.css(tfCur);
            slLast.css(tfLast);
        },



        /**
         * SETUP CAN BANG GIUA CAC SLIDES
         */
        balanceMask : function(a) {
            varibleModule(this);

            // Shortcut varible ba bien khoi tao luc dau
            var sign       = a.s,
                sTranslate = va.can.sTranslate * sign,
                classImage = '.'+ va.ns +'imgback',
                timer      = 'timer',

                // Id slide current va last
                idCur  = va.center.nLeft,
                idLast = idCur - sign,

                // Transition setup --> Loai bo transition khi drag/touch lien tiep
                ts = a.isContinuity ? {} : M.ts(va.cssTf, a.sp, va.ease);


            // Function setup tren imgback moi slide
            var
            fnSetupID = function(id, tf0, tf1, isCur) {

                var $imgback = va.$s.eq(va.idMap[id]).find(classImage),
                    tfBegin  = that.tf1(tf0),
                    tfEnd    = that.tf1(tf1);

                // Loai bo timer va transition
                clearTimeout($imgback.data(timer));
                M.tsRemove($imgback);

                // Tao va update vi tri cua imgback clone
                // that.updateImgClone(id, sign, isCur);


                /* Reset tranform luc dau :
                    + Neu la next slide bang navigation thi reset transform luc dau
                    + Neu co thi transform luc dau --> tranform tiep tuc */
                $imgback.css(va.cssTf) == 'none' && $imgback.css(tfBegin);

                // Add transition vao imgback
                setTimeout(function() { $imgback.css(ts).css(tfEnd) }, 2);

                // Loai bo transition tren imgback --> de kiem soat va code dep hon
                $imgback.data(timer, setTimeout(function() { fnHideID($imgback) }, a.sp));
            },

            fnHideID = function($el) {

                // Loai bo transition va transform
                M.tsRemove($el);
                M.tfRemove($el);

                // Hide wrap clone
                var $imgClone = $el.data('$imgClone');
                $imgClone && $imgClone.css('visibility', 'hidden');
            };

            fnSetupID(idCur, - sTranslate, 0, 1);
            fnSetupID(idLast, 0, sTranslate, 0);


            // Bo sung setup slide lastNext --> loai bo transition va transform
            var idLastNext  = idLast - sign,
                imgLastNext = va.$s.eq(va.idMap[idLastNext]).find(classImage);

            clearTimeout(imgLastNext.data(timer));
            fnHideID(imgLastNext);
        },



        balanceCoverflow : function(a) {
            var that = this;
            varibleModule(that);

            /* Noi dung:
                + Setup z-index slide hai ben giam di 1
                + Setup z-index slide chinh giua tang them 1
                + Di chuyen css origin Canvas --> de transform cho dung
                + Setup transition va transform cho slide giua
                + Xoa transition sau khi di chuyen xong */

            // Shortcut varible
            var sign       = a.s,
                sTranslate = va.can.sTranslate,
                zMap       = va.zMap;

            // Swap z-index cua slide de can bang
            var z = zMap[a.idN] - 1;
            M.shift(zMap, a.is);
            M.push(zMap, z, a.is);


            // Switch slide center
            var idCur  = va.center.nLeft,
                idLast = idCur - sign,

                // Vi tri = vi tri hien co tru khoang cach dinh san va khoang cach Canvas di chuyen
                cover = o.coverflow,
                gap   = (va.wSlideFull - cover.space - sTranslate) * sign,
                zCur  = va.zMap[idLast] + 1,                                // z-index danh rieng cho slide current
                ts    = a.isContinuity ? {} : M.ts(va.cssTf, a.sp, va.ease);      // Loai bo transition khi drag/touch lien tiep
            

            // Setup vi tri transform cua tung slide
            var fnSetupID = function(id, rotate, isCur) {

                var slide = va.$s.eq(va.idMap[id]),
                    x     = va.pBegin[id] - gap,
                    tf;

                // Cap nhat thay doi vi tri slide trong namespace
                va.pBegin[id] = x;

                // Setup transform
                tf = va.tfMap[id] = that.tf1(x, rotate);

                // Setup rieng cho slide current
                if( isCur ) tf['z-index'] = zMap[id] = zCur;

                // Loai bo timer va cap tranform tren slide
                slide.css(ts);
                setTimeout(function() { slide.css(tf) }, 1);

                // Loai bo transition tren slide --> de kiem soat va code dep hon
                clearTimeout( slide.data('timer') );
                slide.data('timer', setTimeout(function() { that.M.tsRemove(slide) }, a.sp));
            };

            fnSetupID(idCur, undefined, true);
            fnSetupID(idLast, cover.rotate * sign, false);



            // Canvas origin transform --> di chuyen huong nguoc lai voi xCanvas
            va.origin += sTranslate * sign;

            // Canvas cap nhat origin transform
            var tf = {};
            tf[va.prefix + 'perspective-origin'] = va.origin +'px 50%';
            va.$canvas.css(tf);
        },


        balanceScale : function(a) {

            // Shortcut varible
            var pBegin = va.pBegin;

            // Switch slide center
            var idCur  = va.center.nLeft,
                idLast = idCur - a.s,
                slCur  = $s.eq(va.idMap[idCur]),
                slLast = $s.eq(va.idMap[idLast]),

                // Vi tri = vi tri hien co tru khoang cach dinh san va khoang cach Canvas di chuyen
                gap   = (va.gapBegin - va.can.sTranslate) * a.s,
                pCur  = pBegin[idCur]  - gap,
                pLast = pBegin[idLast] - gap;


            // Cap nhat thay doi vao doi tuong
            pBegin[idCur]  = pCur;
            pBegin[idLast] = pLast;


            // Tim transform value cua idCur/idLast va luu tru chung
            var tfCur  = va.tfMap[idCur]  = that.tf2(pCur),
                tfLast = va.tfMap[idLast] = that.tf2(pLast, o.scale.intensity/100);


            // Set transition truoc va transform sau len slide --> tao hieu ung chuyen dong
            // Set timer de loai bo transition sau khi chuyen dong ket thuc
            var ts    = a.isContinuity ? {} : M.ts(cssTf, a.sp, va.ease),     // Loai bo transition khi drag/touch lien tiep
                timer = 'timer';

            clearTimeout(slCur.data(timer));
            clearTimeout(slLast.data(timer));

            slCur.css(ts).css(tfCur);
            slLast.css(ts).css(tfLast);

            // Loai bo transition tren slide --> de kiem soat va code dep hon
            slCur.data(timer, setTimeout(function()  { M.tsRemove(slCur) }, a.sp));
            slLast.data(timer, setTimeout(function() { M.tsRemove(slLast) }, a.sp));
        },



        /**
         * PHUC HOI LAI VI TRI VA` TRANSFORM CAC SLIDES SAU KHI DI CHUYEN TAM THOI
         */
        restoreMask : function() {
            var that = this;

            var fnSetupID = function(id) {
                that.POSITION.xAnimate(that.imgbackOfSlide(id), 0, 0, 1);
            };

            // Setup 3 slide o chinh giua quay lai vi tri ban dau
            var idCur = that.va.center.nLeft;
            fnSetupID(idCur);
            fnSetupID(idCur + 1);
            fnSetupID(idCur - 1);
        },


        restoreCoverflow : function() {
            var that = this;
            varibleModule(that);

            // Bien shortcut va khoi tao ban dau 
            var idCur = va.center.nLeft,
                sp    = va.speed[cs.idCur],
                ts    = M.ts(va.cssTf, sp, va.ease);        // Loai bo transition khi drag/touch lien tiep


            // Setup thuoc tinh tren moi slide
            var fnSetupID = function(id) {

                var slide = va.$s.eq(va.idMap[id]),
                    tf    = va.tfMap[id];

                // Loai bo timer va setup transition cho slide
                clearTimeout(slide.data('timer'));
                slide.css(ts).css(tf);

                // Loai bo transition tren slide --> de kiem soat va code dep hon
                slide.data('timer', setTimeout(function() { that.M.tsRemove(slide) }, sp));
            };

            fnSetupID(idCur);
            fnSetupID(idCur + 1);
            fnSetupID(idCur - 1);
        },


        restoreScale : function() { that.restore.coverflow() },



        /**
         * TRA VE IMAGE BACK CUA SLIDE VOI CHI SO ID
         */
        imgbackOfSlide : function(id) {
            var va = this.va;
            return va.$s.eq(va.idMap[id]).data('$imgback');
        },


        /**
         * TOA IMAGE BACK CLONE LUU TRU TRONG DATA IMAGE BACK SLIDE ID
         */
        createImgClone : function(id) {
            varibleModule(this);

            // Bien shortcut va khoi tao
            var $imgback   = that.imgbackOfSlide(id),
                widthImage = M.pInt( $imgback.find('img').css('width') ),
                imgLeft    = - M.r( (widthImage - va.wSlide)/2 ),
                styleClone = {
                    'position'   : 'absolute',
                    'overflow'   : 'hidden',
                    'visibility' : 'hidden',
                    'top'        : 0,
                    'width'      : va.wSlide
                },

                // Copy imgback va wrap bang <div>
                $imgItemClone  = $imgback.find('img').clone().css({ 'position': 'relative', 'left': imgLeft }),
                $imgClone      = $('<div/>').css(styleClone).append($imgItemClone);


            // Luu tru wrap imgback clone vao slide
            $imgback.data({
                '$imgClone'  : $imgClone,
                'isAddClone' : 0,
                'left'       : M.pInt($imgback.css('left'))
            });

            return $imgClone;
        },


        // Update vi tri left cua wrap clone
        updateImgClone : function(id, sign, isCur) {
            var that = this,
                va  = that.va;

            var $imgback  = that.imgbackOfSlide(id),
                $imgClone = $imgback.data('$imgClone');

            // Neu $imgClone chua ton tai --> tao $imgClone
            if( !$imgClone ) $imgClone = that.createImgClone(id);

            var sTranslate = va.can.sTranslate * sign;
            sTranslate = isCur ? sTranslate : - sTranslate;

            // Cap nhap vi tri va toggle show $imgClone
            var left = - $imgback.data('left') + sTranslate;
            $imgClone.css({ 'left': left, 'visibility': 'visible' });


            // Append $imgClone vao page neu khong co trong page
            if( !$imgback.data('isAddClone') ) {

                $imgback.append($imgClone);
                $imgback.data('isAddClone', true);
            }
        }
    };
})(jQuery);