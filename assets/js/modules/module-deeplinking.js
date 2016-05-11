/**
 * MODULE DEEPLINKING
 * ========================================================================== */
(function($) {
    
    // Kiem tra bien module
    if( !window.rt01MODULE ) window.rt01MODULE = {};

    /**
     * MODULE DEEPLINGING
     */
    rt01MODULE.DEEPLINKING = {

        /**
         * TRA LAI GIA TRI HASH (ID) TREN DIA CHI TRANG WEB
         */
        hashReturn : function(isIDReturn) {

            var that      = this,
                va        = that.va,
                is        = that.is,
                oDeeplink = that.o.deeplinking,

                // Lua chon prefix Custom va prefix Mac dinh --> uu tien prefix Custom
                prefix0   = oDeeplink.prefixDefault[0] + va.codeID + oDeeplink.prefixDefault[1],
                codeName  = oDeeplink.prefix != null ? oDeeplink.prefix : prefix0,

                reStr     = codeName +'\\d+',
                re        = new RegExp(reStr, 'g'),
                hash      = window.location.hash,
                linkCheck = hash.match(re),
                idReturn;


            // Kiem tra va tra ve id-text cua last slide tren hash
            var fnIDTextOnHash = function() {
                if( !!oDeeplink.isIDConvert ) {
                    for( i = 0; i < va.IDsOnDom.length; i++ ) {

                        var idCur = va.IDsOnDom[i];
                        if( idCur != undefined && hash.indexOf(idCur.toString()) != -1 ) {
                            return i;
                        }
                    }
                }
                return null;
            };



            // Tra lai ket qua ID nhan duoc tu hash
            if( isIDReturn ) {

                // Uu tien doc id-text truoc
                idReturn = fnIDTextOnHash();
                if( idReturn != null ) return idReturn;

                // Neu khong co id-text thi tiep tuc kiem tra theo dang 'codeID_slideID'
                if( linkCheck != null ) {
                    idReturn = that.M.pInt( linkCheck[0].substr(codeName.length) );

                    // Dat lai idBegin neu nho hon idReturn < num
                    if( idReturn < that.cs.num ) return idReturn;
                }
                // Tra lai gia tri null neu khong hop le
                return null;
            }



            // Kiem tra va tra lai gia tri Hash moi' thay the cho hash hien tai
            // 1. Truoc tien kiem tra hashLast co ton tai tren HASH hay khong
            // 2. Setup de lay hashCur
            // 3. Thay the hashLast bang hashCur hoac khong co hashLast thi them hashCur vao HASH
            else {

                var hashCur = null, hashLast = null;


                /**
                 * PHAN 1: LAY HASHLAST
                 * @param string hashLast
                 */
                // Lay id tren hash co trung voi ID-Dom cua cac Slide hay khong
                idReturn = fnIDTextOnHash();
                if( idReturn != null ) hashLast = va.IDsOnDom[idReturn];

                // Neu khong co hashLast id-text thi tiep tuc tim kiem dang 'codeID_slideID'
                if( hashLast === null && linkCheck != null ) hashLast = linkCheck[0];



                /**
                 * PHAN 2: LAY HASHCUR
                 * @param string hashCur
                 */
                var idTextCur = va.IDsOnDom[that.cs.idCur];
                if( !!oDeeplink.isIDConvert && idTextCur != undefined ) {
                    hashCur = idTextCur;
                }
                if( hashCur === null ) {
                    hashCur = !!oDeeplink.isOnlyShowID ? '' : (codeName + that.cs.idCur);
                }



                /**
                 * PHAN 3: CHUYEN DOI GIUA HASHCUR VA HASHLAST
                 * @param string hash
                 */
                // Neu hashLast khong ton tai: cong hashCur vao HASH hien tai
                // HashCur phai khac empty
                if( hashLast === null ) {
                    if( hashCur != '' ) {

                        // Neu khong co hash --> cong them dau '#'
                        // Neu co hash cuo'i cung` --> cong vao tiep theo
                        // Them dau '-' cho multi hash --> de doc hon
                        if( hash == '' )            hash = '#'+ hashCur;
                        else if( hash == '#' )      hash += hashCur;
                        else if( /\+$/.test(hash) ) hash += hashCur;
                        else                        hash += '+'+ hashCur;
                    }
                }

                // Neu hashLast ton tai: thay the hashLast bang hashCur
                else {
                    hash = hash.replace(hashLast, hashCur);
                }

                /**
                 * THANH PHAN CUA HASH NEU O TRONG CAC TRUONG HOP SAU
                 *  + Thay the dau '#+' o dau bang '#'
                 *  + Thay the dau '+' o cuoi bang ''
                 *  + Thay the dau '++' thanh '+'
                 */
                hash = hash.replace(/^#\+/g, '#').replace(/\+$/g, '').replace(/\++/g, '+');

                // Cuoi cung tra lai gia tri Hash
                return hash;
            }
        },



        // Doc id tu link trang page --> di toi slide do
        read : function() {
            var that  = this,
                idCur = that.hashReturn(true);

            if( idCur != null ) {

                // Set lai idCur theo url
                that.cs.idCur = that.va.idBegin = idCur;

                // Update gia tri trong properties: reset lai idCenterMap, loadWay...
                that.PROP.code();
            }
        },


        // Toggle link on browser
        write : function() {

            // Lay gia tri Hash moi tu dia chi trang web
            var that    = this,
                ti      = that.ti,
                hashNew = that.hashReturn(false);



            /**
             * FUNCTION CLASS
             */
            // Function cho phep thay doi Hash 1 lan duy nhat
            var fnHashReset = function() {

                clearTimeout(ti.hashReset);
                ti.hashReset = setTimeout(function() { rt01VA.isStopHashChange = false }, 100);
            },

            // Function thay doi Hash 
            fnHashChange = function() {

                // Ngan hanh dong event 'hashchange' --> tranh lap lai
                rt01VA.isStopHashChange = true;


                /**
                 * SETUP DIA CHI MOI TREN BROWSER
                 *  + Ho~ tro API 'History PushState' --> Khong di chuyen toi DOM
                 */
                if( !!window.history && !!window.history.pushState ) {
                    try      { window.history.pushState(null, null, hashNew); }
                    catch(e) { }
                }
                // Truong hop khong ho tro API 'History PushState' --> Su dung cach thong thuong
                else window.location.hash = hashNew;



                /**
                 * PHUC HOI LAI EVENT HASHCHANGE
                 */
                fnHashReset();
            };



            /**
             * KIEM TRA HASH NEW KHONG TRUNG VOI HASH CU~
             */
            window.location.hash != hashNew && fnHashChange();
        },



        // Event khi hashChange
        events : function() {
            var that = this;

            // Loai bo event roi dang ki lai --> ho tro update
            $(window).off(that.va.ev.hash);
            that.o.isDeeplinking && $(window).on(that.va.ev.hash, function(e) {

                // Ngan browser load lai trang
                e.preventDefault();
                if( !rt01VA.isStopHashChange ) {

                    // Kiem tra hash change co phai la 'Code' hien tai
                    // --> neu phai thi di toi id cua slide
                    var idCur = that.hashReturn(true);
                    if( idCur != null ) that.TOSLIDE.run(idCur, true, false, true);
                }
            });
        }
    };
})(jQuery);