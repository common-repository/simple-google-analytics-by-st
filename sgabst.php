<?php
/* Plugin Name: Simple Google Analytics by ST
Plugin URI: #
Description: This is a simple plugin which allows you to include your Google Analytics tracking code in the website.
Version: 1.0.3
Author: Sumanta Relkar
Simple Google Analytics by ST is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Simple Google Analytics by ST is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details see <http://www.gnu.org/licenses/>.
*/

/** Exit if accessed directly */
if (!defined('ABSPATH'))
  {
    exit;
  }

define('SGABST_PLUGIN_DIR_URL', plugin_dir_url(__FILE__));
define('SGABST_PLUGIN_VER', '1.0.3');


/** Add Custom Admin Menu */
add_action('admin_menu', 'sgabst_add_google_analytics_menu');
add_action('admin_init', 'sgabst_register_analytics_settings');

/** Register our setting with unique slugs */
function sgabst_register_analytics_settings()
  {
    register_setting('sgabst_option-group', 'sgabst_option_text');
    register_setting('sgabst_option-group', 'sgabst_option_chk');
  }

/** Add our menu in the Setting menu */
function sgabst_add_google_analytics_menu()
  {
    $sgabst = add_options_page('Google Analytics Setting', 'Add Google Analytics', 'manage_options', 'sgabst-google-analytics', 'sgabst_check_user_capability');
    
    // load css on Google Analytics Setting page only
    add_action('load-' . $sgabst, 'sgabst_plugin_scripts');
  }

/** Load our css */
function sgabst_plugin_scripts()
  {
    wp_enqueue_style('sgabst_admin_css', SGABST_PLUGIN_DIR_URL . 'css/sgabst.css', '', SGABST_PLUGIN_VER);
  }

/** Checking Permission of the current user for access this page  */
function sgabst_check_user_capability()
  {
    if (!current_user_can('manage_options'))
      {
        wp_die('You do not have sufficient permissions to access this page.');
      }
?>

    <div class="sgabst-wrapper">
	    <h1>Simple Google Analytics by ST</h2>
        <h3>Google Analytics Setting</h3>

        <form method="post" id="sgabst-form" action="options.php">
            <?php
    settings_fields('sgabst_option-group');
    do_settings_fields('sgabst_option-group', '');
    // Get an array of options from the database.
    $optionschkVal = esc_attr(get_option('sgabst_option_chk'));
    //print_r($options);
?>
			
			<table class="form-table">
			<tr valign="top">
			<th scope="row">
			Insert tracking code :</th>
			<td><input type="checkbox" id="insetgaCode" name="sgabst_option_chk" value="1" <?php
    checked($optionschkVal, 1);
?> />
            </td>
			</tr >
			<tr valign="top">
			<th scope="row">
			Enter your tracking code : <br/>(Ex:UA-999999-9)</th>
			<td><input type="text" name="sgabst_option_text" value="<?php
    echo esc_attr(get_option('sgabst_option_text'));
?>" placeholder="UA-999999-9" />
			</td>
			</tr>
			</table>
            <?php
    submit_button();
?>
        </form>
	</div>
<?php
  }

/** Add script in the header section */
add_action('wp_head', 'sgabst_add_analytics');
function sgabst_add_analytics()
  {
    
    $trackingCode   = esc_attr(get_option('sgabst_option_text'));
    $optionschkVald = esc_attr(get_option('sgabst_option_chk'));
    
    if (!empty($trackingCode) && (1 == $optionschkVald))
      {
        if (!current_user_can('manage_options') && !is_admin())
          {
            
            $add_analytics = "<!-- Google Analytics --><script type='text/javascript'>
      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', '" . $trackingCode . "']);
      _gaq.push(['_trackPageview']);
      _gaq.push(['_trackPageLoadTime']);
      (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();
    </script><!-- End Google Analytics -->";
            
            echo $add_analytics;
          }
      }
  }

/** Plugin activation */

register_activation_hook(__FILE__, 'sgabst_plugin_install');
function sgabst_plugin_install()
  {
    
    set_transient('sgabst-admin-notice-activation', true, 5);
    
  }

add_action('admin_notices', 'sgabst_admin_notice_activation_notice');

function sgabst_admin_notice_activation_notice()
  {
    
    /* Check transient, if available display notice */
    if (get_transient('sgabst-admin-notice-activation'))
      {
?>
        <div class="updated notice is-dismissible">
            <p>Thank you for using this plugin!</p>
        </div>
        <?php
        /* Delete transient, only display this notice once. */
        delete_transient('sgabst-admin-notice-activation');
      }
  }

/** Plugin deactivation */

register_deactivation_hook(__FILE__, 'sgabst_plugin_deactivation');
function sgabst_plugin_deactivation()
  {
  }

?>