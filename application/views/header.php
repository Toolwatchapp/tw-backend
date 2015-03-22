<!DOCTYPE html>
<html lang="en">
<head>
    <title>Toolwatch • Easily measure and track the accuracy of your mechanical watch</title>
    <meta name="keywords" content="toolwatch, toolwatchapp, accuracy, precision, measure, mechanical watch, manual winding, automatic winding">
    <meta name="description" content="Easily measure and track the accuracy of your mechanical watch">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='//fonts.googleapis.com/css?family=Raleway:500,700' rel='stylesheet' type='text/css'>
    <link href='//fonts.googleapis.com/css?family=Raleway:700,400' rel='stylesheet' type='text/css'>
	<?php
		foreach($styleSheets as $css) { echo '<link rel="stylesheet" href="'.css_url($css).'">'; }
		foreach($javaScripts as $js) { echo '<script src="'.js_url($js).'"></script>'; }
	?>
	<script>
          (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
          (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
          m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
          })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

          ga('create', 'UA-59148878-1', 'auto');
          ga('send', 'pageview');
    </script>
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
            <div class="modal-footer">
			     <p>Handcrafted with love in Lausanne, Switzerland, near the Watch Valley. Copyright 2015.</p>
            </div>
		</div>
	  </div>
	</div>
    <header class="navbar <?php echo $headerClass; ?>">
        <div class="container container-fluid">
            <div class="row">
               <div class="col-md-12"><a href="<?php echo base_url(); ?>"><div class="logo"></div></a></div>
            </div>
            <div class="row collapse navbar-collapse" id="nav-menu">
                <div class="col-md-12">
                    <ul class="nav navbar-nav">
                        <?php 
                            if($userIsLoggedIn)
                            {
                                echo '<li><a href="/logout/" title="Logout">Logout</a></li>';
                                echo '<li><a href="/measures/" title="Measures">Measures</a></li>';
                            }
                            else
                            {
                                
                                echo '<li><a href="#" title="Login" data-toggle="modal" data-target="#pageModal" data-modal-update="true" data-href="/login/">Login</a></li>';
                                echo '<li><a href="#" title="Measures" data-toggle="modal" data-target="#pageModal" data-modal-update="true" data-href="/login/">Measures</a></li>';
                            }
                        ?>
						<li><a href="/about/">About</a></li>
						<li><a href="/contact/">Contact</a></li>
                    </ul>
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