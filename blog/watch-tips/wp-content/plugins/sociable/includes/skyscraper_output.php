<?php



/*



 * The Output And Shortcode Functions For sociable



 */

/*



 * Returns The Skyscraper Output For The Global $post Object Do Not 



 */

function diff_date($date1, $date2){



	$date1 = mktime(substr($date1,8,2), substr($date1,10,2), substr($date1,12,2), substr($date1,4,2), substr($date1,6,2), substr($date1,0,4));

	$date2 = mktime(substr($date2,8,2), substr($date2,10,2), substr($date2,12,2), substr($date2,4,2), substr($date2,6,2), substr($date2,0,4));



	$diff_time = ceil((($date2 - $date1)/60));

	return $diff_time;



}

 

 

function skyscraper_html( $where = "" ){

    global $skyscraper_options, $wp_query; 

	if (!is_admin() || 1==1){

	//	echo "<script type='text/javascript'>";

	//	echo "var skyscraper_dir = '".SOCIABLE_HTTP_PATH."' ;";

	//	echo "</script>";

		echo " var skyscraper_dir =  document.createElement('input');

				skyscraper_dir.id = 'skyscraper_dir';

				skyscraper_dir.type = 'hidden';

				skyscraper_dir.value = '".SOCIABLE_HTTP_PATH."';

				document.body.appendChild(skyscraper_dir);	";

			

		$widget_width = str_replace("px", "", $skyscraper_options["widget_width"]);

		

		$widget_position = "null";

		if (isset($skyscraper_options["widget_position"])){

			$widget_position = 1;

		}

		

		$labels_color = $skyscraper_options["labels_color"];

		$text_size = str_replace("px", "", $skyscraper_options["text_size"]);

		$background_color = $skyscraper_options["background_color"];

		

		$addWhere = "";

		

		if ($where == ""){

			$addWhere = "var div = document.createElement('div');

						div.id = 'skyscraper';

						document.body.appendChild(div);";

		}		

		$url_site= $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];

		$script = "	



	 			if (!document.getElementById('fb-root')){



					var div = document.createElement('div');



					div.id = 'fb-root';



					document.body.appendChild(div);



				}

				(function(d, s, id) {



				  var js, fjs = d.getElementsByTagName(s)[0];



				  if (d.getElementById(id)) return;



				  js = d.createElement(s); js.id = id;



				  js.src = \"http://connect.facebook.net/en_US/all.js#xfbml=1\";



				  fjs.parentNode.insertBefore(js, fjs);

				}(document, 'script', 'facebook-jssdk'));



			



			".$addWhere."



 



			jQuery(document).ready(function(){						

						oPlugin.toolbarStart('skyscraper', ".$widget_position.",230,".$widget_width.",'".$background_color."','".$labels_color."',false,'#6A6A6A',".$text_size.",'#587cc8');



									 	



						".get_share_node()."



						".get_counters_node()."			



						".get_social_banner_node()."		



						".get_latest_node()."															



						".get_mentions_node()."										



						".get_follow_us_node()."										



						".get_rss_node()."



						oPlugin.CreateGoToTop('New_Id_12','Top','<img src=\"".SOCIABLE_HTTP_PATH."images/toolbar/gototop.png\" style=\"width:30px;\" />');						

						oPlugin.CreateGoToHome('New_Id_13','Go Home','<img src=\"".SOCIABLE_HTTP_PATH."images/toolbar/gotohome.png\" style=\"width:30px;\" />');												



	    }); 	



					jQuery('.title').css('font-size', '".$text_size."px');		



 



		";



		echo $script;



	}



}

