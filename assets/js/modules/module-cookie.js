/**
 * MODULE COOKIE
 * ========================================================================== */
(function($) {
    
    // Kiem tra bien module
    if( !window.rt01MODULE ) window.rt01MODULE = {};

    /**
     * MODULE COOKIE
     */
    rt01MODULE.COOKIE = {

        write : function() {
            var that = this,
                date = new Date(),
                name = rt01VA.codeName + that.va.codeID + that.o.cookie.name;

            // Cong them so ngay luu tru va convert theo gio GTM chuan
            date.setTime( date.getTime() + (that.o.cookie.days * 24 * 60 * 60 * 1000) );
            var expires = '; expires='+ date.toGMTString();

            // Ghi hoac update cookie gia tri moi
            document.cookie = name +'='+ that.cs.idCur + expires +'; path=/';
        },
        

        read : function() {

            var that    = this,
                aCookie = document.cookie.replace(/\s+/g, '').split(';'),
                name    = rt01VA.codeName + that.va.codeID + that.o.cookie.name +'=',
                idCur   = null;

            // Kiem tra tat ca cookie
            for( i = 0; i < aCookie.length; i++ ) {
                if( aCookie[i].indexOf(name) == 0 ) idCur = that.M.pInt( aCookie[i].substr(name.length) );
            }

            // Setup idCur neu cookie co luu tru gia tri trong qua khu
            if( idCur != null ) that.cs.idCur = that.va.idBegin = idCur;
        }
    };
})(jQuery);