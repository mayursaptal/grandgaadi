<?php
function wpcargo_pod_api_action(){
    add_action( 'wpcargo_api_after_update_shipment', 'wpcpod_api_shipment_signature_callback', 10, 2 );
    add_action( 'wpcargo_api_after_update_shipment', 'wpcpod_api_shipment_images_callback', 10, 2 );
    add_action( 'admin_notices', 'wpcpod_app_settings_notice__error' );
}
add_action( 'plugins_loaded', 'wpcargo_pod_api_action' );

function wpcpod_api_shipment_signature_callback( $shipmentID, $request ){
    if( empty( $request->get_param( 'signature' ) ) ){
        return false;
    }
    $dataURL 				= $request->get_param( 'signature' );  
    $wpcargo_id 			= $shipmentID;		
    if($wpcargo_id) {
        $wpcargo_img_delete		= get_post_meta($wpcargo_id, 'wpcargo-pod-signature', true);
        wp_delete_attachment( $wpcargo_img_delete );
    }
    $upload_dir = wp_upload_dir();
    $parts = explode(',', $dataURL);  
    $data = $parts[1];  
    $data = base64_decode($data); 
    if (!is_dir($file = $upload_dir['basedir']. '/wpcargo-signature/')) {
        mkdir($upload_dir['basedir']. '/wpcargo-signature/', 0777, true);
    }
    $file = $upload_dir['basedir']. '/wpcargo-signature/' . uniqid() . '.png';
    $success = file_put_contents( $file, $data );
    $attach_id = wp_insert_attachment( $attachment, $file, $wpcargo_id );	
    require_once( ABSPATH . 'wp-admin/includes/image.php' );		
    $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
    wp_update_attachment_metadata( $attach_id, $attach_data );
    update_post_meta($wpcargo_id, 'wpcargo-pod-signature', $attach_id);			
    set_post_thumbnail( $wpcargo_id, $attach_id );
}
function wpcpod_api_shipment_images_callback( $shipmentID, $request ){
    if( empty( $request->get_param( 'pod_images' ) ) ){
        return false;
    }

    $dataURLs 				= $request->get_param( 'pod_images' ); 
    
    $image_id               = array();
    if( !empty( $dataURLs ) ){
        foreach ($dataURLs as $dataURL) {
            $upload_dir = wp_upload_dir();
            $parts = explode(',', $dataURL);  
            $data = $parts[1];  
            $data = base64_decode($data); 
            $file = $upload_dir['path']. '/' . uniqid() . '.jpeg';
            $success = file_put_contents( $file, $data );
            $attach_id = wp_insert_attachment( $attachment, $file, $shipmentID );	
            require_once( ABSPATH . 'wp-admin/includes/image.php' );		
            $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
            wp_update_attachment_metadata( $attach_id, $attach_data );		
            set_post_thumbnail( $shipmentID, $attach_id );
            $image_id[] = $attach_id;
        }
    }
    if( !empty( $image_id  ) ){
        update_post_meta( $shipmentID, 'wpcargo-pod-image', implode(",", $image_id) );	
    }
}

function wpcpod_save_base64_image( $shipmentID, $dataURL ){
    $upload_dir = wp_upload_dir();
    $parts      = explode(',', $dataURL);  
    $data       = $parts[1];  
    $data       = base64_decode($data); 
    $file       = $upload_dir['basedir']. '/' . uniqid() . '.png';
    $success    = file_put_contents( $file, $data );
    $attach_id  = wp_insert_attachment( $attachment, $file, $shipmentID );	
    require_once( ABSPATH . 'wp-admin/includes/image.php' );		
    $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
    wp_update_attachment_metadata( $attach_id, $attach_data );
    return $attach_id;
}

function wpcpod_app_settings_notice__error() {
    $podapp_status 	= get_option('wpcargo_podapp_status') ? get_option('wpcargo_podapp_status') : array();	
    $excl_status = wpcpod_api_delican_status( );
    $excl_status = "'".implode( "', '" , $excl_status )."'";
    if( !empty( $podapp_status ) ){
        return false;
    }
    ?>
    <div class="notice notice-error is-dismissible">
        <p><span class="dashicons dashicons-bell" style="color:#d54e21"></span> <?php _e( 'POD APP settings is NOT set-up.', 'wpcargo-pod' ); ?> <?php printf( '<a href="%s" >' . __( 'Click Here' ) . '</a>',  admin_url('admin.php?page=wpc-podapp-settings') ) ?> <?php _e( 'This may affect your POD application.', 'wpcargo-pod' ); ?></p>
    </div>
    <?php
}