    <footer>
    <?php if(!$this->agent->is_mobile()){ ?>
        <div id="publication_footer" class="publication">

              <a target="_blank" href="http://www.fratellowatches.com/toolwatch-just-got-better/"><img src="<?php echo img_url('fratello_logos_transparant.png');?>"></a>
              <a target="_blank" href="http://wristreview.com/?p=16698"><img src="<?php echo img_url('wristreview.png');?>"></a>
              <a target="_blank" href="http://www.producthunt.com/tech/toolwatch"><img src="<?php echo img_url('product-hunt-logo-horizontal-orange.png');?>"></a>
              <a target="_blank" href="http://www.hebdo.ch/hebdo/montres-passion/detail/precision-testez-votre-montre-en-ligne"><img src="<?php echo img_url('logo_hebdo_2014.png');?>"></a>
              <a target="_blank" href="<?php echo base_url();?>assets/pdf/ToolwatchEuropastar.pdf"><img src="<?php echo img_url('Europa-star-logo-blanc.jpg');?>"></a>
              <a target="_blank" href="https://www.ablogtowatch.com/toolwatch-io-watch-accuracy-app/"><img src="<?php echo img_url('aBlogtoWatch-Logo.jpg');?>"></a>
        </div>
     <?php } ?>
        <div class="container container-fluid">

            <div class="row">

                <div class="col-sm-12">
                    <div class="links col-sm-2">
                        <a href="<?php echo base_url(); ?>#demo-screen">Features</a>
                    </div>
                    <div class="links col-sm-2">
                        <a href="https://blog.toolwatch.io/watch-tips/">Blog</a>
                    </div>

                    <?php
                        if($userIsLoggedIn)
                        {
                            echo '
                            <div class="links col-sm-2">
                                <a href="/measures/">My Measures</a>
                            </div>';
                        }
                        else
                        {
                            echo '
                            <div class="links col-sm-2">
                                <a href="#" title="Login" data-toggle="modal" data-target="#pageModal" data-modal-update="true" data-href="/login/">My Measures</a>
                            </div>';
                        }
                    ?>

                    <div class="links col-sm-2">
                        <a href="https://trello.com/b/ExI1gUJz/toolwatch-public-roadmap">Roadmap</a>
                    </div>

                    <div class="links col-sm-2">
                        <a href="/about/">About</a>
                    </div>
                    <div class="links col-sm-2">
                        <a href="/contact/">Contact</a>
                    </div>
                    <div style="margin-top: 20px;" class="social col-sm-12">
                        <a href="https://instagram.com/toolwatchapp/" target="_blank" title="Instagram"><span class="fa fa-instagram"></span></a>
                        <a href="https://www.pinterest.com/toolwatch/" target="_blank" title="Pinterest"><span class="fa fa-pinterest-p"></span></a>
                        <a href="https://www.facebook.com/Toolwatch" target="_blank" title="Facebook"><span class="fa fa-facebook-square"></span></a>
                        <a href="https://twitter.com/ToolwatchApp" target="_blank" title="Twitter"><span class="fa fa-twitter"></span></a>
                        <a href="https://m.me/297656450437407" target="_blank" title="Facebook Messenger"><img style="margin-top: -12px; margin-left: 10px;" width='27px' height='auto' src="/assets/img/messenger.png"/></a>

                    </div>
                    <div style="margin-top: 20px;" class="social col-sm-12">
                        <style>.bmc-button img{vertical-align: middle !important;}.bmc-button{text-decoration: none; !important;display:inline-block !important;padding:5px 10px !important;color:#FFFFFF !important;background-color:#BB5794 !important;border-radius: 3px !important;border: 1px solid transparent !important;font-size: 26px !important;box-shadow: 0px 1px 2px rgba(190, 190, 190, 0.5) !important;-webkit-box-shadow: 0px 1px 2px 2px rgba(190, 190, 190, 0.5) !important;-webkit-transition: 0.3s all linear !important;transition: 0.3s all linear !important;margin: 0 auto !important;font-family:"Cookie", cursive !important;}.bmc-button:hover, .bmc-button:active, .bmc-button:focus {-webkit-box-shadow: 0 4px 16px 0 rgba(190, 190, 190,.45) !important;box-shadow: 0 4px 16px 0 rgba(190, 190, 190,.45) !important;opacity: 0.85 !important;color:#FFFFFF !important;}</style><link href="https://fonts.googleapis.com/css?family=Cookie" rel="stylesheet"><a class="bmc-button" target="_blank" href="https://www.buymeacoffee.com/m7dP5qT"><img src="https://www.buymeacoffee.com/assets/img/BMC-btn-logo.svg" alt="BMC logo"><span style="margin-left:5px">Buy us a coffee</span></a>
                    </div>

                </div>

            </div>
            <div class="row">
                <div class="col-md-12 copyright">
                    <p>Handcrafted with love in Lausanne, Switzerland, near the Watch Valley. Copyright &copy; <?php 
$copyYear = 2015; 
$curYear = date('Y'); 
echo $copyYear . (($copyYear != $curYear) ? '-' . $curYear : '');
?>.</p>
                </div>
            </div>
        </div>

    </footer>
</body>
</html>
