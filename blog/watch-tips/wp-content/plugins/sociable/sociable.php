<?php
/*
Plugin Name: Sociable
Plugin URI: http://blogplay.com/plugin
Description: Automatically add links on your posts, pages and RSS feed to your favorite social bookmarking sites. 
Version: 4.3.4.1
Author: Blogplay
Author URI: http://blogplay.com/
Copyright 2006 Peter Harkins (ph@malaprop.org)
Copyright 2008-2009 Joost de Valk (joost@yoast.com)
Copyright 2009-Present Blogplay.com (info@blogplay.com)


This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/







/*
 * Define Some Paths
*/


define( 'SOCIABLE_HTTP_PATH' , WP_PLUGIN_URL . '/' . str_replace(basename( __FILE__) , "" , plugin_basename(__FILE__) ) );
define( 'SOCIABLE_ABSPATH' , WP_PLUGIN_DIR . '/' . str_replace(basename( __FILE__) , "" , plugin_basename(__FILE__) ) );


/*
 * Includes
 */

include 'includes/class-sociable_Admin_Options.php';

include("includes/skyscraper_output.php");

include 'includes/class-Sociable_Globals.php';

include 'includes/sociable_output.php';







/*
 * Global Variables
 */



//$sociable_known_sites = Sociable_Globals::default_sites();


$sociable_options = get_option( 'sociable_options' );
$skyscraper_options = get_option( 'skyscraper_options' );
$skyscraper_latest = get_option( 'skyscraper_latest' );
$skyscraper_mentions = get_option( 'skyscraper_mentions' );

//$sociable_post_types = array(); //Set This blank here, won't work before init



//$sociable_taxonomies = array(); //Same Here



/*
 * General Init Function
 */



function sociable_init(){

	wp_enqueue_script('jquery'); 	


    global $sociable_post_types, $sociable_taxonomies, $sociable_options, $skyscraper_options;

	$import_call_asyn = true;

	$url_shares = $_SERVER["REQUEST_URI"];

	if (strpos($url_shares, "wp-admin")){


		if (strpos($url_shares, "wp-admin") && !strpos($url_shares, "page=skyscraper_options")){


			$import_call_asyn = false;

		} 

	}







	else{



		if (!isset($skyscraper_options["active"])){



			$import_call_asyn = false;	







		}







	} 







	if ($import_call_asyn){



		wp_enqueue_script( 'async_call' , SOCIABLE_HTTP_PATH . 'js/async_call.js' );







		wp_enqueue_script( 'oplugin' , SOCIABLE_HTTP_PATH . 'js/oPlugin.js' );







		wp_enqueue_style(  "skyscraper_style_shape",SOCIABLE_HTTP_PATH."css/shape.css");







		wp_enqueue_style(  "skyscraper_style_toolbar", SOCIABLE_HTTP_PATH."css/toolbar.css");







		







	}







	if (!isset($sociable_options['icon_size']) || $sociable_options['icon_size'] == "" || !isset($sociable_options['version']) || !isset($sociable_options['blogplay_tags'])){



		sociable_reset();	

	} 



 

 

	if (!isset($skyscraper_options['accept_read_twitter'])){







		//skyscraper_reset();



	}



	



	



    load_plugin_textdomain( 'sociable', false, dirname( plugin_basename( __FILE__ ) )."/languages" );







    $active_sites = ( isset( $sociable_options['active_sites'] ) ) ? $sociable_options['active_sites'] : array() ;







    //Set The Post Types



    $sociable_post_types = Sociable_Globals::sociable_get_post_types();



    //Set The Custom Taxonomies



    $sociable_taxonomies = Sociable_Globals::sociable_get_taxonomies();



    wp_enqueue_script( 'sociable' , SOCIABLE_HTTP_PATH . 'js/sociable.js' );
	
    wp_enqueue_script( 'vuible' , SOCIABLE_HTTP_PATH . 'js/vuible.js' );







	wp_enqueue_script( 'addtofavourites' , SOCIABLE_HTTP_PATH . 'js/addtofavorites.js' );







    if( ! is_admin() ){



      //Load Up The Front Of Site CSS And JS



        if( array_key_exists( 'Add to favorites' , $active_sites ) ){



//            wp_enqueue_script( 'addtofavourites' , SOCIABLE_HTTP_PATH . 'js/addtofavorites.js' );







        }







        if( isset( $sociable_options['use_stylesheet'] ) ){



            wp_enqueue_style( 'sociablecss' , SOCIABLE_HTTP_PATH . 'css/sociable.css' );







        }







    }







}



