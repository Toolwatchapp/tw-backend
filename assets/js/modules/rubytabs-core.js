/**
 * RUBYTABS JQUERY PLUGIN
 * @package         RubyTabs
 * @author          HaiBach
 * @link            http://
 * @version         1.02
 */


;(function($) {
'use strict';


/**
 * KHOI TAO CAC BIEN GLOBAL
 * ========================================================================== */
if( !window.rt01MODULE ) window.rt01MODULE = {};
if( !window.rt01VA ) {

    window.rt01VA = {
        'codeName'  : 'rubytabs',
        'codeData'  : 'tabs',
        'namespace' : 'rt01'
    };

    /**
     * OPTIONS DEFAULTS
     */
    rt01VA.optsDefault = {
        'tagCanvas'     : 'div',
        'nameCanvas'    : 'canvas',
        'nameViewport'  : 'viewport',
        'nameSlide'     : 'slide',
        'nameImageBack' : 'imgback',
        'nameImageLazy' : 'img',
        'nameNav'       : 'nav',
        'namePag'       : 'pag',
        'nameCap'       : 'cap',
        'nameNext'      : 'nav-next',
        'namePrev'      : 'nav-prev',
        'namePlay'      : 'playpause',
        'nameTimer'     : 'timer',
        'nameLayer'     : 'layer',
        'nameOverlay'   : 'overlay',
        'nameDataSlide' : 'slide',
        'nameDataLazy'  : 'src',

        'name'          : null,                         // Use for search DOM outer Code
        'current'       : 'cur',
        'thumbWrap'     : 'thumbitem',
        'actived'       : 'actived',
        'deactived'     : 'deactived',

        // Setting type of elements
        'optionsPlus'   : 'tabs',                       // Options cong vao`, bao gom value ['tabs', 'slider']
        'layout'        : 'line',                       // line, dot, dash, free
        'fx'            : 'line',                       // fade, move, rectMove...
        'fxEasing'      : 'easeOutCubic',               // Easing cho hieu ung chinh
        'cssOne'        : 'roDeal',                     // Hieu ung bang css one bao gom slide out va slide in
        'cssTwoOut'     : 'slideShortDownOut',          // Hieu ung bang css cho slide out
        'cssTwoIn'      : 'pushSoftIn',                 // Hieu ung bang css cho slide in
        'cssThreePrev'  : 'pullIn',
        'cssThreeNext'  : 'pushIn',
        'cssEasing'     : null,
        'imagePosition' : 'center',                     // Vi tri cua Image back : ['center', 'fill', 'fit', 'stretch', 'tile']
        'direction'     : 'hor',                        // Swipe direction, defalut is horizontal, value ['hor', 'ver']

        // Setting with number or mix value
        'width'         : null,
        'height'        : null,
        'speed'         : 400,
        'speedHeight'   : 400,
        'layerSpeed'    : 400,
        'layerStart'    : 400,
        'perspective'   : 800,                          // Support for layer
        'slot'          : 'auto',                       // 'auto' || number
        'stepNav'       : 1,                            // 'visible' || number 1 -> n
        'stepPlay'      : 1,
        'widthRange'    : null,                         // Width cua Code trong khoang [from-to] : [width, wMin, wMax]
        'padding'       : 0,                            // padding default: 0, included padding-left, padding-right, padding range value
        'margin'        : 30,                           // margin default: 0, included margin-left, margin-right, margin range value
        'widthSlide'    : 1,                            // Width value of Slide in direction Horizontal, included range value, unit -> ['width', 'from', 'to']
        'heightSlide'   : 1,                            // Height value of Slide in direction Vertical, include range value
        'idBegin'       : 0,                            // ID cua Slide de hien thi dau tien khi khoi tao xong Code :  ['begin', 'center', 'end', 1234...]
        'showBy'        : 'all',                        // ['all', 'desktop', 'mobile']
        'showInRange'   : 0,
        'offsetBy'      : null,                         // Fullscreen options: offset by container, included offset-top & offset-bottom,
        'wheel'         : 'auto',                       // Event Wheel, gom cac options ['auto', 'both', false]

        // Setting with boolean value
        'isCenter'      : true,
        'isNav'         : false,
        'isPag'         : true,
        'isCap'         : false,
        'isLayerRaUp'   : true,
        'isSlideshow'   : false,
        'isSwipe'       : true,
        'isLoop'        : true,
        'isAnimRebound' : true,
        'isKeyboard'    : false,
        'isOverlay'     : false,
        'isViewGrabStop': false,
        'isFullscreen'  : false,
        'isDeeplinking' : false,
        'isCookie'      : false,

        // Setting with object value
        'load'          : { 'preload'       : 1,                // Number slide preload -> show cs; [type number, 'all']
                            'amountEachLoad': 2,
                            'isLazy'        : true             // Tinh nang lazyload
                          },

        'swipe'         : { 'isBody'        : true,             // Turn off swipe on body
                            'isAutoOnPag'   : true,             // Tu dong dangki hoac loaibo swipe gestures tren pagination
                            'easing'        : 'easeOutQuint'    // Easing cho transition sau khi swipe roi khoi
                          },

        'className'     : { 'grab'          : ['grab', 'grabbing'],
                            'swipe'         : ['', 'swiping'],
                            'stop'          : ['stopLeft', 'stopRight']
                          },

        // Fx to Random Fx Math : 'randomMath'
        'fxMathName'    : ['rectMove', 'rectRun', 'rectSlice',
                           'rubyFade', 'rubyMove', 'rubyRun', 'rubyScale',
                           'zigzagRun'
                          ],

        'pag'           : { 'type'          : 'tabs',           // ['tabs', 'thumbnail', bullet', 'list']
                            'width'         : null,
                            'height'        : null,
                            'minWidth'      : null,
                            'minHeight'     : null,
                            'maxWidth'      : null,
                            'maxHeight'     : null,
                            'direction'     : 'hor',            // ['hor', 'ver']
                            'position'      : 'begin',          // ['begin', 'end']
                            'align'         : 'begin',          // ['begin', 'center', 'end', 'justify']
                            'speed'         : 300,
                            'easing'        : 'easeOutCubic',

                            /**
                             * Support options: 
                             *  + null   --> none setup, width or height pagination depend on css
                             *  + 'full' --> width or height == Code. depend on direction 'hor' or 'ver'
                             *  + 'self' --> width or height == all width/height PagItems
                             */
                            'sizeAuto'      : 'full',
                            'typeSizeItem'  : 'self',           // Setup tat ca item the kich thuoc [max, min, self]
                            /**
                             * Lam cho Pag Item current vi tri chi'nh giua khi tap len item current
                             * Chi ap du.ng cho Tabs Hor, Tabs Ver luon luon co ItemCur o vi tri chi'nh giua
                             */
                            'isItemCurCenterWhenTap' : true,
                            'isJustifyWhenLarge'     : false,   // Kich thuoc tat ca items deu fit lai bang kich thuoc voi pagination
                            'isArrow'       : true,             // Hien thi mui ten dieu khien
                            'isTapOnArrow'  : true,             // Dangki-Huybo event tap tren arrow
                            'isMark'        : false,            // Ki hieu Pag Item Current
                            'sizeMarkTo'    : 'border',         // Loai kich thuoc cua Pag Mark - [self, padding, border, margin]
                            'moreClass'     : null,             // Adding class into pagination
                            'widthMinToHor' : 0,
                            'rangeMinToHor' : 0,                // Tu dong chuyen sang huong 'hor' khi chieu rong cua Document nho hon gia tri
                            'wheel'         : 'auto'            // Event Wheel cho Pagination, bao gom ['auto', 'both', false]
                          },

        'video'         : { 'height'        : 480 },

        'slideshow'     : { 'delay'         : 8000,
                            'timer'         : 'arc',            // [line, arc]
                            'isAutoRun'     : true,             // only actived false when have playpause button
                            'isPlayPause'   : true,
                            'isTimer'       : true,             // Timer only turn on when slideshow on
                            'isLoop'        : true,
                            'isHoverPause'  : false,
                            'isRunInto'     : false,            // Chi slideshow khi Code o tren vu`ng hien thi
                            'isRandom'      : false
                          },

        'timerArc'      : { 'width'         : null,
                            'height'        : null,
                            'fps'           : 30,
                            'rotate'        : 0,

                            'radius'        : 14,
                            'weight'        : 4,
                            'stroke'        : 'hsla(0,0%,0%,.6)',
                            'fill'          : 'transparent',

                            'radiusOuter'   : 14,
                            'weightOuter'   : 2,
                            'strokeOuter'   : 'hsla(0,0%,0%,.1)',
                            'fillOuter'     : 'transparent'
                          },

        'markup'        : { 'loader'        : '<div class="{ns}loader"><svg class="{ns}loader-circular"><circle class="{ns}loader-path" cx="50%" cy="50%" r="20" fill="none" stroke-width="4" stroke-miterlimit="15"/></svg></div>',
                            'nav'           : '<div class="{ns}nav"><div class="{ns}nav-prev">prev</div><div class="{ns}nav-next">next</div></div>',

                            // Options co gia gia tri bao gom ['code', 'viewport', 'nav', 'control']
                            'navInto'       : 'viewport',
                            'pagInto'       : 'code',
                            'controlInto'     : 'code',
                            'timerInto'     : 'control',
                            'playInto'      : 'control'},

        'deeplinking'   : { 'prefixDefault' : ['ruby', 'slide'],
                            'prefix'        : null,             // Prefix custom cho code, ket hop ca prefix-code va prefix-slide
                            'isIDConvert'   : true,             // Tu dong chuyen sang id-dom tren hash thay vi 'codeID_slideID'
                            'isOnlyShowID'  : true
                          },

        'cookie'        : { 'name'          : '',
                            'days'          : 7
                          },

        // Options danh rieng cho thiet bi mobile
        'mobile'        : {
            'speedHeight'   : null,
            'direction'     : 'hor'
        },

        // Options danh rieng cho nhung browser khong ho tro transition
        'fallback'      : {
            'markup'    : { 'loader' : '<div class="{ns}loader {ns}loader-old">loading</div>' }
        },


        // Tool for developer
        'isAutoInit'    : false,
        'isPosReport'   : false,
        'rev'           : ['erp']           // ['omed', 'moc.oidutsyht'], 'eerf'
    };



    /**
     * OPTIONS DEFAULT PLUS
     */
    rt01VA.optsPlus = {
        
        /**
         * OPTIONS PLUS MAC DINH CHO TABS
         */
        'tabs' : {},
        
        /**
         * OPTIONS PLUS MAC DINH CHO SLIDER
         */
        'slider' : {
            'margin'    : 0,
            'load'      : { 'isLazy'    : false },
            'pag'       : { 'type'      : 'thumbnail',
                            'position'  : 'end',
                            'align'     : 'center' },

            'coverflow' : { 'perspective' : 800,
                            'space'       : 50,
                            'rotate'      : 75 },

            'scale'     : { 'intensity' : 50 }
        },

        /**
         * OPTIONS PLUS MAC DINH CHO CAROUSEL
         */
        'carousel' : {
            'fx'         : 'line',
            'speed'      : 600,
            'widthSlide' : 300,
            'margin'     : 15,

            'isCenter'   : false,
            'isLoop'     : false,
            'isPag'      : false,
            'isNav'      : true,

            'load'       : { 'isLazy': false }
        }
    };
}
    


/* CODE MAIN SETUP
 * ========================================================================== */
$[rt01VA.codeName] = function($code, OptsJS) {

    /**
     * VARIBLES GLOBAL
     */
    var cs  = {},
        va  = { 
            $self   : $code,                                // Luu tru code
            codekey : Math.ceil( Math.random()*1e9 ),       // Codekey for codetab --> tranh xung dot multi code
            ns      : rt01VA.namespace
        },
        is  = {},
        ti  = {},
        
        $w = $(window), $doc = $(document),
        $canvas, $viewport, $control, $thumbItem,

        num, cssTf, cssTs, cssT, cssAT, i, j,
        divdiv = '<div/>';


    var o   = {},       // Bien o : merge tat ca cac options Data, JS, Defaults
        oo  = {},       // Bien oo : luu tru cac options luc ban dau

        // Bien one : dung de ho tro module
        one = { 'cs': cs, 'o': o, 'oo': oo, 'va': va, 'is': is, 'ti': ti };

    
    /**
     * INIT METHODS
     */
    var INIT = {

        check : function() {
            
            M.browser();                            // Browser detect --> nam o tren phuc vu cho proto.ajax
            M.cssName();                            // CSS: get prefixed css style
            M.setupBegin();                         // Setup bien dau tien ki init Code

            cs.ev.trigger('init');                  // Callback event begin init

            // Kiem tra phien ban
            if( NOISREV.check() ) {

                // // Kiem tra ajax image load
                // if( o.flickr.photosetID ) flickr.photoset();

                // // Kiem tra Code co doi tuong con hay khong
                // else INIT.pre();


                /**
                 * KIEM TRA TIEP THEO
                 *  + Kiem tra ben trong Code co' noi dung hay khong
                 */
                if( $code.children().length ) {
                    if( is.SHOW ){
                        SHOW.setupInit();
                    }
                    else {

                        // Setup bien de Code luon hien thi khi khong co Module
                        is.showInRange = is.wake = true;
                        INIT.ready();
                    }
                }
            }
            else $code.remove();
        },


        ready : function() {
            cs.ev.trigger('ready');                             // Event trigger 'ready'
            RENDER.structure();                                 // Code: create Canvas
            PROP.code();                                        // Code: get properties
                                                                // --> o tren PAG.renderSelf vi can thuoc tinh is.pag truoc
            is.playpause && !va.$playpause
            && SLIDESHOW.renderPlayPause();                     // Code: render playpause
            is.TIMER && TIMER.render();                         // Code: render Timer

            is.pag && PAG.renderSelf();                         // Code: render Pagnation
            is.nav && NAV.render();                             // Code: render Navigation
            is.cap && CAPTION.render();                         // Code: render Caption

            PROP.slides();                                      // Slide: properties, below PAG.renderSelf() --> can $pagItem dinh nghia truoc
            RENDER.other();                                     // Code: render other elements

            PROP.deepLinkCookie();                              // Ho tro doc deeplinking va cookie luc dau --> can co va.IDsOnDom truoc

            LOAD.way();                                         // Setup thu tu ID de load truoc sau, o duoi 'PROP.slide' de kiem load ajax
            LOAD.next();                                        // Loading slide dau tien
        },


        load : function() {
            is.initLoaded = true;                               // Bien luu gia tri Code da show
            cs.ev.trigger('loaded');                            // Event trigger 'loaded'

            is.pag && !is.pagList && PAG.typeSizeItem();        // Ho tro cho fn ben duoi 'UPDATE.general()' va vi tri tabs-vertical luc dau

            is.res && is.fullscreen && FULLSCREEN.varible();    // Fullscreen: re calculation padding & va.rate, nead
            UPDATE.general();                                   // Slide: Setup other elements (can` height Code neu huong la 'vertical')

            // cs.idCur == 0 && cs.ev.trigger('start');            // Event start
            // M.toggleSlide();                                    // First-item: add Actived

            EVENTS.setup();                                     // Sap xep va setup cac Events trong Code

            // Them timer cho Slideshow -> Fixed IE luc bat dau : lay duoc gia tri scrollTop khong chinh xac.
            setTimeout(function() {
                is.slideshow && SLIDESHOW.init();
            }, 400);

            M.setupEnd();                                       // Setup nhung thu con lai sau init end
            is.initEnd = true;                                  // Thong bao da~ khoi tao ket thuc

            // Setup khi da~ loaded tat ca hinh anh
            EVENTS.loadAll();
        }
    },







    /**
     * SMALL MEDTHODS
     */
    M = {
 
        /**
         * SETUP DAU TIEN CAC BIEN TRONG CODE
         */
        setupBegin : function() {

            /**
             * MERGE TAT CAT MODULES VOI NHAU
             */
            PROP.mergeAllModules();


            /**
             * MERGE CAC OPTIONS LAI VOI NHAU
             */
            PROP.mergeAllOpts();


            /**
             * MERGE CAC FUNCTION VAO BIEN TOAN CUC
             *  + Gop chung APIBASE va API vao 'cs'
             *  + Luu tru bien 'cs' tren doi tuong Code
             */
            cs.one = one;
            cs = $.extend(true, cs, API, APIMORE);
            $.data($code[0], rt01VA.codeName, cs);



            /**
             * SETUP ID CUA CODE
             *  + Ho tro nhan biet nhieu` code trong page
             *  + va.codeID: chi so id cua Code rieng biet
             *  + rt01VA.one[num]: luu tru tat ca bien trong Code rieng biet
             */
            var
            codeID = rt01VA.num;
            codeID = (codeID === undefined) ? 0 : codeID + 1;

            va.codeID = rt01VA.num = codeID;
            rt01VA['one'+ codeID] = one;



            /**
             * SETUP CAC GIA TRI BAN DAU
             */
            va.ns = rt01VA.namespace;

            // id timer cua tat ca layer --> loai bo 1 luc tat ca de dang
            ti.layer = [];

            // Su dung cho slideshow co video va map --> tat ca video phai dong thi slideshow tiep tuc duoc
            va.nVideoOpen = va.nMapOpen = 0;

            // Mac dinh mo khoa event Tap
            is.tapEnable = true;

            // Ten hieu ung --> ho tro toggle class hieu ung
            va.fxLast = va.fxCur = 'none';

            // Them class khac nhau vao Code tuy theo moi slide
            va.classAdd = [];

            // Bien actived va deactived
            va.actived   = va.ns + o.actived;
            va.deactived = va.ns + o.deactived;

            // Bien ho tro Update Toan bo Code - bo sung them thong tin
            // Bien se~ reset null khi de'n phan cuoi API.prop()
            va.addInfo = null;



            /**
             * HO TRO FUNCTION MA BROWSER CU~ KHONG CO
             */
            if( !!rt01MODULE.OLD ) {
                !Array.prototype.indexOf && OLD.arrayIndex();
                !String.prototype.replaceAt && OLD.replaceAt();
            }
        },

        /**
         * SETUP CAC THUOC TINH CON LAI KHI KET THUC INIT
         */
        setupEnd : function() {

            // Fixed cho IE7 khong tinh toan chinh xac' kich thuoc pagination
            !is.ts && setTimeout(UPDATE.resize, 50);
        },




        /**
         * BROWSER DETECT VA KIEM TRA CAC THUOC TINH HO TRO HTML5 + CSS3
         */
        browser : function() {

            // Bien shortcut va khoi tao ban dau
            var navAgent = navigator.userAgent;
                navAgentAll = navAgent || navigator.vender || window

            is.ie = /*@cc_on!@*/false || document.documentMode;     // At least IE6
            is.safari = /Constructor/i.test(Object.prototype.toString.call(window.HTMLElement));
            is.opera = !!window.opera || /\sOPR\//i.test(navAgent);
            is.chrome = !!window.chrome && !is.opera;               // Chrome 1+
            is.firefox = window.InstallTrigger !== undefined;

            // Kiem tra ie11 --> ie11 khong ho tro 'conditional compilation' nua
            is.ie11 = !!(is.ie && !new Function('/*@cc_on return @_jscript_version; @*/')());
            is.ie7  = !!(is.ie && /MSIE\s7\./i.test(navAgent));

            
            // Ten cua browser - neu khong tim dc tra ve undefined
            var browser = ['ie', 'safari', 'opera', 'chrome', 'firefox'];
            for( i = browser.length; i >= 0; i-- ) {
                if( !!is[browser[i]] ) { is.browser = browser[i]; break; }
            }

            // Kiem tra browser ho tro 'console'
            is.console = typeof console === 'object';

            // Kiem tra browser co ho tro html5.canvas
            is.canvas2d = (function() {
                var el = document.createElement('canvas');
                return !!(el.getContext && el.getContext('2d'));
            }());

            // Kiem tra browser co ho tro touch event
            // Loai bo 'is.msGesture' --> khong chinh xac va khong can thiet, thay the bang 'is.evPoinerAll'
            // is.msGesture = !!(window.navigator && window.navigator.msPointerEnabled) || !!window.MSGesture;
            is.evPointer = !!window.PointerEvent;
            is.evMSPointer = !!window.MSPointerEvent;
            is.evPointerAll = is.evPointer || is.evMSPointer;
            is.evSwipe = !!(("ontouchstart" in window) || (window.DocumentTouch && document instanceof DocumentTouch));
            is.swipeSupport = is.evSwipe || is.evPointer || is.evMSPointer;

            // Kiem tra co phai mobile, dua tren 3 yeu to:
            // + Ho tro touch/pointer events
            // + Ho tro huong xoay "orientation" --> tren mobile simular khong ho tro
            // + UserAgent thuoc cac loai trinh duyen thong dung hien nay "Android|webOS|iPhone|iPad ...."
            // + Su dung ma~ kiem tra dua tren trang "detectmobilebrowsers.com"
            var navAgentAll = navAgent || navigator.vender || window.opera;
            is.mobile = is.swipeSupport &&
            ( /(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(navAgentAll) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navAgentAll.substr(0, 4)) );

            // Kiem tra co phai Android native browser(khac Chrome) va version < 4.4
            is.androidNative = is.mobile && /Mozilla\/5\.0/i.test(navAgent) && /Android/i.test(navAgent)
                                         && /AppleWebKit/i.test(navAgent) && !(/Chrome/i.test(navAgent))
                                         && !(/Android\s+4\.4/i.test(navAgent));
            // Kiem tra iOS
            // is.iOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;


            // Setup ten cua tat ca loai event
            var suffix    = '.'+ va.ns + va.codekey,
                swipeName = ['', '', ''];
            
            if     ( is.evSwipe )     swipeName = ['touchstart', 'touchmove', 'touchend'];
            else if( is.evPointer )   swipeName = ['pointerdown', 'pointermove', 'pointerup'];
            else if( is.evMSPointer ) swipeName = ['MSPointerDown', 'MSPointerMove', 'MSPointerUp'];

            va.ev = {
                click   : 'click'     + suffix,
                drag    : 'dragstart' + suffix +' selectstart'+ suffix,         // 'selectstart' --> ho tro IE7-8
                resize  : 'resize'    + suffix,
                scroll  : 'scroll'    + suffix,
                key     : 'keyup'     + suffix,
                hash    : 'hashchange'+ suffix,

                swipe   : {
                    start : swipeName[0] + suffix,
                    move  : swipeName[1] + suffix,
                    end   : swipeName[2] + suffix,
                    type  : 'swipe' },

                mouse   : {
                    start : 'mousedown'+ suffix,
                    move  : 'mousemove'+ suffix,
                    end   : 'mouseup'  + suffix,
                    type  : 'mouse' }
            };

            // Neu khong co event Touch thi reset doi tuong ev.touch
            // --> khong xung dot khi dang ky chuoi voi chuoi '.code12345'
            if( swipeName[0] == '' ) va.ev.swipe = { start: '', move: '', end: '', type: 'swipe' };
            // Loai bo Mouse event trong cac thiet bi iOS
            // if( is.iOS ) va.ev.mouse = { start: '', move: '', end: '', type: 'mouse' };

            // Kiem tra co ho tro Event Wheel Native hay khong
            is.wheelNative = !!('onwheel' in document.createElement('div')); 
        },

        /**
         * LAY PREFIX CUA CSS3 + TEN CUA CSS
         */
        cssName : function() {

            // KIEM TRA PREFIX VERDER CUA BROWSER
            // Loai bo dau '-' --> vi du 'abc-def' tra lai ket qua 'abcDef'
            var fnTest = {
                camelCase : function(prop) {
                    return prop.replace(/-([a-z])/gi, function(m, prop) {
                        return prop.toUpperCase();
                    });
                },

                // MAIN TEST CSS
                css : function(prop, isPrefix) {
                    // Bien khoi tao va shortcut ban dau
                    var style  = document.createElement('p').style,
                        vender = 'Webkit Moz ms O'.split(' '),
                        prefix = '-webkit- -moz- -ms- -o-'.split(' ');

                    // Truoc tien kiem tra style khong co' vender
                    var styleCase = this.camelCase(prop);
                    if( style[styleCase] != undefined ) return (isPrefix ? '' : true);


                    // Tiep tuc kiem tra neu co' vender
                    // Dau tien chuyen doi style thanh Upper --> vi du 'flex-wrap' thanh 'FlexWrap'
                    var preStyle = M.properCase(styleCase);
                    // Kiem tra tung vender
                    for( var i = 0, len = vender.length; i < len; i++ ) {
                        if( style[vender[i] + preStyle] != undefined ) return (isPrefix ? prefix[i] : true);
                    }

                    // Tra lai ket qua false neu khong ho tro
                    return false;
                },

                // PREFIX CUA STYLE CSS
                prefix : function(prop) { return this.css(prop, true) }
            };
                


            /* Kiem tra prefix va bien transfrom co ban */
            var tf = 'transform', ts = 'transition';

            // CSS check
            is.ts = fnTest.css(ts);
            // is.ts = false;
            is.opacity = fnTest.css('opacity');


            // Store prefix support transition
            if( is.ts ) {
                var prefix = va.prefix = fnTest.prefix(tf),
                    timing = '-timing-function';
                
                va.cssTf = cssTf = prefix + tf;
                va.cssTs = cssTs = fnTest.prefix(ts) + ts;
                va.cssD  = cssTs +'-duration';
                va.cssAD = prefix +'animation-duration';
                cssT     = cssTs + timing;
                cssAT    = prefix +'animation'+ timing;
            }



            // Translate type: fix in safari mobile and ie
            // Shortcut translate begin/end
            var tl3D   = 'translate3d(',
                isTf3D = fnTest.css('perspective');

            va.tl0   = isTf3D ? tl3D        : 'translate(';
            va.tl1   = isTf3D ? ',0)'       : ')';
            va.tlx0  = isTf3D ? tl3D        : 'translateX(';
            va.tlx1  = isTf3D ? ',0,0)'     : ')';
            va.tly0  = isTf3D ? tl3D +'0,'  : 'translateY(';
            va.tly1  = isTf3D ? ',0)'       : ')';
        },

        /**
         * CHUYEN DOI TEN EASING SANG 'CUBIC-BEZIER' CSS3
         */
        easeName : function(name) {

            // Easing linear
            if( name == 'linear' ) return 'linear';

            // Modern browser ho tro css3
            if( is.ts ) {

                // Easing swing
                if( name == 'swing' ) return 'ease';

                // Easing others
                var easeDefault = '.25,.1,.25,1',
                    easeList    = {
                        'InSine'     : '.47,0,.745,.715',
                        'OutSine'    : '.39,.575,.565,1',
                        'InOutSine'  : '.445,.05,.55,.95',

                        'InQuad'     : '.55,.085,.68,.53',
                        'OutQuad'    : '.25,.46,.45,.94',
                        'InOutQuad'  : '.455,.03,.515,.955',

                        'InCubic'    : '.55,.055,.675,.19',
                        'OutCubic'   : '.215,.61,.355,1',
                        'InOutCubic' : '.645,.045,.355,1',

                        'InQuart'    : '.895,.03,.685,.22',
                        'OutQuart'   : '.165,.84,.44,1',
                        'InOutQuart' : '.77,0,.175,1',

                        'InQuint'    : '.755,.05,.855,.06',
                        'OutQuint'   : '.23,1,.32,1',
                        'InOutQuint' : '.86,0,.07,1',

                        'InExpo'     : '.95,.05,.795,.035',
                        'OutExpo'    : '.19,1,.22,1',
                        'InOutExpo'  : '1,0,0,1',

                        'InCirc'     : '.6,.04,.98,.335',
                        'OutCirc'    : '.075,.82,.165,1',
                        'InOutCirc'  : '.785,.135,.15,.86',

                        'InBack'     : '.6,-.28,.735,.045',
                        'OutBack'    : '.175,.885,.32,1.275',
                        'InOutBack'  : '.68,-.55,.265,1.55'
                    };

                name = name.replace('ease', '');
                return 'cubic-bezier('+ (!!easeList[name] ? easeList[name] : easeDefault) +')';
            }

            // Old browser: ho tro jQuery easing
            // Kiem tra plugin easing va ten easing co ho add vao chua --> neu khong su dung easing mac dinh 'swing'
            else {
                return (!!$.easing && !!$.easing[name]) ? name : 'swing';
            }
        },




        /**
         * TOGGLE CLASS 'CURRENT' GIUA CAC SLIDES
         */
        toggleSlide : function() {
            var idCur   = cs.idCur,
                idLast  = cs.idLast,
                $slCur  = va.$s.eq(idCur),
                $slLast = va.$s.eq(idLast),
                current = va.ns + o.current,
                deactived = va.deactived;

            
            // Slide: toggle class actived
            va.$s.not($slCur).removeClass(current).addClass(deactived);
            $slCur.addClass(current).removeClass(deactived);

            // Callback event toggle
            idLast != undefined && cs.ev.trigger('deselectID', idLast);
            cs.ev.trigger('selectID', idCur);

            
            // Pag: toggle class actived
            // Su dung phuong phap cu, tuong tu o tren!
            if( is.pag ) {
                va.$pagItem.eq(idLast).removeClass(current);
                va.$pagItem.eq(idCur).addClass(current);
                o.pag.isMark && PAG.sizePosOfMark();
            }

            // Nav: toggle class inactive
            is.nav && NAV.toggle();

            // Cap: toggle Content
            is.cap && CAPTION.toggle($slCur, $slLast);

            // Load slide hien tai dang xem, mac du chua toi luot phai load
            !is.apiRemove && LOAD.add($slCur);

            // Toggle classAdd tren Code
            is.CLASSADD && CLASSADD.toggle();

            // Toggle Deeplink & Cookie
            // Them dieu kien: idLast != undefined --> ngan can luc dau fire function
            if( idLast != undefined ) {
                o.isDeeplinking && is.DEEPLINKING && DEEPLINKING.write();
                o.isCookie && is.COOKIE && COOKIE.write();
            }

            // Kiem tra va setup Iframe lazy
            is.IFRAME && IFRAME.convertTag($slCur);

            // Update lai Code Nested trong Slide hien tai
            is.NESTED && NESTED.refreshInSlide($slCur);

            // Toggle swipe gestures tren Slide hien tai
            is.SWIPE && SWIPE.toggleEvent();
        },

        /**
         *  Cac truong hop:
         *  + is = -1: loai bo het class
         *  + is = 0: toggle sang class[0]
         *  + is = 1: toggle sang class[1]
         */
        toggleClass : function(type, value, $obj) {

            /**
             * DIEU KIEN TIEP TUC FUNTION
             *  -> Toi uu tren mobile --> khong can toggle class 'grabbing'
             */
            if( is.mobile && type == 'grab' ) return;


            /**
             * TIEP TUC FUNCTION
             */
            var classes  = o.className[type],
                class0   = va.ns + classes[0],
                class1   = va.ns + classes[1],
                classAdd = value ? class1 : class0,
                classDel = value ? class0 : class1;

            // Setup doi tuo.ng toggle class mac dinh
            if( $obj === undefined ) $obj = $viewport;


            // value = -1 --> REMOVE ALL
            if( value == -1 ) $obj.removeClass(class0 +' '+ class1);

            // value = 0 --> chuyen sang class[0]
            // value = 1 --> chuyen sang class[1]
            else $obj.addClass(classAdd).removeClass(classDel);
        },




        /**
         * LAY GIA TRI VALUE TRONG CHUOI
         */
        valueX : function(str) {

            // Array: get value
            var a = str.substr(7, str.length - 8).split(', ');

            // Array: return value 5
            return M.pInt(a[4]);
        },




        /**
         * SETUP CAC BIEN KHI SCROLL BROWSER
         */
        scroll : {
            setup : function() {

                // Truong hop options slideshow chi run khi o trong vung nhin thay
                if( is.ssRunInto ) {
                    is.into = false;
                    M.scroll.check();

                    var t = 200;
                    $w.off(va.ev.scroll);
                    $w.on(va.ev.scroll, function() {
                        clearTimeout(ti.scroll);
                        ti.scroll = setTimeout(function() { !is.ssPauseAbsolute && M.scroll.check() }, t);
                    });
                }
                
                // Truong hop slideshow run khong can trong vung nhin thay
                else {
                    is.into = true;
                }
            },

            check : function(isNoGo) {
                M.scroll.position();

                // Kiem tra Code o? trong vung xem cua browser
                var isInto = !(va.topW > va.botCode || va.botW < va.topCode),
                    isGoSlideshow = !isNoGo && is.slideshow && is.ssRunInto;

                if( isInto ) {
                    if( !is.into ) {
                        is.into = true;
                        isGoSlideshow && SLIDESHOW.go('scrollInto');
                    }
                }
                else {
                    if( is.into ) {
                        is.into = false;
                        isGoSlideshow && SLIDESHOW.go('scrollOut');
                    }
                }
            },

            position : function() {

                // Lay Vi tri top/bottom cua Window
                va.hWin = $w.height();
                va.topW = $w.scrollTop();
                va.botW = va.hWin + va.topW;

                // Code offset
                va.topCode = $code.offset().top;
                va.botCode = va.topCode + $code.outerHeight();
            }
        },




        /**
         * METHOD LIEN QUAN TOI MATH
         */
        a     : function(v)         { return Math.abs(v) },
        r     : function(v)         { return Math.round(v) },
        c     : function(v)         { return Math.ceil(v) },
        ra    : function()          { return Math.random() },
        rm    : function(m ,n)      { return M.ra() * (n - m) + m },
        sum : function(a, to) {
            var total = 0;
            if( to < 0 ) return total;
            if( to === undefined ) to = a.length;

            for( var i = 0; i < to; i++ ) {
                total += a[i];
            }
            return total;
        },



        /**
         * METHOD LIEN QUAN TOI CHUYEN DOI NUMBER
         */
        pFloat : function(n) {
            /**
             * Kiem tra va convert thanh so float
             * Voi dieu kien < 9007199254740992 --> lon hon ket qua khong dung
             */
            if( /^\-?\d*\.?\d+$/g.test(n) ) {
                var n1 = parseFloat(n);
                if (n1 < 9007199254740992 ) return n1;
            }

            // + them kiem tra co phai boolean hay khong
            else if( /^(false|off)$/g.test(n) ) n = false;
            return n;
        },

        // Chuyen doi gia tri thuoc tinh lay boi css() sang so nguyen
        pInt : function(v) { return /^\-?\d+/g.test(v) ? parseInt(v, 10) : 0; },

        // Chuyen doi gia tri theo ti le %
        pPercent : function(v, source) {
            if( v > 0 && v < 1 ) v *= source;
            return M.r(v);
        },

        // Kiem tra: Doi tuo.ng la Ma?ng + So luong cua Ma?ng + Tat ca gia tri cua Ma?ng la Number
        elesIsNumber : function(v, lenCheck) {
            var len   = v.length,
                isNum = $.isArray(v) && len == lenCheck;
            
            if( isNum ) {
                for( i = 0; i < len; i++ ) {
                    isNum = isNum && $.isNumeric(v[i]);
                }
            }
            return isNum;
        },



        /**
         * METHOD LIEN QUAN TOI TRANSITON
         */
        cssD1 : function()          { va.cssD1[va.cssD] = va.speed[cs.idCur] +'ms'; },
        tl    : function(x,y,u)     { var u = u ? u : 'px'; return va.tl0 + x + u +', '+ y + u + va.tl1; },

        // Translate x/y , ho tro fallback transition
        tlx   : function(x,u)       { var u = u ? u : 'px'; return is.ts ? (va.tlx0 + x + u + va.tlx1) : (x + u); },
        tly   : function(y,u)       { var u = u ? u : 'px'; return is.ts ? (va.tly0 + y + u + va.tly1) : (y + u); },

        // Add hay remove transition tren doi tuong co dinh
        tsAdd : function($obj, sp, es)   {
            var ts = {};
            if(!es) es = va.ease;

            ts[cssTs] = cssTf +' '+ sp +'ms '+ es;
            $obj.css(ts);
        },
        tsRemove : function($obj, isTimer) {

            // Truoc tien setup cssTs == none : de loai bo chuyen dong, co hieu qua tren firefox va IE
            var ts = {}; ts[cssTs] = 'none'; ts[va.cssD] = 0;
            $obj.css(ts);

            // Sau do setup timer de loai transition khoi DOM
            ts[cssTs] = ''; ts[va.cssD] = '';
            !!isTimer ? setTimeout(function() { $obj.css(ts) }, 0) : $obj.css(ts);
        },
        tfRemove : function($obj) { var tf = {}; tf[cssTf] = ''; $obj.css(tf); },
        ts : function(p, s, a, d) {
            a = a ? ' '+ a : '';
            d = d ? ' '+ d +'ms' : '';
            var t = {}; t[cssTs] = p +' '+ s +'ms'+ a + d;
            return t;
        },


        
        /**
         * METHOD LIEN QUAN TOI ARRAY
         */
        shift : function($obj, isShift) { isShift ? $obj.shift() : $obj.pop() },
        push  : function($obj, v, isPush) { isPush ? $obj.push(v) : $obj.unshift(v) },

        /**
         * LUA CHON HIEU U'NG NGAU NHIEN TRONG MA?NG HIEU U'NG
         */
        randomInArray : function(arr, except) {

            // Neu la hieu u'ng hien tai la ma?ng
            if( $.isArray(arr) ) {
                var itemCur = $.extend(true, [], arr),
                    indexItemLast = itemCur.indexOf(except);

                // Loai bo hieu u'ng moi vua thuc hien
                // Neu khong tim thay trong ma?ng hieu u'ng -> tang them 1 de fixed lua cho.n
                if( indexItemLast == -1 ) indexItemLast = itemCur.length + 1;
                itemCur.splice(indexItemLast, 1);

                // Luu chon hua u'ng ngau~ nhien trong ma?ng vua loai bo? hieu u'ng cu~
                return itemCur[ M.r(M.rm(0, itemCur.length - 1)) ];
            }
            return arr;
        },

        randomInArray2 : function(arrSource, arrCopy, except) {
            if( $.isArray(arrSource) ) {

                // Reset lai Mang Copy neu Mang? ro~ng
                // Reset lai Mang Copy neu con lai 1 doi tuong gio'ng Except
                if( !arrCopy.length || (arrCopy.length == 1 && arrCopy[0] == except) ) {
                    arrCopy = $.extend(true, arrCopy, arrSource);
                }

                // Loai bo doi tuong Except truong tien
                if( except != undefined ) {
                    var indexExcept = arrCopy.indexOf(except);
                    if( indexExcept != -1 ) arrCopy.splice(indexExcept, 1);
                }


                // Doi tuong trong Mang? Copy
                var idCur   = M.r(M.rm(0, arrCopy.length - 1)),
                    itemCur = arrCopy[idCur];

                // Loai bo Item trong Mang
                arrCopy.splice(idCur, 1);

                // Tra lai ID lay dc trong mang
                return itemCur;
            }
            return arrSource;
        },




        /**
         * METHOD OTHERS
         */
        // Swipe swap varible
        swapVaOnSwipe : function() { return va.$swipeCur.is($canvas.add(va.$s)) ? va.can : va.pag; },

        // Hien thi thong bao loi
        message : function(message, detail) {
            if( is.console ) {
                var str = '['+ rt01VA.codeName +': '+ message +']';
                if( !!detail ) str += ' -> '+ detail;
                console.warn(str);
            }
        },

        // Toggle add/removeClass tren doi tuong
        xClass : function($obj, isAdd, str) { $obj[(isAdd ? 'add' : 'remove') +'Class'](str); },

        // Viet hoa chu cai dau tien cua chuoi
        properCase : function(str) { return str.charAt(0).toUpperCase() + str.slice(1); },

        // Chuyen doi 'string' thanh 'object'
        stringToObject : function(str) {

            if( typeof str == 'string' ) {
                // Thay the dau phay ke'p thanh dau' pha?y don - neu co
                str = str.replace(/\u0027/g, '\u0022');
                str = $.parseJSON(str);
            }
            return $.isPlainObject(str) ? str : {};
        },

        /**
         * KIEM TRA GIA TRI WIDTH WINDOW/CODE TRONG RANGE - MEDIA CSS
         */
        matchMedia : function(min, max, isWidthOfCode) {

            /**
             * TRUONG HOP LAY WIDTH CUA CODE
             */
            if( !!isWidthOfCode ) {
                var wCode = $code.outerWidth();
                if( min <= wCode && wCode <= max ) return true;
            }


            /**
             * TRUONG HOP LAY WIDTH CUA WINDOW BROWSER
             */
            else {
                // Truong hop Browser ho tro matchMedia
                if( !!window.matchMedia ) {
                    var str = '(min-width: WMINpx) and (max-width: WMAXpx)'.replace('WMIN', min).replace('WMAX', max);
                    if( window.matchMedia(str).matches ) return true;
                }

                // Truong hop binh thuong : khong hop tro matchMedia
                else {
                    var wWin = $(window).width();
                    if( min <= wWin && wWin <= max ) return true;
                }
            }

            return false;
        },

        /**
         * TIM KIEM GIA TRI CAN THIET TRONG MA?NG
         * @return int Gia tri Width cua Code
         */
        getValueInRange : function(value, valueName) {
            var name = !!valueName ? valueName : 'value';

            // Bo sung: cho phep gia tri mac dinh, va` lay value gia tri nho nhat
            var wMin = 1e5, id = -1;
            for( i = value.num - 1; i >= 0; i-- ) {

                // From & To so sanh voi Width Window
                if( M.matchMedia(value[i].from, value[i].to ) ) {
                    
                    if( wMin >= value[i].to ) {
                        wMin = value[i].to;
                        id = i;
                    }
                }
            }

            // Value tra ve
            return (id > -1 ? value[id][name] : null);
        }
    },







    /**
     * VALUE OF PROPERTIES
     */
    PROP = {

        /**
         * GOI TAT CAC MODULE VAO BIEN TOAN CUC
         */
        mergeAllModules : function() {

            // Chuyen cac Module co sa~n trong Code sang bien 'one'
            one.INIT     = INIT;
            one.M        = M;
            one.PROP     = PROP;
            one.RENDER   = RENDER;
            one.LOAD     = LOAD;
            one.EVENTS   = EVENTS;
            one.POSITION = POSITION;
            one.SIZE     = SIZE;
            one.TOSLIDE  = TOSLIDE;
            one.FX       = FX;
            one.VIEW     = VIEW;


            // Nhu'nh bien 'one' vao` cac Module o ngoai`
            SWIPE       = $.extend({}, rt01MODULE.SWIPE, one);
            RESPONSIVE  = $.extend({}, rt01MODULE.RESPONSIVE, one);
            NAV         = $.extend({}, rt01MODULE.NAV, one);
            PAG         = $.extend({}, rt01MODULE.PAG, one);
            CAPTION     = $.extend({}, rt01MODULE.CAPTION, one);
            IMAGE       = $.extend({}, rt01MODULE.IMAGE, one);
            VIDEO       = $.extend({}, rt01MODULE.VIDEO, one);
            IFRAME      = $.extend({}, rt01MODULE.IFRAME, one);
            VIEW        = $.extend(VIEW, rt01MODULE.VIEW, one);
            FXMATH      = $.extend({}, rt01MODULE.FXMATH, one);
            FXCSS       = $.extend({}, rt01MODULE.FXCSS, one);
            SLIDESHOW   = $.extend({}, rt01MODULE.SLIDESHOW, one);
            TIMER       = $.extend({}, rt01MODULE.TIMER, one);
            SHOW        = $.extend({}, rt01MODULE.SHOW, one);
            DEEPLINKING = $.extend({}, rt01MODULE.DEEPLINKING, one);
            COOKIE      = $.extend({}, rt01MODULE.COOKIE, one);
            AJAX        = $.extend({}, rt01MODULE.AJAX, one);
            APIMORE     = $.extend({}, rt01MODULE.APIMORE);
            FULLSCREEN  = $.extend({}, rt01MODULE.FULLSCREEN, one);
            NESTED      = $.extend({}, rt01MODULE.NESTED, one);
            CLASSADD    = $.extend({}, rt01MODULE.CLASSADD, one);
            OLD         = $.extend({}, rt01MODULE.OLD, one);


            // Kiem tra cac Module o ngoai` co ton tai hay khong
            is.SWIPE       = !!rt01MODULE.SWIPE;
            is.RESPONSIVE  = !!rt01MODULE.RESPONSIVE;
            is.NAV         = !!rt01MODULE.NAV;
            is.PAG         = !!rt01MODULE.PAG;
            is.IMAGE       = !!rt01MODULE.IMAGE;
            is.VIDEO       = !!rt01MODULE.VIDEO;
            is.IFRAME      = !!rt01MODULE.IFRAME;
            is.VIEW        = !!rt01MODULE.VIEW;
            is.FXMATH      = !!rt01MODULE.FXMATH;
            is.FXCSS       = !!rt01MODULE.FXCSS;
            is.SLIDESHOW   = !!rt01MODULE.SLIDESHOW;
            is.TIMER       = !!rt01MODULE.TIMER;
            is.SHOW        = !!rt01MODULE.SHOW;
            is.DEEPLINKING = !!rt01MODULE.DEEPLINKING;
            is.COOKIE      = !!rt01MODULE.COOKIE;
            is.AJAX        = !!rt01MODULE.AJAX;
            is.APIMORE     = !!rt01MODULE.APIMORE;
            is.NESTED      = !!rt01MODULE.NESTED;
            is.CLASSADD    = !!rt01MODULE.CLASSADD;
        },

        /**
         * GOP TAT CA NHUNG OPTIONS LAI VOI NHAU
         */
        mergeAllOpts : function() {
            var optsDefault = rt01VA.optsDefault;
            
            /**
             * LAY DATA TREN HTML5
             *  + Kiem tra option tren data co phai json
             *  + Dam bao chuyen doi ra JSON neu co cau truc object
             */
            var optsData = $code.data(rt01VA.codeData);
            optsData = M.stringToObject(optsData);


            /**
             * MERGE OPTIONS :
             *  + Go.p tat da~ option tren data main va data js vao` option default cua Code
             *  + Thu' tu. uu tien: [optsData] > [OptsJS] > [options TypeCode] > [options Default]
             *  + Uu tien options danh rieng cho browser khong ho tro transtion
             *  + Uu tien options danh rieng cho mobile
             */
            var nameOptsPlus = null;
            if( !!optsData.optionsPlus )                nameOptsPlus = optsData.optionsPlus;
            if( !nameOptsPlus && !!OptsJS.optionsPlus ) nameOptsPlus = OptsJS.optionsPlus;
            if( !nameOptsPlus )                         nameOptsPlus = optsDefault.optionsPlus;

            var optsPlus = rt01VA.optsPlus[nameOptsPlus];
            o = $.extend(true, o, optsDefault, optsPlus, OptsJS, optsData);

            if( !is.ts && !$.isEmptyObject(o.fallback) )  o = $.extend(true, o, o.fallback);
            if( is.mobile && !$.isEmptyObject(o.mobile) ) o = $.extend(true, o, o.mobile);
        },




        /**
         * TACH VA LUU TRU MANG CO 3 THANH PHAN
         *  + Value bao gom Mang lo'n chua tung Mang co 3 thanh phan
         */
        chain3 : function(val, nameValue) {

            // Kiem tra value Name, mac dinh la 'value'
            if( !nameValue ) nameValue = 'value';


            /**
             * CHUYEN DOI THANH MA?NG LAN NUA
             *  + TH 1: number
             *  + TH 2: mang co 3 gia tri va moi gia tri la number
             */
            if     ( $.isNumeric(val) )       val = [[val, 0, 100000]];
            else if( M.elesIsNumber(val, 3) ) val = [val];


            // DIEU KIEN DE TIEP TUC FUNCTION
            if( !$.isArray(val) ) return false;


            // TIEP TUC FUNCTION
            var chain = { num : val.length },
                wMax  = 0;      // Gia tri cao nhat trong mang

            for( i = chain.num-1; i >= 0; i-- ) {
                var a = val[i];

                // Bo sung: tu dong bo sung var con thieu
                if( $.isNumeric(a) ) a = [a, 0, 100000];

                // Bien doi chuoi thanh cac thanh phan khac
                a[1] = M.pInt(a[1]);
                a[2] = M.pInt(a[2]);

                chain[i] = {
                    'from' : a[1],
                    'to'   : a[2]
                };
                
                chain[i][nameValue] = parseFloat(a[0]);      // included float number

                // Tim kiem gia tri lo'n nhat trong Ma?ng
                wMax = (wMax < a[2]) ? a[2] : wMax;
            }

            chain.wMax = M.pInt(wMax);
            return chain;
        },

        /**
         * TACH VA LUU TRU MANG CO 4 THANH PHAN
         * Tuong tu nhu chane3
         *  + Truong hop co 2 gia tri --> Bo dung gia tri thu 3 va 4
         */
        chain4 : function(val) {

            // Chuyen doi thanh ma?ng du'ng quy tac
            if     ( $.isNumeric(val) )       val = [[val, val, 0, 100000]];
            else if( M.elesIsNumber(val, 2) ) val = [[val[0], val[1], 0, 100000]];
            else if( M.elesIsNumber(val, 4) ) val = [val];

            // DIEU KIEN DE TIEP TUC FUNCTION
            if( !$.isArray(val) ) return false;


            // TIEP TUC FUNCTION
            var chain = { num : val.length },
                wMax  = 0;

            for( i = chain.num-1; i >= 0; i-- ) {
                var a = val[i];

                // Bo sung: tu dong bo sung var con thieu
                if( $.isNumeric(a) ) a = [a, a, 0, 100000];

                // Case: auto set from/to
                if( a.length == 2 ) { a[2] = 0; a[3] = 1e5; }

                // Case: double first value -> value left = value right
                else if( a.length == 3 ) { a.unshift(a[0]) }


                // Array: set chain
                chain[i] = {
                    'left'  : parseFloat(a[0]),
                    'right' : parseFloat(a[1]),
                    'from'  : M.pInt(a[2]),
                    'to'    : M.pInt(a[3])
                };

                // wMax: width-to maximum
                wMax = (wMax < M.pInt(a[3])) ? a[3] : wMax;
            }

            chain.wMax = M.pInt(wMax);
            return chain;
        },




        /**
         * SETUP THUOC TINH DEEPLINKING + COOKIE LUC DAU
         */
        deepLinkCookie : function() {

            // Tiep tuc update idCur va idBegin khi 'deeplink' va 'cookie' turn-on
            // Neu co 'deeplink' va 'cookie' cung luc --> uu tien cho 'deeplink'
            if( o.isDeeplinking ) is.DEEPLINKING && DEEPLINKING.read();
            else if( o.isCookie ) is.COOKIE && COOKIE.read();
        },




        /**
         * SETUP CAC THUOC TINH LUC BAT DAU
         */
        setupBegin : function() {

            /**
             * SETUP CAC GIA TRI CHI THUC HIEN 1 LAN DUY NHAT
             */
            if( !va.stepSetupInit ) {
                is.loop = o.isLoop;

                // Khoi tao mang luc ban dau
                va.ssIDRandom   = [];
                va.fxMathRandom = [];

                // Doi tuong swipe mac dinh la Canvas
                va.$swipeCur = $canvas;

                // Cac bien vi tri mac dinh
                va.xBuffer = 0;

                // Thuoc tinh cua Canvas va pagination --> su dung cho swipe
                va.can = { 'viewport' : $viewport };
                // Chua setup vi chua check isPag va $pag
                va.pag = {};
                is.swipeLimit = false;

                // Add class ten browser firefox vao Code --> ho tro fix transform bang css
                var ns      = ' '+ va.ns,
                    classes = '';
                if( is.browser == 'firefox' ) classes += ns +'firefox';
                if( is.ie7 )                  classes += ns +'ie7';
                if( is.mobile )               classes += ns +'mobile';
                if( is.androidNative )        classes += ns +'androidNative';
                $code.addClass(classes);
            }



            /**
             * SETUP CAC GIA TRI LUC BAT DAU CO THE UPDATE
             */
            // Range chieu width cua slide
            va.sSlideRange = PROP.chain3(o.widthSlide, 'width');

            // Setup typeHeight cua Code
            is.heightFixed = $.isNumeric(o.height);
            // Type Height chuyen sang 'Fixed' neu Fullscreen
            if( o.isFullscreen ) is.heightFixed = true;

            // Neu hieu u'ng la 'line' -> su dung translate bang 'left' -> nhanh hon
            // Dat o day vi` no' lien quan ca'c function phia duoi
            // if( o.fx == 'line' && !is.mobile ) is.ts = false;
        },

        /**
         * SETUP CAC THUOC TINH THANH TUNG MUC RIENG BIET
         */
        idNum : function() {

            // So luo.ng slide trong Code
            num = cs.num = va.$s.length;

            // ID slide current setup
            // Tu dong chuyen doi vi tri 'begin', 'center', 'end' sang gia tri number
            // Tu dong chuyen doi id dau neu gia tri la '<= 0'
            // Tu dong chuyen doi id cuoi neu '>= num'
            if( !va.stepSetupInit ) {
                var idBegin = o.idBegin;

                if     ( idBegin == 'begin' )       idBegin = 0;
                else if( idBegin == 'center' )      idBegin = ~~((num/2) - .5);
                else if( idBegin == 'centerRight' ) idBegin = ~~( num/2 );
                else if( idBegin == 'end' )         idBegin = num-1;

                else if (idBegin == -1 || idBegin >= num ) idBegin = num-1;
                else if( idBegin <= 0 )                    idBegin = 0;

                if( cs.idCur === undefined ) cs.idCur = va.idBegin = idBegin;
            }



            // Slide: only 1
            // Khoa cac thuoc tinh Code
            is.nav    = o.isNav && is.NAV;
            is.pag    = o.isPag && is.PAG;
            is.cap    = o.isCap && !!rt01MODULE.CAPTION;
            is.fullscreen = o.isFullscreen && !!rt01MODULE.FULLSCREEN;
            is.center = o.isCenter;

            if( num == 1 ) {
                is.nav = is.center = false;
                if( !is.pagTabs ) is.pag = false;
            }
        },

        transform : function() {

            // CSS duration options
            va.cssD0     = {};
            va.cssD1     = {};
            va.cssDEmpty = {};
            va.cssD0[va.cssD]     = '0s';   // Before: '0s'
            va.cssDEmpty[va.cssD] = '';
            va.xTimer = 100;


            // Canvas: set Transition timing function
            // Bien va.ease da ho tro browser fallback
            va.ease = M.easeName(o.swipe.easing);
            va.moveBy = va.moveLastBy = 'swipe';
        },

        direction : function() {

            // Swipe direction
            // Do o.direction duoc dam bao co 2 gia tri 'hor' va 'ver' --> short setup
            // Kiem tra va.addInfo --> Ho tro update VER TO HOR
            va.can.dirs = (o.direction == 'ver' && !is.mobile) ? 'ver' : 'hor';
            if( !(va.addInfo && va.addInfo.pagDirs) ) va.pag.dirs = o.pag.direction;

            // Bien thong bao huo'ng Vertical
            is.dirsHor = (va.can.dirs == 'hor');

            // Bien cssTf fallback thay doi theo huong swipe --> xem xet loai bo
            // Chi su dung tren Canvas
            if( !is.ts ) cssTf = va.cssTf = (!is.dirsVer ? 'left' : 'top');


            // Cac thuoc tinh Canvas va pagination giong nhau
            var fnSameValue = function(name) {
                    var isHor = va[name].dirs == 'hor';

                    // Ten transform, ho tro fallback
                    va[name].cssTf = is.ts ? cssTf
                                           : (isHor ? 'left' : 'top');

                    // Ten bien pageX thay doi theo huong trong Canvas va pagination
                    va[name].pageX = isHor ? 'pageX' : 'pageY';
                };
            fnSameValue('can');
            fnSameValue('pag');
        },

        fx : function() {

            /**
             * SETUP PHAN LOAI HIEU UNG
             */
            var fxType  = 'math',
                aFxType = ['cssOne', 'cssTwo', 'cssThree', 'line', 'fade', 'none'],

                fnFxType = function() {
                    for( i = 0; i < aFxType.length; i++ ) {
                        if( o.fx == aFxType[i] ) {

                            // Mac dinh
                            fxType = aFxType[i];

                            // Truong hop khong ho tro Css Transition cho Hieu u'ng Css
                            if( !is.ts && /css/g.test(aFxType[i]) ) fxType = 'fadeBack';
                            break;
                        }
                    }
                };

            fnFxType();
            va.fxType = fxType;

            
            // Ten hieu u'ng tu dong chuyen doi thanh Layout Dot
            var a = ['randomMath'];
            a = $.merge(a, aFxType);
            a = $.merge(a, o.fxMathName);
            va.fxInLayoutDot = a;
        },

        layout : function() {

            /**
             * SETUP HUONG VIEW
             */
            var viewList = ['fade', 'mask', 'coverflow', 'scale', 'zoom'];

            // Setup option 'view' luc ban dau
            va.view = 'basic';
            if( viewList.indexOf(o.fx) != -1 ) va.view = o.fx;

            // Tu dong chuyen doi option 'view' neu khong co' module VIEW hoac. huo'ng Vertical
            if( !is.VIEW || !is.dirsHor ) va.view = 'basic';

            // Doi ten chu~ thuo`ng thanh viet hoa chu~ cai dau tien cua option 'view'
            va.View = M.properCase(va.view);



            /**
             * SETUP LAYOUT
             */
            va.layout = o.layout;
            o.stepNav = o.stepPlay = 1;



            /**
             * CHUYEN DOI SANG LAYOUT KHAC
             * @param string va.layout
             */
            if( o.fx == 'line') va.layout = 'line';

            // Neu 'o.fx' la ten trong danh sach 'o.fxMathName' hoac la Array -> Layout Dot
            else if( va.fxInLayoutDot.indexOf(o.fx) != -1 || $.isArray(o.fx) ) va.layout = 'dot';

            // Chuyen doi sang layout khac tuy thuoc vao opts 'view'
            var viewListToLine = ['mask', 'coverflow', 'scale'];
            if( viewListToLine.indexOf(o.fx) != -1 ) va.layout = 'line';
        },

        center : function() {

            // Chuyen doi is.center dua Num slides & va.layout
            if( num == 2 && va.layout == 'line' ) is.center = false;

            // Tao bien moi de so sanh cho de dang --> vua center vua loop (mac dinh)
            // Truong hop pagination la 'tabs' --> load theo kieu binh thuong
            is.centerLoop = is.center && is.loop;



            /**
             * SETUP CHO LAYOUT CENTER
             */
            if( is.centerLoop ) {

                // Slidee clone duoc reset -> chu yeu' phuc vu. fillHole
                !!va.$slClone && va.$slClone.remove();
                va.$slClone = $('');


                // Khoi tao bien 'va.center'
                // Check number slide is odd or even
                var center = va.center = {
                    'isNum' : (M.c(num/2) > num/2) ? 'odd' : 'even'
                };

                // So luong slide left/right --> luu vao namespace va.center
                var nLeft  = ~~((num - 1) / 2),
                    nRight = (center.isNum == 'odd') ? nLeft : nLeft + 1;

                center.nLeft  = nLeft;
                center.nRight = nRight;
            }


            /**
             * SETUP CHO LAYOUT KHONG CENTER
             */
            else {
                va.center = null;
                is.loop = false;
            }
        },

        swipeEvent : function() {

            // Nhung option luc ban dau
            if( !va.stepSetupInit ) {
                va.swipeTypeCur = null;
            }
        },

        responsive : function() {

            /**
             * SETUP CAC BIEN LIEN QUAN RESPONSIVE
             */
            // Width: setup
            if( !!o.widthRange ) va.sizeRange = PROP.chain3(o.widthRange);
            else                 va.sizeRange = null;    // Func update: reset value

            // Padding: setup
            va.pa = { 'left': o.padding, 'top': 0 };    // va.pa always != undefined
            if( o.padding != 0 ) va.paRange = PROP.chain3(o.padding);
            else                 va.paRange = null;     // Func update: reset value

            // Margin: setup
            if( o.margin != 0 ) va.maRange = PROP.chain4(o.margin);
            else                va.maRange = null;      // Func update: reset value



            /**
             * SETUP TRUONG HOP CO REPSONSIVE
             */
            is.res = $.isNumeric(o.width) && is.RESPONSIVE;
            if( is.res ) {

                va.wRes = o.width;
                va.hRes = is.heightFixed ? o.height : 0;

                // Fullscreen: setup
                if( o.isFullscreen ) {

                    // Height responsive : auto add value when not setup --> used for fullscreen 
                    if( va.hRes == 0 ) va.hRes = va.wRes;

                    // Ratio responsive
                    va.rRes = va.wRes / va.hRes;
                }
            }



            /**
             * SETUP CAC BIEN BAN DAU
             */
            if( !va.stepSetupInit ) {
                va.rate = 1;
            }
        },

        grab : function() {

            // Grab stop
            if( o.isViewGrabStop ) $viewport.addClass(va.ns +'grabstop');
            else                   $viewport.removeClass(va.ns +'grabstop');
        },

        pagination : function() {
            var op = o.pag;

            // Ho tro cho phien ba?n cu~
            if( op.type == 'tab' ) op.type = 'tabs';

            // Setup cho pagination type free --> chi render khong co event
            is.pagList  = op.type == 'list';
            is.pagTabs  = op.type == 'tabs';
            is.pagThumb = op.type == 'thumbnail';
            is.alignJustify = op.align == 'justify';
            if( is.pagList ) is.swipeOnPag = false;


            // Kiem tra TAB VERTICAL
            var fnIsPagVer = function(opt, pag) {
                return !is.outsidePag
                    && !is.pagList
                    && (opt.isPag && pag.direction == 'ver');
            };


            // Kiem tra loai Pagination TAB VERTICAL
            va.pagVer = fnIsPagVer(o, o.pag) && va.pag.dirs == 'ver' ? (o.pag.position == 'begin' ? 'begin' : 'end')
                                                                     : null;

            // Reset Margins tren Viewport neu truoc kia la TAB VERTICAL
            if( !!va.stepSetupInit && fnIsPagVer(oo, oo.pag) ) {
                $viewport.css({ 'margin-left': '', 'margin-right': '' });
            }

            // Kiem tra kich thuoc cua Pag Item = kich thuoc moi Item
            // Neu co kich thuoc fixed thi kich thuoc Pag Item Self = false
            is.pagItemSizeSelf = (op.typeSizeItem == 'self' && !is.alignJustify);
            if( $.isNumeric(op.width) || $.isNumeric(op.height) ) is.pagItemSizeSelf = false;
        },

        slideshow : function() {

            // Timer
            var auto = o.slideshow;
            is.slideshow = o.isSlideshow && is.SLIDESHOW;
            is.timer = is.slideshow && auto.isTimer && auto.timer != 'none' && is.TIMER;
            va.timer = (auto.timer == 'arc' && !is.canvas2d) ? 'line' : auto.timer;

            // Button PlayPause
            is.playpause = is.slideshow && auto.isPlayPause;

            // Setup autoRun --> autoRun cho false khi dong thoi co playpause va isAutoRun false
            is.autoRun = !(auto.isPlayPause && !auto.isAutoRun);
            is.ssPauseAbsolute = !is.autoRun;

            // Setup other
            is.ssRunInto = auto.isRunInto;
        },

        setupEnd : function() {

            // Update gia tri khi refresh lai Code
            if( va.stepSetupInit ) {

                // Update fixed: remove Viewport-height inline
                is.heightFixed && $viewport.css('height', '');
            }

            // Loai bo cac options trong version free
            o.rev[0] == 'eerf' && NOISREV.eerf();
        },

        /**
         * SETUP CAC THUOC TINH CUA CODE THANH TUNG MANG RIENG BIET
         */
        code : function() {

            /**
             * SETUP CAC THUOC TINH LUC BAN DAU -> UU TIEN THEO THU TU
             */
            PROP.setupBegin();
            PROP.idNum();
            PROP.transform();
            PROP.direction();           // Co anh huo'ng toi view()

            PROP.fx();
            PROP.layout();
            PROP.center();
            PROP.swipeEvent();          // Co anh huo?ng tu` fx va layout
            PROP.responsive();

            PROP.grab();
            PROP.pagination();          // Nam duoi swipe event
            PROP.slideshow();
            PROP.setupEnd();



            /**
             * SETUP PHAN CON LAI
             */
            // Code: clear datas after first setup Code
            !va.stepSetupInit && $code.removeAttr('data-'+ rt01VA.codeData).removeData(rt01VA.codeData);

            // Varible to recognize call PROP.setup() run first
            if( va.stepSetupInit === undefined ) va.stepSetupInit = 1;

            // Add class khi setup xong properties
            UPDATE.addClass();
        },



        /**
         * THUOC TINH VA OPTIONS CUA TUNG SLIDE
         */
        slides : function() {

            /**
             * SETUP CAC BIEN LUC BAN DAU
             */
            if( va.fx === undefined ) {
                va.fx        = [];
                va.cssEasing = [];
                va.slot      = [];
                va.speed     = [];
                va.delay     = [];
                va.imgPos    = [];
                va.fxNum     = o.fxMathName.length;

                // Mang luu tru cac ID Dom tren tung Slide
                va.IDsOnDom  = [];
            }

            // Reset vi tri cua Slide luc dau o che do fallback
            if( !is.ts ) va.$s.css({ 'left': '', 'top': '' });



            /**
             * SETUP TUNG PHAN SLIDE
             */
            var fxType = va.fxType;
            va.$s.each(function(i) {

                var $ele    = $(this),
                    optsCur = $ele.data('optsSlide') || {};


                /**
                 * SETUP PHAN BAT BUOC
                 *  + Luu tru ID cho tung Slide
                 *  + Luu tru ID cho tung Pag Item
                 */
                $ele.data({ 'id' : i });
                is.pag && va.$pagItem.eq(i).data('id', i);



                /**
                 * SETUP OPTION CURRENT CUA TUNG SLIDE
                 *  + Truong hop luc bat dau setup slide khi Code Init
                 *  + Truong hop update thuoc tinh cua Code
                 *  + Truong hop update thuoc tinh cua tung Slide
                 */
                if( va.fx[i] === undefined ) {
                    var
                    optsData = $ele.attr('data-'+ o.nameDataSlide);
                    optsData = M.stringToObject(optsData);
                    optsCur  = $.extend(true, optsCur, o, optsData);

                    // Loai bo thuoc tinh data-slide tren tung Slide
                    $ele.removeAttr('data-'+ o.nameDataSlide);
                }
                else if( $.isPlainObject(va.oUpdate) && !$.isEmptyObject(va.oUpdate) ) {
                    optsCur = $.extend(true, optsCur, va.oUpdate);
                }
                else if( $.isPlainObject(va.oSlides) && $.isPlainObject(va.oSlides[i]) ) {
                    optsCur = $.extend(true, optsCur, va.oSlides[i]);
                }
                // Khong co optsCur --> khong tiep tuc setup nua~
                else return;



                /**
                 * SETUP CAC THUOC TINH CUA HIEU UNG
                 */
                // Setup Fx name
                if     ( fxType == 'cssOne' )   va.fx[i] = optsCur.cssOne;
                else if( fxType == 'cssTwo' )   va.fx[i] = [optsCur.cssTwoIn, optsCur.cssTwoOut];
                else if( fxType == 'cssThree' ) va.fx[i] = [optsCur.cssThreeNext, optsCur.cssThreePrev];
                else if( fxType == 'none' )     va.fx[i] = 'none';
                else                            va.fx[i] = (va.layout == 'line') ? null : optsCur.fx;

                // Setup Fx Easing ---> only cho css
                var cssEasing = optsCur.cssEasing;
                va.cssEasing[i] = !!cssEasing ? M.easeName(cssEasing) : cssEasing;

                // Setup Others options
                va.slot[i]   = optsCur.slot;
                va.speed[i]  = optsCur.speed;
                va.delay[i]  = optsCur.slideshow.delay;
                va.imgPos[i] = optsCur.imagePosition;

                // Kiem tra gia tri toi thieu cua Speed va Delay
                if( va.speed[i] < 200 ) va.speed[i] = 200;
                if( va.delay[i] < 500 ) va.delay[i] = 500;




                /**
                 * SETUP CAC THUOC TINH KHAC
                 */
                // Luu tru classAdd cua tung slide
                if( is.CLASSADD ) va.classAdd[i] = CLASSADD.filter(optsCur);

                // Kiem tra co id-text va luu tru tat ca id-text cua slide vao mang
                va.IDsOnDom[i] = $ele.attr('id');

                // Kiem tra co phai load ajax hay khong
                is.AJAX && AJAX.check(optsCur, $ele);

                // Kiem tra va luu tru Iframe lazy
                is.IFRAME && IFRAME.checkExist($ele);

                // Luu tru thanh phan 'control'
                // Luu tru toan bo options hien tai tren moi Slide
                $ele.data({ 'optsSlide': optsCur, 'control': optsCur.control });
            });
            
            

            /**
             * SETUP CAC BIEN SAU CUNG
             */
            va.tDelay = va.delay[cs.idCur];

            // value 1: for init Code; value 2: for init slide
            if( va.stepSetupInit == 1 ) va.stepSetupInit = 2;

            // Toggle 'first' & 'last' Class cho Pag Items
            is.pag && PAG.firstLastClass();
        }
    },







    /**
     * UPDATE VALUE PROPERTIES
     */
    UPDATE = {

        /**
         * CAN NHAT TONG HOP CAC KICH THUOC - VI TRI LUC BAN DAU
         */
        general : function() {

            /**
             * CSS WIDTH CHO CANVAS
             */
            if( va.layout == 'line' || va.layout == 'dot' ) {

                // Setup chieu rong cua Canvas theo huong swipe
                va.sCanvas = is.dirsHor ? va.wSlide : va.wCode;
                $canvas.css('width', va.sCanvas);
            }

            // TranslateW: get
            SIZE.sTranslate();



            /**
             * SETUP CAC BIEN KHAC TRONG LAYOUT LINE
             */
            if( va.layout == 'line' ) {

                /**
                 * XAC DINH SO LUO.NG SLIDE BEN CANH NHI`N THAY DUOC SO VOI SLIDE CHINH GIUA
                 * @param int va.center.nEdge
                 */
                if( is.centerLoop ) {

                    var wAll = 0, i = 0;
                    while (wAll < va.wCode) {
                        wAll = (va.wSlide + va.ma[0] + va.ma[1]) * (i * 2 + 1);       // So 1: cho slide giua, so 2 cho 2 slide ben canh
                        i++;
                    }
                    var nEdge = i-1;
                    if( nEdge * 2 >= num ) nEdge = ~~((num-1)/2);

                    // Luu ket qua vao namespace va.center
                    va.center.nEdge = nEdge;
                }


                /**
                 * OTHER SETUP
                 */
                // Setup lai vi tri sap xep cau tung Slide
                var sizeName = 'size'+ va.View;
                !!VIEW[sizeName] && VIEW[sizeName]();
            }



            /**
             * CAP NHAT VI TRI CUA CANVAS LUC BAT DAU
             */
            POSITION.canvasBegin();



            /**
             * PAGINATION : UPDATE LAI CAC GIA TRI
             */
            if( is.pag && !is.pagList ) {
                PAG.propAndStyle();
                PAG.posAndSizeOfItems();
                PAG.updateThumbnail();
                o.pag.isMark && PAG.sizePosOfMark();

                // Setup vi tri chinh giua cho Pag Item Current - Nhung khong co Animation
                PAG.posCenterForItemCur(true, true);
                


                /**
                 * KIEM TRA CHUYEN TABS VERTICAL SANG HORIZONTAL
                 * + Setup timer > 30ms : can phai lay Height cua Code SIZE.animHeightForCode() run truoc
                 * + verToHor() phai bo? vao function() {} --> fixed IE7
                 */
                setTimeout(function() { PAG.verToHor() }, 40);
            }
        },


        // Loai bo class hien co tren Code --> su dung cho update properties
        removeClass : function() {

            // Code: remove exist class
            var ns        = ' '+ va.ns,
                classCode = ns +'one'+ ns +'multi';

            classCode += ns +'layout-line'+ ns +'layout-dot';          // Layout type
            classCode += ns +'fx-'+ va.fxType;
            classCode += ns +'height-auto'+ ns +'height-fixed';         // Height type

            // Kiem tra huong cua pagination
            $code.removeClass(classCode);

            // Pagination loai bo class them vao
            is.pag && PAG.toggleClass(false);
        },

        // Add class vao Code sau khi update
        addClass : function() {

            // Code: class layout & height type
            var ns         = ' '+ va.ns,
                typeHeight = is.heightFixed ? 'fixed' : 'auto',
                classCode  = ns +'layout-'+ va.layout + ns +'height-'+ typeHeight + ns +'fx-'+ va.fxType;

            // Class nhan biet Browser ho tro transition && showInRange
            classCode += ns + (is.ts ? 'transition' : 'no-transition');
            classCode += is.opacity ? '' : ns +'no-opacity';
            if( !is.showInRange ) classCode += ns +'none';

            // Add Class vao Code sau khi setup
            $code.addClass(classCode);

            // Pagination add type class
            is.pag && PAG.toggleClass(true);
        },


        // Reset other when update options
        reset : function() {

            // Layout dot: remove translate
            if( va.layout == 'dot' ) {
                var _tf = {}; _tf[cssTf] = '';
                va.$s.css(_tf);
                POSITION.xAnimate($canvas, 0, 1, 1);
            }
        },



        // Update when resize Code
        // Thu tu function rat quan trong!!!!
        resize : function() {
            // console.log('resize');
            cs.ev.trigger('resize');                            // Event trigger 'resize'

            // Setup size cua pagItem --> tim kiem gia tri wItem/hItem
            // Boi vi trong template TAB VERTICAL --> can phai reset kich thuoc pagination truoc
            is.pag && !is.pagList && PAG.typeSizeItem();


            SIZE.widthForCode();                                // Lay Chieu rong cua Code truoc tien.
            is.res && RESPONSIVE.updateVars();                  // Responsive: calculation padding & va.rate
            is.IMAGE && IMAGE.updateAllImagesBy('size');        // Cap nhat kich thuoc cua Image Item khi co Chieu ro.ng cua Slide

            is.heightFixed && SIZE.heightFixedForCode();        // Lay Chieu cao cua Code truoc tien -> Ho tro. image autofit/autofill
            SIZE.endOfCode();                                   // Kich thuoc cua Code tong hop tuy thuoc Direction
            is.res && is.fullscreen && FULLSCREEN.varible();    // Fullscreen: tinh toa'n lai. Padding & va.rate, nead hCode first
            is.IMAGE && IMAGE.updateAllImagesBy('position');    // Cap nhat vi tri cua Image back co trong tat ca? cac Slides sau khi co Chieu cao cua Code

            UPDATE.general();


            /* Phan setup can DELAY */
            SIZE.animHeightForCode(true);                       // animHeightForCode: update make image shake --> delay co san
        }
    },







    /**
     * NOISREV
     */
    NOISREV = {
        check : function() {

            // Bien khoi tao ban dau
            var ver   = o.rev[0],
                isRun = false;

            // Phien ban pre
            if     ( ver == 'erp' || ver == 'eerf' ) isRun = true;
            else if( ver == 'omed') {

                var demoURL = o.rev[1].split('').reverse().join('');
                if( document.URL.indexOf(demoURL) != -1 ) isRun = true;
            }
            return isRun;
        },

        // Thuoc tinh cua phien ban free
        eerf : function() {

            // Options chung
            var options = {
                cssOne      : null,
                cssTwoIn    : null,
                cssTwoOut   : null,
                cssEasing   : null,

                isSlideshow : false,
                name        : null
            };
            o  = $.extend(true, o, options);

            // Layout line
            if( o.fx === null ) { o.fx = va.layout = 'line' }

            // 'pag' options
            o.pag.direction = 'hor';
        }
    },







    /**
     * RENDER ELEMENTS
     */
    RENDER = {

        /**
         * CAU TRUC RENDER MARKUP CAC THANH PHAN
         */ 
        structure : function() {

            // Setup markup first: Viewport, Canvas
            RENDER.viewport();
            RENDER.canvas();
            RENDER.overlayGhost($viewport);


            // Slides: setup markup
            // Tao var $s rong --> de add new slide trong vong lap
            va.$s = $('');
            $canvas.children().each(function() { RENDER.slide($(this)) });


            // Setup cac thanh phan trong moi~ Slide
            va.$s.each(function() {
                var $slCur = $(this);

                // Setup Caption, PagItem
                RENDER.capPagHTML($slCur);

                // Setup Videos
                is.VIDEO && VIDEO.convertTag($slCur);
            });
        },

        /**
         * TAO MARKUP VIEWPORT
         */
        viewport : function() {

            // Bien shortcut va khoi tao ban dau
            var viewClass = va.ns + o.nameViewport,
                viewport  = $code.children('.'+ viewClass);


            // Tim kiem Viewport
            if( viewport.length ) $viewport = viewport;
            else {
                $code.wrapInner( $(divdiv, { 'class': viewClass }) );
                $viewport = $code.children('.'+ viewClass);
            }

            // Luu tru doi tuong 'viewport'
            va.$viewport = $viewport;
        },

        /**
         * TAO MARKUP CANVAS
         *  + Mac dinh tagName la 'div'
         *  + Co the thay doi tagName cua Canvas by options 'tagCanvas'
         *  + Tu dong thay doi tagName cua Canvas thanh 'ul' neu phat hien tagName slide la 'li'
         */
        canvas : function() {
            
            // Bien shortcut va khoi tao ban dau
            var canvasClass = va.ns + o.nameCanvas,
                tagCanvas   = o.tagCanvas,
                canvas      = $viewport.children('.'+ canvasClass);


            // Canvas DOM ton tai, get tagName cua Canvas lan nua
            if( canvas.length ) {
                tagCanvas = canvas[0].tagName.toLowerCase();
            }
            // Canvas DOM not exist, create Canvas DOM with tagName options
            else {

                // Tu dong convert tagCanvasName neu phat hien tagName children la 'li'
                if( tagCanvas == 'div' && $viewport.children()[0].tagName.toLowerCase() == 'li' ) tagCanvas = 'ul';

                var html = (tagCanvas == 'ul') ? '<ul></ul>' : divdiv;
                $viewport.children().wrapAll( $(html, {'class': canvasClass}) );
            }

            // $canvas refer to DOM, and store data --> reuse for later
            $canvas = va.$canvas = $viewport.children('.'+ canvasClass);
            $canvas.data({ 'tagName': tagCanvas, 'pos' : { 'x' : 0 } });
        },

        /**
         * OVERLAY GHOST : HO TRO SWIPE GESTURE KHONG BI NGAN CAN BOI THANH PHAN KHAC
         */
        overlayGhost : function($parent) {

            var $overlayGhost = $(divdiv, { 'class' : va.ns +'overlay-ghost' });
            $parent.append($overlayGhost);
        },

        /**
         * TAO MARKUP CAC SLIDES
         *  + Wrap 'div'/'li'  cho slide khong co wraper
         *  + Add class 'cs-slide' va add icon loader vao slide
         */
        slide : function($sl) {
            var slClass = va.ns + o.nameSlide,
                slTag   = $sl[0].tagName.toLowerCase();


            // Slide co wrapper la 'div'/'li' hoac class 'cs-slide'
            if( slTag == 'li' || slTag == 'div' || $sl.hasClass(slClass) ) {

                // Loai bo class hien tai
                !$sl.children().length && $sl.removeClass(slClass);
            }

            // Slide khong co wrapper, chi co 1 thanh phan nhu '<a>'
            else {
                var cTag   = $canvas.data('tagName'),
                    html   = (cTag == 'ul') ? '<li></li>' : divdiv,
                    parent = $(html, {'class': slClass});

                $sl.wrap(parent);
                $sl = $sl.closest('.'+ slClass);
            }



            // Slide: add class --> de chac chan slide co class 'cs-slide'
            // Slides assign to varible $s, add class 'sleep' to setup height 100% , hidden all children
            $sl.addClass(slClass).addClass(va.ns +'sleep').addClass(va.deactived);

            // Slide store data ban dau de khong khi get thong tin --> khong bi loi
            var FALSE = false;
            $sl.data({
                'isLoading'  : FALSE,
                'isLoaded'   : FALSE,
                'isImgback'  : FALSE,
                'isLayer'    : FALSE,
                'isVideo'    : FALSE,
                'isAjax'     : FALSE,
                'isPagEmpty' : FALSE,
                'loadBy'     : 'normal'
            });


            // Create icon loader
            RENDER.loaderAdd($sl, $sl, '$slLoader');

            // Slide add to varible $s
            va.$s = va.$s.add($sl);

            // Function return slide: use for add new slide by api
            return $sl;
        },

        /**
         * TIM KIEM VA TAO MARKUP CAPTION ITEM VA PAG ITEM CUA TUNG SLIDE
         */
        capPagHTML : function($slCur) {

            /**
             * TIM KIEM NOI DUNG CAPTION CUA SLIDE HIEN TAI
             */
            var ns       = va.ns,
                capHTML  = '',
                slData   = $slCur.data(),
                $imgback = $slCur.find('.'+ ns + o.nameImageBack);

            // Truoc tien lay noi du.ng cu?a Image back
            $imgback.each(function() {
                var $i = $(this);

                // Noi dung caption tuy theo tag
                // Neu la image thi la noi dung trong attr 'alt'
                // Neu la link tag thi lay noi dung ben trong
                var tag = this.tagName.toLowerCase();
                if     ( tag == 'img' ) capHTML = $i.attr('alt');
                else if( tag == 'a' )   capHTML = $i.html();
            });


            // Tien tuc tim kiem noi dung trong Node Caption Item
            var $capItem = $slCur.children('.'+ ns +'capitem');
            if( $capItem.length ) {
                capHTML = $capItem.html();
                $capItem.remove();
            }

            // Luu tru Caption Item vao Data Slide
            slData.htmlCap = capHTML;      



            /**
             * SETUP PAGINATION ITEM SETUP
             */
            // Pagination item: tim kiem '.pagitem' --> luu tru vao data slide
            var $pagItem = $slCur.children('.'+ ns +'pagitem');

            // Neu khong co thi tao dom
            if( !$pagItem.length ) {
                $pagItem = $(divdiv, { 'class': ns +'pagitem' });
                slData.isPagEmpty = true;
            }

            // Luu tru vao trong slide roi loai bo
            slData.$pagItem = $pagItem;
            $pagItem.remove();
        },




        /**
         * TIM KIEM CAC THANH O BEN NGOAI CODE
         */
        searchDOM : function(classSearch) {
            var $dom = $(),
                NAME = o.name;

            /**
             * TIEM KIEM DOI TUONG KHI CODE CO TREN - TIM KIEM BEN NGOAI
             */
            if( NAME != null && NAME != undefined ) {

                var $el = $(classSearch);
                if( $el.length ) {
                    $el.each(function() {

                        // Kiem tra tren data codeData co phai la json + co' doi tuong name hay khong
                        var dataDom = $(this).data(rt01VA.codeData);
                        dataDom = M.stringToObject(dataDom);

                        if( $.isPlainObject(dataDom) && dataDom.name == NAME ) $dom = $(this);
                    });

                    // Tra ve doi tuong neu duoc phat hien
                    if( $dom.length ) return $dom;
                }
            }



            /**
             * TIEP TUC TIM KIEM DOI TUONG BEN TRONG CODE
             */
            $code
                .find(classSearch)
                .each(function() {
                    var $find = $(this);

                    // Kiem tra de Loai bo doi tuong trong Code Nested
                    if( $find.closest('.'+ va.ns + o.nameViewport).length == 0 ) return $find;
                });

            // Tra ve doi tuong Ro~ng neu khong tim thay
            return $();
        },

        /**
         * CHEN CAC THANH MARKUP VAO DOI TUO.NG
         */
        into : function(intoParent, $child) {
            var oMarkup = o.markup, $parent;

            // Tim kiem doi tuong Parent
            switch( intoParent ) {

                case 'viewport' :
                    $parent = $viewport;
                    break;

                case 'nav' :
                    if( !va.$nav ) {
                        va.$nav = $(divdiv, {'class' : va.ns + o.nameNav});
                        RENDER.into(oMarkup.navInto, va.$nav);
                    }
                    $parent = va.$nav;
                    break;

                case 'control' :
                    if( !$control ) {
                        $control = $(divdiv, {'class' : va.ns +'control'});
                        RENDER.into(oMarkup.controlInto, $control);
                    }
                    $parent = $control;
                    break;

                default :
                    $parent = $code;
                    break;
            }

            // Chen` doi tuo.ng con vao Parent moi vua tim duoc
            $parent.append($child);
        },

        /**
         * RENDER ICON LOADER
         */
        loaderAdd : function($sl, $parent, name) {

            // Thay the Namespace vao Markup
            var markup = o.markup.loader;
            markup = markup.replace(/\{ns\}/g, va.ns);

            // Luu tru vao data Slide va chen vao doi tuo.ng Parent
            var $loader = $(markup),
                slData  = $sl.data();

            slData[name] = $loader;
            $parent.append($loader);
        },

        loaderRemove : function($sl, name) {

            var $loader = $sl.data(name);
            $loader && $loader.remove();
        },




        /**
         * UPDATE IMAGE TRONG THANH PHAN OVERLAY
         */
        divImg : function(name, parent, isAfter) {
            
            var classes   = va.ns + o[name+'Name'],
                nameUpper = M.properCase(name);     // Viet hoa chu cai dau tien cua "name", Vi du: overlay -> Overlay

            va[name] = $code.find('.'+ classes);

            // Option co TURN ON --> setup
            if( o['is'+ nameUpper] ) {
                if( !va[name].length ) {

                    // Kiem tra image o trong container
                    var src = $code.data('img'+ name),
                        tag = (!!src) ? '<div class="'+ classes +'"><img src="'+ src +'" alt="['+ name +']"></div>'
                                      : '<div class="'+ classes +'"></div>';

                    // Chon lua chen after hay before so voi doi tuong parent
                    isAfter && parent.after($(tag)) || parent.before($(tag));
                }
            }

            // Option co TURN OFF --> loai bo --> ho tro cho update api
            else if( va[name].length ) va[name].remove();
        },

        /**
         * RENDER CAC THANH PHAN KHAC
         */
        other : function() {

            // Render thanh phan Overlay
            (oo.isOverlay != o.isOverlay) && RENDER.divImg('overlay', $canvas, true);
        }
    },







    /**
     * LOAD METHOD
     * Thuc hien chuc nang sau:
     *  + Bat dau load id slide khac 0
     *  + Load theo hinh zigzag phai/trai neu load id slide != 0
     *  + Preload truoc bao nhieu slide, mac dinh la 1
     *  + Load dong thoi cac slide khac nhau de toi uu toc load
     *  + Khi chua load xong, di chuyen toi slide khac --> uu tien load slide do
     */
    LOAD = {

        /**
         * THU TU ID SLIDE XUAT HIEN DUOC LUU TRU TRONG []
         * @process
         *  + Tim kiem ID-Slide bat dau trong []
         *  + Thu tu con lai trong [] chi viec +1
         *  + Neu thu tu lon hon va.num --> bat dau lai = 0
         */
        idMap : function() {
            var map = [];

            /**
             * SETUP ID MAP CHO LAYOUT CENTER
             */
            if( va.layout == 'line' && is.centerLoop ) {
                // Uu tien slide xuat hien ben phai neu tong slide la so chan
                var idBegin = M.c(num / 2) + cs.idCur;
                if( va.center.isNum == 'even' ) idBegin++;

                // idBegin bat dau lai bang so nho, neu lon hon num
                if( idBegin >= num ) idBegin -= num;


                // Func loop: add id to map
                for( i = 0; i < num; i++ ) {

                    // Id begin tro ve 0, neu lon hon num
                    if( idBegin >= num ) idBegin = 0;

                    // Map: add value
                    map[i] = idBegin++;
                }
            }


            /**
             * SETUP ID MAP CHO LAYOUT KHONG CENTER
             */
            else {
                for( i = 0; i < num; i++ ) {
                    map.push(i);
                }
            }

            // Luu vao bien
            va.idMap = map;
        },

        /**
         * LUU TRU ID CUA SLIDES VAO DE LOADING TUNG SLIDE THE THU TU
         */
        way : function() {

            // Khoi tao gia tri ban dau, su dung cho nhung fn khac
            va.nAddLoad  = 0;       // Number of slide add to loading
            va.nLoaded   = 0;       // Number of slide already loaded
            is.preloaded = false;   // Kiem tra preload slide xong chua

            var IDToLoad = [],       // Shortcut array id slide to load
                idCur    = cs.idCur, // Shortcut ID current
                oLoad    = o.load;


            /**
             * SO LUONG SLIDES LOADING SONG SONG VOI NHAU
             *  + Luc dau sau khi preload xong, va.nLoadParallel luon luon -1 --> cho nen + 1 luc dau tien --> can bang va khoi rac roi
             */
            va.nLoadParallel = oLoad.amountEachLoad + 1;


            /**
             * SETUP PRELOAD
             *  + neu 'all', load toan bo slides
             *  + neu == 0 --> luon luon load truoc 1slide
             */
            va.preload = oLoad.preload;
            if( oLoad.preload == 'all' ) va.preload = num;
            if( oLoad.preload <= 0 )     va.preload = 1;




            /**
             * LOADING CAC SLIDE THEO THU TU NHO? DEN LO'N - THANG TIEN
             * Load theo thu tu tu` 0,1,2,3...
             */
            var fnLoadLinear = function() {

                for( i = 0; i < num; i++ ) {
                    IDToLoad.push(i);
                }
            },

            /**
             * LOADING ZIGZAG CAC SLIDES DUA VAO 'IDMAP'
             */
            fnLoadZigzagByIDMap = function() {

                var idMap    = va.idMap,
                    idCenter = M.c(num/2 - 1),
                    idCur    = idCenter,
                    nLeft    = 1,
                    nRight   = 1,
                    isRight  = true;

                // Setup id ban dau
                IDToLoad[0] = idMap[idCur];
                for( i = 1; i < num; i++ ) {

                    if( isRight ) {
                        idCur = idCenter + nRight;
                        nRight++;
                        isRight = false;
                    }
                    else {
                        idCur = idCenter - nLeft;
                        nLeft++;
                        isRight = true;
                    }
                    IDToLoad[i] = idMap[idCur];
                }
            },

            /**
             * LOADING ZIGZAG CAC SLIDES THEO HUONG PHAI TRAI
             * Bat dau load vi tri idBegin --> load Phai Trai --> load tiep Phai Trai
             */
            fnLoadZigzagLine = function() {

                var right     = 1,      // Default: load right first
                    n         = 1,
                    leftEnd   = 0,      // Shortcut leftEnd
                    rightrEnd = 0;      // Shortcut rightEnd

                IDToLoad[0] = va.idBegin;
                for( i = 1; i < num; i++ ) {

                    if( (idCur != num - 1) && (right || leftEnd) ) {
                        IDToLoad[i] = idCur + n;

                        // Left: end
                        if( leftEnd ) n++;
                        else          right = 0;

                        // Right: check end
                        if( IDToLoad[i] >= num-1 ) rightrEnd = 1;
                    }
                    else {
                        IDToLoad[i] = idCur - n;
                        n++;

                        // Right: end
                        right = !rightrEnd;

                        // Left: check end
                        if( IDToLoad[i] <= 0 ) leftEnd = 1;
                    }
                }
            };

            // Truoc tien setup idMap truoc
            LOAD.idMap();

            // Truong hop slide sap xep vi tri center --> load zigzag round
            if( va.layout == 'line' && is.centerLoop ) fnLoadZigzagByIDMap();

            // Truong hop slide sap xep vi tri thang hang hoac co pagination type la 'tabs'
            // Neu ID = 0 thi load thang tien, con lai load zigzagline
            else idCur == 0 ? fnLoadLinear() : fnLoadZigzagLine();


            // Kiem tra cac id co phai load ajax --> loai bo khoi load luc dau
            if( is.AJAX ) IDToLoad = AJAX.removeAutoLoad(IDToLoad);

            // Gian gia tri cuoi cung tim duoc vao namespace
            va.IDToLoad = IDToLoad;
        },
        
        /**
         * LOAD SLIDE TIEP THEO
         */
        next : function($slNext) {

            // Doi tuong Slide Next
            if( !$slNext ) $slNext = va.$s.eq(va.IDToLoad[0]);

            // Kiem tra co phai load ajax --> cho tai noi dung ve roi setup
            // Con khong thi setup binh thuong
            ($slNext.data('isAjax') && is.AJAX) ? AJAX.get($slNext) : LOAD.slideBegin($slNext);
        },




        /**
         * SETUP DE LOADING SONG SONG NHIEU SLIDES CUNG LUC KHI BAT DAU
         */
        parallelBegin : function() {

            var IDToLoad = va.IDToLoad;
            if( IDToLoad != null ) IDToLoad.shift();    // id slide hien tai duoc lay ra
            va.nAddLoad++;                              // Tang so luong da load

            // Cac slide  preload cung luc trong truoc khi Code bat dau xuat hien
            // Luc nay LOAD.slideBegin() o LOAD.parallelEnd() bi tam dung
            if( va.nAddLoad < va.preload && IDToLoad != null ) {
                LOAD.slideBegin( va.$s.eq(IDToLoad[0]) );
            }
        },

        /**
         * SETUP LOADING SONG SONG NHIEU SLIDES CUNG LUC KHI SETUP KET THUC SLIDE
         */
        parallelEnd : function($slide) {

            // Bien shortcut va khoi tao ban dau
            var IDToLoad = va.IDToLoad,
                oLoad = o.load;

            // Varible use for preload
            va.nLoaded++;

            // Tat ca slide da preloaded
            if( !is.preloaded && va.nLoaded == va.preload ) is.preloaded = true;


            // Kiem tra co phai load lien tiep hay khong
            if( !oLoad.isLazy ) {

                // LoadAmount chi thuc hien neu nhu preLoad da xong
                // Kiem tra reset lai gia tri va.nLoadParallel neu va.nLoadParallel == 0
                if( is.preloaded ) {

                    va.nLoadParallel--;
                    if( !va.nLoadParallel ) va.nLoadParallel = o.load.amountEachLoad;
                }


                // Load next slide
                // Dieu kien: va.IDToLoad array khac empty va is.preloaded da load xong
                // Neu is.preloaded chua load xong thi LOAD.slideBegin() bi tam dung --> load new slide chuyen sang LOAD.parallelBegin()
                // Them dieu kien: LOAD.add() khong thuc hien --> tranh run func nay nhieu lan cung luc
                if( IDToLoad != null && is.preloaded && va.nLoadParallel >= oLoad.amountEachLoad && !$slide.data('isLoadAdd') ) {

                    // Kiem tra va.IDToLoad lan nua, khong empty -> cho truong hop: va.nLoadParallel > va.IDToLoad.length
                    for( i = va.nLoadParallel; i > 0 && IDToLoad != null && IDToLoad.length; i-- ) {
                        LOAD.next();
                    }
                }
            }
        },

        /**
         * LOADING THEM SLIDE MOI KHI SWAP DEN SLIDE DO'
         */
        add : function($slide) {
            var slData = $slide.data();

            // Kiem tra slide da load xong hay chua
            if( !slData.isLoading ) {

                // Sua lai bien loadAll
                is.loadAll = false;

                // Vi khong biet id slide current trong va.IDToLoad[] --> su dung vong lap
                // Lay index id trong mang va.IDToLoad
                // Kiem tra va.IDToLoad != null trong truong hop them slide bang 'API.addSlide'
                var IDToLoad = va.IDToLoad;
                if( IDToLoad != null ) {

                    for( i = IDToLoad.length - 1; i >= 0; i-- ) {
                        if( IDToLoad[i] == cs.idCur ) {

                            // Hoan doi id trong IDToLoad[], neu khong co trong thu tu load tiep theo
                            IDToLoad.splice(0, 0, IDToLoad.splice(i, 1)[0]);

                            // Break loop for
                            i = -1;
                        }
                    }
                }


                // Luu tru bien de nhan bien load bang LOAD.add() --> khong tai nua trong loadAmount
                slData.isLoaded = true;

                // Kiem tra load slide tiep theo
                LOAD.next($slide);
            }
        },




        /**
         * SETUP SLIDE HIEN TAI LUC BAT DAU
         */
        slideBegin : function($slide) {

            var ns      = va.ns,
                slData  = $slide.data(),
                slideID = slData.id;

            // Chen them class 'init' nhan biet bat dau khoi tao Code
            slideID == va.idBegin && $code.addClass(ns +'init');

            // Load: setup begin
            cs.ev.trigger('loadBegin', [$slide, slideID]);

            // Chi load binh thuong moi chay fn setupBegin
            slData.loadBy == 'normal' && LOAD.parallelBegin();

            // Remove class 'sleep' --> remove height = 100% && all children show
            $slide.removeClass(ns +'sleep');


            // Tim kiem Image duoc quan ly trong Code
            // Bat buoc phai class {ns}img hoac Image layer
            // SelectorImage : ".{ns}imgback, .{ns}img, img.{ns}layer"
            // Callback phien ban cu: ho tro 'imglazy'
            var selectorImage = '.'+ ns + o.nameImageBack +', .'+ ns + o.nameImageLazy +', img.'+ ns + o.nameLayer,
                $images       = $slide.find(selectorImage),

                // Tim kiem tat cac Images trong Code nested
                // Loai bo image trong Code nested --> khoi bi chong cheo' tinh toan
                $codeNested   = $slide.find('.'+ ns),    
                $imagesNested = $codeNested.find(selectorImage);
                $images = $images.not($imagesNested);


            // Setup data trong Slide hien tai
            var imageNum = $images.length;
            $slide.data({
                '$images'   : $(),
                'imageNum'  : imageNum,
                'nCur'      : 0,
                'isLoading' : true
            });



            /**
             * SETUP KICH THUOC VA TI LE CUA CODE O SLIDE DAU
             *  + Phuc vu kich thuoc ti le cua image luc ban dau
             */
            if( slideID == va.idBegin ) {

                // Lay kich thuoc width Code
                SIZE.widthForCode();
                // Responsive: tinh toa'n gia tri padding & va.rate
                is.res && RESPONSIVE.updateVars();
                va.rateInit = va.rate;


                // Toggle slide Current luc ban dau
                cs.idCur == 0 && cs.ev.trigger('start');
                M.toggleSlide();
            }



            /**
             * SETUP TAT CA IMAGES TRONG SLIDE
             */
            if( imageNum && is.IMAGE )
                IMAGE.setupEachSlide({ '$images': $images, '$slide': $slide, 'id': slideID });

            else
                LOAD.slideEnd($slide);
        },

        /**
         * SETUP SLIDE HIEN TAI SAU KHI DA LOADED XONG IMAGE
         */
        slideEnd : function($slide) {

            var hSlide = $slide.outerHeight(true),
                slData = $slide.data(),
                id     = slData.id;


            // Slide current: setting data
            slData.height   = hSlide;
            slData.isLoaded = true;


            /**
             * HIEN THI CODE VA SETUP CAC GIA TRI KHAC KHI LOAD XONG SLIDE DAU TIEN
             */
            if( !is.initLoaded ) {

                // Toggle class 'init' & 'ready' -> Code da san san
                $code.addClass(va.ns +'ready').removeClass(va.ns +'init');

                // SETUP CHIEU CAO CHO CODE O SLIDE DAU TIEN
                // --> Phai loai bo class 'init' truoc khi thuc hien fn --> khoi bi lag
                if( is.heightFixed ) SIZE.heightFixedForCode();
                else                 SIZE.heightAutoForCode(hSlide);

                // Kich thuoc cua Code tong hop tuy thuoc Direction
                SIZE.endOfCode();

                // Init: load continue
                INIT.load();
            }


            /**
             * SETUP VI TRI CUA IMAGE BACK
             */
            if( is.IMAGE ) {
                var $imgbackItem = $slide.data('$imgbackItem');
                !!$imgbackItem && IMAGE.backPosition($imgbackItem);
            }


            // Cap nhat kich thuoc va vi tri trong huo'ng Vertical
            !is.dirsHor && VERTICAL.slideLoaded();

            // Hien thi slide sau khi loaded het image.
            $slide.addClass(va.ns +'ready');


            // Layer: init, need va.hCode first!
            // layer.init($slide);
            // (id == cs.idCur) && layer.run(id, 'start');

            // Hotspot: init --> tuong tu nhu layer
            // hotspot.init($slide);
            // var HOTSPOT = rt01MODULE.HOTSPOT;
            // HOTSPOT && HOTSPOT.init(one, $slide);
            

            // Video, Map: init
            is.VIDEO && VIDEO.init($slide);
            // map.init($slide);


            // Icon loader: remove
            RENDER.loaderRemove($slide, '$slLoader');


            // SLideshow: play next --> khong phu hop voi 'isLazy' option
            // slData.isPlayNext && cs.play();


            // Events trigger: slide loaded
            cs.ev.trigger('loadSlide.'+ id);
            cs.ev.trigger('loadEnd', [$slide, id]);


            // Events 'loadAll' : khi va.IDToLoad[] empty
            if( va.IDToLoad != null && va.IDToLoad.length == 0 ) {
                is.loadAll = true;
                va.IDToLoad   = null;

                cs.ev.trigger('loadAll');
            }


            // Setup khi add new slide bang api add
            if( is.apiAdd ) {
                cs.refresh();       // Refresh Code lan nua khi load xong
                is.apiAdd = false;  // Bien return false --> de biet ket thuc update add
            }


            // Slide: load next, varible $slide for new add loading
            // O duoi cuoi cung --> tien func & so sanh o tren khong bi anh huong khi load new slide moi
            LOAD.parallelEnd($slide);
        }
    },







    /**
     * POSITION
     */
    POSITION = {

        /**
         * SETUP ANIMATION CUA DOI TUONG VOI VI TRI CO DINH
         */
        xAnimate : function($obj, nx, isNoAnim, isPosFixed, _speed, _ease) {

            /**
             * VALUE SETUP
             * Doi tuong translate la $obj --> neu khong co chon doi tuong swipeCur
             */
            var $swipe = ($obj === null) ? va.$swipeCur : $obj,
                p      = $swipe.is($canvas.add(va.$s)) ? va.can : va.pag,

                // Vi tri can di chuyen toi
                x = isPosFixed ? nx : (- nx * p.sTranslate + p.xCanvas),

                // Toc do va easing khi transition
                sp = _speed ? _speed : va.speed[cs.idCur],
                es = _ease ? M.easeName(_ease) : va.ease;

            // Setup Vi tri gioi ha.n trong Effect Carousel
            x = POSITION.xLimitInCarousel(x);

            // Cap nhat vi tri hien tai cua xCanvas
            p.xCanvas = x;



            /**
             * TRANSITION SETUP
             * Phan chi thanh 2 truong hop:
             *  + Ho tro transition css
             *  + Khong ho tro transition css
             */
            var tf = {};
            if( is.ts ) {

                // Clear timeout thuoc tinh transition
                clearTimeout($swipe.data('timer'));

                // Them thuoc tinh transition css vao Canvas
                if( !isNoAnim ) M.tsAdd($swipe, sp, es);

                // Canvas: set transform - important
                // Ho tro transition theo huong swipe
                var translate = (p.dirs == 'hor') ? 'tlx' : 'tly';
                tf[p.cssTf] = va[translate + 0] + x +'px'+ va[translate + 1];
                // setTimeout(function() { $swipe.css(tf) }, 4);
                $swipe.css(tf);

                // Clear thuoc tinh transition --> kiem soat tot hon
                $swipe.data('timer', setTimeout(function() { M.tsRemove($swipe) }, sp));
            }

            // Transition danh cho old brower --> su dung jQuery animate
            else {
                tf[p.cssTf] = x;

                if( isNoAnim ) $swipe.stop(true, true).css(tf);
                else           $swipe.animate(tf, {duration: sp, queue: false, easing: es});
            }
        },

        /**
         * SETUP VI TRI MIN - MAX TRONG HIEU UNG CAROUSEL
         */
        xLimitInCarousel : function(x) {

            // Dieu kien de gioi han vi tri trong hieu ung Carousel
            if( va.layout == 'line' && !is.loop && va.$swipeCur.is(va.$canvas) ) {
                var p = va.can;

                if     ( x > p.xMin ) x = p.xMin;
                else if( x < p.xMax ) x = p.xMax;
            }

            // Tra ve gia tri vi tri x Gioi han
            return x;
        },

        /**
         * SETUP DI CHUYEN DOI TUONG TOI VI TRI CO DINH
         */
        xTranslate : function($obj, nx, isPosFixed, xPlus, isHorCustom) {

            // Position: init
            var x;
            if( isPosFixed ) x = nx;
            else             x = nx * va.can.sTranslate;

            // Transform: add _xPlus
            if( $.isNumeric(xPlus) ) x += xPlus;

            // Object: set transform
            var tf     = {},
                isHor  = isHorCustom === undefined ? is.dirsHor : isHorCustom,
                tlName = isHor ? 'tlx' : 'tly';

            tf[cssTf] = is.ts ? M[tlName](x) : x;
            $obj.css(tf);
        },




        /**
         * CAN BANG CHO LAYOUT CENTER
         * @porpose
         *  + Di chuyen slide o vi tri edge
         *  + -> slides luon can bang so luong 2 ben sau khi Canvas move
         *
         * @howtodo
         *  + Xac dinh bao nhieu slide can di chuyen --> vong lap di chuyen tung slide
         *  + Di chuyen tung slide: xac dinh id slide can di chuyen, vi tri(p) se di chuyen toi
         *  + -> thuc hien di chuyen bang xTranslate()
         */
        balance : function(isContinuity, isOne, speed) {
            // Dieu kiem thuc hien function
            if( !is.loop ) return;


            // Kiem tra di chuyen 'next' hay 'prev' slide
            // Di chuyen 'next' va 'prev' co cung cach thuc nhu nhau --> chi khac doi so
            var isNext = va.nMove > 0,

                // Thuoc tinh luu tru su khac nhau khi di chuyen 'next' hay 'prev'
                a = isNext ? { is : 1, s : 1, id0 : 0, idN : num - 1 }
                           : { is : 0, s : -1, id0 : num - 1, idN : 0 },

                // So luong slide di chuyen duoc ket hop voi options 'isOne', mac dinh la va.nMove
                nMove = isOne ? 1 : M.a(va.nMove);


            // Toc do khi translate --> cang nhieu slide thi toc do cang nho
            a.speed = (speed === undefined) ? va.speed[cs.idCur] : speed;

            // Chen nhung option khac vao namespace
            a.isContinuity = isContinuity;


            // Swap slide to balace
            var w = va.can.sTranslate,
                id, xCur, tf;

            for( i = 0; i < nMove; i++ ) {

                // GIA TRI CUA SLIDE RIA --> dich chuyen varible trong array
                // id: lay id slide of first array
                // xCur : Lay vi tri of last array + wslide
                // tf: vi tri thanh tranform
                id   = va.idMap[a.id0];
                xCur = va.pBegin[a.idN] + (w * a.s);


                // Gia tri Transform cho truong hop view
                var tf = {};
                if( va.view == 'basic' || va.view == 'mask' ) {
                    var tl = is.dirsHor ? 'tlx' : 'tly';            // Translate bang css3
                    tf[cssTf] = M[tl](xCur);
                }
                else if( va.view == 'coverflow' ) {

                    // Setup transform cua slide ria
                    tf = VIEW.transform1(xCur, - o.coverflow.rotate * a.s);
                    tf['z-index'] = va.zMap[a.idN]-1;
                }
                else if( va.view == 'scale' ) {

                    // Setup transform cua slide ria
                    tf = VIEW.transform2(xCur, o.scale.intensity/100);
                }



                // Update gia tri trong namespace
                var aIS = a.is;
                M.shift(va.idMap, aIS);
                M.push(va.idMap, id, aIS);

                M.shift(va.pBegin, aIS);
                M.push(va.pBegin, xCur, aIS);

                M.shift(va.tfMap, aIS);
                M.push(va.tfMap, tf, aIS);



                // Setup transition cua slide ria khi Code chi co 3 SLIDES
                // Neu khong thi loai bo transtion
                var ts = {}, slEdge = va.$s.eq(id);
                if( va.view != 'basic' && num == 3 ) {

                    // Xoa bo timer clear transition(neu co) truoc khi assign transition
                    clearTimeout(slEdge.data('timer'));

                    ts = M.ts(cssTf, a.speed, va.ease);
                    slEdge.data('timer', setTimeout(function() { M.tsRemove(slEdge) }, a.speed));
                }
                else ts[cssTs] = '';


                // Assign transition va transform moi vua setup vao slide can di chuyen
                slEdge.css(ts).css(tf);

                // UPDATE SLIDE CHINH GIUA VA CANVAS
                var balanceName = 'balance'+ va.View;
                !!VIEW[balanceName] && VIEW[balanceName](a);
            }
        },

        /**
         * COPY SLIDES VAO CHO TRONG KHI DI CHUYEN BANG PAGINATION
         * @purpose
         *  + Khi di chuyen bang pagination --> slide o vi tri edge tu dong di chuyen de tat ca slide can bang
         *  + Khi do xuat hien khoang trang do slide edge di chuyen --> copy slide edge giu nguyen vi tri --> sau time translate thi loai bo slide da copy.
         */
        fillHole : function() {
            // Dieu kien thuc hien function
            if( va.view != 'basic' || !is.loop ) return;

            // Kiem tra slideClone - remove
            va.$slClone.length && va.$slClone.remove();


            // Kiem tra clone slide hay ko
            // Khi pagination ma chi di chuyen slide an sau Viewport thi khong can thiet clone slide.
            var center   = va.center,
                nMove    = (va.nMove > 0) ? center.nLeft : center.nRight,
                nMin     = nMove - center.nEdge,
                nMoveAbs = M.a(va.nMove);

            if( nMoveAbs > nMin ) {

                // clone slide - chi clone slide nhin thay
                // -> id get tu nMin
                for( i = nMin; i < nMoveAbs; i++ ) {

                    // Copy slide roi append vao Canvas
                    // Loai bo class 'Cur' neu co tren Slide Clone
                    var id = (va.nMove > 0) ? va.idMap[i] : va.idMap[num - 1 - i],
                        sl = va.$s.eq(id).clone().removeClass(va.ns + o.current).appendTo($canvas);

                    // Add slide vua moi clone vao bien --> remove toan bo slide clone sau khi di chuyen xong
                    va.$slClone = va.$slClone.add(sl);
                }

                // Xoa bo tat ca slide clone sau khi transition ket thuc
                clearTimeout(ti.fillHole);
                ti.fillHole = setTimeout(function() {

                    va.$slClone.remove();
                }, va.speed[cs.idCur] + 10);
            }
        },




        /**
         * SETUP CHUYEN DONG REBOUND KHI TAP VAO NAVIGATION KHONG CHO DI CHUYEN
         */
        animRebound : function(dirs) {
            if( !o.isAnimRebound ) return;

            // Bien shortcut va khoi tao ban dau
            var p      = va.can,
                layout = va.layout,
                isNext = dirs == 'next',
                sign   = isNext ? -1 : 1,

                tSpeed = 150,                           // Thoi gian chuyen dong
                plus   = 30,                            // x plus value, unit px
                xBack  = isNext ? p.xMax : p.xMin,      // Vi tri ban dau cua Canvas
                xLimit = 130 * sign + xBack;            // Vi tri gio han de Canvas quay tro lai --> +/-130px



            /**
             * LAY GIA TRI CUA VI TRI HIEN TAI -> HO TRO LAY VI TRI CANVAS DI CHUYEN
             */
            var xCur = $canvas.css(cssTf);
            if( is.ts ) xCur = (xCur == 'none') ? xBack : M.valueX(xCur);
            else        xCur = (xCur == 'auto') ? xBack : M.pInt(xCur);
            


            /**
             * SETUP ANIMATION CHO CANVAS
             */
            var xGo = plus * sign + xCur,

                // Function chuyen dong Go va Back
                fnGo   = function() { POSITION.xAnimate(null, xGo, 0, 1, tSpeed) },
                fnBack = function() { POSITION.xAnimate(null, xBack, 0, 1) };

            /* xGo: limited value
                --> khi Canvas di chuyen vuot qua gioi han cho phep
                --> Canvas di chuyen ve vi tri ban dau */
            if( xGo/sign > xLimit/sign ) {
                fnBack();
            }

            // /* Animate run
            //     --> Se cho Canvas di 1 doan --> setup timer de quay tro lai */
            else {
                fnGo();
                clearTimeout(ti.rebound);
                ti.rebound = setTimeout(fnBack, tSpeed);
            }
        },

        /**
         * SETUP TIEP TUC DI CHUYEN KHI NGUNG SWIPE - BANH DA`
         */
        flywheel : function() {
            var isCanvas = $canvas.is(va.$swipeCur),
                p        = isCanvas ? va.can : va.pag;


            // Di chuyen cho pagination truoc
            if( !isCanvas ) {

                /**
                 * DIEU KIEN DE BA'NH DA` DI CHUYEN:
                 *  + O trong pham vi Viewport
                 *  + Thoi gian swipe nho hon 200ms
                 *  + Di chuyen tam thoi phai lon hon 1 sTranslate --> truong hop slide chinh
                */
                var tDrag      = va.tDrag1 - va.tDrag0,
                    isContinue = (va.xBuffer < 0 && va.xBuffer > p.xMax) && (tDrag < 200) && (M.a(va.xOffset) > 10);
                if( !isContinue ) return;


                /**
                 * TIEP TUC THUC HIEN FUNCTION
                 */
                var xOff    = va.pageX1 - va.x0Fix,     // khoang cach swipe duoc --> lay dung gia tri thay vi xOffset
                    xTarget = va.xBuffer + xOff,

                    /**
                     * wLimit : Khoang cach gioi han
                     *  + Ho tro kiem tra tiep tuc co flywheel hay khong --> khoang cach giua x[0], x[1] > wLimit
                     *  + Ho tro di chuyen pag thang toi vie`n neu' thieu' khoang cach wLimit
                     */
                    wLimit = 50;

                // Truong hop vi tri can di chuyen toi cach' vien 1 khoang cach wLimit
                if     ( xTarget + wLimit > 0 )      xTarget = 0;
                else if( xTarget - wLimit < p.xMax ) xTarget = p.xMax;

                // Setup translate cho pagination
                PAG.translateTo(xTarget);
            }
        },




        /**
         * DI CHUYEN CANVAS TOI VI TRI LUC BAN DAU
         *  + Code center: xCanvas da co gia tri --> func() chi de update gia tri tren Canvas
         *  + Loai bo transition khi update
         */
        canvasBegin : function() {

            /**
             * VI TRI BAN DAU CUA CANVAS
             * @param int xCanvas
             *  + Sau khi resize --> Canvas va slide deu reset lai position --> xCanvas cung reset lai
             *  + Code center --> xCanvas: tinh toan vi tri lui` lai cua Canvas
             */
            var layout = va.layout,
                p      = va.can,
                xBegin = 0;

            // Vi tri bat dau o layout Line
            if( layout == 'line' && is.center ) {
                var sSlideCur = is.dirsHor ? va.wSlide
                                           : va.$s.eq(cs.idCur).outerHeight(true);

                xBegin = M.r( (va.sCode - sSlideCur)/2 );
            }

            // Update vi tri bat dau cua Canvas
            p.xCanvas = xBegin;



            /**
             * VI TRI GIOI HAN CUA CANVAS -> SWIPE BUFFER BI GIAM TI LE
             * @param int xMin
             * @param int xMax
             */
            if( layout == 'dot' )
                p.xMin = p.xMax = 0;

            else if( layout == 'line' ) {
                // Vi tri toi thieu cua Canvas
                p.xMin = xBegin;

                // Kich thuoc To?ng co.ng cua tat ca cac Slide
                // Dong thoi loai bo? Margin left cua Item dau` va Margin right cua Item cuoi'
                var sSlideAll = M.sum(va.sSlideMap) - (va.ma[0] + va.ma[1]);

                // Vi tri Toi da cua Canvas
                if( va.wCode < sSlideAll )
                    p.xMax = - (sSlideAll - va.wCode + xBegin);
                else
                    p.xMax = xBegin;
            }



            /**
             * DI CHUYEN CANVAS TOI VI TRI DA SLIDE HIEN TAI
             */
            va.$swipeCur = $canvas;
            M.tsRemove($canvas, true);

            if( is.loop ) POSITION.xAnimate(null, xBegin, true, true);
            else          POSITION.xAnimate(null, cs.idCur, true);
        }
    },







    /**
     * SIZES
     */
    SIZE = {

        /**
         * LAY VALUE MARGIN
         * @param array va.ma Gia tri thu 1 la 'left', thu 2 la 'right'
         */
        margin : function() {

            /**
             * KIEM TRA VA LAY GIA TRI CUA MARGIN PHU HOP
             */
            var wMin   = 1e5,
                id     = null,
                margin = va.maRange,
                wCode  = va.wCode,
                wWin   = $w.width();

            if( !!va.maRange ) {
                for( i = margin.num - 1; i >= 0; i-- ) {

                    // Tim kiem doi tuong co gia tri nam` trong gioi ha.n
                    // Uu tien cho doi tuong co gia tri 'to' nho nhat
                    if( margin[i].from <= wWin && wWin <= margin[i].to ) {
                        if( wMin >= margin[i].to ) {

                            wMin = margin[i].to;
                            id   = i;
                        }
                    }
                }
            }

            // Lay gia tri cua margin
            // Ho tro gia tri cua Margin theo ti le %
            if( id != null )
                va.ma = [ M.pPercent(margin[id].left, wCode), M.pPercent(margin[id].right, wCode) ];
            else
                va.ma = [0, 0];



            /**
             * TU DONG LAY MARGIN KHI VIEWPORT CO PADDING
             * Fixed khi Viewport co CSS styled -> nhin thay cac slide tren Viewport
             */
            if( !va.maRange ) {
                if( is.dirsHor && va.wCode != $viewport.innerWidth() ) {
                    va.ma[0] = M.pInt($viewport.css('padding-left'));
                    va.ma[1] = M.pInt($viewport.css('padding-right'));
                }

                if( !is.dirsHor && va.hCode != $viewport.innerHeight() ) {
                    va.ma[0] = M.pInt($viewport.css('padding-top'));
                    va.ma[1] = M.pInt($viewport.css('padding-bottom'));
                }
            }
        },

        /**
         * KICH THUOC BAO GOM MARGIN CUA SLIDE
         * @param int va.wSlideFull
         * @param int va.can.sTranslate
         */
        sTranslate : function() {

            // Lay gia tri cua Margin
            SIZE.margin();

            // Assign value
            // Mac dinh wTranlate = wSlideFull --> cac view khac se update gia tri sau
            va.wSlideFull = va.can.sTranslate = va.wSlide + va.ma[0] + va.ma[1];
        },




        /**
         * SETUP CHIEU RONG TRONG CODE
         * @param init va.wCode
         */
        widthForCode : function() {

            /**
             * TABS VERTICAL
             * SETUP MARGIN CHO VIEWPORT -> LAY CHIEU RONG "va.wCode" CHINH XAC
             */
            if( is.pag && !!va.pagVer ) {

                // Neu khong co Kich thuoc Margin thi ti'nh kich thuoc cua Pag Item truoc
                !va.pag.maRight && PAG.getSizeOfItems();
                PAG.marginOnViewport();
            }



            /**
             * KICH THUOC CHIEU RONG CUA CODE
             */
            va.wCode = $viewport.width();



            /**
             * SETUP KICH THUOC WIDTH CUA SLIDE
             * @param int va.wSlide
             */
            // Setup Huo'ng Horizontal
            var wSlide = null;
            if( is.dirsHor ) {

                // Lay gia tri cua Chieu rong cua Slide tu ma?ng Range
                wSlide = M.getValueInRange(va.sSlideRange, 'width');

                // Chuyen doi unit percent sang px, don vi percent trong khoang [0, 1]
                if( wSlide > 0 && wSlide <= 1 ) wSlide *= va.wCode;
            }

            // Setup Huo'ng Vertical
            else {
                wSlide = va.wCode;
            }

            // Lam tro`n gia tri wSlide
            va.wSlide = M.pInt(wSlide);
        },

        /**
         * SETUP CHIEU CAO CHO VIEWPORT DE PHUC VU ANIMATE HEIGHT TRONG HEIGHT-AUTO
         */
        heightLockForAnim : function() {
            
            // Truoc tien setup Chieu cao co dinh hien tai cho Viewport
            $viewport.css('height', $viewport.height());

            // Setup timer de loai bo Chieu cao co' dinh cho Viewport
            clearTimeout(ti.heightLock);
            ti.heightLock = setTimeout(function() {

                $viewport.css('height', '');
            }, o.speedHeight + 10);
        },

        /**
         * SETUP HIEU UNG ANIMATE HEIGHT CHO CODE
         * @param int va.hCode
         */
        animHeightForCode : function(isUpdateResize) {
            var timePlus = 30;

            // Ho tro smoothHeight cho doi tuong Canvas & PagInner
            var fnSmoothHeight = function(height) {

                // Assign value chieu cao cua Code
                // Kich thuoc cua Viewport thay doi theo huong swipe
                va.hCode = height;
                if( !is.dirsHor ) va.sCode = height;


                /**
                 * HIEU U'NG ANIMATION HEIGHT
                 *  + Loai bo hieu u'ng neu speedHeight = null || Resize event
                 */
                if( o.speedHeight === null || isUpdateResize ) {
                    M.scroll.check();
                }

                else {
                    var speedHeight = o.speedHeight - timePlus;

                    // Setup Hieu uo'ng animation
                    $viewport
                        .stop()
                        .rt01Animate({ 'height': height }, {
                            duration : speedHeight,
                            complete : function() {

                                // Da?m ba?o loai bo Chieu cao co' dinh tren Viewport
                                $viewport.css('height', '');

                                // Update gia tri cac bie'n lien quan scroll browser
                                M.scroll.check();
                            }
                        });
                }
            },



            /**
             * KIEM TRA CHIEU CAO THAY DOI TREN VIEWPORT
             */
            fnCheckHeightChange = function() {

                // Lay chieu cao hien tai cua slide current
                var hCur = va.$s.eq(cs.idCur).outerHeight(true);


                // Smooth resize height Code when move to near slide
                // Them options isUpdateResize de luon luon run fnSmoothHeight()
                // Tranh truong hop khi update, va.hCode == hCur --> khong chay smoothHeight()
                if( !is.heightFixed && ((va.hCode != hCur && hCur > 0) || !!isUpdateResize) ) {
                    fnSmoothHeight(hCur);


                    /**
                     * UPDATE CAC GIA TRI CUA PAG VER KHI THAY DOI CHIEU CAO
                     *  + Smooth height cho pagination chieu huong vertical
                     */
                    if( is.pag && !is.pagList && va.pag.dirs == 'ver' && !is.outsidePag && o.pag.sizeAuto == 'full' ) {
                        PAG.propAndStyle();
                    }
                }
            };



            /**
             * FUNCTION SELECT
             * Setup timer cho animHeightForCode --> THAY DOI CHIEU CAO SAU CUNG
             * >= 30 ms --> layout DOT khi toggle class 'hNative' can delay cho old browser ???
             */
            setTimeout(fnCheckHeightChange, timePlus);
        },




        /**
         * SETUP CHIEU CAO CUA HEIGHT AUTO KHI LOAD XONG SLIDE DAU TIEN
         * @param int va.hCode
         */
        heightAutoForCode : function(hSlide) {

            // Luu tru va setup Chieu cao luon luon la so nguyen
            va.hCode = M.pInt(hSlide);
        },

        /**
         * SETUP CHIEU CAO CUA HEIGHT FIXED CHO CODE
         * @param int va.hCode
         */
        heightFixedForCode : function() {

            // Function setup chieu cao cho Viewport
            var fnHeightForViewport = function(h) { $viewport.css('height', h) };


            /**
             * SETUP TRONG CHE DO FULLSCREEN
             */
            if( o.isFullscreen ) {
                var hWin = $w.height();


                /**
                 * SETUP KICH THUOC HEIGHT KHI CO DOI TUONG 'OFFSET'
                 */
                if( o.offsetBy != null ) {
                    var hOffset = 0,
                        isImg   = false;

                    // Lay kich thuoc Chieu cao cua doi tuong Offset
                    var $offset = $(o.offsetBy);
                    $offset.each(function() {
                        hOffset += $(this).outerHeight(true);
                    });

                    // Kiem tra doi thuong 'Offset' co chua Image hay khong
                    if( $offset.find('img').length ) isImg = true;

                    // Height Code will substract by height offsetBy container
                    hWin -= hOffset

                    // Cap nhat lai Vi tri + Kich thuoc Code khi Doi tuong 'Offset' chua Image
                    if( isImg ) $w.load(function() { cs.refresh() });
                }

                va.hCode = hWin;
                fnHeightForViewport(va.hCode);
            }



            /**
             * SETUP BINH THUONG 
             */
            else {

                // Muc do uu tien cua height Code: va.hRes > height css > o.height
                // Assign height Viewport when have height repsonsive
                if( va.hRes ) {
                    va.hCode = M.r(va.hRes * va.rate);
                    fnHeightForViewport(va.hCode);
                }
                else {

                    // Height value in css
                    var h = $viewport.height();

                    // Kiem tra neu set Chieu cao tu option khac chieu cao trong css
                    if( is.heightFixed && h != o.height ) {
                       h = o.height;
                       fnHeightForViewport(h);
                    }

                    if( !h ) h = 0;
                    va.hCode = h;
                }
            }
        },

        /**
         * KICH THUOC CHO CODE SUA KHI BIET GIA TRI WIDTH - HEIGHT
         */
        endOfCode : function() {

            /**
             * KIEM TRA WIDTH CUA CODE THAY DOI THI UPDATE LAI GIA TRI CUA WIDTH-HEIGHT
             */
            if( va.wSlide != $viewport.width() ) {

                // Lay Chieu rong cua Code truoc tien.
                SIZE.widthForCode();
                // Responsive: calculation padding & va.rate
                is.res && RESPONSIVE.updateVars();
                // Lay Chieu cao cua Code truoc tien -> Ho tro. image autofit/autofill
                is.heightFixed ? SIZE.heightFixedForCode()
                               : SIZE.heightAutoForCode( va.$s.eq(cs.idCur).outerHeight(true) );
            }


            /**
             * SETUP CAC BIEN CO LIEN QUA TOI DIRECTION
             */
            // Bien hien thi kich thuoc (width/height) cua Code
            va.sCode = is.dirsHor ? va.wCode : va.hCode;
        }
    },







    /**
     * VIEW
     */
    VIEW = {

        /**
         * FUNCTION TINY
         */
        // Transform Translate ket hop voi Rotate
        transform1 : function(x, ndeg) {

            var con = 'translate3d('+ x.toFixed(1) +'px, 0, 0)';
            if( ndeg != undefined ) con += ' rotateY('+ ndeg.toFixed(1) +'deg)';

            var tf = {}; tf[cssTf] = con;
            return tf;
        },

        // Transform Translate ket hop voi Scale
        transform2 : function(x, nScale) {

            var con = 'translate3d('+ x.toFixed(1) +'px, 0, 0)';
            if( nScale != undefined ) con += ' scale('+ nScale +')';

            var tf = {}; tf[cssTf] = con;
            return tf;
        },



        /**
         * SETUP THUOC TINH KHI RESIZE TRONG FN SIZE
         */
        sizeBasic : function() {

            var pBegin    = va.pBegin    = [],
                sSlideMap = va.sSlideMap = [],
                nBegin    = is.centerLoop ? va.center.nLeft : 0,
                p         = va.can;


            /**
             * LUU TRU VI TRI CUA TUNG SLIDE
             * @param array va.pBegin
             */
            // Kich thuoc mac dinh cua Slide cho huo'ng Horizontal
            if( is.dirsHor ) {
                for( i = 0; i < num; i++ ) {

                    sSlideMap[i] = va.wSlideFull;
                    pBegin[i] = sSlideMap[i] * (- nBegin + i);
                }
            }

            // Kich thuoc cua Slide cho huo'ng Vertical
            else {

                var fnHeightSlideCur = function(id) {
                    return va.$s.eq(id).outerHeight(true) + va.ma[0] + va.ma[1];
                };
                
                // Truong hop Center Loop
                if( is.centerLoop ) {
                    var hTopPlus    = 0,
                        hBottomPlus = 0;

                    // Vi tri cua Phia tren
                    for( i = nBegin; i < num; i++ ) {
                        sSlideMap[i] = fnHeightSlideCur(va.idMap[i]);
                        pBegin[i] = hTopPlus;
                        hTopPlus += sSlideMap[i];   // Vi tri bat dau = 0 -> Phai nam phia duoi
                    }

                    // Vi tri cua Phia duoi
                    for( i = nBegin - 1; i >= 0; i-- ) {
                        sSlideMap[i] = fnHeightSlideCur(va.idMap[i]);
                        hBottomPlus -= sSlideMap[i];
                        pBegin[i]    = hBottomPlus;
                    }
                }

                // Truong hop khong phai Center Loop
                else {
                    for( i = 0; i < num; i++ ) {
                        3[i] = fnHeightSlideCur(i);
                        pBegin[i] = sSlideMap[i] * i;
                    }
                }
            }



            /**
             * SETUP TRANSFORM VI TRI CUA TUNG SLIDE DUA VAO VI TRI DA LUU TRU O TREN
             */
            var isHor     = p.dirs == 'hor',
                translate = isHor ? 'tlx' : 'tly',
                tf        = {},
                id;

            va.tfMap = [];
            for( i = 0; i < num; i++ ) {
                id = is.centerLoop ? va.idMap[i] : i;

                tf[p.cssTf] = M[translate](pBegin[i]);

                va.tfMap.push(tf);          // add vao namespace transform
                va.$s.eq(id).css(tf);       // Dat slide o vi tri dinh san
            }
        },




        /**
         * SETUP VIEW CUA FX FADE
         */
        bufferFade : function(sign) {

            /**
             * XAC DINH SLIDE CURRENT VA NEXT
             */
            var isNextSlide = sign > 0,
                idCur       = cs.idCur,
                idNext;

            // Xac dinh ID cua Slide Next
            if( isNextSlide ) {
                idNext = idCur + 1;
                if( idNext >= num ) idNext = 0;
            }
            else {
                idNext = idCur - 1;
                if( idNext < 0 ) idNext = num - 1;
            }

            // Doi tuong Slide Current va Slide Next
            var $slideCur  = va.$s.eq(idCur),
                $slideNext = va.$s.eq(idNext);



            /**
             * TOGGLE CLASS 'NEXT' TREN SLIDE NEXT CU VA MOI
             */
            var classNext = va.ns +'next';;
            if( cs.idNext != idNext ) {

                // Loai bo class 'next' va 'opacity' cho  Slide Next cu~
                if( $.isNumeric(cs.idNext) )
                    va.$s.eq(cs.idNext).css('opacity', '').removeClass(classNext);

                $slideNext.addClass(classNext);
            }

            // Luu tru lai idNext
            cs.idNext = idNext;
            


            /**
             * SETUP OPACITY TREN SLIDES
             */
            // Ti le xBuffer so voi Chieu dai cua Slide
            var rate = $.easing.easeOutQuad(null, M.a(va.xBuffer), 0, 1, va.wSlide);
            rate = parseFloat(rate.toFixed(3));

            var opacityCur  = 1 - rate,
                opacityNext = rate;

            $slideCur.css('opacity', opacityCur);
            $slideNext.css('opacity', opacityNext);
        },

        /**
         * PHUC HOI TRANG THAI KHI DUNG SWIPE
         */
        restoreFade : function() {
            
            // Lay idLast tu idNext -> De setup hieu ung Fade
            cs.idLast = cs.idNext;

            // Loai bo class 'next' tren Slide Next
            va.$s.eq(cs.idNext).removeClass(va.ns +'next');
            // Loai bo idNext -> boi vi da~ loai bo class 'next' -> toggle class trong bufferFade()
            cs.idNext = null;

            // Thuc hien hieu u'ng Fade -> Khong di chuyen thi khong thuc hien
            (va.xBuffer != 0) && FX.fade();
        },

        /**
         * RESET HIEU U'NG KHI SWIPE LIEN TUC
         */
        continuityFade : function() {
            
            // Loai bo css Opacity khoi Slide Last
            var $slideLast = va.$s.eq(cs.idLast);
            $slideLast.css({ 'opacity': '' });

            // Setup cac bien ket thuc hieu u'ng
            TOSLIDE.end();
        }
    },







    /**
     * UPDATE SIZE & POSITION IN DIRECTION VERTICAL
     */
    VERTICAL = {

        /**
         * UPDATE KICH THUOC VA VI TRI KHI SLIDE LOADED XONG
         */
        slideLoaded : function() {
            // Kich thuoc cua cac Slide
            VIEW.sizeBasic();

            // Update vi tri cua Canvas lai khi update vi tri cua tung Slide
            if( va.layout == 'line' ) POSITION.canvasBegin();
        }
    },







    /**
     * SWAP TO OTHER SLIDE
     */
    TOSLIDE = {

        /**
         * SETUP KHI BAT DAU DI CHUYEN DEN SLIDE KE TIEP
         */
        run : function(nSlide, isIDFixed, isContinuity, isPagCenter) {
            var nCur = cs.idCur;

            // Kiem tra dieu kien thuc hien fn
            if( !is.lockNav && (!isIDFixed || (isIDFixed && nCur != nSlide)) ) {

                /**
                 * SLIDETO : LUU TRU CAC THUOC TINH LUC BAN DAU
                 */
                va.ts = {
                    'num'          : nSlide,
                    // ID cua Slide truc tiep hay khong
                    'isIDFixed'    : !!isIDFixed,
                    // Swipe lien tuc hay khong
                    'isContinuity' : !!isContinuity,
                    // Mac dinh khong co lam center
                    'isPagCenter'  : (isPagCenter === undefined) ? true : !!isPagCenter
                };



                /**
                 * SETUP CAC BIEN BAN DAU
                 *  + fxRun : ho tro Slideshow + setup Tabs Ver khi Body resize
                 *  + slideNext : di chuyen next hay prev
                 */
                is.fxRun = true;
                $code.addClass(va.ns +'fxRun');

                is.slideNext = isIDFixed ? (nSlide - cs.idCur > 0) : (nSlide > 0);
                cs.ev.trigger('fxBegin');



                /**
                 * SETUP CAC THANH PHAN KHAC TRONG SLIDE DA~ LOADED
                 */
                if( va.$s.eq(nCur).data('isLoaded') ) {

                    // layer.slidePause(nCur);                  // Layer current pause
                    is.VIDEO && VIDEO.slideDeactived(nCur);     // Do'ng lai tat ca? cac Video
                    // map.slideClose(nCur);                    // Map current close
                }

                // Slideshow: setup stop timer khi chay hieu ung chuyen slide
                is.slideshow && SLIDESHOW.go('slideToBegin');



                /**
                 * MAIN SETUP
                 */
                // Callback func: start && before
                isIDFixed ? (nSlide == 0) && cs.ev.trigger('start')
                          : (nCur + nSlide == 0 || nCur + nSlide - num == 0 ) && cs.ev.trigger('start');
                cs.ev.trigger('before');

                // ID: convert to ts.num
                if( isIDFixed ) va.ts.num -= nCur;

                // Easing transition cua Canvas
                var es;
                if     ( va.moveBy == 'swipe' && va.moveLastBy != 'swipe' ) es = o.swipe.easing;
                else if( va.moveBy == 'tap' && va.moveLastBy != 'tap' )     es = o.fxEasing;

                if( es ) {
                    va.ease = M.easeName(es);
                    va.moveLastBy = va.moveBy;
                }

                // Tiep tuc setup tuy theo Layout
                TOSLIDE[va.layout]();
            }
        },




        /**
         * SETUP KE TIEP TRONG LAYOUT 'LINE'
         */
        line : function() {
            var ts = va.ts;

            // Toggle ID current
            TOSLIDE.toggleID();
            !is.heightFixed && SIZE.animHeightForCode();
            // Setup khi slide chay xong effect --> dat vi tri dau cho giong nhau
            clearTimeout(ti.lineEnd);
            ti.lineEnd = setTimeout(TOSLIDE.end, va.speed[cs.idCur]);



            /**
             * DI CHUYEN DOI TUONG CANVAS HUONG HORIZONTAL
             */
            if( is.dirsHor ) {
                if( is.centerLoop ) {

                    // Di chuyen bang Tap Pagination
                    if( ts.isIDFixed ) {
                        POSITION.fillHole();
                    }

                    TOSLIDE.lineTranslate();
                }

                // Setup mac dinh, Di chuyen den doi tuo.ng ke tiep
                else {
                    !ts.isContinuity && POSITION.xAnimate($canvas, ts.num);
                }
            }
            

            /**
             * DI CHUYEN DOI TUONG CANVAS HUONG VERTICAL
             */
            else {
                if( is.centerLoop ) {

                    if( M.a(ts.num) == 1 ) {
                        var id         = ts.num > 0 ? cs.idLast : cs.idCur,
                            hSlideCur  = va.$s.eq(id).outerHeight(true) + va.ma[0] + va.ma[1],
                            xTranslate = - (hSlideCur * ts.num - va.can.xCanvas);

                        // CHUA HOAN THANH --> DUNG TAI DAY
                        POSITION.balance(ts.isContinuity);
                        !ts.isContinuity && POSITION.xAnimate($canvas, xTranslate, false, true);
                    }
                }
            }
        },

        /**
         * TACH CHUYEN DONG THANH NHIEU CHUYEN DONG NHO KHI TAP PAGITEM
         */
        lineTranslate : function() {
            var ts = va.ts,
                n  = M.a(ts.num);

            /**
             * SETUP DANH CHO VIEW KHAC DI CHUYEN LON 1 SLIDE
             */
            if( va.view != 'basic' && n > 1 ) {

                var tOne = ~~(va.speed[cs.idCur] / n),  // Thoi gian di chuyen tung slide
                    t    = 0,
                    sign = ts.num > 0 ? 1 : -1;         // Phan biet 'next' hay 'prev'

                // Function setup transform tung slide
                var fnTranslateOne = function(_time, _es) {
                    setTimeout(function() {

                        // Easing rieng cho tach chuyen dong nay
                        va.ease = M.easeName(_es);
                        va.moveLastBy = 'multi';

                        POSITION.balance(ts.isContinuity, sign, tOne + 100);
                        !ts.isContinuity && POSITION.xAnimate($canvas, sign, 0, 0, tOne + 100);
                    }, _time - 100);
                };

                // Tang thoi gian sau khi set timer
                for( i = 0; i < n; i++, t += tOne) {

                    var es = (i == n-1) ? o.fxEasing : 'linear';
                    fnTranslateOne(t, es);
                }

                // Setup lock swipe va lock TOSLIDE.run() khi thuc function multi run
                is.lockSwipe = is.lockNav = true;
                setTimeout(function() { is.lockSwipe = is.lockNav = false; }, va.speed[cs.idCur]);
            }


            /**
             * SETUP VIEW BASIC HOAC DI CHUYEN 1 SLIDE
             */
            else {

                POSITION.balance(ts.isContinuity);
                !ts.isContinuity && POSITION.xAnimate($canvas, ts.num);
            }
        },

        /**
         * SETUP KE TIEP TRONG LAYOUT 'DOT'
         */
        dot : function() {
            var ts = va.ts;

            // Toggle ID current
            // Them timer khi toggle class --> khac phuc loi nhap' nhay' ban dau khi thuc hien hieu u'ng 'Math'
            if( va.fxType == 'math' ) ts.isDelayWhenToggleID = true;
            TOSLIDE.toggleID();

            // Setup Animation height cho Viewport trong Height-Auto
            !is.heightFixed && SIZE.animHeightForCode();


            // Bien namespace va khoi tao ban dau
            var f = {};
            f.isNext  = ts.num > 0;
            f.$slLast = va.$s.eq(cs.idLast);
            f.$slCur  = va.$s.eq(cs.idCur);

            // FxFunc run setup
            if( ts.isContinuity && va.view == 'fade' )
                VIEW.continuityFade();
            else
                FX.init(f);
        },




        /**
         * CHUYEN DOI ID HIEN TAI VOI ID LAST
         */
        toggleID : function() {

            /**
             * SETUP GIA TRI CHIEU CAO CHO VIEWPORT TRONG HEIGTH-AUTO DE TAO RA HIEU UNG ANIMATE HEIGHT
             *  + Loai bo hieu ung neu speedHeight = null
             */
            !is.heightFixed && (o.speedHeight !== null) && SIZE.heightLockForAnim();




            /**
             * THAY DOI GIA TRI CUA ID CURRENT VA LAST
             */
            var ts    = va.ts,
                idCur = cs.idCur,
                // Luu tru so Slide di chuyen
                nMove = va.nMove = ts.num;

            // Luu tru idLast va cap nhat id current
            // idLast2 --> Ho tro loai fx css khi swap slide lien tiep
            cs.idLast2 = cs.idLast;             
            cs.idLast  = idCur;


            // idCur return value when out range [0, num]
            idCur += nMove;
            if( is.loop ) {
                if(      nMove < 0 && idCur < 0 )    idCur = num-1;
                else if( nMove > 0 && idCur >= num ) idCur = 0;
            }

            // ID current chuyen sang id moi
            // Ket hop voi event swapID
            cs.ev.trigger('beforeSwapIDCur');
            cs.idCur = idCur;
            cs.ev.trigger('afterSwapIDCur');


            // Them timer cho hieu u'ng layout dot : Browser Chrome bi. loi -> slide shake
            // Neu them delay thi trong M.toggleSlide() --> su dung phuong phap cu: va.$s.not($slCur).removeClass(current)
            // --> vi trong may yeu', setTimeout co the bi bo? qua neu click lien tuc
            if( !!ts.isDelayWhenToggleID ) setTimeout(M.toggleSlide, 10);
            else                           M.toggleSlide();




            /**
             * SETUP PAG TABS ITEM CURRENT DI CHUYEN TOI VI TRI CHINH GIUA
             * Dieu kien :
             *  + Chi di chuyen chinh giua ki swipe tren body Code
             *  + Khi Tap tren Pag Item
             *  + Tabs Ver luon luon thuc hien function nay`
             */
            if( is.pag && !is.pagList && ts.isPagCenter
            &&  (va.moveBy == 'swipe' || (va.moveBy == 'tap' && o.pag.isItemCurCenterWhenTap) || va.pag.dirs == 'ver') ) {

                // Boi vi posCenter cho Tabs Ver luon cap nhat thuoc tinh PAG.propAndStyle --> isForce = true : khong chuyen dong sai vi tri'
                var isForceTf = (va.pag.dirs == 'ver') ? true : false;
                PAG.posCenterForItemCur(isForceTf);
            }
        },


        /**
         * SETUP KHI KET THUC HIEU U'NG
         */
        end : function() {

            // Setup thong bao ket thuc hieu ung swap slide
            is.fxRun = false;
            $code.removeClass(va.ns +'fxRun');
            cs.ev.trigger('fxEnd');

            // Other setup
            cs.ev.trigger('after');                         // Event after()
            cs.idCur == num - 1 && cs.ev.trigger('end');    // Event end()

            // Reset Slideshow khi Tap vao $nav, $pag, drag
            if( is.slideshow ) {
                is.hoverAction = true;

                // Kiem tra Pause slideshow khi co option 'isLoop' false va idCur end
                if( !o.slideshow.isLoop && cs.idLast == num - 1 && cs.idCur == 0 )
                    cs.pause();

                else
                    SLIDESHOW.go('slideToEnd');
            }
        }
    },







    /**
     * EVENTS
     */
    EVENTS = {

        /**
         * SAP XEP VA SETUP CAC EVENTS TRONG CODE
         */
        setup : function() {

            // Tap End Event tren Document
            // EVENTS.tapEndOnDocument();

            // Event Navigation va Pagination
            is.NAV && NAV.eventTap();
            is.PAG && PAG.eventTap();
            
            // Event Keyboard
            EVENTS.keyboard();

            // Event Wheel va Mousewheel cho Viewport va PagInner
            EVENTS.wheel({ '$wheel' : $viewport, 'direction' : va.can.dirs, 'optWheel' : o.wheel });
            is.PAG && EVENTS.wheel({ '$wheel' : va.$pag, 'direction' : va.pag.dirs, 'optWheel' : o.pag.wheel });

            // Event Deeplinking
            is.DEEPLINKING && DEEPLINKING.events();

            // Event Window thay doi kich thuoc
            EVENTS.resize();
        },

        /**
         * LAY DUNG' EVENT GIUA EVENT MOUSE - TOUCH - SWIPE
         */
        getEventRight : function(e) {
            var i = e;
            if( /^touch/.test(e.type) )        i = e.originalEvent.touches[0];
            else if( /pointer/i.test(e.type) ) i = e.originalEvent;
            return i;
        },

        /**
         * CHUYEN DOI CAC EVENT THANH TYPE MOUSE - TOUCH - POINTER
         */ 
        // getType : function(type) {
        //     if     ( /^mouse/.test(type) ) return 'mouse';
        //     else if( /^touch/.test(type) ) return 'touch';
        //     else if( /pointer/i.test(type) ) return 'pointer';
        //     return null;
        // },





        /**
         * SETUP TIMER DE LOAI BO 2 HANH DONG EVENT 'CLICK' 'SWIPEEND' CUNG LUC
         */
        delayToTapNext : function() {
            is.tapEnable = false;
            setTimeout( function() { is.tapEnable = true }, 10);
        },

        /**
         * LUU TRU VI TRI BAT DAU SWIPE TREN DOI TUONG
         */
        // tapBegin : function($obj) {
        //     var evStart = va.ev.mouse.start +' '+ va.ev.swipe.start;
            
        //     // Dang ki Event Start cho tung doi tuong
        //     $obj.off(evStart).on(evStart, function(e) {
        //         var $item = $(this);

        //         if( !va.typeTapCur ) {
        //             var i = EVENTS.getEventRight(e);

        //             // Luu tru vi tri va doi tuong Tap hien tai
        //             va.$tapCur = $item;
        //             va.xTapStart  = i.pageX;
        //             va.yTapStart  = i.pageY;;
        //             va.typeTapCur = EVENTS.getType(e.type);
        //         }
        //     });
        // },

        /**
         * KIEM TRA CO PHAI TAP EVENT TREN DOI TUONG
         */
        // checkTap : function($obj, e) {
        //     var radius = 20;

        //     /**
        //      * KIEM TRA DOI TUONG TAP HIEN TAI
        //      *  + typeTapCur : loai bo? event mouse + swipe thuc hien dong thoi
        //      */
        //     if( !(  va.typeTapCur && va.typeTapCur == EVENTS.getType(e.type) &&
        //             va.$tapCur && va.$tapCur.is($obj) ) ) {
        //         return false;
        //     }



        //     /**
        //      * SO SANH VI TRI CUOI VA` VI TRI BAT DAU CUA DOI TUONG
        //      */
        //     var i      = EVENTS.getEventRight(e);
        //     var xStart = va.xTapStart,
        //         yStart = va.yTapStart,
        //         xEnd   = i.pageX,
        //         yEnd   = i.pageY,
        //         isTapEnable = false;

        //     // Kiem tra co phai Tap event
        //     if( xStart - radius <= xEnd && xEnd <= xStart + radius &&
        //         yStart - radius <= yEnd && yEnd <= yStart + radius ) {
        //         isTapEnable = true;
        //     }

        //     va.typeTapCur = null;
        //     return isTapEnable;
        // },

        /**
         * SETUP TAP EVENT TREN DOCUMENT --> RESET VALUE
         */
        // tapEndOnDocument : function() {
        //     var evName = va.ev.mouse.end +'Tap'+' '+ va.ev.swipe.end +'Tap';
        //     $(document).on(evName, function(e) {

        //         clearTimeout(ti.tapEnd);
        //         ti.tapEnd = setTimeout(function() {
        //            if( !!va.typeTapCur ) {
        //                 va.$tapCur = $();
        //                 va.typeTapCur = null;
        //             } 
        //         }, 10);
        //     });
        // },





        /**
         * NAVIGATION EVENTS
         */
        prevCore : function(step) {
            va.moveBy = 'tap';
            if( is.loop || (!is.loop && cs.idCur > 0) ) TOSLIDE.run(-step);
            else                                        POSITION.animRebound('prev');
        },
        nextCore : function(step) {
            va.moveBy = 'tap';
            if( is.loop || (!is.loop && cs.idCur < num-1) ) TOSLIDE.run(step);
            else                                            POSITION.animRebound('next');
        },
        prev : function() {
            if( is.tapEnable ) {
                var step = o.stepNav;

                EVENTS.prevCore(step);
                EVENTS.delayToTapNext();
            }
            
            return false;
        },
        next : function(isSlideshow) {
            if( is.tapEnable ) {
                // Setup bao nhieu buoc
                var step = isSlideshow ? o.stepPlay : o.stepNav;

                EVENTS.nextCore(step);
                EVENTS.delayToTapNext();
            }

            return false;
        },




        /**
         * EVENT CHUYEN DOI SLIDE BANG PHIM KEYBOARD
         */
        keyboard : function() {
            $doc.off(va.ev.key);

            if( o.isKeyboard ) {
                $doc.on(va.ev.key, function(e) {

                    // Check slideInto
                    M.scroll.check(true);
                    if( is.into ) {

                        var keycode = e.keyCode;
                        if     ( keycode == 37 ) EVENTS.prevCore(1);
                        else if( keycode == 39 ) EVENTS.nextCore(1);
                    }
                });
            }
        },

        /**
         * EVENT CHUYEN DOI SLIDE BANG WHEEL EVENT
         */
        wheel : function(opts) {
            var suffix         = '.'+ va.ns + va.codekey,
                nameWheel      = 'wheel'+ suffix,
                nameMosuewheel = 'mousewheel'+ suffix,
                $wheel         = opts.$wheel;


            /**
             * DIEU KIEN THUC HIEN FUNCTION
             */
            if( !opts.$wheel ) return;



            /**
             * TRUOC TIEN SETUP DATA WHEEL VA LOAI BO EVENT WHEEL TREN DOI TUONG
             */
            // Loai bo event Wheel tren doi tuo.ng
            $wheel.off(nameWheel +' '+ nameMosuewheel);

            // Setup data Wheel cua Doi tuo.ng
            if( !$wheel.data('wheel') ) $wheel.data('wheel', { 'type': null, 'delta': 0 });
            var wheelData = $wheel.data('wheel');




            /**
             * FUNCTION CLASS DI CHUYEN DEN SLIDE KE TIEP
             */
            var fnGotoNextSlide = function(deltaX, deltaY) {

                var wheelDelta = wheelData.delta,
                    isScrollPagePrevent = false,

                    // Function class setup wheel delta hien tai
                    fnDeltaPlus = function(deltaCur, rate) {
                        if( deltaCur != 0 && deltaCur != undefined ) {
                            if( rate === undefined ) rate = 1;

                            wheelDelta += deltaCur > 0 ? 1 * rate : -1 * rate;
                            isScrollPagePrevent = true;
                        }
                    };


                /**
                 * SETUP CAC GIA TRI DELTA TUY THUOC THEO OPTIONS
                 */
                if( opts.optWheel == 'auto' ) {

                    // Truong hop Direction Horizontal
                    if( opts.direction == 'hor' ) fnDeltaPlus(deltaX, 0.2);

                    // Thuong hop Direction Vertical
                    else fnDeltaPlus(deltaY);
                }

                else if( opts.optWheel == 'both' ) {
                    var delta = deltaX || deltaY;
                    fnDeltaPlus(delta);
                }



                /**
                 * KIEM TRA DI CHUYEN TOI SLIDE KE TIEP
                 *  + Wheel 2 la`n moi duoc phep di chuyen toi vi tri Slide ke tiep
                 */
                if     ( wheelDelta <= -2 ) { EVENTS.prevCore(1); wheelDelta = 0; wheelData.type === null; }
                else if( wheelDelta >= 2 )  { EVENTS.nextCore(1); wheelDelta = 0; wheelData.type === null; }

                // Luu tru cac gia tri cua Wheel event tren data
                wheelData.delta = wheelDelta;
                // Tra ve gia tri co ngan ca?n Scroll Page hay khong
                return isScrollPagePrevent;
            };




            /**
             * CAU TRUC CUA EVENT WHEEL GIUA WHEEL NATIVE VA` WHEEL PLUGIN
             */
            if( o.wheel != false ) {
                $wheel.on(nameMosuewheel +' '+ nameWheel, function(e) {
                    var typeCur = e.type,
                        events  = e.originalEvent;

                    // Trun`g ten event Wheel moi setup tiep tuc
                    if( wheelData.type === null || wheelData.type == typeCur ) {

                        // Setup Type wheel hien tai neu da~ loai bo
                        if( wheelData.type === null ) wheelData.type = typeCur;

                        var deltaX = e.deltaX || events.deltaX,
                            deltaY = e.deltaY || events.deltaY;

                        // Kiem tra di chuyen toi Slide ke tiep
                        var isScrollPagePrevent = fnGotoNextSlide(deltaX, deltaY);

                        // Ngan chan khong scrollPage khi Wheel
                        if( isScrollPagePrevent ) return false;
                    }
                });
            }
        },

        /**
         * EVENT CAP NHAT LAI CODE SAU KHI DA LOADED TAT CA IMAGE
         */
        loadAll : function() {

            /**
             * FUNCTION KIEM TRA GIA TRI 'RATE' THAY DOI
             *  + Cap nhat lai Code neu Rate luc Init khac voi Rate hien tai
             */
            var fnCheckRate = function() {
                is.res && va.rateInit != va.rate && cs.refresh();
            };


            /**
             * THUC HIEN EVENT
             */
            cs.ev.on('loadAll', function() {
                fnCheckRate();
            });
        },




        /**
         * EVENT CAP NHAT LAI CODE SAU KHI BROWSER RESIZE
         */
        resize : function() {
            
            // Bien shortcut va khoi tao ban dau
            var fnCheck = function() {

                clearTimeout(ti.resize);
                ti.resize = setTimeout(function() {

                    // Fullscreen: find height page first
                    if( o.isFullscreen ) va.hCode = $w.height();
                    // Update cac bien lien quan scroll Browser
                    is.slideshow && !is.ssPauseAbsolute && M.scroll.check();

                    // Code: toggle showInRange
                    !!o.showInRange && is.SHOW && SHOW.toggle();

                    // Reupdate Code: when show/hide scroll-bar browser
                    if( is.showInRange && (($viewport.width() != va.wCode) || ($viewport.height() != va.hCode)) ) {
                        UPDATE.resize();
                    }
                }, 100);
            };

            // Resize: event
            $w.off(va.ev.resize);
            $w.on(va.ev.resize, fnCheck);




            /**
             * !IMPORTANT
             *  + Them event kiem tra 'div' resize
             *  + Thay the cho cac function:
             *      - Code nested khi khoi tao phai can phai resize
             *      - EVENTS.CodeLoaded() --> Code da loaded cac hinh anh can phai resize
             *      - EVENTS.pageLoaded() --> Trang web loaed cac noi dung bao gom font can phai resize
             *      - EVENTS.reCheck() --> loai bo reCheck trong 'event resize' va` hieu ung animate trong hieghtCode()
             *
             *  + Luy y: Neu va.wCode gio'ng nhu wCur, co the them bie'n moi va.wCodeResizeloop de so sanh wCur
             */
            clearInterval(ti.resizeLoop);
            ti.resizeLoop = setInterval(function() {

                var hCur = va.$s.eq(cs.idCur).outerHeight(true),
                    wCur = $viewport.width();

                // console.log(hCur, va.hCode, wCur, va.wCode);
                if( !is.fxRun && (wCur != va.wCode || hCur != va.hCode) ) {

                    // console.log('resize loop', hCur, va.hCode, wCur, va.wCode);
                    UPDATE.resize();
                }
            }, 1000);
        }
    },







    /**
     * EFFECTS
     */
    FX = {

        /**
         * PHAN LOAI HIEU U'NG LUC BAN DAU
         *  + Hieu ung 'math'
         *  + Hieu ung 'css'
         *  + Hieu ung 'fade'
         */
        init : function(f) {
            var fxType  = va.fxType;

            // Hieu u'ng Math
            if( fxType == 'math' && is.FXMATH ) FXMATH.check(f);

            // Hieu u'ng CSS
            else if( /css/g.test(fxType) && is.FXCSS ) FXCSS.setup();

            // Hieu u'ng Fade
            else if( fxType == 'fade' ) FX.fade();
            else                        FX.none();
        },

        /**
         * SETUP CAC BIEN O CUOI
         */
        end : function(speedCur) {

            /**
             * FUNCTION THUC HIEN CAC BUOC CUOI CUNG CUA HIEU UNG
             */
            var fnSetupEnd = function() {

                // Setup cho hieu ung Math
                if( va.fxType == 'math' ) {
                    !!va.$fxSlCur && va.$fxSlCur.css('visibility', '');
                    !!va.$fxOverlay && va.$fxOverlay.remove();
                }
                TOSLIDE.end();
            };


            /**
             * LUA CHON
             */
            // Truong hop khong co Timer
            if( speedCur === null ) fnSetupEnd();

            // Truong hop co Timer
            else {
                if( !$.isNumeric(speedCur) ) speedCur = va.speed[cs.idCur];

                // Setup timer de thuc hien
                clearTimeout(ti.fxEnd);
                ti.fxEnd = setTimeout(fnSetupEnd, speedCur);
            }
        },




        /**
         * HIEU UNG FADE
         * Hieu ung fade bang jQuery --> ho tro cho hieu ung custom cho old browser
         */
        fade : function(isFallback) {
            var idCur      = cs.idCur,
                $slideCur  = va.$s.eq(idCur),
                $slideLast = va.$s.eq(cs.idLast),
                styleEnd   = { 'opacity': '', 'visibility': '' };


            /**
             * SETUP CAC SLIDE LUC BAN DAU
             */
            // Loai bo class 'next' tren tat ca Slide - neu co
            va.$s.removeClass(va.ns +'next');

            // Quan trong
            // Reset gia tri 'xBuffer' neu thuc hien hieu u'ng bang event 'tap'
            if( va.moveBy == 'tap' ) va.xBuffer = 0;

            // Thoi gian Animation Fade dua theo xBuffer
            var wRate    = M.a(va.xBuffer) / va.wSlide,
                speedCur = ~~( (1 - wRate) * va.speed[idCur] );

            // Thoi gian Truong hop fallback hieu u'ng
            if( isFallback ) speedCur = 300;



            /**
             * SETUP ANIMATE FADE TREN TUNG SLIDE
             */
            var fnAnimateFade = function($slide, isCur) {

                var opacityCur    = $slide.css('opacity'),
                    visibilityCur = $slide.css('visibility'),
                    opacityBegin  = opacityCur,
                    opacityEnd    = isCur ? 1 : 0;

                // Opacity ban dau cua Slide neu Tap ba`ng Nav & Pag
                if( va.moveBy == 'tap' ) opacityBegin = isCur ? 0 : 1;

                // Setup hieu u'ng fade tren Slide
                $slide
                    .stop(true)
                    .css({ 'opacity': opacityBegin, 'visibility': 'visible' })
                    .animate({
                        'opacity': opacityEnd
                    },{
                        // Hieu ung css fallback thi 250, con hieu ung fade chi dinh thi lay thoi gian cua slide
                        duration : speedCur,
                        easing   : 'easeOutQuad',
                        complete : function() {
                            $slide.css(styleEnd);
                        }
                    });
            };
            fnAnimateFade($slideLast, false);
            fnAnimateFade($slideCur, true);



            /**
             * LOAI BO STYLE TREN SLIDE LAST CUA LAST CU~
             * Dieu kien : idLast 2 != idCur
             */
            var idLast2 = cs.idLast2;
            if( idLast2 != undefined && idLast2 != idCur ) {
                va.$s.eq(idLast2).stop(true).css(styleEnd);
            }



            /**
             * SETUP OTHER
             */
            FX.end(speedCur);
        },

        /**
         * HIEU UNG 'NONE'
         */
        none : function() {

            TOSLIDE.end();
        }
    },







    /**
     * API BASIC
     */
    API = {

        /**
         * NHUNG API METHOD CO BAN TRONG CODE
         */
        // Method navigation
        prev : function() { EVENTS.prev() },
        next : function() { EVENTS.next() },
        first: function() { TOSLIDE.run(0, true) },
        last : function() { TOSLIDE.run(num - 1, true) },
        goto : function(id) {
            if( typeof id == 'string' ) id = va.IDsOnDom.indexOf(id);
            if( id >= 0 && id < num )   TOSLIDE.run(id, true);
        },


        // Lenh ve slideshow
        play  : function() { is.slideshow && SLIDESHOW.api('play'); },
        pause : function() { is.slideshow && SLIDESHOW.api('pause'); },
        stop  : function() { is.slideshow && SLIDESHOW.api('stop'); },


        // Method update properties
        update : function(options, isNoRefresh) {

            // Luu tru option cu va Cap nhat option voi deep level
            one.oo = oo = $.extend(true, {}, o);
            o = $.extend(true, o, options);
            va.oUpdate = options;

            // Kiem tra Code co toggle show hay khong
            !!is.awake && !isNoRefresh && cs.refresh();
            va.oUpdate = va.addInfo = null;
        },
        updateOnSlides : function(options) {
            if( !$.isPlainObject(options) ) return;

            va.oSlides = options;
            cs.refresh();
            va.oSlides = null;
        },
        refresh : function() {
            PROP.mergeAllModules();
            UPDATE.removeClass();

            PROP.code();
            PROP.slides();
            LOAD.idMap();
            M.toggleSlide();

            UPDATE.reset();
            UPDATE.resize();


            // Others
            RENDER.other();
            EVENTS.setup();

            is.SLIDESHOW && SLIDESHOW.updateAll();
        },


        // Loai bo Code
        destroy : function(isDelete) {

            // Loai bo SWIPE event
            is.SWIPE && SWIPE.events(false);
                

            // Loai bo event Tap tren navigation va pagination
            var evClick = va.ev.mouse.end +' '+ va.ev.swipe.end +' '+ va.ev.click;
            o.isNav && va.$prev.add(va.$next).off(evClick);
            o.isPag && va.$pagItem.off(evClick);

            // Loai bo cac event KHAC
            $doc.off(va.ev.key);
            $viewport.off(va.ev.wheel);

            // Loai vong lap va RESIZE event
            clearInterval(ti.resizeLoop);
            $w.off(va.ev.resize);

            // Dung slideshow
            // Loai bo vong lap timer + event scroll
            if( o.isSlideshow ) {
                clearInterval(ti.timer);
                $w.off(va.ev.scroll);
                this.stop();
            }



            // Loai bo toan bo DOM cua Code
            if( !!isDelete ) {

                // Xoa bo data tren code
                $code.removeData(rt01VA.codeName);

                // Loai bo cac thanh co kha nang markup-outside
                !!va.$nav && va.$nav.remove();
                !!va.$pag && va.$pag.remove();
                o.isCap && va.$cap.remove();

                if( o.isSlideshow ) {
                    !!va.$timer && va.$timer.remove();
                    !!va.$playpause && va.$playpause.remove();
                    !!$control && $control.remove();
                }

                $code.remove();
            }
        },
        // Khoi phuc lai Code sau khi 'destroy'
        restore : function() { INIT.load() },




        /**
         * LAY NHUNG BIEN TRONG CODE
         */
        width       : function() { return va.wCode },
        height      : function() { return va.hCode },
        slideLength : function() { return num },
        slideCur    : function() { return va.$s.eq(cs.idCur) },
        slideAll    : function() { return va.$s },
        opts        : function() { return o },
        varible     : function() { return va },
        browser     : function() { return is.browser },
        isMobile    : function() { return is.mobile },
        isTransition : function() { return is.ts },




        /**
         * EVENTS TRIGGER
         *  ['init', 'ready', 'loaded']
         *  ['loadAll', 'loadSlide.id', 'loadBegin', 'loadEnd']
         *  ['resize']
         *  ['start', 'end', 'before', 'after']
         *  ['selectID', 'deselectID', 'swipeBegin', 'swipeEnd', 'fxBegin', 'fxEnd']
         *  ['slideshowPlay', 'slideshowPause', 'slideshowStop'] **
         *  ['beforeSwapIDCur', 'afterSwapIDCur']
         */
        ev : $(divdiv)
    },







    /**
     * OTHERS MODULES OF SCRIPTS
     */
    SWIPE,
    RESPONSIVE,
    NAV,
    PAG,
    CAPTION,
    IMAGE,
    VIDEO,
    IFRAME,
    FXMATH,
    FXCSS,
    SLIDESHOW,
    TIMER,
    SHOW,
    DEEPLINKING,
    COOKIE,
    AJAX,
    APIMORE,
    FULLSCREEN,
    NESTED,
    CLASSADD,
    OLD;




    

    /**
     * INIT CODE BEGIN
     */
    INIT.check();
};







/**
 * KHOI TAO CODE
 *  + Cu phap : var code = $('..').codeName();
 * ========================================================================== */
$.fn[rt01VA.codeName] = function() {
    var args     = arguments,               // args[0] : options, args[1]: value
        codeName = rt01VA.codeName,
        codeData = null;

    // Setup moi doi tuong
    $(this).each(function() {
        var self = $(this),
            code = self.data(codeName);

        // Tham so thu nhat luon luon la object --> de dang kiem tra
        if( args[0] === undefined ) args[0] = {};

        // Truong hop la object: khoi tao Code moi hoac update properties
        if( $.isPlainObject(args[0]) ) {
            // TAO CODE MOI
            if( !code ) new $[codeName](self, args[0]);
            // UPDATE THUOC TINH
            else if( !$.isEmptyObject(args[0]) ) code.prop(args[0]);

            // Luu data cua code
            codeData = self.data(codeName);
        }
        
        // Truong hop con lai: goi truc tiep function --> neu khong co thi bao error
        else {
            try      { code[args[0]](args[1]) }
            catch(e) { !!window.console && console.warn('['+ codeName +': function not exist!]'); }
        }
    });

    // Tra ve data cho doi tuong
    return codeData;
};





/**
 * CODE AUTO INIT
 */
rt01MODULE.AUTOINIT = function($code) {

    $code.each(function() {
        var $self = $(this),
            data  = $self.data(rt01VA.codeData);

        // Chuyen doi string than Json neu jQuery khong ho tro san~
        if( typeof data == 'string' ) data = $.parseJSON(data);

        // Kiem tra bien data co phai la json hay khong
        // --> kiem tra tiep data co doi tuong 'isAutoInit'
        // --> kiem tra tiep co ton tai data Code khong
        ($.isPlainObject(data) && !!data.isAutoInit)
        && !$self.data(rt01VA.codeName)
        && $self[rt01VA.codeName]();
    });
};
$(document).ready(function() { rt01MODULE.AUTOINIT( $('.'+ rt01VA.namespace) ) });






/**
 * JQUERY EASING LITTLE
 */
$.extend(jQuery.easing, {
    easeOutQuad: function (x, t, b, c, d) {
        return -c *(t/=d)*(t-2) + b;
    },
    easeOutQuint: function (x, t, b, c, d) {
        return c*((t=t/d-1)*t*t*t*t + 1) + b;
    },
    easeInCubic: function (x, t, b, c, d) {
        return c*(t/=d)*t*t + b;
    },
    easeOutCubic: function (x, t, b, c, d) {
        return c*((t=t/d-1)*t*t + 1) + b;
    }
});





/**
 * PLUGINS RUBY ANIMATE JQUERY
 */
$.fn.rt01Animate = function(prop, opts) {
    var $self = $(this),
        easingName = opts.easing || 'easeOutQuad',
        timeEnd    = opts.duration,
        fps        = 25,
        timeLoop   = ~~(1000 / fps),
        timeCur    = 0,

        styleBegin = {},
        styleRange = {},
        styleLast  = {},
        
        timer, xCur, cssCur, name, isOverflowOnDom;


    /**
     * FUNCTION CLASS
     */
    var fn = {

        /**
         * SETUP CAC BIEN KHI MOI BAT DAU 
         */
        setupBegin : function() {

            // Tao vong lap de lay het cac thanh phan trong doi tuo.ng
            for( name in prop ) {
                styleBegin[name] = parseFloat( $self.css(name) );
                styleRange[name] = prop[name] - styleBegin[name];
            }
        },

        /**
         * SETUP GIA TRI KHI MOI BAT DAU ANIMATION
         */
        start : function() {
            // Truoc tien loai bo Timer Animation truoc do neu' co
            clearInterval($self.data('rt01Animate'));

            // Che`n style 'overflow' luc dau` -> fixed Browser cu~
            var styleCur = $self.attr('styele');
            isOverflowOnDom = styleCur && styleCur.indexOf('overflow') != -1;
            !isOverflowOnDom && $self.css('overflow', 'hidden');

            // Chay function Start neu co
            !!opts.start && opts.start();
        },

        /**
         * SETUP GIA TRI KHI KET THUC ANIMATION
         */
        complete : function() {
            // Loai bo style 'overflow' khi ket thuc
            !isOverflowOnDom && $self.css('overflow', '');

            // Setup tat ca style target -> phong ngua function khong du'ng gia tri
            $self.css(prop);

            // Loai bo Timer neu thoi gian hien tai lo'n hon Thoi gian End
            clearInterval($self.data('rt01Animate'));
            !!opts.complete && opts.complete();
        },




        /**
         * SETUP CAC GIA TRI TRONG THOI GIAN CU THE LEN DOI TUONG
         */
        styleCur : function() {
            var valuePlus, valueCur, styleCur;
            for( name in prop ) {

                // Tinh toan gia tri hien tai
                valuePlus = Math.round(styleRange[name] * xCur);
                valueCur  = styleBegin[name] + valuePlus;

                // Kiem tra gia tri hien tai voi gia tri qua' khu
                if( valueCur != styleLast[name] ) {

                    // Setup style current vao doi tuo.ng
                    styleCur = {};
                    styleCur[name] = valueCur;
                    $self.css(styleCur);

                    // Luu tru gia tri hien tai vao` doi tuo.ng Style last
                    styleLast[name] = valueCur;
                }
            }
        },

        /**
         * SETUP NHUNG BIEN TRONG 1 LOOP
         */
        loop : function () {
            // Thoi gian hien tai duoc cong them vao
            timeCur += timeLoop;
            // Gia tri chenh lech giua [0, 1] tai thoi gian hien tai
            xCur = $.easing[easingName](null, timeCur, 0, 1, timeEnd);

            // Setup gia tri style cu the tren doi tuo.ng
            fn.styleCur();

            // Setup khi ket thuc Animation
            (timeCur >= timeEnd) && fn.complete();
        },

        /**
         * SETUP KHI KHOI TAO ANIMATION
         */
        init : function() {
            fn.setupBegin();
            fn.start();

            // Neu Thoi gia duration = 0 -> khong can setup vo`ng lap
            if( timeEnd == 0 ) fn.complete();
            else               $self.data('rt01Animate', setInterval(fn.loop, timeLoop));
        }
    };

    // Khoi tao Animation
    fn.init();
};

})(jQuery);







/**
 * MODULE SUPPORT OLD BROWSER
 * ========================================================================== */
(function($) {
    
    // Kiem tra bien module
    if( !window.rt01MODULE ) window.rt01MODULE = {};


    /**
     * NHUNG FUNCTION HO TRO OLD BROWSER
     */
    rt01MODULE.OLD = {
        arrayIndex : function() {

            // Phien ban? rut gon, khong ho tro 'fromIndex'
            Array.prototype.indexOf = function(elt) {
                var len  = this.length >>> 0,
                    from =  0;

                for( ; from < len; from++ ) {
                  if (from in this && this[from] === elt)
                    return from;
                }
                return -1;
            };
        },

        replaceAt : function() {

            // Thay the ki tu bang func substr
            // Mac dinh thay the 1 ki tu, them tuy chon so luong ki tu thay the
            String.prototype.replaceAt = function(_newStr, _index, _nChar) {
                // Mac dinh thay the 1 ki tu
                if( _nChar === undefined ) _nChar = 1;

                return this.substr(0, _index) + _newStr + this.substr(_index + _nChar);
            }
        }
    };
})(jQuery);