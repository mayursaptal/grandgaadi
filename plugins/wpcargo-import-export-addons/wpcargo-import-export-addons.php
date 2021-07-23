<?php
/*
	Plugin Name: WPCargo Import and Export Add-ons
	Plugin URI: http://www.wpcargo.com
	Description: Allows you to Import/Export your shipments or to make backups. Requires WPCargo plugin to work.
	Version: 4.5.6
	Author: <a href="http://wptaskforce.com/">WPTaskforce</a>
	Author URI: http://www.wpcargo.com
	Text Domain: wpc-import-export
	Domain Path: /languages
*/
if ( ! defined( 'WPINC' ) ) {
	die;
}
/** Define plugin constants */
define( 'WPC_IMPORT_EXPORT_VERSION', '4.5.6' );
define( 'WPC_IMPORT_EXPORT_TEXTDOMAIN', 'wpc-import-export' );
define( 'WPC_IMPORT_EXPORT_BASENAME', plugin_basename( __FILE__ ) );
define( 'WPC_IMPORT_EXPORT_URL', plugin_dir_url( __FILE__ ) );
define( 'WPC_IMPORT_EXPORT_PATH', plugin_dir_path( __FILE__ ) );
define( 'WPC_IMPORT_EXPORT_UPDATE_REMOTE', 'updates-php7.2'  );
require_once(WPC_IMPORT_EXPORT_PATH.'admin/classes/class-scripts.php');
require_once(WPC_IMPORT_EXPORT_PATH.'admin/classes/class-import-export.php');
require_once(WPC_IMPORT_EXPORT_PATH.'admin/includes/functions.php');
require_once(WPC_IMPORT_EXPORT_PATH.'admin/includes/hooks.php');
require_once(WPC_IMPORT_EXPORT_PATH.'admin/includes/settings.php');
$WPCargo_Import_Export = new WPCargo_Import_Export;
$WPCargo_Import_Export->create_shortcode();
// Plugin Localization for the text Domain
function wpcargo_import_and_export_load_textdomain() {
	load_plugin_textdomain( 'wpc-import-export', false, '/wpcargo-import-export-addons/languages' );
}
add_action( 'plugins_loaded', 'wpcargo_import_and_export_load_textdomain' );
// Hooks & Filters
function wpcie_row_action_callback( $actions ){
    $mylinks = array(
		'<a href="' . admin_url( 'admin.php?page=wpcie-settings' ) . '" aria-label="' . __( 'Settings', 'wpc-import-export' ) . '">' . __( 'Settings', 'wpc-import-export' ) . '</a>',
		'<a href="' . admin_url( 'admin.php?page=wptaskforce-helper' ) . '" aria-label="' . __( 'License', 'wpc-import-export' ) . '">' . __( 'License', 'wpc-import-export' ) . '</a>'
	);
	$actions = array_merge( $actions, $mylinks );
	return $actions;
}
add_filter('plugin_action_links_' .plugin_basename( __FILE__ ), 'wpcie_row_action_callback', 10);