function get_social_banner_node(){



	



	



	global $skyscraper_options;

	global $title_shared;

	global $url_shares;	



	



	$social_banner_node = "";



	



	



	if(!empty($skyscraper_options["sociable_banner"])){



	



		$follow_us = sc_follow_links(1);



		



		$follow_buttons = $follow_us["follow_buttons"];



 



		$follow_us_count = $follow_us["count"];

		$width_banner = 200;

		$tag_banner = '';
		
		if (isset($skyscraper_options["blogplay_tags"])){
			
			if (!empty($skyscraper_options["blogplay_tags"])){
				
				$tag_banner = '<blogplay.com>';
			}
		}	 



		$social_banner_node = " var url = '". addslashes(trim($url_shares))."';



								var title = '".addslashes(trim($title_shared)) ."';	



								var counter =	'<ul class=\"boxBanner_ul\">';				



								counter += '	<li>';			



								counter += '	<div class=\"fb-like\" data-send=\"false\" data-layout=\"box_count\" data-width=\"50\" data-href=\"'+url+'\" data-show-faces=\"false\"></div>';



								



								counter += '	</li>';			



								counter += '	<li>';						



								counter += '	<iframe width=\"100%\" scrolling=\"no\" frameborder=\"0\" title=\"+1\" vspace=\"0\" tabindex=\"-1\" style=\"position: static; left: 0pt; top: 0pt; width: 60px; margin: 0px; border-style: none; visibility: visible; height: 60px;\" src=\"https://plusone.google.com/_/+1/fastbutton?url='+url+'&amp;size=tall&amp;count=true&amp;hl=en-US&amp;jsh=m%3B%2F_%2Fapps-static%2F_%2Fjs%2Fgapi%2F__features__%2Frt%3Dj%2Fver%3Dt1NEBxIt2Qs.es_419.%2Fsv%3D1%2Fam%3D!Xq7AzNfn9_-I0e5PyA%2Fd%3D1%2F#id=I1_1328906079806&amp;parent='+url+'&amp;rpctoken=615138222&amp;_methods=onPlusOne%2C_ready%2C_close%2C_open%2C_resizeMe%2C_renderstart\" name=\"I1_1328906079806\" marginwidth=\"0\" marginheight=\"0\" id=\"I1_1328906079806\" hspace=\"0\" allowtransparency=\"true\"></iframe>';



								counter += '	</li>';							



								counter += '	<li>';					



								counter += '<iframe scrolling=\"no\" frameborder=\"0\" allowtransparency=\"true\" src=\"https://platform.twitter.com/widgets/tweet_button.html?_version=2&amp;count=vertical&amp;enableNewSizing=false&amp;id=twitter-widget-6&amp;lang=en&amp;original_referer='+url+'&amp;size=m&amp;text='+title+' ".$tag_banner." &amp;url='+url+'\" class=\"twitter-share-button twitter-count-vertical\" style=\"width: 55px; height: 62px;\" title=\"Twitter Tweet Button\"></iframe>';						



								counter += '	</li>';



								counter += '</ul>';";



								



		if ($follow_us_count > 0){



				 



				$social_banner_node .= "



						counter += '<ul class=\"boxBanner_ul_margin\">';



						counter += '<li>';



						counter += '</li>';	



						counter += '</ul>';";



						



				$social_banner_node .= " counter += '".$follow_buttons."'; ";



				



				if ($follow_us_count > 1){



				



					$width_banner = $width_banner + (70 * $follow_us_count);



				}



				else{



					$width_banner = $width_banner + (90 * $follow_us_count);



				}



				



				



		}			 			



					  			



		$label_text = trim($skyscraper_options["sociable_banner_text"]);



		



		if (strlen($label_text) > 35){



		//	$label_text = substr($label_text,0,35);



		}						



		$label_text = addslashes($label_text);



		 



		$social_banner_node .= "oPlugin.CreateNode('New_Id_14','".$label_text."', '',  counter,'banner',80,".$width_banner.");";	



		$timer = ($skyscraper_options["sociable_banner_timer"] * 1000);



		



		$colorBack = $skyscraper_options["sociable_banner_colorBack"];



		$colorFont = $skyscraper_options["sociable_banner_colorFont"];



		$colorLabel = $skyscraper_options["sociable_banner_colorLabel"];



		$fontSize = $skyscraper_options["sociable_banner_fontSize"];



		



		$social_banner_node .="setTimeout('showBanner(".$timer.",\"".$colorBack."\", \"".$colorLabel."\", \"".$colorFont."\", \"".$fontSize."\")', ".$timer.");";



				



	}	



		



	return $social_banner_node;



}

