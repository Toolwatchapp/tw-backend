<?php

/*
 * Global Arrays For The sociable Plugin.
 */
class Sociable_Globals{
    
    function default_sites(){
        
        $sites = Array(

            'BarraPunto' => Array(
                    'favicon' => 'barrapunto.png',
                    'url' => 'http://barrapunto.com/submit.pl?subj=TITLE&amp;story=PERMALINK',
                    'spriteCoordinates' => Array(1,1),
            ),

            'Bitacoras.com' => Array(
                    'favicon' => 'bitacoras.png',
                    'url' => 'http://bitacoras.com/anotaciones/PERMALINK',
                    'spriteCoordinates' => Array(19,1),
            ),

            'BlinkList' => Array(
                    'favicon' => 'blinklist.png',
                    'url' => 'http://www.blinklist.com/index.php?Action=Blink/addblink.php&amp;Url=PERMALINK&amp;Title=TITLE',
                    'spriteCoordinates' => Array(37,1),
                    'supportsIframe' => false,
            ),

            'blogmarks' => Array(
                    'favicon' => 'blogmarks.png',
                    'url' => 'http://blogmarks.net/my/new.php?mini=1&amp;simple=1&amp;url=PERMALINK&amp;title=TITLE',
                    'spriteCoordinates' => Array(73,1),
            ),

            'Blogosphere News' => Array(
                    'favicon' => 'blogospherenews.png',
                    'url' => 'http://www.blogospherenews.com/submit.php?url=PERMALINK&amp;title=TITLE',
                    'spriteCoordinates' => Array(91,1),
            ),

            'blogtercimlap' => Array(
                    'favicon' => 'blogter.png',
                    'url' => 'http://cimlap.blogter.hu/index.php?action=suggest_link&amp;title=TITLE&amp;url=PERMALINK',
                    'spriteCoordinates' => Array(109,1),
            ),

            'Faves' => Array(
                    'favicon' => 'bluedot.png',
                    'url' => 'http://faves.com/Authoring.aspx?u=PERMALINK&amp;title=TITLE',
                    'spriteCoordinates' => Array(127,1),
            ),

            'connotea' => Array(
                    'favicon' => 'connotea.png',
                    'url' => 'http://www.connotea.org/addpopup?continue=confirm&amp;uri=PERMALINK&amp;title=TITLE&amp;description=EXCERPT',
                    'spriteCoordinates' => Array(163,1),
            ),

            'Current' => Array(
                    'favicon' => 'current.png',
                    'url' => 'http://current.com/clipper.htm?url=PERMALINK&amp;title=TITLE',
                    'spriteCoordinates' => Array(181,1),
            ),

            'del.icio.us' => Array(
                    'favicon' => 'delicious.png',
                    'url' => 'http://delicious.com/post?url=PERMALINK&amp;title=TITLE&amp;notes=EXCERPT',
                    'spriteCoordinates' => Array(199,1),
            ),

            'Design Float' => Array(
                    'favicon' => 'designfloat.png',
                    'url' => 'http://www.designfloat.com/submit.php?url=PERMALINK&amp;title=TITLE',
                    'spriteCoordinates' => Array(217,1),
            ),

            'Digg' => Array(
                    'favicon' => 'digg.png',
                    'url' => 'http://digg.com/submit?phase=2&amp;url=PERMALINK&amp;title=TITLE&amp;bodytext=EXCERPT',
                    'description' => 'Digg',
                    'spriteCoordinates' => Array(235,1),
            ),

            'Diigo' => Array(
                    'favicon' => 'diigo.png',
                    'url' => 'http://www.diigo.com/post?url=PERMALINK&amp;title=TITLE',
                    'spriteCoordinates' => Array(253,1),
            ),

            'DotNetKicks' => Array(
                    'favicon' => 'dotnetkicks.png',
                    'url' => 'http://www.dotnetkicks.com/kick/?url=PERMALINK&amp;title=TITLE',
                    'spriteCoordinates' => Array(271,1),
            ),

            'DZone' => Array(
                    'favicon' => 'dzone.png',
                    'url' => 'http://www.dzone.com/links/add.html?url=PERMALINK&amp;title=TITLE',
                    'spriteCoordinates' => Array(289,1),
            ),

            'eKudos' => Array(
                    'favicon' => 'ekudos.png',
                    'url' => 'http://www.ekudos.nl/artikel/nieuw?url=PERMALINK&amp;title=TITLE&amp;desc=EXCERPT',
                    'spriteCoordinates' => Array(307,1),
            ),

            'email' => Array(
                    'favicon' => 'email_link.png',
                    'url' => 'mailto:?subject=TITLE&amp;body=PERMALINK',
                    'spriteCoordinates' => Array(325,1),
                    'supportsIframe' => false,
            ),

            'Facebook' => Array(
                    'favicon' => 'facebook.png',
                    'url' => 'http://www.facebook.com/share.php?u=PERMALINK&amp;t=TITLE',
                    'spriteCoordinates' => Array(343,1),
            ),

            'Fark' => Array(
                    'favicon' => 'fark.png',
                    'url' => 'http://cgi.fark.com/cgi/fark/farkit.pl?h=TITLE&amp;u=PERMALINK',
                    'spriteCoordinates' => Array(1,19),
            ),

            'Fleck' => Array(
                    'favicon' => 'fleck.png',
                    'url' => 'http://beta3.fleck.com/bookmarklet.php?url=PERMALINK&amp;title=TITLE',
                    'spriteCoordinates' => Array(19,19),
            ),

            'FriendFeed' => Array(
                    'favicon' => 'friendfeed.png',
                    'url' => 'http://www.friendfeed.com/share?title=TITLE&amp;link=PERMALINK',
                    'spriteCoordinates' => Array(37,19),
            ),

            'FSDaily' => Array(
                    'favicon' => 'fsdaily.png',
                    'url' => 'http://www.fsdaily.com/submit?url=PERMALINK&amp;title=TITLE',
                    'spriteCoordinates' => Array(55,19),
            ),

            'Global Grind' => Array (
                    'favicon' => 'globalgrind.png',
                    'url' => 'http://globalgrind.com/submission/submit.aspx?url=PERMALINK&amp;type=Article&amp;title=TITLE',
                    'spriteCoordinates' => Array(73,19),
            ),

            'Google' => Array (
                    'favicon' => 'googlebookmark.png',
                    'url' => 'http://www.google.com/bookmarks/mark?op=edit&amp;bkmk=PERMALINK&amp;title=TITLE&amp;annotation=EXCERPT',
                    'description' => 'Google Bookmarks',
                    'spriteCoordinates' => Array(91,19),
            ),

            'Gwar' => Array(
                    'favicon' => 'gwar.png',
                    'url' => 'http://www.gwar.pl/DodajGwar.html?u=PERMALINK',
                    'spriteCoordinates' => Array(109,19),
                    'supportsIframe' => false,
            ),

            'HackerNews' => Array(
                    'favicon' => 'hackernews.png',
                    'url' => 'http://news.ycombinator.com/submitlink?u=PERMALINK&amp;t=TITLE',
                    'spriteCoordinates' => Array(127,19),
            ),

            'Haohao' => Array(
                    'favicon' => 'haohao.png',
                    'url' => 'http://www.haohaoreport.com/submit.php?url=PERMALINK&amp;title=TITLE',
                    'spriteCoordinates' => Array(145,19),
            ),

            'HealthRanker' => Array(
                    'favicon' => 'healthranker.png',
                    'url' => 'http://healthranker.com/submit.php?url=PERMALINK&amp;title=TITLE',
                    'spriteCoordinates' => Array(163,19),
            ),

            'HelloTxt' => Array(
            'favicon' => 'hellotxt.png',
            'url' => 'http://hellotxt.com/?status=TITLE+PERMALINK',
                    'spriteCoordinates' => Array(181,19),
            ),

            'Hemidemi' => Array(
                    'favicon' => 'hemidemi.png',
                    'url' => 'http://www.hemidemi.com/user_bookmark/new?title=TITLE&amp;url=PERMALINK',
            'spriteCoordinates' => Array(199,19),
            ),

            'Hyves' => Array(
                    'favicon' => 'hyves.png',
                    'url' => 'http://www.hyves.nl/profilemanage/add/tips/?name=TITLE&amp;text=EXCERPT+PERMALINK&amp;rating=5',
                    'spriteCoordinates' => Array(217,19),
            ),

            'Identi.ca' => Array(
                    'favicon' => 'identica.png',
                    'url' => 'http://identi.ca/notice/new?status_textarea=PERMALINK',
                    'spriteCoordinates' => Array(235,19),
                    'supportsIframe' => false,
            ),

            'IndianPad' => Array(
                    'favicon' => 'indianpad.png',
                    'url' => 'http://www.indianpad.com/submit.php?url=PERMALINK',
                    'spriteCoordinates' => Array(253,19),
            ),

            'Internetmedia' => Array(
                    'favicon' => 'im.png',
                    'url' => 'http://internetmedia.hu/submit.php?url=PERMALINK',
                    'spriteCoordinates' => Array(271,19),
            ),

            'Kirtsy' => Array(
                    'favicon' => 'kirtsy.png',
                    'url' => 'http://www.kirtsy.com/submit.php?url=PERMALINK&amp;title=TITLE',
                    'spriteCoordinates' => Array(289,19),
            ),

            'laaik.it' => Array(
                    'favicon' => 'laaikit.png',
                    'url' => 'http://laaik.it/NewStoryCompact.aspx?uri=PERMALINK&amp;headline=TITLE&amp;cat=5e082fcc-8a3b-47e2-acec-fdf64ff19d12',
                    'spriteCoordinates' => Array(307,19),
            ),

            'LinkArena' => Array(
                    'favicon' => 'linkarena.png',
                    'url' => 'http://linkarena.com/bookmarks/addlink/?url=PERMALINK&amp;title=TITLE',
                    'spriteCoordinates' => Array(325,19),
            ),

            'LinkaGoGo' => Array(
                    'favicon' => 'linkagogo.png',
                    'url' => 'http://www.linkagogo.com/go/AddNoPopup?url=PERMALINK&amp;title=TITLE',
                    'spriteCoordinates' => Array(343,19),
            ),

            'LinkedIn' => Array(
                    'favicon' => 'linkedin.png',
                    'url' => 'http://www.linkedin.com/shareArticle?mini=true&amp;url=PERMALINK&amp;title=TITLE&amp;source=BLOGNAME&amp;summary=EXCERPT',
                    'spriteCoordinates' => Array(1,37),
            ),

            'Linkter' => Array(
                    'favicon' => 'linkter.png',
                    'url' => 'http://www.linkter.hu/index.php?action=suggest_link&amp;url=PERMALINK&amp;title=TITLE',
                    'spriteCoordinates' => Array(19,37),
            ),

            'Live' => Array(
                    'favicon' => 'live.png',
                    'url' => 'https://favorites.live.com/quickadd.aspx?marklet=1&amp;url=PERMALINK&amp;title=TITLE',
                    'spriteCoordinates' => Array(37,37),
            ),

            'Meneame' => Array(
                    'favicon' => 'meneame.png',
                    'url' => 'http://meneame.net/submit.php?url=PERMALINK',
                    'spriteCoordinates' => Array(55,37),
                    'supportsIframe' => false,
            ),

            'MisterWong' => Array(
                    'favicon' => 'misterwong.png',
                    'url' => 'http://www.mister-wong.com/addurl/?bm_url=PERMALINK&amp;bm_description=TITLE&amp;plugin=soc',
                    'spriteCoordinates' => Array(73,37),
            ),

            'MisterWong.DE' => Array(
                    'favicon' => 'misterwong.png',
                    'url' => 'http://www.mister-wong.de/addurl/?bm_url=PERMALINK&amp;bm_description=TITLE&amp;plugin=soc',
                    'spriteCoordinates' => Array(73,37),
            ),

            'Mixx' => Array(
                    'favicon' => 'mixx.png',
                    'url' => 'http://www.mixx.com/submit?page_url=PERMALINK&amp;title=TITLE',
                    'spriteCoordinates' => Array(91,37),
            ),

            'muti' => Array(
                    'favicon' => 'muti.png',
                    'url' => 'http://www.muti.co.za/submit?url=PERMALINK&amp;title=TITLE',
                    'spriteCoordinates' => Array(109,37),
            ),

            'MyShare' => Array(
                    'favicon' => 'myshare.png',
                    'url' => 'http://myshare.url.com.tw/index.php?func=newurl&amp;url=PERMALINK&amp;desc=TITLE',
                    'spriteCoordinates' => Array(127,37),
            ),

            'MySpace' => Array(
                    'favicon' => 'myspace.png',
                    'url' => 'http://www.myspace.com/Modules/PostTo/Pages/?u=PERMALINK&amp;t=TITLE',
                    'spriteCoordinates' => Array(145,37)
            ),

            'MSNReporter' => Array(
                    'favicon' => 'msnreporter.png',
                    'url' => 'http://reporter.nl.msn.com/?fn=contribute&amp;Title=TITLE&amp;URL=PERMALINK&amp;cat_id=6&amp;tag_id=31&amp;Remark=EXCERPT',
                    'description' => 'MSN Reporter',
                    'spriteCoordinates' => Array(163,37),
            ),

            'N4G' => Array(
                    'favicon' => 'n4g.png',
                    'url' => 'http://www.n4g.com/tips.aspx?url=PERMALINK&amp;title=TITLE',
                    'spriteCoordinates' => Array(181,37),
            ),

            'Netvibes' => Array(
                    'favicon' => 'netvibes.png',
                    'url' =>	'http://www.netvibes.com/share?title=TITLE&amp;url=PERMALINK',
                    'spriteCoordinates' => Array(199,37),
            ),

            'NewsVine' => Array(
                    'favicon' => 'newsvine.png',
                    'url' => 'http://www.newsvine.com/_tools/seed&amp;save?u=PERMALINK&amp;h=TITLE',
                    'spriteCoordinates' => Array(217,37),
            ),

            'Netvouz' => Array(
                    'favicon' => 'netvouz.png',
                    'url' => 'http://www.netvouz.com/action/submitBookmark?url=PERMALINK&amp;title=TITLE&amp;popup=no',
                    'spriteCoordinates' => Array(235,37),
            ),

            'NuJIJ' => Array(
                    'favicon' => 'nujij.png',
                    'url' => 'http://nujij.nl/jij.lynkx?t=TITLE&amp;u=PERMALINK&amp;b=EXCERPT',
                    'spriteCoordinates' => Array(253,37),
            ),

            'Ping.fm' => Array(
                    'favicon' => 'ping.png',
                    'url' => 'http://ping.fm/ref/?link=PERMALINK&amp;title=TITLE&amp;body=EXCERPT',
                    'spriteCoordinates' => Array(271,37),
            ),

            'Posterous' => Array(
                    'favicon' => 'posterous.png',
                    'url' => 'http://posterous.com/share?linkto=PERMALINK&amp;title=TITLE&amp;selection=EXCERPT',
                    'spriteCoordinates' => Array(289,37),
            ),

            'PDF' => Array(
                    'favicon' => 'pdf.png',
                    'url' => 'http://www.printfriendly.com/print?url=PERMALINK&amp;partner=sociable',
                    'spriteCoordinates' => Array(325,37),
            ),

            'Print' => Array(
                    'favicon' => 'printfriendly.png',
                    'url' => 'http://www.printfriendly.com/print?url=PERMALINK&amp;partner=sociable',
                    'spriteCoordinates' => Array(343,37),
            ),

            'Propeller' => Array(
                    'favicon' => 'propeller.png',
                    'url' => 'http://www.propeller.com/submit/?url=PERMALINK',
                    'spriteCoordinates' => Array(1,55),
            ),

            'Ratimarks' => Array(
                    'favicon' => 'ratimarks.png',
                    'url' => 'http://ratimarks.org/bookmarks.php/?action=add&address=PERMALINK&amp;title=TITLE',
                    'spriteCoordinates' => Array(19,55),
            ),

            'Rec6' => Array(
                    'favicon' => 'rec6.png',
                    'url' => 'http://rec6.via6.com/link.php?url=PERMALINK&amp;=TITLE',
                    'spriteCoordinates' => Array(37,55),
            ),

            'Reddit' => Array(
                    'favicon' => 'reddit.png',
                    'url' => 'http://reddit.com/submit?url=PERMALINK&amp;title=TITLE',
                    'spriteCoordinates' => Array(55,55),
            ),

            'RSS' => Array(
                    'favicon' => 'rss.png',
                    'url' => 'FEEDLINK',
                    'spriteCoordinates' => Array(73,55),
            ),

            'Scoopeo' => Array(
                    'favicon' => 'scoopeo.png',
                    'url' => 'http://www.scoopeo.com/scoop/new?newurl=PERMALINK&amp;title=TITLE',
                    'spriteCoordinates' => Array(91,55),
            ),	

            'Segnalo' => Array(
                    'favicon' => 'segnalo.png',
                    'url' => 'http://segnalo.alice.it/post.html.php?url=PERMALINK&amp;title=TITLE',
                    'spriteCoordinates' => Array(109,55),
            ),

            'Simpy' => Array(
                    'favicon' => 'simpy.png',
                    'url' => 'http://www.simpy.com/simpy/LinkAdd.do?href=PERMALINK&amp;title=TITLE',
                    'spriteCoordinates' => Array(127,55),
            ),

            'Slashdot' => Array(
                    'favicon' => 'slashdot.png',
                    'url' => 'http://slashdot.org/bookmark.pl?title=TITLE&amp;url=PERMALINK',
                    'spriteCoordinates' => Array(145,55),
            ),

            'Socialogs' => Array(
                    'favicon' => 'socialogs.png',
                    'url' => 'http://socialogs.com/add_story.php?story_url=PERMALINK&amp;story_title=TITLE',
                    'spriteCoordinates' => Array(163,55),
            ),

            'SphereIt' => Array(
                    'favicon' => 'sphere.png',
                    'url' => 'http://www.sphere.com/search?q=sphereit:PERMALINK&amp;title=TITLE',
                    'spriteCoordinates' => Array(181,55),
            ),

            'Sphinn' => Array(
                    'favicon' => 'sphinn.png',
                    'url' => 'http://sphinn.com/index.php?c=post&amp;m=submit&amp;link=PERMALINK',
                    'spriteCoordinates' => Array(199,55),
            ),

            'StumbleUpon' => Array(
                    'favicon' => 'stumbleupon.png',
                    'url' => 'http://www.stumbleupon.com/submit?url=PERMALINK&amp;title=TITLE',
                    'spriteCoordinates' => Array(217,55),
                    'supportsIframe' => false,
            ),

            'Techmeme' => Array( 
                    'favicon' => 'techmeme.png',
                    'url' => 'http://twitter.com/home/?status=tip%20@Techmeme%20PERMALINK%20TITLE', 
                    'description' => 'Suggest to Techmeme via Twitter',
                    'spriteCoordinates' => Array(253,55),
                    'supportsIframe' => false,
            ), 

            'Technorati' => Array(
                    'favicon' => 'technorati.png',
                    'url' => 'http://technorati.com/faves?add=PERMALINK',
                    'spriteCoordinates' => Array(271,55),
            ),

            'ThisNext' => Array(
                    'favicon' => 'thisnext.png',
                    'url' => 'http://www.thisnext.com/pick/new/submit/sociable/?url=PERMALINK&amp;name=TITLE',
                    'spriteCoordinates' => Array(289,55),
            ),

            'Tipd' => Array(
                    'favicon' => 'tipd.png',
                    'url' => 'http://tipd.com/submit.php?url=PERMALINK',
                    'spriteCoordinates' => Array(307,55),
            ),

            'Tumblr' => Array(
                    'favicon' => 'tumblr.png',
                    'url' => 'http://www.tumblr.com/share?v=3&amp;u=PERMALINK&amp;t=TITLE&amp;s=EXCERPT',
                    'spriteCoordinates' => Array(325,55),
                    'supportsIframe' => false,
            ),

            'Twitter' => Array(
                    'favicon' => 'twitter.png',
                    'url' => 'http://twitter.com/intent/tweet?text=TITLE%20-%20PERMALINK',
                    'spriteCoordinates' => Array(
					"option1" =>array("32"=>array(-288,32))
					
					)
            ),

            'Upnews' => Array(
                    'favicon' => 'upnews.png',
                    'url' => 'http://www.upnews.it/submit?url=PERMALINK&amp;title=TITLE',
                    'spriteCoordinates' => Array(1,73),
            ),

            'Webnews.de' => Array(
                    'favicon' => 'webnews.png',
                    'url' => 'http://www.webnews.de/einstellen?url=PERMALINK&amp;title=TITLE',
                    'spriteCoordinates' => Array(19,73),
                    'supportsIframe' => false,
            ),

            'Webride' => Array(
                    'favicon' => 'webride.png',
                    'url' => 'http://webride.org/discuss/split.php?uri=PERMALINK&amp;title=TITLE',
            'spriteCoordinates' => Array(37,73),
            ),

            'Wikio' => Array(
                    'favicon' => 'wikio.png',
                    'url' => 'http://www.wikio.com/vote?url=PERMALINK',
                    'spriteCoordinates' => Array(55,73),
            ),

            'Wikio FR' => Array(
                    'favicon' => 'wikio.png',
                    'url' => 'http://www.wikio.fr/vote?url=PERMALINK',
                    'spriteCoordinates' => Array(55,73),
            ),

            'Wikio IT' => Array(
                    'favicon' => 'wikio.png',
                    'url' => 'http://www.wikio.it/vote?url=PERMALINK',
                    'spriteCoordinates' => Array(55,73),
            ),

            'Wists' => Array(
                    'favicon' => 'wists.png',
                    'url' => 'http://wists.com/s.php?c=&amp;r=PERMALINK&amp;title=TITLE',
                    'class' => 'wists',
                    'spriteCoordinates' => Array(73,73),
            ),

            'Wykop' => Array(
                    'favicon' => 'wykop.png',
                    'url' => 'http://www.wykop.pl/dodaj?url=PERMALINK',
                    'spriteCoordinates' => Array(91,73),
                    'supportsIframe' => false,
            ),

            'Xerpi' => Array(
                    'favicon' => 'xerpi.png',
                    'url' => 'http://www.xerpi.com/block/add_link_from_extension?url=PERMALINK&amp;title=TITLE',
                    'spriteCoordinates' => Array(109,73),
            ),

            'YahooBuzz' => Array(
                    'favicon' => 'yahoobuzz.png',
                    'url' => 'http://buzz.yahoo.com/submit/?submitUrl=PERMALINK&amp;submitHeadline=TITLE&amp;submitSummary=EXCERPT&amp;submitCategory=science&amp;submitAssetType=text',
                    'description' => 'Yahoo! Buzz',
                    'spriteCoordinates' => Array(127,73),
            ),

            'Yahoo! Bookmarks' => Array(
                    'favicon' => 'yahoomyweb.png',
                    'url' => 'http://bookmarks.yahoo.com/toolbar/savebm?u=PERMALINK&amp;t=TITLE&opener=bm&amp;ei=UTF-8&amp;d=EXCERPT',
                    'spriteCoordinates' => Array(145,73),
                    'supportsIframe' => false,
            ),

            'Yigg' => Array(
                'favicon' => 'yiggit.png',
                'url' => 'http://yigg.de/neu?exturl=PERMALINK&amp;exttitle=TITLE',
                'spriteCoordinates' => Array(163,73),
            ),

            'Add to favorites' => Array(
                'favicon' => 'addtofavorites.png',
                'url' => 'javascript:AddToFavorites();',
                'spriteCoordinates' => Array(181,73),
                'supportsIframe' => false,
            ),             

//            'Blogplay' => Array(
//                'favicon' => 'blogplay.png',
//                'url' => 'http://blogplay.com',
//                'spriteCoordinates' => Array(199,73),
//                'supportsIframe' => false,      
//            ),                              

            'MOB' => Array(
                'favicon' => 'mob.png',
                'url' => 'http://www.mob.com/share.php?u=PERMALINK&t=TITLE',
                'spriteCoordinates' => Array(217,73),
            ),

            '豆瓣' => Array(
                'favicon' => 'douban.png',
                'url' => 'http://www.douban.com/recommend/?url=PERMALINK&title=TITLE',
                'description' => '豆瓣',
                'spriteCoordinates' => Array(235,73),
            ),

            '豆瓣九点' => Array(
                'favicon' => 'douban9.png',
                'url' => 'http://www.douban.com/recommend/?url=PERMALINK&title=TITLE&n=1',
                'spriteCoordinates' => Array(253,73),
            ),    

            'QQ书签' => Array(
                'favicon' => 'qq.png',
                'url' => 'http://shuqian.qq.com/post?jumpback=1&title=TITLE&uri=PERMALINK',
                'spriteCoordinates' => Array(271,73),
            ),    

            'LaTafanera' => Array(
                'favicon' => 'latafanera.png',
                'url' => 'http://latafanera.cat/submit.php?url=PERMALINK',
                'spriteCoordinates' => Array(289,73),
            ),

            'SheToldMe' => Array(
                'favicon' => 'shetoldme.png',
                'url' => 'http://shetoldme.com/publish?url=PERMALINK&title=TITLE',
                'spriteCoordinates' => Array(307,73),
            ),

            'viadeo FR' => Array(
                'favicon' => 'viadeo.png',
                'url' => 'http://www.viadeo.com/shareit/share/?url=PERMALINK&title=TITLE&urllanguage=fr',
                'spriteCoordinates' => Array(325,73),
            ),

            'Diggita' => Array(
                'favicon' => 'diggita.png',
                'url' => 'http://www.diggita.it/submit.php?url=PERMALINK&title=TITLE',
                'spriteCoordinates' => Array(343,73),
            ),   	 

        );                       
        
        return apply_filters( 'sociable_known_sites' , $sites );
        
    }
    
    /*
     * Return The Non Default Registered Post Types
     */
    function sociable_get_post_types(){
        
        $args = array(
            'public'   => true,
            '_builtin' => false
        );
        
        $types = get_post_types( $args, 'objects' , 'and');
        
        return $types;
    }
    
    /*
     * Return The Custom Taxonomies
     */
    function sociable_get_taxonomies(){
        
        $args = array(
          'public'   => true,
          '_builtin' => false

        ); 
        
        $taxonomies = get_taxonomies( $args, 'objects' , 'and' );
        
        return $taxonomies;
    }
    
}

?>