/*







 * Hooks And Filters







 */



add_action( 'admin_init' , array( 'sociable_Admin_Options' , 'init' ) );







add_action( 'admin_menu' , array( 'sociable_Admin_Options' , 'add_menu_pages' ) );







add_action( 'save_post' , array( 'sociable_Admin_Options' , 'save_post' ) );







add_action( 'init' , 'sociable_init' );







add_action( 'wp_head' , 'sociable_init_async' );







function sociable_init_async(){



		echo "<script type='text/javascript'>";







		echo  "var base_url_sociable = '".SOCIABLE_HTTP_PATH."'";







		echo "</script><script type='text/javascript' src='http://apis.google.com/js/plusone.js'></script>";







}



add_filter( 'the_content', 'auto_sociable' );



//add_filter( 'get_pages', 'auto_skyscraper' );







//add_filter( 'the_excerpt', 'auto_skyscraper' );







add_filter( 'the_excerpt', 'auto_sociable' );







register_activation_hook(__FILE__, 'sociable_activate' );







register_deactivation_hook( __FILE__, 'sociable_deactivate' );







/*







 * Activation Function







 */



function sociable_activate(){







    if( ! get_option( 'sociable_options' ) ){



        return sociable_reset();







    }







}



/*







 * Reset Function







 */



