<?php
/*
 * Plugin Name: WPCargo API Addon
 * Plugin URI: http://wpcargo.com/
 * Description: WPCargo API allows to share you WPCargo shipment data using API, Available shortcode [wpcargo_api_account]
 * Author: <a href="http://wptaskforce.com/">WPTaskForce</a>
 * Text Domain: wpcargo-api
 * Version: 5.0.5
 */
if ( ! defined( 'ABSPATH' ) ) {
	die;
}
define( 'WPC_API_VERSION', "5.0.5");
define( 'WPC_API_TEXTDOMAIN', 'wpcargo-api' );
define( 'WPC_API_URL', plugin_dir_url( __FILE__ ) );
define( 'WPC_API_PATH', plugin_dir_path( __FILE__ ) );
define( 'WPC_API_FILE_DIR', __FILE__ );
define( 'WPC_API_BASENAME', plugin_basename( __FILE__ ) );
// Load plugin text domain
add_action( 'plugins_loaded', 'wpcapi_load_textdomain' );
function wpcapi_load_textdomain() {
	load_plugin_textdomain( 'wpcargo-api', false, '/wpcargo-api-addon/languages' );
}
// Load plugin auto update
add_action( 'init', 'wpcapi_plugin_auto_update' );
function wpcapi_plugin_auto_update(){
	require_once( WPC_API_PATH. 'admin/classes/class-autoupdate.php');
	$plugin_remote_path = 'http://www.wpcargo.com/repository/wpcargo-api-addon/updates-php7.2.php';
	new WPCargo_API_AutoUpdate ( WPC_API_VERSION, $plugin_remote_path, WPC_API_BASENAME );
}
require_once(WPC_API_PATH.'/admin/includes/functions.php');
require_once(WPC_API_PATH.'/admin/classes/class-scripts.php');
require_once(WPC_API_PATH.'/admin/classes/class-main.php');
require_once(WPC_API_PATH.'/admin/classes/class-api.php');