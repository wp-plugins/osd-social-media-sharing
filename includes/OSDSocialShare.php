<?php
// Prevent direct access to file
defined('ABSPATH') or die("No script kiddies please!");

$osd_social_media_sharing = new OSDSocialShare;

class OSDSocialShare {
    private $args = null;
    private $user_settings = array();

    function __construct($args = 
        NULL) { 
        $this->args = $args;
        $this->user_settings = get_option('osd_social_share_options');

        add_action('wp', array($this, 'filter_the_content'));
        add_filter('widget_text', array($this, 'filter_text_widgets'));
        add_shortcode('osd_social_media_sharing', array($this, 'replace_shortcode'));

        // Load style sheet
        add_action('wp_enqueue_scripts', array($this, 'add_style'));        
    }

    public function filter_the_content() {
        if(is_main_query() && is_singular()) {
            global $post;
            $options = $this->user_settings;
            $hide = get_post_meta($post->ID, 'osd_remove_sms_icons', true);

            if ($hide != 1
                && isset($options['post_types'])
                && count($options['post_types']) > 0
                && isset($options['post_types'][$post->post_type])) {
                $post->post_content .= $this->replace_shortcode();
            }
        }    
    }

    public function add_style() {
        wp_enqueue_style('osd_sms_css', plugins_url('osd-social-media-sharing/includes/style.css'));
    }

    private function share_link($platform) {
        $site_name = urlencode(get_bloginfo('name'));
        $protocol = (isset($_SERVER['HTTPS'])) ? "https://" : "http://";
        $current_url = urlencode($protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
        $text = '';
        $post_title = urlencode(get_the_title());
        $button_title = "Click to share on ".ucfirst($platform);
        $target = ($this->user_settings['target'] == 'new' && $platform != "email") ? "_blank" : "_self";

        switch ($platform) {
            case 'facebook':
                $url = "https://www.facebook.com/sharer.php?u={$current_url}";
                break;
            case 'twitter':
                $url = "https://twitter.com/share?url={$current_url}&text={$post_title}";
                break;
            case 'google':
                $url = "https://plus.google.com/share?url={$current_url}";
                break;
            case 'linkedIn':
                $url = "http://www.linkedin.com/shareArticle?mini=true&url={$current_url}&title={$post_title}&summary={$text}&source={$site_name}";
                break;
            case 'pinterest':
                $url = "http://pinterest.com/pin/create/button/?url={$current_url}&media=&description={$text}";
                break;
            case 'email':
                $url = "mailto:someone@example.com?subject={$post_title}&body={$text}";
                break;
        }

        return "<a class='osd-sms-link' data-platform='{$platform}' target='{$target}' title='{$button_title}' href='{$url}' rel='nofollow'>";
    }

    // Shortcode implementation
    public function filter_text_widgets($content) {
        return do_shortcode($content);
    }

    function replace_shortcode($atts = array()) {
        $options = $this->user_settings;
        $html = "<div class='osd-sms-title'>{$options['label']}</div>";

        foreach ($options as $platform => $option) {
            if (isset($option['enabled']) && $option['enabled'] == 1) {
                if (isset($option['button-type']) && $option['button-type'] == 'icon') {
                    if (isset($option['icon']) && $option['icon'] != '') {
                        $html .= "<div class='icon-button'>".$this->share_link($platform)."<img src='".wp_get_attachment_url($option['icon'])."' /></a></div>";
                    } else {
                        $html .= "<div class='icon-button'>".$this->share_link($platform)."<img src='".plugins_url('osd-social-media-sharing/images/'.$platform.'.svg')."' /></a></div>";
                    }
                } else {
                    $html .= "<div class='text-button'>".$this->share_link($platform).ucfirst($platform)."</a></div>";
                }
            }
        }

        $html = "<div class='osd-sms-wrapper'>{$html}</div>";
        return $html;
    }

    private function apiCall($data, $url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $curlData = curl_exec($curl);
        curl_close($curl);
       
        return json_decode($curlData, true);
    }
}