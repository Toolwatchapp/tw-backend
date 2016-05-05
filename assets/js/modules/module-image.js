/**
 * MODULE IMAGE
 * ========================================================================== */
(function($) {
    
    // Kiem tra bien module
    if( !window.rt01MODULE ) window.rt01MODULE = {};

    // Bien toan cuc
    var that, o, cs, va, is, ti, M, PAG,

        /**
         * CAP NHAP BIEN TOAN CUC
         */
        varibleModule = function(self) {
            that = self;
            o    = self.o;
            cs   = self.cs;
            va   = self.va;
            is   = self.is;
            ti   = self.ti;
            M    = self.M;
            PAG  = $.extend({}, rt01MODULE.PAG, self);
        };


    /**
     * MODULE IMAGE
     */
    rt01MODULE.IMAGE = {

        /**
         * SETUP ALL IMAGES EACH SLIDE
         */
        setupEachSlide : function(dataCur) {
            varibleModule(this);
            var ns            = va.ns,
                $slide        = dataCur.$slide,
                isHaveImgback = false;

            // Setup tat ca ca'c image trong slide
            dataCur.$images.each(function() {
                var $i = $(this);


                /**
                 * CHUYEN DOI TAG CUA LINK THANH TAG IMG
                 */
                if( /a/i.test(this.tagName) ) $i = that.linkToImage($i);


                /**
                 * KIEM TRA IMAGE LAZY VA IMAGE BACK TRONG CODE
                 *  + Ho tro chi duoc phep 1 Image back trong moi~ Slide
                 *  + Image se duoc ho tro ti'nh nang Lazyload va Responsive
                 */
                var isImgback   = !isHaveImgback && $i.hasClass(ns + o.nameImageBack),
                    isImgOfCode = isImgback || $i.hasClass(ns + o.nameImageLazy);


                /**
                 * NHUNG THUOC TINH CO BAN DUOC LUU TRU TREN IMAGE DATA
                 */
                $i.data({
                    '$slide'       : $slide,
                    'slideID'      : dataCur.id,
                    'isImgOfCode'  : isImgOfCode,
                    'isImgback'    : isImgback,
                    'isSrcOutside' : false,
                    'isLoaded'     : false,
                    'src'          : []
                });



                /**
                 * SETUP NHUNG THUOC TINH CHI CO TRONG IMAGE BACK
                 */
                if( isImgback  ) {
                    isHaveImgback = true;

                    // Wrap Image Item to Div.imgback
                    that.wrap($i);

                    // Store object image background
                    $slide.data({
                        '$imgback'     : $slide.find('.'+ va.ns +'imgback'),
                        '$imgbackItem' : $i,
                        'isImgback'    : true
                    });
                }



                /**
                 * SETUP OTHERS
                 */
                // Src image setup, cho vao mang theo thu tu uu tien --> roi get tu` dau
                // Loai bo chuoi bat dau 'data:image/' --> boi vi do Code tu them vao, xung dot voi load image
                var dataSRC     = $i.data('src'),
                    srcSelf     = $i.attr('src'),
                    isSrcInline = /^data\:image\//g.test(srcSelf);
                !isSrcInline && dataSRC.push(srcSelf);


                var srcLazy = $i.attr('data-'+ o.nameDataLazy);
                if( srcLazy != undefined ) {
                    dataSRC.push(srcLazy);
                    $i.removeAttr('data-'+ o.nameDataLazy);
                }


                // Image: check data image && setup data image
                that.getData($i);


                // Image kiem tra src co o ngoai (server flickr)
                // Neu la srcOutside thi cho cho load xml xong, neu khong thi chay thang IMAGE.load
                var iData = $i.data();
                // if( iData.flickr && iData.flickr.photoID ) flickr.photo($i);
                // else                                       that.load($i);
                // that.load($i);
                that.loadEachImage($i);
            });
        },

        /**
         * EVENT LOAD TUNG IMAGE
         *  + Chu y: Khong the chuyen sang module.IMAGE dc --> bie'n trong event 'onload' khong chi'nh xac nua~
         */
        loadEachImage : function($i) {
            var that = this;
            varibleModule(that);

            /**
             * FUNCTION SETUP SAU KHI IMAGE LOADED
             */
            var iData  = $i.data(),
                $slCur = iData.$slide,
                slData = $slCur.data();

            var fnSetupAfterLoaded = function() {

                // Kiem tra da load het image --> neu load het --> setup slideEnd
                slData.nCur = slData.nCur + 1;
                if( slData.nCur == slData.imageNum ) {
                    setTimeout(function() { that.LOAD.slideEnd($slCur) }, 10);
                }


                /* THEM THUMBNAIL */
                // Kiem tra xem co add thumbnail bang imgback hay khong
                if( is.pag && slData.isThumbByImgback && iData.isImgback ) {

                    // Clone imgback voi thuoc tinh --> add vao pagination
                    PAG.renderThumbEnd($i.clone(true), slData.$thumb, $slCur);
                }
            };



            /**
             * KIEM TRA SRC CUA IMAGE DA LOAD THANH CONG
             */
            var imageNew = new Image(),
                dataSRC  = iData.src,
                srcCur   = dataSRC.pop();

            // EVENT IMAGE LOAD THANH CONG
            imageNew.onload = function() {
                varibleModule(that);

                // Image: set properties
                // Truyen agrument bang image DOM --> nhanh va lay size Width/Height chinh xac hon jquery selector
                that.prop($i, this);

                // Image: all image loaded
                fnSetupAfterLoaded();
            };

            // EVENT IMAGE LOAD THAT BAI
            imageNew.onerror = function() {
                varibleModule(that);

                // Neu src trong mang con value --> load tiep tuc src con lai trong mang
                if( dataSRC.length ) that.loadEachImage($i);

                // Neu mang src empty --> bao loi khong load duoc
                else {

                    // Image: change alt
                    $i.attr('alt', '[ image load failed ]');
                    M.message('image load failed', srcCur);

                    // Image: all image loaded
                    fnSetupAfterLoaded();
                }
            };

            // Image src: get, o duoi function i.onload --> fixed bug for IE
            // Lay src trong data --> lay theo thu tu uu tien.
            $i.attr('src', srcCur);
            imageNew.src = srcCur;
        },

        /**
         * LAY 'DATA-IMAGE' TREN IMAGE --> HO TRO 'FLICKR'
         */
        getData : function($i) {

            var optsImage = $i.data('image');
            if( $.isPlainObject(optsImage) ) {

                // Luu tru option Image tren data
                // Xoa data attribute tren Image
                $i.data(optsImage).removeAttr('data-image');
            }
        },

        /**
         * SETUP CAC THUOC TINH CUA IMAGE SAU KHI LOAD XONG
         */
        prop : function($i, i) {
            varibleModule(this);

            /**
             * LUU TRU KICH THUOC CUA IMAGE TREN DATA
             */
            var iData  = $i.data(),
                wImage = i.width,
                hImage = i.height;

            $i.data({
                'isLoaded' : true,
                'width'    : wImage,
                'height'   : hImage,
                'rate'     : wImage / hImage
            });


            /**
             * LUU TRU IMAGE HIEN TAI VAO NHO'M IMAGE TRONG SLIDE
             */
            var slData = iData.$slide.data();
            slData.$images = slData.$images.add($i);


            /**
             * SETUP KICH THUOC CHO IMAGE THEO TY LE
             */
            iData.isImgOfCode && that.itemSize($i);


            /**
             * LOAI BO HANH DONG EVENT DRAG TREN IMAGE BACK
             */
            iData.isImgback && $i.on(va.ev.drag, function(e) { return false });
        },



        /**
         * CHUYEN DOI TAG 'A' SANG TAG 'IMG'
         * Sao chep toan do data, alt, id cua link 'a'
         * Video: wrap by div, (div > img)
         */
        linkToImage : function($a) {
            varibleModule(this);

            /**
             * TOA IMAGE MOI VOI CAC THUOC TINH MAC DINH
             */
            var attrs     = {},
                imgGif    = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7',
                imgAlt    = o.isCap ? 'image link' : $a.text(),
                $imageNew = $('<img>', { 'src': imgGif, 'alt': imgAlt });

            // Copy tat ca cac thuoc tinh co tren Link vao` Node Image moi
            $.each($a[0].attributes, function(key, attr) {
                var nameCur  = attr.name,
                    valueCur = attr.value;

                $imageNew.attr(nameCur, valueCur);
                attrs[nameCur] = valueCur;
            });

            // Them Data Lazy vao Node Image moi
            // Dong thoi loai bo thuoc tinh 'href' tren Image moi
            $imageNew
                .attr( 'data-'+ o.nameDataLazy, (attrs.href ? attrs.href : '') )
                .removeAttr('href');



            /**
             * SETUP OTHERS
             */
            // IE fixed: loai bo thuoc tinh width-height tren Dom
            is.ie && $imageNew.removeAttr('width height');

            // Thay the doi tuong trong DOM
            $a.after($imageNew).remove();
            return $imageNew;
        },

        /**
         * WRAP IMAGEBACK ITEM BOI TAG 'DIV'
         */
        wrap : function($imgItem) {

            // Tao Node 'div' moi va` wrap Image Item
            // Chuyen thuoc tinh 'Class' cua Item sang Wrap
            var classImgback = this.va.ns + o.nameImageBack;
                $imgWrap     = $('<div/>', { 'class': $imgItem.attr('class') });

            // Wrap Image Item bang Node Image Wrap va` refresh lai. bien' Image Wrap
            $imgItem.wrap($imgWrap).removeAttr('class');
            $imgWrap = $imgItem.closest('.'+ classImgback);


            /**
             * COPY CAC THUOC TINH DATA VIDEO SANG NODE IMAGE WRAP
             */
            var attrName = ['data-video', 'data-video-link'];
            for( var i = 0, len = attrName.length; i < len; i++ ) {

                // Lay thuoc tinh hien tai
                var attrCur = $imgItem.attr( attrName[i] );

                // Neu thuoc tinh data ton tai tren Dom thi copy sang Image Wrap
                // Dong thoi loai bo? thuoc tinh tren Image Item
                if( !!attrCur ) {
                    $imgWrap.attr(attrName[i], attrCur);
                    $imgItem.removeAttr(attrName[i]);
                }
            }
        },




        /**
         * CAP NHAT KICH THUOC HOAC VI TRI CUA IMAGES
         */
        updateAllImagesBy : function(typeUpdate) {
            var that = this;
            varibleModule(that);

            /**
             * PHAN BIET TEN CUA IMAGE TRONG DATA SLIDE VA TEN FUNCTION CAN THUOC CAT NHAT
             */
            // Cap nhat kich thuoc cua tat ca Image trong Code
            var nameImage, nameFunction;
            if( typeUpdate == 'size' ) {
                nameImage    = '$images';
                nameFunction = 'itemSize';
            }
            // Cap nhat vi tri cua Image back
            else {
                nameImage    = '$imgbackItem';
                nameFunction = 'backPosition';
            }


            /**
             * SETUP KICH THUOC HOAC VI TRI CUA IMAGES
             */
            va.$s.each(function() {

                // Lay Images can setup trong Slide hien tai
                var $images = $(this).data(nameImage);

                // Cap nhat kich thuoc hoac vi tri cua Image
                !!$images && $images.each(function() {
                    that[nameFunction]($(this));
                });
            });
        },

        /**
         * UPDATE KICH THUOC CUA IMAGE ITEM
         *  + Setup kich thuoc truoc tien de lay Chieu cao cua tung Slide
         *  + Luon luon de kich thuoc tren Image Item --> ho tro lay kich thuoc chinh xac tren IE
         */
        itemSize : function($imgItem) {
            varibleModule(this);
            var iData = $imgItem.data(),

                // Reset style css cho Image Item
                style = { 'width': '', 'height': '', 'left': '', 'top':'' },
                // Xac dinh Loai vi tri cua Image back
                typePosition = va.imgPos[ iData.slideID ];


            /**
             * FUNCTION CLASS SETUP KICH THUOC CUA IMAGE THEO HUO"NG KHAC NHAU
             */
            var fnSizeDependRate = function() {
                    style.width  = M.r( iData.width * va.rate );
                    style.height = M.r( iData.height * va.rate );
                    $imgItem.css(style);
                },

                fnSizeDependWidth = function() {
                    style.width  = va.wSlide;
                    style.height = M.r( style.width / iData.rate);
                    $imgItem.css(style);
                };


            /**
             * SETUP KICH THUOC CHO IMAGE CUA CODE
             */
            // Truong hop Image la Image back
            if( iData.isImgback ) {

                // Kich thuoc theo ti le Responsive, bao gom type ['center', 'tile']
                if( typePosition == 'center' || typePosition == 'tile' ) {
                    fnSizeDependRate();
                }

                // Kich thuoc theo Chieu rong cua Viewport, bao gom type ['fill', 'fit', 'stretch']
                else {
                    if( !is.heightFixed ) fnSizeDependWidth();
                }
            }

            // Truong hop Image binh thuong
            else fnSizeDependRate();
        },

        /**
         * SETUP KICH THUOC VA VI TRI CUA IMAGE BACK
         */
        backPosition : function($imgItem) {
            varibleModule(this);

            var iData        = $imgItem.data(),
                typePosition = va.imgPos[iData.slideID],
                wImage       = iData.width,
                hImage       = iData.height,
                rateImage    = iData.rate,
                rateCanvas   = va.wCode / va.hCode,
                wImageCur,
                hImageCur;


            /**
             * FUNCTION CLASS
             */
            // Kich thuoc tuy thuoc vao Chieu rong Viewport
            var fnSizeDependWidth = function() {
                    wImageCur = va.wSlide;
                    hImageCur = M.r( wImageCur / rateImage);
                },

                // Kich thuoc tuy thuoc vao Chieu cao Code
                fnSizeDependHeight = function() {
                    hImageCur = va.hCode;
                    wImageCur = M.r(hImageCur * rateImage);
                }



            /**
             * TRUONG HOP VI TRI TYPE 'FILL'
             * Khong phu thuoc vao ti le Responsive
             */
            if( typePosition == 'fill' ) {

                // Truong hop co chieu Co dinh
                if( is.heightFixed ) {
                    (rateImage > rateCanvas) ? fnSizeDependHeight() : fnSizeDependWidth();

                    // Setup kich thuoc cho Image Item
                    $imgItem.css({ 'width' : wImageCur, 'height' : hImageCur });

                    // Setup vi tri Center Left cho Image back
                    that.backCenterLeft($imgItem);
                    // Setup vi tri Center Top cho Image back
                    that.backCenterTop($imgItem);
                }
            }


            /**
             * TRUONG HOP VI TRI TYPE 'FIT'
             * Khong phu thuoc vao ti le Responsive
             */
            else if( typePosition == 'fit' ) {

                // Truong hop co chieu Co dinh
                if( is.heightFixed ) {
                    (rateImage > rateCanvas) ? fnSizeDependWidth() : fnSizeDependHeight();

                    // Setup kich thuoc cho Image Item
                    $imgItem.css({ 'width' : wImageCur, 'height' : hImageCur });


                    // Setup vi tri Center Left cho Image back
                    that.backCenterLeft($imgItem);
                    // Setup vi tri Center Top cho Image back
                    that.backCenterTop($imgItem);
                }
            }


            /**
             * TRUONG HOP VI TRI TYPE 'STRETCH'
             * Khong phu thuoc vao ti le Responsive
             */
            else if( typePosition == 'stretch' ) {

                // Truong hop co chieu Co dinh
                if( is.heightFixed ) {
                    wImageCur = va.wSlide;
                    hImageCur = va.hCode;

                    // Setup kich thuoc cho Image Item
                    $imgItem.css({ 'width' : wImageCur, 'height' : hImageCur });
                }
            }


            /**
             * TRUONG HOP VI TRI TYPE 'TILE'
             */
            else if( typePosition == 'tile' ) {
                var aPosition    = [],
                    wImageAll    = 0,
                    hImageAll    = 0,
                    leftCur      = 0,
                    topCur       = 0,
                    isWidthFill  = false,
                    isHeightFill = false;

                // Lay kich thuoc hien tai cua Image Item
                wImageCur = $imgItem.outerWidth(true);
                hImageCur = $imgItem.outerHeight(true);


                /**
                 * VONG LAP TINH TOAN VI TRI CUA TUNG IMAGE CLONE
                 * Vong lap thu 1 la lap theo chieu cao
                 * @param array aPosition
                 */
                do {
                    /**
                     * CAP NHAT CAC GIA TRI TRUOC TIEN
                     */
                    leftCur     = 0;
                    topCur      = hImageAll;
                    wImageAll   = 0
                    isWidthFill = false;


                    /**
                     * VONG LAP THU 2 LA LAP CHIEU RONG
                     */
                    do {
                        // Truoc tien luu tru vi tri Left - Top
                        aPosition.push([leftCur, topCur]);

                        // Cap nhat cac gia tri
                        leftCur   += wImageCur;
                        wImageAll += wImageCur;

                        // Kiem tra co tiep tuc vong lap thu 2
                        if( wImageAll >= va.wSlide ) isWidthFill = true;
                        // console.log(isWidthFill, wImageAll, va.wSlide);

                    } while( !isWidthFill );


                    /**
                     * CAP NHAT CAC GIA TRI SAU KHI TINH TOAN VI TRI IMAGE CLONE THEO CHIEU RONG
                     */
                    hImageAll += hImageCur;
                    
                    /**
                     * KIEM TRA TIEP TUC VONG LAP THU 1
                     */
                    // Chieu cao Co dinh thi phai lap' day` chieu cao cua Code
                    if( is.heightFixed ) {
                        if( hImageAll >= va.hCode ) isHeightFill = true;
                    }
                    // Chieu cao Tu do thi vong lap chi chay 1 lan
                    else isHeightFill = true;

                } while( !isHeightFill );



                /**
                 * SETUP CHEN` IMAGE CLONE VAO IMAGE BACK VOI VI TRI CO SAN
                 */
                // Loai bo Image clone truoc do
                var $imgItemClone = iData.$itemClone,
                    $imgWrap      = $imgItem.parent('.'+ va.ns + 'imgback');
                if( !!$imgItemClone ) $imgItemClone.remove();

                // Reset data Item Clone
                iData.$itemClone = $();

                // Vong lap de chen` Image clone
                for( var i = 1, posLength = aPosition.length; i < posLength; i++ ) {
                    var $imgCloneCur = $imgItem.clone();

                    // Chen ben duoi Image Item
                    $imgCloneCur
                        .addClass(va.ns + 'imgclone')
                        .css({ 'left': aPosition[i][0], 'top': aPosition[i][1] })
                        .appendTo($imgWrap);

                    // Luu tru Image clone vao Data
                    iData.$itemClone = iData.$itemClone.add($imgCloneCur);
                }
            }


            /**
             * TRUONG HOP VI TRI TYPE 'CENTER'
             */
            else {

                // Setup vi tri Center Left cho Image back
                that.backCenterLeft($imgItem);
                // Setup vi tri Center Top cho Image back
                is.heightFixed && that.backCenterTop($imgItem);
            }
        },

        /**
         * SETUP IMAGE BACK VI TRI CENTER LEFT
         */
        backCenterLeft : function($imgItem) {
            varibleModule(this);

            var leftOnDom = M.pInt( $imgItem.css('left') ),
                leftCur   = ~~( (va.wSlide - $imgItem.outerWidth(true)) / 2 );

            // Setup css 'left'
            if( leftOnDom !== leftCur ) $imgItem.css('left', leftCur);
        },

        /**
         * SETUP IMAGE BACK O VI TRI CENTER TOP
         */
        backCenterTop : function($imgItem) {
            varibleModule(this);

            var top = M.r( (va.hCode - $imgItem.outerHeight(true)) / 2 );
            if( top == 0 ) top = '';
            $imgItem.css('top', top);
        }
    };
})(jQuery);
