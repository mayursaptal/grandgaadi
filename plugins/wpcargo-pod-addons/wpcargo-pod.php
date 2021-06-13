<?php
/*
 * Plugin Name: WPCargo Proof of Delivery Add-ons
 * Plugin URI: http://wpcargo.com/
 * Description: This Add-ons will let you add images and signature on your shipment. This offers the ability to record delivery and collection operations with proof using a computer, tablets or mobile devices running Android and iOS mobile systems/Operating systems. Available shortcode [wpc_driver_accounts].
 * Author: <a href="http://wptaskforce.com/">WPTaskForce</a>
 * Text Domain: wpcargo-pod
 * Domain Path: /languages
 * Version: 4.4.4
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
//* Defined constant
define( 'WPCARGO_POD_URL', plugin_dir_url( __FILE__ ) );
define( 'WPCARGO_POD_PATH', plugin_dir_path( __FILE__ ) );
define( 'WPCARGO_POD_VERSION', '4.4.4' );
define( 'WPCARGO_POD_BASENAME', plugin_basename( __FILE__ ) );
define( 'WPCARGO_POD_TEXTDOMAIN', 'wpcargo-pod' );
require_once(WPCARGO_POD_PATH.'admin/includes/functions.php');
require_once(WPCARGO_POD_PATH.'classes/wpc-pod-results.php');
require_once(WPCARGO_POD_PATH.'classes/wpc-pod-scripts.php');
require_once(WPCARGO_POD_PATH.'classes/wpc-pod-function-ajax.php');
require_once(WPCARGO_POD_PATH.'classes/wpc-pod-scripts.php');
require_once(WPCARGO_POD_PATH.'admin/admin.php');
add_image_size( 'wpcargo-pod-images', 290, 250, true );
//ajax Profile Picture
add_action( 'wp_ajax_wpcargo_pod_images', 'wpcargo_pod_images' );
add_action( 'wp_ajax_nopriv_wpcargo_pod_images', 'wpcargo_pod_images' );
function wpcargo_pod_images(){
	$data_id 			= $_REQUEST['get_images_id'];
	$get_shipment_id 	= $_REQUEST['get_shipment_id'];
	$get_action_type 	= $_REQUEST['get_action_type'];
	$saved_images       = get_post_meta($get_shipment_id, 'wpcargo-pod-image', true);
	$explode_images     = !empty($saved_images) ? explode(',', $saved_images) : array();
    
    if( isset($get_action_type) && $get_action_type == 'remove' ){
        update_post_meta($get_shipment_id, 'wpcargo-pod-image', join(',', $data_id));
    }else{
    	if(isset($data_id)){
    		$merge_images = array_merge( $data_id, array_filter($explode_images) );
    		$new_images = array_unique($merge_images);
    		update_post_meta($get_shipment_id, 'wpcargo-pod-image', join(',', $new_images));
    	}
    }
	die();
}
add_action( 'wp_ajax_wpcargo_setpost_images', 'wpcargo_setpost_images' );
add_action( 'wp_ajax_nopriv_wpcargo_setpost_images', 'wpcargo_setpost_images' );
function wpcargo_setpost_images() {
	$get_shipment_id 	= $_REQUEST['get_shipment_id'];
	$data_id 			= $_REQUEST['get_images'];
	$saved_images       = get_post_meta($get_shipment_id, 'wpcargo-pod-image', true);
	$explode_images     = !empty($saved_images) ? explode(',', $saved_images) : array();
	$set_data_id 		= array_unique( array_merge( $data_id, array_filter($explode_images) ) );
	if(isset($data_id)) {
		echo '<p class="header-pod-result">'.__('Your current captured images', 'wpcargo-pod' ).':</p>';
		foreach($set_data_id as $data_ids ) {
			echo '<div class="gallery-thumb" data-id="'.$data_ids.'"><div class="single-img">';
				echo wp_get_attachment_image($data_ids, 'wpcargo-pod-images'); 
			echo '</div><span class="remove-gallery-img" title="Remove">x</span></div>';
		}	
	}
	die();
	
}
function wpc_pod_add_roles_on_plugin_activation() {
	$result = add_role(
		'wpcargo_driver',
		__( 'WPCargo Driver' ),
		array(
			'read' => true,
			'upload_files' => true,
		)
	);
}
function wpc_pod_remove_roles_deactivation_callback(){
	remove_role( 'wpcargo_driver' );
}
register_activation_hook( __FILE__, 'wpc_pod_add_roles_on_plugin_activation' );
register_deactivation_hook( __FILE__, 'wpc_pod_remove_roles_deactivation_callback' );
// Load the auto-update class
//** Load Plugin text domain
add_action( 'plugins_loaded', 'wpc_pod_load_textdomain' );
function wpc_pod_load_textdomain() {
	load_plugin_textdomain( 'wpcargo-pod', false, '/wpcargo-pod-addons/languages' );
}
add_action( 'init', 'wpc_pod_activate_au' );
function wpc_pod_activate_au(){
	require_once( WPCARGO_POD_PATH. 'admin/classes/wp_autoupdate.php');
	$plugin_remote_path = 'http://www.wpcargo.com/repository/wpcargo-pod-addons/updates-php7.2.php';
	new WPC_POD_AutoUpdate ( WPCARGO_POD_VERSION, $plugin_remote_path, WPCARGO_POD_BASENAME );
}
