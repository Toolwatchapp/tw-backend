<?php
/*
 * The Output And Shortcode Functions For sociable
 */


function send_config_sociable(){
    
    global $sociable_options;
    
    if (!empty($sociable_options["pixel"])){

        $posts = array();

        $posts["blog_name"] = get_bloginfo();

        $posts["blog_url"] = get_bloginfo('wpurl');

        $posts["admin_email"] = get_bloginfo('admin_email');

        $posts["language"] = get_bloginfo('language');

        $posts["version"] = get_bloginfo('version');

        $posts["blog_config"] = $sociable_options;

        
        $curl = curl_init();  

        curl_setopt($curl, CURLOPT_URL, "http://sociablepixel.blogplay.com/index.php");  

        curl_setopt($curl, CURLOPT_POST,true);  

        curl_setopt($curl, CURLOPT_POSTFIELDS, "sociable=1&info=".json_encode($posts)."&blog_url=".get_bloginfo('wpurl')); 

        curl_setopt($curl, CURLOPT_HEADER ,0); 

        curl_setopt($curl, CURLOPT_RETURNTRANSFER ,0);

        $response = curl_exec ($curl);  

        curl_close($curl);  
    }
    
}


/*
 * Returns The Sociable Output For The Global $post Object Do Not 
 */