function sociable_reset(){



    global $wpdb;



    //reset all data to factory defaults, install if is there.







    //Delete All Metadata From The Database ?



    $wpdb->query("DELETE FROM $wpdb->postmeta WHERE meta_key = '_sociableoff'");



    $sociable_options = array(
        
        'pixel' => "",
        
    	'blogplay_tags' => 1, 

		'version' =>'4.3.2',

        'automatic_mode'         => 'on',

        'tagline'                => 'Be Sociable, Share!',

        'custom_image_directory' => '',

        'use_stylesheet'         => 'on',

        'use_images'             => 'on',

        'use_alphamask'          => 'on',

        'new_window'             => 'on',

		'help_grow'              => '', 

        'locations'              => array(







						            'is_single' => 'on',







						            'is_page' => 'on'







									 ),







        'active_sites'           => array(







									'Twitter'  => 'on',







						            'Facebook' => 'on',







									'email'=>'on',



									'vuible' =>'on',



									'Add to favorites'=>'on',







						            'StumbleUpon'  =>'on',







									'Delicious'   =>'on',







									'Google Reader' =>'on',







									'LinkedIn' => 'on',







									







									'More' => 'on',







									'Twitter Counter' =>'on',







									'Facebook Counter' =>'on',







									'Google +' =>'on',







									'LinkedIn Counter' =>'on',			 







									'StumbleUpon Counter' =>'on',
									
									'vuible Counter' =>'on'







						        	),







        'icon_size'       => '32',







		'icon_option' => 'option1',







		"active" 	=> 1,



		'linksoptions' => ''



    );



    $sociable_known_sites = array(



        'Facebook'    => array(







			            'favicon' => 'facebook.png',







			            'url' => 'http://www.facebook.com/share.php?u=PERMALINK&amp;t=TITLE',







						'spriteCoordinates' => Array( 







								                '16' => array("-48px","0px"),







								                '32' => array("-96px","0px"),







								                '48' => array("-144px","0px"),







								                '64' => array("-192px","0px")







								            )







			        ),







		'Facebook Counter'    => array(







									'counter' =>1,







						            'favicon' => 'likecounter.png',







						            'url' => '<iframe src="http://www.facebook.com/plugins/like.php?href=PERMALINKCOUNT&send=false&layout=button_count&show_faces=false&action=like&colorscheme=light&font" scrolling="no" frameborder="0" style="border:none; overflow:hidden;height:32px;width:100px" allowTransparency="true"></iframe>',







									'spriteCoordinates' => Array( 







											                '16' => array("-48px","0px"),







											                '32' => array("-96px","0px"),







											                '48' => array("-144px","0px"),







											                '64' => array("-192px","0px")







											            )







						        ), 



        'Myspace'     => array(







            'favicon' => 'myspace.png',







            'url' => 'http://www.myspace.com/Modules/PostTo/Pages/?u=PERMALINK&amp;t=TITLE',







            'spriteCoordinates' => Array( 







                '16' => array("0px","-16px"),







                '32' => array("0px","-32px"),







                '48' => array("0px","-48px"),







                '64' => array("0px","-64px")







            )







        ),



        'Twitter'     => array(







            'favicon' => 'twitter.png',







            'url' => 'http://twitter.com/intent/tweet?text=TITLE%20-%20PERMALINK%20  SHARETAG',







            'spriteCoordinates' => Array( 







                '16' => array("-144px","-16px"),







                '32' => array("-288px","-32px"),







                '48' => array("-432px","-48px"),







                '64' => array("-576px","-64px")







            )







        ),



		        'Twitter Counter'     => array(







			'counter' =>1,







            'favicon' => 'twitter.png',







            'url' => '<a href="https://twitter.com/share" data-text="TITLECOUNT - PERMALINKCOUNT" data-url="PERMALINKCOUNT" class="twitter-share-button" data-count="horizontal">Tweet</a><script type="text/javascript" src="//platform.twitter.com/widgets.js"></script>',







            'spriteCoordinates' => Array( 







                '16' => array("-144px","-16px"),







                '32' => array("-288px","-32px"),







                '48' => array("-432px","-48px"),







                '64' => array("-576px","-64px")







            )







        ),







        'LinkedIn'    => array(







            'favicon' => 'linkedin.png',







            'url' => 'http://www.linkedin.com/shareArticle?mini=true&amp;url=PERMALINK&amp;title=TITLE&amp;source=BLOGNAME&amp;summary=EXCERPT',







            'spriteCoordinates' => Array( 







                '16' => array("-144px","0px"),







                '32' => array("-288px","0px"),







                '48' => array("-432px","0px"),







                '64' => array("-576px","0px")







            )







        ),



		        'LinkedIn Counter'    => array(







				'counter'=>1,







            'favicon' => 'linkedin.png',







            'url' => '<script src="http://platform.linkedin.com/in.js" type="text/javascript"></script><script type="IN/Share" data-url="PERMALINKCOUNT" data-counter="right"></script>',







            'spriteCoordinates' => Array( 



                '16' => array("-144px","0px"),







                '32' => array("-288px","0px"),







                '48' => array("-432px","0px"),







                '64' => array("-576px","0px")







            )







        ),



        'Delicious'   => array(



            'favicon' => 'delicious.png',







            'url' => 'http://delicious.com/post?url=PERMALINK&amp;title=TITLE&amp;notes=EXCERPT',







            'spriteCoordinates' => Array( 







                '16' => array("-16px","0px"),







                '32' => array("-32px","0px"),







                '48' => array("-48px","0px"),







                '64' => array("-64px","0px")







            )







        ),        



        'Digg'        => array(



            'favicon' => 'digg.png',







            'url' => 'http://digg.com/submit?phase=2&amp;url=PERMALINK&amp;title=TITLE&amp;bodytext=EXCERPT',







            'spriteCoordinates' => Array( 







                '16' => array("-32px","0px"),







                '32' => array("-64px","0px"),







                '48' => array("-96px","0px"),







                '64' => array("-128px","0px")







            )







        ),







		'Digg Counter'        => array(







			'counter' =>1,







            'favicon' => 'digg.png',







            'url' => "<script type='text/javascript'>(function() {var s = document.createElement('SCRIPT'), s1 = document.getElementsByTagName('SCRIPT')[0];s.type = 'text/javascript';s.async = true;s.src = 'http://widgets.digg.com/buttons.js';s1.parentNode.insertBefore(s, s1);})();</script><a href='http://digg.com/submit?url=PERMALINK&amp;title=TITLE'  class='DiggThisButton DiggCompact'></a>",



            'spriteCoordinates' => Array( 







                '16' => array("-32px","0px"),







                '32' => array("-64px","0px"),







                '48' => array("-96px","0px"),







                '64' => array("-128px","0px")







            )







        ),



        'Reddit'      => array(



            'favicon' => 'reddit.png',







 







            'url' => 'http://reddit.com/submit?url=PERMALINK&amp;title=TITLE',







            'spriteCoordinates' => Array( 







                '16' => array("-64px","-16px"),







                '32' => array("-128px","-32px"),







                '48' => array("-192px","-48px"), 







                '64' => array("-256px","-64px")







            )







        ),







        







        'StumbleUpon'  => array(







            'favicon' => 'stumbleupon.png',







            'url' => 'http://www.stumbleupon.com/submit?url=PERMALINK&title=TITLE',







            'spriteCoordinates' => Array( 







                '16' => array("-112px","-16px"),







                '32' => array("-224px","-32px"),







                '48' => array("-336px","-48px"),







                '64' => array("-448px","-64px")







            )),







			







			        'StumbleUpon Counter'  => array(







			'counter' =>1,







            'favicon' => 'stumbleupon.png',







            'url' => '<script src="http://www.stumbleupon.com/hostedbadge.php?s=2&r=PERMALINKCOUNT"></script>',







            'spriteCoordinates' => Array( 







                '16' => array("-112px","-16px"),







                '32' => array("-224px","-32px"),







                '48' => array("-336px","-48px"),







                '64' => array("-448px","-64px")







            )







        ),

		'vuible'  => array(
            'favicon' => 'vuible.png',
            'url' => 'http://vuible.com/pins-settings/?m=bm&imgsrc=SOURCE&source=PERMALINK&title=TITLE&video=0',
            'spriteCoordinates' => Array( 
                '16' => array("-112px","-16px"),
                '32' => array("-224px","-32px"),
                '48' => array("-336px","-48px"),
                '64' => array("-448px","-64px")
            )
),
'vuible Counter'  => array(
			'counter' =>1,
            'favicon' => 'vuible.png',
            'url' => '<script src="http://www.stumbleupon.com/hostedbadge.php?s=2&r=PERMALINKCOUNT"></script>',
            'spriteCoordinates' => Array( 
                '16' => array("-112px","-16px"),
                '32' => array("-224px","-32px"),
                '48' => array("-336px","-48px"),
                '64' => array("-448px","-64px")
            )
),





		 'Google Bookmarks' => Array (







                    'favicon' => 'google.png',







                    'url' => 'http://www.google.com/bookmarks/mark?op=edit&amp;bkmk=PERMALINK&amp;title=TITLE&amp;annotation=EXCERPT',







                    'description' => 'Google Bookmarks',







                    'spriteCoordinates' => Array( 







                '16' => array("-96px","0px"),







                '32' => array("-192px","0px"),







                '48' => array("-288px","0px"),







                '64' => array("-384px","0px")







            )







            ),







			







			'Google +' => Array (







			







					'counter' =>1,







                    'favicon' => 'google.png',







                /*    'url' => '<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>







<g:plusone annotation="bubble" size="medium"></g:plusone>',*/







					'url' => '<g:plusone annotation="bubble" href="PERMALINKCOUNT" size="medium"></g:plusone>',







/*







    <script type="text/javascript">







      window.___gcfg = {







        lang: \'en-US\'







      };







      (function() {







        var po = document.createElement(\'script\'); po.type = \'text/javascript\'; po.async = true;







        po.src = \'https://apis.google.com/js/plusone.js\';







        var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);







      })();







    </script>







',*/







                    'description' => 'Google Bookmarks',







                    'spriteCoordinates' => Array( 







                '16' => array("-96px","0px"),







                '32' => array("-192px","0px"),







                '48' => array("-288px","0px"),







                '64' => array("-384px","0px")







            )







            ),







			







			            'HackerNews' => Array(







                    'favicon' => 'hacker_news.png',







                    'url' => 'http://news.ycombinator.com/submitlink?u=PERMALINK&amp;t=TITLE',







                   'spriteCoordinates' => Array( 







                '16' => array("-128px","0px"),







                '32' => array("-256px","0px"),







                '48' => array("-384px","0px"),







                '64' => array("-512px","0px")







            )







            ),







			   'MSNReporter' => Array(







                    'favicon' => 'msn.png',







                    'url' => 'http://reporter.es.msn.com/?fn=contribute&amp;Title=TITLE&amp;URL=PERMALINK&amp;cat_id=6&amp;tag_id=31&amp;Remark=EXCERPT',







                    'description' => 'MSN Reporter',







                    'spriteCoordinates' => Array( 







                '16' => array("-176px","0px"),







                '32' => array("-352px","0px"),







                '48' => array("-528px","0px"),







                '64' => array("-704px","0px")







            )







            ),







			







			 'BlinkList' => Array(







                    'favicon' => 'blinklist.png',







                    'url' => 'http://www.blinklist.com/index.php?Action=Blink/addblink.php&amp;Url=PERMALINK&amp;Title=TITLE',







                    'spriteCoordinates' => Array( 







                '16' => array("0px","0px"),







                '32' => array("0px","0px"),







                '48' => array("0px","0px"),







                '64' => array("0px","0px")







            ),







                    'supportsIframe' => false,







            ),







			'Sphinn' => Array(







                    'favicon' => 'sphinn.png',







                    'url' => 'http://sphinn.com/index.php?c=post&amp;m=submit&amp;link=PERMALINK',







                    'spriteCoordinates' => Array( 







                '16' => array("-96px","-16px"),







                '32' => array("-192px","-32px"),







                '48' => array("-288px","-48px"),







                '64' => array("-384px","-64px")







            )







            ),







			







			'Posterous' => Array(







                    'favicon' => 'posterous.png',







                    'url' => 'http://posterous.com/share?linkto=PERMALINK&amp;title=TITLE&amp;selection=EXCERPT',







                    'spriteCoordinates' => Array( 







                '16' => array("-32px","-16px"),







                '32' => array("-64px","-32px"),







                '48' => array("-96px","-48px"),







                '64' => array("-128px","-64px")







            )







            ),







			'Tumblr' => Array(







                    'favicon' => 'tumblr.png',







                    'url' => 'http://www.tumblr.com/share?v=3&amp;u=PERMALINK&amp;t=TITLE&amp;s=EXCERPT',







                   'spriteCoordinates' => Array( 







                '16' => array("-128px","-16px"),







                '32' => array("-256px","-32px"),







                '48' => array("-384px","-48px"),







                '64' => array("-512px","-64px")







				),







                    'supportsIframe' => false







            ),







			'email' => Array(







                    'favicon' => 'gmail.png',







					'url' => 'https://mail.google.com/mail/?view=cm&fs=1&to&su=TITLE&body=PERMALINK&ui=2&tf=1&shva=1',







                    'spriteCoordinates' => Array( 







                '16' => array("-80px","0px"),







                '32' => array("-160px","0px"),







                '48' => array("-240px","0px"),







                '64' => array("-320px","0px")







            ),







                    'supportsIframe' => false







            ),







			







			'Google Reader' => array (







					'favicon' => 'googlebuzz.png',







						'url' => 'http://www.google.com/reader/link?url=PERMALINK&amp;title=TITLE&amp;srcURL=PERMALINK&amp;srcTitle=BLOGNAME',







				'spriteCoordinates' => Array( 







                '16' => array("-112px","0px"),







                '32' => array("-224px","0px"),







                '48' => array("-336px","0px"),







                '64' =>  array("-448px","0px")







            )







			),







			 'Add to favorites' => array(







			 'favicon' => 'favorites.png',







			 'url' => 'javascript:AddToFavorites();',







			 'spriteCoordinates' => Array( 







                '16' => array("-64px","0px"),







                '32' => array("-128px","0px"),







                '48' => array("-192px","0px"),







                '64' => array("-256px","0px")







            )







		 ),







			 'More' => array(







			 'favicon' => 'more.png',







			 'url' => 'javascript:more();',







			'spriteCoordinates' => Array( 







                '16' => array("0px","0px"),







                '32' => array("0px","0px"),







                '48' =>  array("0px","0px"),







                '64' => array("0px","0px")







            )







		 ),







    );





    //Update will create if it doesn't exist.



    update_option( 'sociable_known_sites' , $sociable_known_sites );



    update_option( 'sociable_options'     , $sociable_options );



    update_option( 'sociable_helpus'      ,	1); 

}







