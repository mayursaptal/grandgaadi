<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}
function wpc_export_meta_query_callback( $meta_query ){
    $user_id    = get_current_user_id();
    if( wpcie_is_manager() ){
        $meta_query['wpcargo_branch_manager'] = array( 
            'key'			=> 'wpcargo_branch_manager',
            'value' 		=> $user_id,
            'compare'		=> '=', 
        );
    }elseif( wpcie_is_employee() ){
        $meta_query['wpcargo_employee'] = array( 
            'key'			=> 'wpcargo_employee',
            'value' 		=> $user_id,
            'compare'		=> '=', 
        );
    }elseif( wpcie_is_agent() ){
        $meta_query['agent_fields'] = array( 
            'key'			=> 'agent_fields',
            'value' 		=> $user_id,
            'compare'		=> '=', 
        );
    }elseif( wpcie_is_driver() ){
        $meta_query['wpcargo_driver'] = array( 
            'key'			=> 'wpcargo_driver',
            'value' 		=> $user_id,
            'compare'		=> '=', 
        );
    }
    return $meta_query;
}
add_filter( 'wpc_export_meta_query', 'wpc_export_meta_query_callback' );
function wpcie_after_save_csv_import_callback( $shipment_id, $data ){
    $user_id    = get_current_user_id();
    if( wpcie_is_manager() ){
        update_post_meta( $shipment_id, 'wpcargo_branch_manager', $user_id );
    }
    if( wpcie_is_employee() ){
        update_post_meta( $shipment_id, 'wpcargo_employee', $user_id );
    }
    if( wpcie_is_agent() ){
        update_post_meta( $shipment_id, 'agent_fields', $user_id );
    }
    if( wpcie_is_driver() ){
        update_post_meta( $shipment_id, 'wpcargo_driver', $user_id );
    }
}
function wpc_import_user_permission( $shipment_id ){
    $user_id    = get_current_user_id();
    $access     = true;
    $owner      = false;
    if( !$shipment_id ){
        return false;
    }   
    if( wpcie_is_manager() ){
        $owner = get_post_meta( $shipment_id, 'wpcargo_branch_manager', true );
    }elseif( wpcie_is_employee() ){
        $owner = get_post_meta( $shipment_id, 'wpcargo_employee', true );
    }elseif( wpcie_is_agent() ){
        $owner = get_post_meta( $shipment_id, 'agent_fields', true );
    }elseif( wpcie_is_driver() ){
        $owner = get_post_meta( $shipment_id, 'wpcargo_driver', true );
    }elseif( wpcie_is_client() ){
        $owner = get_post_meta( $shipment_id, 'registered_shipper', true );
    }
    if( $user_id != $owner && $owner ){
        $access = false;
    }
    return apply_filters( 'wpc_import_user_permission', $access );
}

