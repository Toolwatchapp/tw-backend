    <footer>
    <?php if(!$this->agent->is_mobile()){ ?>
        <div id="publication_footer" class="publication">

              <a target="_blank" href="http://www.fratellowatches.com/toolwatch-just-got-better/"><img src="<?php echo img_url('fratello_logos_transparant.png');?>"></a>
              <a target="_blank" href="http://wristreview.com/?p=16698"><img src="<?php echo img_url('wristreview.png');?>"></a>
              <a target="_blank" href="http://www.producthunt.com/tech/toolwatch"><img src="<?php echo img_url('product-hunt-logo-horizontal-orange.png');?>"></a>
              <a target="_blank" href="http://www.hebdo.ch/hebdo/montres-passion/detail/precision-testez-votre-montre-en-ligne"><img src="<?php echo img_url('logo_hebdo_2014.png');?>"></a>
              <a target="_blank" href="<?php echo base_url();?>assets/pdf/ToolwatchEuropastar.pdf"><img src="<?php echo img_url('Europa-star-logo-blanc.jpg');?>"></a>
              <a target="_blank" href="https://theoandharris.com/how-accurate-is-accurate/"><img src="<?php echo img_url('tw.jpg');?>"></a>
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
                        <script type='text/javascript' src='https://ko-fi.com/widgets/widget_2.js'></script><script type ='text/javascript'>kofiwidget2.init('Buy Us a Coffee', '#298ee8', 'A872I1N');kofiwidget2.draw();</script>
                    </div>

                </div>

            </div>
            <div class="row">
                <div class="col-md-12 copyright">
                    <p>Handcrafted with love in Lausanne, Switzerland, near the Watch Valley. Copyright &copy; 2017.</p>
                </div>
            </div>
        </div>

    </footer>
</body>
</html>
