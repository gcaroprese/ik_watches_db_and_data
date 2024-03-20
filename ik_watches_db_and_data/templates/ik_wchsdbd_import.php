<?php
/*
Template: IK Watches DB Data - Import Template
Author: Gabriel Caroprese / Inforket.com
Update Date: 09/10/2021
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

?>
<style>
.error, .loading-import{ display: none; }
label, input[type=submit] {
    display: block;
}
label{ margin-bottom: 20px; }
.loading-import img{
    width: 25px;
    position: relative;
    top: 7px;
    left: 7px;
}
</style>
<div id="ik_wchsdbd_import_panel">
    <div class="ik_wchsdbd_panel">
    <h1>Import from watches DB</h1>
    <form action="" method="post" enctype="multipart/form-data" autocomplete="no">
        <label>
            <input required type="checkbox" class="suretoimport" name="suretoimport" value="1" /> <span>Are you sure to import watches and jewellery from the database?</span>
        </label>  
    	<button class="button-primary" id="submitimport">Import</button><span class="loading-import"><img src="<?php echo get_site_url(); ?>/wp-content/plugins/ik_watches_db_and_data/img/loading.gif" /></span>
    </form>
    <div id="result_import"></div>
</div>
<script>
//I assign translator to order if translator was selected
jQuery(document).on('click', '#submitimport', function(){
    
    var operation = 0;
    jQuery('#result_import').empty();
    var checkbox = jQuery('.suretoimport');
    if (checkbox.prop('checked') == false){
      alert("Confirm you're OK to import.");
    } else {
        jQuery('.loading-import').fadeIn(600);
        ik_wchsdbd_import_watches(operation);
    }
    
    return false;

});    

function ik_wchsdbd_import_watches(importing){
            var data = {
            action: "ik_wchsdbd_ajax_import",
            "post_type": "post",
            "importing": importing,
        };  
    
        // The variable ajax_url should be the URL of the admin-ajax.php file
        jQuery.post( "<?php echo admin_url('admin-ajax.php'); ?>", data, function(response) {

            if (response == 0){
                jQuery('.loading-import').fadeOut(600);
                var message = '<p>Data Successfully Imported.</p>';
            } else {
                var message = "<p>Importing...<br>Don't close the window until processing completes.</p>";
                setTimeout(function(){
                    ik_wchsdbd_import_watches(response);
                }, 2000);
            }
        
            jQuery('#result_import').html(message);
            
        }, "json");
}

jQuery( document ).ajaxError(function() {
    jQuery('.loading-import').fadeOut(600);
    jQuery('#result_import').html("<p>Importation couldn't finish. Please, repeat.</p>");
});
    
</script>