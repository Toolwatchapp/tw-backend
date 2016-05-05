/**
 * MODULE VIDEO
 * ========================================================================== */
(function($) {

    // Kiem tra bien module
    if( !window.rt01MODULE ) window.rt01MODULE = {};

    // Bien toan cuc
    var that, o, cs, va, is, ti, M, SLIDESHOW,
        varibleModule = function(self) {
            that = self;
            o    = self.o;
            cs   = self.cs;
            va   = self.va;
            is   = self.is;
            ti   = self.ti;
            M    = self.M;
            SLIDESHOW = $.extend({}, rt01MODULE.SLIDESHOW, self);
        };

    /**
     * MODULE VIDEO
     */
    rt01MODULE.VIDEO = {

        /**
         * BAT DAU CONVERT TAG TRONG PHAN RENDER
         */
        convertTag : function($slCur) {
            varibleModule(this);

            /**
             * TIM KIEM VIDEO LINK TRONG SLIDE HIEN TAI
             */
            var selectorVideo = '[data-video-link]',
                $videos       = $slCur.find(selectorVideo);

            // Loai bo nhung Videos trong Nested
            var $nested = $slCur.find(va.ns),
                $videoInNested = $nested.find(selectorVideo);
            $videos = $videos.not($videoInNested);

            // Khong setup nua khi khong co Videos
            if( !$videos.length ) return false;



            /**
             * SETUP TUNG VIDEO LINK
             */
            // Tao doi tuong div moi
            // Copy toan bo thuoc tinh co trong link sang Node div
            // Tao moi Image preview cua Video neu co duong thuoc tinh href
            var fnLinkToDiv = function($link) {

                /**
                 * TAO DOI TUONG IMAGE WRAP VOI THUOC TINH COPY TU DOI TUONG LINK
                 */
                var $videoWrap = $('<div/>', { 'class': va.ns +'video' });

                // Copy tat ca cac thuoc tinh co tren Link vao` Node Div moi
                var attrs = {};
                $.each($link[0].attributes, function(key, attr) {

                    var nameCur  = attr.name,
                        valueCur = attr.value;

                    $videoWrap.attr(nameCur, valueCur);
                    attrs[nameCur] = valueCur;
                });



                /**
                 * TAO IMAGE PREVIEW CHO VIDEO NEU CO THUOC TINH 'HREF'
                 */
                if( attrs.href && !/^\s*$/g.test(attrs.href) ) {
                    var $imagePreview = $('<img/>', { 'src': attrs.href, 'alt': $link.text() });

                    // Chen them class Video preview vao Image preview
                    $imagePreview.addClass(va.ns +'video-preview');
                    // Chen Image preview vao Video wrap
                    $videoWrap.append($imagePreview).removeAttr('href');

                    // Chuyen class Image Lazy tu Video Wrap sang Image Preview
                    var classLazy = va.ns + o.nameImageLazy;
                    if( $videoWrap.hasClass(classLazy) ) {
                        $videoWrap.removeClass(classLazy);
                        $imagePreview.addClass(classLazy);
                    }
                }


                /**
                 * THAY THE NODE LINK BANG VIDEO WRAP
                 */
                $link.after($videoWrap).remove();
                return $videoWrap;
            };

            // Setup tu`ng Video
            // Neu la tag link thi chuyen doi sang tag Div
            // Khong chuyen doi tag tren Image back
            $videos.each(function() {
                var $videoCur = $(this),
                    videoTag  = $videoCur[0].tagName.toLowerCase(),
                    isImgback = $videoCur.hasClass(va.ns + o.nameImageBack);

                // Kiem tra chuyen doi Link sang Node Div moi
                if( videoTag == 'a' && !isImgback ) {
                    fnLinkToDiv($videoCur);
                }
            });
        },

        /**
         * KHOI TAO VIDEO KHI BAT DAU LOAD SLIDE HIEN TAI
         */
        init : function($slCur) {
            varibleModule(this);
            var slData     = $slCur.data(),
                $videoLink = $slCur.find('[data-video-link]'),
                $videoAll  = $(),
                isVideo    = false;


            // Setup tung link Video
            $videoLink.each(function() {
                var $videoCur = $(this),
                    strLink   = $videoCur.data('videoLink');

                // Truoc tien lay ID va Type cua Video hien tai
                var videoData = that.getID(strLink);

                // Chi setup nhung link Video duoc ho tro [Youtube, Vimeo]
                if( videoData.type ) {

                    /**
                     * SETUP NHUNG OPTIONS TREN TUNG` VIDEO
                     */
                    var optsDefault = {
                        $self     : $videoCur,
                        $slide    : $slCur,
                        isImgback : $videoCur.hasClass(va.ns + o.nameImageBack),
                        isShow    : false
                    };

                    // Setup Image Preivew cua Video
                    var $imgPreview  = $videoCur.find('img'),
                        isImgPreview = false;

                    if( $imgPreview.length ) {
                        videoData.$imgPreview  = $imgPreview;
                        videoData.isImgPreview = isImgPreview = true;
                    }

                    // Setup vi tri cua Video trong Slide hien tai
                    var $imgback = slData.$imgback,
                        videoPosition;

                    if ( !!$imgback && $imgback.is($videoCur) ) videoPosition = 'imgback';
                    else if( !!$videoCur.data('layer') )        videoPosition = 'layer';
                    else                                        videoPosition = 'free';
                    // Luu tru tren Video Data
                    videoData.position = videoPosition;

                    // Ket hop Options Default va Video Data
                    var optsOnVideo = M.stringToObject( $videoCur.data('video') );
                    videoData = $.extend({}, optsDefault, o.video, videoData, optsOnVideo);

                    // Them class type vao Video hien tai
                    // Loai bo cac thuoc ti'nh khong can thiet tren Dom cua Video hien tai
                    $videoCur
                        .addClass( va.ns +'video '+ va.ns + videoData.type )
                        .addClass( isImgPreview ? '' : va.ns + 'no-preview' )
                        .removeAttr('data-video-link')
                        .removeAttr('data-video')
                        .data('video', videoData);



                    /**
                     * SETUP CAC THANH PHAN CAN THIET TRONG VIDEO
                     */
                    // Setup doi tuo.ng Iframe cua Video
                    that.setupIframe(videoData);

                    // Chen cac thanh phan button can` thiet vao Video
                    isImgPreview && that.addElements(videoData);

                    // Luu tru Video vao bien tong quat -> su dung de update Video
                    isVideo = true;
                    $videoAll = $videoAll.add($videoCur);
                }
            });


            // Luu tru cac bien Video vao` data Slide hien tai
            if( isVideo ) {
                slData.isVideo = true;
                slData.$video  = $videoAll;
            }
        },




        /**
         * GET ID CUA VIDEO HIEN TAI
         */
        getID : function(strLink) {
            var videoType = false, videoID;


            /**
             * KIEM TRA LINK VIDEO CO DUOC HO TRO VA PHAN LOAI LINK VIDEO
             */
            // Kiem tra link Youtube
            // RegExp Youtube : Dua tren "http://stackoverflow.com/a/9102270"
            var
            fnCheckYoutube = function() {
                var reYoutube = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/,
                    match     = strLink.match(reYoutube);

                if( match && match[2].length == 11 ) {
                    videoID   = match[2];
                    videoType = 'youtube';
                }
            },

            // Kiem tra link Vimeo
            // RegExp Vimeo : Dua tren "http://stackoverflow.com/a/13286930"
            // Ho tro them 'ondemand'
            fnCheckVimeo = function() {
                var reVimeo = /^.*(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|ondemand\/(?:\w+\/)?|)(\d+)(?:$|\/|\?)/,
                    match = strLink.match(reVimeo);

                if( match && match[3] ) {
                    videoID   = match[3];
                    videoType = 'vimeo';
                }
            };


            // Cac buoc thu tu kiem tra link Video
            fnCheckYoutube();
            if( !videoType ) fnCheckVimeo();




            /**
             * TRA LAI KET QUA KIEM TRA
             */
            return {
                'type' : videoType,
                'id'   : videoID
            };
        },

        /**
         * SETUP IFRAME CUA VIDEO HIEN TAI
         */
        setupIframe : function(videoData) {
            var that = this;
            varibleModule(that);


            /**
             * SETUP THONG SO TREN DUONG LINK CUA VIDEO
             */
            var isAutoplay = videoData.isImgPreview ? 1 : 0,
                para = '?';

            if( videoData.type == 'youtube' ) {

                // Iframe video paramater
                para += 'rel=0';                        // Not show info in player
                para += '&autohide=3';                  // Auto hide button play, volume, force auto hide when height slide large than height video
                para += '&autoplay='+ isAutoplay;       // Auto play when show
                para += '&showinfo=0';                  // Turn on info video
                para += '&wmode=opaque';                // Fixed ie7/8, iframe overlay & z-index --> lam cho button close khong xuat hien; Trong firefox: iframe flash khong xuat hien

                videoData.src = 'https://www.youtube.com/embed/' + videoData.id + para;
            }
            else if( videoData.type == 'vimeo' ) {
                para += '&autoplay='+ isAutoplay;
                videoData.src = 'https://player.vimeo.com/video/' + videoData.id + para;
            }



            /**
             * SETUP NHUNG THUOC TINH TREN IFRAME
             */
            var iframeAttrs = {
                'class'       : va.ns + 'video-item',
                'frameborder' : 0,
                'allowfullscreen' : '',
                'width'       : '100%',
                'height'      : videoData.isImgPreview ? '100%' : videoData.height +'px'
            };

            videoData.$iframe = $('<iframe></iframe>', iframeAttrs);



            /**
             * CHEN IFRAME TRUC TIEP VAO VIDEO WRAP NEU KHONG CO IMAGE PREVIEW
             */
            if( !videoData.isImgPreview ) videoData.$self.append(videoData.$iframe);
        },

        /**
         * CHEN CAC THANH BUTTON OPEN - CLOSE CHO VIDEO
         */
        addElements : function(videoData) {
            varibleModule(this);
            var $self = videoData.$self;


            /**
             * TAO CAC DOI TUONG BUTTONS
             * Chen them class 'swipe-prevent' --> de loai bo event Swipe start
             */
            var ns          = va.ns,
                classSelect = ns +'swipe-prevent',
                classPlay   = ns +'btn-play',
                classClose  = ns +'btn-close';

            videoData.$btnPlay  = $('<div/>', { 'class' : classPlay +' '+ classSelect }),
            videoData.$btnClose = $('<div/>', { 'class' : classClose +' '+ classSelect });



            /**
             * TAO DOI TUONG OVERLAY VA LOADER
             */
            that.RENDER.loaderAdd($self, $self, '$loader');



            /**
             * CHEN CAC DOI TUONG VAO VIDEO VA SETUP EVENTS
             */
            $self.append(videoData.$btnPlay, videoData.$btnClose);

            // Setup Events cho cac Buttons
            that.events(videoData);
        },




        /**
         * EVENT CHO CAC THANH PHAN TRONG VIDEO
         */
        events : function(videoData) {
            varibleModule(this);
            var that = this,
                nameTapEvent = va.ev.click;


            /**
             * SETUP EVENT TAP TREN BUTTON OPEN
             */
            videoData.$btnPlay.on(nameTapEvent, function(e) {
                varibleModule(that);
                that.fnOpen(videoData);

                // Use for slideshow
                if( is.slideshow ) {
                    va.nVideoOpen++;
                    SLIDESHOW.go('videoOpen');
                }
            });



            /**
             * SETUP EVENT TAP TREN BUTTON CLOSE
             */
            videoData.$btnClose.on(nameTapEvent, function(e) {
                varibleModule(that);
                that.fnClose(videoData);

                // Setup for slideshow
                if( is.slideshow ) {
                    va.nVideoOpen--;

                    // Setup bien nVideoOpen luon >= 0
                    if(va.nVideoOpen < 0) va.nVideoOpen = 0;
                    SLIDESHOW.go('videoClosed');
                }
            });



            /**
             * FIXED IE : SETUP EVENT HOVER CHO IFRAME DE BUTTON CLOSE HIEN THI
             */
            if( is.ie ) {
                var classHover = va.ns +'hover';
                videoData.$iframe.hover(

                    function(e) { videoData.$btnClose.addClass(classHover) },
                    function(e) { videoData.$btnClose.removeClass(classHover) }
                );
            }      
        },

        /**
         * FUCTION CLASS CORE DE OPEN HOAC CLOSE VIDEO
         */
        fnOpen : function(videoData) {
            var that = this, ns = that.va.ns, $self = videoData.$self;

            if( !videoData.isShow ) {
                // Chen class 'init' vao khi load Iframe Video
                $self.addClass(ns +'video-init');
                videoData.isShow = true;

                // Them class 'Video back show' vao` Slide neu Video hien tai la Image back
                if( videoData.isImgback ) {
                    videoData.$slide.addClass(ns +'videoback-show');
                }


                /**
                 * CHEN IFRAME VIDEO VA SETUP EVENT
                 */
                videoData.$iframe
                    .attr('src', videoData.src)
                    .prependTo($self)
                    .on('load', function() {

                        // Thay doi class Actived tren Video
                        $self
                            .addClass(ns +'video-ready').removeClass(ns +'video-init')
                            .off('load');
                    });
            }
        },

        fnClose : function(videoData) {
            var that = this, ns = that.va.ns;

            if( videoData.isShow ) {

                videoData.$self.removeClass( '{ns}video-ready {ns}video-init'.replace(/\{ns\}/g, ns) );
                videoData.isShow = false;

                // Loai bo class 'Video back show' o Slide neu Video hien tai la Image back
                if( videoData.isImgback ) {
                    videoData.$slide.removeClass(ns +'videoback-show');
                }

                // Loai bo Iframe Video
                if( videoData.$iframe.length ) {
                    videoData.$iframe
                        .attr('src', 'about:blank')
                        .remove();
                }
            }
        },

        /**
         * SETUP KHI SLIDE DEACTIVED
         */
        slideDeactived : function(id) {
            var that = this;
            varibleModule(that);

            // Do'ng tat ca cac Video co tren Slide hien tai
            var $video = va.$s.eq(id).data('$video');
            !!$video && $video.each(function() {
                that.fnClose( $(this).data('video') );
            });

            // Reset lai bien nVideoOpen
            if( is.slideshow ) va.nVideoOpen = 0;
        }
    };
})(jQuery);