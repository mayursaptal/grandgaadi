<?php
if ( ! defined( 'ABSPATH' ) ) { die; }
function wpc_export_frontend_request_callback( ) {
	ob_start();
	if( !is_user_logged_in() ){
		?><p style="text-align:center;"><?php esc_html_e('Sorry, You are not allowed to Access this Importer.', 'wpc-import-export' );?></p><?php
		return false;
	}
	$current_user = wp_get_current_user();
	$user_role = $current_user->roles;
	if( !in_array('administrator', $user_role ) && !in_array( 'cargo_agent', $user_role )){
		?><p style="text-align:center;"><?php esc_html_e('Sorry, You are not allowed to Access this Importer.', 'wpc-import-export' );?></p><?php
		return false;
	}
	?>
    <form method="post" action="">
    	<?php wp_nonce_field( 'wpc_import_ie_results_action', 'wpc_ie_frontend_nonce' ); ?>
    	<h3><?php esc_html_e('Date Range', 'wpc-import-export'); ?></h3>
        <p><label for="date-from"><?php esc_html_e('From :', 'wpc-import-export'); ?></label> <input id="date-from" type="date" name="date-from" value="" required /></p>
        <p><label for="date-to"><?php esc_html_e('To :', 'wpc-import-export'); ?></label> <input id="date-to" type="date" name="date-to" value="" required /></p>
        <p><input type="submit" name="submit" value="<?php esc_html_e('Export File', 'wpc-import-export'); ?>" /></p>
    </form>
	<?php
	echo ob_get_clean();
}
function wpc_export_frontend_generator(){
	global $wpdb;
	if ( isset( $_REQUEST['wpc_ie_frontend_nonce'] ) && wp_verify_nonce( $_REQUEST['wpc_ie_frontend_nonce'], 'wpc_import_ie_results_action' ) ) {
		$table_name = $wpdb->prefix.'wpcargo_custom_fields';
		$meta_fields_label = array();
		$wpcargo_meta_data = array(
			'wpcargo_shipper_name',
			'wpcargo_shipper_phone',
			'wpcargo_shipper_address',
			'wpcargo_shipper_email',
			'wpcargo_receiver_name',
			'wpcargo_receiver_phone',
			'wpcargo_receiver_address',
			'wpcargo_receiver_email',
			'agent_fields',
			'wpcargo_type_of_shipment',
			'wpcargo_courier',
			'wpcargo_mode_field',
			'wpcargo_qty',
			'wpcargo_total_freight',
			'wpcargo_carrier_ref_number',
			'wpcargo_origin_field',
			'wpcargo_pickup_date_picker',
			'wpcargo_status',
			'wpcargo_comments',
			'wpcargo_weight',
			'wpcargo_packages',
			'wpcargo_product',
			'payment_wpcargo_mode_field',
			'wpcargo_carrier_field',
			'wpcargo_departure_time_picker',
			'wpcargo_destination',
			'wpcargo_pickup_time_picker',
			'wpcargo_expected_delivery_date_picker',
			'calculator_desc'
		);
		
		$wpc_default_fields = $wpdb->get_results("SELECT tbl2.meta_key  FROM `{$wpdb->prefix}posts` AS tbl1 INNER JOIN `{$wpdb->prefix}postmeta` AS tbl2 ON tbl1.ID = tbl2.post_id WHERE tbl1.post_type='wpcargo_shipment' AND tbl2.meta_value != '' GROUP BY tbl2.meta_key");
		if( !empty( $wpc_default_fields ) ){
			foreach( $wpc_default_fields as $field_key ){
				if ( !in_array( $field_key->meta_key , $wpcargo_meta_data ) ) continue;
				$meta_fields_label[] = $field_key->meta_key;
			}
		}
		if( $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name ) {
			$wpc_get_fields = $wpdb->get_results("SELECT `field_key` FROM `{$wpdb->prefix}wpcargo_custom_fields`");
			if( !empty( $wpc_get_fields ) ){
				foreach( $wpc_get_fields as $field_key ){
					if ( in_array( $field_key->field_key , $wpcargo_meta_data ) ) continue;
					$meta_fields_label[] = $field_key->field_key;
				}
			}
		}
		$date_from = $_POST['date-from'];
		$date_to = strtotime( $_POST['date-to'] );
		$wpc_ie_args = array(
			'post_type' 		=> 'wpcargo_shipment',
			'post_status' 		=> 'publish',
			'posts_per_page' 	=> -1,
			'date_query' => array(
				array(
					'after'     => $date_from,
					'before'    => array(
						'year'  => date('Y', $date_to ),
						'month' => date('n', $date_to ),
						'day'   => date('j', $date_to ),
					),
				'inclusive' => true,
				),
			),
		);
			$filename_unique = "shipment-export-".time().".csv";
			header('Content-Type: application/csv');
			header('Content-Disposition: attachment; filename="'.$filename_unique.'"');
			$csv_file = fopen($filename_unique, "w");			
			$wpc_ie_query = new WP_Query( $wpc_ie_args );
			if ( $wpc_ie_query->have_posts() ) :
				//fputcsv( $csv_file, $meta_fields_label );
				$meta_fields_label_header = $meta_fields_label;
				array_unshift( $meta_fields_label_header, 'ID','Shipment #');
				echo implode(',', $meta_fields_label_header );
				echo "\n";	
				while ( $wpc_ie_query->have_posts() ) : $wpc_ie_query->the_post();
					$excel_data		= array();
					$excel_data[] 	= str_replace( ',', ' ', get_the_ID() );
					$excel_data[] 	= str_replace( ',', ' ', get_the_title() );
					foreach( $meta_fields_label as $meta_field ) {
						$wpcargo_post_meta = maybe_unserialize( get_post_meta( get_the_ID(), $meta_field, TRUE));
						if(is_array($wpcargo_post_meta)) {
							$data_array = array();
							if( $meta_field == 'calculator_desc' ){
								foreach( $wpcargo_post_meta as $data ){
									$data_array[] = esc_html__('Length : ', 'wpc-import-export' ).$data['length'] .' '.esc_html__('Width : ', 'wpc-import-export' ).$data['width'] .' '.esc_html__('Height : ', 'wpc-import-export' ).$data['height'] .' '.esc_html__('Description : ', 'wpc-import-export' ). $data['description'];
								}
								$excel_data[] = str_replace( ',', ' ', implode(" | ", $data_array) );
							}else{
								$excel_data[] = str_replace( ',', ' ', implode(" | ", $wpcargo_post_meta) );
							}
						}else{
							$excel_data[] = str_replace( ',', ' ', $wpcargo_post_meta );
						}
					}
					echo implode(',', $excel_data );
					echo "\n";
					//fputcsv( $csv_file, $excel_data );
				endwhile;	
			else:
				echo esc_html__('No Shipment Found', 'wpc-import-export' );		
			endif;
			$fileURL = ABSPATH.$filename_unique;
			unlink ( $fileURL );
			wp_reset_postdata();
			exit;
	}
}
add_shortcode( 'wpc-export-frontend', 'wpc_export_frontend_request_callback' );
add_action('wp','wpc_export_frontend_generator');
