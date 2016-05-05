/**
 * MODULE CAPTION
 * ========================================================================== */
(function($) {

    // Kiem tra bien module
    if( !window.rt01MODULE ) window.rt01MODULE = {};

    /**
     * MODULE CAPTION
     */
    rt01MODULE.CAPTION = {

        /**
         * RENDER CAPTION ELEMENT
         */
        render : function() {

            // Caption: search DOM
            var that     = this,
                o        = that.o,
                va       = that.va,
                divdiv   = '<div/>',

                classes  = '.'+ va.ns + o.nameCap,
                $capHTML = that.RENDER.searchDOM(classes);

            if( $capHTML.length ) va.$cap = $capHTML;
            else                  va.$cap = $(divdiv, {'class' : va.ns + o.nameCap});


            // Them capCur va capLast --> them hieu ung cho caption
            va.$capCur   = $(divdiv, { 'class': va.ns +'cap-cur' });
            va.$capLast  = $(divdiv, { 'class': va.ns +'cap-last' });
            va.$capInner = $(divdiv, { 'class': va.ns +'capinner' });
            va.$capInner.append(va.$capCur, va.$capLast).appendTo(va.$cap);

            // Cap: add to Code
            if( !$capHTML.length ) va.$self.append(va.$cap);
        },


        toggle : function($slCur, $slLast) {
            var o  = this.o,
                va = this.va,
                is = this.is;

            // Bien shortcut va khoi tao ban dau
            var capCur    = $slCur.data('htmlCap'),
                capLast   = $slLast.length ? $slLast.data('htmlCap') : '',

                animEnd   = { duration : o.speedHeight,
                              complete : function() {
                                    var $self = $(this);

                                    if     ( $self.is(va.$capLast) )  $self.css('visibility', '');
                                    else if( $self.is(va.$capInner) ) $self.css('height', '');
                              }};

            // Thay doi noi dung giua caption current
            va.$capCur.html(capCur);


            // Hieu ung khong chay tren mobile --> khong can thiet
            // HIEU UNG FADE giua caption current va last
            // HIEU UNG HEIGHT cho caption
            if( !is.mobile && !is.ie7 ) {

                // NOI DUNG capton Last
                va.$capLast.html(capLast);

                // Lay height cua caption
                var hCur  = va.$capCur.outerHeight(true),
                    hLast = va.$capLast.outerHeight(true) || hCur;      // Fixed luc dau = 0
                    

                // HIEU UNG
                va.$capCur
                    .stop(true)
                    .css('opacity', 0)
                    .animate({ 'opacity' : 1 }, animEnd);

                va.$capLast
                    .stop(true)
                    .css({ 'opacity': 1, 'visibility': 'visible' })
                    .animate({ 'opacity' : 0 }, animEnd);
                
                hCur != hLast &&
                va.$capInner
                    .stop(true)
                    .css('height', hLast)
                    .animate({ 'height' : hCur }, animEnd);
            }
        }
    };
})(jQuery);