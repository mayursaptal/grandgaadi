<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
function wpcargo_pod_dashboard_callback( $shipment_id ){
	$assigned_driver 	= get_post_meta( $shipment_id, 'wpcargo_driver', true );
	$signature 			= get_post_meta( $shipment_id, 'wpcargo-pod-signature', true );	
	$images 			= get_post_meta( $shipment_id, 'wpcargo-pod-image', true);
	require_once( WPCARGO_POD_PATH.'templates/assigned-driver.tpl.php');
}
// Add script in the Dashboard script
function wpcargo_pod_dashboard_registered_styles( $styles ){
	$styles[] = 'wpcargo-pod-dashboard-style';
	return $styles;
}
// Add script in the Dashboard script
function wpcargo_pod_dashboard_registered_scripts( $script ){
	$script[] = 'wpcargo-pod-signature-scripts';
	$script[] = 'wpcargo-pod-scripts';
	return $script;
}
// Add Shipment table header "Sign"
function wpcargo_pod_dashboard_table_header_action(){
	echo '<th class="text-center">'.apply_filters( 'pod_table_header_sign_label', _e('Sign', 'wpcargo-pod' ) ).'</th>';
}
function wpcargo_pod_dashboard_table_table_action( $shipment_id ){
	$signature = get_post_meta($shipment_id, 'wpcargo-pod-signature', true);
	$btn_label = apply_filters( 'pod_table_header_sign_label', __('Sign', 'wpcargo-pod' ) );
	$btn_color = 'btn-outline-info';
	if( $signature ){
		$btn_label = apply_filters( 'pod_table_header_signed_label', __('Signed', 'wpcargo-pod' ) );
		$btn_color = 'btn-outline-blue-grey';
	}
	echo '<td class="text-center"><button type="button" class="show-signaturepad btn '.$btn_color.' btn-rounded btn-small waves-effect px-3 py-1" data-toggle="modal" data-target="#pod-modal" data-id="'.$shipment_id.'">'.$btn_label.'</button></td>';
}
function wpcargo_pod_after_admin_page_load_action(){
	?>
	<!-- Modal -->
	<div class="modal fade top" id="pod-modal" tabindex="-1" role="dialog" aria-labelledby="podModalPreview" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-frame modal-top" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="podModalPreview"><?php echo apply_filters( 'pod_modal_title', __('Proof of Delivery', 'wpcargo-pod' ) ) ?></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body my-4">
					<?php _e('Loading...', 'wpcargo-pod' ); ?>
				</div>
			</div>
		</div>
	</div>
	<!-- Modal -->
	<?php
	do_action('wpcargo_pod_after_sign_modal');
}
function wpcargo_pod_show_shignaturepad() {
	global $wpcargo;
	$shipment_id 				= $_POST['sid'];
	$options  					= get_option('wpcargo_pod_option_settings');
	$shipper_selected_option 	= array();
	$receiver_selected_option 	= array();
	$wpcargo_get_status = get_post_meta( $shipment_id, 'wpcargo_status', true );
	if( !empty($options) && array_key_exists('shipper_fields', $options) ){
		$shipper_selected_option = $options['shipper_fields'];
	}
	if( !empty($options) && array_key_exists('receiver_fields', $options) ){
		$receiver_selected_option = $options['receiver_fields'];
	}
	if ( !current_user_can('upload_files') ) {
		$user = get_role('wpcargo_driver');
		$user->add_cap('upload_files');
	}	
    ob_start();
	require_once( WPCARGO_POD_PATH.'templates/wpc-pod-sign.tpl.php');
	$output = ob_get_clean();
	echo $output;
    wp_die( );
}
function wpcargo_pod_signed_load_action(){
	global $wpcargo;
	$current_user 			= wp_get_current_user();
	$options  				= get_option('wpcargo_pod_option_settings');
	$get_curr_history		= array();
	$wpcargo_shipmentID		= $_POST['shipmentID'];
	$wpcargo_location		= $_POST['location'];
	$custommeta 			= $_POST['custommeta'];
	
	$wpcargo_update_status 	= $_POST['status'];
				
	$wpcargo_pod_signature	= $_POST['signature'];
	$wpcargo_pod_notes		= $_POST['notes'];
	$wpcargo_get_history 	= get_post_meta( $wpcargo_shipmentID, 'wpcargo_shipments_update', true);
	$unser_shipment_history	= !empty($wpcargo_get_history) ? unserialize($wpcargo_get_history) : array();
	$get_curr_history[] 	= array(
								'date' 			=>	date( $wpcargo->date_format ),
								'time' 			=>	current_time( $wpcargo->time_format ),
								'location'		=>	$wpcargo_location,
								'status'		=>	$wpcargo_update_status,
								'updated-by' 	=> $current_user->ID,
								'updated-name' 	=> $wpcargo->user_fullname($current_user->ID),
								'remarks'		=>	$wpcargo_pod_notes
							);
	$get_curr_history		= apply_filters( 'wpcargo_pod_current_history', $get_curr_history );			
	$serialized_data 		= array_merge(!empty($unser_shipment_history) ? $unser_shipment_history : array(), $get_curr_history);
	update_post_meta( $wpcargo_shipmentID,	'wpcargo_shipments_update',	serialize($serialized_data));
	update_post_meta( $wpcargo_shipmentID,	'wpcargo-pod-signature', $wpcargo_pod_signature);
	update_post_meta( $wpcargo_shipmentID,	'wpcargo_status', $wpcargo_update_status);
	wpcargo_send_email_notificatio( $wpcargo_shipmentID, $wpcargo_update_status );
	do_action( 'wpcpod_after_signed_load_action', $wpcargo_shipmentID, $custommeta );
	do_action( 'wpc_add_sms_notification', $wpcargo_shipmentID );
	wp_die();
}
function wpcargo_pod_assign_driver_save( $shipment_id, $data ){
    if( isset( $data['wpcargo_driver'] ) && (int)$data['wpcargo_driver'] && can_wpcfe_assign_driver() ){
        $old_driver = get_post_meta( $shipment_id, 'wpcargo_driver', true );
		update_post_meta( $shipment_id, 'wpcargo_driver', (int)$data['wpcargo_driver'] );
        // check if the driver is changed Send email notification
        if( $old_driver != (int)$data['wpcargo_driver'] && wpcargo_pod_can_send_email_driver() ){
            wpcargo_assign_shipment_email( $shipment_id, (int)$data['wpcargo_driver'], _e( 'Driver', 'wpcargo-pod' ) );
        }
    }elseif( isset( $data['wpcargo_driver'] ) && !(int)$data['wpcargo_driver'] ){
        update_post_meta( $shipment_id, 'wpcargo_driver', '' );
    }
}
function wpcargo_pod_assign_driver_dropdown( $shipment_id ){
	$assigned_driver = get_post_meta( $shipment_id, 'wpcargo_driver', true );
	if( !can_wpcfe_assign_driver() ){
		return false;
	}
	?>
	<div class="form-group">
		<div class="select-no-margin">
			<label><?php esc_html_e( 'Driver','wpcargo-pod' ); ?></label>
			<select id="wpcargo_driver" name="wpcargo_driver" class="form-control browser-default mdb-select" >
				<option value=""><?php echo apply_filters( 'pod_assign_vehicle_label', _e( '-- Select Driver --', 'wpcargo-pod' ) ); ?></option>
				<?php foreach( wpcargo_pod_get_drivers() as $driverID => $driver_name ): ?>
					<option value="<?php echo $driverID; ?>" <?php selected( get_post_meta( $shipment_id, 'wpcargo_driver', true ), $driverID ); ?>><?php echo $driver_name; ?></option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>
	<?php
}
function wpcargo_pod_wpcfe_bulk_update_form_fields(){
	?>
	<div class="form-group">
		<div class="select-no-margin">
			<label><?php _e( 'Driver','wpcargo-pod' ); ?></label>
			<select id="wpcargo_driver" name="wpcargo_driver" class="form-control browser-default mdb-select" >
				<option value=""><?php echo apply_filters( 'pod_assign_vehicle_label', __( '-- Select Driver --', 'wpcargo-pod' ) ); ?></option>
				<?php foreach( wpcargo_pod_get_drivers() as $driverID => $driver_name ): ?>
					<option value="<?php echo $driverID; ?>"><?php echo $driver_name; ?></option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>
	<?php
}
function wpcargo_pod_can_send_email_driver(){
	$gen_settings = get_option( 'wpcargo_option_settings' );
	$email_driver = !array_key_exists('wpcargo_email_driver', $gen_settings ) ? true : false;
	return $email_driver;
}
function wpcargo_pod_assign_email_options( $options ){
	?>
	<tr>
		<th><?php esc_html_e( 'Disable Email for Driver?', 'wpcargo-pod' ) ; ?></th>
		<td>
			<input type="checkbox" name="wpcargo_option_settings[wpcargo_email_driver]" <?php  echo ( !empty( $options['wpcargo_email_driver'] ) && $options['wpcargo_email_driver'] != NULL  ) ? 'checked' : '' ; ?> />
		</td>
	</tr>
	<?php
}
// Sidebar Menu
add_action( 'wp_loaded', 'wpcargo_pod_create_pages' );
function wpcargo_pod_create_pages(){
	wpcargo_pod_create_report_page();
	wpcpod_route_page();
	wpcpod_set_driver_access();
}
function wpcargo_pod_generate_page( $post_title, $post_name, $post_content ){
	$page_args    = array(
		'comment_status' => 'closed',
		'ping_status' 	=> 'closed',
		'post_author' 	=> 1,
		'post_date' 	=> date('Y-m-d H:i:s'),
		'post_content' 	=> $post_content,
		'post_name' 	=> $post_name,
		'post_status' 	=> 'publish',
		'post_title' 	=> $post_title,
		'post_type' 	=> 'page',
	);
	$page_id = wp_insert_post( $page_args, false );
	update_post_meta( $page_id, '_wp_page_template', 'dashboard.php' );
	return $page_id;
}
function is_wpcpod_page_exist( $shortcode ){
    global $wpdb;
    $sql = "SELECT `ID` FROM `{$wpdb->prefix}posts` WHERE `post_status` LIKE 'publish' AND `post_type` LIKE 'page' AND `post_content` LIKE %s LIMIT 1";
    return $wpdb->get_var( $wpdb->prepare( $sql, '%'.$shortcode.'%') );
}
function wpcargo_pod_create_report_page(){
	$page_id = is_wpcpod_page_exist( '[wpcpod_report]' );
	if( $page_id ){
		return $page_id;
	}
	$post_title 	= __('Driver Report', 'wpcargo-pod');
	$post_name 		= 'wpcpod-report-order';
	$post_content 	= '[wpcpod_report]';
	return wpcargo_pod_generate_page( $post_title, $post_name, $post_content );
}
function wpcpod_route_page(){
	$page_id = is_wpcpod_page_exist( '[wpcpod_route]' );
	if( $page_id ){
		return $page_id;
	}
	$post_title 	= __('Driver Route Planner', 'wpcargo-pod' );
	$post_name 		= 'wpcpo-route';
	$post_content 	= '[wpcpod_route]';
	return wpcargo_pod_generate_page( $post_title, $post_name, $post_content );
}
function wpcpod_set_driver_access(){
	$dashboard_role = get_option('wpcfe_access_dashboard_role');
	$dashboard_role = !empty( $dashboard_role ) && is_array( $dashboard_role ) ? $dashboard_role : array() ;
	if( !in_array('wpcargo_driver', $dashboard_role) ){
		$dashboard_role[] = 'wpcargo_driver';
		update_option( 'wpcfe_access_dashboard_role', $dashboard_role );
	}
}
function wpcargo_pod_sidebar_menu( $menu_array ){
	if( !function_exists('wpcfe_admin_page') ){
		return false;
    }
    if( wpcpod_route_allowed_user() ){
		$wpcpod_route_class = 'wpcpod-route';
		if( wpcpod_route_page() == get_the_ID() ){
			$wpcpod_route_class .= ' active';
		}
        $menu_array[$wpcpod_route_class] = array(
            'label' => __('Driver Route', 'wpcargo-pod'),
            'permalink' => get_the_permalink( wpcpod_route_page() ),
            'icon' => 'fa-map-o'
        );
    }
	
	if( can_export_wpcpod_report() ){
		$wpcpod_report_class = 'wpcpod-menu';
		if( wpcargo_pod_create_report_page() == get_the_ID() ){
			$wpcpod_report_class .= ' active';
		}
		$menu_array[$wpcpod_report_class] = array(
			'label' => __('Driver Report', 'wpcargo-pod'),
			'permalink' => get_the_permalink( wpcargo_pod_create_report_page() ),
			'icon' => 'fa-cloud-download'
		);
	}

    return $menu_array;
}
add_filter('wpcfe_after_sidebar_menus', 'wpcargo_pod_sidebar_menu', 10, 1 );
function wpcpod_dashboard_route_script_callback(){
	if( empty( get_option('shmap_api') ) || !wpcpod_route_allowed_user() ){
		return false;
	}
	include_once(WPCARGO_POD_PATH.'templates/route-planner-script.php');
}
add_action( 'wpcpod_after_route_planner', 'wpcpod_dashboard_route_script_callback' );
// Driver Report page restriction
function driver_report_page_restriction(){
	global $post;
	if( wpcargo_pod_create_report_page() == $post->ID && !can_export_wpcpod_report() ){
		$dashboard = get_the_permalink( wpcfe_admin_page() );
		wp_redirect( $dashboard );
        die;
	}
}
add_action( 'template_redirect', 'driver_report_page_restriction' );
function wpcpod_after_sign_popup_form_callback(){
    $shmap_api          		= get_option('shmap_api');
    $shmap_country_restrict     = get_option('shmap_country_restrict');
    if( empty( $shmap_api ) ){
    	return;
    }
    ?>
    <script>
    /*
    ** Google map Script Auto Complete location
    */   
    function wpcpodGetPlaceDynamic() {
        var defaultBounds = new google.maps.LatLngBounds(
            new google.maps.LatLng(-33.8902, 151.1759),
            new google.maps.LatLng(-33.8474, 151.2631)
        );
        var input = document.getElementsByClassName('wpcargo-pod-location');
        var options = {
            bounds: defaultBounds,
            types: ['geocode'],
            <?php if( !empty( $shmap_country_restrict ) ): ?>
                componentRestrictions: {country: "<?php echo $shmap_country_restrict; ?>"}
            <?php endif; ?>
        };
        for (i = 0; i < input.length; i++) {
            autocomplete = new google.maps.places.Autocomplete(input[i], options);
        }
        <?php do_action( 'wpcpod_after_get_dynamic_place' ); ?>
    }
    </script>
    <?php
    echo wpcargo_map_script( 'wpcpodGetPlaceDynamic' );
}
add_action( 'wpcpod_after_sign_popup_form', 'wpcpod_after_sign_popup_form_callback', 10 );