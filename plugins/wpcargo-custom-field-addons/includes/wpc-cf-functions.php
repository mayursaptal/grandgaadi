<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
}
add_action('plugins_loaded', 'wpc_cf_change_track_result_template');
function wpc_cf_change_track_result_template() {
	global $wpcargo_track_form;
	remove_action('wpcargo_track_shipper_details', array( $wpcargo_track_form, 'wpcargo_trackform_result_shipper_details_template' ), 10 );
	remove_action('wpcargo_track_shipment_details', array( $wpcargo_track_form, 'wpcargo_trackform_result_shipment_details_template' ), 10 );
	remove_filter('wpcargo_track_shipment_query', array($wpcargo_track_form, 'wpcargo_trackform_result_query'), 10 );
}
add_action('admin_init', 'wpc_cf_track_result_ship_template');
function wpc_cf_track_result_ship_template(){	
	global $wpcargo_print_admin;
	remove_action('admin_print_shipper', array( $wpcargo_print_admin, 'wpcargo_print_shipper_template' ), 10 );
	remove_action('admin_print_shipment', array( $wpcargo_print_admin, 'wpcargo_print_shipment_template' ), 10 );
}
// helpers
function wpccf_upload_accepted_filetype(){
	$files = array(
		'image/*', 'application/pdf', '.psd', '.doc', '.docx', '.csv', '.xls', '.xlsx'
	);
	return apply_filters( 'wpccf_upload_accepted_filetype', $files );
}
function wpccf_upload_maxfilesize_mb(){
	return apply_filters( 'wpccf_upload_maxfilesize_mb', 5 );
}