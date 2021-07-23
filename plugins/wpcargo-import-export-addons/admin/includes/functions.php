<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}
function wpcie_email_notification(){
	return apply_filters( 'wpcie_email_notification', true );
}
function wpcie_default_status(){
	$status = get_option( 'wpcfe_default_status' ) ? get_option( 'wpcfe_default_status' ) : __( 'Shipment Created', 'wpc-import-export' ) ;
	return apply_filters( 'wpcie_default_status', $status );
}
function wpcie_update_status(){
	return apply_filters( 'wpcie_update_status', __( 'Shipment Updated', 'wpc-import-export' ) );
}
function wpcie_category_list(){
	return get_categories( array(
		'taxonomy' 	=> 'wpcargo_shipment_cat',
		'orderby' 	=> 'name',
		'order'   	=> 'ASC',
		'hide_empty' => true,
	) );
}
function wpcie_upload_errors(){
	$phpFileUploadErrors 	= array(
		0 => __('There is no error, the file uploaded with success.', 'wpc-import-export' ),
		1 => __('The uploaded file exceeds the upload_max_filesize directive in php.ini.', 'wpc-import-export' ),
		2 => __('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.', 'wpc-import-export' ),
		3 => __('The uploaded file was only partially uploaded.', 'wpc-import-export' ),
		4 => __('No file was uploaded.', 'wpc-import-export' ),
		6 => __('Missing a temporary folder.', 'wpc-import-export' ),
		7 => __('Failed to write file to disk.', 'wpc-import-export' ),
		8 => __('A PHP extension stopped the file upload.', 'wpc-import-export' ),
	);
	return $phpFileUploadErrors;
}
function wpcie_export_file_format_list(){
	$extension = array(
		'xls' => ",", 
		'xlt' => ",", 
		'xla' => ",", 
		'xlw' => ",",
		'csv' => ","
	);
	return apply_filters( 'wpcie_export_file_format_list', $extension );
}
function wpcie_clean_dir( $directory ){
	$files = glob( $directory.'*'); // get all file names
	foreach($files as $file){ // iterate files
	if(is_file($file))
		unlink($file); // delete file
	}
}
function wpcie_get_frontend_page(){
	global $wpdb;
	$sql 			= "SELECT `ID` FROM {$wpdb->prefix}posts WHERE `post_content` LIKE '%[wpcie_import_export]%' AND `post_status` LIKE 'publish' LIMIT 1";
	$shortcode_id 	= $wpdb->get_var( $sql );
	if( ! $shortcode_id ){
		// Create post object
		$importExport = array(
			'post_title'    => wp_strip_all_tags( esc_html__('Import / Export', 'wpc-import-export') ),
			'post_content'  => '[wpcie_import_export]',
			'post_status'   => 'publish',
			'post_type'   	=> 'page',
		);
		
		// Insert the post into the database
		$shortcode_id = wp_insert_post( $importExport );		
	}
	if( $shortcode_id ){
		update_post_meta( $shortcode_id, '_wp_page_template', 'dashboard.php');
	}
	return $shortcode_id;
}
function wpcie_is_client(){
	$current_user = wp_get_current_user();
	$roles 		  =  $current_user->roles;
	if( in_array( 'wpcargo_client', $roles ) ){
		return true;
	}
	return false;
}
function wpcie_is_employee(){
	$current_user = wp_get_current_user();
	$roles 		  =  $current_user->roles;
	if( in_array( 'wpcargo_employee', $roles ) ){
		return true;
	}
	return false;
}
function wpcie_is_manager(){
	$current_user = wp_get_current_user();
	$roles 		  =  $current_user->roles;
	if( in_array( 'wpcargo_branch_manager', $roles ) ){
		return true;
	}
	return false;
}
function wpcie_is_agent(){
	$current_user = wp_get_current_user();
	$roles 		  =  $current_user->roles;
	if( in_array( 'cargo_agent', $roles ) ){
		return true;
	}
	return false;
}
function wpcie_is_driver(){
	$current_user = wp_get_current_user();
	$roles 		  =  $current_user->roles;
	if( in_array( 'wpcargo_driver', $roles ) ){
		return true;
	}
	return false;
}
function wpcie_package_key_value_pair(){
	$pairs = [];
	if( !empty( wpcargo_package_fields() ) ){
		foreach( wpcargo_package_fields() as $key => $value ){
			$pairs[] = array(
				'key' => $key,
				'label' => $value['label']
			);
		}
	}
	return $pairs;
}
function wpcie_history_key_value_pair(){
	$pairs = [];
	if( !empty( wpcargo_history_fields() ) ){
		foreach( wpcargo_history_fields() as $key => $value ){
			$pairs[] = array(
				'key' => $key,
				'label' => $value['label']
			);
		}
	}
	return $pairs;
}
function wpcie_registered_form_fields(){
	$fields = array(
		array(
			'meta_key' 	=> 'wpcargo_shipper_name',
			'label' 	=> esc_html__( 'Shipper Name', 'wpc-import-export' ),
			'fields' 	=> array()
		),
		array(
			'meta_key' 	=> 'wpcargo_shipper_phone',
			'label' 	=> esc_html__( 'Shipper Phone Number', 'wpc-import-export' ),
			'fields' 	=> array()
		),
		array(
			'meta_key' 	=> 'wpcargo_shipper_address',
			'label' 	=> esc_html__( 'Shipper Address', 'wpc-import-export' ),
			'fields' 	=> array()
		),
		array(
			'meta_key' 	=> 'wpcargo_shipper_email',
			'label' 	=> esc_html__( 'Shipper Email', 'wpc-import-export' ),
			'fields' 	=> array()
		),
		array(
			'meta_key' 	=> 'wpcargo_receiver_name',
			'label' 	=> esc_html__( 'Receiver Name', 'wpc-import-export' ),
			'fields' 	=> array()
		),
		array(
			'meta_key' 	=> 'wpcargo_receiver_phone',
			'label' 	=> esc_html__( 'Receiver Phone Number', 'wpc-import-export' ),
			'fields' 	=> array()
		),
		array(
			'meta_key' 	=> 'wpcargo_receiver_address',
			'label' 	=> esc_html__( 'Receiver Address', 'wpc-import-export' ),
			'fields' 	=> array()
		),
		array(
			'meta_key' 	=> 'wpcargo_receiver_email',
			'label' 	=> esc_html__( 'Receiver Email', 'wpc-import-export' ),
			'fields' 	=> array()
		),
		array(
			'meta_key' 	=> 'agent_fields',
			'label' 	=> esc_html__( 'Agent Name', 'wpc-import-export' ),
			'fields' 	=> array()
		),
		array(
			'meta_key' 	=> 'wpcargo_type_of_shipment',
			'label' 	=> esc_html__( 'Type of Shipment', 'wpc-import-export' ),
			'fields' 	=> array()
		),
		array(
			'meta_key' 	=> 'wpcargo_courier',
			'label' 	=> esc_html__( 'Courier', 'wpc-import-export' ),
			'fields' 	=> array()
		),
		array(
			'meta_key' 	=> 'wpcargo_mode_field',
			'label' 	=> esc_html__( 'Mode', 'wpc-import-export' ),
			'fields' 	=> array()
		),
		array(
			'meta_key' 	=> 'wpcargo_qty',
			'label' 	=> esc_html__( 'Quantity', 'wpc-import-export' ),
			'fields' 	=> array()
		),
		array(
			'meta_key' 	=> 'wpcargo_total_freight',
			'label' 	=> esc_html__( 'Total Freight', 'wpc-import-export' ),
			'fields' 	=> array()
		),
		array(
			'meta_key' 	=> 'wpcargo_carrier_ref_number',
			'label' 	=> esc_html__( 'Carrier Reference No.', 'wpc-import-export' ),
			'fields' 	=> array()
		),
		array(
			'meta_key' 	=> 'wpcargo_origin_field',
			'label' 	=> esc_html__( 'Origin', 'wpc-import-export' ),
			'fields' 	=> array()
		),
		array(
			'meta_key' 	=> 'wpcargo_pickup_date_picker',
			'label' 	=> esc_html__( 'Pickup Date', 'wpc-import-export' ),
			'fields' 	=> array()
		),
		array(
			'meta_key' 	=> 'wpcargo_comments',
			'label' 	=> esc_html__( 'Comments', 'wpc-import-export' ),
			'fields' 	=> array()
		),
		array(
			'meta_key' 	=> 'wpcargo_weight',
			'label' 	=> esc_html__( 'Weight', 'wpc-import-export' ),
			'fields' 	=> array()
		),
		array(
			'meta_key' 	=> 'wpcargo_packages',
			'label' 	=> esc_html__( 'Packages', 'wpc-import-export' ),
			'fields' 	=> array()
		),
		array(
			'meta_key' 	=> 'wpcargo_product',
			'label' 	=> esc_html__( 'Product', 'wpc-import-export' ),
			'fields' 	=> array()
		),
		array(
			'meta_key' 	=> 'payment_wpcargo_mode_field',
			'label' 	=> esc_html__( 'Payment Mode', 'wpc-import-export' ),
			'fields' 	=> array()
		),
		array(
			'meta_key' 	=> 'wpcargo_carrier_field',
			'label' 	=> esc_html__( 'Carrier', 'wpc-import-export' ),
			'fields' 	=> array()
		),
		array(
			'meta_key' 	=> 'wpcargo_departure_time_picker',
			'label' 	=> esc_html__( 'Departure Time', 'wpc-import-export' ),
			'fields' 	=> array()
		),
		array(
			'meta_key' 	=> 'wpcargo_destination',
			'label' 	=> esc_html__( 'Destination', 'wpc-import-export' ),
			'fields' 	=> array()
		),
		array(
			'meta_key' 	=> 'wpcargo_pickup_time_picker',
			'label' 	=> esc_html__( 'Pickup Time', 'wpc-import-export' ),
			'fields' 	=> array()
		),
		array(
			'meta_key' 	=> 'wpcargo_expected_delivery_date_picker',
			'label' 	=> esc_html__( 'Expected Delivery Date', 'wpc-import-export' ),
			'fields' 	=> array()
		)
	);
	$addition_fields = array(
		array(
			'meta_key' 	=> 'wpc-multiple-package',
			'label' 	=> esc_html__( 'Package Details', 'wpc-import-export' ),
			'fields' 	=> wpcie_package_key_value_pair()
		),
		array(
			'meta_key' 	=> 'wpcargo_shipments_update',
			'label' 	=> esc_html__( 'Shipment History', 'wpc-import-export' ),
			'fields' 	=> wpcie_history_key_value_pair()
		),
		array(
			'meta_key' 	=> 'wpcargo_status',
			'label' 	=> esc_html__( 'Shipment Status', 'wpc-import-export' ),
			'fields' 	=> array()
		),
	);
	$form_fields = apply_filters( 'ie_registered_fields', $fields );
	/*
	 * Merge the meta fields to the shipment history and multiple packages fields
	 */
	$form_fields = array_merge( $form_fields, $addition_fields );
	foreach ( array_reverse( wpcie_default_headers() ) as $value ) {
		array_unshift($form_fields, $value );
	}
	return $form_fields;
}
function wpcie_registered_headers(){	
	$headers = [];
	if( !empty( wpcie_registered_form_fields() ) ){
		foreach ( wpcie_registered_form_fields() as $fields ) {
			$headers[$fields['meta_key']] = $fields['label'];
		}
	}
	return $headers;
}
function wpcie_default_headers(){
	$default_headers = array(
		array(
			'meta_key' 	=> 'shipment_id',
			'label' 	=> wpcie_shipment_id_label(),
			'fields' 	=> array()
		),
		array(
			'meta_key' 	=> 'shipment_title',
			'label' 	=> wpcie_shipment_title_label(),
			'fields' 	=> array()
		),
		array(
			'meta_key' 	=> 'registered_shipper',
			'label' 	=> wpcie_registered_shipper_label(),
			'fields' 	=> array()
		)
	);
	return $default_headers;
}
function wpcie_get_headers(){
	return array_map( 'trim',  array_values( wpcie_registered_headers() ) );
}
function wpcie_get_meta_keys(){
	return array_map( 'trim',  array_keys( wpcie_registered_headers() ) );
}
function wpcie_get_key_value_pairs(){
	return wpcie_registered_headers();
}
function wpcie_csv_template_headers(){
	if( empty( wpcie_registered_headers() ) ){
		return false;
	}
	$headers = array();
	foreach ( wpcie_registered_headers() as $key => $value) {
		if( wpcie_check_field_type( $key, 'file' ) ){
			continue;
		}
		$headers[$key] = $value.' ('.$key.')';
	}
	return apply_filters( 'wpcie_csv_template_headers', $headers );
}
function wpcie_check_field_type( $meta_key, $type ){
	if( !function_exists('wpccf_get_field_by_metakey') ){
		return false;
	}
	$meta_info = wpccf_get_field_by_metakey( $meta_key );
	if( !empty($meta_info) && array_key_exists('field_type', $meta_info ) && $meta_info['field_type'] === $type ){
		return true;
	}
	return false;
}
function wpcie_check_header( $custom_header, $default_header ){
	$result = false;
	if( !empty( $custom_header ) ){
		foreach ( $custom_header as $value ) {
			if( !in_array( $value, $default_header ) ){
				$result = false;
				break;
			}
			$result = true;
		}
	}
	return $result;
}
function wpcie_get_user_fullname( $userID = null ){
    $user_fullname = '';
    if( $userID ){
    	$user_info = get_userdata( $userID );
	    if( !empty( $user_info->first_name ) || !empty( $user_info->last_name ) ){
	        $user_fullname = $user_info->first_name.' '.$user_info->last_name;
	    }else{
	        $user_fullname = $user_info->display_name;
	    }
	}
    return $user_fullname;
}
function wpcie_get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strrpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strrpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}
function wpcie_disable(){
	return get_option( 'wpcie_disable' ) ? true : false ;
}
function wpcie_restricted_role(){
	return get_option( 'wpcie_restricted_role' ) ? get_option( 'wpcie_restricted_role' ) : array() ;
}
function is_wpcie_restricted_role(){
	$current_user = wp_get_current_user();
	$status = false;
	if( array_intersect( $current_user->roles, wpcie_restricted_role() ) ){
		$status = true;
	}
	return apply_filters( 'is_wpcie_restricted_role', $status );
}
function can_wpcie_import(){
	return apply_filters( 'can_wpcie_import', can_wpcfe_add_shipment() );
}
function can_wpcie_export(){
	$current_user = wp_get_current_user();
	$status = true;
	if( array_intersect( $current_user->roles, wpcie_restricted_role() ) ){
		$status = false;
	}
	return apply_filters( 'can_wpcie_export', $status );
}
/*
 * Language Translation for the Ecncrypted Files
 */
