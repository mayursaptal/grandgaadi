<?php
if (!defined('ABSPATH')){
    exit; // Exit if accessed directly
}
//** Helpers
function wpcsc_get_container_history( $container_id ){
	$history = maybe_unserialize( get_post_meta( $container_id, 'container_history', true ) );
	return !empty( $history ) && is_array( $history ) ? wpcargo_history_order( $history ) : array() ;
}
function wpcsc_allowed_users(){
	return apply_filters( 'wpcsc_allowed_users', array('administrator', 'wpcargo_employee') );
}
function can_access_containers(){
	$current_user 	= wp_get_current_user();
	$roles 			= $current_user->roles;
	if( !empty( array_intersect( $roles, wpcsc_allowed_users() ) ) ){
		return true;
	}
	return false;
}
function wpcsc_generate_number(){
	global $wpdb;
	$autogen 	= get_option('enable_container_autogen');
	if( !$autogen ){
		return false;
	}
	$prefix 	= esc_html__( get_option('container_prefix') );
	$numdigit  	= apply_filters( 'wpcsc_generate_number_digit', 12 );
	$numstr 	= '';
	for ( $i = 1; $i < $numdigit; $i++ ) {
		$numstr .= 9;
	}
	$container_number = $prefix.str_pad( wp_rand( 0, $numstr ), $numdigit, "0", STR_PAD_LEFT );
	if( wpcsc_generate_number_exist( $container_number ) ) {
		$container_number = wpcsc_generate_number();
	}
	return apply_filters( 'wpcsc_generate_number', $container_number );
}
function wpcsc_generate_number_exist( $container_number = '' ){
	global $wpdb;
	$result =  $wpdb->get_var( "SELECT COUNT(*) FROM `{$wpdb->prefix}posts` WHERE `post_type` LIKE 'shipment_container' AND `post_title` LIKE '".$container_number."'" );
	return $result;
}
function wpc_container_frontend_page(){
	global $wpdb;
	$sql 			= "SELECT `ID` FROM {$wpdb->prefix}posts WHERE `post_content` LIKE '%[wpcargo-container]%' AND `post_status` LIKE 'publish' LIMIT 1";
	$shortcode_id 	= $wpdb->get_var( $sql );
	if( ! $shortcode_id ){
		// Create post object
		$continer_args = array(
			'post_title'    => wp_strip_all_tags( __('Containers', 'wpcargo-shipment-container') ),
			'post_content'  => '[wpcargo-container]',
			'post_status'   => 'publish',
			'post_type'   	=> 'page',
		);
		
		// Insert the post into the database
		$shortcode_id = wp_insert_post( $continer_args );		
	}
	if( $shortcode_id ){
		update_post_meta( $shortcode_id, '_wp_page_template', 'dashboard.php');
		update_post_meta( $shortcode_id, 'wpcfe_menu_icon', 'fa fa-truck mr-3');
	}
	return $shortcode_id;
}
function wpcsc_get_shipment_container( $shipment_id ){
	global $wpdb;
	$sql 	= "SELECT tbl2.meta_value FROM `{$wpdb->prefix}posts` AS tbl1 
		LEFT JOIN  `{$wpdb->prefix}postmeta` AS tbl2 ON tbl2.post_id = tbl1.ID 
	WHERE 
		tbl1.post_status LIKE 'publish' 
		AND tbl1.post_type LIKE 'wpcargo_shipment' 
		AND tbl1.ID = %d 
		AND tbl2.meta_key LIKE 'shipment_container' 
	";
	$sql 	= $wpdb->prepare( $sql, $shipment_id);
	$result = $wpdb->get_var( $sql );
	return $result;
}
function wpcsc_get_container_id( $container_number ){
	global $wpdb;
	$sql 	= $wpdb->prepare("SELECT `ID` FROM `{$wpdb->prefix}posts` WHERE `post_status` LIKE 'publish' AND `post_type` LIKE 'shipment_container' AND `post_title` LIKE %s LIMIT 1", $container_number);
	$result = $wpdb->get_var( $sql );
	return $result;
}
function wpcsc_get_container_number( $container_id ){
	global $wpdb;
	$sql 	= $wpdb->prepare("SELECT `post_title` FROM `{$wpdb->prefix}posts` WHERE `post_status` LIKE 'publish' AND `post_type` LIKE 'shipment_container' AND `ID` = %d", $container_id);
	$result = $wpdb->get_var( $sql );
	return $result;
}
// Can update shipments
function wpcsc_update_shipment_role(){
	if( !class_exists('WPCargo_Frontend_Template') ){
		return array();
	}
    $update_shipment_role = get_option( 'wpcfe_update_shipment_role' ) ? get_option( 'wpcfe_update_shipment_role' ) : array( 'wpcargo_employee' ) ;
    return $update_shipment_role;
}
function wpcsc_can_update_shipment(){
	$can_update_roles 	= wpcsc_update_shipment_role();
	$user 				= wp_get_current_user();
	$result 			= false;
	$current_role 		= !empty($user->roles) ? $user->roles : array();
	if( array_intersect( $can_update_roles, $current_role ) || in_array( 'administrator', $current_role ) ){
		$result = true;
	}
	return $result;
}
function wpcsc_shipment_bulk_container_assign(){
	?>
	<button id="bulkContainerAssign" class="btn btn-success btn-sm" data-toggle="modal" data-target="#shipmentBulkContainerModal"><?php esc_html_e('Assign to Container', 'wpcargo-shipment-container'); ?></button>
	<?php
}
function wpcsc_current_user_role(){
    $current_user   = wp_get_current_user();
    $user_roles     = $current_user->roles;
    return $user_roles;
}
function wpcsc_include_template( $file_name, $dir = '' ){
	$file_slug              = strtolower( preg_replace('/\s+/', '_', trim( str_replace( '.tpl', '', $file_name ) ) ) );
    $file_slug              = preg_replace('/[^A-Za-z0-9_]/', '_', $file_slug );
	$dir 					= $dir ? $dir.'/' : '';
    $custom_template_path   = get_stylesheet_directory().'/wpcargo/wpcargo-shipment-container/'.$dir.$file_name.'.php';
    if( file_exists( $custom_template_path ) ){
        $template_path = $custom_template_path;
    }else{
        $template_path  = WPCARGO_SHIPMENT_CONTAINER_PATH.'templates/'.$dir.$file_name.'.php';
        $template_path  = apply_filters( "wpcsc_locate_template_{$file_slug}", $template_path );
    }
	return $template_path;
}
function wpcsc_admin_include_template( $file_name, $dir = '' ){
	$file_slug              = strtolower( preg_replace('/\s+/', '_', trim( str_replace( '.tpl', '', $file_name ) ) ) );
    $file_slug              = preg_replace('/[^A-Za-z0-9_]/', '_', $file_slug );
	$dir 					= $dir ? $dir.'/' : '';
    $custom_template_path   = get_stylesheet_directory().'/wpcargo/wpcargo-shipment-container/admin/'.$dir.$file_name.'.php';
    if( file_exists( $custom_template_path ) ){
        $template_path = $custom_template_path;
    }else{
        $template_path  = WPCARGO_SHIPMENT_CONTAINER_PATH.'admin/templates/'.$dir.$file_name.'.php';
        $template_path  = apply_filters( "wpcsc_admin_locate_template_{$file_slug}", $template_path );
    }
	return $template_path;
}
/* Display custom column */
function wpc_shipment_container_table_column_display_callback( $column, $post_id ) {
	global $wpcargo;
    if ( $column == 'flight' ){
        echo get_post_meta( $post_id, 'container_no', TRUE );
    }
    if( $column == 'shipments' ){
    	$shipment_count = wpcshcon_shipment_count( $post_id );
		echo $shipment_count 
		? '<a href="#" class="text-info" data-id="'.$post_id.'"><span class="dashicons dashicons-list-view"></span> '.sprintf( _n( '%s Shipment', '%s Shipments', $shipment_count, 'wpcargo-shipment-container' ), $shipment_count ).'</a>' 
		: '';
    }
    if( $column == 'agent' ){
    	echo get_post_meta( $post_id, 'container_agent', TRUE );
    }
    if( $column == 'delivery_agent' ){
    	echo get_post_meta( $post_id, 'delivery_agent', TRUE );
    }
    if( $column == 'status' ){
    	echo get_post_meta( $post_id, 'container_status', TRUE );
    }
    if( $column == 'scprint' ){
    	echo '<a href="'.admin_url( 'admin.php?page=print-shipment-container&id='.$post_id ).'" target="_blank"><span class="dashicons dashicons-printer"></span></a>';
    }
    if( $column == 'scmanifest' ){
    	echo '<a href="'.admin_url( '/?wpcscpdf='.$post_id ).'"><span class="dashicons dashicons-download"></span></a>';
    }
}
add_action( 'manage_shipment_container_posts_custom_column' , 'wpc_shipment_container_table_column_display_callback', 10, 2 );

