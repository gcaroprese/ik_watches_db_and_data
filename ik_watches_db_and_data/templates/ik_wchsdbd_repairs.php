<?php
/*

Template: IK Watches DB Data - Repairs Template
Author: Gabriel Caroprese / Inforket.com
Update Date: 28/10/2021

*/

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $values_to_process = array(
        'RepairType' => 'text',
        'DateIn' => 'date', 
        'FirstName' => 'text', 
        'Surname' => 'text', 
        'Address' => 'textarea', 
        'Phone' => 'text', 
        'Mobile' => 'text', 
        'Email' => 'text', 
        'Brand' => 'text', 
        'JewelleryItem' => 'text',
        'JewelleryMetal' => 'text',
        'JewelleryGemstones' => 'text',
        'JewelleryDescription' => 'text',
        'Series' => 'text', 
        'Model' => 'text', 
        'SerialNo' => 'text', 
        'WatchDescription' => 'text', 
        'Problem' => 'textarea', 
        'Repairer' => 'text', 
        'CostQuote' => 'price', 
        'RetailQuote' => 'price', 
        'GoAheadDate' => 'date', 
        'WorkNotes' => 'textarea', 
        'InvoiceNo' => 'text', 
        'CompletionDate' => 'text', 
        'CollectDate' => 'date', 
        'CollectDate' => 'date', 
        'Notes' => 'textarea'
        );
    

    	//I process the repair submission
    	foreach ($values_to_process as $value_to_process => $value_type){
    	    
    	    if (isset($_POST[$value_to_process])){
    	        if ($_POST[$value_to_process] != ''){ 
        	        if($value_type == 'date'){
        	            $repair_data[$value_to_process] = sanitize_text_field($_POST[$value_to_process]);
                        $repair_data[$value_to_process] = substr($repair_data[$value_to_process], 6, 4).'-'.substr($repair_data[$value_to_process], 3, 2).'-'.substr($repair_data[$value_to_process], 0, 2);
        	        } else if($value_type == 'price'){
        	            $repair_data[$value_to_process] = floatval($_POST[$value_to_process]);
        	        } else if($value_type == 'textarea'){
        	            $repair_data[$value_to_process] = sanitize_textarea_field($_POST[$value_to_process]);
        	            $repair_data[$value_to_process] = str_replace('\\', '', $repair_data[$value_to_process]);
        	        } else {
        	            $repair_data[$value_to_process] = sanitize_text_field($_POST[$value_to_process]);
        	            $repair_data[$value_to_process] = str_replace('\\', '', $repair_data[$value_to_process]);
        	        }
    	        }
    	        
    	    }
    	}
    	
    	if (isset($repair_data) && isset($_POST['repair_update_id'])){
    	    $repair_id = absint($_POST['repair_update_id']);
    	    if ($repair_id > 0){
                global $wpdb;
                $tableupdate = $wpdb->prefix.'ik_watchrepairs';
                $where = [ 'RepairNo' => $repair_id ];
                $rowResult = $wpdb->update($tableupdate,  $repair_data , $where);  
    	    }
    	}
        if (isset($rowResult)){  
        ?>
            <div id="ik_wchsdbd_popup_show_repairadded">
                <div class="ik_wchsdbd_popup_close">
                    <span class="dashicons dashicons-dismiss"></span>
                </div>
                <div class="ik_wchsdbd_popup_content">
                    <p>Repair ID #<?php echo $repair_id; ?> updated.</p>
                </div>
            </div>
        <?php
            
    	} else{
    	    ?>
            <div id="ik_wchsdbd_popup_show_repairadded">
                <div class="ik_wchsdbd_popup_close">
                    <span class="dashicons dashicons-dismiss"></span>
                </div>
                <div class="ik_wchsdbd_popup_content">
                    <p>Error. Repair not updated.</p>
                </div>
            </div>
            <?php
    	}
    
}