function get_rss_node(){



	$rss_node = "";

	$latest_posts = "";

	global $skyscraper_options;

	

	

	if (!empty($skyscraper_options["accept_read_rss"])){



		if ($skyscraper_options["accept_read_rss"] != 1){

	

			return $rss_node;

		}

	}

	else{



		return $rss_node;

	}

		

	$version = phpversion();

	

	if ( substr($version,0,1) == 5 &&  isset($skyscraper_options["rss_feed"]) &&  $skyscraper_options["rss_feed"]!="http://"){ 

	

		include("rss_php.php");

		

		$rss = new rss_php;

    	$rss->load($skyscraper_options["rss_feed"]);

    	$items = $rss->getItems();

		

		if (!empty($skyscraper_options["rss_feed"])){

			 

			if (count($items) > 0){

				

				$cant = 0;

				foreach($items as $item){

					

					if ($cant <= $skyscraper_options["num_rss"]){

						

						$title="";

						if (isset($item["title"])){

							$title =  addslashes($item["title"]);

						}

						$description="";

						if (isset($item["description"])){

							$description =  addslashes($item["description"]);

						}

						$guid="";

						if (isset($item["link"])){

							$guid =  addslashes($item["link"]);

						}

						$pubDate="";

						if (isset($item["pubDate"])){

							$pubDate =  ago(strtotime($item["pubDate"]));

						}

																	

						$latest_posts .= "['".$title."','','".$description."','".$guid."','".$pubDate."'],";

					}

					else{

						break;

					}

				 	$cant++;						 

				} 

				$latest_posts = trim($latest_posts, ",");	

			}						

		}	

	}

	

	if ($latest_posts != ""){

	

	$rss_node = "var LatestBlogPostContent = [

										".$latest_posts."

										];

					oPlugin.CreateNode('New_Id_5','Posts','',LatestBlogPostContent,'Notice',220,460);";

	}

					

	return $rss_node;

}

function get_latest_node(){



	$latest_node = "";	

	global $skyscraper_options;	

	

	if (!empty($skyscraper_options["accept_read_twitter"])){



		if ($skyscraper_options["accept_read_twitter"] != 1){

	

			return $latest_node;

		}

	}

	else{



		return $latest_node;

	}

	

		

	if ( isset($skyscraper_options["twitter_username"])){

	

		$latest_tweets = get_option_tweets("skyscraper_latest");

		if ($skyscraper_options["twitter_username"] != ""){

		

			if ($latest_tweets != ""){

			

				$latest_node = "



				



					var LastestTwittsContent = [

													".$latest_tweets."	

													];

														

									oPlugin.CreateNode('New_Id_3','Latest','',LastestTwittsContent,'Notice',220,460);";

			}

		}

	}

	return $latest_node;

}

 

function get_mentions_node(){



	$mentions_node = "";

	

	global $skyscraper_options;

	

	if (!empty($skyscraper_options["accept_read_twitter"])){



		if ($skyscraper_options["accept_read_twitter"] != 1){

	

			return $mentions_node;

		}

	}

	else{

	

		return $mentions_node;

	}

	

		

	if ( isset($skyscraper_options["twitter_username"])){

	

		$mentions_tweets = get_option_tweets("skyscraper_mentions");

		if ($skyscraper_options["twitter_username"] != ""){

		

			if ($mentions_tweets != "" ){

			

				$mentions_node = "var  TweetsMentionsContent = [

													".$mentions_tweets."	

														];										

									oPlugin.CreateNode('New_Id_4','Mentions','',TweetsMentionsContent,'Notice',220,460);";

			}

		}

	}

	return $mentions_node;

}

