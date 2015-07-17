<?php
/*
Plugin Name: SumoMe
Plugin URI: http://sumome.com
Description: Free Tools to grow your email list from SumoMe.com
Version: 1.10
Author: SumoMe
Author URI: http://www.SumoMe.com
*/

if (!class_exists('WP_Plugin_SumoMe'))
{

class WP_Plugin_SumoMe {
  public function __construct()
  {
    add_action('wp_head', array(&$this, 'append_script_code'));
    add_action('admin_head', array(&$this, 'append_admin_script_code'));
    add_action('admin_menu', array(&$this, 'admin_menu'));
    add_action('admin_init', array(&$this, 'admin_init'));
  }

  public static function activate()
  {
  }

  public static function deactivate()
  {
  }

  public function admin_init()
  {
    register_setting('sumome', 'sumome_site_id', array($this, 'sanitize_site_id'));

    $this->check_generate_site_id();

    add_settings_section(
      'sumome-settings',
      'Settings',
      null,
      'sumome'
    );

    add_settings_field(
      'sumome-site_id',
      'Site ID',
      array(&$this, 'settings_field_site_id'),
      'sumome',
      'sumome-settings',
      array('field' => 'sumome_site_id', 'label_for' => 'sumome_site_id')
    );
  }

  public function sanitize_site_id($value)
  {
    $value = preg_replace('/[^0-9a-f]/', '', strtolower($value));

    return $value;
  }

  public function settings_field_site_id($args)
  {
    $field = $args['field'];
    $value = get_option($field);

    if (!$value) {

    }

    echo <<<EOF
<script type="text/javascript">
function sumome_generate_site_id() {
  function _sumome_r() {
    return (Math.random().toString(16)+"000000000").substr(2,8);
  }

  var new_sumome_site_id = _sumome_r() + _sumome_r() + _sumome_r() + _sumome_r() + _sumome_r() + _sumome_r() + _sumome_r() + _sumome_r();

  document.getElementById('sumome_site_id').value = new_sumome_site_id;
}
</script>
EOF;
    echo sprintf('<input type="text" name="%s" id="%s" value="%s" style="width: 540px" /> <button onclick="sumome_generate_site_id(); return false;" class="button">Get new site ID</button>', $field, $field, esc_attr($value));
  }

  public function admin_menu()
  {
    add_options_page('SumoMe', 'SumoMe', 'manage_options', 'sumome', array(&$this, 'plugin_settings_page'));
    add_menu_page('SumoMe', 'SumoMe', 'manage_options', 'options-general.php?page=sumome', '', plugins_url('sumome/images/icon.png'));
  }

  public function plugin_settings_page()
  {
    if (!current_user_can('manage_options'))
    {
      wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    include(sprintf('%s/templates/settings.php', dirname(__FILE__)));
  }

  public function check_generate_site_id()
  {
    $site_id = get_option('sumome_site_id');

    if (!$site_id) {
      $site_id = '';
      for ($i = 0; $i < 8; $i++) {
        $site_id .= substr(md5(uniqid()), 0, 8);
      }

      update_option('sumome_site_id', $site_id);
    }
  }

  public function append_script_code()
  {
    $this->check_generate_site_id();

    $site_id = get_option('sumome_site_id');

    if ($site_id) {
      echo('<script data-cfasync="false" src="//load.sumome.com/" data-sumo-site-id="' . esc_attr($site_id) . '" async></script>');
    }
  }

  public function append_admin_script_code()
  {
    if (defined('XMLRPC_REQUEST') || defined('DOING_AJAX') || defined('IFRAME_REQUEST'))
      return false;

    $this->check_generate_site_id();

    $site_id = get_option('sumome_site_id');

    if ($site_id) {
      echo('<script data-cfasync="false" src="//load.sumome.com/" data-sumo-mode="admin" data-sumo-site-id="' . esc_attr($site_id) . '" async></script>');
    }
  }
}

} // end class_exists

register_activation_hook(__FILE__, array('WP_Plugin_SumoMe', 'activate'));
register_deactivation_hook(__FILE__, array('WP_Plugin_SumoMe', 'deactivate'));

$wp_plugin_sumome = new WP_Plugin_SumoMe();

function sumome_plugin_settings_link($links)
{
  $settings_link = '<a href="options-general.php?page=sumome">Settings</a>';
  array_unshift($links, $settings_link);
  return $links;
}

$plugin = plugin_basename(__FILE__);
add_filter('plugin_action_links_'.$plugin, 'sumome_plugin_settings_link');
