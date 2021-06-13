<?php
class WPC_CF_Hooks{
	public function __construct() {
		add_action( 'wpcargo_track_shipper_details', array($this, 'wpc_cf_track_result_shipper_template'), 10 );
		add_action( 'wpcargo_track_shipment_details', array($this, 'wpc_cf_track_result_shipment_template'), 10 );
		add_action( 'admin_print_shipper', array($this, 'wpc_cf_print_admin_shipper_template'), 10 );
		add_action( 'admin_print_shipment', array($this, 'wpc_cf_print_admin_shipment_template'), 10 );
	}
	function wpc_cf_track_result_shipper_template( $shipment_detail ){
		$options 				= get_option('wpcargo_cf_option_settings');
		require_once( wpccf_include_template( 'wpc-cf-track-shipper.tpl' ) );
	}
	function wpc_cf_track_result_shipment_template( $shipment_detail ){
		$sections 		= wpccf_additional_sections();
		if( !empty( $sections ) ){
			$shipment_id 	= $shipment_detail->ID;	
			require_once( wpccf_include_template( 'track-custom-section.tpl' ) );
		}
	}
	function wpc_cf_print_admin_shipper_template( $shipment_detail ){
		$options 				= get_option('wpcargo_cf_option_settings');
		require_once( WPCARGO_CUSTOM_FIELD_PATH.'admin/templates/print-cf-shipper.tpl.php' );
	}
	function wpc_cf_print_admin_shipment_template( $shipment_detail ){
		$options 				= get_option('wpcargo_cf_option_settings');
		require_once( WPCARGO_CUSTOM_FIELD_PATH.'admin/templates/print-cf-shipment.tpl.php' );
	}
}
$wpc_cf_hooks = new WPC_CF_Hooks;