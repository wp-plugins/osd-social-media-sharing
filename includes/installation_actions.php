<?php
// Prevent direct access to file
defined('ABSPATH') or die("No script kiddies please!");

if (get_option('osd_social_share_options') === false) {
    $default_options = array(
        'label' => 'Share this:',
        'target' => 'new',
        'emailTo' => 'someone@example.com',
        'post_types' => array('post' => '1'),
        'services' => array(
            'email' => array(
                    'order' => '1',
                    'button-type' => 'icon',
                    'icon' => '',
                    'enabled' => '1'
            ),
            'twitter' => array(
                    'order' => '2',
                    'button-type' => 'icon',
                    'icon' => '',
                    'enabled' => '1'
                ),
            'linkedIn' => array(
                    'order' => '3',
                    'button-type' => 'icon',
                    'icon' => '',
                    'enabled' => '1'
            ),
            'google' => array(
                    'order' => '4',
                    'button-type' => 'icon',
                    'icon' => '',
                    'enabled' => '1'
            ),
            'pinterest' => array(
                    'order' => '5',
                    'button-type' => 'icon',
                    'icon' => '',
                    'enabled' => '1'
            ),
            'facebook' => array(
                    'order' => '6',
                    'button-type' => 'icon',
                    'icon' => '',
                    'enabled' => '1'
            )
        )
    );

    add_option('osd_social_share_options', $default_options, '', 'yes');
}