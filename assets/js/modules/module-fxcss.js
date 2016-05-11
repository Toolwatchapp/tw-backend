/**
 * MODULE CSS EFFECTS
 * ========================================================================== */
(function($) {

    // Kiem tra bien module
    if( !window.rt01MODULE ) window.rt01MODULE = {};

    /**
     * MODULE HIEU UNG CSS
     */
    rt01MODULE.FXCSS = {

        /**
         * Ket hop voi CssOutIn va CssOne. Setup chia lam 3 phan:
         *  + Toggle class tren idLast va idCurrent --> bao gom ten hieu ung + speed
         *  + Toggle class tren Viewport --> Them class NoClip + ten hieu ung combine (neu co)
         *  + Toggle class tren ID last cua last --> loai bo ten class --> ho tro swap nav lien tiep
         *  + Setup moi thu con lai khi chay xong hieu ung bang css
         */
        setup : function() {

            var that = this,
                cs   = that.cs,
                va   = that.va,
                is   = that.is,
                M    = that.M,
                FX   = that.FX;

            /**
             * SETUP CAC BIEN BAN DAU
             */
            var prefix     = 'ruby',        // Prefix chung cho file 'animate.css'
                idCur      = cs.idCur,
                idLast     = cs.idLast,
                cssAD      = va.cssAD,
                $viewport  = va.$viewport,

                isCssOne   = va.fxType == 'cssOne',
                isCssOutIn = va.fxType == 'cssTwo',
                isCssIn    = va.fxType == 'cssThree',
                classAnim  = ' '+ prefix +'-animated',
                classClip  = va.ns +'noclip',
                dataTimer  = 'tiRemove' + M.properCase(va.fxType),
                dataFxAdd  = 'fxAdded',
                speedCur   = va.speed[idCur],
                speedCSS   = {},
                cssReset   = {},
                easeCur    = va.cssEasing[idCur],
                easeName   = va.prefix +'animation-timing-function',
                fxEasing   = {},

                // Bien va setup danh cho cssOne
                ns         = prefix +'-slide',
                IN         = ns +'In',
                OUT        = ns +'Out';

            // Setup thoi gian hieu ung hoat dong
            speedCSS[cssAD] = speedCur +'ms';

            // Setup easing animation
            fxEasing[easeName] = !!easeCur ? easeCur : '';

            // CSS Reset style khi swap slide ket thuc
            cssReset[cssAD]    = '';
            cssReset[easeName] = '';




            /**
             * Toggle hieu ung bang css tren slide current
             * --> slide last nghich dao va tuong tu
             */
            var fnSlideToggleCSS = function(id, isCur) {

                    var $slide = va.$s.eq(id),
                        fxAdd, fxDel;

                    // Setup class Add va Delete
                    if( isCssOne ) {
                        fxAdd = isCur ? IN : OUT;
                        fxDel = isCur ? OUT : IN;
                    }
                    else {
                        // Ten hieu u'ng Them vao va` Xoa'
                        fxAdd = va.fx[id][isCur ? 0 : 1] || '';
                        fxDel = $slide.data(dataFxAdd) || '';

                        // Kiem tra co phai mang hieu ung hay khong
                        var nameFxLast = 'fxLast'+ (isCur ? 'Out' : 'In');
                        fxAdd = va[nameFxLast] = M.randomInArray(fxAdd, va[nameFxLast]);

                        // Luu tru hieu ung hien tai vao data slide
                        $slide.data(dataFxAdd, fxAdd);
                    }


                    // Loai bo timer remove class cua slide
                    clearTimeout($slide.data(dataTimer));

                    // Them thoi gian chuyen dong vao slide
                    $slide.css(speedCSS);

                    // Toggle class vao idCurrent va idLast
                    $slide.removeClass(fxDel).css(fxEasing).addClass(fxAdd + classAnim);

                    // Loai bo class effect tren slide
                    $slide.data(dataTimer, setTimeout(function() {
                        $slide.removeClass(fxAdd + classAnim).css(cssReset);
                        isCssIn && fnResetAnimateOnSlide($slide);
                    }, speedCur));
                },

                fnResetAnimateOnSlide = function($slCur) {
                    var ts = {};

                    ts[va.cssTf]  = '';
                    ts[va.cssTs]  = 'none';
                    ts[cssAD]     = '';
                    ts['opacity'] = '';

                    $slCur.stop(true);
                    $slCur.css(ts).css(va.cssTs, '');
                },

                fnTranslateLineOnSlide = function($slCur) {

                    var sign        = is.slideNext ? -1 : 1,
                        nameFnReset = va.fxType +'Reset';

                    // Loai bo timer luu tru trong data
                    clearTimeout($slCur.data(dataTimer));

                    // Hien thi SlideLast va loai bo css khong can thiet
                    $slCur.removeClass($slCur.data(dataFxAdd)).addClass(classAnim);

                    // Di chuyen SlideLast voi khoang cach sTranslate
                    that.POSITION.xAnimate($slCur, va.sCode * 1.5 * sign , false, true);
                    $slCur.fadeOut(speedCur, function() { $(this).css('display', ''); });
                    
                    // Loai bo class hien thi va transition
                    $slCur.data(dataTimer, setTimeout(function() {

                        $slCur.removeClass(classAnim);
                        fnResetAnimateOnSlide($slCur);
                    }, speedCur));
                };

            /**
             * SETEUP CHO CAC LOAI HIEU UNG CSS
             */
            if( isCssIn ) {
                var $slLast = va.$s.eq(idLast),
                    $slNext = va.$s.eq(idCur);

                // Setup Slide Prev
                fnResetAnimateOnSlide($slLast);
                fnTranslateLineOnSlide($slLast);

                // Setup slide Next
                fnResetAnimateOnSlide($slNext);
                fnSlideToggleCSS(idCur, is.slideNext);
            }
            else {
                fnSlideToggleCSS(idLast, false);
                fnSlideToggleCSS(idCur, true);
            }




            /**
             * Loai bo class tren Viewport
             *  + Loai bo timer remove class
             *  + Them class noclip de hien hieu ung css khong bi cat
             *  + Setup timer loai bo class no clip
             */
            var fxViewAdd, fxViewDel;
            if( isCssOne ) {
                var fxPrefix  = prefix +'one-',
                    fxCur     = va.fxLast = M.randomInArray(va.fx[idCur], va.fxLast),
                    fxViewAdd = fxPrefix + fxCur,
                    fxViewDel = $viewport.data(dataFxAdd),
                    isNext    = va.nMove > 0,
                    navCur    = isNext ? ns +'Next' : ns +'Prev',
                    navLast   = isNext ? ns +'Prev' : ns +'Next';

                fxViewAdd = classClip +' '+ fxViewAdd +' '+ navCur;
                fxViewDel = fxViewDel +' '+ navLast;

                // Luu tru Hieu ung hien tai vao data Viewport
                va.$viewport.data(dataFxAdd, fxViewAdd);
            }
            else {
                fxViewAdd = classClip;
                fxViewDel = '';
            }

            clearTimeout($viewport.data(dataTimer));
            $viewport.removeClass(fxViewDel).addClass(fxViewAdd);

            $viewport.data(dataTimer, setTimeout(function() {
                $viewport.removeClass(fxViewAdd)
            }, speedCur));



            /**
             * Loai bo hieu ung tren slide last cua last
             * Voi dieu kien phai co idLast2 va phai khac voi idCurrent
             */
            var idLast2 = cs.idLast2;
            if( idLast2 != undefined && idLast2 != idCur ) {

                var $slLast2   = va.$s.eq(idLast2),
                    fxLast2Del = isCssOne ? OUT
                                          : $slLast2.data(dataFxAdd) || '';

                clearTimeout($slLast2.data(dataTimer));
                $slLast2.removeClass(fxLast2Del + classAnim).css(cssReset);

                // Loai css Transform tren Slide Last2
                isCssIn && fnResetAnimateOnSlide($slLast2);
            }



            /**
             * KET THUC SETUP HIEU UNG
             */
            FX.end();
        }
    };
})(jQuery);