<?php
if ( ! defined( 'ABSPATH' ) ) {
 exit; 
}
class WPCargo_CF_Check_Installed_Plugin{
	function __construct(){
		add_action( 'admin_notices', array($this, 'check_license_manager') );
		add_action( 'init', array( $this, 'activate_autoupdate' ) );
	}
	function check_wpcargo() {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if(!is_plugin_active( 'wpcargo/wpcargo.php')) {
			deactivate_plugins(__FILE__);
			esc_html_e('WPCargo Custom Field Add-ons plugin requires <a href="http://wpcargo.com/">WPCargo</a> Free Version to activate', 'wpcargo-custom-field' );
			die();
		}
	}
	function check_license_manager() {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if (!is_plugin_active( 'wptaskforce-license-helper/wptaskforce-license-helper.php')) {
			$class = 'notice notice-error';
			$message = esc_html__('WPCargo Custom Field Add-ons plugin requires <a href="http://wpcargo.com/">WPTaskForce License Helper</a> plugin to activate!', 'wpcargo-custom-field' );
			printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message ); 
		}
	}	
	function activate_autoupdate(){
		global $wpdb;
		require_once ( WPCARGO_CUSTOM_FIELD_PATH.'admin/classes/class-autoupdate.php' );
		$plugin_remote_path = 'http://www.wpcargo.com/repository/wpcargo-custom-field-addons/updates-7.2.php';
		new WPCargo_Custom_Field_AutoUpdate ( WPCARGO_CUSTOM_FIELD_VERSION, $plugin_remote_path, WPCARGO_CUSTOM_FIELD_BASENAME );
	}
}
$wpcargo_cf_check_installed_plugin = new WPCargo_CF_Check_Installed_Plugin;