<?php
/*
Plugin Name: OSD Social Media Sharing
Plugin URI: http://outsidesource.com
Description: Add buttons to share any of your content on facebook, twitter, google plus, pinterest, email and more.
Version: 2.1.1
Author: OSD Web Development Team
Author URI: http://outsidesource.com
License: GPL2v2
*/

// Prevent direct access to file
defined('ABSPATH') or die("No script kiddies please!");

if (is_admin()) {
	include_once('includes/global_settings.php');
	include_once('includes/post_settings.php');
} else {
	include_once('includes/OSDSocialShare.php');
	include_once('includes/js.php');
}

// Add settings page link to plugins page
function osd_social_share_settings_link_generate($links) { 
	$settings_link = '<a href="admin.php?page=osd-social-share-options">Settings</a>'; 
	array_unshift($links, $settings_link); 
	return $links; 
}
add_filter("plugin_action_links_".plugin_basename(__FILE__), 'osd_social_share_settings_link_generate');