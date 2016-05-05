/**
 * MODULE RESPONSIVE
 * ========================================================================== */
(function($) {
    
    // Kiem tra bien module
    if( !window.rt01MODULE ) window.rt01MODULE = {};

    /**
     * MODULE RESPONSIVE
     */
    rt01MODULE.RESPONSIVE = {

        /**
         * UPDATE CAC GIA TRI CUA RESPONSIVE
         * @param object va.pa
         * @param int va.rate
         */
        updateVars : function() {
            var that = this,
                o    = that.o,
                va   = that.va;


            /**
             * FUNCTION LAY GIA TRI CUA PADDING TRONG DOI TUONG VA.PARANGE
             */
            var fnGetPadding = function() {
                var pa = 0;
                if( !!va.paRange ) {

                    pa = that.M.getValueInRange(va.paRange);
                    if( pa === null ) pa = 0;
                }
                return pa;
            };



            /**
             * RANGE SETUP
             *  + Padding : get
             *  + Padding chi hoat dong khi va.wCode nho? hon Width Responsive
             */
            if( !!o.widthRange ) {

                /**
                 * DIEU KIEN
                 * TH 1: wMax < va.wRes -> uu tien cho Width small trong Range
                 * TH 2: wMax > va.wRes -> uu tien cho Width trong Range
                 */
                var wMax   = va.sizeRange.wMax,
                    isCond = (wMax > va.wRes) ? (wMax >= va.wCode) : (va.wRes > va.wSlide);

                if( isCond ) {
                    // Lay kich thuoc tu ma?ng Range
                    var sizeRange = that.M.getValueInRange(va.sizeRange);

                    // if return === null -> padding get va.paRange
                    // else -> calculator from va.wCode
                    va.pa.left = (sizeRange === null) ? fnGetPadding() : (va.wSlide - sizeRange)/2;
                }
                else va.pa.left = (va.wSlide - va.wRes)/2;
            }

            // Khong co' option 'widthRange'
            else va.pa.left = (va.wRes > va.wSlide) ? fnGetPadding() : (va.wSlide - va.wRes)/2;

            // Lam tron so
            va.pa.left = ~~(va.pa.left);



            /**
             * SETUP OTHERS
             */
            // Vi padding left luon luon co gia tri nen luc nao cung lay dc ratio widthContent / widthResponsive
            var rateCur = (va.wSlide - (va.pa.left * 2)) / va.wRes;
            va.rate = (rateCur > 1) ? 1 : rateCur;
        }
    };
})(jQuery);