/* Add custom column to post list */
function wpcsc_datatable_info_callback(){
	$shipper_display 	= get_option('container_shipper_display');
	$receiver_display 	= get_option('container_receiver_display');
	
	if( empty( $shipper_display ) ){
		$shipper_display 	= 'wpcargo_shipper_name';
		$shipper_label		= __( 'Shipper', 'wpcargo-shipment-container' );
	}else{
		$shipper_label		= wpc_shipment_container_get_field_label( $shipper_display );
	}
	if( empty( $receiver_display ) ){
		$receiver_display 	= 'wpcargo_receiver_name';
		$receiver_label		= __( 'Receiver', 'wpcargo-shipment-container' );
	}else{
		$receiver_label 	= wpc_shipment_container_get_field_label( $receiver_display );
	}

	$datatable = array( 
		'shipping_no' 		=> __( 'Shipping NO.', 'wpcargo-shipment-container' ),
		$shipper_display	=> $shipper_label,
		$receiver_display  	=> $receiver_label,
		'registered_to' 	=> __( 'Client', 'wpcargo-shipment-container' ),
		'agent' 			=> __( 'Agent', 'wpcargo-shipment-container' ),
	);
	return apply_filters( 'wpcsc_datatable_info_callback', $datatable );
}
function wpc_shipment_container_key_label_header_callback(){
	$key_header = array( 
		'flight' 	=> __( 'Flight/ Container No.', 'wpcargo-shipment-container' ),
		'shipments'	=> __( 'Shipments', 'wpcargo-shipment-container' ),
		'agent'		=> __( 'Agent', 'wpcargo-shipment-container' ),
		'delivery_agent'	=> __( 'Driver', 'wpcargo-shipment-container' ),
		'status'	=> __( 'Status', 'wpcargo-shipment-container' ),
		'scprint'	=> __( 'Print','wpcargo-shipment-container' ),
		'scmanifest'=> __( 'Manifest', 'wpcargo-shipment-container' ),
	);
	return apply_filters( 'wpcsc_table_key_label', $key_header );
}
function wpc_shipment_container_table_column_callback( $columns ) {
    return array_merge( $columns, wpc_shipment_container_key_label_header_callback() );
}
add_filter( 'manage_shipment_container_posts_columns' , 'wpc_shipment_container_table_column_callback' );

