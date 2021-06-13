<?php
/*
 * Plugin Name: WPCargo Receiving Add-ons
 * Plugin URI: http://wpcargo.com/
 * Description: This Add-ons will let you auto update the Shipment History by using the barcode scanner or entering the number on the input fields.
 * Author: <a href="http://wptaskforce.com/">WPTaskForce</a>
 * Text Domain: wpcargo-receiving
 * Domain Path: /languages
 * Version: 4.2.3
 */
if ( !defined( 'ABSPATH' ) ) {
	exit;
}
//* Defined constant
define( 'WPCARGO_RECEIVING_VERSION', '4.2.3' );
define( 'WPCARGO_RECEIVING_TEXTDOMAIN', 'wpcargo-receiving' );
define( 'WPCARGO_RECEIVING_URL', plugin_dir_url( __FILE__ ) );
define( 'WPCARGO_RECEIVING_PATH', plugin_dir_path( __FILE__ ) );
define( 'WPCARGO_RECEIVING_BASENAME', plugin_basename( __FILE__ ) );
require_once(WPCARGO_RECEIVING_PATH.'includes/functions.php');
require_once(WPCARGO_RECEIVING_PATH.'admin/admin.php');
add_action( 'init', 'wpc_receiving_activate_au' );
function wpc_receiving_activate_au(){
	require_once( WPCARGO_RECEIVING_PATH. 'admin/classes/wp_autoupdate.php');
	$plugin_remote_path 	= 'http://www.wpcargo.com/repository/wpcargo-receiving/updates-php7.2.php';
	new WPC_Receiving_AutoUpdate ( WPCARGO_RECEIVING_VERSION, $plugin_remote_path, WPCARGO_RECEIVING_BASENAME );
}
//** Load plugin text Domain
add_action( 'plugins_loaded', 'wpc_receiving_load_textdomain' );
function wpc_receiving_load_textdomain() {
	load_plugin_textdomain( 'wpcargo-receiving', false, '/wpcargo-receiving/languages' );
}