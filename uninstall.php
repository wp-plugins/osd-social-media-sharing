<?php
// Prevent direct access to file
defined('ABSPATH') or die("No script kiddies please!");

//if uninstall not called from WordPress exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}

//remove options
delete_option('osd_social_share_options');