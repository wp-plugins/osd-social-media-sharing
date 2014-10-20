<?php
// Prevent direct access to file
defined('ABSPATH') or die("No script kiddies please!");

function osd_social_share_meta_boxes() {
    new OSDSocialShareMetaBoxes();
}
add_action('load-post.php', 'osd_social_share_meta_boxes');
add_action('load-post-new.php', 'osd_social_share_meta_boxes');

class OSDSocialShareMetaBoxes {
    function __construct() {
        add_action('add_meta_boxes', array($this, 'add_meta_box'));
        add_action('save_post', array($this, 'save'));
    }

    public function add_meta_box($post_type) {
        $user_post_types = get_option('osd_social_share_options')['post_types'];

        if (isset($user_post_types[$post_type])){
            //add_meta_box( $id, $title, $callback, $page, $context, $priority, $callback_args ); 
            add_meta_box('osd_enable_sharing', 
                'OSD Social Media Sharing', 
                array($this, 'render_meta_box_content'), 
                '', 
                'side', 
                'default');
        }
    }

    public function render_meta_box_content($post) { 
        // Add an nonce field so we can check for it later.
        wp_nonce_field('osd_sms_meta_nonce', 'osd_sms_meta_nonce');
        $hide = get_post_meta($post->ID, 'osd_remove_sms_icons', true);
        $selected = ($hide == 1) ? " checked='checked'" : '';

        echo "<input type='checkbox'{$selected} name='osd_remove_sms_icons' id='osd_remove_sms_icons' value='1' />";       
        echo "<label for='osd_remove_sms_icons'>Remove Social Media Sharing buttons from this page / post?</label>";
    }

    public function save($post_id) {
        // Verify that the nonce is valid.
        // If this is an autosave, don't do anything.
        // Check the user's permissions.
        if (!isset($_POST['osd_sms_meta_nonce']) 
            || !wp_verify_nonce($_POST['osd_sms_meta_nonce'], 'osd_sms_meta_nonce')
            || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            || ($_POST['post_type'] == 'page' && !current_user_can('edit_page', $post_id)) 
            || !current_user_can('edit_post', $post_id)) {
            return $post_id;
        }
        /* OK, its safe for us to save the data now. */

        $val = ($_POST['osd_remove_sms_icons'] == 1) ? 1 : 0;

        // Update the meta field.
        update_post_meta($post_id, 'osd_remove_sms_icons', $val);
    }
}