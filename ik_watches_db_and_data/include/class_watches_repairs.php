<?php

/*
Template: Init class IK_Watches
Author: Gabriel Caroprese / Inforket.com
Update Date: 28/10/2021
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class Ik_Watches_Repairs {
    /* 
        Method to get repairs
    */
    private function get_repairs($order = 'DESC', $search = '', $filterData = ''){ 
        
        if ($order != 'DESC'){
            $order = 'ASC';
        } else {
            $order != 'DESC';
        }
        
        if ($search != ''){
            $search = sanitize_text_field($search);
            $searchQuery = "WHERE RepairNo = '".$search."' OR FirstName LIKE '%".$search."%' OR Surname LIKE '%".$search."%' OR Problem LIKE '%".$search." %' ";
            $filter = $this->get_filter_params($filterData, false);
        } else {
            $searchQuery = '';
            $filter = $this->get_filter_params($filterData, true);
        }
        
        global $wpdb;
        $query_repairs = "SELECT * FROM ".$wpdb->prefix."ik_watchrepairs ".$searchQuery.$filter."ORDER BY RepairNo ".$order;
        $repairs = $wpdb->get_results($query_repairs);
        
        if (isset($repairs[0]->RepairNo)){
            $repairs_data = $repairs;
        } else {
            $repairs_data = false;
        }
        
        return $repairs_data;
    }

    /* 
        Method to get filter query parameters
    */
    private function get_filter_params($filter, $where = false){
        $filterQuery = '';
        if ($filter != '' && $filter != 'all'){
            if ($where == true){
                $filterQuery = ' WHERE';
            }
            
            if ($filter == 'completed'){
                $filterQuery .= " CompletionDate != '0000-00-00'";
            } else 
            if ($filter == 'completedbutnotcollected'){
                $filterQuery .= " CompletionDate != '0000-00-00' AND CollectDate = '0000-00-00'";
            } else 
            if ($filter == 'notcompletedorcollected'){
                $filterQuery .= " CompletionDate = '0000-00-00' OR CollectDate = '0000-00-00'";
            } else {
                $filterQuery = '';
            }
        }
        
        return $filterQuery;
    }
    
    public function get_repair_by_id($idrepair = 0){ 
        
        if (is_int($idrepair)){
            if ($idrepair > 0){
                global $wpdb;
                $query_repairs = "SELECT * FROM ".$wpdb->prefix."ik_watchrepairs WHERE RepairNo = ".$idrepair;
                $repair = $wpdb->get_results($query_repairs);
                
                if (isset($repair[0]->RepairNo)){
                    return $repair[0];
                }
            }
        }
        
        return false;
        
    }
    

    /* 
        Method to get last index ID
    */
    public function get_last_index(){ 
        global $wpdb;
        $query_repairs = "SELECT * FROM ".$wpdb->prefix."ik_watchrepairs ORDER BY RepairNo DESC";
        $repairs = $wpdb->get_results($query_repairs);
        
        if (isset($repairs[0]->RepairNo)){
            $repairs_index = $repairs[0]->RepairNo;
        } else {
            $repairs_index = 0;
        }
        
        return $repairs_index;
    }
    
    /* 
        Method to get repairs count
    */
    public function get_repairs_count($filter = ''){ 
        
        $filter = $this->get_filter_params($filter, true);
        
        global $wpdb;
        $query_repairs = "SELECT * FROM ".$wpdb->prefix."ik_watchrepairs".$filter;
        $repairs = $wpdb->get_results($query_repairs);
        
        if (isset($repairs[0]->RepairNo)){
            $repairs_count = count($repairs);
        } else {
            $repairs_count = 0;
        }
        
        return $repairs_count;
    }
    
    /* 
        Method to list repairs
    */
    public function get_repair_list($orderdir = 'DESC',  $search = '', $filter = ''){
        $search = sanitize_text_field($search);
        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        if ($orderdir != 'DESC'){
            $orderdir = 'ASC';
            $ordercont = 'desc';
        } else {
            $orderdir != 'DESC';
            $ordercont = 'asc';
        }
        if ($search != ''){
            $resultsButton = '<button id="ik_wchsdbd_showall_repairs" class="button-primary">Show All Repairs</button>';
        } else {
            $resultsButton = '                    
            <select name="ik-filtrer-repairs" id="ik-filter-repairs" onchange="location = this.value;">
                <option value="'.$actual_link.'&filter=all" data="all" selected="">All Repairs</option>
                <option value="'.$actual_link.'&filter=completed" data="completed">Completed</option>
                <option value="'.$actual_link.'&filter=completedbutnotcollected" data="completedbutnotcollected">Completed but not Collected</option>
                <option value="'.$actual_link.'&filter=notcompletedorcollected" data="notcompletedorcollected">Not Completed or Colleted</option>
            </select>';
        }
        
        $repairs = $this->get_repairs($orderdir, $search, $filter);
        
        $repairs_list = '
            <div class="ik_wchsdbd_repairs_list_content">
    			<p class="search-box">
    				<label class="screen-reader-text" for="tag-search-input">Search:</label>
    				<input type="search" id="tag-search-input" name="s" value="'.$search.'">
    				<input type="submit" id="ik_wchsdbd_search_repairs" class="button" value="Search">'.$resultsButton.'
    			</p>
    			<table>
    					<thead>
    						<tr>
    							<th><input type="checkbox" class="select_all"></th>
    							<th class="repairno orderer sorted '.strtolower($orderdir).'"><a href="'.get_site_url().'/wp-admin/admin.php?page=ik_wchsdbd_panel_repairs&ordendir='.$ordercont.'">Repair No.<span class="sorting-indicator"></span></a></th>
    							<th class="datein">Date In</th>
    							<th>Customer</th>
    							<th>Details</th>
    							<th class="datacompleted">Date Completed</th>
    							<th class="datacollected">Date Collected</th>
    							<th>Actions</th>
    							<th><a href="#" class="ik_wchsdbd_button_delete_selected button action">Delete</a></th>
    						</tr>
    					</thead>
    					<tbody>';

        if ($repairs != false){
    					
    		foreach($repairs as $repair){
            
                $date_completed = $repair->CompletionDate;
                
                if ($date_completed == '0000-00-00'){
                    $date_completed = '-';
                }
                
                $date_colllected = $repair->CollectDate;
                 
                if ($date_colllected == '0000-00-00'){
                    $date_colllected = '-';
                }
                
                $repairs_list .= '
                        <tr iddato="'.$repair->RepairNo.'">
        					<td><input type="checkbox" class="select_dato"></td>
        					<td class="ik_wchsdbd_iddata">'.$repair->RepairNo.'</td>
        					<td class="ik_wchsdbd_iddata">'.$repair->DateIn.'</td>
        					<td class="ik_wchsdbd_iddata">'.$repair->FirstName.' '.$repair->Surname.'</td>
        					<td class="ik_wchsdbd_iddata">'.$repair->Problem.'</td>
        					<td class="ik_wchsdbd_iddata">'.$date_completed.'</td>
        					<td class="ik_wchsdbd_iddata">'.$date_colllected.'</td>
        					<td class="ik_wchsdbd_iddata_buttons_actions" iddato="'.$repair->RepairNo.'">
        						<a target="_blank" href="'.$actual_link.'&repair_to_print='.$repair->RepairNo.'" class="ik_wchsdbd_print dashicons dashicons-printer"></span>
        						<a href="#" class="ik_wchsdbd_email dashicons dashicons-email"></span>
        					</td>
        					<td class="ik_wchsdbd_iddata_buttons" iddato="'.$repair->RepairNo.'">
        						<button class="ik_wchsdbd_button_more_details button action">More Details</button>
        						<button class="ik_wchsdbd_button_delete_repair button action">Delete</button>
        					</td>
        				</tr>';
    		}
				
        } else{
            $repairs_list .= '<tr><td colspan="8">Nothing found.</td></tr>';
        }
        
        $repairs_list .= '
			    </tbody>
				    <tfoot>
						<tr>
							<th><input type="checkbox" class="select_all"></th>
							<th class="repairno orderer sorted '.strtolower($orderdir).'"><a href="'.get_site_url().'/wp-admin/admin.php?page=ik_wchsdbd_panel_repairs&ordendir='.$ordercont.'">Repair No.<span class="sorting-indicator"></span></a></span></th>
							<th class="datein">Date In</th>
							<th>Customer</th>
							<th>Details</th>
							<th class="datacompleted">Date Completed</th>
							<th class="datacollected">Date Collected</th>
    						<th>Actions</th>
							<th><a href="#" class="ik_wchsdbd_button_delete_selected button action">Delete</a></th>
						</tr>
					</tfoot>
					<tbody>
				</tbody>
			</table>
		</div>';
        
        return $repairs_list;
    }
 
     /* 
        Method to check if there's an ID called to be printed
    */
    public function print_data_by_ID(){
        if (isset($_GET['repair_to_print'])){
            $print_id = absint($_GET['repair_to_print']);
            if ($print_id > 0){
                $repair_data = $this->get_repair_by_id($print_id);
                
                if ($repair_data != false){
     
                    $logo_image = get_option('ik_watches_db_img');
                    $postaladdress = get_option('ik_watches_db_postaladdress');
                    $phone = get_option('ik_watches_db_phone');
                    $mobile = get_option('ik_watches_db_mobile');
    
                    $print_details = '
                    <div id="ik_wchsdbd_print_data">
                        <p align="center" style="margin-bottom: 50px;"><img src="'.$logo_image.'" width="480"></p>
                        <h1 align="center">Repair Receipt</h1>
                        <table border="0" cellspacing="1" cellpadding="2" align="center">
                            <tr>
                                <td align="right"><b>Repair No.:</b></td>
                                <td align="left">'.$repair_data->RepairNo.'</td>
                            </tr>
                            <tr>
                                <td align="right"><b>Date:</b></td>
                                <td align="left">'.$repair_data->DateIn.'</td>
                            </tr>
                            <tr>
                                <td align="right"><b>Customer:</b></td>
                                <td align="left">'.$repair_data->FirstName.' '.$repair_data->Surname.'</td>
                            </tr>
                            <tr>
                                <td align="right" valign="top"><b>Address:</b></td>
                                <td align="left">'.$repair_data->Address.'</td>
                            </tr>
                            <tr>
                                <td align="right"><b>Phone:</b></td>
                                <td align="left">'.$repair_data->Phone.'</td>
                            </tr>
                            <tr>
                                <td align="right"><b>Mobile:</b></td>
                                <td align="left">'.$repair_data->Mobile.'</td>
                            </tr>
                            <tr>
                                <td align="right"><b>Email:</b></td>
                                <td align="left">'.$repair_data->Email.'</td>
                            </tr>';
                      
                        if ($repair_data->RepairType == 'Jewellery'){
                            $print_details .= '
                                <tr>
                                    <td align="right"><b>Jewellery:</b></td>
                                    <td align="left">'.$repair_data->JewelleryItem.'</td>
                                </tr>
                                <tr>
                                    <td align="right"><b>Metal:</b></td>
                                    <td align="left">'.$repair_data->JewelleryMetal.'</td>
                                </tr>
                                <tr>
                                    <td align="right"><b>Gemstones:</b></td>
                                    <td align="left">'.$repair_data->JewelleryGemstones.'</td>
                                </tr>
                                <tr>
                                    <td align="right" valign="top"><b>Description:</b></td>
                                    <td align="left">'.$repair_data->JewelleryDescription.'</td>
                                </tr>';                   
                        } else {
                            $print_details .= '
                                <tr>
                                    <td align="right"><b>Watch:</b></td>
                                    <td align="left">'.$repair_data->Brand.'</td>
                                </tr>
                                <tr>
                                    <td align="right"><b>Model:</b></td>
                                    <td align="left">'.$repair_data->Model.'</td>
                                </tr>
                                <tr>
                                    <td align="right"><b>Serial No.:</b></td>
                                    <td align="left">'.$repair_data->SerialNo.'</td>
                                </tr>
                                <tr>
                                    <td align="right" valign="top"><b>Description:</b></td>
                                    <td align="left">'.$repair_data->WatchDescription.'</td>
                                </tr>';                      
                        }
                      
                        $print_details .= '
                                <tr>
                                    <td align="right" valign="top"><b>Problem:</b></td>
                                    <td align="left">'.$repair_data->Problem.'</td>
                                </tr>
                            </table>
                            <h4 align="center" style="margin-top: 50px;"><span>Phone: '.$phone.'</span><span>Mobile: '.$mobile.'</span><br>
                              Postal: '.$postaladdress.'</h4>
                        </div>
                        <p align="center" class="noprint">
                          <button type="button" class="button-primary" id="ik_wchsdbd_print_button">Print</button>
                        </p>
                        <script>
                        jQuery("#ik_wchsdbd_print_button").on("click", function(){
                            var restorepage = jQuery("body").html();
                            var printcontent = jQuery("#ik_wchsdbd_print_data").clone();
                            jQuery("body").empty().html(printcontent);
                            window.print();
                            jQuery("body").html(restorepage);
                        });
                        </script>';
                    
                    return $print_details;
                    
                }
                
            }
        }
        
        return false;
    }   
    
}

?>