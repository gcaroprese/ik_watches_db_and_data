<?php

/*
Template: Ajax function IK_Watches
Author: Gabriel Caroprese / Inforket.com
Update Date: 15/10/2021
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// Ajax to import watches
add_action( 'wp_ajax_ik_wchsdbd_ajax_import', 'ik_wchsdbd_ajax_import');
function ik_wchsdbd_ajax_import(){
    if (isset($_POST['importing'])){
        
        $operation = absint($_POST['importing']);
        
        if (get_option('ik_wchsdbd_last_imported') != NULL && get_option('ik_wchsdbd_last_imported') != false && $operation == 0){
            $operation = absint(get_option('ik_wchsdbd_last_imported'));
        }
        
        $newoperation = $operation + 1;
        
        $amountProcessed = 0;
        $amountLimit = 100;
        
        $wrepetead = 0;
        $wimported = 0;
        $jrepetead = 0;
        $jimported = 0;

        if ($operation != 0){
            $offset = $amountLimit*$operation;
        } else {
            $offset = 0;
        }
        
        $data_import = new Ik_Watches();
        $watches = $data_import->get_watches($offset, $amountLimit);
        $jewellery = $data_import->get_jewellery();

        
        
        if ($jewellery != false && get_option('ik_wchsdbd_jewelry_imported') != 'yes'){
            
            //I get the cat ID
            $jewelryTerm = get_term_by('name', 'Jewelry', 'product_cat');
            if ($jewelryTerm == false){
                $term_created = wp_insert_term(
                    'Jewelry',
                    'product_cat',
                    array(
                        'slug' => 'jewelry',
                    )
                );
                $jewelryTerm = $term_created['term_id'];
            }

            foreach ($jewellery as $jewell){
            
                //I make sure the plugin duplicated matching by StockNo value
                global $wpdb;
                $query_existing = "SELECT * FROM ".$wpdb->prefix."postmeta WHERE meta_key = 'stockno' AND meta_value LIKE '".$jewell['StockNo']."'";
                $existing_product = $wpdb->get_results($query_existing);
                
                
                if (isset($existing_product[0]->meta_value)){
                    //It was repeated
                    $jrepetead = $jrepetead + 1;
                } else {
                    
                    //It's not repeated. I import product
                    $jewellName = $jewell['ItemType'].' '.$jewell['Brand'];
                    if($jewell['PrimaryMetal'] != '' && $jewell['PrimaryMetal'] != ' '){
                        $jewellName .= ' of '.$jewell['PrimaryMetal'];
                    }
                    
        
                    $argsProduct = array(	   
                    	'post_author' => get_current_user_id(), 
                    	'post_content' => $jewell['Description'],
                    	'post_status' => 'Publish',
                    	'post_title' => $jewellName,
                    	'post_parent' => '',
                    	'post_type' => "product"
                    ); 
                    
                    // Create a simple WooCommerce product
                    $productID = wp_insert_post( $argsProduct );
                    
                    //I assign category
                    wp_set_object_terms($productID, $jewelryTerm->term_id, 'product_cat');

                    
                    // Setting the product type
                    wp_set_object_terms($productID, 'simple', 'product_type' );
                    
                    // Setting meta data
                    $image_id = media_sideload_image( get_site_url().'/wp-content/jewellery/tn-'.$jewell['StockNo'].'.jpg', $productID, $jewellName, 'id' );
                    
                    if (is_int($image_id)){
                        update_post_meta($productID, '_thumbnail_id', $image_id);
                    }
                    update_post_meta($productID, '_price', $jewell['SellPrice']);
                    update_post_meta($productID, '_regular_price', $jewell['SellPrice']);
                    
                    update_post_meta($productID, 'stockno' , $jewell['StockNo']);
                    update_post_meta($productID, 'itemtype' , $jewell['ItemType']);
                    update_post_meta($productID, 'brand' , $jewell['Brand']);
                    update_post_meta($productID, 'newpreowned' , $jewell['NewPreOwned']);
                    update_post_meta($productID, 'description' , $jewell['Description']);
                    update_post_meta($productID, 'primarymetal' , $jewell['PrimaryMetal']);
                    update_post_meta($productID, 'additionalmetal' , $jewell['AdditionalMetal']);
                    update_post_meta($productID, 'primarygemstonetype' , $jewell['PrimaryGemstoneType']);
                    update_post_meta($productID, 'primarygemstonecut' , $jewell['PrimaryGemstoneCut']);
                    update_post_meta($productID, 'primarygemstoneweight' , $jewell['PrimaryGemstoneWeight']);
                    update_post_meta($productID, 'primarygemstonecolour' , $jewell['PrimaryGemstoneColour']);
                    update_post_meta($productID, 'primarygemstoneclarity' , $jewell['PrimaryGemstoneClarity']);
                    update_post_meta($productID, 'additionalgemstonetype' , $jewell['AdditionalGemstoneType']);
                    update_post_meta($productID, 'additionalgemstonecut' , $jewell['AdditionalGemstoneCut']);
                    update_post_meta($productID, 'additionalgemstoneweight' , $jewell['AdditionalGemstoneWeight']);
                    update_post_meta($productID, 'additionalgemstonecolour' , $jewell['AdditionalGemstoneColour']);
                    update_post_meta($productID, 'additionalgemstoneclarity' , $jewell['AdditionalGemstoneClarity']);
                    update_post_meta($productID, 'totalitemweight' , $jewell['TotalItemWeight']);
                    update_post_meta($productID, 'certificate' , $jewell['Certificate']);
                    update_post_meta($productID, 'insurancevaluation' , $jewell['InsuranceValuation']);
                    update_post_meta($productID, 'costprice' , $jewell['CostPrice']);
                    update_post_meta($productID, 'sellprice' , $jewell['SellPrice']);
                    update_post_meta($productID, 'datein' , $jewell['DateIn']);
                    update_post_meta($productID, 'datesold' , $jewell['DateSold']);
                    update_post_meta($productID, 'onhold' , $jewell['OnHold']);
                    update_post_meta($productID, 'hide' , $jewell['Hide']);
                    update_post_meta($productID, 'jewellername' , $jewell['JewellerName']);
                    update_post_meta($productID, 'jewellerlabourhours' , $jewell['JewellerLabourHours']);
                    update_post_meta($productID, 'jewellerlabourcostperhour' , $jewell['JewellerLabourCostPerHour']);
                    update_post_meta($productID, 'jewellerprimarymetalpergramcost' , $jewell['JewellerPrimaryMetalPerGramCost']);
                    update_post_meta($productID, 'jewellerprimarymetalgrams' , $jewell['JewellerPrimaryMetalGrams']);
                    update_post_meta($productID, 'jewelleradditionalmetalpergramcost' , $jewell['JewellerAdditionalMetalPerGramCost']);
                    update_post_meta($productID, 'jewelleradditionalmetalgrams' , $jewell['JewellerAdditionalMetalGrams']);
                    update_post_meta($productID, 'jewellerprimarygemstonesupplier' , $jewell['JewellerPrimaryGemstoneSupplier']);
                    update_post_meta($productID, 'jewellerprimarygemstonestones' , $jewell['JewellerPrimaryGemstoneStones']);
                    update_post_meta($productID, 'jewellerprimarygemstoneqty' , $jewell['JewellerPrimaryGemstoneQty']);
                    update_post_meta($productID, 'jewellerprimarygemstoneweight' , $jewell['JewellerPrimaryGemstoneWeight']);
                    update_post_meta($productID, 'jewellerprimarygemstonecost' , $jewell['JewellerPrimaryGemstoneCost']);
                    update_post_meta($productID, 'jewelleradditionalgemstonesupplier' , $jewell['JewellerAdditionalGemstoneSupplier']);
                    update_post_meta($productID, 'jewelleradditionalgemstonestones' , $jewell['JewellerAdditionalGemstoneStones']);
                    update_post_meta($productID, 'jewelleradditionalgemstoneqty' , $jewell['JewellerAdditionalGemstoneQty']);
                    update_post_meta($productID, 'jewelleradditionalgemstoneweight' , $jewell['JewellerAdditionalGemstoneWeight']);
                    update_post_meta($productID, 'jewelleradditionalgemstonecost' , $jewell['JewellerAdditionalGemstoneCost']);
                    update_post_meta($productID, 'jewellergemsettingcost' , $jewell['JewellerGemSettingCost']);
                    update_post_meta($productID, 'jewellerothercosts' , $jewell['JewellerOtherCosts']);
                    update_post_meta($productID, 'sellerfirstname' , $jewell['SellerFirstName']);
                    update_post_meta($productID, 'sellersurname' , $jewell['SellerSurname']);
                    update_post_meta($productID, 'selleraddress' , $jewell['SellerAddress']);
                    update_post_meta($productID, 'sellerphone' , $jewell['SellerPhone']);
                    update_post_meta($productID, 'selleremail' , $jewell['SellerEmail']);
                    update_post_meta($productID, 'docketno' , $jewell['DocketNo']);
                    update_post_meta($productID, 'repairno' , $jewell['RepairNo']);
                    update_post_meta($productID, 'saleonbehalf' , $jewell['SaleOnBehalf']);
                    update_post_meta($productID, 'idtype' , $jewell['IDType']);
                    update_post_meta($productID, 'idreference' , $jewell['IDReference']);
                    update_post_meta($productID, 'sellernotes' , $jewell['SellerNotes']);
                    update_post_meta($productID, 'buyerfirstname' , $jewell['BuyerFirstName']);
                    update_post_meta($productID, 'buyersurname' , $jewell['BuyerSurname']);
                    update_post_meta($productID, 'buyeraddress' , $jewell['BuyerAddress']);
                    update_post_meta($productID, 'buyerphone' , $jewell['BuyerPhone']);
                    update_post_meta($productID, 'buyeremail' , $jewell['BuyerEmail']);
                    update_post_meta($productID, 'invoiceno' , $jewell['InvoiceNo']);
                    update_post_meta($productID, 'saleamount' , $jewell['SaleAmount']);
                    update_post_meta($productID, 'taxfree' , $jewell['TaxFree']);
                    update_post_meta($productID, 'datepaid' , $jewell['DatePaid']);
                    update_post_meta($productID, 'salenotes' , $jewell['SaleNotes']);
                    $jimported = $jimported + 1;
                }
                    
            }
            
            add_option('ik_wchsdbd_jewelry_imported', 'yes');
        }


        if ($watches != false){
            
            //I get the cat ID
            $watchesTerm = get_term_by('name', 'Watch', 'product_cat');
            if ($watchesTerm == false){
                $term_created = wp_insert_term(
                    'Watch',
                    'product_cat',
                    array(
                        'slug'        => 'watch',
                    )
                );
                $watchesTerm = $term_created['term_id'];
            }
            

            foreach ($watches as $watch){
                
                if ($amountProcessed < $amountLimit){
                    
                    //I make sure the plugin duplicated matching by StockNo value
                    global $wpdb;
                    $query_existing = "SELECT * FROM ".$wpdb->prefix."postmeta WHERE meta_key = 'stockno' AND meta_value LIKE '".$watch['StockNo']."'";
                    $existing_product = $wpdb->get_results($query_existing);
                    
                    if (isset($existing_product[0]->meta_value)){
                        //It was repeated
                        $wrepetead = $wrepetead + 1;
                    } else {
                        
                        //It's not repeated. I import product
            
                        $watchName = $watch['Brand'].' '.$watch['Series'];
                        if($watch['Model'] != '' && $watch['Model'] != ' '  && $watch['Model'] != NULL){
                            $watchName .= ' (Model: '.$watch['Model'].')';
                        }
            
                        $argsProduct = array(	   
                        	'post_author' => get_current_user_id(), 
                        	'post_content' => $watch['Description'],
                        	'post_status' => 'Publish',
                        	'post_title' => $watchName,
                        	'post_parent' => '',
                        	'post_type' => "product"
                        ); 
                        
                        // Create a simple WooCommerce product
                        $productID = wp_insert_post( $argsProduct );
                                            
                        //I assign category
                        wp_set_object_terms($productID, $watchesTerm->term_id, 'product_cat');
                        
                        // Setting the product type
                        wp_set_object_terms($productID, 'simple', 'product_type' );
                        
                        // Setting meta data
                        $image_id = media_sideload_image( get_site_url().'/wp-content/watches/tn-'.$watch['StockNo'].'.jpg', $productID, $watchName, 'id' );
                        
                        if (is_int($image_id)){
                            update_post_meta($productID, '_thumbnail_id', $image_id);
                        }           
                        update_post_meta($productID, '_price', $watch['Price']);
                        update_post_meta($productID, '_regular_price', $watch['Price']);
                        
                        update_post_meta($productID, 'stockno', $watch['StockNo']);
                        update_post_meta($productID, 'brand', $watch['Brand']);
                        update_post_meta($productID, 'series', $watch['Series']);
                        update_post_meta($productID, 'model',  $watch['Model']);
                        update_post_meta($productID, 'serialno', $watch['SerialNo']);
                        update_post_meta($productID, 'newpreowned', $watch['NewPreOwned']);
                        update_post_meta($productID, 'movement', $watch['Movement']);
                        update_post_meta($productID, 'movementcalibre', $watch['MovementCalibre']);
                        update_post_meta($productID, 'casesize', $watch['CaseSize']);
                        update_post_meta($productID, 'age', $watch['Age']);
                        update_post_meta($productID, 'boxpapers', $watch['BoxPapers']);
                        update_post_meta($productID, 'cost', $watch['Cost']);
                        update_post_meta($productID, 'newretail', $watch['NewRetail']);
                        update_post_meta($productID, 'new_retail_price', $watch['NewRetail']);
                        update_post_meta($productID, 'quality', $watch['Quality']);
                        update_post_meta($productID, 'price', $watch['Price']);
                        update_post_meta($productID, 'datein', $watch['DateIn']);
                        update_post_meta($productID, 'datesold', $watch['DateSold']);
                        update_post_meta($productID, 'onhold', $watch['OnHold']);
                        update_post_meta($productID, 'hide', $watch['Hide']);
                        update_post_meta($productID, 'refurbcost', $watch['RefurbCost']);
                        update_post_meta($productID, 'refurbnotes', $watch['RefurbNotes']);
                        update_post_meta($productID, 'repairer', $watch['Repairer']);
                        update_post_meta($productID, 'sellerfirstname', $watch['SellerFirstName']);
                        update_post_meta($productID, 'sellersurname', $watch['SellerSurname']);
                        update_post_meta($productID, 'selleraddress', $watch['SellerAddress']);
                        update_post_meta($productID, 'sellerphone', $watch['SellerPhone']);
                        update_post_meta($productID, 'selleremail', $watch['SellerEmail']);
                        update_post_meta($productID, 'docketno', $watch['DocketNo']);
                        update_post_meta($productID, 'saleonbehalf', $watch['SaleOnBehalf']);
                        update_post_meta($productID, 'idtype', $watch['IDType']);
                        update_post_meta($productID, 'idreference', $watch['IDReference']);
                        update_post_meta($productID, 'sellernotes', $watch['SellerNotes']);
                        update_post_meta($productID, 'buyerfirstname', $watch['BuyerFirstName']);
                        update_post_meta($productID, 'buyersurname', $watch['BuyerSurname']);
                        update_post_meta($productID, 'buyeraddress', $watch['BuyerAddress']);
                        update_post_meta($productID, 'buyerphone', $watch['BuyerPhone']);
                        update_post_meta($productID, 'buyeremail', $watch['BuyerEmail']);
                        update_post_meta($productID, 'invoiceno', $watch['InvoiceNo']);
                        update_post_meta($productID, 'saleamount', $watch['SaleAmount']);
                        update_post_meta($productID, 'taxfree', $watch['TaxFree']);
                        update_post_meta($productID, 'datepaid', $watch['DatePaid']);
                        update_post_meta($productID, 'salenotes', $watch['SaleNotes']);
                        update_post_meta($productID, 'wholesaledealer', $watch['WholesaleDealer']);
                        update_post_meta($productID, 'wholesaleamount', $watch['WholesaleAmount']);
                        update_post_meta($productID, 'wholesalecurrency', $watch['WholesaleCurrency']);
                        update_post_meta($productID, 'wholesalenzd', $watch['WholesaleNZD']);
                        update_post_meta($productID, 'wholesalenotes', $watch['WholesaleNotes']);
                        update_post_meta($productID, 'shippingdate', $watch['ShippingDate']);
                        update_post_meta($productID, 'shippingnationalcourier', $watch['ShippingNationalCourier']);
                        update_post_meta($productID, 'shippingnationaltrackingno', $watch['ShippingNationalTrackingNo']);
                        update_post_meta($productID, 'shippingnationalcost', $watch['ShippingNationalCost']);
                        update_post_meta($productID, 'shippinginternationalcourier', $watch['ShippingInternationalCourier']);
                        update_post_meta($productID, 'shippinginternationaltrackingno', $watch['ShippingInternationalTrackingNo']);
                        update_post_meta($productID, 'shippinginternationalcost', $watch['ShippingInternationalCost']);
                        update_post_meta($productID, 'shippingemptyboxsent', $watch['ShippingEmptyBoxSent']);
                        update_post_meta($productID, 'shippingemptyboxdate', $watch['ShippingEmptyBoxDate']);
                        update_post_meta($productID, 'shippingemptyboxtrackingno', $watch['ShippingEmptyBoxTrackingNo']);
                        update_post_meta($productID, 'shippingemptyboxcost', $watch['ShippingEmptyBoxCost']);
                        $wimported = $wimported + 1;
                    }
                    $amountProcessed = $amountProcessed + 1;
                    
                } else {
                    
                    if (!isset($newoperation)){
                        $newoperation = $operation + 1;
                    }
                    
                }
                
            }
                       
        update_option('ik_wchsdbd_last_imported', $newoperation);
                        
        } else {
            $newoperation = 0;
            delete_option('ik_wchsdbd_jewelry_imported');
            delete_option('ik_wchsdbd_last_imported');
        }
        

        echo json_encode( $newoperation );
        
    } else {
        wp_send_json_error();
    }
    
    wp_die();
    
}

//Ajax to delete a repair
add_action( 'wp_ajax_ik_wchsdbd_ajax_delete_repair', 'ik_wchsdbd_ajax_delete_repair');
function ik_wchsdbd_ajax_delete_repair(){
    if(isset($_POST['iddato'])){
        $id_repair = absint($_POST['iddato']);

        global $wpdb;
        $tableDelete = $wpdb->prefix.'ik_watchrepairs';
        $rowResult = $wpdb->delete( $tableDelete , array( 'RepairNo' => $id_repair ) );
        
        echo json_encode( true );
    }
    wp_die();         
}

//Ajax to delete a repair
add_action( 'wp_ajax_ik_wchsdbd_ajax_search_repair', 'ik_wchsdbd_ajax_search_repair');
function ik_wchsdbd_ajax_search_repair(){
    if(isset($_POST['searchterm'])){
        $searchterm = sanitize_text_field($_POST['searchterm']);
        $watches_repair = new Ik_Watches_Repairs();
        $search_result = $watches_repair->get_repair_list('DESC', $searchterm);
       
        echo json_encode( $search_result );
    }
    wp_die();         
}

//Ajax to check out and update repair
add_action( 'wp_ajax_ik_wchsdbd_ajax_repair_by_id', 'ik_wchsdbd_ajax_repair_by_id');
function ik_wchsdbd_ajax_repair_by_id(){
    if(isset($_POST['iddato'])){
        $repair_id = absint($_POST['iddato']);

        $watches_repair = new Ik_Watches_Repairs();
        $watch_repair = $watches_repair->get_repair_by_id($repair_id);
        
        if ($watch_repair->RepairType == 'Watch'){
            $watchChecked = 'checked=""';
            $JewelleryChecked = '';
            $watchDisplay = '';
            $JewelleryDisplay = 'style="display:none"';
        } else {
            $watchChecked = '';
            $JewelleryChecked = 'checked=""';
            $watchDisplay = 'style="display:none"';
            $JewelleryDisplay = '';            
        }
        $dateIn = ($watch_repair->DateIn != '0000-00-00') ? 
date("d-m-Y", strtotime($watch_repair->DateIn)) : '00-00-0000';  
        $CollectDate = ($watch_repair->CollectDate != '0000-00-00') ? 
date("d-m-Y", strtotime($watch_repair->CollectDate)) : '00-00-0000';
        $CompletionDate = ($watch_repair->CompletionDate != '0000-00-00') ? 
date("d-m-Y", strtotime($watch_repair->CompletionDate)) : '00-00-0000';
        $GoAheadDate = ($watch_repair->GoAheadDate != '0000-00-00') ? 
date("d-m-Y", strtotime($watch_repair->GoAheadDate)) : '00-00-0000';
    
        $form_repair = '
        <div id="ik_repairs_form">
            <form method="post" action="" enctype="multipart/form-data" id="ik_repairForm">
                <input type="hidden" name="repair_update_id" value="'.$repair_id.'">
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
                              <td align="left">'.$watch_repair->RepairNo.'</td>
                            </tr>
                            <tr>
                              <td align="right"><b>Repair Type:</b></td>
                              <td align="left"><label>
                                <input type="radio" name="RepairType" value="Watch" '.$watchChecked.'>
                                Watch</label>
                                &nbsp;&nbsp;&nbsp;
                                <label>
                                <input name="RepairType" type="radio" value="Jewellery" '.$JewelleryChecked.'>
                                Jewellery</label></td>
                            </tr>
                            <tr>
                              <td align="right"><b>Date In:</b></td>
                              <td align="left"><input name="DateIn" type="text" id="DateIn" size="12" maxlength="10" value="'.$dateIn.'" class="hasdatepicker"></td>
                            </tr>
                            <tr>
                              <td align="right"><b>First Name:</b></td>
                              <td align="left"><input name="FirstName" type="text" id="FirstName" size="50" maxlength="50" autofocus="" value="'.$watch_repair->FirstName.'">
                              </td>
                            </tr>
                            <tr>
                              <td align="right"><b>Surname:</b></td>
                              <td align="left"><input name="Surname" type="text" id="Surname" size="50" maxlength="50" value="'.$watch_repair->Surname.'">
                              </td>
                            </tr>
                            <tr>
                              <td align="right"><b>Address:</b></td>
                              <td align="left"><textarea name="Address" id="Address" rows="5" cols="50">'.$watch_repair->Address.'</textarea></td>
                            </tr>
                            <tr>
                              <td align="right"><b>Phone:</b></td>
                              <td align="left"><input name="Phone" type="text" id="Phone" size="50" maxlength="20" value="'.$watch_repair->Phone.'">
                              </td>
                            </tr>
                            <tr>
                              <td align="right"><b>Mobile:</b></td>
                              <td align="left"><input name="Mobile" type="text" id="Mobile" size="50" maxlength="20" value="'.$watch_repair->Mobile.'">
                              </td>
                            </tr>
                            <tr>
                              <td align="right"><b>Email:</b></td>
                              <td align="left"><input name="Email" type="text" id="Email" size="50" maxlength="50" value="'.$watch_repair->Email.'">
                              </td>
                            </tr>
                          </tbody>
                          </table>
                        </div>
                        <div id="tabs-2" style="display: none" aria-labelledby="ui-id-2" class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="tabpanel" aria-expanded="false" aria-hidden="true">
                          <table border="0" cellspacing="1" cellpadding="2" align="center">
                            <tbody><tr>
                              <td align="right"><b>Brand:</b></td>
                              <td align="left"><span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span><input name="Brand" type="text" id="Brand" size="50" maxlength="30" value="'.$watch_repair->Brand.'" class="ui-autocomplete-input" autocomplete="off">
                              </td>
                            </tr>
                            <tr>
                              <td align="right"><b>Range:</b></td>
                              <td align="left"><span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span><input name="Series" type="text" id="Series" size="50" maxlength="100" value="'.$watch_repair->Series.'" class="ui-autocomplete-input" autocomplete="off">
                              </td>
                            </tr>
                            <tr>
                              <td align="right"><b>Model:</b></td>
                              <td align="left"><input name="Model" type="text" id="Model" size="50" maxlength="100" value="'.$watch_repair->Model.'">
                              </td>
                            </tr>
                            <tr>
                              <td align="right"><b>Serial No.:</b></td>
                              <td align="left"><input name="SerialNo" type="text" id="SerialNo" size="50" maxlength="20" value="'.$watch_repair->SerialNo.'">
                              </td>
                            </tr>
                            <tr>
                              <td align="right"><b>Description:</b></td>
                              <td align="left"><input name="WatchDescription" type="text" id="WatchDescription" size="50" maxlength="255" value="'.$watch_repair->WatchDescription.'">
                              </td>
                            </tr>
                          </tbody>
                          </table>
                        </div>
                        <div id="tabs-3" style="display: none" aria-labelledby="ui-id-3" class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="tabpanel" aria-expanded="false" aria-hidden="true">
                          <table border="0" cellspacing="1" cellpadding="2" align="center">
                            <tbody><tr>
                              <td align="right"><b>Item:</b></td>
                              <td align="left"><input name="JewelleryItem" type="text" id="JewelleryItem" size="50" maxlength="255" value="'.$watch_repair->JewelleryItem.'">
                              </td>
                            </tr>
                            <tr>
                              <td align="right"><b>Metal:</b></td>
                              <td align="left"><input name="JewelleryMetal" type="text" id="JewelleryMetal" size="50" maxlength="255" value="'.$watch_repair->JewelleryMetal.'">
                              </td>
                            </tr>
                            <tr>
                              <td align="right"><b>Gemstones:</b></td>
                              <td align="left"><input name="JewelleryGemstones" type="text" id="JewelleryGemstones" size="50" maxlength="255" value="'.$watch_repair->JewelleryGemstones.'">
                              </td>
                            </tr>
                            <tr>
                              <td align="right"><b>Description:</b></td>
                              <td align="left"><input name="JewelleryDescription" type="text" id="JewelleryDescription" size="50" maxlength="255" value="'.$watch_repair->JewelleryDescription.'">
                              </td>
                            </tr>
                          </tbody></table>
                        </div>
                        <div id="tabs-4" aria-labelledby="ui-id-4" class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="tabpanel" aria-expanded="false" aria-hidden="true" style="display: none;">
                          <table border="0" cellspacing="1" cellpadding="2" align="center">
                            <tbody><tr>
                              <td align="right"><b>Problem:</b></td>
                              <td align="left"><textarea name="Problem" id="Problem" rows="5" cols="50">'.$watch_repair->Problem.'</textarea></td>
                            </tr>
                            <tr>
                              <td align="right"><b>Repairer:</b></td>
                              <td align="left"><input name="Repairer" type="text" id="Repairer" size="50" maxlength="50" value="'.$watch_repair->Repairer.'">
                              </td>
                            </tr>
                            <tr>
                              <td align="right"><b>Cost Quote:</b></td>
                              <td align="left"><input class="checkprice_format" name="CostQuote" type="text" id="CostQuote" size="10" maxlength="10" value="'.$watch_repair->CostQuote.'">
                              </td>
                            </tr>
                            <tr>
                              <td align="right"><b>Retail Quote:</b></td>
                              <td align="left"><input class="checkprice_format" name="RetailQuote" type="text" id="RetailQuote" size="10" maxlength="10" value="'.$watch_repair->RetailQuote.'">
                              </td>
                            </tr>
                            <tr>
                              <td align="right"><b>Go Ahead Date:</b></td>
                              <td align="left"><input name="GoAheadDate" type="text" id="GoAheadDate" size="12" maxlength="10" value="'.$GoAheadDate.'" class="hasdatepicker"></td>
                            </tr>
                            <tr>
                              <td align="right"><b>Work Notes:</b></td>
                              <td align="left"><textarea name="WorkNotes" id="WorkNotes" rows="5" cols="50">'.$watch_repair->WorkNotes.'</textarea></td>
                            </tr>
                          </tbody></table>
                        </div>
                        <div id="tabs-5" aria-labelledby="ui-id-5" class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="tabpanel" aria-expanded="false" aria-hidden="true" style="display: none;">
                          <table border="0" cellspacing="1" cellpadding="2" align="center">
                            <tbody><tr>
                              <td align="right"><b>Invoice No.:</b></td>
                              <td align="left"><input name="InvoiceNo" type="text" id="InvoiceNo" size="12" maxlength="10" value="'.$watch_repair->InvoiceNo.'">
                              </td>
                            </tr>
                            <tr>
                              <td align="right"><b>Completion Date:</b></td>
                              <td align="left"><input name="CompletionDate" type="text" id="CompletionDate" size="12" maxlength="10" value="'.$CompletionDate.'" class="hasdatepicker"></td>
                            </tr>
                            <tr>
                              <td align="right"><b>Collect Date:</b></td>
                              <td align="left"><input name="CollectDate" type="text" id="CollectDate" size="12" maxlength="10" value="'.$CollectDate.'" class="hasdatepicker"></td>
                            </tr>
                            <tr>
                              <td align="right"><b>Notes:<br>
                              (not seen<br>
                              by client)</b></td>
                              <td align="left"><textarea name="Notes" id="Notes" rows="5" cols="50">'.$watch_repair->Notes.'</textarea></td>
                            </tr>
                          </tbody>
                          </table>
                        </div>
                    </div>
                    <p align="center">
                    <button class="ik_submit_repair button-primary">Update Repair »</button>
                    </p>
                </div>
            </form>
        </div>    
        <script>
    $(function() {
        $( ".hasdatepicker" ).datepicker({ dateFormat: "dd-mm-yy" });
    });
    </script>';
        echo json_encode( $form_repair );
    }
    wp_die();         
}

// Ajax to upload logo for receipt at config page
add_action( 'wp_ajax_ik_wchsdbd_uploadmedia', 'ik_wchsdbd_uploadmedia');
function ik_wchsdbd_uploadmedia() {
    if(isset($_GET['id']) ){
        $id_file = intval($_GET['id']);
        if ($id_file != 0){
            $logo_file = wp_upload_dir()['baseurl'].'/'.get_post_meta( $id_file, '_wp_attached_file', true);
        } else {
            //empty file
            $logo_file = '';
        }
        wp_send_json_success( $logo_file );
    } else {
        wp_send_json_error();
    }
}


//Ajax to send email with updates about repair
add_action( 'wp_ajax_ik_wchsdbd_ajax_send_repair_email', 'ik_wchsdbd_ajax_send_repair_email');
function ik_wchsdbd_ajax_send_repair_email(){
    //Default message 
    $resultEmail = 'Something went wrong. Try again!';
    
    if(isset($_POST['iddato'])){
        $repair_id = absint($_POST['iddato']);
        
        $watches_repair = new Ik_Watches_Repairs();
        $watch_repair = $watches_repair->get_repair_by_id($repair_id);
        
        if ($watch_repair != false){
            if ($watch_repair->Email == "") {
              $resultEmail = "There is no email address set up for this repair!";
            } else {
                
                $logo_image = get_option('ik_watches_db_img');
                
                if ($logo_image != NULL && $logo_image != false){
                    $logo = '<div style="text-align: center">
                        <img src="'.$logo_image.'" style="text-align: center" alt="logo" />
                    </div>';
                } else {
                    $logo = '';
                }
                
                $postaladdress = get_option('ik_watches_db_postaladdress');
                $phone = get_option('ik_watches_db_phone');
                $mobile = get_option('ik_watches_db_mobile');
                $email_sender = get_option('ik_watches_db_email');
                $bank_account = get_option('ik_watches_db_bank_account');

                
                if ($postaladdress != false && $postaladdress != NULL && $phone != false && $phone != NULL && $mobile != false && $mobile != NULL){
                    
                    $data_info = '<p align="center" style="color:#666666;font-size:14px;margin: 35px 0px">Phone: '.$phone.' | Mobile: '.$mobile.'<br>
                    Postal: '.$postaladdress.'</p>';
                    
                } else {
                    $data_info = '';
                }
                
                if ($email_sender != false && $email_sender != NULL){
                    $email_message = $email_sender;
                } else {
                    $email_message = get_option('admin_email');
                }
                
                
                $message = $logo.'
                <p style="text-align: center; margin: 25px 0px">Here are the details of your watch repair:</p>
                <table border="0" cellspacing="1" cellpadding="2" style="text-align: center; margin: 0 auto;">
                    <tr>
                        <td style="text-align:right"><b>Repair No.:</b></td>
                        <td style="text-align:left">'.$watch_repair->RepairNo.'</td>
                    </tr>
                    <tr>
                    <td align="right"><b>Date:</b></td>
                    <td align="left">'.$watch_repair->DateIn.'</td>
                    </tr>
                    <tr>
                    <td align="right"><b>Customer:</b></td>
                    <td align="left">'.$watch_repair->FirstName.' '.$watch_repair->Surname.'</td>
                    </tr>
                    <tr>
                    <td align="right" valign="top"><b>Address:</b></td>
                    <td align="left">'.$watch_repair->Address.'</td>
                    </tr>
                    <tr>
                    <td align="right"><b>Phone:</b></td>
                    <td align="left">'.$watch_repair->Phone.'</td>
                    </tr>
                    <tr>
                    <td align="right"><b>Mobile:</b></td>
                    <td align="left">'.$watch_repair->Mobile.'</td>
                    </tr>
                    <tr>
                    <td align="right"><b>Email:</b></td>
                    <td align="left">'.$watch_repair->Email.'</td>
                    </tr>';
                    
                    switch ($watch_repair->RepairType) {
                    case "Watch":
                      $message .= '
                    <tr>
                        <td align="right"><b>Watch:</b></td>
                        <td align="left">'.$watch_repair->Brand.' '.$watch_repair->Series.'</td>
                    </tr>
                    <tr>
                    <td align="right"><b>Model:</b></td>
                    <td align="left">'.$watch_repair->Model.'</td>
                    </tr>
                    <tr>
                    <td align="right"><b>Serial No.:</b></td>
                    <td align="left">'.$watch_repair->SerialNo.'</td>
                    </tr>
                    <tr>
                    <td align="right" valign="top"><b>Description:</b></td>
                    <td align="left">'.$watch_repair->WatchDescription.'</td>
                    </tr>';
                      break;
                    case "Jewellery":
                      $message .= '
                      <tr>
                        <td align="right"><b>Item:</b></td>
                        <td align="left">'.$watch_repair->JewelleryItem.'</td>
                    </tr>
                    <tr>
                        <td align="right"><b>Metal:</b></td>
                        <td align="left">'.$watch_repair->JewelleryMetal.'</td>
                    </tr>
                    <tr>
                        <td align="right"><b>Gemstones:</b></td>
                        <td align="left">'.$watch_repair->JewelleryGemstones.'</td>
                    </tr>
                    <tr>
                    <td align="right" valign="top"><b>Description:</b></td>
                    <td align="left">'.$watch_repair->JewelleryDescription.'</td>
                    </tr>';
                      break;
                    }
                    
                    $message .= '
                    <tr>
                        <td align="right" valign="top"><b>Problem:</b></td>
                        <td align="left">'.$watch_repair->Problem.'</td>
                    </tr>
                    <tr>
                        <td align="right" valign="top"><b>Work Notes:</b></td>
                        <td align="left">'.$watch_repair->WorkNotes.'</td>
                    </tr>
                </table>';
                
                $subject = "JewelWatch Repair Receipt - Repair No. ".$watch_repair->RepairNo;
                $itemname = "item";
                switch ($watch_repair->RepairType) {
                case "Watch":
                  $itemname = "watch";
                  break;
                case "Jewellery":
                  $itemname = "jewellery item";
                  break;
                }
                
                if ($watch_repair->RetailQuote <> 0) {
                $RetailQuoteText = "$" . number_format($watch_repair->RetailQuote, 2);
                }
                
                if ($watch_repair->CompletionDate <> "0000-00-00") {
                $subject = "JewelWatch Repair Update - Repair No. ".$watch_repair->RepairNo;
                $message .= '
                <p align="center" style="background-color: #FF0000; font-size: 14px"><b style="color: #FFFFFF">STATUS UPDATE</b></p>
                <p>Your '.$itemname.' has been repaired and is now ready to be collected.</p>
                <p>The cost of repairing your '.$itemname.' will be <b>'.$RetailQuoteText.'</b> (includes GST)</p>
                <p>Please reply to this message to arrange collection.</p>';
                } else if ($watch_repair->RetailQuote <> 0) {
                $subject = 'JewelWatch Repair Update - Repair No. '.$watch_repair->RepairNo;
                
                $message .= '
                <p style="text-align: center;background-color: #FF0000; font-size: 14px"><b style="color: #FFFFFF">STATUS UPDATE</b></p>
                <p>The cost of repairing your '.$itemname.' will be <b>'.$RetailQuoteText.'</b> (includes GST)</p>
                <p>Please reply to this message to let us know if you want to continue with the repair.</p>';
                }
                
                if ($bank_account != '' && $bank_account != NULL && $bank_account != false){
                    $bank_account = str_replace('\n', '<br />', $bank_account);
                $bank_account_message = '<p style="color:#666666;text-align:center;font-size:16px;margin-top:30px;margin-bottom: 30px;"><b>JewelWatchBank Account Details</b><br>
                    '.$bank_account.'</p>';
                } else {
                    $bank_account_message = '';
                }
                
                $message .= $data_info.$bank_account_message.'<p>
                    <tr>
                        <td valign="top" style="font-family:Arial,Helvetica,sans-serif;font-size:10px;color:#000000">This
                          message has been sent by <a href="'.get_site_url().'" target="_blank" >'.get_site_url().'</a>. You are receiving
                          this message because you made a purchase through JewelWatch.
                          To contact JewelWatch please email <a href="mailto:'.$email_message.'" target="_blank">'.$email_message.'</a></td>
                      </tr>
                </p>';
                
                $message .= '<p>
                    <tr>
                      <td valign="top" style="font-family:Arial,Helvetica,sans-serif;font-size:10px;color:#000000"><b style="color:#355a83">Important Info:</b> Please add <a href="mailto:'.$email_message.'" target="_blank">'.$email_message.'</a> to your address book so your email program does not consider our messages as spam.</td>
                  <tr>
                  </p>';
                
                $headers = array( 'Content-Type: text/html; charset=UTF-8' );
                wp_mail( $watch_repair->Email, $subject, $message, $headers);
                
                $resultEmail = 'Email sent to '.$watch_repair->Email.' for Repair No.'.$watch_repair->RepairNo;

            }
        }
        
    }
    echo json_encode( $resultEmail );
    wp_die();         
}
?>