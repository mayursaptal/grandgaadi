<?php
/*
 * Plugin Name: WPCargo Shipment Container Add-ons
 * Plugin URI: http://wptaskforce.com/
 * Description: WPCargo Shipment Container Add-ons helps manage shipment into container. Shorcode available for frontend [wpcargo-container-track-form pageredirect="page_id"], [wpcargo-container-track-result]
 * Author: <a href="http://www.wptaskforce.com/">WPTaskForce</a>
 * Text Domain: wpcargo-shipment-container
 * Domain Path: /languages
 * Version: 4.8.0
 */
if (!defined('ABSPATH')){
    exit; // Exit if accessed directly
}
//* Defined constant
define( 'WPCARGO_SHIPMENT_CONTAINER_VERSION', '4.8.0' );
define( 'WPCARGO_SHIPMENT_CONTAINER_TEXTDOMAIN', 'wpcargo-shipment-container' );
define( 'WPCARGO_SHIPMENT_CONTAINER_FILE', __FILE__ );
define( 'WPCARGO_SHIPMENT_CONTAINER_URL', plugin_dir_url( __FILE__ ) );
define( 'WPCARGO_SHIPMENT_CONTAINER_PATH', plugin_dir_path( __FILE__ ) );
define( 'WPCARGO_SHIPMENT_CONTAINER_BASENAME', plugin_basename( __FILE__ ) );
define( 'WPCARGO_SHIPMENT_CONTAINER_PAGER', 12 );
define( 'WPCARGO_SHIPMENT_CONTAINER_UPDATE_REMOTE', 'updates-7.2'  );
//Check WPCargo is installed
if ( ! class_exists( 'WPCargo_Shipment_Container_InstallCheck' ) ) {
  class WPCargo_Shipment_Container_InstallCheck {
		static function install() {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			if(!is_plugin_active( 'wpcargo/wpcargo.php') ) {
				deactivate_plugins(__FILE__);
				$send_error_message = __('This plugin requires <a href="http://wpcargo.com/">WPCargo</a> Free Version to activate', 'wpcargo-shipment-container');
				die($send_error_message);
			}elseif( !is_plugin_active( 'wpcargo-custom-field-addons/wpcargo-custom-field.php') ){
				deactivate_plugins(__FILE__);
				$send_error_message = __('This plugin requires WPCargo Custom Field Addons to activate', 'wpcargo-shipment-container');
				die($send_error_message);
			}
		}
	}
}
register_activation_hook( __FILE__, array('WPCargo_Shipment_Container_InstallCheck', 'install') );
//Check WPCargo license helper is installed
if ( !class_exists( 'WPCargo_Shipment_Container_License_Checker' ) ) {
  class WPCargo_Shipment_Container_License_Checker {
		static function install() {
			if (!is_plugin_active( 'wptaskforce-license-helper/wptaskforce-license-helper.php')) {
				deactivate_plugins(__FILE__);
				$send_error_message = __('This plugin requires <a href="http://wpcargo.com/">WPTaskForce License Helper</a> plugin to activate!', 'wpcargo-shipment-container');
				die($send_error_message);
			}
		}
	}
}
register_activation_hook( __FILE__, array('WPCargo_Shipment_Container_License_Checker', 'install') );
//* Includes files
require_once( WPCARGO_SHIPMENT_CONTAINER_PATH. 'admin/includes/translation.php');
require_once( WPCARGO_SHIPMENT_CONTAINER_PATH. 'admin/includes/helpers.php');
require_once( WPCARGO_SHIPMENT_CONTAINER_PATH. 'admin/includes/functions.php');
require_once( WPCARGO_SHIPMENT_CONTAINER_PATH. 'admin/includes/hooks.php');
require_once( WPCARGO_SHIPMENT_CONTAINER_PATH. 'admin/includes/ajax-handler.php');
require_once( WPCARGO_SHIPMENT_CONTAINER_PATH. 'admin/classes/class-api.php');
require_once( WPCARGO_SHIPMENT_CONTAINER_PATH. 'admin/classes/class-container.php');
require_once( WPCARGO_SHIPMENT_CONTAINER_PATH. 'admin/classes/class-container-user.php');
require_once( WPCARGO_SHIPMENT_CONTAINER_PATH. 'admin/classes/class-container-scripts.php');