function wpc_shipment_container_get_all_user( $user_field = '' ){
	$users = apply_filters( 'wpcsc_container_user', get_users( array( 'role' => array( $user_field ) ) ), $user_field );
	return $users;
}
//** Template hooks
function container_track_form_title_callback(){
	echo '<h3 id="container-trackform-header">'. apply_filters( 'wpc_container_trackform_header', __( 'Enter Container No.', 'wpcargo-shipment-container' ) ).'</h3>';
}
function container_track_form_description_callback(){
	echo  '<p class="description">'. apply_filters( 'wpc_container_trackform_description', __( 'Ex. CO123456', 'wpcargo-shipment-container' ) ) .'</p>';
}
add_action( 'container_track_form_title', 'container_track_form_title_callback', 1, 1 );
add_action( 'container_track_form_description', 'container_track_form_description_callback', 1, 1 );

function wpc_shipment_container_get_user_name( $userID = '' ){
	global $wpcargo;
	return $wpcargo->user_fullname( $userID );
}
function wpcsc_get_field_data( $fieldID ){
	global $wpdb;
	$table_prefix = $wpdb->prefix;
	$result_fields = $wpdb->get_row( 'SELECT `label`, `field_key`, `field_type` FROM `'.$table_prefix.'wpcargo_custom_fields` WHERE `id` = '.$fieldID, ARRAY_A );
	return $result_fields;
}

