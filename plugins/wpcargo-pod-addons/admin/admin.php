<?php
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
function wpcargo_pod_cors_http_header(){
	header("Access-Control-Allow-Origin: *");
}
add_action('init','wpcargo_pod_cors_http_header');
 
add_filter('kses_allowed_protocols', function($protocols) {
	$protocols[] = 'capacitor';
	return $protocols;
});
 
add_filter('kses_allowed_protocols', function($protocols) {
	$protocols[] = 'ionic';
	return $protocols;
});
require_once(WPCARGO_POD_PATH.'admin/includes/dashboard.php');
require_once(WPCARGO_POD_PATH.'admin/includes/api.php');
require_once(WPCARGO_POD_PATH.'admin/classes/class-pod.php');
require_once(WPCARGO_POD_PATH.'admin/classes/class-api.php');
require_once(WPCARGO_POD_PATH.'admin/classes/wpc-pod-admin-scripts.php');
require_once(WPCARGO_POD_PATH.'admin/classes/wpc-pod-media-upload-restriction.php');
require_once(WPCARGO_POD_PATH.'admin/classes/wpc-pod-settings.php');