/**
 * MODULE PAGINATION
 * ========================================================================== */
(function($) {

    // Kiem tra bien module
    if( !window.rt01MODULE ) window.rt01MODULE = {};

    // Bien toan cuc
    var that, o, cs, va, is, ti, M,
        varibleModule = function(self) {
            that = self;
            o    = self.o;
            cs   = self.cs;
            va   = self.va;
            is   = self.is;
            ti   = self.ti;
            M    = self.M;
        };

    /**
     * MODULE PAGINATION
     */
    rt01MODULE.PAG = {

        /**
         * RENDER CONTAINER PAGINATION
         */
        renderSelf : function() {
            varibleModule(this);

            // Pagination: search DOM
            var ns       = ' '+ va.ns,
                nsPag    = ns +'pag-',
                pag      = o.pag,
                pagOut   = ns +'outside',
                dirs     = pag.direction,
                pagClass = ns + o.namePag + ns + pag.type + nsPag + dirs + nsPag + pag.position,
                $pagHTML = that.RENDER.searchDOM('.'+ va.ns + o.namePag);

            // Kiem tra va` them vao` Class more vao Pagination luc ban dau
            if( typeof pag.moreClass == 'string' ) pagClass += ' '+ pag.moreClass;

            // Pagination: tao dom voi className --> class type va dirs se duoc update sau
            is.outsidePag = !!$pagHTML.length;
            va.$pag       = $pagHTML.length ? $pagHTML.addClass(pagClass + pagOut)
                                            : $('<div/>', { 'class' : pagClass });
            

            // Them DOM PagInner vao Pagination
            va.$pagInner = $('<div/>', {'class' : va.ns +'paginner'});

            
            // PagItems setup
            // Chen PagItem vao Pagination container
            va.$pagItem = $(''); $thumbItem = $('');
            va.$s.each(function() { that.renderPagItem($(this)) });


            // PagArrow + PagMark setup
            // Chen PagArrow vao pagination container
            if( o.pag.isArrow ) that.renderPagArrow();
            if( o.pag.isMark  ) that.renderPagMark();


            // PagItem: append to pagination, ngoai tru layout dash
            va.$pagInner.append(va.$pagItem);


            // Chen Pagination vao Code
            // Vi tri top --> Pagination append vao vi tri dau tien cua Code
            va.$pag.prepend(va.$pagInner);
            // that.RENDER.overlayGhost(va.$pag);   // Khong can thiet
            if( !$pagHTML.length ) {
                va.$self[ (pag.position == 'begin') ? 'prepend' : 'append' ](va.$pag);
            }



            // Add bien Viewport va namespace va.pag
            va.pag.viewport = va.$pag;



            /**
             * THEM CLASS VAO CODE -> HO TRO TABS STYLE CUSTOM
             */
            var classes = nsPag + pag.type;
            if( !is.pagList ) {

                // Them class chieu huong va vi tri
                classes += nsPag + dirs + nsPag + pag.position;
                if( is.outsidePag ) classes += pagOut;
            }
            // Chen tat ca class vao Code
            va.$self.addClass(classes);
        },

        renderPagItem : function($sl) {
            varibleModule(this);

            // Lay pagItem tu data slide
            var pItem = $sl.data('$pagItem');

            // Thumbnail item: them vao PagItem va luu tru vao $thumbItem de su dung sau nay
            is.pagThumb && that.renderThumbBegin($sl, pItem);

            // PagItem: store in object --> su dung sau nay
            va.$pagItem = va.$pagItem.add(pItem);

            // Usefor add new slide by API.add
            return pItem;
        },

        /**
         * RENDER PAG ARROW
         */
        renderPagArrow : function() {
            varibleModule(this);

            var
            str = "<div class='{ns}pagarrow-item {ns}pagarrow-{dirs}'><div class='{ns}pagarrow-icon'></div></div>";
            str = str.replace(/\{ns\}/g, va.ns);

            va.$pagArrowLeft  = $( str.replace(/\{dirs\}/g, 'left') );
            va.$pagArrowRight = $( str.replace(/\{dirs\}/g, 'right') );
            va.$pag.append(va.$pagArrowLeft, va.$pagArrowRight);
        },

        /**
         * RENDER PAGINATION SIGN CURRENT
         */
        renderPagMark : function() {
            varibleModule(this);
            var str = "<div class='{ns}pagmark'><div class='{ns}pagmark-item'></div></div>";

            va.$pagMark = $(str.replace(/\{ns\}/g, va.ns));
            va.$pagMarkItem = va.$pagMark.children();
            va.$pag.append(va.$pagMark);
        },

        /**
         * SETUP TRUOC KHI RENDER THUMBNAIL : TAO WRAPPER, ICONLOADER
         */
        renderThumbBegin : function($sl, $pItem) {
            varibleModule(this);
            var that   = this,
                slData = $sl.data();

            // Thumbnail tag
            var $thumb = $('<div/>', {'class' : va.ns + o.thumbWrap});
            $pItem.append($thumb);

            // Thumbnail luu tru vao slide
            slData.$thumb = $thumb;

            // Add icon loader vao thumbnail
            that.RENDER.loaderAdd($sl, $pItem, '$thumbLoader');




            /**
             * TIM KIEM THUMBNAIL O NGOAI - DUA TREN [DATA-THUMBNAIL-LINK]
             */
            var $imgback = $sl.find('.'+ va.ns + o.nameImageBack),
                thumbLink;

            if( !!$imgback ) {
                thumbLink = $imgback.data('thumbnailLink');

                // Tiep tuc kiem tra Thumbnail Link co empty string hay khong
                if( /^\s*$/g.test(thumbLink) ) thumbLink = false;
            }




            /**
             * TAO IMAGE THUMBNAIL NEU LINK IMAGE SRC TON TAI
             */
            if( !!thumbLink ) {
                var iThumb = new Image();

                iThumb.onload = function() {
                    varibleModule(that);

                    var $i = $('<img></img>', { 'src' : thumbLink }).data('rate', iThumb.width / iThumb.height);
                    that.renderThumbEnd($i, $thumb, $sl);
                }
                iThumb.onerror = function() {
                    varibleModule(that);
                    M.message('thumbnail load failed', thumbLink);
                }

                // Image thumbnail: set src
                iThumb.src = thumbLink;
            }

            // Neu thumbLink khong ton tai --> luu tru vao Code --> tao thumb bang imgback khi Code bat dau load
            else slData.isThumbByImgback = true;
        },

        /**
         * SETUP RENDER CAC THANH PHAN CON LAI KHI CO THUMBNAIL ITEM
         */
        renderThumbEnd : function($i, $thumb, $sl) {
            var that = this;

            // Setup vi tri Center va Style cho Thumb Item
            that.posCenterForThumbItem($i, $thumb);

            // Thumbnail: append image
            $thumb.append($i);

            // Loai bo thumb loader o giai doan cuoi cung
            that.RENDER.loaderRemove($sl, '$thumbLoader');
        },




        /**
         * FUNCTION TOGGLE CLASS TREN PAGINATION
         *  + Phai kiem tra $pag co ton tai hay khong, boi vi Code bat dau setup, setup prop truoc khi khi PAG.renderSelf
         *  + Class tren pagination nhu the nao --> tren Code cung tuong tu
         */
        toggleClass : function(isAdd) {
            var that = this, oo = that.oo;
            varibleModule(that);


            // DIEU KIEN THUC HIEN FUNCTION
            if( !(va.$pag && !$.isEmptyObject(oo)) ) return;

            // TIEP TUC THUC HIEN FUNCTION
            var opt       = isAdd ? o : oo,
                pag       = opt.pag,
                ns        = ' '+ va.ns,
                nsPag     = ns +'pag-',
                dirsCur   = '',
                classPag  = '',
                classCore = '';


            /* PHAN KIEM TRA */
            // Kiem tra more class them vao
            if( o.pag.moreClass != oo.pag.moreClass ) classPag += ' '+ pag.moreClass;

            // Kiem tra class Type
            if( o.pag.type != oo.pag.type ) classPag += ns + pag.type;

            // Kiem tra class Vi tri
            if( o.pag.position != oo.pag.position ) classCore += nsPag +'pos-'+ pag.position;

            // Kiem tra class Direction
            if( o.pag.direction != oo.pag.direction && pag.direction )
                classCore += nsPag + pag.direction;

            else if( !!va.addInfo )
                classCore += va.addInfo.pagDirs == 'hor' ? (isAdd ? nsPag +'hor' : nsPag +'ver')
                                                         : (isAdd ? nsPag +'ver' : nsPag +'hor');


            /* PHAN ADD CLASS VAO DOI TUONG */
            // Setup class cho pagination
            classPag += ' '+ classCore;
            isAdd ? va.$pag.addClass(classPag) : va.$pag.removeClass(classPag);

            // Setup class cho Code --> ho tro Pag style
            var classCode = classCore;
            if( isAdd ) classCode += nsPag + pag.type;
            else        classCode += '{ns}tabs {ns]thumbnail {ns}bullet {ns}list'.replace(/\{ns\}/g, nsPag);

            va.$self[isAdd ? 'addClass' : 'removeClass'](classCode);
        },

        /**
         * TOGGLE 'FIRST' - 'LAST' CLASS CHO PAG ITEMS
         */
        firstLastClass : function() {
            var va = this.va,
                $pagItem   = va.$pagItem,
                classFirst = va.ns + 'first',
                classLast  = va.ns + 'last';

            if( !!$pagItem ) {
                $pagItem.removeClass(classFirst +' '+ classLast);
                $pagItem.first().addClass(classFirst);
                $pagItem.last().addClass(classLast);
            }
        },




        /**
         * EVENT TAP TREN PAGINATION
         */
        eventTap : function() {
            // Setup bien o day de? giai quyet xung dot trong Event 'on'
            var that = this;
            varibleModule(that);

            // Dieu kien de tiep setup Event Tap
            if( !va.$pag ) return false;


            /**
             * EVENT TAP TREN PAG ITEM
             *  + Event Click : ngan ca?n di chuyen toi Slide moi khi bat dau swipe
             */
            // Truoc tien loai bo event Tap tren PagItem truoc
            var evName = va.ev.click +' '+ va.ev.swipe.end;
            va.$pagItem.off(evName);

            // Dang ki event Tap tren PagItem neu co
            if( is.pag ) {

                // Event luu tru vi tri bat dau Tap begin
                // that.EVENTS.tapBegin(va.$pagItem);

                // Event end cho PagItem
                va.$pagItem.on(evName, function(e) {
                    varibleModule(that);
                    var $item = $(this);

                    // Kiem tra co pha Tap event hay khong
                    // var isTapEnable = that.EVENTS.checkTap($item, e);

                    // Goto slide selected
                    if( is.tapEnable ) {
                        va.moveBy = 'tap';
                        that.TOSLIDE.run( $item.data('id') , true, false, true);

                        // Loai bo setup 2 event Tap cung luc
                        that.EVENTS.delayToTapNext();
                    }

                    // Loai bo touchend hoac mouseup --> chi 1 events dc hanh dong
                    // 'preventDefault' khac voi 'return false'
                    e.preventDefault();
                });
            }



            /**
             * EVENT TAP TREN PAG ARROW
             */
            if( o.pag.isArrow ) {
                var $arrows = va.$pagArrowLeft.add(va.$pagArrowRight);
                $arrows.off(evName);

                // Dang ki event tren Pag Arrow
                if( o.pag.isTapOnArrow ) {
                    $arrows.on(evName, function(e) {
                        varibleModule(that);

                        if( is.tapEnable ) {
                            var dirs = $(this).is(va.$pagArrowLeft) ? 'left' : 'right';

                            // Thay doi vi tri Pag moi ki tap tren arrow
                            that.translatePagByTapArrow(dirs);
                            
                            // Loai bo setup 2 event Tap cung luc
                            that.EVENTS.delayToTapNext();
                        }

                        // Fixed loi IE nen dung preventDefault --> khong su dung 'return false'
                        e.preventDefault();
                    });
                }
            }
        },

        /**
         * TOGGLE EVENT SWIPE TREN PAGINATION TUY THUOC KICH THUOC TONG CONG CUA PAGITEM
         */
        toggleEvent : function() {
            varibleModule(this);
            var isViewLarge = va.pag.isViewLarge;

            /**
             * DANG KI - LOAI BO SWIPE EVENT
             *  + Neu khong co option 'isAutoOnPag' == true --> Khong can setup nua~
             *  + Phu thuo.c vao 'isViewLarge' va` 'is.swipePagCur'
             */
            if( is.SWIPE && !!o.swipe.isAutoOnPag && ((isViewLarge && !!is.swipePagCur) || (!isViewLarge && !is.swipePagCur)) ) {
                var statusSwipeOnPag = isViewLarge ? 'offPag' : 'onPag',

                    // Lay Module Swipe o ben ngoai
                    SWIPE = $.extend({}, rt01MODULE.SWIPE, that);

                // Reset lai event swipe cho Pagination
                SWIPE.events(statusSwipeOnPag);
            }
        },






        /**
         * KICH THUOC WIDTH-HEIGHT CHO PAG ITEM
         */
        typeSizeItem : function() {
            varibleModule(this);

            var op    = o.pag,
                p     = va.pag,
                wfit  = va.ns +'wfit',
                hfit  = va.ns +'hfit',
                isHor = (p.dirs == 'hor'),
                isSizeSelf = is.pagItemSizeSelf;
                

            /**
             * RESET WIDTH-HEIGHT TREN PAG INNER
             *  --> lay dung gia tri width/height cua pagItem
             *  --> toggle class 'wfit' va 'hfit' de lay dung kich thuoc
             */
            var fnResetSizeOnInner = function() {

                va.$pagInner
                    .css({
                        'width'         : '',
                        'height'        : '',
                        'margin-right'  : '',
                        'margin-bottom' : ''
                    })
                    .removeClass(wfit +' '+ hfit);

                // Reset kich thuoc cua Pag Item
                va.$pagItem.each(function() { $(this).css({'width': '', 'height': ''}); });
            },


            /**
             * SETUP KICH THUOC WIDTH-HEIGHT-MARGIN TREN PAG INNER
             *  Khoang cach tong duoc tinh bang margin-right va margin-bottom
             *  --> khong anh huong toi size 100% va vi tri pagination
             *  --> Kiem tra TAB VERTICAL OUTSIDE --> loai bo width tren PagInner
             */
            fnSetupSizeOnInner = function() {
                var wInner = isHor  ? (op.typeSizeItem == 'max' ? p.wMax : p.wMin) : p.wMax,
                    hInner = !isHor ? (op.typeSizeItem == 'max' ? p.hMax : p.hMin) : p.hMax,
                    styles = {
                        'width'  : (is.outsidePag && !isHor) ? '' : wInner,
                        'height' : hInner
                    };

                // Them Margin vao Pag Inner
                // Loai bo width-height tren Pag Inner khi Pag Item o kich thuoc tu do
                if( isHor ) {
                    styles['margin-bottom'] = p.maBottom;
                    if( isSizeSelf ) styles.width = '1px';
                }
                else {
                    styles['margin-right'] = p.maRight;
                    if( isSizeSelf ) styles.height = '1px';
                }

                // Setup style con Pag Inner
                va.$pagInner.css(styles);



                /**
                 * Setup width-height fit cho Pag Item
                 *  + Neu la Item size Self thi tuy theo p.dirs ma` wfit hay hfit
                 *  + Neu Item khong phai size Self thi bat buoc phai co wfit-hfit
                 */
                if( !is.pagList ) {
                    var classes = wfit +' '+ hfit;

                    if( isSizeSelf ) {
                        classes = isHor ? hfit : wfit;
                    }

                    // Setup class wfit-hfit tren Pag Inner
                    va.$pagInner.addClass(classes);
                }
            },


            /**
             * Lay padding va border cua VIEWPORT
             *  --> ho tro pag-tab voi opt SIZEAUTO-FULL
             */
            fnGetSpaceOuterOfViewport = function() {
                var pad     = 'padding-',
                    border  = 'border-',

                    fnSpace = function(aProp) {
                        var sizeView = 0, sizePag  = 0;

                        for( i = aProp.length - 1; i >= 0; i-- ) {
                            sizeView += M.pInt(va.$viewport.css(aProp[i]));
                            sizePag  += M.pInt(va.$pag.css(aProp[i]));
                        }
                        return sizeView - sizePag;
                    };

                va.viewSpace = {
                    'hor': fnSpace([pad +'left', pad +'right', border +'left-width', border +'right-width']),
                    'ver': fnSpace([pad +'top', pad +'bottom', border +'top-width', border +'bottom-width'])
                };
            };



            /**
             * BAT DAU SETUP
             */
            fnResetSizeOnInner();
            that.getSizeOfItems();

            fnSetupSizeOnInner();
            fnGetSpaceOuterOfViewport();
        },

        /**
         * LAY KICH THUOC WIDTH-HEIGHT CUA TUNG ITEM
         */
        getSizeOfItems : function() {
            varibleModule(this);
            var op = o.pag,
                p  = va.pag;

            
            /**
             * LAY GIA TRI PADDING - BORDER - MARGIN CUA PAG ITEM
             */
            var
            fnGetPaBoMaOfItems = function() {
                var cssName = ['padding', 'border', 'margin'],
                    cssDirs = ['top', 'right', 'bottom', 'left'],
                    lenName = cssName.length,
                    lenDirs = cssDirs.length;

                // Truong tien: Reset lai cac gia tri cua Css Name
                for( i = 0; i < lenName; i++ ) {
                    p[cssName[i]] = [[], [], [], []];
                }

                // Vong lap tung doi tuong Pag Item
                va.$pagItem.each(function(index) {
                    var $itemCur = $(this);

                    /**
                     * VONG LAP DE LAY CAC GIA TRI CSS NAME
                     *  + Vong lap thu nhat la CSS Name
                     *  + Vong lap thu hai la CSS Dirs
                     *  + Gia tri sap xep the thu tu. : Padding.Top.IDPagItem
                     */
                    for( i = 0; i < lenName; i++ ) {
                        for( j = 0; j < lenDirs; j++ ) {
                            p[cssName[i]][j][index] = M.pInt( $itemCur.css(cssName[i] +'-'+ cssDirs[j]) );
                        }
                    }
                });
            },

            /**
             * LAY KICH THUOC WIDTH - HEIGHT CUA PAG ITEM
             */
            fnGetSizeOfItems = function(ns) {
                var ns2   = ns == 'w' ? 'width' : 'height',
                    ns3   = ns == 'w' ? 'Width' : 'Height',
                    names = [ns +'Self', ns +'ToPadding', ns + 'ToBorder', ns +'ToMargin'];

                // Reset thuoc tinh luc ban dau
                for( i = 0; i < names.length; i++ ) {
                    p[names[i]] = [];
                }

                // Setup tung item
                va.$pagItem.each(function() {
                    var $itemCur   = $(this),
                        dSelf      = M.r( $itemCur[ns2]() ),

                        // Khoang cach xunh quanh cua Item : Padding, Border, Margin
                        dPadding   = M.r( $itemCur['inner'+ ns3]() - dSelf ),
                        dPadToBor  = M.r( $itemCur['outer'+ ns3]() - dSelf ),
                        dPadToMar  = M.r( $itemCur['outer'+ ns3](true) - dSelf );


                    // SETUP KICH THUOC PAG ITEM KHI CO OPTION : WIDTH, HEIGHT, MINWIDTH, MAXWIDTH...
                    var optsMin = op['min'+ ns3],
                        optsMax = op['max'+ ns3];

                    if( $.isNumeric(op[ns2]) ) dSelf = op[ns2];
                    if( $.isNumeric(optsMin) && dSelf < optsMin ) dSelf = optsMin;
                    if( $.isNumeric(optsMax) && dSelf > optsMax ) dSelf = optsMax;

                    // Push tat ca kich thuoc vao ma?ng
                    // Phan kich thuoc phai cong lai. --> boi vi Kich thuoc self co the thay doi
                    p[names[0]].push(dSelf);
                    p[names[1]].push(dSelf + dPadding);
                    p[names[2]].push(dSelf + dPadToBor);
                    p[names[3]].push(dSelf + dPadToMar);
                });

                
                /**
                 * SETUP KICH THUOC KHAC
                 *  + Kich thuoc Min - Max cua Pag Item
                 *  + Kich thuoc Tong? cong cua tat ca Pag Items
                 */
                p[ns +'Min'] = Math.min.apply(null, p[names[0]]);
                p[ns +'Max'] = Math.max.apply(null, p[names[0]]);
                p[ns +'Sum'] = M.sum(p[names[3]]);
            },

            /**
             * LAY GIA TRI LON NHAT TRONG MA?NG
             */
            fnMaxOfTwoArray = function(arr1, arr2) {
                var maxValue = 0;
                for( i = 0; i < cs.num; i++ ) {

                    var valueCur = arr1[i] - arr2[i];
                    if( valueCur > maxValue ) maxValue = valueCur;
                }
                return maxValue;
            };


            // Bat dau setup
            fnGetPaBoMaOfItems();
            fnGetSizeOfItems('w');
            fnGetSizeOfItems('h');
            
            // Kich thuoc Tong cong cua Pag Items tuy thuoc vao Direction
            p.sSum = (p.dirs == 'hor') ? p.wSum : p.hSum;

            // Gia tri Lon nhat cua Margin cho Pag Inner
            p.maRight  = fnMaxOfTwoArray(p.wToMargin, p.wSelf);
            p.maBottom = fnMaxOfTwoArray(p.hToMargin, p.hSelf);
        },

        // Lay gia tri cac thuoc tinh cua pagination lien quan den kich thuoc/size
        propAndStyle : function() {
            varibleModule(this);
            var that  = this,
                $pag  = va.$pag,
                num   = cs.num,
                p     = va.pag,
                isHor = p.dirs == 'hor';


            /**
             * Chieu dai cua Pagination thay doi theo huong swipeCur
             *  Thay doi theo option sizeAuto [null, 'full', 'self']
             *      + Chuyen doi sizeAuto khi pagination co markup outside
             *      + null : khong setup gi ca
             *      + full : width/height pag == width/height Code
             *      + self : width/height pag = tong cua width/height PagItem cong lai
             */
            var sizeAuto = (is.outsidePag && !isHor) ? 'self' : o.pag.sizeAuto,
                style    = { 'width': '', 'height': '' },
                sViewport;

            if( sizeAuto === null ) {
                sViewport = isHor ? $pag.width() : $pag.height();
            }
            else if( sizeAuto == 'full' ) {
                if( isHor ) sViewport = style.width  = va.wCode + va.viewSpace.hor;
                else        sViewport = style.height = va.hCode + va.viewSpace.ver;
            }
            else if( sizeAuto == 'self' ) {
                if( isHor ) sViewport = style.width  = p.wSum;
                else        sViewport = style.height = p.hSum;
            }

            // Setup size auto len pagination
            p.sViewport = sViewport;
            va.$pag.css(style);

            // Kich thuoc cua Pag --> Nam phia duoi can phai Update Style truoc tien
            p.wViewport = $pag.width();
            p.hViewport = $pag.height();
            p.sTranslate = 0;   // Bien nay khong su du.ng nua trong Pag



            /**
             * SETUP ALIGN JUSTIFY
             *  + Justify: opts sizeAuto la null || full, markup inside va Huong pag la 'hor'
             */
            if( is.alignJustify && !is.outsidePag && sizeAuto != 'self' ) {
                
                // Truong hop Huo'ng Horizontal
                if( isHor ) {

                    // Lay kich thuoc lo'n nhat cua Item
                    var wMaxItem  = Math.max.apply(null, p.wToMargin),
                        wSumItems = wMaxItem * num;

                    // Kich thuoc cua Item tuy thuoc vao Size tong co.ng cua Items voi Size Viewport
                    var wJustify = wMaxItem;
                    if( p.wViewport >= wSumItems || o.pag.isJustifyWhenLarge ) wJustify = ~~(p.wViewport / num);

                    // Update kich thuoc cua Pag Inner
                    var wItem = wJustify - p.maRight;
                    va.$pagInner.css({ 'width': wItem, 'height': p.hSelf[0] });
                }
            }




            /**
             * Setup cac bien khac cua Pag
             *  + Do dai lai con cua va.wCode so voi tong width pagItem --> multi use
             *  + Kiem tra cho phep pagItem co center
             *    --> width Viewport phai lon hon tong width pagItem cong lai
             */
            // Truoc tien cap nhat kich thuoc cua Pag Item
            that.getSizeOfItems();

            // Setup cac bien tiep theo
            var wRemain     = p.sViewport - p.sSum,
                isViewLarge = p.isViewLarge = wRemain >= 0;
            
            

            /**
             * Setup PULL-ALIGN cua Pag
             * PULL se tro ve mac dinh la 'begin' --> neu do dai cua pagination lon hon Viewport
             */
            p.align = o.pag.align;
            if( p.align == 'justify' || (!isViewLarge && p.align != 'begin') ) p.align = 'begin';

            // Tiep tuc setup neu o cac vi tri khac nhau
            if( p.align == 'begin' ) {
                p.xMin = p.xCanvas = 0;
                p.xMax = isViewLarge ? 0 : wRemain;
            }
            else if( p.align == 'end' ) {
                p.xMin = p.xCanvas = wRemain;
                p.xMax = p.sViewport;
            }
            else if( p.align == 'center' ) {
                p.xMin = p.xCanvas = M.r(wRemain / 2);
                p.xMax = p.xMin + p.sSum;
            }



            /**
             * TOGGLE EVENT SWIPE CUA PAGINATION TUY THUOC VAO KICH THUOC CUA PAGITEM
             */
            that.toggleEvent();
        },

        /**
         * VI TRI CUA TUNG ITEM TRONG PHUONG THUC SIZE.sTranslate()
         */
        posAndSizeOfItems : function() {
            varibleModule(this);
            var p     = va.pag,
                isHor = p.dirs == 'hor';


            /**
             * TRUOC TIEN UPDATE LAI KICH THUOC WIDTH-HEIGHT CUA PAG ITEM
             */
            that.getSizeOfItems();


            /**
             * SETUP VI TRI CUA TUNG ITEM DUA THEO HUO"NG TABS
             */
            var nameSize = isHor ? 'wToMargin' : 'hToMargin';
            p.pBegin = [0];

            for( i = 1; i < that.cs.num; i++ ) {
                p.pBegin[i] = p.pBegin[i-1] + p[nameSize][i-1];
            }



            /**
             * VONG LAP DE DI CHUYEN TUNG ITEM THEO VI TRI DA SETUP
             */
            var tl = (isHor ? 'tlx' : 'tly'), tf = {};
            for( i = 0; i < that.cs.num; i++ ) {

                // Setup vi tri
                tf[p.cssTf] = M[tl](p.pBegin[i]);

                // Setup kich thuoc
                if( is.pagItemSizeSelf ) {
                    if( isHor ) tf['width']  = p.wSelf[i];
                    else        tf['height'] = p.hSelf[i];
                }
                va.$pagItem.eq(i).css(tf);
            }
        },





        /**
         * VI TRI VA KICH THUOC CHO THUMBNAIL ITEM
         */
        posCenterForThumbItem : function($imgItem, $thumb) {
            varibleModule(this);

            var ns       = va.ns,
                wPagOpts = o.pag.width,
                hPagOpts = o.pag.height,
                wThumb   = $.isNumeric(wPagOpts) ? wPagOpts : $thumb.width(),
                hThumb   = $.isNumeric(hPagOpts) ? hPagOpts : $thumb.height(),
                rThumb   = wThumb / hThumb,
                rImgItem = $imgItem.data('rate');


            // Setup image thumb o vi tri chinh giua va fill trong wrapper
            // Them class Thumbnail setup fit width/height
            var classAdd = '',
                style    = { 'width': '', 'height': '', 'left': '', 'top': '' };

            if( wThumb && hThumb ) {
                if( rImgItem > rThumb ) {
                    classAdd   = ns +'hfit';
                    style.left = - M.r((rImgItem * hThumb - wThumb) / 2);
                }
                else {
                    classAdd  = ns +'wfit';
                    style.top = - M.r((wThumb / rImgItem - hThumb) / 2);
                }
            }


            // Setup style tren Image Item
            $imgItem.css(style);

            // Toggle class Fit cho Thumbnail
            var classRemove = '{ns}hfit{ns}wfit'.replace(/\{ns\}/g, ns).replace(classAdd, '');
            $thumb.addClass(classAdd).removeClass(classRemove);
        },

        /**
         * CAP NHAT VI TRI CENTER CHO TAT CA THUMBNAIL
         */
        updateThumbnail : function() {
            var that = this;
            varibleModule(that);

            /**
             * DIEU KIEN THUC HIEN
             */
            if( !(is.pagThumb && is.initEnd) ) return;


            /**
             * UPDATE THUMBNAIL TUNG SLIDE
             */ 
            va.$s.each(function() {
                
                // Kiem tra Thumbnail ton tai trong Slide hien tai
                var $thumb = $(this).data('$thumb');
                if( !$thumb ) return;

                // Setup vi tri Center cho Image Item
                // Dong thoi Kiem tra Image Item da Loaded chua
                var $imgItem = $thumb.find('img'),
                    isLoaded = $imgItem.data('isLoaded');

                isLoaded && that.posCenterForThumbItem( $imgItem, $thumb );
            });
        },





        /**
         * SETUP VI TRI CHINH GIU CHO PAG ITEM CURRENT
         */
        posCenterForItemCur : function(isForceTf, isNoAnim) {
            var that = this,
                p    = that.va.pag;
            varibleModule(that);


            /**
             * MAIN FUNCTION
             * Tim kiem vi tri cua Pag Inner
             *  + Vi tri : KhoangCach phia truoc ItemCur - ((KhoangCach tu ItemCur so voi Viewport)/2)
             */
            var fnTranslate = function() {
                varibleModule(that);

                // Truong hop lon hon: Size Viewport > Size PagItems
                // Neu di chuyen bang POSITION.xAnimate thi can kiem tra arrowActived()
                // Pag huong Vertical luc bat dau luon luon co' Animation --> thay tu. nhien hon
                if( p.isViewLarge ) {
                    if( p.dirs == 'ver' ) isNoAnim = false;
                    that.translateTo(p.xCanvas, isForceTf, isNoAnim);
                }

                // Truong hop nho? hon: Size Viewport < Size PagItems
                else {

                    // Vi tri can de'n
                    var disOuter  = (p.dirs == 'hor') ? p.wToMargin : p.hToMargin,
                        disBefore = M.sum(disOuter, cs.idCur),
                        xTarget   = - M.r(disBefore - ((p.sViewport - disOuter[cs.idCur])/2));


                    // Truong hop o ria Viewport thi di chuyen toi ria
                    if     ( xTarget > 0 )      xTarget = 0;
                    else if( xTarget < p.xMax ) xTarget = p.xMax;

                    // Setup translate cho pagination
                    that.translateTo(xTarget);
                }
            };


            /**
             * SETUP TIMER CHO CHUYEN DONG
             * Tabs Ver thi cho` sau khi animate height roi` moi toi translate Pag
             */
            if( p.dirs == 'hor' ) {
                fnTranslate();
            }
            else {
                var timer = 10 + o.speedHeight;
                clearTimeout(ti.centerItemCur);
                ti.centerItemCur = setTimeout(fnTranslate, timer);
            }
        },

        /**
         * DI CHUYEN PAG TOI VI TRI CO DING!!
         *  + Khac vi tri xCanvas thi moi setup --> tiet kiem Memory
         *  + Ho tro PagItem0 o vi tri chinh giua sau khi resize nho dan --> van phuc hoi vi tri PagItem0
         *  + Thiet lap bang tay, khong dung xAnimate() --> Canvas va pagination cung transition
         *  + Ho tro loai bo transition inline tren doi tuo.ng
         *  + Ho tro fallback browser no transition
         */
        translateTo : function(xTarget, isForceTf, isNoAnim) {
            var that = this,
                p    = that.va.pag;
            varibleModule(that);

            // Dieu kien thuc hien function
            if( !(xTarget != p.xCanvas || xTarget == 0 || !!isForceTf) ) return;


            /**
             * SETUP TRANSLATE LEN PAG INNER
             */
            var tf = {},
                sp = o.pag.speed,
                es = o.pag.easing,
                tl = (p.dirs == 'hor') ? 'tlx' : 'tly';

            // Setup transfrom ho tro dirction
            tf[p.cssTf] = M[tl]( M.r(xTarget) );

            // PHAN CO ANIMATION
            if( !isNoAnim ) {

                // Phan browser ho tro css transition
                if( is.ts ) {
                    var ts = M.ts(va.cssTf, sp, M.easeName(es));

                    // Can phai co delay > 1ms (cho trinh duyen detach transition)
                    va.$pagInner.css(ts);
                    // setTimeout(function() { that.va.$pagInner.css(tf) }, 2);
                    va.$pagInner.css(tf);

                    // Loai bo transition --> sach se
                    clearTimeout(ti.pagCenter);
                    ti.pagCenter = setTimeout(function() { varibleModule(that); M.tsRemove(va.$pagInner); }, sp);
                }

                // Setup phan brower khong ho tro css transition
                else {
                    va.$pagInner.animate(tf, {duration: sp, queue: false, easing: es});
                }
            }

            // PHAN KHONG CO ANIMATION
            else va.$pagInner.css(tf);
                


            /**
             * OTHERS SETUP
             *  + Update vi tri xCanvas cua pagination
             *  + Kiem tra Arrow Actived : sau khi update vi tri xCanvas
             *  + Cap nhat vi tri cua Pag Mark
             */
            p.xCanvas = xTarget;
            o.pag.isArrow && that.arrowActived(xTarget);
            o.pag.isMark && that.sizePosOfMark();
        },



        /**
         * THEM MARGIN VAO CODE VIEWPORT --> DE LAY WIDTH VIEWPORT CHINH XAC
         */
        marginOnViewport : function() {
            varibleModule(this);

            // var margin = va.pag.wMax + va.pag.maRight;
            var margin = va.$pag.outerWidth(true);

            va.pagVer == 'begin' && va.$viewport.css('margin-left', margin);
            va.pagVer == 'end'   && va.$viewport.css('margin-right', margin);
        },

        /**
         * TABS VERTICAL CHUYEN SANG HUONG HORIZONTAL - VA PHUC HOI LAI NHU CU
         */
        verToHor : function() {
            varibleModule(this);
            var op   = o.pag,
                p    = va.pag,
                dirs = null;

            // Kiem tra co thay doi huong cua tabs hay khong
            if( is.pagTabs && op.direction == 'ver' ) {

                // Kiem tra co Chuyen doi sang huo'ng Horizontal
                var isMinToHor = M.matchMedia(0, op.widthMinToHor, true);
                // Kiem tra tiep tuc neu ket qua false
                if( !isMinToHor ) isMinToHor = M.matchMedia(0, op.rangeMinToHor);
                
                // Setup tiep tuc
                if( p.dirs == 'ver' && isMinToHor ) {
                    dirs = p.dirs = 'hor';

                    // Clear Height tren pag dom
                    // Ngan can setup height tren pag trong animHeightForCode()
                    !!va.$pag && va.$pag.stop(true).css('height', '');
                }
                else if( p.dirs == 'hor' && !isMinToHor ) {
                    dirs = p.dirs = 'ver';
                }
            }


            // Update Code neu co thay doi huong
            // Loai bo width-inline truoc de lay kich width dung khi update
            if( !!dirs ) {
                va.$canvas.add(va.$pag).css('width', '');
                va.addInfo = { 'pagDirs': dirs };
                cs.update({}, false);
            }
        },

        /**
         * ARROW TOGGLE ACTIVED
         *  @note Setup fn khi co' su thay doi vi tri' pag.xCanvas
         */
        arrowActived : function(xCanvasCur) {
            varibleModule(this);

            var xPlusToShow = 30,
                actived     = va.actived,
                $paLeft     = va.$pagArrowLeft,
                $paRight    = va.$pagArrowRight;


            // Truong hop Viewport nho? hon chieu dai` PagItem cong. lai
            if( !va.pag.isViewLarge ) {

                // Arrow left
                var isClassOnLeft = xCanvasCur < va.pag.xMin - xPlusToShow;
                M.xClass($paLeft, isClassOnLeft, actived);

                // Arrow Right
                var isClassOnRight = xCanvasCur > va.pag.xMax + xPlusToShow;
                M.xClass($paRight, isClassOnRight, actived);
            }

            // Truong hop Viewport lo'n hon
            else $paLeft.add($paRight).removeClass(actived);
        },

        /**
         * DI CHUYEN PAGINATION BOI TAP TREN ARROW
         */
        translatePagByTapArrow : function(dirs) {
            varibleModule(this);
            var p = va.pag;
            
            // Dieu kien de tiep hoat dong function
            if( p.isViewLarge ) return;


            /**
             * TIM KIEM KHOANG CACH CAN DI CHUYEN TREN PAGINATION
             */
            var isLeft = dirs == 'left',
                sign   = isLeft ? 1 : -1,
                // xPlus : Do chenh lech dc cong. vao` xWish
                // --> Nhin thay phan con` lai da~ di chuyen tren pag
                xPlus  = 10,
                xWish  = p.xCanvas + ((p.sViewport - xPlus) * sign),
                xLimit = isLeft ? p.xMin : p.xMax;

            // Setup vi tri can de'n nam trong gio'i han vi tri cho phep
            if( (isLeft && xWish > xLimit ) || (!isLeft && xWish < xLimit) ) {
                xWish = xLimit;
            }

            // Setup translate pagination
            that.translateTo(xWish);
        },

        /**
         * KICH THUOC & VI TRI CUA PAGINATION MARK
         */
        sizePosOfMark : function() {
            varibleModule(this);
            var p     = va.pag,
                xPlus = 0;

            // Dieu kien tiep tuc function
            if( p.margin === undefined ) return;


            /**
             * FUNCTION LAY KICH THUOC CUA PAG MARK
             */
            var
            fnGetSize = function() {
                var isHor    = p.dirs == 'hor',
                    ns       = isHor ? 'w' : 'h',
                    ns2      = isHor ? '3' : '0',
                    sizeTo   = o.pag.sizeMarkTo,
                    idCur    = cs.idCur,
                    styles   = { 'width': '', 'height': '' },

                    margin   = p.margin[ns2][idCur],
                    marToBor = margin   + p.border[ns2][idCur],
                    marToPad = marToBor + p.padding[ns2][idCur],
                    dItemCur;

                // Lay kich thuoc cua Pag Mark tuy thuoc vao opts 'sizeMarkTo'
                if( sizeTo == 'margin' ) {
                    dItemCur = p[ns +'ToMargin'][idCur];
                    xPlus    = 0;
                }
                else if( sizeTo == 'border' ) {
                    dItemCur = p[ns +'ToBorder'][idCur];
                    xPlus    = margin;
                }
                else if( sizeTo == 'padding' ) {
                    dItemCur = p[ns +'ToPadding'][idCur];
                    xPlus    = marToBor;
                }
                else {
                    dItemCur = p[ns +'Self'][idCur];
                    xPlus    = marToPad;
                }

                
                // Dieu kien tiep tuc
                if( dItemCur == p.dMark ) return;

                // Setup kich thuoc len Pag Mark
                styles[isHor ? 'width' : 'height'] = dItemCur;
                va.$pagMarkItem.css(styles);
                p.dMark = dItemCur;
            },


            /**
             * FUNCTION DI CHUYEN VI TRI PAG MARK
             */
            fnTranslate = function() {

                // Tim vi tri cua di chuyen cho Pag Mark
                if( p.pBegin === undefined ) return;
                var xMove = p.xCanvas + p.pBegin[cs.idCur] + xPlus;
                if( xMove == p.xMark ) return;

                // Setup di chuyen vi tri cho Pag Mark
                // Luu tru vi tri cua Pag Mark
                that.POSITION.xTranslate(va.$pagMarkItem, xMove, true, null, p.dirs == 'hor');
                p.xMark = xMove;
            };


            fnGetSize();
            fnTranslate();
        },

        /**
         * DI CHUYEN TAM THOI TREN PAG MARK
         */
        xBufferOnMark : function(pageX) {
            varibleModule(this);
            var p = va.pag;

            // Loai bo Transition cua Pag Mark
            if( is.ts ) {
                var ts = {}; ts[va.cssD] = '0s';
                va.$pagMarkItem.css(ts);
            }

            // Thay doi vi tri cua Pag Mark theo swipe gestures
            var xMove = pageX + p.xMark;
            that.POSITION.xTranslate(va.$pagMarkItem, M.c(xMove), true, null, p.dirs == 'hor');
            p.xMark = xMove;
        }
    };
})(jQuery);