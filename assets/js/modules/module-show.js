/**
 * MODULE HIDE-SHOW
 * ========================================================================== */
(function($) {
    
    // Kiem tra bien module
    if( !window.rt01MODULE ) window.rt01MODULE = {};

    // Bien toan cuc
    var that, o, va, is,
        varibleModule = function(self) {
            that = self;
            o    = self.o;
            va   = self.va;
            is   = self.is;
        };


    /**
     * MODULE SHOW - HIDE CODE
     */
    rt01MODULE.SHOW = {

        /**
         * SETUP SHOW-HIDE TRONG INIT CODE
         *  + Kiem tra Code co o che do sleep(option 'show', 'showInRange') hay khong
         */
        setupInit : function() {
            varibleModule(this);

            /**
             * SETUP SHOW-HIDE TREN THIET BI 'DESKTOP' & 'MOBILE'
             */
            var isShowCode = true;
            if( (is.mobile && o.showBy == 'desktop')
            ||  (!is.mobile && o.showBy == 'mobile') ) isShowCode = false;

            if( isShowCode ) {

                /**
                 * TIEP TUC SETUP OPTION SHOW-FROM
                 */
                that.setupVars();
                that.check();

                // Chuyen sang INIT.ready hoac dang ki event resize
                is.awake ? that.INIT.ready() : that.resizeON();
            }

            // Loai bo Code neu thiet bi hien thi khong du'ng
            else va.$self.remove();
        },



        /**
         * SETUP CAC BIEN CUA SHOW-HIDE
         * @param array va.showInRange
         * @param boolean is.showCode
         */
        setupVars : function() {
            varibleModule(this);
            var M = that.M;


            /**
             * SETUP BIEN SHOWFROM
             */
            if( !!o.showInRange ) {

                /**
                 * FUNCTION CHUYEN DOI BIEN THANH DOI TUONG RANGE
                 * @return object chain
                 */
                var fnChain2 = function(val) {
                    
                    if( $.isNumeric(val) )            val = [[val, 100000]];
                    else if( M.elesIsNumber(val, 2) ) val = [val];

                    // Kiem tra gia tri co phai Array hay khong de tiep setup
                    if( !$.isArray(val) ) return false;


                    var chain = { num : val.length };
                    for( i = chain.num-1; i >= 0; i-- ) {
                        var a = val[i];

                        // Bo sung gia tri cua con thieu
                        if( $.isNumeric(a) ) a = [a, 100000];

                        // Bien doi gia tri cua bie'n thanh cac thanh phan khac cua 'chain'
                        chain[i] = { 'from': M.pInt(a[0]), 'to': M.pInt(a[1]) };
                    }
                    return chain;
                };

                // Setup bien 'showInRange' thanh doi tuo.ng range
                va.showInRange = fnChain2(o.showInRange);
            }

            // Default setup: if no showInRange value
            else {
                is.showInRange = is.awake = true;
            }
        },




        /**
         * KIEM TRA CODE CO HIEN THI TRONG KICH THUOC WINDOW HIEN TAI
         * @param boolean is.showInRange
         * @param boolean is.wake
         */
        check : function() {
            varibleModule(this);
            var range = va.showInRange;


            /**
             * SETUP BIEN 'is.showInRange'
             */
            if( $.isPlainObject(va.showInRange) ) {
                is.showInRange = false;

                // Kiem tra trong ma?ng va.showInRange
                for( i = range.num-1; i >= 0; i-- ) {
                    if( that.M.matchMedia(range[i].from, range[i].to) ) {
                        is.showInRange = true;
                        break;
                    }
                }
            }


            /**
             * SETUP BIEN 'is.awake'
             *  + Code is sleep --> Code not init, not setup
             */
            if( is.awake === undefined && is.showInRange ) is.awake = true;
        },




        /**
         * TOGGLE CLASS 'NONE' TREN CODE
         */
        toggle : function() {
            varibleModule(this);

            // Show: check
            that.check();

            // Toggle class 'none' tren code
            var hide = va.ns +'none';
            va.$self[(is.showInRange ? 'remove' : 'add') + 'Class'](hide);
        },




        /**
         * EVENT RESIZE
         */
        resizeON : function() {
            var that = this,
                va   = that.va,
                is   = that.is,
                ti   = that.ti;

            va.$self.addClass(va.ns +'none');
            $(window).on('resize.codeShow'+ va.codekey, function() {

                clearTimeout(ti.showResize);
                ti.showResize = setTimeout(function() {

                    that.check();
                    is.awake && that.resizeOFF();
                }, 200);
            });
        },

        resizeOFF : function() {
            varibleModule(this);

            $(window).off('resize.codeShow'+ va.codekey);
            va.$self.removeClass(va.ns +'none');

            // Init ready when Code awake
            that.INIT.ready();
        }
    };
})(jQuery);