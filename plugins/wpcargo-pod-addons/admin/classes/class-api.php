<?php
if (!defined('ABSPATH')){
    exit; // Exit if accessed directly
}
if ( class_exists('WPCARGO_API') ) :
    class WPCARGO_POD_API extends WPCARGO_API {
        //  Register route
        
        public function pod_routes(){
            $namespace = $this->wpcargo_namespace . $this->wpcargo_api_version;
            $base      = 'api';
            register_rest_route( $namespace, '/' . $base.'/(?P<apikey>[a-zA-Z0-9-]+)/pod/settings', array(
                array(
                    'methods'               => WP_REST_Server::READABLE,
                    'callback'              => array( $this, 'pod_settings' ),
                    'permission_callback'   => array( $this, 'wpcargo_api_premission' )
                    )
                ) 
            );
            register_rest_route( $namespace, '/' . $base.'/(?P<apikey>[a-zA-Z0-9-]+)/pod/search/', array(
                array(
                    'methods'               => WP_REST_Server::CREATABLE,
                    'callback'              => array( $this, 'pod_search' ),
                    'permission_callback'   => array( $this, 'wpcargo_api_premission' )
                    )
                ) 
            );
            register_rest_route( $namespace, '/' . $base.'/(?P<apikey>[a-zA-Z0-9-]+)/pod/track/', array(
                array(
                    'methods'               => WP_REST_Server::CREATABLE,
                    'callback'              => array( $this, 'pod_track' ),
                    'permission_callback'   => array( $this, 'wpcargo_api_premission' )
                    )
                ) 
            );
            register_rest_route( $namespace, '/' . $base.'/(?P<apikey>[a-zA-Z0-9-]+)/pod/status', array(
                array(
                    'methods'               => WP_REST_Server::READABLE,
                    'callback'              => array( $this, 'pod_status' ),
                    'permission_callback'   => array( $this, 'wpcargo_api_premission' )
                    )
                ) 
            );
            register_rest_route( $namespace, '/' . $base.'/(?P<apikey>[a-zA-Z0-9-]+)/pod/status/(?P<status>[a-zA-Z0-9-_]+)', array(
                array(
                    'methods'               => WP_REST_Server::READABLE,
                    'callback'              => array( $this, 'pod_shipments' ),
                    'permission_callback'   => array( $this, 'wpcargo_api_premission' )
                    )
                ) 
            );
            register_rest_route( $namespace, '/' . $base.'/(?P<apikey>[a-zA-Z0-9-]+)/pod/status/(?P<status>[a-zA-Z0-9-_]+)/(?P<page>[0-9]+)', array(
                array(
                    'methods'               => WP_REST_Server::READABLE,
                    'callback'              => array( $this, 'pod_shipments' ),
                    'permission_callback'   => array( $this, 'wpcargo_api_premission' )
                    )
                ) 
            );
            register_rest_route( $namespace, '/' . $base.'/(?P<apikey>[a-zA-Z0-9-]+)/pod/status/(?P<status>[a-zA-Z0-9-_]+)/count', array(
                array(
                    'methods'               => WP_REST_Server::READABLE,
                    'callback'              => array( $this, 'pod_count_shipments' ),
                    'permission_callback'   => array( $this, 'wpcargo_api_premission' )
                    )
                ) 
            );
            register_rest_route( $namespace, '/' . $base.'/(?P<apikey>[a-zA-Z0-9-]+)/pod/shipment/(?P<ID>[0-9]+)/', array(
                array(
                    'methods'               => WP_REST_Server::READABLE,
                    'callback'              => array( $this, 'pod_shipment_by_id' ),
                    'permission_callback'   => array( $this, 'wpcargo_api_premission' )
                    )
                ) 
            );
            register_rest_route( $namespace, '/' . $base.'/pod/login', array(
                array(
                    'methods'               => WP_REST_Server::CREATABLE,
                    'callback'              => array( $this, 'login' ),
                    'permission_callback'   => array( $this, 'wpcargo_api_auth_premission' )
                    )
                ) 
            );
        }
        // Register our REST Server
        public function pod_restful_server(){
            add_action( 'rest_api_init', array( $this, 'pod_routes' ) );
        }
        // Route Callbacks
        public function pod_settings(){
            $podapp_status 	    = get_option('wpcargo_podapp_status') ? get_option('wpcargo_podapp_status') : array();	
            $unrequired_fields  = get_option( 'wpcargo_podapp_unrequired_fields' );
            $unrequired_fields  = !empty( $unrequired_fields ) && is_array( $unrequired_fields) ? $unrequired_fields : array() ;
            $settings = array(
                'status' => wpcpod_api_shipment_status(),
                'fields' => wpcpod_api_fields_status(),
                'route' => $podapp_status,
                'unrequired' => $unrequired_fields
            );
            return $settings;
        }
        public function pod_search( WP_REST_Request $request ){
            $allStatus  = wpcpod_api_shipment_status( );
            $search     = $request->get_param( 'search' );
            $status     = $request->get_param( 'status' );
            $status     = !empty( $status ) && in_array($status, $allStatus ) ? $status : 'all';
            $apikey     = $request->get_param( 'apikey' );
            $userID     = wpcapi_get_apikey_user( $apikey  );
            $shipments  = $this->get_pod_searched_shipments( $userID, $search, $status );
            return $this->pod_shipments_data($shipments);
        }
        public function pod_track( WP_REST_Request $request ){
            $allStatus  = wpcpod_api_shipment_status( );
            $search     = $request->get_param( 'track' );
            $apikey     = $request->get_param( 'apikey' );
            $userID     = wpcapi_get_apikey_user( $apikey  );
            $shipments  = $this->get_pod_track_shipments( $userID, $search );
            return $this->pod_shipments_data($shipments);
        }
        
        public function pod_status(){
            return wpcpod_api_shipment_status();
        }
        public function pod_count_shipments( WP_REST_Request $request ){
            $pod_access = $this->has_pod_access( $request );
            if( $pod_access['status'] == 'error' ){
                return $pod_access;
            }
            $shipment_count = $this->get_pod_count_shipments( $pod_access['user_id'], $pod_access['shipment_status'], $pod_access['page'] );
            return array(
                "status" => "success",
                "count" => $shipment_count
            );
        }
        public function pod_shipments( WP_REST_Request $request ){
            $pod_access = $this->has_pod_access( $request );
            if( $pod_access['status'] == 'error' ){
                return $pod_access;
            }
            $shipments          = $this->get_pod_shipments( $pod_access['user_id'], $pod_access['shipment_status'], $pod_access['page'] );
            return $this->pod_shipments_data($shipments);
        }
        public function pod_shipment_by_id( WP_REST_Request $request ){
            $pod_access = $this->is_assigned_shipment( $request );
            if( $pod_access['status'] == 'error' ){
                return $pod_access;
            }
            return $this->pod_shipment_details($pod_access['shipment_id']);
        }
        public function login( WP_REST_Request $request ){
            $creds                    = array();
            $username                 = $request->get_param( 'username' );
            $password                 = $request->get_param( 'password' );
            $creds['user_login']      = $username;
            $creds['user_password']   = $password;
            $creds['remember']        = true;
            $_SERVER['HTTP_REFERER']  = 0;

            $user = wp_signon( $creds, false );

            if ( is_wp_error( $user ) ){
                return array(
                    "type" => "error",
                    "message" => $user->get_error_message(),
                  );
            }else{
                $success    = new stdClass;
                $user_info  = get_userdata( $user->ID );
                $roles      = array_map(function( $_role ){
                    global $wp_roles;
                    return $wp_roles->roles[$_role]['name'];
                }, $user_info->roles );
                $success->status    = array(
                  "type" => "success",
                  "message" => "Success",
                );
                $success->user          = array(
                    'ID'            => $user->ID,
                    'username'      => $user_info->user_login,
                    'roles'         => implode(', ', $roles),
                    'first_name'    => $user_info->first_name,
                    'last_name'     => $user_info->last_name,
                    'user_email'    => $user_info->user_email
                );
                $success->api   = get_user_meta( $user->ID, 'wpcargo_api', true);
                return $success;
            }
         
        }

        public function has_pod_access( $request  ){
            $status     = wpcpod_api_shipment_status( );
            $page       = $request->get_param( 'page' );
            $r_status   = $request->get_param( 'status' );
            $apikey     = $request->get_param( 'apikey' );
            $userID     = wpcapi_get_apikey_user( $apikey  );
            $userdata 	= get_userdata( $userID );

            $page       = (int)$page <= 0 ? 1 : (int)$page;
            // Check is status is in the list
            if( !array_key_exists($r_status, $status ) && $r_status != 'all' ){
                return array(
                    'status' => 'error',
                    'message' => esc_html__("Unregistered Status.", 'wpcargo-pod' ),
                );
            }
            // Check if the logged in user is a driver
            if( !in_array( 'wpcargo_driver', $userdata->roles ) ){
                return array(
                    'status' => 'error',
                    'message' => esc_html__("Account role denied.", 'wpcargo-pod' )
                );
            }
            $shipment_status    = array_key_exists($r_status, $status ) ? $status[$r_status] : 'all';
            return array(
                'status'    => 'success',
                'user_id'   => $userID,
                'shipment_status' => $shipment_status,
                'page'      => $page
            );
        }

        public function is_assigned_shipment( $request  ){
            global $wpdb;
            $shipment_id    = $request->get_param( 'ID' );
            $apikey         = $request->get_param( 'apikey' );
            $userID         = wpcapi_get_apikey_user( $apikey  );
            $userdata 	    = get_userdata( $userID );
            // Check if the logged in user is a driver
            if( !in_array( 'wpcargo_driver', $userdata->roles ) ){
                return array(
                    'status' => 'error',
                    'message' => esc_html__("Account role denied.", 'wpcargo-pod' )
                );
            }

            
            $sql = $wpdb->prepare( 
                "SELECT tbl1.ID FROM `{$wpdb->prefix}posts` tbl1 
                LEFT JOIN `{$wpdb->prefix}postmeta` tbl2 ON tbl1.ID = tbl2.post_id 
                WHERE 
                tbl1.post_status LIKE 'publish' 
                AND tbl1.ID = %d 
                AND tbl1.post_type LIKE 'wpcargo_shipment' 
                AND tbl2.meta_key LIKE 'wpcargo_driver' 
                AND tbl2.meta_value = %d",
                $shipment_id, $userID
            );

            $shipnmentID  = $wpdb->get_var( $sql  );
            
            if( !$shipnmentID ){
                return array(
                    'status' => 'error',
                    'message' => esc_html__("You are not assigned to this shipment", 'wpcargo-pod' )
                );
            }

            return array(
                'status'        => 'success',
                'user_id'       => $userID,
                'shipment_id'   => $shipnmentID
            );
        }

        public function get_pod_shipments( $user_id, $status = '', $page = 1 ){
            global $wpdb;
            $per_page   = 12;
            $offset     = ( $page - 1) * $per_page;
            if( !empty($status) ){
                $excl_status = wpcpod_api_delican_status( );
                if( !empty( $excl_status ) && $status == 'all' ){
                    $excl_status = "'".implode( "', '" , $excl_status )."'";
                    $sql        = $wpdb->prepare( 
                        "SELECT tbl1.ID FROM `{$wpdb->prefix}posts` tbl1 
                        LEFT JOIN `{$wpdb->prefix}postmeta` tbl2 ON tbl1.ID = tbl2.post_id 
                        LEFT JOIN `{$wpdb->prefix}postmeta` tbl3 ON tbl1.ID = tbl3.post_id 
                        WHERE 
                        tbl1.post_status LIKE 'publish' 
                        AND tbl1.post_type LIKE 'wpcargo_shipment' 
                        AND tbl2.meta_key LIKE 'wpcargo_driver' 
                        AND tbl2.meta_value = %d 
                        AND tbl3.meta_key LIKE 'wpcargo_status' 
                        AND tbl3.meta_value NOT IN ( ".$excl_status." ) 
                        GROUP BY tbl1.ID 
                        ORDER BY tbl1.post_date 
                        DESC LIMIT 
                        %d OFFSET %d",
                        $user_id, $per_page, $offset
                    );
                }else{
                    $sql        = $wpdb->prepare( 
                        "SELECT tbl1.ID FROM `{$wpdb->prefix}posts` tbl1 
                        LEFT JOIN `{$wpdb->prefix}postmeta` tbl2 ON tbl1.ID = tbl2.post_id 
                        LEFT JOIN `{$wpdb->prefix}postmeta` tbl3 ON tbl1.ID = tbl3.post_id 
                        WHERE 
                        tbl1.post_status LIKE 'publish' 
                        AND tbl1.post_type LIKE 'wpcargo_shipment' 
                        AND tbl2.meta_key LIKE 'wpcargo_driver' 
                        AND tbl2.meta_value = %d 
                        AND tbl3.meta_key LIKE 'wpcargo_status' 
                        AND tbl3.meta_value LIKE %s 
                        GROUP BY tbl1.ID 
                        ORDER BY tbl1.post_date 
                        DESC LIMIT 
                        %d OFFSET %d",
                        $user_id, $status, $per_page, $offset
                    );
                }
                
            }else{
                $sql        = $wpdb->prepare( 
                    "SELECT tbl1.ID FROM `{$wpdb->prefix}posts` tbl1 
                    LEFT JOIN `{$wpdb->prefix}postmeta` tbl2 ON tbl1.ID = tbl2.post_id 
                    LEFT JOIN `{$wpdb->prefix}postmeta` tbl3 ON tbl1.ID = tbl3.post_id 
                    WHERE 
                    tbl1.post_status LIKE 'publish' 
                    AND tbl1.post_type LIKE 'wpcargo_shipment' 
                    AND tbl2.meta_key LIKE 'wpcargo_driver' 
                    AND tbl2.meta_value = %d 
                    GROUP BY tbl1.ID 
                    ORDER BY tbl1.post_date 
                    DESC LIMIT 
                    %d OFFSET %d",
                    $user_id, $status, $per_page, $offset
                );
            }
            return $wpdb->get_col( $sql );
        }
        public function get_pod_searched_shipments( $user_id, $shipment_number, $status ){
            global $wpdb;
            $excl_status = wpcpod_api_delican_status( );
            $excl_status = "'".implode( "', '" , $excl_status )."'";
            $shipment_number = '%'.$shipment_number.'%';
            if( $status == 'all' ){
                $sql        = $wpdb->prepare( 
                    "SELECT tbl1.ID FROM `{$wpdb->prefix}posts` tbl1 
                    LEFT JOIN `{$wpdb->prefix}postmeta` tbl2 ON tbl1.ID = tbl2.post_id 
                    LEFT JOIN `{$wpdb->prefix}postmeta` tbl3 ON tbl1.ID = tbl3.post_id 
                    WHERE 
                    tbl1.post_status LIKE 'publish' 
                    AND tbl1.post_type LIKE 'wpcargo_shipment' 
                    AND tbl1.post_title LIKE %s 
                    AND tbl2.meta_key LIKE 'wpcargo_driver' 
                    AND tbl2.meta_value = %d 
                    AND tbl3.meta_key LIKE 'wpcargo_status' 
                    AND tbl3.meta_value NOT IN ( ".$excl_status." ) 
                    GROUP BY tbl1.ID 
                    ORDER BY tbl1.post_title 
                    ASC",
                    $shipment_number, $user_id
                );
            }else{
                $sql        = $wpdb->prepare( 
                    "SELECT tbl1.ID FROM `{$wpdb->prefix}posts` tbl1 
                    LEFT JOIN `{$wpdb->prefix}postmeta` tbl2 ON tbl1.ID = tbl2.post_id 
                    LEFT JOIN `{$wpdb->prefix}postmeta` tbl3 ON tbl1.ID = tbl3.post_id 
                    WHERE 
                    tbl1.post_status LIKE 'publish' 
                    AND tbl1.post_type LIKE 'wpcargo_shipment' 
                    AND tbl1.post_title LIKE %s 
                    AND tbl2.meta_key LIKE 'wpcargo_driver' 
                    AND tbl2.meta_value = %d 
                    AND tbl3.meta_key LIKE 'wpcargo_status' 
                    AND tbl3.meta_value LIKE %s 
                    GROUP BY tbl1.ID 
                    ORDER BY tbl1.post_title 
                    ASC",
                    $shipment_number, $user_id, $status
                );
            }
            return $wpdb->get_col( $sql );
        }
        public function get_pod_track_shipments( $user_id, $shipment_number){
            global $wpdb;
            $shipment_number = '%'.$shipment_number.'%';
            $sql        = $wpdb->prepare( 
                "SELECT tbl1.ID FROM `{$wpdb->prefix}posts` tbl1 
                LEFT JOIN `{$wpdb->prefix}postmeta` tbl2 ON tbl1.ID = tbl2.post_id 
                WHERE 
                tbl1.post_status LIKE 'publish' 
                AND tbl1.post_type LIKE 'wpcargo_shipment' 
                AND tbl1.post_title LIKE %s 
                AND tbl2.meta_key LIKE 'wpcargo_driver' 
                AND tbl2.meta_value = %d 
                GROUP BY tbl1.ID 
                ORDER BY tbl1.post_title 
                ASC LIMIT 1",
                $shipment_number, $user_id
            );
            return $wpdb->get_col( $sql );
        }
        public function get_pod_count_shipments( $user_id, $status ){
            global $wpdb;
            if( $status == 'all' ){
                $sql        = $wpdb->prepare( 
                    "SELECT count(tbl1.ID) FROM `{$wpdb->prefix}posts` tbl1 
                    LEFT JOIN `{$wpdb->prefix}postmeta` tbl2 ON tbl1.ID = tbl2.post_id 
                    WHERE 
                    tbl1.post_status LIKE 'publish' 
                    AND tbl1.post_type LIKE 'wpcargo_shipment' 
                    AND tbl2.meta_key LIKE 'wpcargo_driver' 
                    AND tbl2.meta_value = %d 
                    GROUP BY tbl1.ID",
                    $user_id
                );
            }else{
                $sql        = $wpdb->prepare( 
                    "SELECT count(tbl1.ID) FROM `{$wpdb->prefix}posts` tbl1 
                    LEFT JOIN `{$wpdb->prefix}postmeta` tbl2 ON tbl1.ID = tbl2.post_id 
                    LEFT JOIN `{$wpdb->prefix}postmeta` tbl3 ON tbl1.ID = tbl3.post_id 
                    WHERE 
                    tbl1.post_status LIKE 'publish' 
                    AND tbl1.post_type LIKE 'wpcargo_shipment' 
                    AND tbl2.meta_key LIKE 'wpcargo_driver' 
                    AND tbl2.meta_value = %d 
                    AND tbl3.meta_key LIKE 'wpcargo_status' 
                    AND tbl3.meta_value LIKE %s 
                    GROUP BY tbl1.ID",
                    $user_id, $status
                );
            }
            return $wpdb->get_var( $sql );
        }
        public function pod_shipment_details( $shipment_id ){
            $shipment_data                      = array();
            $history                            = wpcapi_get_shipment_history( $shipment_id );
            $img_ids                            = get_post_meta( $shipment_id, 'wpcargo-pod-image', true);
            $sign_id                            = get_post_meta( $shipment_id, 'wpcargo-pod-signature', true);
            $img_ids                            = array_filter( explode(',', $img_ids) );
            $imgs_urls                          = array();
            if( !empty( $img_ids ) ){
                foreach ($img_ids as $_id ) {
                    $imgs_urls[] = wp_get_attachment_url( $_id );
                }
            }
            $shipment_data['ID']                = $shipment_id;
            $shipment_data['shipment_number']   = get_the_title( $shipment_id ); 
            $shipment_data['wpcargo_status']    = get_post_meta( $shipment_id, 'wpcargo_status', true ); 
            if( !empty( wpcapi_get_registered_metakeys() ) ){
            foreach ( wpcapi_get_registered_metakeys() as $metakey => $field_info ) {
                $metavalue  = maybe_unserialize( get_post_meta( $shipment_id, $metakey, true ) );
                $shipment_data[$metakey] = $metavalue;
            }
            }
            $shipment_data['shipment_packages'] = wpcargo_get_package_data( $shipment_id );
            $shipment_data['shipment_history']  = $history;
            $shipment_data['pod_signature']     = wp_get_attachment_url( $sign_id );
            $shipment_data['pod_images']        = $imgs_urls;
            return $shipment_data;
        }
        public function pod_shipments_data( $shipment_ids ){
            global $wpcargo;
            $shipment_data = array();
            $counter = 0;
            if( empty( $shipment_ids ) ){
                return $shipment_data;
            }
            foreach ( $shipment_ids as $shipment_id ) {
                $shipment_data[$counter] = $this->pod_shipment_details( $shipment_id );
                $counter++;
            }
            return $shipment_data;
        }
    }
    
    $wpcargoPODAPI = new WPCARGO_POD_API();
    $wpcargoPODAPI->pod_restful_server();
endif;