function skyscraper_reset(){







	$skyscraper_options = array(

                        

                        "pixel"                     => "",

                        

						"version" 					=> "1.0",



						"widget_width" 				=> "60px",



						"widget_position" 			=> "1",



						"background_color" 			=> "#fefefe",



						"labels_color" 				=> "#f7f7f7",



						"text_size" 				=> "10px",



						"counters" 					=> array("check" => "1",



															"folded" => "1"),



						"share" 					=> array("check" => "1",



															"folded" => "1"),



						"num_tweets"				=> 3,



						"num_rss"					=>3,



						"locations"					=> array("is_front_page" => 1,



															"is_home" 		 => 1,



															"is_single" 	 => 1,



															"is_page" 	 	 => 1,



															"is_category" 	 => 1,



															"is_date" 		 => 1,



															"is_tag" 		 => 1,



															"is_author" 	 => 1,



															"is_search" 	 => 1,



															"is_rss" 		 => 1  ),



															



					  "counters"					=> array("check" => 1,



					  										 "folded" => 1),



					  "share"						=> array("check" => 1,



					  										 "folded" => 1),







					  "sociable_banner"				=> "",







					  "sociable_banner_timer"	    => 15, 







					  "sociable_banner_text"	 	=> 'Please spread the word: Be Sociable, Share!',







					  "sociable_banner_colorBack"   => '#FFFFFF',			  







					  "sociable_banner_fontSize"	=> '9px',







					  "sociable_banner_colorLabel"	=> '#F7F7F7',



					  



					  "sociable_banner_colorFont"	=> '#6A6A6A',



					  



					  "accept_read_twitter"			=> '',



					  



					  "accept_read_rss"				=> ''



	);







    update_option( 'skyscraper_options'   , $skyscraper_options );



    



    	



	$skyscraper_latest = array();



	update_option("skyscraper_latest",$skyscraper_latest );



	



	$skyscraper_mentions = array();



	update_option("skyscraper_mentions",$skyscraper_mentions );



}







