<?php

// Exit if accessed directly.
if (!defined('WP_UNINSTALL_PLUGIN'))
  {
    exit;
  }

global $wpdb;

$sgabst_plugin_options = $wpdb->get_results("SELECT option_name FROM $wpdb->options WHERE option_name LIKE '%sgabst_option_text%'");

foreach ($sgabst_plugin_options as $option)
  {
    delete_option($option->option_name);
  }

?>