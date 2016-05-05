/**
 * MODULE CLASSADD
 * ========================================================================== */
(function($) {
    
    // Kiem tra bien module
    if( !window.rt01MODULE ) window.rt01MODULE = {};

    /**
     * MODULE CLASSADD
     */
    rt01MODULE.CLASSADD = {

        // Kiem tra va luu tru classAdd cua tung slide
        filter : function(opt) {

            var classAdd = '';
            if( opt.classAdd != undefined ) {

                // Dam bao chuyen doi sang chuoi
                classAdd = opt.classAdd.toString();
            }
            return classAdd;
        },


        // Toggle class tren Code khi switch slide
        toggle : function() {
            var va = this.va,
                cs = this.cs;

            var classLast = va.classAdd[cs.idLast],
                classCur  = va.classAdd[cs.idCur];

            // Loai bo class cu va add class moi
            if( classLast != undefined && classLast != '' ) va.$self.removeClass(classLast);
            if( classCur  != undefined && classCur  != '' ) va.$self.addClass(classCur);
        }
    };
})(jQuery);