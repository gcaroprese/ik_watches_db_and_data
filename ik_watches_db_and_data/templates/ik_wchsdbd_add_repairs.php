<?php
/*

Template: IK Watches DB Data - Add Repairs Template
Author: Gabriel Caroprese / Inforket.com
Update Date: 25/06/2021

*/

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

//I run datepicker    
wp_enqueue_script('jquery-ui-datepicker');
wp_register_style('jquery-ui', IK_WCHSDBD_PLUGIN_PUBLIC.'/css/date-picker.css
');
wp_enqueue_style('jquery-ui');

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
    	
    	if (isset($repair_data)){
        	global $wpdb;
        
        	$table = $wpdb->prefix.'ik_watchrepairs';
        	$rowResult = $wpdb->insert($table,  $repair_data , $format = NULL);
            
            $result = '
            <div id="ik_wchsdbd_popup_show_repairadded">
                <div class="ik_wchsdbd_popup_close">
                    <span class="dashicons dashicons-dismiss"></span>
                </div>
                <div class="ik_wchsdbd_popup_content">
                    <p>Repair ID #'.$wpdb->insert_id.' created.</p>
                </div>
            </div>';
            
    	} else{
            $result = '
            <div id="ik_wchsdbd_popup_show_repairadded">
                <div class="ik_wchsdbd_popup_close">
                    <span class="dashicons dashicons-dismiss"></span>
                </div>
                <div class="ik_wchsdbd_popup_content">
                    <p>Error. Repair not created.</p>
                </div>
            </div>';
    	}
    
} else {
    $result = '';
}

//Get Today date and repair index ID
$watches_repair = new Ik_Watches_Repairs();
$new_repair_id = $watches_repair->get_last_index() + 1;
$dateToday = date('d/m/Y');

?>

