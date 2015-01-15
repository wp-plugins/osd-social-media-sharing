document.onready = function() {
    build_preview();
    update_services();

    jQuery('.image-picker').click(function() {
        if (jQuery(this).hasClass('remove-icon')) {
            var preview_parent = jQuery(this).parent().prev();
            var platform = jQuery(preview_parent).find('.platform').val();
            jQuery(preview_parent).find('.icon-preview').html('<img src="'+path+'icons.svg#'+platform+'">');
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

    jQuery('.button-type').change(function() {
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

    jQuery('.enable-service').change(function() {
        build_preview();
        update_services();
    });

    jQuery(".ui-sortable").sortable({
        update: function() {
            update_order();
        },
        distance: 20
    });
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