function get_counters_node(){

	

	global $skyscraper_options;

	global $title_shared;

	global $url_shares;	

	$counters_node = "";



	if ((!empty($skyscraper_options["counters"]["check"]))){
		
		
		$tag_counter = '';
		
		if (isset($skyscraper_options["blogplay_tags"])){
		
			if (!empty($skyscraper_options["blogplay_tags"])){
				
				$tag_counter = '(blogplay.com)';
			}
		}
		 

		$counters_node = " var url = '". addslashes(trim($url_shares))."';

							var title = '".addslashes(trim($title_shared)) ."';	";



								



		$counters_node .= "	



							var counter = '<ul class= \"boxCounters_ul\">';



 



							counter += '<li style=\"margin-left:2px\"><fb:like send=\"false\" layout=\"box_count\" show_faces=\"false\" font=\"\"></fb:like></li>';

							counter +=' <li style=\"margin-left:0px\"><iframe width=\"100%\" scrolling=\"no\" frameborder=\"0\" title=\"+1\" vspace=\"0\" tabindex=\"-1\" style=\"position: static; left: 0pt; top: 0pt; width: 60px; margin: 0px; border-style: none; visibility: visible; height: 60px;\" src=\"https://plusone.google.com/_/+1/fastbutton?url='+url+'&amp;size=tall&amp;count=true&amp;hl=en-US&amp;jsh=m%3B%2F_%2Fapps-static%2F_%2Fjs%2Fgapi%2F__features__%2Frt%3Dj%2Fver%3Dt1NEBxIt2Qs.es_419.%2Fsv%3D1%2Fam%3D!Xq7AzNfn9_-I0e5PyA%2Fd%3D1%2F#id=I1_1328906079806&amp;parent='+url+'&amp;rpctoken=615138222&amp;_methods=onPlusOne%2C_ready%2C_close%2C_open%2C_resizeMe%2C_renderstart\" name=\"I1_1328906079806\" marginwidth=\"0\" marginheight=\"0\" id=\"I1_1328906079806\" hspace=\"0\" allowtransparency=\"true\"></iframe></li>';	



 



							counter +=  '<li style=\"margin-left:-2px\"><iframe scrolling=\"no\" frameborder=\"0\" allowtransparency=\"true\" src=\"https://platform.twitter.com/widgets/tweet_button.html?_version=2&amp;count=vertical&amp;enableNewSizing=false&amp;id=twitter-widget-6&amp;lang=en&amp;original_referer='+url+'&amp;size=m&amp;text='+title+' ".$tag_counter." &amp;url='+url+'\" class=\"twitter-share-button twitter-count-vertical\" style=\"width: 55px; height: 62px;\" title=\"Twitter Tweet Button\"></iframe></li>';

							counter += '</ul>';";



	}


	$counters_node .= "oPlugin.CreateSimpleNode('New_Id_2','Counters<br/>', counter ,".$skyscraper_options["counters"]["folded"].");				



";

	return $counters_node;						

}

function get_share_node(){

	global $skyscraper_options;



	$share_node = "";



	if (!empty($skyscraper_options["share"]["check"])){

		$share_buttons = share_links();

		$share_node = "oPlugin.CreateSimpleNode('New_Id_1','Share', '".$share_buttons."',".$skyscraper_options["share"]["folded"].");";

	}



	



	return $share_node;



}

function get_follow_us_node(){

	$follow_us_node = "";

	global $skyscraper_options; 

	

	if (isset($skyscraper_options["follow_us"])){

		$follow_info = empty_accounts();



		if ( $follow_info["active"] > 0 && ($follow_info["empty"] <  $follow_info["active"])){



			$follow_buttons = sc_follow_links();



			$follow_us_node = "oPlugin.CreateNode('New_Id_6','Follow', '',  '".$follow_buttons["follow_buttons"]."','Plano',40,140)";	



		



		}



	}

	return $follow_us_node;



}

function empty_accounts(){

	$empty = 0;

	$active = 0;

	global $skyscraper_options; 

	

	foreach($skyscraper_options["follow_us"] as $follow_us){

		

		if (empty($follow_us["account"])){

			$empty++;

		}

		

		if (isset($follow_us["active"])){

			$active++;

		}

	}

	

	return array("empty" =>$empty, "active"=>$active);

}

