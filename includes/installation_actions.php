<?php
// Prevent direct access to file
defined("ABSPATH") or die("No script kiddies please!");

$options = get_option("osd_social_share_options");
if ($options === false || !isset($options["services"]["email"])) {
    if ($options === false) {
        add_option("osd_social_share_options", $osd_social_media_sharing->defaults, "", "yes");
    } else {
        update_option("osd_social_share_options", $osd_social_media_sharing->defaults);
    }
}