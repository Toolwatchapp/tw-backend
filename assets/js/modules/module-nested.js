/**
 * MODULE NESTED
 * ========================================================================== */
(function($) {
    
    // Kiem tra bien module
    if( !window.rt01MODULE ) window.rt01MODULE = {};

    /**
     * MODULE NESTED
     */
    rt01MODULE.NESTED = {

        // Tu dong khoi tao lai Code nested trong 'api add slide'
        autoInit : function($slCur) {

            var $codeNested = $slCur.find('.'+ this.va.ns);
            rt01MODULE.AUTOINIT($codeNested);
        },


        // Loai bo Code nested trong slide hien tai khi su dung api-remove
        destroy : function($slCur) {
            var that = this;

            // Kiem tra co code nested hay khong
            var $nested = $slCur.find('.'+ that.va.ns);
            if( $nested.length ) {

                var nestedData = $nested.data(rt01VA.codeName);
                nestedData = that.M.stringToObject(nestedData);
                $.isPlainObject(nestedData) && !$.isEmptyObject(nestedData) && nestedData.destroy(true);
            }
        },



        /**
         * REFRESH CAC GIA TRI CUA CODE NESTED TRONG SLIDE HIEN TAI
         */
        refreshInSlide : function($slCur) {
            var that = this,
                va   = that.va,
                $codeNested = $slCur.find('.'+ va.ns);


            // Kiem tra trong tung Code Nested neu co
            $codeNested.each(function() {
                var $self = $(this),
                    code  = $self.data(rt01VA.codeName);

                // Chi ap dung cho Code dang hoat do.ng
                if( !!code ) {

                    // Refresh lai Code Nested cho Width hoac Height < 10
                    if( code.one.va.wCode < 10 || code.one.va.hCode < 10 ) code.refresh();
                }
            });
        }
    };
})(jQuery);