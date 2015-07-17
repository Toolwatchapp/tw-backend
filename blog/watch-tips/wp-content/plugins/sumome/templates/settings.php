<style type="text/css">

#sumome {
  width: 100%;
  box-sizing: border-box;
  -moz-box-sizing: border-box;
  -webkit-box-sizing: border-box;
}

#sumome .row {
  width: 100%;
  margin-left: auto;
  margin-right: auto;
  margin-top: 0;
  margin-bottom: 0;
  box-sizing: border-box;
  -moz-box-sizing: border-box;
  -webkit-box-sizing: border-box;
}

#sumome .row .row {
  width: auto;
  margin-left: -0.9375rem;
  margin-right: -0.9375rem;
  margin-top: 0;
  margin-bottom: 0;
  max-width: none;
}

#sumome .columns {
  position: relative;
  padding-left: 0.9375rem;
  padding-right: 0.9375rem;
  float: left;
  box-sizing: border-box;
  -moz-box-sizing: border-box;
  -webkit-box-sizing: border-box;
}

#sumome .large-4 {
  width: 33.33333%;
}

#sumome .large-8 {
  width: 66.66667%;
}

#sumome .large-12 {
  width: 100%;
}

#sumome .th {
  line-height: 0;
  display: inline-block;
  border: solid 4px #fff;
  max-width: 100%;
  -webkit-box-shadow: 0 0 0 1px rgba(0,0,0,0.2);
  box-shadow: 0 0 0 1px rgba(0,0,0,0.2);
  -webkit-transition: all 200ms ease-out;
  -moz-transition: all 200ms ease-out;
  transition: all 200ms ease-out;
}

#sumome img {
  display: inline-block;
  vertical-align: middle;
  max-width: 100%;
  height: auto;
}

#sumome h4 {
  font-size: 1.4375rem;
  line-height: 1.4;
  margin: .5em 0;
}

#sumome .sumome-instructions {
  background: #ffffd5;
  border: 1px solid #ffffa2;
  padding: .5em;
}

</style>

<div id="sumome">

<div class="wrap">
  <h2>SumoMe WordPress Plugin</h2>
</div>

<!-- Second Band (Image Right with Text) -->

<div class="row">
  <div class="large-8 columns">
    <h4>Step 1. Welcome to the family! Let's register your account</h4>
    <div class="row">
      <div class="large-12 columns">
        <p>Click the blue tab on the top right of your screen. It may be tiny, but it's there. Way over there ===></p>

        <p>Sign up to register your account.</p>

		<p>If you have any issues during installation, please <a target="_blank" href="http://help.sumome.com">check out our FAQ</a>.</p>
      </div>
    </div>
  </div>
  <div class="large-4 columns">
    <a class="th"><img src="<?php echo plugins_url('images/sumome-site-badge.png', dirname(__FILE__)) ?>"></a>
  </div>
  <div class="large-12 columns">
    <hr />
  </div>
</div>

<!-- Third Band (Image Left with Text) -->

<div class="row">
  <div class="large-4 columns">
    <a class="th"><img src="<?php echo plugins_url('images/sumome-site-store.png', dirname(__FILE__)) ?>"></a>
  </div>
  <div class="large-8 columns">
    <h4>Step 2. Install Apps</h4>
    <div class="row">
      <div class="large-12 columns">
        <p>Click on the Sumo Store icon and click the app you want to install.</p>

        <p>Bamn! The app is now live on your site.</p>
      </div>
    </div>
  </div>
  <div class="large-12 columns">
    <hr />
  </div>
</div>

<!-- Fourth Band (Image Right with Text) -->

<div class="row">
  <div class="large-8 columns">
    <h4>Step 3. Grow Faster</h4>
    <div class="row">
      <div class="large-12 columns">
        <p>Click the SumoMe blue tab on your site to return to your apps.</p>

        <p>Click on the app icons to edit the settings.</p>

        <p>Shazam. Your site is getting better while you sleep!</p>
      </div>
    </div>
  </div>
  <div class="large-4 columns">
    <a class="th"><img src="<?php echo plugins_url('images/sumome-site-highlighter.png', dirname(__FILE__)) ?>"></a>
  </div>
  <div class="large-12 columns">
    <hr />
  </div>
</div>

<!-- Fifth Band (Image Left with Text) -->

<div class="row">
  <div class="large-4 columns">
    <a class="th" href="http://sumome.com" target="_blank"><img src="<?php echo plugins_url('images/sumome-site-site.png', dirname(__FILE__)) ?>"></a>
  </div>
  <div class="large-8 columns">
    <h4>Step 4. Leave a Review :)</h4>
    <div class="row">
      <div class="large-12 columns">
       <p>We'll love you forever if you leave an <a target="_blank" href="http://wordpress.org/support/view/plugin-reviews/sumome">honest review here</a> of the SumoMe plugin. :)</p>
		<p></p>
      </div>
    </div>
  </div>
  <div class="large-12 columns">
    <hr />
  </div>
</div>

<div class="row">
<form method="post" action="options.php">
    <?php settings_fields('sumome'); ?>
    <table class="form-table">
      <?php do_settings_fields('sumome', 'sumome-settings') ?>
    </table>
    <?php submit_button(); ?>
  </form>
    <div class="sumome-instructions">
If you already have a site ID from a previous installation and you wish to retain all your settings then enter the site ID below otherwise you may use a new site ID to perform a new installation.  Changing the site ID will lose all settings, apps and purchases.
    </div>

</div>

<!-- Sixth Band (Image right with Text) -->
<!--
<div class="row">
  <div class="large-8 columns">
    <a class="th" href="http://sumome.com/" target="_blank"><img src="<?php echo plugins_url('images/sumome-site-site.png', dirname(__FILE__)) ?>"></a>
  </div>
  <div class="large-12 columns">
    <h4>Step 5. Visit SumoMe</h4>
    <div class="row">
      <div class="large-4 columns">
        <p>If you like the plugin please leave an <a target="_blank" href="http://wordpress.org/support/view/plugin-reviews/sumome">honest review here</a>.</p>
      </div>
    </div>
  </div>
  <div class="large-12 columns">
    <hr />
  </div>
</div>
-->

</div>
