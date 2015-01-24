document.onready = function() {
    build_preview();
    update_services();

    jQuery('body').on('click', '.image-picker', function() {
        if (jQuery(this).hasClass('remove-icon')) {
            var preview_parent = jQuery(this).parent().prev();
            var platform = jQuery(preview_parent).find('.platform').val();
            jQuery(preview_parent).find('.icon-preview').html("<div class='osd-sms-icon-button osd-no-custom-icon'><div class='osd-sms-link' data-platform='"+platform+"'></div></div>");
            jQuery(preview_parent).find('.icon-id').val('');
            build_preview();
            update_services();
        } else {
            var this_button = jQuery(this);
            var preview_parent = jQuery(this_button).parent().prev();
            wp.media.editor.send.attachment = function(props, attachment) {
                jQuery(preview_parent).find('.icon-preview').html("<img src='"+attachment.url+"' />");
                jQuery(preview_parent).find('.icon-id').val(attachment.id);
                build_preview();
                update_services();
            }
            wp.media.editor.open(this);
            return false;
        }
    });

    jQuery('body').on('change', '.button-type', function() {
        var preview_parent = jQuery(this).parent().next();

        if (jQuery(this).val() == 'text') {
             jQuery(preview_parent).find('.icon-preview').hide();
             jQuery(preview_parent).find('.text-preview').show();
        } else {
             jQuery(preview_parent).find('.icon-preview').show();
             jQuery(preview_parent).find('.text-preview').hide();
        }

        build_preview();
        update_services();
    });

    jQuery('.available-services').on('change', '.enable-service', function() {
        build_preview();
        update_services();
    });

    jQuery(".ui-sortable").sortable({
        update: function() {
            update_order();
        },
        distance: 20
    });

    jQuery('.add-custom').click(function() {
        var last_row = jQuery('table.available-services tr.list_item:last-of-type');
        var new_row_num = parseInt(jQuery(last_row).find('.count').html()) + 1;
        var new_order_num = parseInt(jQuery(last_row).find('.order-val').val()) + 1;
        var new_id = 'custom_'+uniqId();

        var new_row = "<tr class='list_item'>";
        new_row += "<td class='order move'>";
        new_row += "<div class='count'>"+new_row_num+"</div>";
        new_row += "<input name='osd_social_share_options[services]["+new_id+"][order]' class='order-val' type='hidden' value='"+new_order_num+"' />";
        new_row += "</td>";
        new_row += "<td class='move service-name'><input type='text' value='' placeHolder='Custom Service Name' name='osd_social_share_options[services]["+new_id+"][service-name]'></td>";
        new_row += "<td class='custom-url'>";
        new_row += "<input type='text' name='osd_social_share_options[services]["+new_id+"][url]' value='' />";
        new_row += "</td>";
        new_row += "<td>";
        new_row += "<select autocomplete='off' class='button-type' name='osd_social_share_options[services]["+new_id+"][button-type]'>";
        new_row += "<option value='icon'>Icon</option>";
        new_row += "<option value='text'>Text</option>";
        new_row += "</select>";
        new_row += "</td>";
        new_row += "<td>";
        new_row += "<div class='text-preview' style='display: none;'></div>";
        new_row += "<div class='icon-preview'><div class='osd-sms-icon-button osd-no-custom-icon'><div class='osd-sms-link'></div></div></div>";
        new_row += "<input class='icon-id' name='osd_social_share_options[services]["+new_id+"][icon]' type='hidden' value='' />"; 
        new_row += "<input class='platform' type='hidden' value='"+new_id+"' />";
        new_row += "</td>";
        new_row += "<td><div class='submit button-primary image-picker'>Select</div></td>";
        new_row += "<td><input class='enable-service' type='checkbox' id='"+new_id+"' name='osd_social_share_options[services]["+new_id+"][enabled]' value='1' checked='checked' /><div class='delete-custom' title='Delete'>-</div></td>";
        new_row += "</tr>";

        jQuery('table.available-services tbody').append(new_row);
    });

    jQuery('.available-services').on('click', '.delete-custom', function() {
        jQuery(this).parents('tr').first().remove();
        update_order();
        build_preview();
        update_services();
    });

    // Update the button text on keyup for custom service types
    jQuery('body').on('keyup', '.service-name > input', function() {
        jQuery(this).parents('tr').first().find('.text-preview').html(jQuery(this).val());
        build_preview();
    });
}

function uniqId() {
    //return Math.round(new Date().getTime() + (Math.random() * 100));
    return new Date().getTime();
}

function update_order() {
    var counter = 0;
    jQuery('.ui-sortable > tr').each(function() {
        counter++;
        jQuery(this).find('.order-val').val(counter);
        jQuery(this).find('.count').html(counter);
    });

    build_preview();
}

function build_preview() {
    var button_count = 0;
    var preview = jQuery('#preview-wrapper');
    jQuery(preview).html("<div class='preview-hr'>"+jQuery('#label').val()+"</div>");

    jQuery('.enable-service:checked').each(function() {
        button_count++;
        var parent_row = jQuery(this).closest('tr');
        var button = jQuery(parent_row).find('.button-type').val();

        if (button == 'icon') {
            jQuery(parent_row).find('.icon-preview').clone().appendTo(preview);
        } else {
           jQuery(parent_row).find('.text-preview').clone().appendTo(preview);
        }
    });

    if (button_count == 0) {
        jQuery(preview).html('No options selected.');
    }
}

function update_services() {
    jQuery('.ui-sortable > tr').each(function() {
        var image_button = jQuery(this).find('.image-picker');

        if (jQuery(this).find('.button-type').val() == 'icon'
            && jQuery(this).find('.icon-id').val() != '') {
            jQuery(image_button).addClass('remove-icon');
            jQuery(image_button).html('Remove');
        } else {
            jQuery(image_button).removeClass('remove-icon');
            jQuery(image_button).html('Select');
        }
    });
}