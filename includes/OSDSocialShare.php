<?php
// Prevent direct access to file
defined('ABSPATH') or die("No script kiddies please!");

class OSDSocialShare {
    private $args = null;
    private $options = null;
    public $defaults = array(
        "label" => "Share this:",
        "target" => "new",
        "emailTo" => "someone@example.com",
        "post_types" => array("post" => 1),
        "services" => array(
            "facebook" => array(
                "url" => "https://www.facebook.com/sharer/sharer.php?u={{CURRENT_URL}}",
                "button-type" => "icon",
                "enabled" => 1,
            ),
            "twitter" => array(
                "url" => "https://twitter.com/intent/tweet?text={{POST_TITLE}}&url={{CURRENT_URL}}",
                "button-type" => "icon",
                "enabled" => 1,
            ),
            "google" => array(
                "url" => "https://plus.google.com/share?url={{CURRENT_URL}}",
                "button-type" => "icon",
                "enabled" => 1,
            ),
            "linkedIn" => array(
                "url" => "https://www.linkedin.com/shareArticle?mini=true&url={{CURRENT_URL}}&title={{POST_TITLE}}&summary=&source={{SITE_NAME}}",
                "button-type" => "icon",
                "enabled" => 1,
            ),
            "pinterest" => array(
                "url" => "http://www.pinterest.com/pin/create/button/?url={{CURRENT_URL}}&description={{POST_TITLE}}&media=",
                "button-type" => "icon",
                "enabled" => 1,
            ),
            "email" => array(
                "url" => "mailto:{{EMAIL_TO}}?subject={{EMAIL_SUBJECT}}&body={{EMAIL_BODY}}",
                "button-type" => "icon",
                "enabled" => 1,
            ),
            "reddit" => array(
                "url" => "http://www.reddit.com/submit/?url={{CURRENT_URL}}",
                "button-type" => "icon",
                "order" => -1,
                "enabled" => 0,
            )
        )
    );


    // Constructor
    function __construct($args = NULL) {
        $this->args = $args;
        $this->options = get_option("osd_social_share_options");
        $this->options = ($this->options == false) ? array() : $this->options;

        // Multilingual
        load_plugin_textdomain('osd-sms-domain', false, basename(dirname(__FILE__))."/lang/");

        // Populate the default services classes (this makes sure anything newly added to the service arrays will show up in updates)
        foreach ($this->defaults["services"] as $name => $service) {
            foreach ($service as $key => $value) {
                if (!isset($this->options["services"][$name][$key]) || $this->options["services"][$name][$key] === "") {
                    $this->options["services"][$name][$key] = $this->defaults["services"][$name][$key];
                }
            }
            if (!isset($this->options["services"][$name]) || $this->options["services"][$name] == "") {
                $this->options["services"][$name] = $this->defaults["services"][$name];
            }
        }

        // Populate the other defaults
        foreach ($this->defaults as $key => $value) {
            if (!isset($this->options[$key]) || $this->options[$key] == "") {
                $this->options[$key] = $this->defaults[$key];
            }
        }

        // Add front-end filters
        if (!is_admin()) {
            add_action("wp", array($this, "filter_the_content"));
            add_filter("widget_text", array($this, "filter_text_widgets"));
            add_shortcode("osd_social_media_sharing", array($this, "replace_shortcode"));

            // Load style sheet
            add_action("wp_enqueue_scripts", array($this, "add_style"));
        }
    }


    // Retrieve options from this class
    public function get_options() {
        return $this->options;
    }


    // Filter the content
    public function filter_the_content() {
        if(is_main_query() && is_singular()) {
            global $post;
            $hide = get_post_meta($post->ID, 'osd_remove_sms_icons', true);

            if ($hide != 1
                && isset($this->options['post_types'])
                && count($this->options['post_types']) > 0
                && isset($this->options['post_types'][$post->post_type])
                && $this->options['post_types'][$post->post_type] == 1) {
                $post->post_content .= "[osd_social_media_sharing]";
            }
        }    
    }


    // Adds styling for the front-end
    public function add_style() {
        wp_enqueue_style('osd_sms_css', plugins_url('osd-social-media-sharing/includes/style.css'));
    }


    // Creates a HTML share link
    private function share_link($platform, $button_title, $custom_url) {
        $target = ($this->options["target"] == "new" && $platform != "email") ? "_blank" : "_self";
        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != '') ? "https://" : "http://";
        $searches = array(
            "{{SITE_NAME}}", 
            "{{CURRENT_URL}}", 
            "[page]",
            "{{POST_TITLE}}", 
            "[title]",
            "{{EMAIL_TO}}",
            "{{EMAIL_SUBJECT}}", 
            "{{EMAIL_BODY}}",
        );
        $replacements = array(
            urlencode(get_bloginfo("name")),
            urlencode($protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']),
            urlencode($protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']),
            urlencode(get_the_title()),
            urlencode(get_the_title()),
            rawurlencode($this->options['emailTo']),
            rawurlencode(get_the_title()),
            rawurlencode(get_permalink()),
        );
        $url = str_replace($searches, $replacements, $custom_url);

        // Put the url in an attribute to prevent Pinterest's official pinit script from hijacking the image
        return "<a class='osd-sms-link' data-platform='{$platform}' target='{$target}' title='{$button_title}' href='#' data-url='{$url}' rel='nofollow'>";
    }


    // Shortcode implementation
    public function filter_text_widgets($content) {
        return do_shortcode($content);
    }


    // Shortcode function (everything runs on the shortcode)
    function replace_shortcode($atts = array()) {
        $html = "<div class='osd-sms-title'>{$this->options['label']}</div>";
        foreach ($this->options["services"] as $platform => $link) {
            $button_title = (isset($link['service-name'])) ? $link['service-name'] : ucfirst($platform);
            $button_title = __("Click to share on ", "osd-sms-domain").$button_title;
            $custom_url = (isset($link['url'])) ? $link['url'] : '';

            if (isset($link['enabled']) && $link['enabled'] == 1) {
                if (isset($link['button-type']) && $link['button-type'] == 'icon') {
                    if (isset($link["icon"]) && $link["icon"] != "") {
                        $html .= "<div class='osd-sms-icon-button'>".$this->share_link($platform, $button_title, $custom_url)."<img src='".wp_get_attachment_url($link['icon'])."' /></a></div>";
                    } else {
                        $html .= "<div class='osd-sms-icon-button osd-no-custom-icon'>".$this->share_link($platform, $button_title, $custom_url)."</a></div>";
                    }
                } else {
                    if (isset($link["service-name"])) {
                        $html .= "<div class='osd-sms-text-button'>".$this->share_link($platform, $button_title, $custom_url).$link['service-name']."</a></div>";
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

$osd_social_media_sharing = new OSDSocialShare();