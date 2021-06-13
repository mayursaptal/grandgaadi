<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/*
 * Language Translation for the Ecncrypted Files
 */
function wpc_receiving_empty_track_number_message(){
	return esc_html__('Empty Tracking Number!', 'wpcargo-receiving');
}
function wpc_receiving_update_success_message(){
	return esc_html__('Shipment Updated Succesfully!', 'wpcargo-receiving');
}
function wpc_receiving_number_not_found_message(){
	return esc_html__('Shipment Not Found!', 'wpcargo-receiving');
}
function wpc_receiving_added_successfully_message(){
	return esc_html__('Shipment Added Succesfully!', 'wpcargo-receiving');
}
function wpc_receiving_activate_license_message(){
	return sprintf(
		'%s <a href="'.admin_url().'admin.php?page=wptaskforce-helper" title="WPCargo license page">%s</a>.',
		__('Please activate your license key', 'wpcargo-receiving'),
		__('here', 'wpcargo-receiving')
	);
}
function wpc_receiving_license_helper_plugin_dependent_message(){
	return sprintf(
		'%s <a href="http://wpcargo.com/" target="_blank">WPTaskForce License Helper</a> %s',
		__('This plugin requires', 'wpcargo-receiving'),
		__('plugin to be active!', 'wpcargo-receiving')
	);
}
function wpc_receiving_frontend_manager_plugin_dependent_message(){
	return sprintf(
		'%s <strong>WPCargo Frontend Manager</strong> %s',
		__('This plugin requires', 'wpcargo-receiving'),
		__('plugin to be active!', 'wpcargo-receiving')
	);
}
function wpc_receiving_frontend_manager_bulk_success_message(){
	return sprintf( 
		'<p><strong>%s</strong> %s</p>',
		__( 'Success!', 'wpcargo-receiving' ),
		__( 'Following shipments were updated successfully.', 'wpcargo-receiving' )
	 );;
}
function wpc_receiving_wpcargo_plugin_dependent_message(){
	return sprintf(
		'%s <a href="https://wordpress.org/plugins/wpcargo/" target="_blank">WPCargo</a> %s',
		__('This plugin requires', 'wpcargo-receiving'),
		__('plugin to be active!', 'wpcargo-receiving')
	);
}
function wpc_receiving_cheating_plugin_dependent_message(){
	return esc_html__('Cheating, uh?', 'wpcargo-receiving');
}
function wpc_receiving_label(){
	return esc_html__('Receiving', 'wpcargo-receiving');
}
require_once(WPCARGO_RECEIVING_PATH.'admin/classes/wpc-receiving-scripts.php');
require_once(WPCARGO_RECEIVING_PATH.'admin/classes/wpc-receiving-loader.php');
require_once(WPCARGO_RECEIVING_PATH.'admin/classes/wpc-receiving-settings.php');