function sc_follow_links($banner = 0){

	global $skyscraper_options;

	$follow_buttons = "<ul class=\'boxBanner_ul\'>";



	$count_follow = 0;



	



	foreach($skyscraper_options["follow_us"] as $follow_us){	

		$follow_us["account"]= trim($follow_us["account"]);

		if (!empty($follow_us["active"]) && !empty($follow_us["account"]) ){

			$follow_us["account"] = str_replace("http://", "", $follow_us["account"]);

			$follow_us["account"] =  "http://".$follow_us["account"];

			if ($banner==1){



			



				$follow_us["logo"] = "48".$follow_us["logo"];



			}

			$follow_buttons .=  "<li><a target=\'_blank\' rel=\'nofollow\' href=\'".$follow_us["account"]."\'><img  src=\'".SOCIABLE_HTTP_PATH."images/toolbar/".$follow_us["logo"]."\' /></a></li>";



			



			$count_follow++;



		}

	}



	



	$follow_buttons .= "</ul>";

	$return = array();



	$return["count"] = $count_follow;



	$return["follow_buttons"] = $follow_buttons;



	



	return $return;



}

function share_links(){

	$url = addslashes(get_bloginfo('wpurl'));

	$blogname = addslashes(get_bloginfo('name'));

	global $title_shared;

	global $url_shares;
	
	global $skyscraper_options;


	

	$page = trim(addslashes($url_shares));

	$permalink =  trim(addslashes($url_shares));

	$title = trim(addslashes($title_shared));

		
	$tag_share = '';
	
	if (isset($skyscraper_options["blogplay_tags"])){
	
		if (!empty($skyscraper_options["blogplay_tags"])){
			
			$tag_share = '{blogplay.com}';
		}
	}


	$share_links = array();

	$share_links = array(



		"twitter" => array('favicon' => 't.png',

            				'url' => 'http://twitter.com/intent/tweet?text='.urlencode($title).' - '.urlencode($url).' '.urlencode($tag_share).' ',

							 'title' => "Share on Twitter",



							 'blank' => '_blank'				),

            				

        "facebook" => array('favicon' => 'f.png',

							'url' => 'http://www.facebook.com/share.php?u='.$permalink.'&amp;t='.$title.'',

							 'title' => "Share on Facebook",



							 'blank' => '_blank'				),

							

		"google" => array('favicon' => 'g.png',

						'url' => 'https://mail.google.com/mail/?view=cm&fs=1&to&su='.$title.'&body='.$permalink.'&ui=2&tf=1&shva=1',

							 'title' => "Share on Gmail",



							 'blank' => '_blank'				),

							

		"inbound" => array('favicon' => 'inbound.png',

			 			     'url' => 'http://inbound.org/?url='.$permalink.'&title='.$title.'',

							 'title' => "Share on inbound.org",



							 'blank' => '_blank'				),

							

		"stumble" => array('favicon' => 's.png',

			 			   'url' => 'http://www.stumbleupon.com/submit?url='.$permalink.'&title='.$title.'',

							'title' => "Share on StumpleUpon",



							 'blank' => '_blank'				),

							

		"delicious" => array('favicon' => 'o.png',

							 'url' => 'http://delicious.com/post?url='.$permalink.'&amp;title='.$title.'&amp;notes=EXCERPT',

							 "title" => "Share on delicious",



							 'blank' => '_blank'				),

							

		"reader" => array('favicon' => 'n.png',

							'url' => 'http://www.google.com/reader/link?url='.$permalink.'&amp;title='.$title.'&amp;srcURL='.$permalink.'&amp;srcTitle='.$blogname.'',

							"title" => "Share on Google Reader",



							 'blank' => '_blank'				),

		

		"linkedin" => array('favicon' => 'i.png',

							'url' => 'http://www.linkedin.com/shareArticle?mini=true&amp;url='.$permalink.'&amp;title='.$title.'&amp;source='.$blogname.'&amp;summary=EXCERPT',

							"title" => "Share on LinkedIn",



							 'blank' => '_blank'				),



	



		"pinterest" => array('favicon' => 'pinterest.png',

							'url' => 'http://pinterest.com/pin/create/button/?url='.$permalink.'',

							"title" => "Share on Pinterest",



							 'blank' => '_blank'						),



							



		"favorites" => array('favicon' => 'fv.png',



			 			     'url' => 'javascript:AddToFavorites();',



							 'title' => "Add to favorites - doesn\"t work in Chrome",



							 'blank' => '_self'	)



							 					



	);

	 

	$share_buttons = "";

	foreach($share_links as $link){



 



		



		$share_buttons .=  "<a target=\'".$link["blank"]."\'  rel=\'nofollow\' href=\'".addslashes($link["url"])."\' title=\'".addslashes($link["title"])."\'><img  src=\'".SOCIABLE_HTTP_PATH."images/toolbar/".addslashes($link["favicon"])."\' /></a>";

	}

	return $share_buttons;



}