/*



 * De-Activate Function



 */



function sociable_deactivate(){



//    global $wpdb;



//    //Delete The Metadata



//    $wpdb->query("DELETE FROM $wpdb->postmeta WHERE meta_key = '_sociableoff'");



//    //delete The Options



//    return delete_option( 'sociable_options' );



}







/*



 * Function To Completely Remove The Options



 */



function sociable_2_remove(){



    require_once( ABSPATH . '/wp-admin/includes/plugin.php' );



    



    global $wpdb;



    //Delete The Metadata



    $wpdb->query("DELETE FROM $wpdb->postmeta WHERE meta_key = '_sociableoff'");



    //delete The Options



    delete_option( 'sociable_options' );



	delete_option( 'skyscraper_options' );



    



 







    deactivate_plugins( array( 'sociable/sociable.php' ) );



    wp_redirect( '/wp-admin/plugins.php?deactivate=true' );



}







/*



 * Generic Plugin Wide Functions



 */



function _get_sociable_image_path(){







    global $sociable_options;







        







    if( empty( $sociable_options['custom_icons'] )){







		if ($sociable_options['icon_option'] !="option6"){







        $path = trailingslashit( SOCIABLE_HTTP_PATH . 'images/'.$sociable_options['icon_option']."/" . $sociable_options['icon_size'] );







		}else{







		







		$path = trailingslashit( SOCIABLE_HTTP_PATH . 'images/original/');







		}







    } else {







        $path = trailingslashit( SOCIABLE_HTTP_PATH . 'images/customIcons/');







    }







 







     







    return $path;







}







