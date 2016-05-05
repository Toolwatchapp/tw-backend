/**
 * MODULE NAVIGATION
 * ========================================================================== */
(function($) {
    
    // Kiem tra bien module
    if( !window.rt01MODULE ) window.rt01MODULE = {};

    // Bien toan cuc
    var that, o, cs, va, is,

        /**
         * CAP NHAP BIEN TOAN CUC
         */
        varibleModule = function(self) {
            that = self;
            o    = self.o;
            cs   = self.cs;
            va   = self.va;
            is   = self.is;
        };

    /**
     * MODULE NAVIGATION
     */
    rt01MODULE.NAV = {

        /**
         * RENDER NAVIGATION
         */
        render : function() {
            varibleModule(this);
            var ns = va.ns;


            /**
             * TIM KIEM DOI TUONG NAVIGATION DAU TIEN
             */
            var classes  = '.'+ va.ns + o.nameNav,
                $navHTML = that.RENDER.searchDOM(classes);

            if( $navHTML.length ) {
                va.$nav = $navHTML;
            }
            else {
                // Render Navigation mac. dinh neu khong to`n tai san
                va.$nav = $( o.markup.nav.replace(/\{ns\}/g, ns) );
                // Chen vao Code
                that.RENDER.into(o.markup.navInto, va.$nav);
            }



            /**
             * TIM KIEM CAC THANH PHAN KHAC TRONG NAVIGATION
             */
            var $prev = va.$nav.find('.'+ ns + o.namePrev),
                $next = va.$nav.find('.'+ ns + o.nameNext);

            // Navigation Prev
            if( $prev.length ) va.$prev = $prev;
            else {
                va.$prev = $('<div/>', { 'class': ns + o.namePrev, 'text': 'prev'});
                va.$nav.append(va.$prev);
            }

            // Navigation Next
            if( $next.length ) va.$next = $next;
            else {
                va.$next = $('<div/>', { 'class': ns + o.nameNext, 'text': 'next'});
                va.$nav.append(va.$next);
            }
        },



        /**
         * EVENT TAP-CLICK
         */
        eventTap : function() {
            var that   = this,
                va     = that.va,
                evName = va.ev.click +' '+ va.ev.swipe.end;


            // Dieu kieu de setup event Tap
            if( !va.$nav ) return false;

            // Loai bo event tren Navigation
            va.$prev.add(va.$next).off(evName);

            // Dang ki lai event tren Navigation neu co'
            if( that.is.nav ) {
                va.$prev.on(evName, function(e) { that.EVENTS.prev(); e.preventDefault(); });
                va.$next.on(evName, function(e) { that.EVENTS.next(); e.preventDefault(); });
            }
        },



        /**
         * TOGGLE NAVIGATION NEXT PREV
         */
        toggle : function() {
            varibleModule(this);
            var deactived = va.deactived,
                idCur     = cs.idCur,
                num       = cs.num;

            if( !is.loop ) {
                if( idCur == 0 )       va.$prev.addClass(deactived);
                if( idCur == num - 1 ) va.$next.addClass(deactived);
                
                if( idCur != 0 )       va.$prev.removeClass(deactived);
                if( idCur != num - 1 ) va.$next.removeClass(deactived);
            }

            else va.$prev.add(va.$next).removeClass(deactived);
        }
    };
})(jQuery);