/**
 * MODULE IFRAME
 * ========================================================================== */
(function($) {

    // Kiem tra bien module
    if( !window.rt01MODULE ) window.rt01MODULE = {};

    // Bien toan cuc
    var that, o, va, is, M,
        varibleModule = function(self) {
            that = self;
            o    = self.o;
            va   = self.va;
            is   = self.is;
            M    = self.M;
        };

    /**
     * MODULE IFRAME LAZY
     */
    rt01MODULE.IFRAME = {

        /**
         * KIEM TRA TON TAI IFRAME LAZY
         */
        checkExist : function($slCur) {
            varibleModule(this);

            /**
             * TIM KIEM IFRAME LINK TRONG SLIDE HIEN TAI
             */
            var selectorIframe = 'a.{ns}iframe'.replace(/\{ns\}/g, va.ns),
                $iframes       = $slCur.find(selectorIframe);

            // Loai bo nhung Videos trong Nested
            var $nested = $slCur.find(va.ns),
                $iframeInNested = $nested.find(selectorIframe);
            $iframes = $iframes.not($iframeInNested);

            // Luu tru Iframe vao data Slide hien tai
            if( $iframes.length ) $slCur.data({ 'isIframe': true, '$iframe': $iframes });
        },


        /**
         * CONVERT LINK TAG SANG IFRAME TAG
         */
        convertTag : function($slCur) {
            varibleModule(this);
            var slData = $slCur.data();

            // Dieu kien thuc hien
            if( !slData.isIframe ) return;



            /**
             * FUNCTION CHUYEN DOI LINK SANG IFRAME
             *  + Copy toan bo thuoc tinh co trong link sang Node div
             */
            var fnLinkToIframe = function($link) {

                /**
                 * TAO DOI TUONG IFRAME VOI THUOC TINH COPY TU DOI TUONG LINK
                 */
                var $iframe = $('<iframe/>');

                // Copy tat ca cac thuoc tinh co tren Link vao` Node Div moi
                var attrs = {};
                $.each($link[0].attributes, function(key, attr) {

                    var nameCur  = attr.name,
                        valueCur = attr.value;

                    $iframe.attr(nameCur, valueCur);
                    attrs[nameCur] = valueCur;
                });


                // Thay doi thuoc tinh 'href' sang 'src'
                if( attrs.href && !/^\s*$/g.test(attrs.href) ) {
                    $iframe.attr('src', attrs.href).removeAttr('href');
                }

                // Thay the node Link bang Iframe
                $link.after($iframe).remove();
            };



            /**
             * SETUP IFRAME TREN SLIDE HIEN TAI
             */
            slData.$iframe.each(function() {
                fnLinkToIframe( $(this) );
            });

            // Loai bo Iframe khoi slide sau khi setup xong
            slData.isIframe = false;
        }
    };
})(jQuery);
