/**
 * MODULE FULLSCREEN
 * ========================================================================== */
(function($) {
    
    // Kiem tra bien module
    if( !window.rt01MODULE ) window.rt01MODULE = {};

    /**
     * MODULE FULLSCREEN
     */
    rt01MODULE.FULLSCREEN = {

        varible : function() {
            var that = this,
                va   = that.va,
                M    = that.M;

            // width/height content: de biet ti le
            va.wContent = va.wCode - (va.pa.left*2);
            va.hContent = M.r(va.wContent / va.rRes);

            // Truong hop: content nho hon page
            if( va.hContent < va.hCode ) {

                va.pa.top = M.r( (va.hCode - va.hContent) / 2 );
            }

            // Truong hop nguoc lai: content lon hon page
            // --> setup hContent = height page, tinh toan lai va.rate va padding
            else {
                va.pa.top = 0;
                va.hContent = va.hCode;
                va.wContent = M.r(va.hContent * va.rRes);

                va.rate = va.wContent / va.wRes;
                va.pa.left = M.r( (va.wCode - va.wContent)/2 );
            }
        }
    };
})(jQuery);