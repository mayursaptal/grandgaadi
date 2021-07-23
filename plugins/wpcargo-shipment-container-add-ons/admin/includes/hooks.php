<?php
// Frontend Manager Hooks
function wpcsc_registered_frontend_scripts( $scripts ){
    $scripts[] = 'shipment-container-sortable-scripts';
    $scripts[] = 'shipment-container-datatable-scripts';
    $scripts[] = 'shipment-container-scripts';
    return $scripts;
}
function wpcsc_registered_frontend_style( $styles ){
    $styles[] ='shipment-container-datatable-styles';
    $styles[] ='shipment-container-styles';
    return $styles;
}
add_action( 'plugins_loaded', 'wpcsc_frontend_register_assets_callback' );
function wpcsc_frontend_register_assets_callback(){
    add_filter( 'wpcfe_registered_styles', 'wpcsc_registered_frontend_style', 10 );
    add_filter( 'wpcfe_registered_scripts', 'wpcsc_registered_frontend_scripts', 10 );
    if( can_access_containers() ){
        add_action( 'before_wpcfe_shipment_form_submit', 'wpcsc_container_details', 40 );
    }
    add_action('after_wpcfe_save_shipment', 'save_shipment_container_frontend_callback', 10, 2);
}
add_filter('wpcfe_after_sidebar_menus', 'wpc_container_sidebar_menu', 5, 1 );
function wpc_container_sidebar_menu( $menu_array ){
    if( function_exists('wpcfe_admin_page') && can_access_containers() ){
        $menu_array['wpcsc-menu'] = array(
            'page-id' => wpc_container_frontend_page(),
            'label' => wpc_container_label_plural(),
            'permalink' => get_the_permalink( wpc_container_frontend_page() ),
            'icon' => 'fa-truck'
        ) ;
    }
    return $menu_array;
}
add_action( 'wpcsc_table_header_value', 'wpcsc_container_list_header' );
function wpcsc_container_list_header(){
    $key_label = wpc_shipment_container_key_label_header_callback();
    if( !empty( $key_label ) ){
        foreach ( $key_label as $key => $value) {
            $_class = '';
            if(  $key == 'scprint' ){
                continue;
            }
            if( $key == 'scmanifest' ){
                $_class = 'text-center';
            }
            ?><th class="<?php echo $_class; ?>"><?php echo $value; ?></th><?php
        }
    }
}
// Add Shipment table for the assigned container
add_action( 'wpcfe_shipment_table_header', 'wpcsc_shipment_container_table_header', 10 );
function wpcsc_shipment_container_table_header(){
    echo '<th class="no-space">'.esc_html__( apply_filters( 'wpcsc_shipment_container_table_header_label', __('Container', 'wpcargo-shipment-container' ) ) ).'</th>';
}
add_action( 'wpcfe_shipment_table_data', 'wpcsc_shipment_container_table_data', 10, 1 );
function wpcsc_shipment_container_table_data( $shipment_id ){
    $value          = '';
    $container_id   = wpcsc_get_shipment_container( $shipment_id );
    if( $container_id ){
        $container_number = wpcsc_get_container_number( $container_id  );
        if( $container_number ){
            $value = $container_number;
        }else{
            $value = '<span><i class="unassigned-shipment fa fa-unlink fa-lg mr-2 text-danger" data-id="'.$shipment_id.'"></i>'.__('Container NOT Found', 'wpcargo-shipment-container' ).'</span>';
        }
    }
    echo '<td class="no-space">'.$value.'</td>';
}
add_action( 'wpcsc_table_data_value', 'wpcsc_container_list_data', 10, 1 );
function wpcsc_container_list_data( $container_id ){
    global $wpcargo;
    $key_label = wpc_shipment_container_key_label_header_callback();
    if( !empty( $key_label ) ){
        foreach ( $key_label as $key => $value) {
            if(  $key == 'scprint' ){
                continue;
            }
            $_value = '';
            $_class = '';
            if ( $key == 'flight' ){
                $_value = get_post_meta( $container_id, 'container_no', TRUE );
            }
            if( $key == 'shipments' ){
                $shipment_count = wpcshcon_shipment_count( $container_id );
                $_value = $shipment_count 
                ? '<a href="#" class="text-info" data-id="'.$container_id.'"><i class="fa fa-list"></i> '.sprintf( _n( '%s Shipment', '%s Shipments', $shipment_count, 'wpcargo-shipment-container' ), $shipment_count ).'</a>' 
                : '';
            }
            if( $key == 'agent' ){
                $_value = get_post_meta( $container_id, 'container_agent', TRUE );
            }
            if( $key == 'delivery_agent' ){
                $_value =  get_post_meta( $container_id, 'delivery_agent', TRUE );
            }
            if( $key == 'status' ){
                $_value =  get_post_meta( $container_id, 'container_status', TRUE );
            }
            if( $key == 'scmanifest' ){
                $_value = '<a href="'.admin_url( '/?wpcscpdf='.$container_id ).'"><span class="dashicons dashicons-download"></span></a>';
                $_class = 'text-center';
            }
            ?><td class="<?php echo $_class; ?>"><?php echo $_value; ?></td><?php
        }
    }
}
// track result Hooks
add_action( 'container_track_result_after_details', 'container_track_assigned_shipment_callback', 10, 1 );
function container_track_assigned_shipment_callback( $container_id ){
    global $wpcargo;
    $shipments 		= wpc_shipment_container_get_assigned_shipment( $container_id );
    if( empty($shipments) ){
        return false;
    }
    ?>
    <div id="container-shipments" class="col-sm-12 my-4">
        <p class="section-header h5-responsive font-weight-normal pb-2 border-bottom"><?php echo wpc_scpt_assinged_container_label(); ?></p>
        <div class="container-fluid w-100 m-0">
            <div class="row">
                <?php foreach( $shipments as $shipment_id ):  ?>
                    <div id="shipment-<?php echo $shipment_id; ?>" class="selected-shipment text-center p-1 col-md-4 border" >
                        <?php do_action( 'wpcsc_before_shipment_content_section', $shipment_id ); ?>
                        <img src="<?php echo $wpcargo->barcode_url( $shipment_id); ?>" alt="<?php echo get_the_title( $shipment_id ); ?>" />
                        <h3 class="shipment-title h6"><?php echo get_the_title( $shipment_id  ); ?></h3>
                        <?php do_action( 'wpcsc_after_shipment_content_section', $shipment_id ); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>   
    </div>
    <?php
}
add_action( 'container_track_result_after_details', 'container_track_history_callback', 10, 1 );
function container_track_history_callback( $container_id ){
    global $wpcargo;
    $history    = wpcsc_get_container_history( $container_id );  
    if( empty($history) ){
        return false;
    }  
    include_once( wpcsc_include_template( 'track-history.tpl' ) );	
}
add_action( 'wpc_shipment_additional_container_info', 'container_update_history_callback', 10, 1 );
function container_update_history_callback( $container_id ){
    global $wpcargo;
    $history    = wpcsc_get_container_history( $container_id );  
    ob_start();
    ?>
    <div id="time-info" class="col-md-12 mb-4">
        <div class="card">
            <section class="card-header">
            <?php echo apply_filters( 'wpcsc_history_label', __('History', 'wpcargo-shipment-container') ); ?>                
            </section>
            <section class="card-body">
            <?php require_once( wpcsc_admin_include_template('history.tpl') ); ?>
            </section>
        </div>
    </div>
    <?php
    echo ob_get_clean();
}
add_action( 'wp', 'wpcsc_save_container_callback' );
function wpcsc_save_container_callback(){
    // Check if nonce is isset
    global $wpcargo;
    if ( ! isset( $_POST['wpcsc_nonce_field_value'] ) 
        || ! wp_verify_nonce( $_POST['wpcsc_nonce_field_value'], 'wpcsc_form_action' ) 
    ) {
        return false;
    }
    if( !isset( $_POST['container_id'] ) && !(int)$_POST['container_id'] ){
        return false;
    }
    if( !(int)$_POST['container_id'] ){
        return false;
    }	
    $container_id       = (int)$_POST['container_id'];	
    $container_args = array(
        'ID'            => $container_id,
        'post_title'    => sanitize_text_field( $_POST['wpcsc_number'] ),
        'post_status'   => 'publish',
    );	
    wp_update_post( $container_args );
    $info_fields = wpc_container_info_fields();
    if( !empty( $info_fields  ) ){
        foreach ( $info_fields as $key => $value) {
			if( isset( $_POST[$key] ) && !empty( $_POST[$key] ) ) {
				update_post_meta( $container_id, $key, sanitize_text_field( $_POST[$key] ) );
			}
        }
    }
    $trip_fields = wpc_trip_info_fields();
    if( !empty( $trip_fields  ) ){
        foreach ( $trip_fields as $key => $value) {
			if( isset( $_POST[$key] ) && !empty( $_POST[$key] ) ){
				update_post_meta( $container_id, $key, sanitize_text_field( $_POST[$key] ) );
			}
        }
    }
    $time_fields = wpc_time_info_fields();
    if( !empty( $time_fields  ) ){
        foreach ( $time_fields as $key => $value) {
			if( isset( $_POST[$key] ) && !empty( $_POST[$key] ) ){
            	update_post_meta( $container_id, $key, sanitize_text_field( $_POST[$key] ) );
			}
        }
    }
    wpcsc_save_history( $container_id, $_POST );
    wp_redirect( get_the_permalink().'/?wpcsc=edit&id='.$container_id.'&update=1' );
    die;
}
function wpcsc_container_details( $shipment_id ){
	$container_id   = wpcsc_get_shipment_container( $shipment_id );
    $containers     = get_shipment_containers();
	?>
	<div id="consolidated-details" class="card mb-4">
		<div class="card">
			<section class="card-header">
				<?php echo apply_filters( 'wpcfe_multipack_header_label', esc_html__('Container Details','wpcargo-shipment-container') ); ?>
			</section>
			<section class="card-body">					
					<?php do_action( 'before_container_details_row', $shipment_id ); ?>						
						<label><?php _e( 'Shipment Container', 'wpcargo-shipment-container' ); ?></label>                   
                        <select name="shipment_container" class="mdb-select mt-0 form-control browser-default" id="shipment_container" >
                            <option value=""><?php esc_html_e('-- Select Container --','wpcargo-shipment-container'); ?></option>
                            <?php if( $containers ): ?>
                                <?php foreach( $containers as $container ): ?>
                                    <option value="<?php echo $container->ID; ?>" <?php selected($container_id, $container->ID); ?>><?php echo $container->post_title; ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>                        
				<?php do_action( 'after_container_details_row', $shipment_id ); ?>					
			</section>
		</div>
	</div>
	<?php
}
function save_shipment_container_frontend_callback( $post_id, $post ){
    if( isset($post['shipment_container']) && can_access_containers() ){
        update_post_meta( $post_id, 'shipment_container', $post['shipment_container'] );
    }
}
// Plugin Rows Hook
function wpc_shipment_container_row_action_callback( $links ){
    $action_links = array(
        'settings' => '<a href="' . admin_url( 'admin.php?page=wpc-container-settings' ) . '" aria-label="' . esc_attr__( 'Settings', 'wpcargo-shipment-container' ) . '">' . esc_html__( 'Settings', 'wpcargo-shipment-container' ) . '</a>',
        'license' => '<a href="' . admin_url( 'admin.php?page=wptaskforce-helper' ) . '" aria-label="' . esc_attr__( 'License', 'wpcargo-shipment-container' ) . '">' . esc_html__( 'License', 'wpcargo-shipment-container' ) . '</a>',
    );
    return array_merge( $action_links, $links );
}
add_filter('plugin_action_links_' . WPCARGO_SHIPMENT_CONTAINER_BASENAME, 'wpc_shipment_container_row_action_callback', 10, 2);
// API Add on Hooks
function wpcscon_api_shipment_data_callback( $data, $shipment_id ){
    $container_id = get_post_meta( $shipment_id, 'shipment_container', true );
    $data['shipment_container'] = get_the_title( $container_id );
    return $data;
}
add_filter('wpcargo_api_shipment_data', 'wpcscon_api_shipment_data_callback', 10, 2);
function wpcscon_api_after_add_shipment_callback( $shipmentID, $request ){
    $shipment_container  = $request->get_param( 'shipment_container' );
    if( empty( $shipment_container ) ){
        return false;
    }
    $container_id = wpcsc_get_container_id( $shipment_container );
    if( !$container_id ){
        return false;
    }
    update_post_meta( $shipmentID, 'shipment_container', (int)$container_id );
}
add_action( 'wpcargo_api_after_add_shipment', 'wpcscon_api_after_add_shipment_callback', 10, 2 );
function wpcscon_api_after_update_shipment_callback( $shipmentID, $request ){
    $shipment_container  = $request->get_param( 'shipment_container' );
    if( empty( $shipment_container ) ){
        return false;
    }
    $container_id = wpcsc_get_container_id( $shipment_container );
    if( !$container_id ){
        return false;
    }
    update_post_meta( $shipmentID, 'shipment_container', (int)$container_id );
}
add_action( 'wpcargo_api_after_update_shipment', 'wpcscon_api_after_update_shipment_callback', 10, 2 );
// Manifest Helpers
function wpcsc_pdf_siteinfo_manifest_callback( $container_id, $site_info = '' ){
    include_once( wpcsc_include_template( 'siteinfo', 'manifest' ) );	
}
function wpcsc_pdf_header_manifest_callback( $container_id, $site_info = '' ){
    $tracknumber	= get_the_title( $container_id );
    $container_no 	= get_post_meta( $container_id , 'container_no', true );
	$destination 	= get_post_meta( $container_id , 'destination', true );
	$origin 		= get_post_meta( $container_id , 'origin', true );
    include_once( wpcsc_include_template( 'header', 'manifest' ) );	
}
function wpcsc_pdf_content_manifest_callback( $container_id, $site_info = '' ){
    global $wpcargo;
    $shipment_fields    = apply_filters( 'wpcsc_manifest_registered_fields', get_option('container_field_manifest') );
    $url_barcode	    = WPCARGO_PLUGIN_URL."/includes/barcode.php?codetype=Code128&size=60&text=";
    $shipments		    = wpc_shipment_container_get_assigned_shipment($container_id);	
	$shipment_ids 		= apply_filters( 'wpcsc_shipment_manifest_list', $shipments, $container_id );
    include_once( wpcsc_include_template( 'content', 'manifest' ) );	
}
function wpcsc_pdf_before_footer_manifest_callback( $container_id, $site_info = '' ){
    $acknowledgement    = wpautop(get_option('container_manifest_acknowledge'));
    $footer_data 	    = wpautop(get_option('container_print_footer'));	
    include_once( wpcsc_include_template( 'footer', 'manifest' ) );	
}
add_action( 'wpcsc_pdf_header_manifest', 'wpcsc_pdf_siteinfo_manifest_callback', 10, 2 );
add_action( 'wpcsc_pdf_header_manifest', 'wpcsc_pdf_header_manifest_callback', 10, 2 );
add_action( 'wpcsc_pdf_content_manifest', 'wpcsc_pdf_content_manifest_callback', 10, 2 );
add_action( 'wpcsc_pdf_footer_manifest', 'wpcsc_pdf_before_footer_manifest_callback', 10, 2 );
// Import/Export Hooks
function wpcsc_ie_registered_field( $fields ){
    $fields[] = array(
        'meta_key' 	=> 'wpcsc_container',
        'label' 	=> esc_html__( 'Container Number', 'wpcargo-shipment-container' ),
        'fields' 	=> array()
    );
    return $fields;
}
function wpcsc_export_data_callback( $data, $shipment_id, $meta_key ){
    $container_id   = wpcsc_get_shipment_container( $shipment_id );
    if( $meta_key === 'wpcsc_container' && $container_id ){
        return get_the_title( $container_id );
    }
    return $data;
}
function wpcsc_import_save_data_callback( $shipment_id, $data ){
    if( array_key_exists( 'wpcsc_container', $data ) ){
        $container_id = wpcsc_get_container_id( $data['wpcsc_container'] );
        if( $container_id ){
            update_post_meta( $shipment_id, 'shipment_container', $container_id );
        }
    }
}
function wpcsc_plugins_loaded_callback(){
    add_filter( 'ie_registered_fields', 'wpcsc_ie_registered_field' );
    add_filter( 'wpc_ie_meta_data', 'wpcsc_export_data_callback', 10, 3 );
    add_action( 'wpcie_after_save_csv_import', 'wpcsc_import_save_data_callback', 10, 2 );
}
add_action( 'plugins_loaded', 'wpcsc_plugins_loaded_callback' );
//** Load Plugin text domain
add_action( 'plugins_loaded', 'wpc_shipment_container_load_textdomain' );
function wpc_shipment_container_load_textdomain() {
	load_plugin_textdomain( 'wpcargo-shipment-container', false, '/wpcargo-shipment-container-add-ons/languages' );
}
// Load the auto-update class
function wpc_shipment_container_get_plugin_remote_update(){
	require_once( WPCARGO_SHIPMENT_CONTAINER_PATH. 'admin/classes/wp_autoupdate.php');
	$plugin_remote_path = 'http://www.wpcargo.com/repository/wpcargo-shipment-container-add-ons/'.WPCARGO_SHIPMENT_CONTAINER_UPDATE_REMOTE.'.php';
	return new WPC_Shipment_Container_AutoUpdate ( WPCARGO_SHIPMENT_CONTAINER_VERSION, $plugin_remote_path, WPCARGO_SHIPMENT_CONTAINER_BASENAME );

}
function wpc_shipment_container_activate_au(){
    wpc_shipment_container_get_plugin_remote_update();
}
function wpc_shipment_container_plugin_update_message( $data, $response ) {
	$autoUpdate 	= wpc_shipment_container_get_plugin_remote_update();
	$remote_info 	= $autoUpdate->getRemote('info');
	if( !empty( $remote_info->update_message ) ){
		echo $remote_info->update_message;
	}
}
add_action( 'in_plugin_update_message-wpcargo-shipment-container-add-ons/wpcargo-shipment-container-add-ons.php', 'wpc_shipment_container_plugin_update_message', 10, 2 );
add_action( 'init', 'wpc_shipment_container_activate_au' );