function wpcie_activate_license_message(){
	return esc_html__( 'Please activate your license key', 'wpc-import-export' ).' <a href="'.admin_url().'admin.php?page=wptaskforce-helper" title="WPCargo license page">'.esc_html__('here', 'wpc-import-export' ).'</a>.';
}
function wpcie_shipment_id_label(){
	return esc_html__( 'ShipmentID', 'wpc-import-export' );
}
function wpcie_shipment_title_label(){
	return esc_html__( 'Shipment Title', 'wpc-import-export' );
}
function wpcie_shipment_category_label(){
	return esc_html__( 'Shipment Category', 'wpc-import-export' );
}
function wpcie_registered_shipper_label(){
	return esc_html__( 'Assigned Client', 'wpc-import-export' );
}
function wpcie_no_result_label(){
	return esc_html__( 'No Result Found!', 'wpc-import-export' );
}
function wpcie_file_header_error_label(){
	return esc_html__( 'Wrong file header format, Please Download CSV template as template.', 'wpc-import-export' );
}
function wpcie_file_delimiter_error_label(){
	return esc_html__( 'Something went wrong, Please check your file header or Change your file Delimeter according to your file Delimiter.', 'wpc-import-export' );
}
function wpcie_file_format_error_label(){
	return esc_html__( 'Wrong file upload format, it must .csv format', 'wpc-import-export' );
}
function wpcie_data_process_label(){
	return esc_html__( 'Data Processed.', 'wpc-import-export' );
}
function wpcie_loading_label(){
	return esc_html__( 'Loading...', 'wpc-import-export' );
}
function wpcie_data_process_message_label( $number = 0 ){
	return sprintf( '%d %s', $number, __( 'data is being process, Please wait while processing the file to download.', 'wpc-import-export' ) );
}
function wpcie_license_helper_plugin_dependent_label(){
	$link = filter_var( 'https://www.wpcargo.com/purchase/',  FILTER_SANITIZE_URL );
	$format_link = '<a href="'. $link .'" target="_blank">'.apply_filters( 'wpcwlh_required_label', 'WPTaskForce License Helper' ).'</a>';
	return sprintf( 
		'This plugin requires %s plugin to be active!',
		$format_link
	);
}
function wpcie_wpcargo_plugin_dependent_label(){
	$link = filter_var( 'https://wordpress.org/plugins/wpcargo',  FILTER_SANITIZE_URL );
	$format_link = '<a href="'. $link .'" target="_blank">'.apply_filters( 'wpcargo_required_label', 'WPCargo' ).'</a>';
	return sprintf( 
		'This plugin requires %s plugin to be active!',
		$format_link
	);
}
function wpcie_frontend_manager_plugin_dependent_message(){
	$link = filter_var( 'https://wordpress.org/plugins/wpcargo',  FILTER_SANITIZE_URL );
	$format_link = '<strong>WPCargo Frontend Manager</strong>';
	return sprintf( 
		'This plugin requires %s plugin to be active!',
		$format_link
	);
}
function wpcie_custom_field_plugin_dependent_message(){
	$link = filter_var( 'https://wordpress.org/plugins/wpcargo',  FILTER_SANITIZE_URL );
	$format_link = '<strong>WPCargo Custom Field Add-ons</strong>';
	return sprintf( 
		'This plugin requires %s plugin to be active!',
		$format_link
	);
}
function wpcie_cheating_plugin_dependent_label(){
	return esc_html__('Cheating, uh?', 'wpc-import-export');
}
function wpcie_include_template( $file_name ){
    $file_slug              = strtolower( preg_replace('/\s+/', '_', trim( str_replace( '.tpl', '', $file_name ) ) ) );
    $file_slug              = preg_replace('/[^A-Za-z0-9_]/', '_', $file_slug );
    $custom_template_path   = get_stylesheet_directory().'/wpcargo/wpcargo-import-export/'.$file_name.'.php';
    if( file_exists( $custom_template_path ) ){
        $template_path = $custom_template_path;
    }else{
        $template_path  = WPC_IMPORT_EXPORT_PATH.'templates/'.$file_name.'.php';
        $template_path  = apply_filters( "wpc_ie_locate_template_{$file_slug}", $template_path );
    }
	return $template_path; 
}
function wpcie_admin_include_template( $file_name ){
    $file_slug              = strtolower( preg_replace('/\s+/', '_', trim( str_replace( '.tpl', '', $file_name ) ) ) );
    $file_slug              = preg_replace('/[^A-Za-z0-9_]/', '_', $file_slug );
    $custom_template_path   = get_stylesheet_directory().'/wpcargo/wpcargo-import-export/admin/'.$file_name.'.php';
    if( file_exists( $custom_template_path ) ){
        $template_path = $custom_template_path;
    }else{
        $template_path  = WPC_IMPORT_EXPORT_PATH.'admin/templates/'.$file_name.'.php';
        $template_path  = apply_filters( "wpc_ie_locate_admin_template_{$file_slug}", $template_path );
    }
	return $template_path; 
}