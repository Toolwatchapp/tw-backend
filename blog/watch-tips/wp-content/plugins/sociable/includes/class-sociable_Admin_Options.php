<?php
/*
 * Administration Options Class For Sociable 2
 */
class sociable_Admin_Options{
    /**
     * A Function To Hook To Admin Init.
     */
    function init(){        
        register_setting( 'sociable_options_group' , 'sociable_options' );
        //Add The Settings Sections
       // add_settings_section( 'sociable_locations', __( 'Locations' ),  array( 'sociable_Admin_Options' , 'location_options_callback' )  , 'sociable_options' );
       // add_settings_section( 'sociable_options', __( 'General Options' ),  array( 'sociable_Admin_Options' , 'general_options_callback' )  , 'sociable_options' );
		register_setting( 'skyscraper_options_group' , 'skyscraper_options' );
	//	add_settings_section( 'sociable_locations', __( 'Locations' ),  array( 'sociable_Admin_Options' , 'location_options_callback' )  , 'skyscraper_options' );
    }
    function skyscraper_init(){        
    	register_setting( 'skyscraper_options_group' , 'skyscraper_options' );
		add_settings_section( 'sociable_locations', __( 'Locations' ),  array( 'sociable_Admin_Options' , 'location_options_callback' )  , 'skyscraper_options' );
        //Add All The Settings Fields
        //self::add_settings_fields();      
    }
    function Select_Sociable_Page(){
        global $sociable_options;
		?>

			<style>

			.Title-Box .BG-Middle {

					vertical-align: middle;

			}

			</style>

			<div class="wrap" style="margin-top:25px">    
					<div style="width: 80%; margin-left: 25px; color: rgb(147, 147, 147); font-weight: bold; font-size: 15px;">
			Congrats for joining the leader in the sharing space of WordPress plugins. "Sociable" is totally FREE, has over 1,7 million downloads and now you can enjoy Fueto and make your searches more Sociable.<br /> <br />
					</div>
			<div class="wrap" style="width:42%;float:left">                
                <?php //wp_nonce_field('sociable-config'); ?>
                <TABLE class="Title-Box" cellspacing="0" cellpadding="0" id="Preview-Title" style="margin:0 0 0 25px">
				<TR>
					<TD class="Border-Left" ></TD><TD  class="BG-Middle" >Skyscraper Sociable</TD><TD class="Border-Right"></TD>
				</TR>
				<TR> 
					<TD colspan="3" >
							<table>
							<tr>
							<td>
							<img src="<?php echo SOCIABLE_HTTP_PATH ?>images/skyphoto.png" style="margin-left:-5px;margin-top:20px;" />
							</td>
							<td valign="top" >
								<br/><br/>
								<span style="font-size:18px;color:#18305e;font-weight:bold;">Skyscraper Sociable</span>
								<p style="font-size:12px;color:#939393;font-weight:bold;" >
											Now introducing the ultimate advanced and feature packed plugin for setting up rating system on your WordPress blog. 
<br/ ><br/ >
Sociable Skyscraper allows you to set up different rating systems for posts, pages and comments with great degree of customization.
<br/ ><br/ >
List of features is so smart and non-stop growing:
<br/ ><br/ >
You can get more "Sociable" with Sociable Skyscraper and easily getting
Rating and Review of: posts, pages, comments, Facebook, G+, LinkedIN,
Twitter as well as multiple ratings for posts and pages. Visitor's counter,
visitor's from Facebook and Twitter... As a plus you get an easy way to get
TOP or HOME from Sociable Skyscraper.
<br/ ><br/ >
Enjoy it now!!!
<br/ ><br/ >
Be Sociable, Share!!! 
								</p>
								
								<a href="?page=skyscraper_options" style="color:#ffffff;text-decoration:none;" ><img src="<?php echo SOCIABLE_HTTP_PATH ?>images/button_newsky.png" ></a>
							
							</td>
							</tr>

							

							</table>
					</TD>
					
					
				</TR>
				</TABLE>
                <BR/>
				
			</div>
			<div class="wrap" style="margin-left:30px;width:48%;float:left">
			<TABLE class="Title-Box" cellspacing="0" cellpadding="0" id="Preview-Title" style="margin:0 0 0 25px">
				<TR>
					<TD class="Border-Left" ></TD><TD  class="BG-Middle" >Classic Sociable</TD><TD class="Border-Right"></TD>
				</TR>
				<TR>
					<TD colspan="3" >
						<div style="margin-left:5px;">
							<br /><br />
								<span style="font-size:18px;color:#18305e;font-weight:bold;">Classic Sociable</span>
							
								<p style="font-size:12px;color:#939393;font-weight:bold;" >
									We've improved our visual interface, the default icons are now much
<br/ >
more appealing, and a touch bit larger (you do want your readers to
<br/ >
share your posts, don't you? :) Get it now! 
								</p>
									<img src="<?php echo SOCIABLE_HTTP_PATH ?>images/socciable_old.png" ><br/>
									<div style="height: 176px;"></div>
								<a href="?page=sociable_options" style="color:#ffffff;text-decoration:none;" ><img src="<?php echo SOCIABLE_HTTP_PATH ?>images/button_sociabb.png" ></a>
							</div>
					</TD>
					
				</TR>
				</TABLE>
                <BR/>
			
			</div>

			</div>
    <?php }
    
    function create_select_options($value){
	
		for($i=3; $i<=9; ){
			
			$sel = "";
			if ($value == $i){
				$sel = "selected";
			}
			echo "<option ".$sel." value='".$i."'> latest ".$i."</option>";
				
			$i = $i+3;		
		}
	}
	
    
    function Create_Options_Page_Skycraper(){  
	        global $skyscraper_options;
            global $sociable_options;
		?>
			<style>

			.Title-Box .BG-Middle {

					vertical-align: middle;

			}

			</style>
			<div class="wrap" style="width:48%;float:left">
			<DIV style="margin:0 0 0 25px" class="Post-subTXT" id="Post-subTXT" >			
			<iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Fblogplay.com%2F&amp;send=false&amp;layout=standard&amp;width=450&amp;show_faces=true&amp;action=recommend&amp;colorscheme=light&amp;font&amp;height=80&amp;appId=133479460071366" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:450px; height:40px;" allowTransparency="true"></iframe>
	<a href="https://twitter.com/share" class="twitter-share-button" data-url="http://blogplay.com" data-text="Check the sociable plugin" >Tweet</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>	
			<br />	
			</div>
            <form method="post" action="options.php" id="form1" autocomplete="off">
                <?php wp_nonce_field('sociable-config'); ?>
                <INPUT type="hidden" class="version-INPUT" id="version" name="skyscraper_options[version]" value="<?php echo $skyscraper_options["version"];?>" /> 
                <TABLE class="Title-Box" cellspacing="0" cellpadding="0" id="Preview-Title" style="margin:0 0 0 25px">
				<TR>
					<TD class="Border-Left" ></TD><TD  class="BG-Middle" ><?php  _e("Style Options","sociable");?></TD><TD class="Border-Right"></TD>
				</TR>
				</TABLE>
                <BR/>               
					<DIV style="margin:0 0 0 25px" class="Post-subTXT" id="Post-subTXT" >
							<?php  _e("Text Size","sociable");?>: 
							<select id="text_size" name="skyscraper_options[text_size]" style="margin-left:73px">
							<?php 
								for($px=10; $px <= 20; $px++) {
									$sel = "";
									if($px== $skyscraper_options["text_size"])$sel = "selected";
							?>
								<option <?php echo $sel?> value="<?php echo $px?>"><?php echo $px?>px</option>
							<?php 
							}?>
						</select>
					</DIV>					
					<DIV style="margin:0 0 0 24px" class="Post-subTXT" id="Post-subTXT" ><?php  _e("Widget Width","sociable");?>: 
						<select id="text_size" id="widget_width" name="skyscraper_options[widget_width]" style="margin-left:50px">
							<?php 
							for($wi=70; $wi <= 90; ) {
									$sel = "";
									if($wi== $skyscraper_options["widget_width"])$sel = "selected";
							?>
								<option <?php echo $sel?> value="<?php echo $wi?>"><?php echo $wi?>px</option>
							<?php 
							$wi +=5;
							}?>
						</select>
					</DIV>
					<DIV style="margin:0 0 0 25px" class="Post-subTXT" id="Post-subTXT" ><?php  _e("Background Color","sociable");?>: 
						<input value="<?php echo $skyscraper_options['background_color']?>" style="margin-left:22px" id="background_color" name="skyscraper_options[background_color]" type="text"  /> ( #fefefe default color)
					</DIV>
					<DIV style="margin:0 0 0 25px" class="Post-subTXT" id="Post-subTXT" ><?php  _e("Labels Color","sociable");?>: 
						<input value="<?php echo $skyscraper_options['labels_color']?>" style="margin-left:49px" id="background_color" name="skyscraper_options[labels_color]" type="text"  /> ( #f7f7f7 default color)
					</DIV>
						<BR/>			
				<TABLE class="Title-Box" cellspacing="0" cellpadding="0" id="Tagline-Title">
					<TR>
						<TD class="Border-Left" ></TD><TD  class="BG-Middle" >
						Social Options
						</TD>
						<TD class="Border-Right"></TD>
					</TR>
				</TABLE>
				<BR/>
				<DIV  style="margin:0 0 0 25px" class="Post-subTXT">
				<?php  _e("Your Twitter username","sociable");?>:<?php
					if (!empty($skyscraper_options["twitter_username"])){
						$twitter_username = $skyscraper_options["twitter_username"];
					}
					else{
						$twitter_username = "@";
					}
				?>
				<input type="text" name="skyscraper_options[twitter_username]" value="<?php echo $twitter_username?>"  />	
				<select name="skyscraper_options[num_tweets]" id="num_tweets">
				<?php self:: create_select_options($skyscraper_options["num_tweets"]) ?>
				</select>
				</DIV>
				<BR/>
				<DIV style="border: 1px solid rgb(223, 223, 223); margin-left: 22px; font-size: 10px; font-style: italic; width: 327px; padding: 0px 11px;">
				<p>This feature will read your latest tweets and mentions posted by other users and show them on skyscraper sociable.
				<p>Sociable will save and use your twitter username only to read tweets. </p>
				
				<p>Your visitors can read the information that you are sharing.</p>
				<p>If you agree check here:
				
				<?php
					
					$sel = "";
					if (isset($skyscraper_options["accept_read_twitter"])){
				
						if ($skyscraper_options["accept_read_twitter"] == 1){
							
							$sel = "checked";
						}
					}
				?>
				<input type="checkbox" <?php echo $sel?> name="skyscraper_options[accept_read_twitter]" value="1"/> 
				</p>
				</DIV>
				<BR/>
				<BR/>
				<DIV  style="margin:0 0 0 25px" class="Post-subTXT">
				<?php  _e("Your RSS feed","sociable");?>: 
				<?php
				
					if (!empty($skyscraper_options["rss_feed"])){
						$rss_feed = $skyscraper_options["rss_feed"];
					}
					else{
						$rss_feed = "http://";
					}
				?>
				<input type="text" name="skyscraper_options[rss_feed]" style="margin-left: 46px;" value="<?php echo $rss_feed?>"  />
				<select name="skyscraper_options[num_rss]" id="num_rss">
				<?php self:: create_select_options($skyscraper_options["num_rss"]) ?>
				</select>
				</DIV><BR/>
				<DIV style="border: 1px solid rgb(223, 223, 223); margin-left: 22px; font-size: 10px; font-style: italic; width: 327px; padding: 0px 11px;">
				<p>This feature will read your rss posts and show them on skyscraper sociable. </p>				
				<p>Sociable will save and use the rss url only to read posts.</p>			
				<p>Your visitors can read the information that you are sharing.</p> 				
				<p>If you agree check here:
				
				<?php
					
					$sel = "";
					if (isset($skyscraper_options["accept_read_rss"])){
				
						if ($skyscraper_options["accept_read_rss"] == 1){
							
							$sel = "checked";
						}
					}
				?>
				<input type="checkbox" <?php echo $sel?> name="skyscraper_options[accept_read_rss]" value="1"/></p> 
				</DIV>
				<BR/><BR/>
				<DIV  style="margin:0 0 0 25px" class="Post-subTXT">
							<?php
					

								$checked = "";
								if (isset($skyscraper_options["counters"]["check"])){
									$checked = "checked";
								}
								
								$folded = "checked";
								$unfolded = "";
								if (isset($skyscraper_options["counters"]["folded"])){
								    
									if($skyscraper_options["counters"]["folded"] == "1"){
									   
										$folded = "checked";
										$unfolded= "";
									}else{
									   
										$unfolded = "checked";
										$folded= "";
									}
								}
								
							
							?>
					<input type="checkbox" <?php echo $checked ?> name="skyscraper_options[counters][check]" id="" /> 
					Counters 
					<input name="skyscraper_options[counters][folded]" <?php echo $unfolded?> value="0" type="radio">Folded 
					<input name="skyscraper_options[counters][folded]" <?php echo $folded?> value="1" type="radio">Unfolded
				</DIV><BR/>
				<DIV style="border: 1px solid rgb(223, 223, 223); margin-left: 22px; font-size: 10px; font-style: italic; width: 327px; padding: 0px 11px;">
				<p>This feature load Facebook Counter, Twitter Counter and Google Plus Counter.</p>
				<p>Will load scripts from each site and show information of yours visitors and maybe and could delay the load of the page.</p>
				</p>
				</DIV> 
				<BR/> <BR/>
				<DIV  style="margin:0 0 0 25px" class="Post-subTXT">
					<?php
								$checked = "";
								if (isset($skyscraper_options["share"]["check"])){
									$checked = "checked";
								}
                                else{
                                    $checked = "";
                                }
								
								if (isset($skyscraper_options["share"]["folded"])){
										if($skyscraper_options["share"]["folded"] == "1"){
											$folded = "checked";
											$unfolded= "";
										}else{
											$unfolded = "checked";
											$folded= "";
										}
								}
								
								
							?>
					
					<input type="checkbox" <?php echo $checked ?> name="skyscraper_options[share][check]" /> Share 
					<input style="margin-left:19px" <?php echo $unfolded?> value="0" name="skyscraper_options[share][folded]" type="radio">Folded 
					<input name="skyscraper_options[share][folded]" <?php echo $folded?> value="1"  type="radio">Unfolded
				</DIV>
				<DIV   class="Content-Box" id="Preview-Content">
				
				</DIV>
					<div style="clear:both"></div>				
		
				<BR/>
				<DIV class="Content-Box" id="Preview-Content">
					<ul class="items_li">
						<li>
							<?php
								$checked = "";
								if (isset($skyscraper_options['follow_us_check']) && $skyscraper_options['follow_us_check'] == "on"){
									$checked = "checked";
								}
							?>
							Follow Us
							<ul class="sub_item_li">
								<li>
									<?php
										$checked = "";
										if (isset($skyscraper_options['follow_us']['twitter']["active"]) && $skyscraper_options['follow_us']['twitter']["active"] == "on"){
											$checked = "checked";
										}
									?>
									<input <?php echo $checked ?> name="skyscraper_options[follow_us][twitter][active]" type="checkbox" style="padding-bottom:5px" />
									<?php
										$account = "http://twitter.com/";
										if(!empty($skyscraper_options["follow_us"]["twitter"]["account"])){
												$account = $skyscraper_options["follow_us"]["twitter"]["account"];
										}
									?>
						<input type="hidden" value="t.png" name="skyscraper_options[follow_us][twitter][logo]" />
						<img style="padding-bottom:5px" src="<?php echo SOCIABLE_HTTP_PATH ?>images/toolbar/t.png"/>
						<input size="40" name="skyscraper_options[follow_us][twitter][account]" value="<?php echo $account?>" type="text" />
								</li>
								<li>
									<?php
										$checked = "";
										if ( isset($skyscraper_options['follow_us']['feed']["active"]) && $skyscraper_options['follow_us']['feed']["active"] == "on"){
											$checked = "checked";
										}
									?>
									<input <?php echo $checked ?> name="skyscraper_options[follow_us][feed][active]" type="checkbox" style="padding-bottom:5px" />
							<input type="hidden" value="rss.png" name="skyscraper_options[follow_us][feed][logo]" />
									<?php
										$rss = "http://";
										if(!empty($skyscraper_options["follow_us"]["feed"]["account"])){
												
												$rss = $skyscraper_options["follow_us"]["feed"]["account"];
										}
									?>
						<img style="padding-bottom:5px" src="<?php echo SOCIABLE_HTTP_PATH ?>images/toolbar/rss.png"/>
						<input size="40" value="<?php echo $rss?>" name="skyscraper_options[follow_us][feed][account]" type="text" />
								</li>
								<li>
									<?php
										$checked = "";
										if ( isset($skyscraper_options['follow_us']['fb']["active"]) && $skyscraper_options['follow_us']['fb']["active"] == "on"){
											$checked = "checked";
										}
									?>
									<input <?php echo $checked ?> name="skyscraper_options[follow_us][fb][active]" type="checkbox" style="padding-bottom:5px" />
								<input type="hidden" value="f.png" name="skyscraper_options[follow_us][fb][logo]" />
							
									<?php
										$fb = "http://facebook.com/";
										if(!empty($skyscraper_options["follow_us"]["fb"]["account"])){
												
												$fb = $skyscraper_options["follow_us"]["fb"]["account"];
										}
									?>
							<img style="padding-bottom:5px" src="<?php echo SOCIABLE_HTTP_PATH ?>images/toolbar/f.png"/>
							<input size="40" value="<?php echo $fb?>" name="skyscraper_options[follow_us][fb][account]" type="text" />
								</li>
								
								<li>
									<?php
										$checked = "";
										if ( isset($skyscraper_options['follow_us']['li']["active"]) && $skyscraper_options['follow_us']['li']["active"] == "on"){
											$checked = "checked";
										}
									?>
									<input <?php echo $checked ?> name="skyscraper_options[follow_us][li][active]" type="checkbox" style="padding-bottom:5px" />
						<input type="hidden" value="i.png" name="skyscraper_options[follow_us][li][logo]" />
						<input type="hidden" value="linkedin.com/in/" name="skyscraper_options[follow_us][li][url]" />
									<?php
										$li = "http://linkedin.com/";
										if(!empty($skyscraper_options["follow_us"]["li"]["account"])){
												
												$li = $skyscraper_options["follow_us"]["li"]["account"];
										}
									?>
					<img style="padding-bottom:5px" src="<?php echo SOCIABLE_HTTP_PATH ?>images/toolbar/i.png"/>
					<input size="40" value="<?php echo $li?>" name="skyscraper_options[follow_us][li][account]" type="text" />
								</li>								
							</ul>
						</li>						
					</ul>
				</DIV>
				<br />				

<div class="Content-Box" id="Active-Content" style="display: block;">
				<br>
				<div align="center" style="width:100%;">
					<table align="center" cellspacing="0" cellpadding="10" border="0" class="GeneralOptions-List">
						<tbody><tr valign="top">
							<td align="right" class="Title">Active Sociable Banner</td>
							<td align="left" style="width:5px;">			
				<?php
					
					$sel = "";
					
					if (isset($skyscraper_options["sociable_banner"])){
							
							if (!empty($skyscraper_options["sociable_banner"])){
								$sel = "checked";
							}
							
					}
				
				?>
				
		<input type="checkbox" name="skyscraper_options[sociable_banner]" id="sociable_banner" <?php echo $sel?>></td>
							<td align="left" class="Content">
							<span class="TXT">Active Sociable "Reminder to Share" Banner / Check if you want to remind your readers Share your content.</span>
							<br>
							</td>
						</tr>
						<tr valign="top">
							<td align="right" class="Title">Blogplay Tags</td>

							<td align="left" style="width:5px;">			

				<?php
					$sel = "";
					if (isset($skyscraper_options["blogplay_tags"])){
							if (!empty($skyscraper_options["blogplay_tags"])){

								$sel = "checked";

							}
					}
				?>

		<input type="checkbox" name="skyscraper_options[blogplay_tags]" id="blogplay_tags" <?php echo $sel?>></td>
							<td align="left" class="Content">
							<span class="TXT">Accept include the blogplay.com tag into my shares and counters.</span>
							<br>
							</td>
						</tr>
						<tr valign="top">
							<td align="right" class="Title">Banner's label (35 char Max.)</td>
							
							<td align="left" style="width:5px;">
							<?php 
								$sel = 'Please spread the word: Be Sociable, Share!';
								if (isset($skyscraper_options["sociable_banner_text"])){
									if (!empty($skyscraper_options["sociable_banner_text"])){
										$sel = $skyscraper_options["sociable_banner_text"];
									}
								}
							?>										
							</td>
							<td align="left" class="Content">
							
							<span class="TXT"> <input style="width:245px !important" type="text" name="skyscraper_options[sociable_banner_text]"  value="<?php echo $sel?>"></span>
							<br>
							</td>
						</tr>
			
							<tr valign="top">
						
							<td align="right" class="Title">Banners Timer (sec.)</td>
							
							<td align="left" style="width:5px;">
														
							</td>
							<td align="left" class="Content">							
							<span class="TXT">
							
							<select name="skyscraper_options[sociable_banner_timer]" id="banner_timer" >
								<?php  
									for($timer=10; $timer <= 120; $timer++){ 
										
										$sel = "";
										
										if (!empty($skyscraper_options["sociable_banner_timer"])){
										
											if ($skyscraper_options["sociable_banner_timer"] == $timer){
												
												$sel = "selected";
											}
										}		
								?>
								<option value="<?php echo $timer?>" <?php echo $sel ?> ><?php echo $timer?></option>
								<?php 
								
										$timer = $timer + 4;
									} ?>
							</select>
							
							</span>
							<br>
							</td>
						</tr>
						 
						<tr valign="top">
						
							<td align="right" class="Title">Font Color <a title="default color #6A6A6A" class="default_values">(#6A6A6A)</a></td>
							
							<td align="left" style="width:5px;">
														
							</td>
							<td align="left" class="Content">							
							
							<span class="TXT">
							
							<?php 
								$sel = '#6A6A6A';
								
								if (isset($skyscraper_options["sociable_banner_colorFont"])){
									
									if (!empty($skyscraper_options["sociable_banner_colorFont"])){
										
										$sel = $skyscraper_options["sociable_banner_colorFont"];
									}
								}
							?>
							
							<input type="text" value="<?php echo $sel?>" name="skyscraper_options[sociable_banner_colorFont]" style="width:81px !important">						
							</span>
							<br>
							</td>
						</tr>
						<!-- Font Size -->
						<tr valign="top">
					
						<td align="right" class="Title">Font Size <a title="default size 9px" class="default_values">(9px)</a></td>
						
						<td align="left" style="width:5px;">
													
						</td>
						<td align="left" class="Content">							
						<span class="TXT">
					
						<select name="skyscraper_options[sociable_banner_fontSize]" id="banner_fontSize" >
								<?php  
									for($fontSize=8; $fontSize <= 16; $fontSize++){ 
										
										$sel = "";
										
										if (!empty($skyscraper_options["sociable_banner_fontSize"])){
										
											if ($skyscraper_options["sociable_banner_fontSize"] == $fontSize){
												
												$sel = "selected";
											} 
										}		
								?>
								<option value="<?php echo $fontSize?>px" <?php echo $sel ?> ><?php echo $fontSize?>px</option>
								<?php 									
									} ?>
							</select>
					
						
						</span>
						<br>
						</td>
						</tr>
						
						<!-- color label  -->
						<tr valign="top">
						
							<td align="right" class="Title">Label Color <a title="default color #F7F7F7" class="default_values">(#F7F7F7)</a></td>
							
							<td align="left" style="width:5px;">
														
							</td>
							<td align="left" class="Content">							
							
							<span class="TXT">
							
							<?php 
								$sel = '#F7F7F7';
								
								if (isset($skyscraper_options["sociable_banner_colorLabel"])){
									
									if (!empty($skyscraper_options["sociable_banner_colorLabel"])){
										
										$sel = $skyscraper_options["sociable_banner_colorLabel"];
									}
								}
							?>
							
							<input type="text" value="<?php echo $sel?>" name="skyscraper_options[sociable_banner_colorLabel]" style="width:81px !important">						
							</span>
							<br>
							</td>
						</tr>
						<!-- color font  -->
							<tr valign="top">
						 
							<td align="right" class="Title">Background Color <a title="default color #F7F7F7" class="default_values">(#F7F7F7)</a></td>
							
							<td align="left" style="width:5px;">
														
							</td>
							<td align="left" class="Content">							
							<span class="TXT">
							<?php 
								$sel = '#F7F7F7';
								
								if (isset($skyscraper_options["sociable_banner_colorBack"])){
									
									if (!empty($skyscraper_options["sociable_banner_colorBack"])){
										
										$sel = $skyscraper_options["sociable_banner_colorBack"];
									}
								}
							?>
							<input type="text" value="<?php echo $sel?>" name="skyscraper_options[sociable_banner_colorBack]" style="width:81px !important">	
							
							</span>
							<br>
							</td>
						</tr>
						
					</tbody></table>						
					<br><br>
				</div>
			</div>
			<!-- general options -->	
			<TABLE class="Title-Box" style="cursor:pointer;" cellspacing="0" cellpadding="0" onclick="hideOrShow('GeneralOptions');">
				<TR>
					<TD class="Border-Left" ></TD><TD  class="BG-Middle" id="GeneralOptions-Title" ><span id="GeneralOptions-Tab"> + </span> <?php  _e("General Options","sociable");?></TD><TD class="Border-Right"></TD>
				</TR>
			</TABLE>
			<BR/>
			
			<DIV class="Content-Box" id="GeneralOptions-Content" style="display:none;" >
				
				<BR/>
				<DIV align="center" style="width:100%;">
					<TABLE  align="center" class="GeneralOptions-List" cellspacing="0" border=0 cellpadding	="10" >
						
						<TR valign="top" >
							<TD align="right" class="Title" ><?php  _e("Widget Position","sociable")?></TD>
							<TD align="left" style="width:5px;" ><INPUT <?php if(!empty($skyscraper_options["widget_position"])) echo "checked='checked'"?> type="checkbox" name="skyscraper_options[widget_position]" id="widget_position" /></TD>
							<TD align="left" class="Content">
															<SPAN class="TXT"><?php  _e("Check if you want Sociable Fixed on the screen","sociable");?> </SPAN>
							<BR/>
														
							</TD>
						</TR>
					</TABLE>						
					<BR/><BR/>
				</DIV>
			</DIV>
						<TABLE class="Title-Box" style="cursor:pointer;"  cellspacing="0" cellpadding="0" onclick="hideOrShow('Locations');" >
				<TR>
					<TD class="Border-Left" ></TD><TD  class="BG-Middle" id="Locations-Title" ><span id="Locations-Tab">+ </span><?php  _e("Locations","sociable");?></TD><TD class="Border-Right"></TD>
				</TR>
			</TABLE>
			<BR/>
			
			<DIV class="Content-Box" id="Locations-Content" style="display:none;" >
				<DIV  class="Locations-TXT" id="Locations-TXT" ><?php  _e("Please select the locations that you wish to allow the Sociable plugin to  insert itself.","sociable");?></DIV>
					
				<BR/>
				<DIV align="center" style="width:100%;">
					<TABLE  align="center" class="Locations-List" cellspacing="0" border=0 cellpadding="10">
						<TR valign="top" >
							<TD align="right" class="Title" ><?php  _e("Home page","sociable");?></TD>
							<TD align="left" style="width:5px;" ><INPUT <?php if(!empty($skyscraper_options["locations"]["is_front_page"])) echo "checked='checked'"?> type="checkbox" name="skyscraper_options[locations][is_front_page]" id="HomePage" /></TD>
							<TD align="left" class="Content">
															<SPAN class="TXT"><?php  _e("The front page of the blog (if set to a static page), or the main blog page (if set to your latest posts).","sociable");?></SPAN>
															
							</TD>
						</TR>
						
						<TR valign="top" >
							<TD align="right" class="Title" ><?php  _e("Blog page","sociable");?></TD>
							<TD align="left" style="width:5px;" ><INPUT <?php if(!empty($skyscraper_options["locations"]["is_home"])) echo "checked='checked'"?> type="checkbox" name="skyscraper_options[locations][is_home]" id="BlogPage" /></TD>
							<TD align="left" class="Content">
															<SPAN class="TXT"><?php  _e("The home page of the blog if is set to your latest posts, or the posts page if the home page is set to a static page","sociable");?></SPAN>
															
							</TD>
						</TR>
						
						<TR valign="top" >
							<TD align="right" class="Title" ><?php  _e("Posts","sociable");?></TD>
							<TD align="left" style="width:5px;" ><INPUT <?php if(!empty($skyscraper_options["locations"]["is_single"])) echo "checked='checked'"?> type="checkbox" name="skyscraper_options[locations][is_single]" id="Posts" /></TD>
							<TD align="left" class="Content">
															<SPAN class="TXT"><?php  _e("Single post pages","sociable");?></SPAN>
															
							</TD>
						</TR>
						
						<TR valign="top" >
							<TD align="right" class="Title" ><?php  _e("Pages","sociable");?></TD>
							<TD align="left" style="width:5px;" ><INPUT <?php if(!empty($skyscraper_options["locations"]["is_page"])) echo "checked='checked'"?> type="checkbox" name="skyscraper_options[locations][is_page]" id="Pages" /></TD>
							<TD align="left" class="Content">
															<SPAN class="TXT"><?php  _e("Individual Wordpress pages","sociable");?></SPAN>
															
							</TD>
						</TR>
						
						<TR valign="top" >
							<TD align="right" class="Title" ><?php  _e("Category archives","sociable");?></TD>
							<TD align="left" style="width:5px;" ><INPUT <?php if(!empty($skyscraper_options["locations"]["is_category"])) echo "checked='checked'"?> type="checkbox" name="skyscraper_options[locations][is_category]" id="CategoryArchives" /></TD>
							<TD align="left" class="Content">
						<SPAN class="TXT"><?php  _e("Category archive pages","sociable");?></SPAN>
															
							</TD>
						</TR>
						
						<TR valign="top" >
							<TD align="right" class="Title" ><?php  _e("Date archives","sociable");?></TD>
							<TD align="left" style="width:5px;" ><INPUT  <?php if(!empty($skyscraper_options["locations"]["is_date"])) echo "checked='checked'"?> type="checkbox" name="skyscraper_options[locations][is_date]" id="DateArchives" /></TD>
							<TD align="left" class="Content">
						<SPAN class="TXT"><?php  _e("Date archive pages","sociable");?> </SPAN>
															
							</TD>
						</TR>
						
						<TR valign="top" >
							<TD align="right" class="Title" ><?php  _e("Tag archives","sociable");?></TD>
							<TD align="left" style="width:5px;" ><INPUT <?php if(!empty($skyscraper_options["locations"]["is_tag"])) echo "checked='checked'"?> type="checkbox" name="skyscraper_options[locations][is_tag]" id="TagArchives" /></TD>
							<TD align="left" class="Content">
															<SPAN class="TXT"><?php  _e("Tag archive pages","sociable");?> </SPAN>
															
							</TD>
						</TR>
						
						<TR valign="top" >
							<TD align="right" class="Title" ><?php  _e("Author archives","sociable");?></TD>
							<TD align="left" style="width:5px;" ><INPUT <?php if(!empty($skyscraper_options["locations"]["is_author"])) echo "checked='checked'"?> type="checkbox" name="skyscraper_options[locations][is_author]" id="AuthorArchives" /></TD>
							<TD align="left" class="Content">
															<SPAN class="TXT"><?php  _e("Author archive pages","sociable");?></SPAN>
															
							</TD>
						</TR>
						
						<TR valign="top" >
							<TD align="right" class="Title" ><?php  _e("Search results","sociable");?></TD>
							<TD align="left" style="width:5px;" ><INPUT <?php if(!empty($skyscraper_options["locations"]["is_search"])) echo "checked='checked'"?> type="checkbox" name="skyscraper_options[locations][is_search]" id="SearchResults" /></TD>
							<TD align="left" class="Content">
															<SPAN class="TXT"><?php  _e("Search results pages","sociable");?></SPAN>
															
							</TD>
						</TR>
						
						<TR valign="top" >
							<TD align="right" class="Title" ><?php  _e("RSS feeds","sociable");?></TD>
							<TD align="left" style="width:5px;" ><INPUT <?php if(!empty($skyscraper_options["locations"]["is_rss"])) echo "checked='checked'"?> type="checkbox" name="skyscraper_options[locations][is_rss]" id="RssFeeds" /></TD>
							<TD align="left" class="Content">
															<SPAN class="TXT"><?php  _e("RSS feeds","sociable");?></SPAN>
															
							</TD>
						</TR>
					
					</TABLE>	
					<BR/><BR/>
				</DIV>
			</DIV>
			
			<TABLE class="Title-Box" style="cursor:pointer;"  cellspacing="0" cellpadding="0" onclick="hideOrShow('Active');" >

				<TR>

					<TD class="Border-Left" ></TD><TD  class="BG-Middle" id="Active-Title" ><span id="Active-Tab">+ </span><?php  _e("Active Skyscraper","sociable");?></TD><TD class="Border-Right"></TD>

				</TR>

			</TABLE>
			<div style="display: block;" id="Active-Content" class="Content-Box">

				<br />

				<div align="center" style="width:100%;">

                <table align="center" cellspacing="0" cellpadding="10" border="0" class="GeneralOptions-List">

						<tbody><tr valign="top">

							<td align="right" class="Title">Active Skyscraper</td>

							<td align="left" style="width:5px;">

							<input <?php if(isset($skyscraper_options["active"])) echo "checked='checked'"?> type="checkbox" id="active" name="skyscraper_options[active]" ></td>

							<td align="left" class="Content">

							<span class="TXT">Check if you want Sociable Skyscraper enable </span>

							<br />

							</td>

						</tr>

					</tbody></table>						

					<br/><br/>
				</div>

			</div>
			<br/><br/>

<TABLE class="Title-Box" style="cursor:pointer;"  cellspacing="0" cellpadding="0">

				<TR>

					<TD class="Border-Left" ></TD><TD  class="BG-Middle" id="Active-Title" ><span id="Active-Tab">+ </span><?php  _e("Send my config","sociable");?></TD><TD class="Border-Right"></TD>

				</TR>

			</TABLE>
			<div style="display: block;" id="Active-Content" class="Content-Box">

				<br />

				<div align="center" style="width:100%;">

                <table align="center" cellspacing="0" cellpadding="10" border="0" class="GeneralOptions-List">

						<tbody><tr valign="top">

							<td align="right" class="Title">Send my config</td>

							<td align="left" style="width:5px;">
                             <?php
                                
                                $checked = "";
                                if (!empty($skyscraper_options["pixel"])){
                                    
                                    $checked = "checked='checked'";
                                }
                             
                             ?>                               
							 <input <?php echo $checked ?> type="checkbox" id="active" name="skyscraper_options[pixel]" />
                            
                            </td>

							<td align="left" class="Content">

							<span class="TXT">Check if you want help Sociable grow. <br /><br /> This information be used only to improve Sociable. <br /><br />You accept send us your blog configuration and blog name  </span>

							<br />

							</td>

						</tr>

					</tbody></table>						

					<br/><br/>
				</div>

			</div>
			<br/><br/>
			<?php settings_fields( 'skyscraper_options_group' ); ?>
			</form>
			<div class="Content-Box">
					
			 <form id="sociable_reset_form" action="" method="POST">
                <?php wp_nonce_field('sociable-reset'); ?>
				<input type="hidden" id="skyscraper_reset" name="skyscraper_reset" value="1">
                <?php //submit_button( __( 'Reset Sociable' ) , 'primary', 'sociable_reset', false ); ?>
            </form>		
					
			<div id="ActionsBar">
				<div style="cursor:pointer;line-height:15px;" onclick="document.getElementById('form1').submit();" class="SaveChanges"><br>
					<span style="margin:30px;">Save Changes</span>
				</div>
				<div style="cursor:pointer;line-height:15px;font-size:12px;" onclick="document.getElementById('sociable_reset_form').submit();" name="sociable_reset" id="sociable_reset" class="ResetSociable"><br>
					<span style="margin:40px;margin-left:35px;">Reset Skyscraper</span>
				</div>
			</div>
		</div>		
		
			</div>
			<div style="float:left;width:49%;margin-left:15px" name="skyscraper" id="skyscraper">
			<script type="text/javascript">
			var base_url_sociable = "<?php echo SOCIABLE_HTTP_PATH?>";
			</script>
			</div>
    <?php 
	}
    
    /**
     * Add The Menu Pages To The Administration Options
     */
     
    function add_menu_pages(){
    
        global $sociable_post_types;
        
        $url = $_SERVER["QUERY_STRING"];
       //	$page[] = add_options_page( __( 'Sociable Options' ), __( 'Sociable Plugin' ), 'manage_options', 'sociable_select' , array( 'sociable_Admin_Options' , 'Select_Sociable_Page' ) );
		//$page[]= add_plugins_page( __( 'Sociable Options' ), __( 'Sociable Plugin' ), 'manage_options', 'Create_Options_Page_Skycraper'  );
		//$page[]= add_plugins_page( 'sociable_options', 'sociable_options', 'read', 'Create_Options_Page'  );
	$page[] =	add_options_page( "","", 'manage_options', 'sociable_select' , array( 'sociable_Admin_Options' , 'Select_Sociable_Page' ) );
	$page[] =	add_options_page( "","", 'manage_options', 'sociable_options' , array( 'sociable_Admin_Options' , 'Create_Options_Page' ) );
	$page[] =	add_options_page( "","", 'manage_options', 'skyscraper_options' , array( 'sociable_Admin_Options' , 'Create_Options_Page_Skycraper' ) );
		// Add a new submenu under Settings:
         //	$page[] =  add_options_page(__( 'Sociable Options' ),__( 'Sociable Plugin' ), 'manage_options', 'sociable_select', 'Select_Sociable_Page');
	    // Add a new top-level menu (ill-advised):
    add_menu_page(__( 'Sociable Options' ), __( 'Select Sociable Plugin' ), 'manage_options', '/options-general.php?page=sociable_select' );
    // Add a submenu to the custom top-level menu:
    add_submenu_page('options-general.php?page=sociable_select',  __( 'Sociable Options' ), __( 'Sociable Options' ), 'manage_options', 'sociable_options' , array( 'sociable_Admin_Options' , 'Create_Options_Page' ) );
    // Add a second submenu to the custom top-level menu:
    add_submenu_page('options-general.php?page=sociable_select',  __( 'Skyscraper Options' ), __( 'Skyscraper Options' ), 'manage_options', 'skyscraper_options' , array( 'sociable_Admin_Options' , 'Create_Options_Page_Skycraper' ) );
		
        //Add CSS And Javascript Specific To This Options Pages 
        add_action( 'admin_print_styles-' . $page[0] , array( 'sociable_Admin_Options' , 'enqueue_styles' ) );
        add_action( 'admin_print_scripts-' . $page[0] , array( 'sociable_Admin_Options' , 'enqueue_scripts' ) ); 
        
        add_action( 'admin_print_styles-' . $page[1] , array( 'sociable_Admin_Options' , 'enqueue_styles' ) );
        add_action( 'admin_print_scripts-' . $page[1] , array( 'sociable_Admin_Options' , 'enqueue_scripts' ) ); 
        add_action( 'admin_print_styles-' . $page[2] , array( 'sociable_Admin_Options' , 'enqueue_styles' ) );
        add_action( 'admin_print_scripts-' . $page[2] , array( 'sociable_Admin_Options' , 'enqueue_scripts' ) ); 
	
        if( isset( $_POST['sociable_reset'] ) ){
            check_admin_referer( 'sociable-reset' );
            
            sociable_reset();
            wp_redirect( $_SERVER['HTTP_REFERER' ] ); 
        }  
        
        if( isset( $_POST['skyscraper_reset'] ) ){
            check_admin_referer( 'sociable-reset' );
            
            skyscraper_reset();
            wp_redirect( $_SERVER['HTTP_REFERER' ] ); 
        }  
        
        
        /*
         * We can create The Meta Boxes Here
         */
        foreach( $sociable_post_types as $type => $data ){
            self::add_meta_box( $type );
        }
        //Also on posts and pages
        self::add_meta_box( 'post' );
        self::add_meta_box( 'page' );
        
    }
    
    /*
     * Function to Enqueue The Styles For The Options Page
     */
    function enqueue_styles(){
	 	wp_enqueue_style( 'style-admin-css', SOCIABLE_HTTP_PATH . 'css/style-admin.css' );
        wp_enqueue_style( 'sociable-admin-css', SOCIABLE_HTTP_PATH . 'css/sociable-admin.css' );
        wp_enqueue_style( 'sociablecss' , SOCIABLE_HTTP_PATH . 'css/sociable.css' );
    }
    
    /*
     * Function To Enqueue The Scripts For The Options Page
     */
    function enqueue_scripts(){
        wp_enqueue_script('jquery'); 
        wp_enqueue_script('jquery-ui-core',false,array('jquery')); 
        wp_enqueue_script('jquery-ui-sortable',false,array('jquery','jquery-ui-core'));
        wp_enqueue_script( 'sociable-admin-js', SOCIABLE_HTTP_PATH . 'js/sociable-admin.js' , array( 'jquery','jquery-ui-core' , 'jquery-ui-sortable' ) );
		wp_enqueue_script( 'admin-fn-js', SOCIABLE_HTTP_PATH . 'js/admin-fn.js' , array( 'jquery','jquery-ui-core' , 'jquery-ui-sortable' ) );
		
    }
    
    
    
    /*
     * Function To Add The Settings Fields.
     */
    function do_site_selection_list($plugin = 'sociable'){
		        
		if ($plugin == 'sociable'){
			
			global $sociable_options;
			$option_plugin = $sociable_options;
			$name_plugin = "sociable_options";
		}
		else{
			
			global $skyscraper_options;
			$option_plugin = $skyscraper_options;
			$name_plugin = "skyscraper_options";
		}
         
        
        $sociable_known_sites = get_option( 'sociable_known_sites' );
        /*
         * Sort The List Based On The Active Sites So That They Display Correctly.
         */
        $active_sites = isset( $option_plugin['active_sites'] ) && is_array( $option_plugin['active_sites'] )  ? $option_plugin['active_sites'] : array() ;
        
        //Start Blank
        $active = Array(); 
        
        //Disabled Untill Proven Active
	$disabled = $sociable_known_sites;
        
        //Loop Through The Active Sites, sorting into 2 arrays
	foreach( $active_sites as $sitename => $value ) {
		$active[$sitename] = $disabled[$sitename];
		unset( $disabled[$sitename] );
	}
        
	uksort($disabled, "strnatcasecmp");
        
        $sites = array_merge( $active, $disabled );
        
        $imagepath = isset( $option_plugin['sociable_imagedir'] ) ? $option_plugin['sociable_imagedir'] : '' ;
        
        if ($imagepath == "") {
                $imagepath = trailingslashit( SOCIABLE_HTTP_PATH ) . 'images/';
        } else {		
                $imagepath .= trailingslashit( $imagepath );
        }
        
        $out ='<ul id="sociable_site_list" >' ;
        $io = 0;
        foreach( $sites as $sitename => $site ){
				
			
            //Set Checked And Active If Relevant
            if( array_key_exists( $sitename, $active_sites ) ){
                $checked = 'checked="checked"';
                $active = 'active';
            } else {
                $checked = '';
                $active = 'inactive';
            }
            if ( $sitename != "More"){
				if (isset($site["counter"])){
					//$image = "<img src='".SOCIABLE_HTTP_PATH."images/".$site["favicon"]."'>";
					$image = $site["url"];
				}else{
					$image = _get_sociable_image( $site, '' );
				}
			}else{
			$image = "<img src='".SOCIABLE_HTTP_PATH."images/more.png'>";
			}
            
//            if ( ! isset( $site['spriteCoordinates']) || isset( $sociable_options['sociable_disablesprite'] ) ) {
//                    if (strpos($site['favicon'], 'http') === 0) {
//                            $imgsrc = $site['favicon'];
//                    } else {
//                            $imgsrc = $imagepath.$site['favicon'];
//                    }
//                    $img = '<img src="' . $imgsrc . '" width="16" height="16" />';
//            } else {
//                    $imgsrc = $imagepath."services-sprite.gif";
//                    $services_sprite_url = $imagepath . "services-sprite.png";
//                    $spriteCoords = $site['spriteCoordinates'];
//                    $img =  '<img src="' . $imgsrc . '" width="16" height="16" style="background: transparent url(' . $services_sprite_url . ') no-repeat; background-position:-' . $spriteCoords[0] . 'px -' . $spriteCoords[1] . 'px" />';
//            }
            
            $out .= '<li id="' . $sitename . '" class="' . $active . '">';
            
            $out .= '<input type="checkbox" id="cb_' . $sitename . '" name="'.$name_plugin.'[active_sites][' . $sitename . ']" ' . $checked . ' />';
            
            $out .= $image;
            if (!isset($site["counter"])){
            $out .= $sitename;
			}
                
            $out .= '</li>';
            
        }
		
        
        echo $out."</ul>";
        
    }
  
    /*
     * Create The HTML For The Options Page
     */
    function Create_Options_Page(){ 
        global $sociable_options;
        global $skyscraper_options;
		?>

			<style>

			.Title-Box .BG-Middle {

					vertical-align: middle;

			}

			</style>

			<div class="wrap">
        <DIV style="margin:0 0 0 25px" class="Post-subTXT" id="Post-subTXT" >			
 
			<iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Fblogplay.com%2F&amp;send=false&amp;layout=standard&amp;width=450&amp;show_faces=true&amp;action=recommend&amp;colorscheme=light&amp;font&amp;height=80&amp;appId=133479460071366" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:450px; height:40px;" allowTransparency="true"></iframe><br />
	<a href="https://twitter.com/share" class="twitter-share-button" data-url="http://blogplay.com" data-text="Check the sociable plugin" >Tweet</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>	
			<br />	
			</div>
            <h2 style="clear:both;"><?php _e( 'Sociable Options' ); ?></h2>
            <form method="post" action="options.php" id="form1" autocomplete="off">
                
                <?php wp_nonce_field('sociable-config'); ?>
                <INPUT type="hidden" class="version-INPUT" id="version" name="sociable_options[version]" value="<?php echo$sociable_options["version"];?>" /> 

                <INPUT type="hidden" id="blogplay_tags" name="sociable_options[blogplay_tags]" value="1" /> 

                <TABLE class="Title-Box" cellspacing="0" cellpadding="0" id="Preview-Title" style="margin:0 0 0 25px">
				<TR>
					<TD class="Border-Left" ></TD><TD  class="BG-Middle" >Preview</TD><TD class="Border-Right"></TD>
				</TR>
				</TABLE>
                <BR/>
			
			<DIV    class="Content-Box" id="Preview-Content">
				<DIV style="margin:0 0 0 25px" align="left" class="Live-Preview" id="Live-Preview" ><?php _e("Live preview of how Sociable will appear on your blog.","sociable")?></DIV>
					
				<BR/>
				
				<DIV style="margin:0 0 0 25px" class="Post-TXT" id="Post-TXT" ><?php  _e("This is your post here...","sociable")?></DIV>
		
				<DIV style="margin:0 0 0 25px" class="Post-subTXT" id="Post-subTXT" >Lorem ipsum dolor sit amet, consectetur adipiscing elit.</DIV>
				
				<BR/>
				<DIV style="margin:0 0 0 25px" id="ShareAndEnjoy"  > <?php do_sociable(); ?></DIV>	
			</DIV>
			<TABLE class="Title-Box" cellspacing="0" cellpadding="0" id="Tagline-Title">
				<TR>
					<TD class="Border-Left" ></TD><TD  class="BG-Middle" ><?php  _e("Tagline","sociable");?></TD><TD class="Border-Right"></TD>
				</TR>
			</TABLE>
			<BR/>
			
			<DIV class="Content-Box" id="Tagline-Content">
				<DIV  class="Tagline-TXT" id="Tagline-TXT" ><?php  _e('Previously we used "Share and Enjoy", remember the good old days?',"sociable");?></DIV>
					
				<BR/>
				<DIV style="width:100%;height:60px;">
					<INPUT type="text" class="Tagline-INPUT" id="tagline" name="sociable_options[tagline]" value="<?php echo$sociable_options["tagline"];?>" /> 
					
					<DIV class="ToSociable" >
							<INPUT type="checkbox" <?php if (!empty($sociable_options["help_grow"])) echo "checked = 'checked'";?> name="sociable_options[help_grow]" id="LinkToSociable" />
							<?php  _e("Link to Sociable","sociable");?><BR/>
							<SPAN style="font-size:14px;"><?php  _e("(Help us grow, please leave the link so others discover Sociable from your blog)","sociable");?></SPAN>
					</DIV>
				</DIV>
			</DIV>
               
                             
			<TABLE class="Title-Box" cellspacing="0" cellpadding="0" id="Tagline-Title">
				<TR>
					<TD class="Border-Left" ></TD><TD  class="BG-Middle" ><?php  _e("Icons to Include","sociable");?></TD><TD class="Border-Right"></TD>
				</TR>
			</TABLE>
			<BR/>
			
			<DIV class="Content-Box" id="IconsToInclude-Box" style="">
				<DIV  class="IconsToInclude-TXT" id="IconsToInclude-TXT" >
					<?php  _e("Check the sites you want to appear on your blog.","sociable");?>
				</DIV>
					<BR/>

				<DIV  style="font-size:13px">

				

					<?php

						$check_tags = "";

						if (isset($sociable_options["linksoptions"])){

					

							if (!empty($sociable_options["linksoptions"])){

								$check_tags = "checked";		

							}

						}

					

					?>

					

					<input type="checkbox" <?php echo $check_tags;?> id="" name="sociable_options[linksoptions]"  /> Accept include the blogplay.com tag into my share icons

				</DIV>
					
				<BR/>
                
                <?php self::do_site_selection_list(); ?>
            </DIV>
		
			<div class="soc_clear"></div>
			
			<TABLE class="Title-Box" cellspacing="0" cellpadding="0" id="IconSize-Title" style="margin-top:20px;">
				<TR>
					<TD class="Border-Left" ></TD><TD  class="BG-Middle" ><?php  _e("Icons Size","sociable");?></TD><TD class="Border-Right"></TD>
				</TR>
			</TABLE>
			<BR/>
			
			<DIV class="Content-Box" style="margin-left:-3px" id="IconSize-Content">
				<?php
					$checked16 = "";
					$checked32 = "";
					$checked48 = "";
					$checked64 = "";
					if ($sociable_options["icon_size"] == 16) $checked16 = "checked='checked'";
					if ($sociable_options["icon_size"] == 32) $checked32 = "checked='checked'";
					if ($sociable_options["icon_size"] == 48) $checked48 = "checked='checked'";
					if ($sociable_options["icon_size"] == 64) $checked64 = "checked='checked'";
					//echo $checked16;
				?>
				<SPAN class="IconSize-Item">	<INPUT  value="16" type="radio" name="sociable_options[icon_size]" <?php echo $checked16;?> />16x16 Pixels </SPAN>
					
				<SPAN class="IconSize-Item">	<INPUT <?php echo$checked32;?> value="32" type="radio" name="sociable_options[icon_size]" />32x32 Pixels </SPAN>
					
				<SPAN class="IconSize-Item">	<INPUT <?php echo$checked48;?> value="48" type="radio"  name="sociable_options[icon_size]"/>48x48 Pixels </SPAN>
					
				<SPAN class="IconSize-Item">	<INPUT <?php echo$checked64;?> value="64" type="radio" name="sociable_options[icon_size]" />64x64 Pixels </SPAN>
				
				
			</DIV>
			
			<TABLE class="Title-Box" cellspacing="0" cellpadding="0" id="IconSize-Title" style="margin-top:20px;">
				<TR>
					<TD class="Border-Left" ></TD><TD  class="BG-Middle" ><?php  _e("Icons Style","sociable");?></TD><TD class="Border-Right"></TD>
				</TR>
			</TABLE>
			<BR/>
			
			<DIV class="Content-Box" id="IconSize-Content" style="padding:20px;">
				
				<?php
					$checked1 = "";
					$checked2 = "";
					$checked3 = "";
					$checked4 = "";
					$checked5 = "";
					$checked6 = "";
					if ($sociable_options["icon_option"] == "option1") $checked1 = "checked='checked'";
					if ($sociable_options["icon_option"] == "option2") $checked2 = "checked='checked'";
					if ($sociable_options["icon_option"] == "option3") $checked3 = "checked='checked'";
					if ($sociable_options["icon_option"] == "option4") $checked4 = "checked='checked'";
					if ($sociable_options["icon_option"] == "option5") $checked5 = "checked='checked'";
					if ($sociable_options["icon_option"] == "option6") $checked6 = "checked='checked'";
					
					 $imagepath = isset( $sociable_options['sociable_imagedir'] ) ? $sociable_options['sociable_imagedir'] : '' ;
        
						if ($imagepath == "") {
								$imagepath = trailingslashit( SOCIABLE_HTTP_PATH ) . 'images/';
						} else {		
								$imagepath .= trailingslashit( $imagepath );
						}
							//echo $imagepath;
				?>
				
				<SPAN class="IconStyle-Item">	<INPUT name="sociable_options[icon_option]" <?php echo$checked1?> value="option1" type="radio" /> <IMG  src="<?php echo$imagepath?>icon_styles/<?php echo$sociable_options["icon_size"]?>/option1_<?php echo$sociable_options["icon_size"]?>.jpg"  /> </SPAN>
				<BR/><BR/>
				<SPAN class="IconStyle-Item">	<INPUT name="sociable_options[icon_option]" <?php echo$checked2?> value="option2" type="radio" /> <IMG  src="<?php echo$imagepath?>icon_styles/<?php echo$sociable_options["icon_size"]?>/option2_<?php echo$sociable_options["icon_size"]?>.jpg"  /> </SPAN>
				<BR/><BR/>
				<SPAN class="IconStyle-Item">	<INPUT name="sociable_options[icon_option]" <?php echo$checked3?> value="option3" type="radio" /> <IMG  src="<?php echo$imagepath?>icon_styles/<?php echo$sociable_options["icon_size"]?>/option3_<?php echo$sociable_options["icon_size"]?>.jpg"  />  </SPAN>
				<BR/><BR/>
				<SPAN class="IconStyle-Item">	<INPUT name="sociable_options[icon_option]" <?php echo$checked4?> value="option4" type="radio" /> <IMG  src="<?php echo$imagepath?>icon_styles/<?php echo$sociable_options["icon_size"]?>/option4_<?php echo$sociable_options["icon_size"]?>.jpg"  /> </SPAN>
				<BR/><BR/>
				<SPAN class="IconStyle-Item">	<INPUT name="sociable_options[icon_option]" <?php echo$checked5?> value="option5" type="radio" /> <IMG  src="<?php echo$imagepath?>icon_styles/<?php echo$sociable_options["icon_size"]?>/option5_<?php echo$sociable_options["icon_size"]?>.jpg"  />  </SPAN>
				<BR/><BR/>				
				<SPAN class="IconStyle-Item">	<INPUT name="sociable_options[icon_option]" <?php echo$checked6?> value="option6" type="radio" /> <IMG  src="<?php echo$imagepath?>icon_styles/16/option_6_16.png"  />  </SPAN>
				<BR/><BR/>				
			</DIV>	
				
			<TABLE class="Title-Box" style="cursor:pointer;"  cellspacing="0" cellpadding="0" onclick="hideOrShow('Locations');" >
				<TR>
					<TD class="Border-Left" ></TD><TD  class="BG-Middle" id="Locations-Title" ><span id="Locations-Tab">+ </span><?php  _e("Locations","sociable");?></TD><TD class="Border-Right"></TD>
				</TR>
			</TABLE>
			<BR/>
			
			<DIV class="Content-Box" id="Locations-Content" style="display:none;" >
				<DIV  class="Locations-TXT" id="Locations-TXT" ><?php  _e("Please select the locations that you wish to allow the Sociable plugin to  insert itself.","sociable");?></DIV>
					
				<BR/>
				<DIV align="center" style="width:100%;">
					<TABLE  align="center" class="Locations-List" cellspacing="0" border=0 cellpadding="10">
						<TR valign="top" >
							<TD align="right" class="Title" ><?php  _e("Home page","sociable");?></TD>
							<TD align="left" style="width:5px;" ><INPUT <?php if(!empty($sociable_options["locations"]["is_front_page"])) echo "checked='checked'"?> type="checkbox" name="sociable_options[locations][is_front_page]" id="HomePage" /></TD>
							<TD align="left" class="Content">
															<SPAN class="TXT"><?php  _e("The front page of the blog (if set to a static page), or the main blog page (if set to your latest posts).","sociable");?></SPAN>
															
							</TD>
						</TR>
						
						<TR valign="top" >
							<TD align="right" class="Title" ><?php  _e("Blog page","sociable");?></TD>
							<TD align="left" style="width:5px;" ><INPUT <?php if(!empty($sociable_options["locations"]["is_home"])) echo "checked='checked'"?> type="checkbox" name="sociable_options[locations][is_home]" id="BlogPage" /></TD>
							<TD align="left" class="Content">
															<SPAN class="TXT"><?php  _e("The home page of the blog if is set to your latest posts, or the posts page if the home page is set to a static page","sociable");?></SPAN>
															
							</TD>
						</TR>
						
						<TR valign="top" >
							<TD align="right" class="Title" ><?php  _e("Posts","sociable");?></TD>
							<TD align="left" style="width:5px;" ><INPUT <?php if(!empty($sociable_options["locations"]["is_single"])) echo "checked='checked'"?> type="checkbox" name="sociable_options[locations][is_single]" id="Posts" /></TD>
							<TD align="left" class="Content">
															<SPAN class="TXT"><?php  _e("Single post pages","sociable");?></SPAN>
															
							</TD>
						</TR>
						
						<TR valign="top" >
							<TD align="right" class="Title" ><?php  _e("Pages","sociable");?></TD>
							<TD align="left" style="width:5px;" ><INPUT <?php if(!empty($sociable_options["locations"]["is_page"])) echo "checked='checked'"?> type="checkbox" name="sociable_options[locations][is_page]" id="Pages" /></TD>
							<TD align="left" class="Content">
															<SPAN class="TXT"><?php  _e("Individual Wordpress pages","sociable");?></SPAN>
															
							</TD>
						</TR>
						
						<TR valign="top" >
							<TD align="right" class="Title" ><?php  _e("Category archives","sociable");?></TD>
							<TD align="left" style="width:5px;" ><INPUT <?php if(!empty($sociable_options["locations"]["is_category"])) echo "checked='checked'"?> type="checkbox" name="sociable_options[locations][is_category]" id="CategoryArchives" /></TD>
							<TD align="left" class="Content">
															<SPAN class="TXT"><?php  _e("Category archive pages","sociable");?></SPAN>
															
							</TD>
						</TR>
						
						<TR valign="top" >
							<TD align="right" class="Title" ><?php  _e("Date archives","sociable");?></TD>
							<TD align="left" style="width:5px;" ><INPUT  <?php if(!empty($sociable_options["locations"]["is_date"])) echo "checked='checked'"?> type="checkbox" name="sociable_options[locations][is_date]" id="DateArchives" /></TD>
							<TD align="left" class="Content">
															<SPAN class="TXT"><?php  _e("Date archive pages","sociable");?> </SPAN>
															
							</TD>
						</TR>
						
						<TR valign="top" >
							<TD align="right" class="Title" ><?php  _e("Tag archives","sociable");?></TD>
							<TD align="left" style="width:5px;" ><INPUT <?php if(!empty($sociable_options["locations"]["is_tag"])) echo "checked='checked'"?> type="checkbox" name="sociable_options[locations][is_tag]" id="TagArchives" /></TD>
							<TD align="left" class="Content">
															<SPAN class="TXT"><?php  _e("Tag archive pages","sociable");?> </SPAN>
															
							</TD>
						</TR>
						
						<TR valign="top" >
							<TD align="right" class="Title" ><?php  _e("Author archives","sociable");?></TD>
							<TD align="left" style="width:5px;" ><INPUT <?php if(!empty($sociable_options["locations"]["is_author"])) echo "checked='checked'"?> type="checkbox" name="sociable_options[locations][is_author]" id="AuthorArchives" /></TD>
							<TD align="left" class="Content">
															<SPAN class="TXT"><?php  _e("Author archive pages","sociable");?></SPAN>
															
							</TD>
						</TR>
						
						<TR valign="top" >
							<TD align="right" class="Title" ><?php  _e("Search results","sociable");?></TD>
							<TD align="left" style="width:5px;" ><INPUT <?php if(!empty($sociable_options["locations"]["is_search"])) echo "checked='checked'"?> type="checkbox" name="sociable_options[locations][is_search]" id="SearchResults" /></TD>
							<TD align="left" class="Content">
															<SPAN class="TXT"><?php  _e("Search results pages","sociable");?></SPAN>
															
							</TD>
						</TR>
						
						<TR valign="top" >
							<TD align="right" class="Title" ><?php  _e("RSS feeds","sociable");?></TD>
							<TD align="left" style="width:5px;" ><INPUT <?php if(!empty($sociable_options["locations"]["is_rss"])) echo "checked='checked'"?> type="checkbox" name="sociable_options[locations][is_rss]" id="RssFeeds" /></TD>
							<TD align="left" class="Content">
															<SPAN class="TXT"><?php  _e("RSS feeds","sociable");?></SPAN>
															
							</TD>
						</TR>
						
						
					</TABLE>	
					<BR/><BR/>
				</DIV>
			</DIV>	
			
			<TABLE class="Title-Box" style="cursor:pointer;" cellspacing="0" cellpadding="0" onclick="hideOrShow('GeneralOptions');">
				<TR>
					<TD class="Border-Left" ></TD><TD  class="BG-Middle" id="GeneralOptions-Title" ><span id="GeneralOptions-Tab"> + </span> <?php  _e("General Options","sociable");?></TD><TD class="Border-Right"></TD>
				</TR>
			</TABLE>
			<BR/>
			
			<DIV class="Content-Box" id="GeneralOptions-Content" style="display:none;" >
				
				<BR/>
				<DIV align="center" style="width:100%;">
					<TABLE  align="center" class="GeneralOptions-List" cellspacing="0" border=0 cellpadding	="10" >
						<TR valign="top" >
							<TD align="right" class="Title" ><?php  _e("Automatic mode","sociable")?></TD>
							<TD align="left" style="width:5px;" ><INPUT <?php if(!empty($sociable_options["automatic_mode"])) echo "checked='checked'"?> type="checkbox" name="sociable_options[automatic_mode]" id="AutoMode" /></TD>
							<TD align="left" class="Content">
															<SPAN class="TXT"><?php  _e("Do you want to automatically use Sociable on the locations specified?","sociable");?> </SPAN>
															<BR/>
															<SPAN class="sTXT">
																	<?php  _e("If this is unchecked, you will have to use the shortcode[sociable/] or template","sociable");?> tag  ?php if( function_exists( do_sociable() ) ){ do_sociable(); } 
															</SPAN>		
															
							</TD>
						</TR>
						
						
						</TR>
						<TR valign="top" >
							<TD align="right" class="Title" ><?php  _e("Use styleSheet","sociable");?></TD>
							<TD align="left" style="width:5px;" ><INPUT <?php if(!empty($sociable_options["use_stylesheet"])) echo "checked='checked'"?> type="checkbox" name="sociable_options[use_stylesheet]" id="UseStyleSheets" /></TD>
							<TD align="left" class="Content">
															<SPAN class="TXT"><?php  _e("Do you want to use the default stylesheet for sociable?","sociable");?></SPAN>
							</TD>
						</TR>
						<TR valign="top" >
							<TD align="right" class="Title" ><?php  _e("Use your own icons","sociable");?></TD>
							<TD align="left" style="width:5px;" ><INPUT <?php if(!empty($sociable_options["custom_icons"])) echo "checked='checked'"?> type="checkbox" name="sociable_options[custom_icons]" id="UseStyleSheets" /></TD>
							<TD align="left" class="Content">
															<SPAN class="TXT"><?php  _e("Do you want to use your own icons for sociable?","sociable");?></SPAN>
							</TD>
						</TR>
						<TR valign="top" >
							<TD align="right" class="Title" ><?php  _e("Use images","sociable");?></TD>
							<TD align="left" style="width:5px;" ><INPUT <?php if(!empty($sociable_options["use_images"])) echo "checked='checked'"?> type="checkbox" name="sociable_options[use_images]" id="UseImages" /></TD>
							<TD align="left" class="Content">
															<SPAN class="TXT"><?php  _e("Do you want to use the Sociable images? If not, the plugin will insert plain text links.","sociable");?></SPAN>
							</TD>
						</TR>
						
						
						<TR valign="top" >
							<TD align="right" class="Title" ><?php  _e("Use alpha mask","sociable");?></TD>
							<TD align="left" style="width:5px;" ><INPUT <?php if(!empty($sociable_options["use_alphamask"])) echo "checked='checked'"?> type="checkbox" name="sociable_options[use_alphamask]" id="AlphaMask" /></TD>
							<TD align="left" class="Content">
															<SPAN class="TXT"><?php  _e("Do you want to use alpha masks on the images (available only on the Original Sociable)?","sociable");?></SPAN>
							</TD>
						</TR>
						<TR valign="top" >
							<TD align="right" class="Title" ><?php  _e("Bottom and Top","sociable");?></TD>
							<TD align="left" style="width:5px;" ><INPUT <?php if(!empty($sociable_options["topandbottom"])) echo "checked='checked'"?> type="checkbox" name="sociable_options[topandbottom]" id="TopAndBottom" /></TD>
							<TD align="left" class="Content">
															<SPAN class="TXT"><?php  _e("Do you want to use Sociable plugin to show up at the top and bottom?","sociable");?></SPAN>
							</TD>
						</TR>
						<TR valign="top" >
							<TD align="right" class="Title" ><?php  _e("Open in new window","sociable");?></TD>
							<TD align="left" style="width:5px;" ><INPUT <?php if(!empty($sociable_options["new_window"])) echo "checked='checked'"?> type="checkbox" name="sociable_options[new_window]" id="OpenNewWindow" /></TD>
							<TD align="left" class="Content">
															<SPAN class="TXT"><?php  _e("do you want to open the links in a new window?","sociable");?></SPAN>
							</TD>
						</TR>
						
						
						
					</TABLE>	
					
					<BR/><BR/>
				</DIV>
			</DIV>
			<table class="Title-Box" style="cursor:pointer;"  cellspacing="0" cellpadding="0" onclick="hideOrShow('Active');" >
				<tr>
					<td class="Border-Left" ></td>
                    <td class="BG-Middle" id="Active-Title" ><span id="Active-Tab">+ </span><?php  _e("Active Sociable Classic","sociable");?></td><td class="Border-Right"></td>
				</tr>
			</table>
			
			<div style="display: block;" id="Active-Content" class="Content-Box">
				<br />
				<div align="center" style="width:100%;">
					<table align="center" cellspacing="0" cellpadding="10" border="0" class="GeneralOptions-List">
						
						<tbody><tr valign="top">
							<td align="right" class="Title">Active Sociable Classic</td>
							<td align="left" style="width:5px;">
							<input <?php if(isset($sociable_options["active"])) echo "checked='checked'"?> type="checkbox" id="active" name="sociable_options[active]" /></td>
							<td align="left" class="Content">
								<span class="TXT">Check if you want Sociable Classic enable </span>
							<br />
														
							</td>
						</tr>
					</tbody></table>						
					<br /><br />
				</div>
			</div>
            	<table class="Title-Box" style="cursor:pointer;"  cellspacing="0" cellpadding="0">
				<tr>
					<td class="Border-Left" ></td>
                    <td class="BG-Middle" id="Active-Title" ><?php  _e(" Send my config","sociable");?></td><td class="Border-Right"></td>
				</tr>
			</table>
			
			<div style="display: block;" id="Active-Content" class="Content-Box">
				<br />
				<div align="center" style="width:100%;">
					<table align="center" cellspacing="0" cellpadding="10" border="0" class="GeneralOptions-List">
						
						<tbody><tr valign="top">
							<td align="right" class="Title">Send my config</td>
							<td align="left" style="width:5px;">
                            
                             <?php
                                
                                $checked = "";
                                if (!empty($sociable_options["pixel"])){
                                    
                                    $checked = "checked='checked'";
                                }
                             
                             ?>      
							<input <?php echo $checked ?> type="checkbox" id="active" name="sociable_options[pixel]" /></td>
							<td align="left" class="Content">
								<span class="TXT">
                                Check if you want help Sociable grow.
                                 </br>           
                                This information be used only to improve Sociable.
                                </br>
                                You accept send us your blog configuration and blog name 
                                </span>
							<br />
														
							</td>
						</tr>
					</tbody></table>						
					<br /><br />
				</div>
			</div>
            
            
				<?php //<HR style="height:10px;background:#18305d;"/>?>
		<?php settings_fields( 'sociable_options_group' ); ?>
		</FORM>
		<DIV class="Content-Box" >
			<DIV id="ActionsBar">
				<DIV class="SaveChanges" onClick="document.getElementById('form1').submit();" style="cursor:pointer;line-height:15px;"><br/>
					<span style="margin:30px;"><?php  _e("Save Changes","sociable");?></span>
				</DIV>
				<DIV class="ResetSociable" id="sociable_reset" name="sociable_reset" onClick="document.getElementById('sociable_reset_form').submit();" style="cursor:pointer;line-height:15px;font-size:12px;"><br/>
					<span style="margin:40px;margin-left:35px;"><?php  _e("Reset Sociable","sociable");?></span>
				</DIV>
				<DIV class="UninstallSociable" onClick="document.getElementById('sociable_remove_form').submit();"  style="cursor:pointer;line-height:15px;font-size:12px;"><br/>
					<span style="margin:25px;margin-left:20px;"><?php  _e("Completly Uninstall Sociable","sociable");?></span>
				</DIV>
			</DIV>
		</DIV>
		<br>
		<br>
                <?php  //do_settings_sections( 'sociable_options' ); ?>
            <form id="sociable_reset_form" action="" method="POST">
                <?php wp_nonce_field('sociable-reset'); ?>
				<input type="hidden" id="sociable_reset" name="sociable_reset" value="1">
                <?php //submit_button( __( 'Reset Sociable' ) , 'primary', 'sociable_reset', false ); ?>
            </form>
            
          
		</div>
    <?php }
    
    function add_meta_box( $page ){
        add_meta_box( 'sociable_off' , __( 'Disable sociable' ), array( 'sociable_Admin_Options' , 'create_meta_box' ) , $page, 'side', 'default' );
    }
    
    function create_meta_box(){
	global $post;
	$sociableoff = false;
        $checked = '';
	if ( get_post_meta( $post->ID,'_sociableoff',true ) ) {
            $checked = 'checked="checked"';
	}
        wp_nonce_field( 'update_sociable_off' , 'sociable_nonce' );
        echo '<input type="checkbox" id="sociableoff" name="sociableoff" ' . $checked . ' /> <p class="description">' . __('Check This To Disable Sociable 2 On This Post Only.') . '</p>';
	
    }
    
    function save_post( $post_id ){
        
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
            return $post_id;
        // verify this came from the our screen and with proper authorization,
        // because save_post can be triggered at other times
        
        $nonce = ( isset( $_POST['sociable_nonce'] ) ) ? $_POST['sociable_nonce'] : false ;
        if ( ! $nonce ||  ! wp_verify_nonce( $nonce, 'update_sociable_off' ) )
          return $post_id;
        // Check permissions
        if ( 'page' == $_POST['post_type'] ){
        if ( !current_user_can( 'edit_page', $post_id ) )
            return;
        } else {
        if ( !current_user_can( 'edit_post', $post_id ) )
            return;
        }
        
        //Lets Do This
        if( isset( $_POST['sociableoff'] ) ){
            update_post_meta( $post_id, '_sociableoff' , $_POST['sociableoff'] );
        } else {
            delete_post_meta( $post_id, '_sociableoff' );
        }
        
        return $post_id;
    }
    
    /**
     * This Function Runs Before The Options Are Printed Out.
     */
    function general_options_callback(){
        
        return true;
    }
    
    /**
     * This Function Runs Before The Location Options Are Echoed Out.
     */
    function location_options_callback(){
        echo '<p>' . __( 'Please Select The Locations That You Wish To Allow The Sociable 2 Plugin To Insert The Links.' ) . '</p>';
    }
    
    /**
     * Adds A Function For The add_settings_field(); function
     * 
     * should be passed:
     * $data = array(
     *      'id' => 'field_id_and_name',
     *      'description' => 'field Description Should Go Here, This is Not The Title, Rather The Description'
     * );
     */
    function Checkbox( $data ){
        global $sociable_options;
        
        //Save The Locations As a seperate array option
        if( isset( $data['locations'] ) ){
            $name = 'sociable_options[locations][' . $data['id'] . ']';
            $checked = ( isset( $sociable_options['locations'][$data['id']] ) ) ? 'checked="checked"' : '' ;
        } else {
            $name = 'sociable_options[' . $data['id'] . ']';
            $checked = ( isset( $sociable_options[$data['id']] ) ) ? 'checked="checked"' : '' ;
        }
        
        
	echo '<input ' . $checked . ' id="' . $data['id'] . '" name="' . $name . '" type="checkbox" /> <span class="description">' . $data['description'] . '</span>';
    }
    
    function TextInput( $data ){
        global $sociable_options;
        
        $value = ( isset( $sociable_options[$data['id']] ) ) ? $sociable_options[$data['id']] : '';
        
        echo '<input id="' . $data['id'] . '" name="sociable_options[' . $data['id'] . ']" size="40" type="text" value="' . esc_attr( $value ) . '" /> <br /><span class="description">' . $data['description'] . '</span>';
        
    }
    
    function TextArea( $data ){
        global $sociable_options;
        
        $value = ( isset( $sociable_options[$data['id']] ) ) ? $sociable_options[$data['id']] : '';
        
        echo '<textarea id="' . $data['id'] . '" name="sociable_options[' . $data['id'] . ']" >' . $value . '</textarea> <br /><span class="description">' . $data['description'] . '</span>';
        
    }
    
    function radio( $data ){
        global $sociable_options;
        
        $cur_val = ( isset( $sociable_options[$data['id']] ) ) ? $sociable_options[$data['id']] : 0 ;
        
        echo '<span class="description">' . $data['description'] . '</span><br />';
        foreach( $data['options'] as $value => $option ){
            $selected = ( $value == $cur_val ) ? 'checked="checked"' : '' ;
            echo '<input type="radio" name="sociable_options[' . $data['id'] . ']" value="' . $value . '" ' . $selected . ' /> <span>' . $option . '</span><br />';
        }
    }
    
    
}
function add_ie7() { 		
echo'<!--[if lt IE 7]>
  <script src="http://ie7-js.googlecode.com/svn/version/2.0(beta3)/IE7.js"
  type="text/javascript"></script>
<![endif]-->
<!--[if lt IE 8]>
  <script src="http://ie7-js.googlecode.com/svn/version/2.0(beta3)/IE8.js" 
  type="text/javascript"></script>
<![endif]-->
<!--[if lt IE 9]>
<script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
<![endif]-->';
} 
//add_action('admin_head', 'add_ie7' ); 
?>
