<?php
/**
 * Plugin Name:       WPTaskForce License Helper
 * Plugin URI:        http://www.wpcargo.com/
 * Description:       This is to help you manage license for your WPCargo Add-ons.
 * Version:           4.0.2
 * Author:            WPTaskForce
 * Author URI:        http://www.wptaskforce.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wptaskforce-license-helper
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
define( 'WPCARGO_LICENSING_FILE', __FILE__ );
define( 'WPCARGO_LICENSING_BASENAME', plugin_basename( WPCARGO_LICENSING_FILE ) );
define( 'WPCARGO_LICENSING_URL', plugin_dir_url( WPCARGO_LICENSING_FILE ) );
define( 'WPCARGO_LICENSING_PATH', plugin_dir_path( WPCARGO_LICENSING_FILE ) );
define( 'WPCARGO_LICENSING_INC_PATH', plugin_dir_path( WPCARGO_LICENSING_FILE ) . 'includes' );
define( 'WPCARGO_LICENSING_ADMIN_URL', plugin_dir_url( WPCARGO_LICENSING_FILE ).'admin' );
define( 'WPCARGO_LICENSING_ADMIN_PATH', plugin_dir_path( WPCARGO_LICENSING_FILE ).'admin' );
function wptaskforce_license_helper_load_textdomain() {
   load_plugin_textdomain( 'wptaskforce-license-helper', false, dirname( WPCARGO_LICENSING_BASENAME ) . '/languages' ); 
}
add_action( 'plugins_loaded', 'wptaskforce_license_helper_load_textdomain' );
// Respository Update
function wptaskforce_license_helper_activate_au(){
    if( !function_exists('get_plugin_data') ){
        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    }
	$data = get_plugin_data( WPCARGO_LICENSING_FILE );
    require_once( WPCARGO_LICENSING_PATH.'admin/wp_autoupdate.php');
    $plugin_remote_path = 'http://wpcargo.com/repository/wptaskforce-license-helper/updates-7.2.php';
    new WPCargo_License_Helper_AutoUpdate ( $data['Version'], $plugin_remote_path, WPCARGO_LICENSING_BASENAME );
}
add_action( 'init', 'wptaskforce_license_helper_activate_au' );

require_once( WPCARGO_LICENSING_ADMIN_PATH. '/functions.php' );
function register_wpcargo_license_helper_page(){
	add_submenu_page( 'wpcargo-settings', 'WPTaskForce License Helper', 'WPTaskForce Helper', 'manage_options', 'wptaskforce-helper', 'wpcargo_license_helper_page'); 
}
add_action('admin_menu','register_wpcargo_license_helper_page');