function _get_sociable_image( $site, $description ){







global $sociable_options;







    $imageclass = '';



    $imagestyle = '';



    $imagepath = _get_sociable_image_path();



    //Get The Source Of The Image



    if ( ! isset( $site['spriteCoordinates'] ) || ! isset( $sociable_options['use_sprites'] ) || is_feed() ) {







        if ( strpos( $site['favicon'], 'http' ) === 0 ) {



                $imagesource = $site['favicon'];



        } else {



                $imagesource = $imagepath.$site['favicon'];



        }







    } else {







        $imagesource = $imagepath . "services-sprite.gif";



        $services_sprite_url = $imagepath . "sprite.png";







        $spriteCoords = $site['spriteCoordinates'];



        



        $size = $sociable_options['icon_size'];







        $imagestyle = 'width: ' . $size . 'px; height: ' . $size . 'px; background: transparent url(' . $services_sprite_url . ') no-repeat; background-position:' . $spriteCoords[$size] . 'px 0';







    }



	







    if( isset( $sociable_options['use_alphamask'] ) ){



        $imageclass .= 'sociable-hovers';          



    }







    //If A Class Has Been Specified, Ensure It Is Added To The Class Attribute.



    if ( isset( $site['class'] ) ) {



        $imageclass .= 'sociable_' . $site['class'];



    }



 



    if( $imagestyle != '' ){



        $imagestyle = 'style="' . $imagestyle . '"';



    }



	if ($sociable_options['icon_option'] !="option6"){







    $image = '<img  src="' . $imagesource . '" title="' . $description . '" alt="' . $description . '"' . $imagestyle . ' />' ;







	}else{







	$image = '<img class="' . $imageclass . '" src="' . $imagesource . '" title="' . $description . '" alt="' . $description . '"' . $imagestyle . ' />' ;







	}



    



    return $image;



}







?>
