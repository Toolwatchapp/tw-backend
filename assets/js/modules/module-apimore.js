/**
 * MODULE API MORE
 * ========================================================================== */
(function($) {
    
    // Kiem tra bien module
    if( !window.rt01MODULE ) window.rt01MODULE = {};

    // Bien toan cuc
    var cs, one, o, va, is, M,

        /**
         * CAP NHAP BIEN TOAN CUC
         */
        varibleModule = function(self) {
            cs    = self;
            one   = self.one;
            o     = self.one.o;
            va    = self.one.va;
            is    = self.one.is;
            M     = self.one.M;
        };


    /**
     * MODULE APIS MORE FUNCTION
     */
    rt01MODULE.APIMORE = {

        // Kiem tra va convert thanh number cho index
        parseIndex : function(index, isAddSlide) {
            var num = this.num;

            // Kiem tra co phai number
            if( /^\-?\d+/g.test(index) ) index = M.pInt(index);

            // Kiem tra index, neu gia tri index khong hop le --> index se la id slide cuoi
            // Slide cuoi cua addSlide khac voi removeSlide
            if( !($.isNumeric(index) && (index >= 0 && index < num)) )
                index = isAddSlide ? num : num-1;

            return index;
        },



        /* Add new slide & remove slide with index
         * Slide va PagItem co cung chung func --> toi uu Code later!!!
        ------------------------------------------------------------------------- */
        fnAddSlide : function(html, index) {
            varibleModule(this);
            var PAG = $.extend({}, rt01MODULE.PAG, one);


            // Kiem tra 'html' co bao gom nguyen SLIDE hay noi dung ben trong SLIDE --> khoi tao $sl du'ng ca'ch
            // 'html' co the la doi tuong jQuery
            var $div   = $(html),
                isWrap = $div.length == 1 && /^(div|article|section)$/ig.test($div[0].tagName),
                $sl    = isWrap ? $div : $('<div/>', { 'html': html });


            // Slide setup markup, va return slide da setup
            // va setup PagItem trong slide
            // Convert chi so index thanh number
            $sl   = one.RENDER.slide($sl);
            index = cs.parseIndex(index, 1);



            // SLIDE SETUP: append to code with index
            var isIDEnd = index == cs.num;
            if( isIDEnd ) { va.$canvas.append($sl) }
            else {
                // Them slide moi vao phia truoc slide index
                va.$s.eq(index).before($sl);

                // Varible $s reset thu tu
                va.$s = va.$canvas.children('.'+ va.ns + o.nameSlide);
            }



            // PAGITEM SETUP
            if( is.pag ) {

                // Lay noi dung ben trong cua capitem va PagItem
                one.RENDER.capPagHTML($sl);

                // Them PagItem vao pagination
                var pagAdd = PAG.renderPagItem($sl);

                // Add PagItem vao pagination
                if( isIDEnd ) va.$pagInner.append(pagAdd);
                else {
                    // Mac dinh them PagItem moi phia truoc PagItem index
                    va.$pagItem.eq(index).before(pagAdd);

                    // Varible va.$pagItem reset thu tuong
                    va.$pagItem = va.$pagInner.children('.'+ va.ns +'PagItem');
                }

                // Them event click vao PagItem
                PAG.eventTap();
            }


            // ID toggle class actived --> Ho tro khi index trung voi idCur
            if( index == cs.idCur ) cs.idLast = cs.idCur + 1;


            // Kiem tra co phai load Nearby hay khong
            // Load nearby khong can chay. fx can thiet --> tam dung load
            if( o.load.isLazy ) cs.refresh();

            // Load binh thuong
            else {

                // Properties Code & slide: resetup
                is.apiAdd = true;           // De cac func khac biet update Code bang apiAdd
                one.PROP.code();            // Setup prop truoc --> trong khi load image
                one.PROP.slides();

                // Cuoi cung LOAD begin
                $sl.data('loadBy', 'apiAdd');
                one.LOAD.slideBegin($sl);
            }


            // Kiem tra 'code nested' --> khoi tao lai code trong slide
            var NESTED = $.extend({}, rt01MODULE.NESTED, one);
            is.NESTED && NESTED.autoInit($sl);
        },

        getFromURL : function(url, index) {
            var one = this.one;

            // Bien khoi tao ban dau
            var settings = {
                    type    : 'GET',
                    cache   : false,
                    crossDomain : true,
                    success : function(data) { one.fnAddSlide(data, index); },
                    error   : function()     { one.M.message('ajax load failed', url); }
                };

            // Setup ajax
            $.ajax(url, settings);
        },

        addSlide : function(obj, index) {
            varibleModule(this);

            // Dieu kien add: phai co it nhat 1 slide
            if( cs.num > 0 ) {

                // Function class Chen` Slide core
                var fnAddSlide = function(html) { cs.fnAddSlide(html, index); };


                // Neu 'obj' la string --> chuyen doi qua jquery selector hoac tai ajax
                if( typeof obj == 'string' && obj != '' ) fnAddSlide(obj);

                // Neu 'obj' la doi tuong {}
                else if( typeof obj == 'object' ) {
                    
                    // Add slide bang ajax --> load ajax truoc
                    if( obj.ajax != undefined && typeof obj.ajax == 'string' ) cs.getFromURL(obj.ajax, index);

                    // Add slide bang doi tuong jquery
                    else if( obj instanceof jQuery ) fnAddSlide(obj);
                    
                    // Add slide bang html
                    else if( obj.html != undefined && typeof obj.html == 'string' ) fnAddSlide($(html));
                }
            }
        },


        removeSlide : function(aIndex) {
            varibleModule(this);

            // Dieu kien remove: phai co it nhat 2 slide
            if( cs.num > 1 ) {

                // Bien de cac func khac nhan biet loai bo slide
                is.apiRemove = true;

                // Neu la` xoa slide khong co' index --> Tu dong chuyen sang ma?ng
                if( !$.isArray(aIndex) ) {
                    // Chuyen thanh ma?ng co 1 value
                    aIndex = [aIndex];
                    // Setup idCur: idCur cuoi, remove se lay bot --> idCur chuyen sang id phia truoc
                    if( cs.idCur == cs.num-1 ) {
                        cs.ev.trigger('beforeSwapIDCur');
                        cs.idCur = cs.num-2;
                        cs.ev.trigger('afterSwapIDCur');
                    }
                }
                // Neu aIndex la ma?ng
                else {
                    // Chuyen idCur sang slide dau tien
                    cs.ev.trigger('beforeSwapIDCur');
                    cs.idLast = cs.idCur = 0;
                    cs.ev.trigger('afterSwapIDCur');
                    // Toggle slide --> loai bo actived tren slide hien tai
                    M.toggleSlide();
                }
                

                // Setup tat ca? cac id cung luc
                for( var i = 0, len = aIndex.length; i < len; i++ ) {
                    // Convert index thanh number
                    var index  = cs.parseIndex(aIndex[i], 0),
                        $slCur = va.$s.eq(index);

                    // Kiem tra code nested co trong slide hay khong --> remove event resize truoc
                    var NESTED = $.extend({}, rt01MODULE.NESTED, one);
                    is.NESTED && NESTED.destroy($slCur);

                    // Remove slide from Code va setup lai var $s
                    $slCur.remove();
                    
                    // Remove PagItem form pagination va setup lai var $pagItem
                    if( is.pag ) va.$pagItem.eq(index).remove();
                }


                // Reset lai bie'n slide va` pagItem
                va.$s = va.$canvas.children('.'+ va.ns + o.nameSlide);
                if( is.pag ) va.$pagItem = va.$pag.find('.'+ va.ns +'pagitem');

                // Lam moi cac thuoc tinh khac trong Code
                cs.refresh();
                is.apiRemove = false;
            }
        },




        /* Sap xep lai thu tu cac slide
        ------------------------------------------------------------------------- */
        orderSlide : function(inOrder) {
            varibleModule(this);
            var num          = cs.num,
                isRightOrder = true;    // Bien nhan biet thu' tu. sa'p xep' hop le.

            
            /**
             * SETUP NEU "INORDER" LA OBJECT {}
             */
            if( num > 1 && $.isPlainObject(inOrder)) {

                // Bien khoi tao va shortcut ban dau
                var aCheck = [],

                    // Function kiem tra so' co hop le. hay khong
                    fnCheckNum = function(n) {
                        return (/^\d+/g.test(n) && M.pInt(n) >= 0 && M.pInt(n) < num);
                    },
                    
                    // Function chuyen doi gia tri trong array
                    fnSwapValueInAarray = function(arr, pOld, pNew) {
                        var temp = arr[pNew];
                        arr[pNew] = arr[pOld];
                        arr[pOld] = temp;
                    };


                // BUOC 1: Tao ma?ng vi tri ban dau cua slide co' thu tu. tang dan
                for( var i = 0; i < num; i++ ) { aCheck.push(i) }

                // BUOC 2: Vong lap lay tung key item trong object
                for( var key in inOrder ) {
                    var val = inOrder[key], pOld, pNew;
                    if( fnCheckNum(key) && fnCheckNum(val) ) {

                        // Tim kie'm vi tri cua key --> chuyen doi vi tri cu~ va moi'
                        pOld = $.inArray(M.pInt(key), aCheck);
                        fnSwapValueInAarray(aCheck, pOld, M.pInt(val));
                    }
                }

                // BUOC 3: Chuyen object thanh ma?ng
                inOrder = aCheck;
            }


            /**
             * SETUP NEU "INORDER" LA ARRAY []
             */
            else if( num > 1 && $.isArray(inOrder) && inOrder.length == num ) {
                var aCheck = $.extend([], inOrder);

                for( var i = 0, poped; i < num; i++ ) {
                    poped = aCheck.pop();
                    if( !$.isNumeric(poped) || $.inArray(poped, aCheck) != -1 || poped < 0 || poped >= num ) {
                        isRightOrder = false;
                        break;
                    }
                }
            }
            else isRightOrder = false;



            // Kiem tra truoc khi sap xep
            if( isRightOrder ) {

                // Truoc tien: loai bo toan bo slide
                va.$s.detach();
                is.pag && va.$pagItem.detach();

                // Sau do: Chen cac slide theo thu tu moi
                for( var i = 0; i < num; i++ ) {
                    var idSwap = inOrder[i];
                    va.$canvas.append(va.$s.eq(idSwap));
                    is.pag && va.$pagInner.append(va.$pagItem.eq(idSwap));
                }

                // Reset lai doi tuo.ng SLIDE
                va.$s = va.$canvas.children('.'+ va.ns + o.nameSlide);
                // Reset lai doi tuong va them event vao PAGITEM
                if( is.pag ) va.$pagItem = va.$pag.find('.'+ va.ns +'pagitem');
                // Reset lai id current
                cs.ev.trigger('beforeSwapIDCur');
                cs.idCur = $.inArray(cs.idCur, inOrder);
                cs.ev.trigger('afterSwapIDCur');

                // Reset va update lai ca'c bien va thuoc tinh
                cs.prop();
            }

            // Thong bao loi neu ma?ng khong du'ng
            else M.message('array or object order not right');
        },




        /* Dang ki va loai bo swipe event
        ------------------------------------------------------------------------- */
        swipeEvent : function(status) {
            var that = this;

               typeof status == 'string'
            && ('onBody onPag offBody offPag').indexOf(status) != -1
            && that.is.SWIPE && that.SWIPE.events(status);
        },





        /* Sap' xep lai slide bang phuong phap menu-sortable
        ------------------------------------------------------------------------- */
        sortableBegin : function() {
            varibleModule(this);
            var ns = ' '+ va.ns;


            // Loai bo het cac' event tren PagItem
            cs.destroy();

            // Them class moi vao` PagItem
            va.$pag.addClass(ns +'sortable');

            // Loai bo vi tri cua PagItem
            var $pagInner = va.$pagInner,
                wPag      = $pagInner.width(),
                hPag      = $pagInner.height();

            $pagInner.removeClass(ns +'hfit'+ ns +'wfit').css({'width': '', 'height': ''});
            va.$pagItem.removeAttr('style').css({'width': wPag, 'height': hPag});
        },

        sortableEnd : function() {
        },

        sortable : function(status) {
            varibleModule(this);

            // API chi hoat dong khi co' PAGINATION
            if( o.isPag ) {
                if     ( status == 'begin' ) cs.sortableBegin();
                else if( status == 'end' )   cs.sortableEnd();
            }
        }
    };
})(jQuery);