<style>
#ik_repairs_form input[type=text]{
    max-width: 470px;
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
.hasdatepicker{
    text-align: center;
}
#ik_repairs_module{
    width:100%;
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
</style>
<div id ="ik_repairs_module">
	<h1>Add New Repair</h1>
	<a class="button" href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=ik_wchsdbd_panel_repairs">Go To Repairs</a>
    <?php
    //I show result if repair added
    echo $result; ?>
    <div id="ik_repairs_form">
        <form method="post" action="" enctype="multipart/form-data" id="ik_repairForm">
            <div style="width: 600px; margin-left: auto; margin-right: auto;">
                <div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
                    <ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all" role="tablist">
                      <li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active" role="tab" tabindex="0" aria-controls="tabs-1" aria-labelledby="ui-id-1" aria-selected="true"><a href="#tabs-1" class="ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-1">Customer Details</a></li>
                      <li id="watch-tab" class="ui-state-default ui-corner-top" role="tab" tabindex="-1" aria-controls="tabs-2" aria-labelledby="ui-id-2" aria-selected="false"><a href="#tabs-2" class="ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-2">Watch Details</a></li>
                      <li id="jewellery-tab" style="display: none" class="ui-state-default ui-corner-top" role="tab" tabindex="-1" aria-controls="tabs-3" aria-labelledby="ui-id-3" aria-selected="false"><a href="#tabs-3" class="ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-3">Jewellery Details</a></li>
                      <li class="ui-state-default ui-corner-top" role="tab" tabindex="-1" aria-controls="tabs-4" aria-labelledby="ui-id-4" aria-selected="false"><a href="#tabs-4" class="ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-4">Repair Details</a></li>
                      <li class="ui-state-default ui-corner-top" role="tab" tabindex="-1" aria-controls="tabs-5" aria-labelledby="ui-id-5" aria-selected="false"><a href="#tabs-5" class="ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-5">Billing Details</a></li>
                    </ul>
                    <div id="tabs-1" aria-labelledby="ui-id-1" class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="tabpanel" aria-expanded="true" aria-hidden="false" style="display: block;">
                      <table border="0" cellspacing="1" cellpadding="2" align="center">
                        <tbody><tr>
                          <td align="right"><b>Repair No.:</b></td>
                          <td align="left"><?php echo $new_repair_id; ?></td>
                        </tr>
                        <tr>
                          <td align="right"><b>Repair Type:</b></td>
                          <td align="left"><label>
                            <input type="radio" name="RepairType" value="Watch" checked="">
                            Watch</label>
                            &nbsp;&nbsp;&nbsp;
                            <label>
                            <input name="RepairType" type="radio" value="Jewellery">
                            Jewellery</label></td>
                        </tr>
                        <tr>
                          <td align="right"><b>Date In:</b></td>
                          <td align="left"><input name="DateIn" type="text" id="DateIn" size="12" maxlength="10" value="<?php echo $dateToday; ?>" class="hasdatepicker"></td>
                        </tr>
                        <tr>
                          <td align="right"><b>First Name:</b></td>
                          <td align="left"><input name="FirstName" type="text" id="FirstName" size="50" maxlength="50" autofocus="" value="">
                          </td>
                        </tr>
                        <tr>
                          <td align="right"><b>Surname:</b></td>
                          <td align="left"><input name="Surname" type="text" id="Surname" size="50" maxlength="50" value="">
                          </td>
                        </tr>
                        <tr>
                          <td align="right"><b>Address:</b></td>
                          <td align="left"><textarea name="Address" id="Address" rows="5" cols="50"></textarea></td>
                        </tr>
                        <tr>
                          <td align="right"><b>Phone:</b></td>
                          <td align="left"><input name="Phone" type="text" id="Phone" size="50" maxlength="20" value="">
                          </td>
                        </tr>
                        <tr>
                          <td align="right"><b>Mobile:</b></td>
                          <td align="left"><input name="Mobile" type="text" id="Mobile" size="50" maxlength="20" value="">
                          </td>
                        </tr>
                        <tr>
                          <td align="right"><b>Email:</b></td>
                          <td align="left"><input name="Email" type="text" id="Email" size="50" maxlength="50" value="">
                          </td>
                        </tr>
                      </tbody>
                      </table>
                    </div>
                    <div id="tabs-2" aria-labelledby="ui-id-2" class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="tabpanel" aria-expanded="false" aria-hidden="true" style="display: none;">
                      <table border="0" cellspacing="1" cellpadding="2" align="center">
                        <tbody><tr>
                          <td align="right"><b>Brand:</b></td>
                          <td align="left"><span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span><input name="Brand" type="text" id="Brand" size="50" maxlength="30" value="" class="ui-autocomplete-input" autocomplete="off">
                          </td>
                        </tr>
                        <tr>
                          <td align="right"><b>Range:</b></td>
                          <td align="left"><span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span><input name="Series" type="text" id="Series" size="50" maxlength="100" value="" class="ui-autocomplete-input" autocomplete="off">
                          </td>
                        </tr>
                        <tr>
                          <td align="right"><b>Model:</b></td>
                          <td align="left"><input name="Model" type="text" id="Model" size="50" maxlength="100" value="">
                          </td>
                        </tr>
                        <tr>
                          <td align="right"><b>Serial No.:</b></td>
                          <td align="left"><input name="SerialNo" type="text" id="SerialNo" size="50" maxlength="20" value="">
                          </td>
                        </tr>
                        <tr>
                          <td align="right"><b>Description:</b></td>
                          <td align="left"><input name="WatchDescription" type="text" id="WatchDescription" size="50" maxlength="255" value="">
                          </td>
                        </tr>
                      </tbody>
                      </table>
                    </div>
                    <div id="tabs-3" aria-labelledby="ui-id-3" class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="tabpanel" aria-expanded="false" aria-hidden="true" style="display: none;">
                      <table border="0" cellspacing="1" cellpadding="2" align="center">
                        <tbody><tr>
                          <td align="right"><b>Item:</b></td>
                          <td align="left"><input name="JewelleryItem" type="text" id="JewelleryItem" size="50" maxlength="255" value="">
                          </td>
                        </tr>
                        <tr>
                          <td align="right"><b>Metal:</b></td>
                          <td align="left"><input name="JewelleryMetal" type="text" id="JewelleryMetal" size="50" maxlength="255" value="">
                          </td>
                        </tr>
                        <tr>
                          <td align="right"><b>Gemstones:</b></td>
                          <td align="left"><input name="JewelleryGemstones" type="text" id="JewelleryGemstones" size="50" maxlength="255" value="">
                          </td>
                        </tr>
                        <tr>
                          <td align="right"><b>Description:</b></td>
                          <td align="left"><input name="JewelleryDescription" type="text" id="JewelleryDescription" size="50" maxlength="255" value="">
                          </td>
                        </tr>
                      </tbody></table>
                    </div>
                    <div id="tabs-4" aria-labelledby="ui-id-4" class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="tabpanel" aria-expanded="false" aria-hidden="true" style="display: none;">
                      <table border="0" cellspacing="1" cellpadding="2" align="center">
                        <tbody><tr>
                          <td align="right"><b>Problem:</b></td>
                          <td align="left"><textarea name="Problem" id="Problem" rows="5" cols="50"></textarea></td>
                        </tr>
                        <tr>
                          <td align="right"><b>Repairer:</b></td>
                          <td align="left"><input name="Repairer" type="text" id="Repairer" size="50" maxlength="50" value="">
                          </td>
                        </tr>
                        <tr>
                          <td align="right"><b>Cost Quote:</b></td>
                          <td align="left"><input class="checkprice_format" name="CostQuote" type="text" id="CostQuote" size="10" maxlength="10" value="0.00">
                          </td>
                        </tr>
                        <tr>
                          <td align="right"><b>Retail Quote:</b></td>
                          <td align="left"><input class="checkprice_format" name="RetailQuote" type="text" id="RetailQuote" size="10" maxlength="10" value="0.00">
                          </td>
                        </tr>
                        <tr>
                          <td align="right"><b>Go Ahead Date:</b></td>
                          <td align="left"><input name="GoAheadDate" type="text" id="GoAheadDate" size="12" maxlength="10" value="" class="hasdatepicker"></td>
                        </tr>
                        <tr>
                          <td align="right"><b>Work Notes:</b></td>
                          <td align="left"><textarea name="WorkNotes" id="WorkNotes" rows="5" cols="50"></textarea></td>
                        </tr>
                      </tbody></table>
                    </div>
                    <div id="tabs-5" aria-labelledby="ui-id-5" class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="tabpanel" aria-expanded="false" aria-hidden="true" style="display: none;">
                      <table border="0" cellspacing="1" cellpadding="2" align="center">
                        <tbody><tr>
                          <td align="right"><b>Invoice No.:</b></td>
                          <td align="left"><input name="InvoiceNo" type="text" id="InvoiceNo" size="12" maxlength="10" value="">
                          </td>
                        </tr>
                        <tr>
                          <td align="right"><b>Completion Date:</b></td>
                          <td align="left"><input name="CompletionDate" type="text" id="CompletionDate" size="12" maxlength="10" value="" class="hasdatepicker"></td>
                        </tr>
                        <tr>
                          <td align="right"><b>Collect Date:</b></td>
                          <td align="left"><input name="CollectDate" type="text" id="CollectDate" size="12" maxlength="10" value="" class="hasdatepicker"></td>
                        </tr>
                        <tr>
                          <td align="right"><b>Notes:<br>
                          (not seen<br>
                          by client)</b></td>
                          <td align="left"><textarea name="Notes" id="Notes" rows="5" cols="50"></textarea></td>
                        </tr>
                      </tbody>
                      </table>
                    </div>
                </div>
                <p align="center">
                <button class="ik_submit_repair button-primary">Add Repair »</button>
                </p>
            </div>
        </form>
    </div>
</div>
<script language="JavaScript" type="text/JavaScript">
jQuery(document).ready(function(){
    jQuery('.ui-tabs-nav').on('click', 'li', function() {
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
setTimeout(function(){ 
    jQuery( ".hasdatepicker" ).datepicker({ dateFormat: "dd-mm-yy" });
}, 1500);
</script>
<script>
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
jQuery('#ik_wchsdbd_popup_show_repairadded').on('click', '.ik_wchsdbd_popup_close', function(){
    jQuery('#ik_wchsdbd_popup_show_repairadded').fadeOut(600);
});
jQuery('#ik_repairs_module input[name="RepairType"]').change(function() {
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
</script>