function wpc_shipment_container_get_assigned_shipment( $postID ){
	global $wpdb;
	$sql = "SELECT tbl1.ID FROM {$wpdb->prefix}posts AS tbl1 ";
	$sql .= "RIGHT JOIN {$wpdb->prefix}postmeta as tbl2 ON tbl1.ID = tbl2.post_id ";
	$sql .= "WHERE tbl1.post_status LIKE 'publish' AND tbl1.post_type LIKE 'wpcargo_shipment' ";
	$sql .= " AND tbl2.meta_key LIKE 'shipment_container' ";
	$sql .= " AND tbl2.meta_value = %d ";
    $result = $wpdb->get_col( $wpdb->prepare( $sql, $postID ) );
	$sorted_shipment 	= array();
	$sorted_shipment_list = !empty( trim( wpc_shipment_container_sorted_shipment( $postID ) ) ) ? explode(",", wpc_shipment_container_sorted_shipment( $postID ) ) : array() ;
	if( !empty( $result ) ){
		$counter = 0;
		foreach ( $result as $shipmentID ) {
			$key = array_search( $shipmentID, $sorted_shipment_list );
			if( $key !== FALSE ){
				$sorted_shipment[$key] = $shipmentID;
				continue;
			}
			$sorted_shipment[99999+$counter] = $shipmentID;
			$counter++;
		}
	}
	// ksort( $sorted_shipment );
	return $sorted_shipment;
}
function wpcshcon_shipment_count( $postID ){
	global $wpdb;
	$sql = "SELECT count(tbl1.ID) FROM {$wpdb->prefix}posts AS tbl1 ";
	$sql .= "RIGHT JOIN {$wpdb->prefix}postmeta as tbl2 ON tbl1.ID = tbl2.post_id ";
	$sql .= "WHERE tbl1.post_status LIKE 'publish' AND tbl1.post_type LIKE 'wpcargo_shipment' ";
	$sql .= " AND tbl2.meta_key LIKE 'shipment_container' ";
	$sql .= " AND tbl2.meta_value = %d ";
    return $wpdb->get_var( $wpdb->prepare( $sql, $postID ) );
}
function wpc_shipment_container_sorted_shipment( $postID ){
	return get_post_meta( $postID, 'wpcc_sorted_shipments', true );
}
function wpc_shipment_container_get_custom_fields( $flag = '' ){
	global $wpdb;
	$table_prefix = $wpdb->prefix;
	$result_fields = $wpdb->get_results( 'SELECT * FROM `'.$table_prefix.'wpcargo_custom_fields` WHERE `section` LIKE "'.$flag.'" ORDER BY ABS(weight)', ARRAY_A );
	return $result_fields;
}
function wpc_shipment_container_get_field_label( $key = '' ){
	global $wpdb;
	$table_prefix = $wpdb->prefix;
	$result = '';
	if( !empty($key) || $key != '' ){
		$result= $wpdb->get_var( 'SELECT `label` FROM `'.$table_prefix.'wpcargo_custom_fields` WHERE `field_key` LIKE "'.$key.'" LIMIT 1' );
	}
	return $result;
}
function wpcsc_datatable_unassigned_shipment( $container_id ){
	global $wpcargo;
	$shipments 			= wpc_shipment_container_get_unassigned_shipment();
	$datatable 			= array_keys( wpcsc_datatable_info_callback() );
	$icons 				= !is_admin() ? 'fa fa-plus-circle fa-lg' : 'dashicons dashicons-plus-alt';
	$data = array();
    if( !empty($shipments) ){
        foreach ($shipments as $shipment) {
			$shipment_array =  array(
				'id' 			=> $shipment,
				"DT_RowId"      => "opt_".$shipment,
                'actions' 		=> '<span class="shipment-assign-icon text-info '.$icons.'" data-id="'.$shipment.'" data-ctn="'.$container_id.'"></span>',
			);
			if( !empty( $datatable ) ){
				foreach ($datatable as $meta_key ) {
					$value = get_post_meta( $shipment, $meta_key, true );
					if( $meta_key == 'registered_to' ){
						$value = $wpcargo->user_fullname( get_post_meta( $shipment, 'registered_shipper', true ) );
					}elseif( $meta_key == 'agent' ){
						$value = $wpcargo->user_fullname( get_post_meta( $shipment, 'agent_fields', true ) );
					}elseif( $meta_key == 'shipping_no' ){
						$value = get_the_title( $shipment );
					}
					$shipment_array[$meta_key] = $value;
				}
			}
			$data[] = $shipment_array;
        }
    }
	return $data;
}
function wpc_shipment_container_get_unassigned_shipment( ){
	global $wpdb;
	$assigned_shipments = get_option('container_assigned_shipments') ? get_option('container_assigned_shipments') : array() ;
	$assigned_shipments = array_map( function( $value ){
		return str_replace( array( "'", '"'), ' ', $value  );
	}, $assigned_shipments );
	$shipment_status = '';
	if( !empty( $assigned_shipments ) ){
		$shipment_status = implode("','", $assigned_shipments );
	}else{
		$shipment_status = 'Pending';
	}
	$sql = "SELECT tbl1.ID FROM `$wpdb->posts` tbl1 
	LEFT JOIN `$wpdb->postmeta` tbl2 ON tbl1.ID=tbl2.post_id AND tbl2.meta_key='shipment_container' 
	LEFT JOIN `$wpdb->postmeta` tbl3 ON tbl1.ID=tbl3.post_id AND tbl3.meta_key='wpcargo_status' 
	WHERE post_status='publish' AND post_type='wpcargo_shipment' 
	AND ( tbl2.meta_key IS NULL OR tbl2.meta_value LIKE '' )
	 AND tbl3.meta_value IN ('".$shipment_status."') 
	 ORDER BY tbl1.post_title ASC";
	$sql = apply_filters( 'wpcsc_get_unassigned_shipment_sql', $sql );
	$result = $wpdb->get_col( $sql );
	return $result;
}
function wpc_shipment_container_get_all_unassigned_shipment( ){
	global $wpdb;
	$assigned_shipments = get_option('container_assigned_shipments');
	$shipment_status = '';
	if( !empty( $assigned_shipments ) ){
		$shipment_status = implode("','", $assigned_shipments );
	}else{
		$shipment_status = 'Pending';
	}
	$sql = "SELECT count( tbl1.ID ) FROM `$wpdb->posts` tbl1 LEFT JOIN `$wpdb->postmeta` tbl2 ON tbl1.ID=tbl2.post_id AND tbl2.meta_key='shipment_container' LEFT JOIN `$wpdb->postmeta` tbl3 ON tbl1.ID=tbl3.post_id AND tbl3.meta_key='wpcargo_status' WHERE post_status='publish' AND post_type='wpcargo_shipment' AND ( tbl2.meta_key IS NULL OR tbl2.meta_value LIKE '' ) AND tbl3.meta_value IN ('".$shipment_status."')";
	$sql = apply_filters( 'wpcsc_get_all_unassigned_shipment_sql', $sql );
	$result = $wpdb->get_var( $sql );
	
	return $result;
}
function wpc_shipment_container_get_paged_shipment( $offset, $items_per_page ){
	global $wpdb;
	$assigned_shipments = get_option('container_assigned_shipments');
	$shipment_status = '';
	if( !empty( $assigned_shipments ) ){
		$shipment_status = implode("','", $assigned_shipments );
	}else{
		$shipment_status = 'Pending';
	}
	$sql = "SELECT tbl1.ID FROM `$wpdb->posts` tbl1 LEFT JOIN `$wpdb->postmeta` tbl2 ON tbl1.ID=tbl2.post_id AND tbl2.meta_key='shipment_container' LEFT JOIN `$wpdb->postmeta` tbl3 ON tbl1.ID=tbl3.post_id AND tbl3.meta_key='wpcargo_status' WHERE post_status='publish' AND post_type='wpcargo_shipment' AND ( tbl2.meta_key IS NULL OR tbl2.meta_value LIKE '' ) AND tbl3.meta_value IN ('".$shipment_status."') ORDER BY ID DESC LIMIT ".$offset.", ".$items_per_page;
	$sql = apply_filters( 'wpcsc_get_paged_shipment_sql', $sql );
	$result = $wpdb->get_results( $sql, OBJECT );
	return $result;
}
function wpc_shipment_container_get_user_fullname( $userID ){
	$user_info = get_userdata( $userID );
	$fullname = '';
	if( !empty( $user_info->first_name ) && !empty($user_info->last_name) ){
		$fullname = ucfirst( $user_info->first_name ). ' ' . ucfirst( $user_info->last_name );
	}else{
		$fullname = $user_info->user_email;
	}
	return $fullname;
}
function get_shipment_container_post(){
	$args = array(
		'post_type'         => 'shipment_container',
		'post_status'       => 'publish',
		'posts_per_page' 	=> -1
	);
	$container_post = new WP_Query( $args );	
	return $container_post;
}
function get_shipment_containers(){
	global $wpdb;
	$sql 		= "SELECT ID, `post_title` FROM `{$wpdb->prefix}posts` WHERE `post_type` LIKE 'shipment_container' AND `post_status` LIKE 'publish' ORDER BY `post_title` ASC";
	$results 	= $wpdb->get_results( $sql );
	return $results;
}
add_action( 'quick_edit_custom_box', 'wpc_shipment_container_bulk_update_status', 10, 2 );
add_action( 'bulk_edit_custom_box', 'wpc_shipment_container_bulk_update_status', 10, 2 );
function wpc_shipment_container_bulk_update_status( $column_name,  $screen_post_type ){
	global $wpcargo, $post;
 	if( $screen_post_type == 'shipment_container'  ){
	    wp_nonce_field( 'container_bulk_update_action', 'container_bulk_update_nonce' );
	    if( $column_name == 'status' ){
			require( wpcsc_admin_include_template( 'quick-edit-history.tpl' ) );
		}
		if( $column_name == 'delivery_agent' ){
			require( wpcsc_admin_include_template( 'quick-edit-agent.tpl' ) );
		}
 	}
}
add_action( 'save_post', 'wpc_shipment_container_bulk_save' );

