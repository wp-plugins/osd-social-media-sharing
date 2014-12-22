<?php
// Prevent direct access to file
defined('ABSPATH') or die("No script kiddies please!");

//SETTINGS PAGE
$settingsPage = new OSDSocialShareSettings();

class OSDSocialShareSettings {
    private $options;

    public function __construct() {
        add_action('admin_menu', array($this, 'add_submenu_page'));
        add_action('admin_init', array($this, 'page_init'));
    }

    //add options page to wp
    public function add_submenu_page() {
        $osd_sms_settings_page = 
            add_options_page(
                'OSD Social Media Sharing Settings', 
                'OSD Sharing', 
                'manage_options', 
                'osd-social-share-options', 
                array($this, 'create_admin_page')
            ); 
        
        //Load style sheet / js
        add_action("admin_print_styles-".$osd_sms_settings_page, array($this, 'osd_sms_admin_style'));
        add_action("admin_print_scripts-".$osd_sms_settings_page, array($this, 'osd_sms_admin_js'));
    }

    //create options page
    public function create_admin_page() {
        // Set class property
        $this->options = get_option('osd_social_share_options');
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2>OSD Social Media Sharing Settings</h2>        
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields('osd-social-share-options');   
                do_settings_sections('osd-social-share-options');
                submit_button(); 
            ?>
            </form>
        </div>
        <?php
    }

    //register / add options 
    public function page_init() {   
        // Register Style Sheet
        wp_register_style('osd_sms_admin_style', plugins_url('includes/admin_style.css', dirname(__FILE__)));
        wp_register_script('osd_sms_admin_js', plugins_url('includes/admin_js.js', dirname(__FILE__)));

        register_setting(
            'osd-social-share-options', // Option group
            'osd_social_share_options', // Option name
            array($this, 'sanitize') // Sanitize
        );

        add_settings_section(
            'available_settings', // ID
            'Media Share Settings', // Title
            array($this, 'print_section_settings'), // Callback
            'osd-social-share-options' // Page
        );

        add_settings_field(
            'label', // ID
            'Sharing Label', // Title 
            array($this, 'label_callback'), // Callback
            'osd-social-share-options', // Page          
            'available_settings' // Section 
        ); 

        add_settings_field(
            'target', // ID
            'Open Links In', // Title 
            array($this, 'target_callback'), // Callback
            'osd-social-share-options', // Page          
            'available_settings' // Section 
        ); 

        add_settings_field(
            'emailTo', // ID
            'Email Sharing "To:" Address', // Title 
            array($this, 'emailTo_callback'), // Callback
            'osd-social-share-options', // Page          
            'available_settings' // Section 
        ); 

        add_settings_field(
            'post_types', // ID
            'Show Sharing Buttons On', // Title 
            array($this, 'post_types_callback'), // Callback
            'osd-social-share-options', // Page          
            'available_settings' // Section 
        );

        add_settings_field(
            'services', // ID
            'Avaliable Services', // Title 
            array($this, 'services_callback'), // Callback
            'osd-social-share-options', // Page
            'available_settings' // Section           
        );

        add_settings_section(
            'live_preview', // ID
            'Live Preview', // Title
            array($this, 'print_section_preview'), // Callback
            'osd-social-share-options' // Page
        ); 
    }

    //sanitize  
    public function sanitize($input) {
        // use to sanitize all inputs
        return $input;
    }

    /**** output to admin settings screen ****/
    public function print_section_settings() {
        echo "Here you can set all the global media sharing settings. Per post options are available on the edit post page.<br /><br /><strong>To re-order the services, simply drag and drop them in the table below. <br /><br />Shortcode:</strong> [osd_social_media_sharing]";
    }

    public function print_section_preview() {
        echo "<div id='preview-wrapper'>No options selected.</div>";
    }

    public function label_callback() {
        printf(
            '<input type="text" id="label" name="osd_social_share_options[label]" value="%s" />',
            isset($this->options['label']) ? esc_attr($this->options['label']) : 'Share this:'
        );
    }

    public function post_types_callback() {
        $post_types = get_post_types(array('public' => 1), 'array');

        echo "<ul class='post-types'>";
        foreach($post_types as $post_type) {
            $checked = '';
            if(isset($this->options['post_types'][$post_type->name])) {
                $checked = " checked='checked'";
            }
            echo 
                "<li>
                    <input type='checkbox' value='1' id='{$post_type->name}' name='osd_social_share_options[post_types][{$post_type->name}]'{$checked} />
                    <label for='{$post_type->name}'>{$post_type->label}</label>
                </li>";
        }
        echo "</ul>";
    }

