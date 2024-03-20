jQuery(document).ready( function($) {
    jQuery('input#ik_wchsdbd_media_manager').click(function(e) {
        e.preventDefault();
        var image_frame;
        if(image_frame){
            image_frame.open();
        }
        // Define image_frame as wp.media object
        image_frame = wp.media({
            title: 'Media',
            multiple : false,
            library : {
                type : 'image',
            }
       });

       image_frame.on('close',function() {
            // On close, get selections and save to the hidden input
            // plus other AJAX stuff to refresh the image preview
            var selection =  image_frame.state().get('selection');
            var gallery_ids = new Array();
            var my_index = 0;
            selection.each(function(attachment) {
                gallery_ids[my_index] = attachment['id'];
                my_index++;
            });
            
            var ids = gallery_ids.join(",");
            jQuery('input#ik_wchsdbd_image_id').val(ids);
            Refresh_Image(ids);
        });

        image_frame.on('open',function() {
            // On open, get the id from the hidden input
            // and select the appropiate images in the media manager
            var selection =  image_frame.state().get('selection');
            var ids = jQuery('input#ik_wchsdbd_image_id').val().split(',');
            ids.forEach(function(id) {
                var attachment = wp.media.attachment(id);
                attachment.fetch();
                selection.add( attachment ? [ attachment ] : [] );
            });
        });
        
        image_frame.open();
     });

});

//Function to delete image
jQuery( "body" ).on( "click", "#ik_wchsdbd_delete_img", function() {
    jQuery(this).remove();
    jQuery('#ik_wchsdbd_preview_image').fadeOut(500);
    jQuery('#ik_wchsdbd_preview_image').attr('src', '' );       
    jQuery('#ik_wchsdbd_logo_url').val('');
    jQuery('#ik_wchsdbd_image_id').val('');

});


// Ajax request to refresh the image preview
function Refresh_Image(the_id){
    var data = {
        action: 'ik_wchsdbd_uploadmedia',
        id: the_id
    };

    jQuery.get(ajaxurl, data, function(response) {
        if(response.success === true) {
            jQuery('#ik_wchsdbd_preview_image').attr('src', response.data );
            jQuery('#ik_wchsdbd_preview_image').fadeIn(600);
            jQuery('#ik_wchsdbd_logo_url').val(response.data );
            
            //I add button to delete image
            if (jQuery('#ik_wchsdbd_delete_img').attr('type') != 'button'){
                jQuery('<input type="button" class="button-primary ik_wchsdbd_button_red" value="Delete" id="ik_wchsdbd_delete_img">').insertAfter    ('#ik_wchsdbd_media_manager');
            }
        }
    });
}