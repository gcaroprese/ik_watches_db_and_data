<?php

/*
Template: Class IK_Watches
Author: Gabriel Caroprese / Inforket.com
Update Date: 19/10/2021
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class Ik_Watches {
    /* 
        Method to check if there's config data existing at options table
    */
    public function get_db_config(){ 
        global $wpdb;
        $wchsdbd_config_q = "SELECT * FROM ".$wpdb->prefix."options WHERE option_name LIKE 'ik_wchsdbd_".$typeDataConfig."'";
        $check_if_config = $wpdb->get_results($wchsdbd_config_q);
        
        if (isset($check_if_config[0]->option_id)){
            $config_exists = true;
        } else {
            $config_exists = false;
        }
        
        return $config_exists;
    }
    
    /* 
        Method to return DB connection values
    */
    public function get_dataconfig($dato_config){
        global $wpdb;
        $wchsdbd_configvals_q = "SELECT * FROM ".$wpdb->prefix."options WHERE option_name LIKE 'ik_wchsdbd_config'";
        $wchsdbd_configvalues = $wpdb->get_results($wchsdbd_configvals_q);
        
        if (isset($wchsdbd_configvalues[0]->option_id)){
            $woo_moodle_config = maybe_unserialize($wchsdbd_configvalues[0]->option_value);
            $dato_config_wchsdbd = $woo_moodle_config[$dato_config];
        } else if ($dato_config == 'valuehostname'){
             $dato_config_wchsdbd = "localhost";
        } else {
            $dato_config_wchsdbd = "";
        }
        
        return $dato_config_wchsdbd;
    }
    
    // Method to connect to watches DB
    private function connect_DB(){
        $servidordb = $this->get_dataconfig('valuehostname');
        $username = $this->get_dataconfig('valueusername');
        $passwordMoodle = $this->get_dataconfig('valuepassword');
        $nameDB = $this->get_dataconfig('valuedbname');
        
        $ik_dbconnection = new PDO('mysql:host='.$servidordb.';dbname='.$nameDB.';charset=utf8', $username, $passwordMoodle);
        return $ik_dbconnection;
    }

    /* 
        Method to check connection to DB is working
    */
    public function db_connection_check(){
    
        $connected = '<p><div id="ik_wchsdbd_connection_test" class="ik_okconnection">Connected</div></p><p>Go to <a href="'.get_site_url().'/wp-admin/admin.php?page=ik_wchsdbd_import">Import Section</a> to import non existing watches.</p>';
        $errorMessage = '<p><div id="ik_wchsdbd_connection_test" class="ik_errorconnection">Disconnected</div></p>';
            
        try
        {
        	$dbc = $this->connect_DB();
        	$dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        	$wchsdbd_checking_watch = "SELECT * FROM watches";
        	$resultcheck1 = $dbc->query($wchsdbd_checking_watch);    
        	$wchsdbd_checking_jewels = "SELECT * FROM jewellery";
        	$resultcheck2 = $dbc->query($wchsdbd_checking_jewels);
        	return $connected;
        	$dbc = NULL;
        }
        catch (PDOException $e)
        {
            return $errorMessage;
        }
    }
    
    /* 
        Method to get watches from DB
    */
    public function get_watches($offset = NULL, $limit = NULL){
        try
        {
        	$dbc = $this->connect_DB();
        	$dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        	$query_watches = "SELECT * FROM watches ORDER BY StockNo ASC";
        	  
        	if ($offset != NULL && $limit != NULL){
        	    $offset = absint($offset);
        	    $limit = absint($limit);
        	    if ($limit != 0 && $offset != 0){
        	        $query_watches .= ' LIMIT '.$offset.', '.$limit;
        	    }
        	}
        	$watches = $dbc->query($query_watches);
        	if ($watches->rowCount() > 0) {
        	    return $watches;
        	} else {
        	    return false;
        	}
        	$dbc = NULL;
        }
        catch (PDOException $e)
        {
            return false;
        }        
    }
    
    /* 
        Method to get jewellery from DB
    */
    public function get_jewellery($offset = NULL, $limit = NULL){
        try
        {
        	$dbc = $this->connect_DB();
        	$dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        	$query_jewellery = "SELECT * FROM jewellery ORDER BY StockNo ASC";
        	
        	if ($offset != NULL && $limit != NULL){
        	    $offset = absint($offset);
        	    $limit = absint($limit);
        	    if ($limit != 0 && $offset != 0){
        	        $query_jewellery .= ' LIMIT '.$offset.', '.$limit;
        	    }
        	}
        	
        	$jewellery = $dbc->query($query_jewellery);    	
        	if ($jewellery->rowCount() > 0) {
        	    return $jewellery;
        	} else {
        	    return false;
        	}
        	$dbc = NULL;
        }
        catch (PDOException $e)
        {
            return false;
        }        
    }
    
    
    
    
    
}


?>