    public function target_callback() {        
        echo 
            "<select class='button-type' name='osd_social_share_options[target]'>
                <option value='new'".(($this->options['target'] == 'new') ? ' selected=\"selected\"' : '').">New Window</option>
                <option value='same'".(($this->options['target'] == 'same') ? ' selected=\"selected\"' : '').">Same Window</option>
            </select>";
    }

    public function emailTo_callback() {
        printf(
            '<input type="text" id="emailTo" name="osd_social_share_options[emailTo]" value="%s" />',
            isset($this->options['emailTo']) ? esc_attr($this->options['emailTo']) : 'someone@example.com'
        );
    }

    public function sort_array_order($l, $r) {
        if ((int) $this->options[$l]['order'] < (int) $this->options[$r]['order']) {
            return -1;
        } else if ((int) $this->options[$l]['order'] > (int) $this->options[$r]['order']) {
            return 1;
        }
        return 0;
    }

    public function services_callback() {
        $services_array = array('facebook', 'twitter', 'google', 'linkedIn', 'pinterest', 'email');
        usort($services_array, array($this, 'sort_array_order'));
        $counter = 0;

        echo 
            "<table class='wp-list-table widefat options-wrapper'>
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Service</th>
                        <th>Button Style</th>
                        <th>Preview</th>
                        <th>Custom Icon</th>
                        <th>Enabled</th>
                    </tr>
                </thead>
                <tfoot>
                </tfoot>
                <tbody class='ui-sortable'>";

        foreach ($services_array as $val) {
            $counter++;
            $icon_selected = ' selected="selected"';
            $icon_display = "";
            $text_selected = "";
            $text_display = 'style="display: none;"';

            if (isset($this->options[$val]['button-type']) && $this->options[$val]['button-type'] == 'text') {
                $text_selected = ' selected="selected"';
                $text_display = "";
                $icon_selected = "";
                $icon_display = 'style="display: none;"';
            }

            $enabled_checked = (isset($this->options[$val]['enabled'])) ? ' checked="checked"' : '';
            $icon = (isset($this->options[$val]['icon'])) ? $this->options[$val]['icon'] : '';
            $order = (isset($this->options[$val]['order']) && $this->options[$val]['order'] != "") ? $this->options[$val]['order'] : $counter;
            $icon_url = ($icon != '') ? "<img src='".wp_get_attachment_url($icon)."' />" : "<img src='".plugins_url('images/icons.svg#'.$val, dirname(__FILE__))."' />";

            echo 
                "<tr class='list_item'>
                    <td class='order move'>
                        <div class='count'>{$counter}</div>
                        <input name='osd_social_share_options[{$val}][order]' class='order-val' type='hidden' value='{$order}' />
                    </td>
                    <td class='move'>".ucfirst($val)."</td>
                    <td>
                        <select class='button-type' name='osd_social_share_options[{$val}][button-type]'>
                            <option value='icon'{$icon_selected}>Icon</option>
                            <option value='text'{$text_selected}>Text</option>
                        </select>
                    </td>
                    <td>
                        <div class='text-preview'{$text_display}>".ucfirst($val)."</div>
                        <div class='icon-preview'{$icon_display}>{$icon_url}</div>
                        <input class='icon-id' name='osd_social_share_options[{$val}][icon]' type='hidden' value='{$icon}' />
                        <input class='platform' type='hidden' value='{$val}' />
                        <input class='platform' type='hidden' value='{$val}' />
                    </td>
                    <td><div class='submit button-primary image-picker'>Select</div></td>
                    <td><input class='enable-service' type='checkbox' id='{$val}' name='osd_social_share_options[{$val}][enabled]' value='1'{$enabled_checked} /></td>
                </tr>";
        }

        echo "</tbody></table>";
        echo "<script>var path = '".plugins_url('images/', dirname(__FILE__))."';</script>";
    }
    /**** end output to admin settings screen ****/

    public function osd_sms_admin_style() {
        wp_enqueue_style('osd_sms_admin_style');
    }

    public function osd_sms_admin_js() {
        wp_enqueue_media();
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('osd_sms_admin_js');
    }
}