add_action( 'wpcie_after_save_csv_import', 'wpcie_after_save_csv_import_callback', 10, 2 );
function wpcie_get_shipment_template( $headers ){
    global $wpdb;
    $sql            = "SELECT `ID` FROM `{$wpdb->prefix}posts` WHERE `post_status` LIKE 'publish' AND `post_type` LIKE 'wpcargo_shipment' LIMIT 1";
    $shipment_id    = $wpdb->get_var( $sql );
    $dummy_data     = array();
    if( $shipment_id ){
        foreach( array_keys( $headers ) as $key ){
            $data = maybe_unserialize ( get_post_meta( 2809, $key, TRUE) );
            if(  $key === 'shipment_id' ){
                $dummy_data[] = 0;
                continue;
            }elseif( $key === 'shipment_title' ){
                $dummy_data[] = 'DUMMY123456';
                continue;
            }elseif( $key === 'wpcargo_shipment_cat' ){
                $dummy_data[] = 0;
                continue;
            }elseif( $key === 'registered_shipper' ){
                $dummy_data[] = 'Juan dela Cruz';
                continue;
            }else{
                if( is_array($data) ) {
                    if( empty( $data ) ){
                        $dummy_data[] = '';
                        continue;
                    } 
                    // Check if the meta has field
                    if( $key == 'wpcargo_shipments_update' || $key == 'wpc-multiple-package' ){
                        if( $key == 'wpcargo_shipments_update' ){
                            $fields = wpcargo_history_fields();
                        }elseif( $key == 'wpc-multiple-package' ){
                            $fields = wpcargo_package_fields();
                        }
                        if( empty( $fields ) ){
                            $dummy_data[] = '';
                            continue;
                        }
                        $data_str = '';
                        $counter = 1;
                        foreach( $data as $val ){
                            if( empty( $val ) ){
                                continue;
                            }
                            foreach( $fields as $key => $value ){
                                if( array_key_exists( $key, $val ) ){
                                    $data_str .= $value['label'].'='.$val[$key].' * ';
                                }else{
                                    $data_str .= $value['label'].'= * ';
                                }
                            }
                            $data_str .= ' | ';
                            if( $counter === 2 ){ break; }
                            $counter++;
                        }
                        $dummy_data[] = $data_str;
                    }else{
                        $dummy_data[] = implode(' | ', $data );
                    }
                    
                }else{
                    $dummy_data[] = $data;
                }
            }
        }
    }
    return $dummy_data;
}
// Registered Admin submenus and pages
function wpcie_admin_submenu_page(){
    //** Import Submenu
    add_submenu_page(
        'edit.php?post_type=wpcargo_shipment',
        __('Import/Export', 'wpc-import-export'),
        __('Import/Export', 'wpc-import-export'),
        'manage_options',
        'wpcie-export',
        'wpcie_submenu_page_callback');
    //** Exmport Submenu
    add_submenu_page(
        NULL,
        __('Import/Export', 'wpc-import-export'),
        __('Import/Export', 'wpc-import-export'),
        'manage_options',
        'wpcie-import',
        'wpcie_submenu_page_callback');
}
add_action( 'admin_menu', 'wpcie_admin_submenu_page' );
// Admin page callbacks
function wpcie_submenu_page_callback(){
    global $wpdb;
    $table_name         = $wpdb->prefix.'wpcargo_custom_fields';
    $field_selection    = wpcie_registered_form_fields();
    $page               = $_GET['page'];
    $tax_args       = array(
        'orderby'       => 'name',
        'order'         => 'ASC',
        'hide_empty'    => 0
    );
    $shipment_category = get_categories($tax_args);		
    ob_start();
    ?>
    <div class="wrap"><div id="icon-tools" class="icon32"></div>
        <h2><?php esc_html_e( 'Shipment Import/ Export', 'wpc-import-export' ); ?> </h2>
        <?php wpcie_page_header_tab();  ?>
        <div id="form-block">
            <?php
                if( $page == 'wpcie-import' ){
                    wpcie_import_form( $field_selection, $shipment_category, $page );
                }elseif( $page == 'wpcie-export' ){
                    wpcie_export_form( $field_selection, $shipment_category, $page);
                }
            ?>
        </div>
    </div>
    <?php
    echo ob_get_clean();
}
// Forms
function wpcie_import_form( $fields = array(), $taxonomy = array(), $page ='' ){
    global $post;
    $filename_unique 		= "shipment-import-template-".time().".csv";
    $saved_field_options    = get_option( 'multiselect_settings' );
    $csv_file 				= fopen($filename_unique, "w");
    $headers                = wpcie_csv_template_headers();
    $headers['registered_shipper'] 		= wpcie_registered_shipper_label().' ID (registered_shipper)';
    $headers['wpcargo_shipment_cat'] 	= wpcie_shipment_category_label().' ID (wpcargo_shipment_cat)';
    if( !empty( $saved_field_options ) && is_array( $saved_field_options ) ){
        $headers = array();
        foreach ($saved_field_options as $key_option => $value_option ) {
            $headers[$key_option] = stripslashes( $value_option ) .'('.$key_option.')';
        }
    }
    $dummy_shipment 	= wpcie_get_shipment_template( $headers );
    fputcsv( $csv_file, array_values( $headers ) );
    fputcsv( $csv_file, $dummy_shipment );
    fclose($csv_file);
    require_once( wpcie_admin_include_template( 'form-import.tpl' ) );
}
function wpcie_export_form( $fields = array(), $taxonomy = array(), $page ='') {
    global $wpcargo;
    add_action( 'wp_ajax_update_import_option_ajax_request',  'update_import_option_ajax_request' );
    $wpcargo_option_settings 	= get_option('wpcargo_option_settings');
    $get_all_status 			= $wpcargo_option_settings['settings_shipment_status'];
    $shipment_status 			= explode(",", $get_all_status);
    $shipment_status	  		= apply_filters( 'wpcargo_status_option', $shipment_status  );
    $selected_status			= isset($_REQUEST['wpcargo_status']) ? $_REQUEST['wpcargo_status'] : '';
    $options 					= get_option( 'multiselect_settings' );
    if( !empty( $options ) ){
        if( array_key_exists( 0, $options ) ){
            $options = array();	
        }
    }
    require_once( wpcie_admin_include_template( 'form-export.tpl' ) );
}
// Page header
function wpcie_page_header_tab(){
    $importExport = new WPCargo_Import_Export;
    $view = $_GET['page'];
    ?>
    <div class="wpc-ie-tab">
        <h2 class="nav-tab-wrapper">
        <a href="<?php echo admin_url( 'edit.php?post_type=wpcargo_shipment&page=wpcie-export' );?>" class="nav-tab<?php if($view == 'wpcie-export') { ?> nav-tab-active<?php } ?>"><?php esc_html_e( "Export Shipment", 'wpc-import-export' ); ?> </a>
        <a href="<?php echo admin_url( 'edit.php?post_type=wpcargo_shipment&page=wpcie-import' );?>" class="nav-tab<?php if($view == 'wpcie-import') { ?> nav-tab-active<?php } ?>"><?php esc_html_e( "Import Shipment", 'wpc-import-export' ); ?> </a>
        </h2>
    </div>
    <?php
    if( $view == 'wpcie-export' ){
        $importExport->wpc_export_request( );
    }elseif( $view == 'wpcie-import' ){
        $importExport->wpc_import_request( );
    }
}
// Frontend Manager Hooks
function wpcie_import_export_sidebar_menu( $menu_array ){
    if( function_exists('wpcfe_admin_page') && !wpcie_disable() ){
        $menu_array['wpcie-menu'] = array(
            'label' => __('Import/Export', 'wpc-import-export'),
            'permalink' => get_the_permalink( wpcie_get_frontend_page() ),
            'icon' => 'fa-recycle'
        ) ;
    }
    return $menu_array;
}
add_filter('wpcfe_after_sidebar_menus', 'wpcie_import_export_sidebar_menu', 10, 1 );
function wpcie_styles( $styles ){
    $styles[] = 'wpcie_styles';
    return $styles;
}
add_filter('wpcfe_registered_styles', 'wpcie_styles', 10, 1 );
function wpcie_import_export_multiselect_script( $scripts ){
    $scripts[] = 'multiselect_js';
	$scripts[] = 'wpc-import-export-frontend-script';
    return $scripts;
}
add_filter('wpcfe_registered_scripts', 'wpcie_import_export_multiselect_script', 10, 1 );
// Remove Report WPCargo FREE submenu
add_action( 'plugins_loaded', function(){
    global $wpc_export_admin;
    remove_action('admin_menu', array( $wpc_export_admin,'wpc_import_export_submenu_page') );
} );
