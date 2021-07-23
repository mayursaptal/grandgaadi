<?php
if (!defined('ABSPATH')){
    exit; // Exit if accessed directly
}
//** AJAX hooks
add_action( 'wp_ajax_check_container', 'wpcsc_check_container_callback' );
add_action( 'wp_ajax_bulk_assign_container', 'bulk_assign_container_callback' );
add_action( 'wp_ajax_nopriv_bulk_assign_container', 'bulk_assign_container_callback' );
function bulk_assign_container_callback(){
	$shipment_ids = $_POST['shipmentIDs'];
	$container_id = (int)$_POST['containerID'];
	$shipments = array();
	if( !empty($shipment_ids) && !empty($container_id) ){
		foreach( $shipment_ids as $shipment_id ){
			update_post_meta( $shipment_id, 'shipment_container', $container_id );
			$shipments[] = get_the_title( $shipment_id );
		}
	}
	do_action( 'wpcsc_after_save_bulk_assign_container', $shipment_ids, $_POST );
	echo json_encode( array_unique($shipments) );
	wp_die();
}
function wpcsc_check_container_callback(){
	global $wpdb;
	$container_number = sanitize_text_field( $_POST['containerNumber'] );
	$sql 	= $wpdb->prepare("SELECT `ID` FROM `{$wpdb->prefix}posts` WHERE `post_status` LIKE 'publish' AND `post_type` LIKE 'shipment_container' AND `post_title` LIKE %s LIMIT 1", $container_number);
	$result = $wpdb->get_var( $sql );
	echo $result;
	wp_die();
}
add_action( 'wp_ajax_delete_container', 'wpcsc_delete_container_callback' );
function wpcsc_delete_container_callback(){
	global $wpdb;
	$container_id 	= (int)$_POST['containerID'];
	$container_title = get_the_title( $container_id );
	$message 	 = array(
		'status' => 'warning',
		'icon'	 => 'ti-alert',
		'message' => __( 'Something went wrong during process, Please try again.', 'wpcargo-shipment-container' )
	);
	//if( is_wpcfe_shipment( $shipment_id ) && can_wpcfe_delete_shipment() && is_user_shipment( $shipment_id ) ){
		$delete_post = wp_delete_post( $container_id, false );
		if( $delete_post ){
			$message 	 = array(
				'status' => 'success',
				'icon'	 => 'ti-check',
				'message' => __( 'Container', 'wpcargo-shipment-container' ).' '.$container_title.' '.__( 'successfully deleted.', 'wpcargo-shipment-container' )
			);
		}
	//}
	echo json_encode( $message  );
	wp_die();
}
add_action( 'wp_ajax_get_shipments', 'wpc_shipment_container_get_shipment' );
function wpc_shipment_container_get_shipment(){
	global $wpcargo;
    $data = wpcsc_datatable_unassigned_shipment( (int)$_POST['postID'] );
    echo json_encode($data);
    wp_die();
}
add_action( 'wp_ajax_assign_shipment_admin', 'wpc_shipment_container_assign_shipment_admin' );
function wpc_shipment_container_assign_shipment_admin(){
	global $wpdb, $wpcargo;
	$shipmentID 	= (int)$_POST['shipmentID'];
	$containerID 	= (int)$_POST['containerID'];
	$result  		= array();
	// Check if the container ID post type is shipment_container
	if( get_post_type( $containerID ) == 'shipment_container' ){
		// Check if the shipment is already Assign to other container
		if( get_post_meta( $shipmentID, 'shipment_container', true ) ){
			$result = array(
				'status' => 'error',
				'message' => sprintf( __( "Shipment %s already assign to container %s.", 'wpcargo-shipment-container' ), get_the_title($shipmentID), get_the_title( get_post_meta( $shipmentID, 'shipment_container', true ) ) )
			);
		}else{
			update_post_meta( $shipmentID, 'shipment_container', $containerID );
			$shipment_title = get_the_title($shipmentID);
			$wpcfe_print_options = wpcfe_print_options();
			$status = get_post_meta( $shipmentID, 'wpcargo_status', true );
			ob_start();
			?>
			<div id="shipment-<?php echo $shipmentID; ?>" data-shipment="<?php echo $shipmentID; ?>" class="selected-shipment" >
					<span class="dashicons dashicons-dismiss" data-id="<?php echo $shipmentID; ?>"></span>
					<?php do_action( 'wpcsc_before_shipment_content_section', $shipmentID ); ?>
                    <img src="<?php echo $wpcargo->barcode_url( $shipmentID ); ?>" alt="<?php echo $shipment_title; ?>" />
					<h3 class="shipment-title"><a style="text-decoration: none;" href="<?php echo admin_url('post.php?post='.$shipmentID.'&action=edit'); ?>" target="_target"><?php echo $shipment_title; ?></a></h3>
					<?php do_action( 'wpcsc_after_shipment_content_section', $shipmentID ); ?>
				</div>
			<?php
			$output = ob_get_clean();
			$result = array(
				'status' 	=> 'success',
				'message' 	=> $output
			);
		}
	}else{
		$result = array(
			'status' => 'error',
			'message' => __( "Selected Container not found! Please reload and try again.", 'wpcargo-shipment-container' ) 
		);
	}
	$result['data'] = wpcsc_datatable_unassigned_shipment( $containerID );
	echo json_encode( $result );
	wp_die();
}
add_action( 'wp_ajax_assign_shipment', 'wpc_shipment_container_assign_shipment' );
function wpc_shipment_container_assign_shipment(){
	global $wpdb, $wpcargo;
	$shipmentID 	= (int)$_POST['shipmentID'];
	$containerID 	= (int)$_POST['containerID'];
	$result  		= array();
	// Check if the container ID post type is shipment_container
	if( get_post_type( $containerID ) == 'shipment_container' ){
		// Check if the shipment is already Assign to other container
		if( get_post_meta( $shipmentID, 'shipment_container', true ) ){
			$result = array(
				'status' => 'error',
				'message' => sprintf( __( "Shipment %s already assign to container %s.", 'wpcargo-shipment-container' ), get_the_title($shipmentID), get_the_title( get_post_meta( $shipmentID, 'shipment_container', true ) ) )
			);
		}else{
			update_post_meta( $shipmentID, 'shipment_container', $containerID );
			$shipment_title = get_the_title($shipmentID);
			$wpcfe_print_options = wpcfe_print_options();
			$status = get_post_meta( $shipmentID, 'wpcargo_status', true );
			ob_start();
			?>
			<tr id="shipment-<?php echo $shipmentID; ?>" data-shipment="<?php echo $shipmentID; ?>" class="selected-shipment p-1 col-md-4" >
				<td class="align-middle"><i class="fa fa-sort mr-3"></i></td>
				<td class="text-center">
					<?php do_action( 'wpcsc_before_shipment_content_section', $shipmentID ); ?>
					<h3 class="shipment-title h6"><a style="text-decoration: none;" href="<?php echo get_the_permalink( wpcfe_admin_page() ).'?wpcfe=track&num='.$shipment_title; ?>" target="_blank"><?php echo $shipment_title; ?></a></h3>
					<?php do_action( 'wpcsc_after_shipment_content_section', $shipmentID ); ?>
				</td>
				<td><?php echo $status; ?></td>
				<td class="print-shipment text-center">
					<div class="dropdown">
						<!--Trigger-->
						<button class="btn btn-default btn-sm dropdown-toggle m-0 py-1 px-2" type="button" id="dropdownPrint-<?php echo $shipmentID; ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-list"></i></button>
						<!--Menu-->
						<div class="dropdown-menu dropdown-primary">
							<?php foreach( $wpcfe_print_options as $print_key => $print_label ): ?>
								<a class="dropdown-item print-<?php echo $print_key; ?> py-1" data-id="<?php echo $shipmentID; ?>" data-type="<?php echo $print_key; ?>" href="#"><?php esc_html_e('Print', 'wpcargo-shipment-container'); ?> <?php echo $print_label; ?></a>
							<?php endforeach; ?>
						</div>
					</div>
				</td>
				<td class="text-center">
					<div class="dropdown">
						<!--Trigger-->
						<button class="btn btn-success btn-sm dropdown-toggle m-0 py-1 px-2" type="button" id="update-<?php echo $shipmentID; ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-edit"></i></button>
						<!--Menu-->
						<div class="dropdown-menu dropdown-primary">
							<?php foreach( $wpcargo->status as $status ): ?>
								<a class="update-shipment dropdown-item py-1" data-id="<?php echo $shipmentID; ?>" data-value="<?php echo $status; ?>" href="#"><?php echo $status; ?></a>
							<?php endforeach; ?>
						</div>
					</div>
				</td>
				<td class="text-center">
					<button class="btn btn-danger btn-sm m-0 py-1 px-2 remove-shipment" data-id="<?php echo $shipmentID; ?>" title="<?php esc_html_e('Remove', 'wpcargo-shipment-container'); ?>"><i class="fa fa-trash"></i></button>
				</td>
				<?php do_action( 'wpcsc_after_shipment_content_section', $shipmentID ); ?>
			</tr>
			<?php
			$output = ob_get_clean();
			$result = array(
				'status' 	=> 'success',
				'message' 	=> $output
			);
		}
	}else{
		$result = array(
			'status' => 'error',
			'message' => __( "Selected Container not found! Please reload and try again.", 'wpcargo-shipment-container' ) 
		);
	}
	$result['data'] = wpcsc_datatable_unassigned_shipment( $containerID );
	echo json_encode( $result );
	wp_die();
}
add_action( 'wp_ajax_add_shipments', 'wpc_shipment_container_add_shipment' );
function wpc_shipment_container_add_shipment(){
	global $wpdb, $wpcargo;
	$data 		= urldecode( $_POST['data'] );
	$postID 	= $_POST['postID'];
	if( !empty( $data ) ){
		// Set the Container post_status : Publish
		$container_args = array(
			'ID'            => $postID,
			'post_status'   => 'publish',
		);	
		wp_update_post( $container_args );
		$shipment_parameter = explode("&", $data);
		foreach( $shipment_parameter as $shipment_data ){
			$shipment = explode("=", $shipment_data);
			update_post_meta( $shipment[1], 'shipment_container', $postID );
			$shipment_title = get_the_title($shipment[1]);
			ob_start();
			?>
			<div id="shipment-<?php echo $shipment[1]; ?>" data-shipment="<?php echo $shipment[1]; ?>" class="selected-shipment text-center p-1 col-md-4" >
            	<span class="dashicons dashicons-dismiss" data-id="<?php echo $shipment[1]; ?>"></span>
            	<?php do_action( 'wpcsc_before_shipment_content_section', $shipment[1] ); ?>
                <img src="<?php echo $wpcargo->barcode_url( $shipment[1]); ?>" alt="<?php echo $shipment_title; ?>" />
            	<h3 class="shipment-title h6"><?php echo $shipment_title; ?></h3>
            	<?php do_action( 'wpcsc_after_shipment_content_section', $shipment[1] ); ?>
            </div>
			<?php
			$output = ob_get_clean();
			echo $output;
		}
	}
	wp_die();
}
add_action( 'wp_ajax_remove_shipment', 'wpc_shipment_container_remove_shipment' );
function wpc_shipment_container_remove_shipment(){
	global $wpdb;
	$postID = $_POST['postID'];
	$result = delete_post_meta( $postID, 'shipment_container' );
	echo $result;
	wp_die();
}
add_action( 'wp_ajax_update_shipment', 'wpc_shipment_container_update_shipment' );
function wpc_shipment_container_update_shipment(){
	global $wpdb;
	$postID 		= $_POST['postID'];
	$status 		= $_POST['status'];
	$old_status 	= get_post_meta( $postID, 'wpcargo_status', true );
	update_post_meta( $postID, 'wpcargo_status', $status );
	if( function_exists('wpcfe_save_report') ){
		wpcfe_save_report( $postID, $old_status, sanitize_text_field($status) );
	}
	if( $status != $old_status ){
		if( function_exists('wpcargo_send_email_notificatio') ){
			wpcargo_send_email_notificatio( $postID, $status );
		}
		do_action( 'wpc_add_sms_shipment_history', $postID );
	}
	wp_die();
}
add_action( 'wp_ajax_page_shipment', 'wpc_shipment_contianer_page_shipment' );
function wpc_shipment_contianer_page_shipment(){
	$page 			= ( $_POST['page'] <= 1 ) ? 1 : $_POST['page'] ;
	$offset 		= ( $page - 1 ) * WPCARGO_SHIPMENT_CONTAINER_PAGER;
	$results 		= wpc_shipment_container_get_paged_shipment( $offset, WPCARGO_SHIPMENT_CONTAINER_PAGER );
	$shipper_display 	= get_option('container_shipper_display');
	$receiver_display 	= get_option('container_receiver_display');
	$shipper_label = wpc_shipment_container_get_field_label( $shipper_display );
	$shipper_label = wpc_shipment_container_get_field_label( $shipper_display );
	ob_start();
	?>
    <div id="shipment-container">
    	<?php if( !empty( $results ) ) : ?>
                <?php foreach( $results as $shipment ): ?>
                <li id="shipment-<?php echo $shipment->ID; ?>" class="shipment-section" data-search-term="<?php echo strtolower( get_the_title( $shipment->ID ) ); ?>">
						<input id="shipment-num-<?php echo $shipment->ID; ?>" type="checkbox" class="form-check-input" name="shipment" value="<?php echo $shipment->ID; ?>" />
						<label for="shipment-num-<?php echo $shipment->ID; ?>"><?php echo get_the_title( $shipment->ID ); ?></label>
						<div class="shipment-info">
							<?php do_action( 'wpcsc_before_shipment_list_item', $shipment->ID ); ?>
							<?php if( !empty( $shipper_display ) ): ?>
								<?php 
									$value = maybe_unserialize( get_post_meta( $shipment->ID, $shipper_display, true ) ); 
									$value = ( is_array( $value ) ) ? implode( ', ', $value ) : $value ;
								?>
								<p><strong><?php esc_html_e('Shipper', 'wpcargo-shipment-container' ); ?> <?php echo wpc_shipment_container_get_field_label( $shipper_display ); ?></strong> : <?php echo $value; ?></p>
							<?php endif; ?>
							<?php do_action( 'wpcsc_before_receiver_shipment_list_item', $shipment->ID ); ?>
							<?php if( !empty( $receiver_display ) ): ?>
								<?php 
									$value = maybe_unserialize( get_post_meta( $shipment->ID, $receiver_display, true ) ); 
									$value = ( is_array( $value ) ) ? implode( ', ', $value ) : $value ;
								?>
								<p><strong><?php esc_html_e('Receiver', 'wpcargo-shipment-container' ); ?> <?php echo wpc_shipment_container_get_field_label( $receiver_display ); ?></strong> : <?php echo $value; ?></p>
							<?php endif; ?>
							<?php do_action( 'wpcsc_after_shipment_list_item', $shipment->ID ); ?>
						</div>
                    </li>
                <?php endforeach; ?>
        <?php else: ?>
        <h2 style="text-align:center;"><?php esc_html_e( 'No Available Shipments found.', 'wpcargo-shipment-container' ); ?></h2>
        <?php endif; ?>
    </div>
    <?php
	$output = ob_get_clean();
	echo $output;
	wp_die();
}
function wpc_shipment_contianer_bulk_container_update(){
	$conStatus  	= $_POST['conStatus'];
	$containers 	= $_POST['containers'];
	$applyShipment 	= $_POST['applyShipment'];
	$results 		= array();
	if( !empty( $containers ) ){
		foreach ( $containers as $container ) {
			$_result = update_post_meta( $container, 'container_status', $conStatus );
			$results[] = array(
				'id' 		=> $container,
				'result' 	=> $_result,
				'status' 	=> $conStatus
			);
			if( $applyShipment ){
				$shipments = wpc_shipment_container_get_assigned_shipment( $container );
				if( !empty( $shipments ) ){
					foreach ($shipments as $shipment_id) {
						update_post_meta( $shipment_id, 'wpcargo_status', $conStatus );						
					}
				}
			}
		}
	}
	echo json_encode($results);
	wp_die();
}
add_action( 'wp_ajax_bulk_container_update', 'wpc_shipment_contianer_bulk_container_update' );