function wpc_shipment_container_bulk_save( $post_id ) {
	global $wpcargo;
    $slug = 'shipment_container';
    if ( !current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    if( !isset( $_REQUEST["container_bulk_update_nonce"] ) ){
    	return;
    }
    if ( !wp_verify_nonce( $_REQUEST["container_bulk_update_nonce"], 'container_bulk_update_action' ) ){
        return;
    }
    $current_user 	= wp_get_current_user();
	$history_fields = wpcargo_history_fields();
	if ( isset( $_REQUEST['_wpcsh_status'] ) && $_REQUEST['_wpcsh_status'] != '' ) {
	    $selected_status 	= trim( sanitize_text_field( $_REQUEST['bulk_container_status'] ) );
		$apply_to_shipment 	= ( isset($_REQUEST['apply_shipment']) ) ? true : false ;
		$current_chistory 	= wpcsc_get_container_history( $post_id );
		$shipments 			= wpc_shipment_container_get_assigned_shipment( $post_id );
		$container_history 	= array();	
		foreach( $history_fields as $field_key => $field_value ){
			$_value = isset( $_REQUEST[ '_wpcsh_'.$field_key] ) ? trim( sanitize_text_field( $_REQUEST[ '_wpcsh_'.$field_key ] ) ) : '';
			if( 'updated-name' == $field_key ){
				$_value = $wpcargo->user_fullname( get_current_user_id() );
			}
			$container_history[$field_key] = $_value;
		}
		array_push( $current_chistory, $container_history );
		update_post_meta( $post_id, 'container_status', $selected_status );	
		update_post_meta( $post_id, 'container_history', $current_chistory );
		if( $apply_to_shipment ){		
			if( !empty( $shipments ) ){
				foreach ($shipments as $shipment_id) {
					$old_status = get_post_meta( $shipment_id, 'wpcargo_status', true );
					update_post_meta( $shipment_id, 'wpcargo_status', $selected_status );
					$shipment_history 		= maybe_unserialize( get_post_meta( $shipment_id,'wpcargo_shipments_update',true) );							
					if( !empty( $shipment_history ) ){
						array_push( $shipment_history, $container_history );
						update_post_meta( $shipment_id, 'wpcargo_shipments_update', $shipment_history );
					}else{
						update_post_meta( $shipment_id, 'wpcargo_shipments_update', array( $container_history ) );
					}
					// Add Report Records
					if( function_exists('wpcfe_save_report') ){
						wpcfe_save_report( $shipment_id, $old_status, sanitize_text_field($selected_status) );
					}
					// Send Email Notification
					if( $selected_status != $old_status ){	
						wpcargo_send_email_notificatio( $shipment_id, $selected_status );
					}
				}
			}
		}
	}
	if ( isset( $_REQUEST['wpcsc_delivery_agent'] ) && $_REQUEST['wpcsc_delivery_agent'] != '' ) {
	    $delivery_agent  = $_REQUEST['wpcsc_delivery_agent'];
		update_post_meta( $post_id, 'delivery_agent', $delivery_agent );
		$apply_to_driver = ( isset($_REQUEST['apply_driver']) ) ? true : false ;
		if( $apply_to_driver ){
			if( !empty( $shipments ) ){
				foreach ($shipments as $shipment_id) {
					update_post_meta( $shipment_id, 'wpcargo_driver', (int)$delivery_agent );					
				}
			}
		}
	}
}
function wpcsc_save_history( $container_id, $data ){
	global $wpcargo;
	//** Get the container status before update
	$drivers 			= wpcargo_pod_get_drivers();
	$shipments 			= wpc_shipment_container_get_assigned_shipment( $container_id );
    $current_status 	= get_post_meta( $container_id, 'container_status', TRUE );
    $selected_status 	= trim( sanitize_text_field( $data['_wpcsh_status'] ) );
    $delivery_agent  	= trim( sanitize_text_field( $data['delivery_agent'] ) );
    $sorted_shipments   = trim( sanitize_text_field( $data['wpcc_sorted_shipments'] ) );
    $apply_to_shipment  = isset( $data['apply_shipment'] );
    $driver_id 	        = array_search( $delivery_agent, $drivers );
    if( $driver_id && !empty( $shipments ) ){
        foreach ( $shipments as $shipment_id ) {
            update_post_meta( $shipment_id, 'wpcargo_driver', (int)$driver_id );
        }
    }
    // Sorted Shipment
    update_post_meta( $container_id, 'wpcc_sorted_shipments',$sorted_shipments  );
    //  Get latest data for the shipment history
    $current_shipment_history 		= array( );
    if( !empty( wpcargo_history_fields() ) ){
        foreach ( wpcargo_history_fields() as $key => $value) {
            if( $key == 'updated-name' ){
                $current_shipment_history[$key] = $wpcargo->user_fullname( get_current_user_id() );
                continue;
			}
			$value = array_key_exists( '_wpcsh_'.$key,  $data ) ? $data['_wpcsh_'.$key] : '' ;
            $current_shipment_history[$key] = trim( sanitize_text_field( $value ) );
        }
	}
    // Get latest data for the current container history
	$current_container_history  = array_key_exists( 'container_history',  $data ) ? $data['container_history'] : array() ; 
    //** Update the Container status when the Container History is update
    if( $selected_status ){
        //** Add new container history if the current status is not equal to selected status
        update_post_meta( $container_id, 'container_status', $selected_status );    
        $current_container_history = array_reverse($current_container_history); 
        array_push( $current_container_history, $current_shipment_history );

        //** if syn is enable update shipment status ang history
        if( $apply_to_shipment ){
        
            if( !empty( $shipments ) ){
                foreach ($shipments as $shipment_id) {
					$old_status         = get_post_meta( $shipment_id, 'wpcargo_status', true );
                    update_post_meta( $shipment_id, 'wpcargo_status', $selected_status );
                    $shipment_history 	= maybe_unserialize( get_post_meta( $shipment_id,'wpcargo_shipments_update',true) );
                    
                    if( !empty( $shipment_history ) ){
                        array_push( $shipment_history, $current_shipment_history );
                        update_post_meta( $shipment_id, 'wpcargo_shipments_update', $shipment_history );
                    }else{
                        update_post_meta( $shipment_id, 'wpcargo_shipments_update', array( $current_shipment_history ) );
                    }
                    // Add Report Records
					if( function_exists('wpcfe_save_report') ){
						wpcfe_save_report( $shipment_id, $old_status, sanitize_text_field($selected_status) );
					}
					// Send Email Notification
                    if( $selected_status != $old_status ){	
                        wpcargo_send_email_notificatio( $shipment_id, $selected_status );
                        do_action( 'wpc_add_sms_shipment_history', $shipment_id );
                    }
                    
                }
            }
        }	
	}   
	update_post_meta( $container_id, 'container_history', $current_container_history ); 
}
function wpcapi_extract_container_data( $containers ){
	global $wpcargo;
	$container_data 	= array();
    $counter 			= 0;
	$registered_fields 	= array_merge( wpc_container_info_fields(), wpc_trip_info_fields(), wpc_time_info_fields() );
    foreach ( $containers as $key => $value ) {
		$assigned_shipment = wpc_shipment_container_get_assigned_shipment( $value['ID'] );
		$container_data[$counter]['ID'] 				= $value['ID'];
		$container_data[$counter]['container_number'] 	= $value['container_number'];
		$container_data[$counter]['post_author'] 		= $value['post_author'];
		$container_data[$counter]['post_date'] 			= $value['post_date'];
		$container_data[$counter]['post_date_gmt'] 		= $value['post_date_gmt'];
		$container_data[$counter]['post_modified'] 		= $value['post_modified'];
		$container_data[$counter]['post_modified_gmt'] 	= $value['post_modified_gmt'];
		if( !empty( $registered_fields ) ){
			foreach ($registered_fields as $field_key => $field_value ) {
				$_value = maybe_unserialize( get_post_meta( $value['ID'], $field_key, true ) );
				if( is_array( $_value ) ){
					$_value = implode(",", $_value);
				}
				$container_data[$counter][$field_key] = $_value;
			}
		}
		$container_data[$counter]['container_history'] = maybe_unserialize( get_post_meta( $value['ID'], 'container_history', true ) );
		$container_data[$counter]['assigned_shipment'] = array_map( function( $shipment ){
			return get_the_title( $shipment );
		}, $assigned_shipment );
		$container_data[$counter] = apply_filters( 'wpcargo_api_container_data', $container_data[$counter], $value['ID'] );
		$counter++;
	}
	return apply_filters( 'wpcargo_api_containers_data', $container_data );
}

function wpcapi_get_user_containers( $userID, $page = 1, $all = false ){
	global $wpdb, $wpcargo;
	$userdata 	= get_userdata( $userID );
    $user_roles = $userdata->roles;
    $per_page 	= 12;
	$offset 	= ( $page - 1) * $per_page;
	$sql 		= '';
	$user_fullname = $wpcargo->user_fullname( $userdata->ID );
	$admin_role = array( 'administrator', 'wpcargo_api_manager', 'wpc_shipment_manager' );
	if( array_intersect( $admin_role, $user_roles ) ){

		$sql .= "SELECT `ID`, `post_title` AS container_number, `post_author`, `post_date`, `post_date_gmt`, `post_modified`, `post_modified_gmt` FROM `{$wpdb->prefix}posts` WHERE `post_status` LIKE 'publish' AND `post_type` LIKE 'shipment_container' ORDER BY `post_date`";

	}elseif( in_array( 'wpcargo_driver', $user_roles ) ){

		$sql .= "SELECT tbl1.ID, tbl1.post_title AS container_number, tbl1.post_author, tbl1.post_date, tbl1.post_date_gmt, tbl1.post_modified, tbl1.post_modified_gmt";
		$sql .= " FROM `{$wpdb->prefix}posts` AS tbl1";
		$sql .= " INNER JOIN `{$wpdb->prefix}postmeta` tbl2 ON tbl1.ID = tbl2.post_id";
		$sql .= " WHERE tbl1.post_status LIKE 'publish' AND tbl1.post_type LIKE 'shipment_container'";
		$sql .= " AND tbl2.meta_key LIKE 'delivery_agent' AND tbl2.meta_value LIKE '{$user_fullname}'";
		$sql .= " ORDER BY tbl1.post_date";

	}elseif( in_array( 'cargo_agent', $user_roles ) ){

		$sql .= "SELECT tbl1.ID, tbl1.post_title AS container_number, tbl1.post_author, tbl1.post_date, tbl1.post_date_gmt, tbl1.post_modified, tbl1.post_modified_gmt";
		$sql .= " FROM `{$wpdb->prefix}posts` AS tbl1";
		$sql .= " INNER JOIN `{$wpdb->prefix}postmeta` tbl2 ON tbl1.ID = tbl2.post_id";
		$sql .= " WHERE tbl1.post_status LIKE 'publish' AND tbl1.post_type LIKE 'shipment_container'";
		$sql .= " AND tbl2.meta_key LIKE 'container_agent' AND tbl2.meta_value LIKE '{$user_fullname}'";
		$sql .= " ORDER BY tbl1.post_date";

	}
	if( !$all && !empty( $sql ) ){
		$sql .= " DESC LIMIT {$per_page} OFFSET {$offset}";
	}
	if( empty( $sql ) ){
		return array();
	}
	$sql 		= apply_filters( 'wpcapi_get_user_containers_api_sql', $sql, $userID, $page, $all );
	$containers = $wpdb->get_results( $sql, ARRAY_A );
	return $containers;
}
function wpcapi_get_unassigned_shipments( $userID ){
	$userdata 			= get_userdata( $userID );
    $user_roles 		= $userdata->roles;
	$admin_roles 		= array( 'administrator', 'wpcargo_api_manager', 'wpc_shipment_manager' );
	$can_assign_roles 	= apply_filters( 'wpcscapi_can_assign_shipments_role', $admin_roles );
	$shipments 			= wpc_shipment_container_get_unassigned_shipment();
	if( empty( array_intersect( $user_roles, $can_assign_roles ) ) ){
		return array(
			'status' => 'error',
			'message' => __( 'Sorry you are restricted to access this route.', 'wpcargo-shipment-container' ) );
	}
	return array_map( function( $shipment ){
		return get_the_title( $shipment );
	}, $shipments );
}