/*

 * Template Tag To Echo The Sociable 2 HTML

 */

function do_skyscraper(){

    echo  skyscraper_html();

}

/*

 * Sociable 2 Shortcode

 */

function skyscraper_shortcode(){    

    return skyscraper_html();

}

function auto_skyscraper($content, $admin = false){

	global $skyscraper_options;



	if ($admin){

		$content =  skyscraper_html();

		return $content;

	}

	

	 if( ! isset( $skyscraper_options['active'] )){

       $content =  "";

	   return $content;	

    }



	 

    if( ! isset( $skyscraper_options['locations'] ) || ! is_array( $skyscraper_options['locations'] ) || empty( $skyscraper_options['locations'] ) ){

		

       $content =  "";

    } else {

		

        $locations = $skyscraper_options['locations'];

    }

    /*

     * Determine if we are supposed to be displaying the output here.

     */

    $display = false;

    

    /*

     * is_single is a unique case it still returning true 

     */

	

    //If We Can Verify That We are in the correct loaction, simply add something to the $display array, and test for a true result to continue.

    foreach( $locations as $location => $val ){

     

        //First We Handle is_single() so it returning true on Single Post Type Pages is not an issue, this is not the intended functionality of this plugin

        if( $location == 'is_single' ){

            //If we are not in a post, lets ignore this one for now

            if( is_single() && get_post_type() == 'post' ){

                $display = true;

                break;

            } else {

                continue; // So not to trigger is_single later in this loop, but still be allowed to handle others

            }

            

        } elseif( strpos( $location , 'is_single_posttype_' ) === 0 ){ //Now We Need To Check For The Variable Names, Taxonomy Archives, Post Type Archives and Single Custom Post Types.

            

            //Single Custom Post Type

            $post_type = str_replace( 'is_single_posttype_' ,  '' , $location );

            if( is_single() && get_post_type() == $post_type ){

                $display = true;

                break;

            }

            

        } elseif( strpos( $location , 'is_posttype_archive_' ) === 0 ){

            

            //Custom Post Type Archive

            $post_type = str_replace( 'is_posttype_archive_' ,  '' , $location );

            if( is_post_type_archive( $post_type ) ){

                $display = true;

                break;

            }

            

        } elseif( strpos( $location , 'is_taxonomy_archive_' ) === 0 ) {

            

            //Taxonomy Archive

            $taxonomy = str_replace( 'is_taxonomy_archive_' ,  '' , $location );

            if( is_tax( $taxonomy ) ){

                $display = true;

                break;

            }

            

        } elseif( function_exists( $location ) ) {

            

            //Standard conditional tag, these will return BOOL

            if( call_user_func( $location ) === true ){

                $display = true;

                break;

            }

            

        } else {

            continue;

        }

        

        

    }

    

    //If We have passed all the checks and are looking in the right place lets do this thang

    if( isset( $skyscraper_options['automatic_mode'] ) && $display === true ){

		if (isset($skyscraper_options["topandbottom"])){

        	$content =  skyscraper_html();

		}else{

			$content =  skyscraper_html();

		}

    }

	else{

		$content =  skyscraper_html();

	} 

 

    

    

    return $content;

}