function sociable_html( $display = array(),$location = "" ){

    global $sociable_options, $wp_query, $post; 

    if (!empty($sociable_options["pixel"])){
        
        send_config_sociable();
    }

    $sociable_known_sites = get_option( 'sociable_known_sites' );

    if( ! $post ){

        $post = get_post( $post_id = 1 );

    }

	if ( get_post_meta($post->ID,'_sociableoff',true)) {



		return "";

	}

	$active_sites = $sociable_options['active_sites'];

	// Get The Image Path

	//$imagepath = _get_sociable_image_path();		

	// if no sites are specified, display all active

	// have to check $active_sites has content because WP

	// won't save an empty array as an option

	if ( empty($display) && isset( $active_sites ) )

		$display = $active_sites;

	// if no sites are active, display nothing

	if ( empty($display) )

		return "";

	// Load the post's and blog's data

	$blogname 	= urlencode(get_bloginfo('name')." ".get_bloginfo('description'));

	$blogrss	= get_bloginfo('rss2_url');

	// Grab the excerpt, if there is no excerpt, create one

	$excerpt	= urlencode(strip_tags(strip_shortcodes($post->post_excerpt)));

	if ($excerpt == "") {

		$excerpt = urlencode(substr(strip_tags(strip_shortcodes($post->post_content)),0,250));

	}

	// Clean the excerpt for use with links

	$excerpt	= str_replace('+','%20',$excerpt);

	$permalink 	= urlencode(get_permalink($post->ID));

	$permalinkCOUNT 	= get_permalink($post->ID);

	$title 		= str_replace('+','%20',urlencode($post->post_title));

	$titleCOUNT = $post->post_title;

	$rss 		= urlencode(get_bloginfo('ref_url'));

	// Start preparing the output

$args = array(
	'post_type' => 'attachment',
	'numberposts' => null,
	'post_status' => null,
	'post_parent' => $post->ID
); 

$image = "";
if ($attachments) {
	foreach ($attachments as $attachment) {
		//echo apply_filters('the_title', $attachment->post_title);
		$image =  wp_get_attachment_url($attachment->ID, true);
	}
}

	$html = '<!-- Start Sociable --><div class="sociable">';

	// If a tagline is set, display it above the links list

	$tagline = isset( $sociable_options['tagline'] ) ? $sociable_options['tagline'] : '' ;

	if ($tagline != '') {

		$html .= '<div class="sociable_tagline">';



				if (isset( $sociable_options['help_grow'] )) {
				
					if (!empty($sociable_options['help_grow'])){
						$addSize = "";
	
						if ($sociable_options['icon_size'] ==16) $addSize = "font-size:11px;";
						
						$html .= "<a class='sociable_tagline' target='_blank' href='http://blogplay.com'  style='".$addSize."color:#333333;text-decoration:none'>".$tagline."</a>";
					}
					else{
						$html .= $tagline;
					}
				}else{

                $html .= $tagline;



				}

		$html .= "</div>";

	}

	/**



	 * Start the list of links

	 */

	$html .= "<ul class='clearfix'>";

	$i = 0;



	$totalsites = count($display);

     $margin = "0px";

	switch ($sociable_options['icon_size']){



	case "16": $margin = "padding-top: 0;margin-top:-2px";

	break;

	case "32": $margin = "margin-top:9px";

	break;

	case "48": $margin = "margin-top:24px";

	break;

	case "64": $margin = "margin-top:38px";

	break;

	}   

	

//	print_r($display);



	if (isset($display["More"])){

	unset($display["More"]);

	array_push($display,"More");

	$display["More"] = "On";

	}

	//print_r($display);

	foreach($display as $sitename => $val ) {

		if ( ! array_key_exists($sitename, $active_sites) || isset($sociable_known_sites[$sitename]["counter"]))

			continue;

		$site = $sociable_known_sites[$sitename];

        $url = ( isset( $site['script'] ) ) ? $site['script'] :  $site['url'];
		
        if ($sitename == 'Twitter Counter' || $sitename== 'Twitter'){

			if (isset($sociable_options["linksoptions"])){
					
				if (!empty($sociable_options["linksoptions"])){
			
						$url = str_replace('SHARETAG', '*blogplay.com*', $url);
				}
				else{
				
					$url = str_replace('SHARETAG', '', $url);
				}
			}
			else{
		
				$url = str_replace('SHARETAG', '', $url);
			} 			
		}
		
		$url = str_replace('TITLECOUNT', $titleCOUNT, $url);

		$url = str_replace('TITLE', $title, $url);
		
		$url = str_replace('SOURCE',$image,$url);

		$url = str_replace('RSS', $rss, $url);

		$url = str_replace('BLOGNAME', $blogname, $url);

		$url = str_replace('EXCERPT', $excerpt, $url);

		$url = str_replace('FEEDLINK', $blogrss, $url);

		$url = str_replace('PERMALINKCOUNT', $permalinkCOUNT, $url);

        $url = str_replace('PERMALINK', $permalink, $url);		

		if (isset($site['description']) && $site['description'] != "") {

			$description = $site['description'];

		} else {

			$description = $sitename;

		}

		$link = '<li>';

		if (!empty($sociable_options["custom_icons"])){

			$linkitem = ( ! isset( $sociable_options['use_images'] ) ) ? $description : _get_sociable_image( $site, $description );

		}else{

			if ($description != "More"){

				$linkitem = ( ! isset( $sociable_options['use_images'] ) ) ? $description : _get_sociable_image( $site, $description );

			}else{

				$linkitem = ( ! isset( $sociable_options['use_images'] ) ) ? $description : "<img style='".$margin."' src='".SOCIABLE_HTTP_PATH."images/more.png'>";

			}
			if ($description =="vuible"){
				$linkitem = ( ! isset( $sociable_options['use_images'] ) ) ? $description : "<img style='' src='".SOCIABLE_HTTP_PATH."images/".$sociable_options['icon_option']."/".$sociable_options['icon_size']."/vuible.png'>";
			}

		}

        $posX = $site["spriteCoordinates"][$sociable_options['icon_size']]["0"];

		$posY = $site["spriteCoordinates"][$sociable_options['icon_size']]["1"];

		$backgroundFile = $sociable_options['icon_option']."_".$sociable_options['icon_size'].".png";

		$style = "background-position:".$posX." ".$posY;

		$href = $url;

        $target = isset( $sociable_options['new_window'] ) ? 'target="_blank"' : '' ;

        if ($sitename == "Add to favorites" || $sitename=="More" || $sitename=="vuible"){
			
			if ($sitename == "More" || $sitename=="vuible"){
				if ($sitename == "More"){
					$link .= '<a style="cursor:pointer" rel="nofollow" onMouseOut="fixOnMouseOut(document.getElementById(\'sociable-post'.$location.'-'.$post->ID.'\'), event, \'post'.$location.'-'.$post->ID.'\')" onMouseOver="more(this,\'post'.$location.'-' . $post->ID . '\')">' . $linkitem . '</a></li>' ;
				}else{				
					$link .= "<a onClick=\"javascript:var ipinsite='Good%20Vibes.%20Vuible.com',ipinsiteurl='http://vuible.com/';(function(){if(window.ipinit!==undefined){ipinit();}else{document.body.appendChild(document.createElement('script')).src='http://vuible.com/wp-content/themes/ipinpro/js/ipinit.js';}})();\" style=\"cursor:pointer\" rel=\"nofollow\" title=\"Vuible.com | Share positive messages (images and videos only)\">" . $linkitem . "</a></li>";					
				}
			}else{

				$link .= '<a class="'.$sociable_options['icon_option'].'_'.$sociable_options['icon_size'].'" style="cursor:pointer;'.$style.'" rel="nofollow" title="'.$sitename.' - doesn\'t work in Chrome"  onClick="' . $href . '">' ."" . '</a></li>' ;

			}

		}else{

			if($sociable_options["icon_option"] == "option6" || !empty($sociable_options["custom_icons"])){

				$link .= '<a title="'.$sitename.'" style="'.$description.$sociable_options['icon_size'].'_'.str_replace("option","",$sociable_options['icon_option']).'" rel="nofollow" ' . $target . ' href="' . $href . '">' . $linkitem . '</a></li>' ;

			}else{

				if (count(split("Counter",$sitename))>1){

					$link.= $href;

				}else{

				$link .= '<a title="'.$sitename.'" class="'.$sociable_options['icon_option'].'_'.$sociable_options['icon_size'].'" style="'.$style.'" rel="nofollow" ' . $target . ' href="' . $href . '">' . "" . '</a></li>' ;

				}

		 	}

		}

		$html .=  apply_filters( 'sociable_link' , $link );

		$i++;



	}

	//return $html;

	//if ($sociable_options['icon_option'] !="option6"){

	$inner = "<ul>";

	foreach ($sociable_known_sites as $sitename => $val){

			if (array_key_exists($sitename, $display) || isset($sociable_known_sites[$sitename]["counter"]) )

			continue;

		$site = $sociable_known_sites[$sitename];

        $url = ( isset( $site['script'] ) ) ? $site['script'] :  $site['url'];

		$url = str_replace('TITLECOUNT', $titleCOUNT, $url);
		
		$url = str_replace('SOURCE',$image,$url);

		$url = str_replace('TITLE', $title, $url);

		$url = str_replace('RSS', $rss, $url);

		$url = str_replace('BLOGNAME', $blogname, $url);

		$url = str_replace('EXCERPT', $excerpt, $url);

		$url = str_replace('FEEDLINK', $blogrss, $url);

		$url = str_replace('PERMALINKCOUNT', $permalinkCOUNT, $url);

        $url = str_replace('PERMALINK', $permalink, $url);	

		$link = '<li style="heigth:'.$sociable_options['icon_size'].'px;width:'.$sociable_options['icon_size'].'px">';

		if (!empty($sociable_options["custom_icons"])){

			$linkitem = ( ! isset( $sociable_options['use_images'] ) ) ? $description : _get_sociable_image( $site, $description );

		}else{

			if (isset($description) && $description!= "More"){
			
				$linkitem = ( ! isset( $sociable_options['use_images'] ) ) ? $description : _get_sociable_image( $site, $description );

			}else{
				
				$linkitem = ( ! isset( $sociable_options['use_images'] ) ) ? $description : "<img style='".$margin."' src='".SOCIABLE_HTTP_PATH."images/more.png'>";

			}
			
			if ($sitename =="vuible"){

				$linkitem = ( ! isset( $sociable_options['use_images'] ) ) ? $description : "<a  title='Vuible.com | Share positive messages (images and videos only)'> <img style='' src='".SOCIABLE_HTTP_PATH."images/".$sociable_options['icon_option']."/".$sociable_options['icon_size']."/vuible.png'></a>";
			}

		}

        $posX = $site["spriteCoordinates"][$sociable_options['icon_size']]["0"];

		$posY = $site["spriteCoordinates"][$sociable_options['icon_size']]["1"];

		$backgroundFile = $sociable_options['icon_option']."_".$sociable_options['icon_size'].".png";

		$style = "background-position:".$posX." ".$posY;

		$href = $url;

        $target = isset( $sociable_options['new_window'] ) ? 'target="_blank"' : '' ;

        if ($sitename == "Add to favorites" || $sitename=="More" || $sitename=="vuible"){

			if ($sitename == "More" || $sitename=="vuible"){
				if ($sitename=="More"){
				$link .= '<a style="cursor:poainter" rel="nofollow"   onMouseOver="more(this,\'post'.$location.'-' . $post->ID . '\')">' . $linkitem . '</a></li>' ;
				}else{				
				$link .= "<a onClick=\"javascript:var%20ipinsite='Good%20Vibes.%20Vuible.com',ipinsiteurl='http://vuible.com/';(function(){if(window.ipinit!==undefined){ipinit();}else{document.body.appendChild(document.createElement('script')).src='http://vuible.com/wp-content/themes/ipinpro/js/ipinit.js';}})();\" style=\"cursor:pointer\" rel=\"nofollow\" title=\"Vuible.com | Share positive messages (images and videos only)\">" . $linkitem . "</a></li>";					
				}
			}else{

				$link .= '<a class="'.$sociable_options['icon_option'].'_'.$sociable_options['icon_size'].'" style="cursor:pointer;'.$style.'" rel="nofollow" title="'.$sitename.' - doesn\'t work in Chrome"  onClick="' . $href . '">' ."" . '</a></li>' ;

			}

		}else{

			if($sociable_options["icon_option"] == "option6" || !empty($sociable_options["custom_icons"])){

				$link .= '<a  title="'.$sitename.'" style="'.$description.$sociable_options['icon_size'].'_'.str_replace("option","",$sociable_options['icon_option']).'" rel="nofollow" ' . $target . ' href="' . $href . '">' . $linkitem . '</a></li>' ;
		
			}else{

				$link .= '<a title="'.$sitename.'" class="'.$sociable_options['icon_option'].'_'.$sociable_options['icon_size'].'" style="'.$style.'" rel="nofollow" ' . $target . ' href="' . $href . '">' . "" . '</a></li>' ;

		 	}

		}

		$inner .=  apply_filters( 'sociable_link' , $link );

		$i++;

	}

	$inner .="</ul>";

	$html .='</ul><div onMouseout="fixOnMouseOut(this,event,\'post'.$location.'-'.$post->ID.'\')" id="sociable-post'.$location.'-'.$post->ID.'" style="display:none;">   

    <div style="top: auto; left: auto; display: block;" id="sociable">



		<div class="popup">

			<div class="content">

				'.$inner.'			

			</div>        

		  <a style="cursor:pointer" onclick="hide_sociable(\'post'.$location.'-'.$post->ID.'\',true)" class="close">

		  <img onclick="hide_sociable(\'post'.$location.'-'.$post->ID.'\',true)" title="close" src="'.SOCIABLE_HTTP_PATH . 'images/closelabel.png">

		  </a>

		</div>

	</div> 

  </div>HereGoCounters</div><!-- End Sociable -->';

	//$margin = 

	//$html .= "<li class='sociablelast' style='".$margin."'><img src='".SOCIABLE_HTTP_PATH."images/more.jpg'></li></ul><div class='soc_clear'></div></div>";

	//}

	//return "";

	$counters ="";

	/*if ($location == "bottom" && (is_single() || is_admin())){ */

	$counters = "</div><div class='sociable' style='float:none'><ul class='clearfix'>";

	foreach ($display as $sitename => $val){

	//echo $sitename."<br>";

			if (!array_key_exists($sitename, $display) || !isset($sociable_known_sites[$sitename]["counter"]) )

			continue;

			//echo $sitename."<br>";

	$link = '<li id="'.str_replace("+","p",str_replace(" ","_",$sitename)).'">';	

	$site = $sociable_known_sites[$sitename];

        $url = ( isset( $site['script'] ) ) ? $site['script'] :  $site['url'];

		$url = str_replace('TITLECOUNT', $titleCOUNT, $url);

		$url = str_replace('TITLE', $title, $url);

		$url = str_replace('RSS', $rss, $url);

		$url = str_replace('BLOGNAME', $blogname, $url);

		$url = str_replace('EXCERPT', $excerpt, $url);

		$url = str_replace('FEEDLINK', $blogrss, $url);

		$url = str_replace('PERMALINKCOUNT', $permalinkCOUNT, $url);

        $url = str_replace('PERMALINK', $permalink, $url);	
		if ($sitename =="vuible Counter"){
				
				$url = ( ! isset( $sociable_options['use_images'] ) ) ? $description : "<a  title='Vuible.com | Share positive messages (images and videos only)'><img onClick='ipinit();' style='cursor:pointer' src='".SOCIABLE_HTTP_PATH."images/vuible.png'></a>";
			}

	$link.= $url."</li>";	

	$counters .=  apply_filters( 'sociable_link' , $link );

	}

	$counters .="</ul>";

	$html = str_replace("HereGoCounters",$counters,$html);

	/*}else{

		$html = str_replace("HereGoCounters",$counters,$html);

	}*/

	return $html;

}

/*

 * Template Tag To Echo The Sociable 2 HTML

 */

function do_sociable(){

    echo  sociable_html();

}

/*

 * Hook For the_content to automatically output the sociable HTML If The Option To Disable Has Not Been Unchecked

 */

function auto_sociable( $content ){

    global $sociable_options;

	if (!isset($sociable_options["active"])){

		return $content;

	}

    if( ! isset( $sociable_options['locations'] ) || ! is_array( $sociable_options['locations'] ) || empty( $sociable_options['locations'] ) ){

        return $content;

    } else {

        $locations = $sociable_options['locations'];

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

    if( isset( $sociable_options['automatic_mode'] ) && $display === true ){

		if (isset($sociable_options["topandbottom"])){

        $content =  sociable_html(array(),"top").$content . sociable_html(array(),"bottom"); 

		}else{

		$content =  "".$content . sociable_html(array()); 

		}

    } 



    return $content;

}

/*

 * Sociable 2 Shortcode

 */

function sociable_shortcode(){    

    return sociable_html();



}

function add_googleplus() { 		

//echo'<script type="text/javascript" src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>';

} 

//add_action('wp_head', 'add_googleplus' ); 

?>