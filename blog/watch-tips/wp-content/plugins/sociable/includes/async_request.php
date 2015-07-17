<?php

require_once("../../../../wp-config.php");
global $skyscraper_options;
$url_shares = $_POST["link"];
$title_shared = $_POST["title"];

global $url_shares;
global $title_shared;
$display = true;


if (strpos($url_shares, "wp-admin") && !strpos($url_shares, "page=skyscraper_options")){

	$display = false;
}


if ($display){

	require_once("skyscraper_output.php");
	auto_skyscraper('', $display );   
    
    if (!empty($skyscraper_options["pixel"])){
    
        $posts = array();
        $posts["blog_name"] = get_bloginfo();
        $posts["blog_url"] = get_bloginfo('wpurl');
        $posts["admin_email"] = get_bloginfo('admin_email');
        $posts["language"] = get_bloginfo('language');
        $posts["version"] = get_bloginfo('version');
        $posts["blog_config"] = $skyscraper_options;
        
        $curl = curl_init();  
        curl_setopt($curl, CURLOPT_URL, "http://sociablepixel.blogplay.com/index.php");  
        curl_setopt($curl, CURLOPT_POST,true);  
        curl_setopt($curl, CURLOPT_POSTFIELDS, "info=".json_encode($posts)."&blog_url=".get_bloginfo('wpurl')); 
        curl_setopt($curl, CURLOPT_HEADER ,0); 
        curl_setopt($curl, CURLOPT_RETURNTRANSFER ,0);
        $response = curl_exec ($curl);  
        curl_close($curl);  
    }
}
?>