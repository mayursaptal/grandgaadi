<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
function wpcfe_register_settings() {
    global $WPCCF_Fields;
    $shipper_fields = array();
    if( class_exists( 'WPCCF_Fields' ) ){
        $shipper_fields 			= $WPCCF_Fields->get_field_key('shipper_info');
    }
    if( !empty( $shipper_fields ) ){
        foreach( $shipper_fields as $field ){
            register_setting( 'wpcfe_settings_group', 'wpcfe_regmap_'.trim($field['field_key']) );
        }
    }
    register_setting( 'wpcfe_settings_group', 'wpcfe_admin' );
    register_setting( 'wpcfe_settings_group', 'wpcfe_waybill_paper_size' );
    register_setting( 'wpcfe_settings_group', 'wpcfe_waybill_paper_orient' );
    register_setting( 'wpcfe_settings_group', 'wpcfe_update_shipment_role' );
    register_setting( 'wpcfe_settings_group', 'wpcfe_add_shipment_deactivated' );
    register_setting( 'wpcfe_settings_group', 'wpcfe_delete_shipment_role' );
    register_setting( 'wpcfe_settings_group', 'wpcfe_access_dashboard_role' );
    register_setting( 'wpcfe_settings_group', 'wpcfe_employee_all_access' );
    register_setting( 'wpcfe_settings_group', 'wpcfe_bol_enable' );
    register_setting( 'wpcfe_settings_group', 'wpcfe_rtl_enable' );
    register_setting( 'wpcfe_settings_group', 'wpcfe_customfont_enable' );
    register_setting( 'wpcfe_settings_group', 'wpcfe_checkout_print' );
    register_setting( 'wpcfe_settings_group', 'wpcfe_client_can_add_shipment' );
    register_setting( 'wpcfe_settings_group', 'wpcfe_default_status' );
    register_setting( 'wpcfe_settings_group', 'wpcfe_approval_registration' );
    register_setting( 'wpcfe_settings_group', 'wpcfe_disable_registration' );
    register_setting( 'wpcfe_settings_group', 'wpcfe_enable_label_multiple_print' );
    register_setting( 'wpcfe_settings_group', 'wpcfe_label_pagination_template' );
} 
add_action( 'admin_init', 'wpcfe_register_settings' );
function wpfe_dashboard_register_meta_boxes() {
    add_meta_box( 
        'wpfe_dashboard-id', 
        __( 'WPCargo Dashboard Attributes', 'wpcargo-frontend-manager' ), 
        'wpcfe_page_attributes_callback', 
        'page',
        'side',
        'high'
    );
}
add_action( 'add_meta_boxes', 'wpfe_dashboard_register_meta_boxes' );
function wpcfe_settings_navigation(){
    $view = $_GET['page'];
    ?>
    <a class="nav-tab <?php echo ( $view == 'wpcfe-settings') ? 'nav-tab-active' : '' ;  ?>" href="<?php echo admin_url().'admin.php?page=wpcfe-settings'; ?>" ><?php esc_html_e('Frontend Dashboard', 'wpcargo-frontend-manager' ); ?></a>
    <?php
}
//** Add plugin Setting navigation to the WPCargo settings
add_action( 'wpc_add_settings_nav', 'wpcfe_settings_navigation' );
function wpcfe_page_attributes_callback( $post ){
    ob_start();
    $menu_icon = get_post_meta( $post->ID, 'wpcfe_menu_icon', true );
    ?>
    <div id="wpcfe-menu-icon-wrapper">
        <p><span class="dashicons dashicons-admin-customizer" style="color: #82878c;"></span> <?php esc_html_e('Menu Icon Class', 'wpcargo-frontend-manager' ); ?></p>
        <input name="wpcfe_menu_icon" type="text" id="wpcfe_menu_icon" value="<?php echo $menu_icon; ?>">
        <p class="description"><?php esc_html_e('Note: This menu icon will display only in WPCargo Dashboard Template. You can find icons available in', 'wpcargo-frontend-manager' ); ?> <a href="https://fontawesome.com/icons?d=gallery&m=free" target="_blank">Fontawesome</a> <?php esc_html_e( 'ei. dashboard', 'wpcargo-frontend-manager' ); ?></p>
    </div>
    <?php
    $output = ob_get_clean();
    echo $output;
}
function wpcfe_save_icon_callback( $post_id ){
    if ( isset( $_POST['wpcfe_menu_icon'] ) ) {
        update_post_meta( $post_id, 'wpcfe_menu_icon', sanitize_text_field( $_POST['wpcfe_menu_icon'] ) );
    }
    if ( isset( $_POST['wpcfe_admin'] ) ) {
        update_post_meta( $post_id, 'wpcfe_admin', sanitize_text_field( $_POST['wpcfe_admin'] ) );
    }else{
        update_post_meta( $post_id, 'wpcfe_admin', 0 );
    }
}
add_action( 'save_post', 'wpcfe_save_icon_callback' );