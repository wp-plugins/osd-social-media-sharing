<?php
// Prevent direct access to file
defined('ABSPATH') or die("No script kiddies please!");

$osd_social_media_sharing = new OSDSocialShare;

class OSDSocialShare {
    private $args = null;
    private $user_settings = array("services" => array());

    function __construct($args = NULL) { 
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
                $post->post_content .= "[osd_social_media_sharing]";
            }
        }    
    }

    public function add_style() {
        wp_enqueue_style('osd_sms_css', plugins_url('osd-social-media-sharing/includes/style.css'));
    }

    private function share_link($platform, $button_title, $custom_url) {
        $target = ($this->user_settings['target'] == 'new' && $platform != "email") ? "_blank" : "_self";

        switch ($platform) {
            case 'facebook':
                $url = "https://www.facebook.com/sharer/sharer.php?u={$this->current_url}";
                break;
            case 'twitter':
                $url = "https://twitter.com/intent/tweet?text={$this->post_title}&url={$this->current_url}";
                break;
            case 'google':
                $url = "https://plus.google.com/share?url={$this->current_url}";
                break;
            case 'linkedIn':
                $url = "https://www.linkedin.com/shareArticle?mini=true&url={$this->current_url}&title={$this->post_title}&summary={$this->text}&source={$this->site_name}";
                break;
            case 'pinterest':
                $url = "http://www.pinterest.com/pin/create/button/?url={$this->current_url}&description={$this->post_title}&media=";
                break;
            case 'email':
                $url = "mailto:{$this->email_to}?subject={$this->email_subject}&body={$this->email_body}";
                break;
            default:
                $search = array("[page]", "[title]");
                $replacements = array($this->current_url, $this->post_title);
                $url = str_replace($search, $replacements, $custom_url);
                break;
        }
        // Put the url in an attribute to prevent Pinterest's official pinit script from hijacking the image
        return "<a class='osd-sms-link' data-platform='{$platform}' target='{$target}' title='{$button_title}' href='#' data-url='{$url}' rel='nofollow'>";
    }

    // Shortcode implementation
    public function filter_text_widgets($content) {
        return do_shortcode($content);
    }

    function replace_shortcode($atts = array()) {
        if (!$options = $this->user_settings) {
            return;
        }
        $html = "<div class='osd-sms-title'>{$options['label']}</div>";

        //set vars for share_link here
        $this->site_name = urlencode(get_bloginfo('name'));
        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != '') ? "https://" : "http://";
        $this->current_url = urlencode($protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
        $this->text = '';
        $this->post_title = urlencode(get_the_title());
        $this->email_to = rawurlencode($options['emailTo']);
        $this->email_subject = rawurlencode(get_the_title());
        $this->email_body = rawurlencode(get_permalink());

        foreach ($options['services'] as $platform => $option) {
            $button_title = (isset($option['service-name'])) ? $option['service-name'] : ucfirst($platform);
            $button_title = "Click to share on ".$button_title;
            $custom_url = (isset($option['url'])) ? $option['url'] : '';

            if (isset($option['enabled']) && $option['enabled'] == 1) {
                if (isset($option['button-type']) && $option['button-type'] == 'icon') {
                    if (isset($option['icon']) && $option['icon'] != '') {
                        $html .= "<div class='osd-sms-icon-button'>".$this->share_link($platform, $button_title, $custom_url)."<img src='".wp_get_attachment_url($option['icon'])."' /></a></div>";
                    } else {
                        $html .= "<div class='osd-sms-icon-button osd-no-custom-icon'>".$this->share_link($platform, $button_title, $custom_url)."</a></div>";
                    }
                } else {
                    if (isset($option['service-name'])) {
                        $html .= "<div class='osd-sms-text-button'>".$this->share_link($platform, $button_title, $custom_url).$option['service-name']."</a></div>";
                    } else {
                        $html .= "<div class='osd-sms-text-button'>".$this->share_link($platform, $button_title, $custom_url).ucfirst($platform)."</a></div>";
                    }
                }
            }
        }

        $html = "<div class='osd-sms-wrapper'>{$html}</div>";
        return $html;
    }
}