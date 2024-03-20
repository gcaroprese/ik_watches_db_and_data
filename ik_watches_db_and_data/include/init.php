<?php

/*
Template: Init IK Watches DB Data
Author: Gabriel Caroprese / Inforket.com
Update Date: 09/10/2021
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// If Woocommerce not active
add_action( 'admin_notices', 'ik_wchsdbd_dependencies' );
function ik_wchsdbd_dependencies() {
    if (!class_exists('woocommerce')) {
    echo '<div class="error"><p>' . __( 'Warning: IK Watches DB Data needs Woocommerce installed and activated in order to work properly.' ) . '</p></div>';
    }
}

//I create the DB table for repairs
function ik_wchsdbd_dbcreate() {
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$tabla_dirdatos_paises = $wpdb->prefix . 'ik_watchrepairs';

	$sql = "CREATE TABLE ".$tabla_dirdatos_paises." (
		RepairNo bigint(20) NOT NULL AUTO_INCREMENT,
		RepairType varchar(60) NOT NULL,
		DateIn date NOT NULL,
		FirstName varchar(60) NOT NULL,
		Surname varchar(60) NOT NULL,
		Address mediumtext NOT NULL,
		Phone varchar(20) NOT NULL,
		Mobile varchar(20) NOT NULL,
		Email varchar(60) NOT NULL,
		Brand varchar(30) NOT NULL,
		Series varchar(100) NOT NULL,
		Model varchar(100) NOT NULL,
		SerialNo varchar(20) NOT NULL,
		WatchDescription varchar(60) NOT NULL,
		JewelleryItem varchar(255) NOT NULL,
		JewelleryMetal varchar(255) NOT NULL,
		JewelleryGemstones varchar(255) NOT NULL,
		JewelleryDescription varchar(255) NOT NULL,
		Problem mediumtext NOT NULL,
		Repairer varchar(60) NOT NULL,
		CostQuote decimal(10,2) NOT NULL,
		RetailQuote decimal(10,2) NOT NULL,
		GoAheadDate date NOT NULL,
		WorkNotes mediumtext NOT NULL,
		InvoiceNo varchar(12) NOT NULL,
		CompletionDate date NOT NULL,
		CollectDate date NOT NULL,
		Notes varchar(60) NOT NULL,
		UNIQUE KEY RepairNo (RepairNo)
	) ".$charset_collate.";";
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}


// I add the menu of the plugin
add_action('admin_menu', 'ik_wchsdbd_menu');
function ik_wchsdbd_menu(){
    add_menu_page('Watches', 'Watches', 'manage_options', 'ik_wchsdbd_panel_repairs', 'ik_wchsdbd_panel_repairs', IK_WCHSDBD_PLUGIN_PUBLIC.'/img/watches-db-plugin-icon.png' );
    add_submenu_page('ik_wchsdbd_panel_repairs', 'Watches DB Repairs', 'Repairs', 'manage_options', 'ik_wchsdbd_panel_repairs', 'ik_wchsdbd_panel_repairs' );    
    add_submenu_page('ik_wchsdbd_panel_repairs', 'Watches DB Add Repairs', 'Add Repairs', 'manage_options', 'ik_wchsdbd_panel_add_repairs', 'ik_wchsdbd_panel_add_repairs' );
    add_submenu_page('ik_wchsdbd_panel_repairs', 'Watches DB Import', 'Import', 'manage_options', 'ik_wchsdbd_import', 'ik_wchsdbd_panel_import' );
    add_submenu_page('ik_wchsdbd_panel_repairs', 'Watches DB Panel', 'Connection', 'manage_options', 'ik_wchsdbd_panel_conection', 'ik_wchsdbd_panel_conection' );
    add_submenu_page('ik_wchsdbd_panel_repairs', 'Watches DB Config', 'Config', 'manage_options', 'ik_wchsdbd_panel_config', 'ik_wchsdbd_panel_config' );
}

// I create the panel config for the plugin
function ik_wchsdbd_panel_conection(){
   include(IK_WCHSDBD_PLUGIN_DIR.'/templates/ik_wchsdbd_connection.php');
}

// I create the panel config for the plugin
function ik_wchsdbd_panel_config(){
   include(IK_WCHSDBD_PLUGIN_DIR.'/templates/config.php');
}

// I create the content for the import page
function ik_wchsdbd_panel_import(){
   include(IK_WCHSDBD_PLUGIN_DIR.'/templates/ik_wchsdbd_import.php');
}

// I create the content for the repairs listing page
function ik_wchsdbd_panel_repairs(){
   include(IK_WCHSDBD_PLUGIN_DIR.'/templates/ik_wchsdbd_repairs.php');
}

// I create the content to add repairs
function ik_wchsdbd_panel_add_repairs(){
   include(IK_WCHSDBD_PLUGIN_DIR.'/templates/ik_wchsdbd_add_repairs.php');
}

?>