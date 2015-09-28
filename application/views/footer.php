<footer>
<?php if (!$this->agent->is_mobile()) {?>
		        <div id="publication_footer" class="publication">

		                <a href="http://www.fratellowatches.com/toolwatch-just-got-better/"><img src="<?php echo img_url('fratello_logos_transparant.png');?>"></a>
	                    <a href="http://wristreview.com/?p=16698"><img src="<?php echo img_url('wristreview.png');?>"></a>
		                <a href="http://www.producthunt.com/tech/toolwatch"><img src="<?php echo img_url('product-hunt-logo-horizontal-black.png');?>"></a>

		        </div>
	<?php }?>
        <div class="container container-fluid">

            <div class="row">

                <div class="col-sm-12">
                    <div class="col-md-2">
                        <div class="logo"></div>
                    </div>
                    <div class="links col-md-offset-1 col-sm-1">
                        <a href="<?php echo base_url();?>#demo-screen">Features</a>
                    </div>
                    <div class="links col-sm-1">
                        <a href="https://blog.toolwatch.io/watch-tips">Blog</a>
                    </div>

<?php
if ($userIsLoggedIn) {
	echo '<div class="links col-sm-1">
                                <a href="/logout">Logout</a>
                            </div>
                            <div class="links col-sm-1">
                                <a href="/measures/">Measures</a>
                            </div>';
} else {
	echo '<div class="links col-sm-1">
                                <a href="#" title="Login" data-toggle="modal" data-target="#pageModal" data-modal-update="true" data-href="/login/">Login</a>
                            </div>
                            <div class="links col-sm-1">
                                <a href="#" title="Login" data-toggle="modal" data-target="#pageModal" data-modal-update="true" data-href="/login/">Measures</a>
                            </div>';
}
?>


                    <div class="links col-sm-1">
                        <a href="/about/">About</a>
                    </div>
                    <div class="links col-sm-1">
                        <a href="/contact/">Contact</a>
                    </div>
                    <div class="social col-sm-3">
                        <a href="https://instagram.com/toolwatchapp/" target="_blank" title="Instagram"><span class="fa fa-instagram"></span></a>
                        <a href="https://www.pinterest.com/toolwatch/" target="_blank" title="Pinterest"><span class="fa fa-pinterest-p"></span></a>
                        <a href="https://www.facebook.com/Toolwatch" target="_blank" title="Facebook"><span class="fa fa-facebook-square"></span></a>
                        <a href="https://twitter.com/ToolwatchApp" target="_blank" title="Twitter"><span class="fa fa-twitter"></span></a>
                    </div>

                </div>

            </div>
            <div class="row">
                <div class="col-md-12 copyright">
                    <p>Handcrafted with love in Lausanne, Switzerland, near the Watch Valley. Copyright &copy; 2015.</p>
                </div>
            </div>
        </div>

    </footer>
</body>
</html>
