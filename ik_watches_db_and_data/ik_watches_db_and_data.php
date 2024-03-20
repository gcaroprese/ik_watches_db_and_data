<?php
/*
Plugin Name: IK Watches DB Data
Description: Option to export Watches data and show reports
Version: 2.2.3
Author: Gabriel Caroprese
Author URI: https://inforket.com/
Requires at least: 5.3
Requires PHP: 7.2
*/ 

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$wchsdbdDir = dirname( __FILE__ );
$wchsdbdPublicDir = plugin_dir_url(__FILE__ );
define( 'IK_WCHSDBD_PLUGIN_DIR', $wchsdbdDir );
define( 'IK_WCHSDBD_PLUGIN_PUBLIC', $wchsdbdPublicDir );

//I add plugin functions
require_once($wchsdbdDir . '/include/init.php');
require_once($wchsdbdDir . '/include/ajax_functions.php');
require_once($wchsdbdDir . '/include/class_watches.php');
require_once($wchsdbdDir . '/include/class_watches_repairs.php');
register_activation_hook( __FILE__, 'ik_wchsdbd_dbcreate' );

?>