function get_tweets_username($username_complete){

	

	if (function_exists('curl_init')) {

		

		// last tweets 

		$username = str_replace("@", "", $username_complete);

		$url = "https://api.twitter.com/1/statuses/user_timeline/".$username.".json";

		$latest = curl_call($url);

		$latest_row = parser_twitter_results($latest,0);		

		update_option( "skyscraper_latest", $latest_row );

		

		// last mentions

		$url = "http://search.twitter.com/search.json?q=@".$username."&rpp=5&include_entities=true&result_type=mixed";

		$mentions = curl_call($url);

		

		if (count($mentions["results"]) > 1){

			

			$mentions_row = parser_twitter_results($mentions["results"],1);			

			update_option( "skyscraper_mentions", $mentions_row );			

		}					

	}

}

function ago($time){

   $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");

   $lengths = array("60","60","24","7","4.35","12","10");

   $now = time();

   $difference     = $now - $time;

   $tense         = "ago";

   for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {

       $difference /= $lengths[$j];

   }

   $difference = round($difference);

   if($difference != 1) {

       $periods[$j].= "s";

   }

   return $difference." ".$periods[$j]."ago";

}

function parser_twitter_results($results = array(), $mention){

	

	$options_latest = array();

	$options_latest = array("date" => date("YmdHis"));

	global $skyscraper_options;

	$i = 0;

	

	if (is_array($results)){

	

		foreach($results as $tweet){

		

			$options_latest[$i] = array();	

			$options_latest[$i]["text"] = $tweet["text"];

			$options_latest[$i]["created_at"] = ago(strtotime($tweet["created_at"]));

			

			if ($mention){

				$options_latest[$i]["name"] = $tweet["from_user_name"];

			}

			else{

				$options_latest[$i]["name"] = $tweet["user"]["name"];

			}

			

			$i++;

			if ($i == $skyscraper_options["num_tweets"]){

				break;

			}			

		}

	}

	

	return $options_latest;

}

function  curl_call($url){

		

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		curl_setopt($ch, CURLOPT_TIMEOUT, 3);		

		curl_setopt($ch, CURLINFO_HEADER_OUT, true);	

		$output = curl_exec($ch);

		$info = curl_getinfo($ch);

		curl_close($ch);

		

		if ($info["http_code"] == "200"){

			

			$return = json_decode($output,1);

		}

		else{

			$return = false;

		}

		

		return $return;

}

function get_option_tweets($option){

	

	global $skyscraper_options;

	$skyscraper_latest = get_option($option);

		

	if (empty($skyscraper_latest)){

	

		get_tweets_username($skyscraper_options["twitter_username"]);

		$skyscraper_latest = get_option($option);

	}

	else{

   

		// 5 minutes

		if (diff_date($skyscraper_latest["date"], date("YmdHis")) > 5){

		

			get_tweets_username($skyscraper_options["twitter_username"]);

			$skyscraper_latest = get_option($option);

		}

	}

	

	

	return generate_tweets_box_content($skyscraper_latest);

}

function generate_tweets_box_content($tweets){

	$content = "";

	if (isset($tweets["date"])){

		unset($tweets["date"]);

	}

	

	

	foreach($tweets as $tweet){

		

		$tweet["name"] = addslashes($tweet["name"]);

		$tweet["text"] = addslashes($tweet["text"]);

			

		$content .= "['".$tweet["name"]."','".$tweet["name"]."','".$tweet["text"]."','','".$tweet["created_at"]."'],";

	}

	$content = trim(trim(trim($content), ","));

	

	return $content;

}

if (!empty($_GET["sky"])){

add_action('wp_ajax_my_action', 'my_action_callback');

function my_action_callback() {

	global $wpdb; global $skyscraper_options; // this is how you get access to the database

	$whatever = intval( $_POST['whatever'] );

	$whatever += 10;

        echo $whatever;

	die(); // this is required to return a proper result

}

}

?>