//Get Today date and repair index ID
$watches_repair = new Ik_Watches_Repairs();

if ($watches_repair->print_data_by_ID() == false){
    
    $qtListing = 100;
    
    //Get paging data
    if (isset($_GET["listing"])){
        // I check if value is integer to avoid errors
        if (strval($_GET["listing"]) == strval(intval($_GET["listing"])) && $_GET["listing"] > 0){
            $paging = intval($_GET["listing"]);
        } else {
            $paging = 1;
        }
    } else {
         $paging = 1;
    }
    
    //I check if there's a filter
    if (isset($_GET['filter'])){
        $queryFilter = sanitize_text_field($_GET['filter']);
    } else {
        $queryFilter = '';
    }

    //I run datepicker    
    wp_enqueue_script('jquery-ui-datepicker');
    wp_register_style('jquery-ui', IK_WCHSDBD_PLUGIN_PUBLIC.'/css/date-picker.css
');
    wp_enqueue_style('jquery-ui');
    
    ?>
    <style>
    #ik_wchsdbd_import_existing_repairs {
        width: 100%;
    }
    #ik_wchsdbd_import_existing_repairs .ik_wchsdbd_repairs_list_content{
        display: grid;
    }
    #ik_wchsdbd_import_existing_repairs table{
        max-width: 1200px;
        margin: 0 auto;
    }
    #ik_wchsdbd_import_existing_repairs tbody tr {
        background-color: #f6f7f7;
    }
    #ik_wchsdbd_import_existing_repairs p.search-box {
        padding: 3% 5%;
        margin: 0;
        position: unset;
        width: 100%;
    }
    #ik_wchsdbd_import_existing_repairs .search-box select{
        position: relative;
        top: -2px;
        height: 30.5px;
    }
    #ik_wchsdbd_search_repairs{
        position: relative;
        left: -3px;
    }
    #ik_wchsdbd_search_repairs, #ik_wchsdbd_import_existing_repairs .search-box select, #ik_wchsdbd_showall_repairs{
        margin-right: 12px;
    }
    #ik_wchsdbd_import_existing_repairs tr td:first-child {
        border-left-width: 0px;
    }
    #ik_wchsdbd_import_existing_repairs th, #ik_wchsdbd_import_existing_repairs td {
        padding: 11px! important;
        text-align: center;
    }
    #ik_wchsdbd_import_existing_repairs .sorting-indicator{
        float: right;
    }
    #ik_wchsdbd_import_existing_repairs tr td {
        border-left: 1px solid #fff;
    }
    #ik_wchsdbd_import_existing_repairs tbody tr:nth-child(2n+1) {
        background: #eee;
    }
    #ik_wchsdbd_import_existing_repairs tr td {
        border-left: 1px solid #fff;
    }
    #ik_wchsdbd_import_existing_repairs tbody tr:nth-child(2n+2) {
        background: #ddd;
    }
    #ik_wchsdbd_import_existing_repairs thead tr, #ik_wchsdbd_import_existing_repairs tfoot tr {
        background: #fff;
    }
    #ik_wchsdbd_import_existing_repairs p.search-box {
        padding-right: 3px;
        padding-bottom: 28px;
        float: right;
        margin: 0;
    }
    #ik_wchsdbd_import_existing_repairs th.sorted a{
        text-decoration: none;
        color: #000! important;
        box-shadow: none;
    }
    #ik_wchsdbd_import_existing_repairs th.datein{
        width: 70px;
    }
    #ik_wchsdbd_import_existing_repairs th.repairno {
        width: 130px;
    }
    #ik_wchsdbd_import_existing_repairs th.datacompleted, #ik_wchsdbd_import_existing_repairs th.datacollected{
        width: 120px;
    }
    #ik_wchsdbd_import_existing_repairs .ik_wchsdbd_iddata_buttons button{
        width: 100%;
        margin-bottom: 2px;
    }
    .ik_wchsdbd_pages {
        display: table;
        margin: 20px auto;
        text-align: center;
    }
    .ik_wchsdbd_pages a {
        padding: 5px 12px;
        margin: 3px;
        text-decoration: none;
        background: #fff;
        font-size: 15px;
        float: left;
    }
    a.now_page {
        background: #2271b1! important;
        color: #fff;
    }
    #ik_repairForm{
        margin-top: 20px;
    }
    #ik_repairs_module{
        width:100%;
    }
    .ik_wchsdbd_iddata_buttons_actions a {
        display: table-cell;
        padding: 3px;
        outline: none;
    }
    .ik_wchsdbd_iddata_buttons_actions a:before {
        background: #2271b1;
        color: #fff;
        padding: 5px;
        font-size: 26px;
        border-radius: 12px;
    }
    #ik_wchsdbd_popup_show_details{
        position: fixed;
        min-width: 290px;
        background: #fff;
        left: 55%;
        transform: translate(-50%, -50%);
        display: block;
        padding: 1% 3%;
        z-index: 99999;
        top: 370px! important;
        min-height: 300px;
        border: 2px solid #2271b1;
        border-radius: 7px;
    }
    .ik_wchsdbd_popup_close{
        position: absolute;
        top: -18px;
        right: -22px;
        z-index: 999999999999;
        display: block;
        cursor: pointer;
    }
    .ik_wchsdbd_popup_close span {
        width: 45px;
        height: 45px;
    }
    .ik_wchsdbd_popup_close span:before {
        font-size: 40px;
        background: #fff;
        padding: 0.5px;
        border-radius: 25px;
        line-height: 1;
        border: 1px solid #2271b1;
    }
    #ik_wchsdbd_popup_show_details input[type=text], #ik_wchsdbd_popup_show_details textarea{
        max-width: 90%;
    }
    #ik_wchsdbd_loading {
        display: none;
        position: fixed;
        top: 200px;
        margin: 0 auto;
        left: 50%;
        text-align: center;
        z-index: 99999999999999;
    }
    #ik_wchsdbd_loading img{
        width: 150px;
    }
    #ik_wchsdbd_popup_show_repairadded{
        position: fixed;
        min-width: 290px;
        background: #fff;
        left: 55%;
        transform: translate(-50%, -50%);
        display: block;
        padding: 3%;
        z-index: 99999;
        text-align: center;
        top: 350px! important;
        border: 2px solid #2271b1;
        border-radius: 7px;
    }
    #ik_wchsdbd_popup_show_repairadded .ik_wchsdbd_popup_content p{
        font-size: 20px;
        margin-top: 20px;
    }
    #ik_wchsdbd_popup_show_details .ik_wchsdbd_emailsent{
        font-size: 20px;
    }
    </style>
    <div id="ik_wchsdbd_import_existing_repairs">
    	<h1>Repairs</h1>
    	<a class="button" href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=ik_wchsdbd_panel_add_repairs">Add New Repair</a>
        <?php
    
        // Order details
        if (isset($_GET["ordendir"])){
            $orderdir = sanitize_text_field($_GET["ordendir"]);
    
        
            if ($orderdir == 'asc'){
                $orderdir = 'ASC';
            } else {
                $orderdir = 'DESC';
            }
        } else {
            $orderdir = 'DESC';
        }
        
        
        	//Listing of repairs
        	$repairs = $watches_repair->get_repair_list($orderdir, '', $queryFilter);
        	if ($repairs != false){
        	    $listing_repairs_all = $watches_repair->get_repairs_count($queryFilter);
        	    $total_pages = intval($listing_repairs_all / $qtListing) + 1;
        		echo $repairs;
    
            	if ($listing_repairs_all > $qtListing && $paging <= $total_pages){
                    echo '<div class="ik_wchsdbd_pages">';
                    for ($i = 1; $i <= $total_pages; $i++) {
                        if ($paging == $i){
                            $selectedPageN = 'class="now_page"';
                        } else {
                            $selectedPageN = "";
                        }
                        echo '<a '.$selectedPageN.' href="'.get_site_url().'/wp-admin/admin.php?page=ik_wchsdbd_panel_repairs&listing='.$i.'">'.$i.'</a>';
                        
                    }
                    echo '</div>';
            	}
        	}
        ?>
    </div>
    <div id="ik_wchsdbd_popup_show_details" style="display: none">
        <div class="ik_wchsdbd_popup_close">
            <span class="dashicons dashicons-dismiss"></span>
        </div>
        <div class="ik_wchsdbd_popup_content">
        </div>
    </div>
    <div id="ik_wchsdbd_loading">
        <img src="<?php echo IK_WCHSDBD_PLUGIN_PUBLIC; ?>/img/loading.gif" alt="loading">
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://code.jquery.com/ui/1.13.0/jquery-ui.js"></script>
    <script>
        jQuery("#ik_wchsdbd_import_existing_repairs th .select_all").on( "click", function() {
            if (jQuery(this).attr('selecteddata') != 'si'){
                jQuery('#ik_wchsdbd_import_existing_repairs th .select_all').prop('checked', true);
                jQuery('#ik_wchsdbd_import_existing_repairs th .select_all').attr('checked', 'checked');
                jQuery('#ik_wchsdbd_import_existing_repairs tbody tr').each(function() {
                    jQuery(this).find('.select_dato').prop('checked', true);
                    jQuery(this).find('.select_dato').attr('checked', 'checked');
                });        
                jQuery(this).attr('selecteddata', 'si');
            } else {
                jQuery('#ik_wchsdbd_import_existing_repairs th .select_all').prop('checked', false);
                jQuery('#ik_wchsdbd_import_existing_repairs th .select_all').removeAttr('checked');
                jQuery('#ik_wchsdbd_import_existing_repairs tbody tr').each(function() {
                    jQuery(this).find('.select_dato').prop('checked', false);
                    jQuery(this).find('.select_dato').removeAttr('checked');
                });   
                jQuery(this).attr('selecteddata', 'no');
                
            }
        });
        jQuery("#ik_wchsdbd_import_existing_repairs .ik_wchsdbd_button_delete_selected").on( "click", function() {
            var confirmar = confirm('Confirm delete repair records?');
            if (confirmar == true) {
                jQuery('#ik_wchsdbd_import_existing_repairs tbody tr').each(function() {
                    if (jQuery(this).find('.select_dato').prop('checked') == true){
                        
                        var ik_wchsdbd_repairs_tr = jQuery(this);
                        var iddato = ik_wchsdbd_repairs_tr.attr('iddato');
                        
                        var data = {
            				action: "ik_wchsdbd_ajax_delete_repair",
            				"post_type": "post",
            				"iddato": iddato,
            			};  
            
                		jQuery.post( ajaxurl, data, function(response) {
                			if (response){
                                ik_wchsdbd_repairs_tr.fadeOut(700);
                                ik_wchsdbd_repairs_tr.remove();
                		    }        
                        });
                    }
                });
            }
            jQuery('#ik_wchsdbd_import_existing_repairs th .select_all').attr('selecteddata', 'no');
            jQuery('#ik_wchsdbd_import_existing_repairs .select_dato').prop('checked', false);
            jQuery('#ik_wchsdbd_import_existing_repairs th .select_all').prop('checked', false);
            jQuery('#ik_wchsdbd_import_existing_repairs th .select_all').removeAttr('checked');
            return false;
        });    
        
        jQuery('#ik_wchsdbd_import_existing_repairs').on('click','td .ik_wchsdbd_button_delete_repair', function(e){
            e.preventDefault();
            var confirmar =confirm('Confirm delete repair selected?');
            if (confirmar == true) {
                var iddato = jQuery(this).parent().attr('iddato');
                var ik_wchsdbd_repairs_tr = jQuery('#ik_wchsdbd_import_existing_repairs tbody').find('tr[iddato='+iddato+']');
                
                var data = {
        			action: "ik_wchsdbd_ajax_delete_repair",
        			"post_type": "post",
        			"iddato": iddato,
        		};  
        
        		jQuery.post( ajaxurl, data, function(response) {
        			if (response){
                        ik_wchsdbd_repairs_tr.fadeOut(700);
                        ik_wchsdbd_repairs_tr.remove();
                        jQuery('#ik_oracionf_edicion_dinamica_dato').remove();
        		    }        
                });
            }
        });
    
        jQuery('#ik_wchsdbd_import_existing_repairs').on('click','td .ik_wchsdbd_button_more_details', function(e){
            e.preventDefault();
            jQuery('#ik_wchsdbd_popup_show_details .ik_wchsdbd_popup_content').empty();
            
            var iddato = jQuery(this).parent().attr('iddato');
            
            jQuery('#ik_wchsdbd_loading').fadeIn(500);
            
            var data = {
    			action: "ik_wchsdbd_ajax_repair_by_id",
    			"post_type": "post",
    			"iddato": iddato,
    		};  
    
    		jQuery.post( ajaxurl, data, function(response) {
    			if (response){
    			    var data = JSON.parse(response);
    			    jQuery('#ik_wchsdbd_loading').attr('style', 'display: none');
    			    jQuery('#ik_wchsdbd_popup_show_details .ik_wchsdbd_popup_content').empty();
                    jQuery('#ik_wchsdbd_popup_show_details .ik_wchsdbd_popup_content').append(data);
                    jQuery('#ik_wchsdbd_popup_show_details').fadeIn(600);
                    var RepairType = jQuery('#ik_wchsdbd_popup_show_details input[name=RepairType]:checked').val();
                    if (RepairType == "Watch") {
                      jQuery("#jewellery-tab").attr("style", "display: none");
                      jQuery("#watch-tab").attr("style", "display: list-item");
                    }
                    if (RepairType == "Jewellery") {
                      jQuery("#watch-tab").attr("style", "display: none");
                      jQuery("#jewellery-tab").attr("style", "display: list-item");
                    }
    		    }      
            });
            
        });
        
        jQuery('#ik_wchsdbd_popup_show_details').on('click', '.ik_wchsdbd_popup_close', function(){
            jQuery('#ik_wchsdbd_popup_show_details').fadeOut(600);
        });
        
        
        jQuery('#ik_wchsdbd_import_existing_repairs .ik_wchsdbd_email').on('click', function(e){
            e.preventDefault();
            jQuery('#ik_wchsdbd_popup_show_details .ik_wchsdbd_popup_content').empty();
            
            var iddato = jQuery(this).parent().attr('iddato');
            
            jQuery('#ik_wchsdbd_loading').fadeIn(500);
            
            var data = {
    			action: "ik_wchsdbd_ajax_send_repair_email",
    			"post_type": "post",
    			"iddato": iddato,
    		};  
    
    		jQuery.post( ajaxurl, data, function(response) {
    			if (response){
    			    var data = JSON.parse(response);
    			    jQuery('#ik_wchsdbd_popup_show_details .ik_wchsdbd_popup_content').empty();
    			    jQuery('#ik_wchsdbd_loading').attr('style', 'display: none');
    			    jQuery('#ik_wchsdbd_popup_show_details').attr('style', 'min-height: 70px! important;');
        			jQuery('#ik_wchsdbd_popup_show_details .ik_wchsdbd_popup_content').append('<p class="ik_wchsdbd_emailsent">'+data+'</p>');
        		    jQuery('#ik_wchsdbd_popup_show_details').fadeIn(600);
    		    }
            });
            return false;
        });  
        
        
        jQuery('#ik_wchsdbd_import_existing_repairs').on('click', '#ik_wchsdbd_search_repairs', function(){
            var searchterm = jQuery('#ik_wchsdbd_import_existing_repairs #tag-search-input').val();
            jQuery('#ik_wchsdbd_loading').fadeIn(500);
           
            var data = {
    			action: "ik_wchsdbd_ajax_search_repair",
    			"post_type": "post",
    			"searchterm": searchterm,
    		};  
    
    		jQuery.post( ajaxurl, data, function(response) {
    			if (response){
    			    var data = JSON.parse(response);
    			    jQuery('#ik_wchsdbd_loading').attr('style', 'display: none');
    			    jQuery('#ik_wchsdbd_import_existing_repairs .ik_wchsdbd_repairs_list_content').fadeOut(500);
    			    jQuery('#ik_wchsdbd_import_existing_repairs .ik_wchsdbd_pages').fadeOut(500);
    			    jQuery('#ik_wchsdbd_import_existing_repairs .ik_wchsdbd_repairs_list_content').empty();
    			    jQuery('#ik_wchsdbd_import_existing_repairs .ik_wchsdbd_pages').empty();
        			jQuery('#ik_wchsdbd_import_existing_repairs').append(data);
    			    jQuery('#ik_wchsdbd_import_existing_repairs .sorting-indicator').remove();
    			    jQuery('#ik_wchsdbd_import_existing_repairs .sorted').attr('style', 'pointer-events: none;');
    		    }        
            });
        });
        
        jQuery('#ik_wchsdbd_import_existing_repairs').on('click', '#ik_wchsdbd_showall_repairs', function(){
           window.location.href = '<?php echo get_site_url(); ?>/wp-admin/admin.php?page=ik_wchsdbd_panel_repairs'; 
        });

        jQuery('#ik_wchsdbd_popup_show_repairadded').on('click', '.ik_wchsdbd_popup_close', function(){
            jQuery('#ik_wchsdbd_popup_show_repairadded').fadeOut(600);
        });
        
        jQuery('#ik_wchsdbd_popup_show_details').on('change', 'input[name=RepairType]', function() {
            var RepairType = jQuery(this).val();
            if (RepairType == "Watch") {
              jQuery("#jewellery-tab").attr("style", "display: none");
              jQuery("#watch-tab").attr("style", "display: list-item");
            }
            if (RepairType == "Jewellery") {
              jQuery("#watch-tab").attr("style", "display: none");
              jQuery("#jewellery-tab").attr("style", "display: list-item");
            }
        });
    
        jQuery('body').on('blur', '.checkprice_format', function(){
            var price = jQuery(this).val();
            var price =  price.replace(/[^\d.-]/g, '');
            if (price.length < 1){
                var price = '0.00';
            }
            if (!price.includes('.')){
                var price = price+'.00';
            }
            if (price.slice(-1) == '.'){
                var price = price+'00';
            }
            jQuery(this).val(price);
        });
    
        jQuery(document).ready(function(){
            jQuery('#ik_wchsdbd_popup_show_details').on('click', '.ui-tabs-nav li', function() {
                jQuery('.ui-tabs-panel').attr('style', 'display: none');
                jQuery('.ui-tabs-nav li').attr('aria-selected', false);
                jQuery('.ui-tabs-active').removeClass('ui-tabs-active');
                jQuery('.ui-state-active').removeClass('ui-state-active');
                jQuery(this).addClass('ui-tabs-active');
                jQuery(this).addClass('ui-state-active');
                jQuery(this).attr('aria-selected', true);
                var tabID = jQuery(this).attr('aria-controls');
                jQuery('#'+tabID).attr('style', 'display: block');
                
                return false;
                
            });
        });
    </script>
    <script>
    <?php
    if ($queryFilter != '' && $queryFilter != 'all'){
        ?>
        jQuery('#ik-filter-repairs option[data=<?php echo $queryFilter; ?>]').prop('selected', true);
        <?php
    }
    ?>
    </script>
<?php
} else {
    echo $watches_repair->print_data_by_ID();
}