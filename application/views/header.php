<!DOCTYPE html>
<html lang="en">
<head>
    <title>Toolwatch • <?php if (isset($title)) {echo $title;
} else {
	echo 'Easily measure and track the accuracy of your mechanical watch';
}

?></title>
    <meta name="keywords" content="toolwatch, toolwatchapp, accuracy, precision, measure, mechanical watch, manual winding, automatic winding">
    <meta name="description" content="Toolwatch makes it super easy to measure the accuracy of any mechanical watch. Keep your watch’s accuracy at its best with Toolwatch.">

    <meta property="og:title" content="<?php if (isset($title)) {echo $title;
} else {
	echo 'Easily measure and track the accuracy of your mechanical watch';
}
?>" />
    <meta property="og:description" content="<?php if (isset($meta_description)) {echo $meta_description;
} else {
	echo 'Toolwatch makes it super easy to measure the accuracy of any mechanical watch. Keep your watch’s accuracy at its best with Toolwatch.';
}
?>" />
    <meta property="og:image" content="<?php if (isset($meta_img)) {echo $meta_img;
} else {
	echo img_url('share.png');
}
?>" />

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='//fonts.googleapis.com/css?family=Raleway:500,700' rel='stylesheet' type='text/css'>
    <link href='//fonts.googleapis.com/css?family=Raleway:700,400' rel='stylesheet' type='text/css'>
    <link rel="apple-touch-icon" sizes="57x57" href="<?php echo ico_url('apple-icon-57x57.png')?>">
    <link rel="apple-touch-icon" sizes="60x60" href="<?php echo ico_url('apple-icon-60x60.png')?>">
    <link rel="apple-touch-icon" sizes="72x72" href="<?php echo ico_url('apple-icon-72x72.png')?>">
    <link rel="apple-touch-icon" sizes="76x76" href="<?php echo ico_url('apple-icon-76x76.png')?>">
    <link rel="apple-touch-icon" sizes="114x114" href="<?php echo ico_url('apple-icon-114x114.png')?>">
    <link rel="apple-touch-icon" sizes="120x120" href="<?php echo ico_url('apple-icon-120x120.png')?>">
    <link rel="apple-touch-icon" sizes="144x144" href="<?php echo ico_url('apple-icon-144x144.png')?>">
    <link rel="apple-touch-icon" sizes="152x152" href="<?php echo ico_url('apple-icon-152x152.png')?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo ico_url('apple-icon-180x180.png')?>">
    <link rel="icon" type="image/png" sizes="192x192" href="<?php echo ico_url('android-icon-192x192.png')?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo ico_url('favicon-32x32.png')?>">
    <link rel="icon" type="image/png" sizes="96x96" href="<?php echo ico_url('favicon-96x96.png')?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo ico_url('favicon-16x16.png')?>">
    <link rel="manifest" href="<?php echo ico_url('manifest.json')?>">
    <link rel="stylesheet" href="<?php echo base_url();?>/assets/js/MediaElement/mediaelementplayer.css" />
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="<?php echo ico_url('ms-icon-144x144.png')?>">
    <meta name="theme-color" content="#ffffff">
	<script>
          (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
          (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
          m=s.getElementsByTagName(o)[0];
a.async=1;
a.src=g;
m.parentNode.insertBefore(a,m)
          })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

         (function(d, s, id){
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {return;}
            js = d.createElement(s);
 js.id = id;
            js.src = "https://connect.facebook.net/en_US/sdk.js#version=v2.2&appId=807383452677000&status=true&cookie=true&xfbml=true";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));

          ga('create', 'UA-59148878-1', 'auto');
          ga('send', 'pageview');
    </script>
    <script type="application/ld+json">
    { "@context" : "http://schema.org",
      "@type" : "Organization",
      "name" : "Toolwatch",
      "url" : "http://toolwatch.io",
      "logo": "http://toolwatch.io/assets/img/toolwatch-square.jpg"
      "sameAs" : [ "https://www.facebook.com/Toolwatch",
        "https://twitter.com/ToolwatchApp",
        "https://www.pinterest.com/toolwatch/",
        "https://instagram.com/toolwatchapp/",
        "https://plus.google.com/104724190750629608501/"]
    }
    </script>

    <meta name="p:domain_verify" content="9f4eefba8c49cf4a79b31c72a7e388a9"/>
<?php
foreach ($styleSheets as $css) {echo '<link rel="stylesheet" href="'.css_url($css).'?'.time().'">';}
foreach ($javaScripts as $js) {echo '<script src="'.js_url($js).'"></script>';}
?>
    <!--[if lt IE 8]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
	<div class="modal fade" id="pageModal" tabindex="-1" role="dialog" aria-labelledby="pageModal" aria-hidden="true" data-keyboard="true" data-backdrop="static">
		<div class="modal-dialog  modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
            <div class="modal-body">
            </div>
		</div>
	  </div>
	</div>
    <header class="navbar <?php echo $headerClass;?>">


        <div class="container container-fluid">

            <div class="row collapse navbar-collapse" id="nav-menu">
                <div class="col-md-12">
                    <div class="nav navbar-nav">

                        <div class="col-md-2">
                            <a href="<?php echo base_url();?>"><div class="logo"></div></a>
                        </div>
                        <div style="margin-top: 10px" class="col-md-1 col-md-offset-4 text-center">
						  <a href="<?php echo base_url();?>#demo-screen">Features</a>
                        </div>
                        <div style="margin-top: 10px" class="col-md-1  text-center">
                          <a href="/about/">About</a>
                        </div>
                        <div style="margin-top: 10px; width: auto" class="col-md-1  text-center">
						  <a href="https://blog.toolwatch.io/watch-tips">Watch Tips</a>
                        </div>
<?php
if ($userIsLoggedIn) {
	echo '<div style="margin-top: 10px" class="col-md-1  text-center"><a href="/logout/" title="Logout">Logout</a></div>';
	echo '<div class="col-md-1 "><a class="btn btn-lg btn-white" href="/measures/" title="Measures">Measures</a></div>';
} else {

	echo '<div style="margin-top: 10px" class="col-md-1  text-center"><a href="#" title="Login" data-toggle="modal" data-target="#pageModal" data-modal-update="true" data-href="/login/">Login</a></div>';
	echo '<div class="col-md-1 "><a class="btn btn-lg btn-white" title="Measures" data-toggle="modal" data-cta="MEASURES" data-target="#pageModal" data-modal-update="true" data-href="/login/">Measures</a></div>';
}
?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button class="navbar-toggle collapsed" data-toggle="collapse" data-target="#nav-menu">
						<span class="fa fa-caret-up"></span>
					</button>
                </div>
            </div>
        </div>
    </header>