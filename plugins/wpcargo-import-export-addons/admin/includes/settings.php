<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}
// Register Settings
function register_wpcie_settings() {
    //register our settings
    register_setting( 'wpcie_registered_settings_group', 'wpcie_disable' );
    register_setting( 'wpcie_registered_settings_group', 'wpcie_restricted_role' );
}
add_action( 'admin_init', 'register_wpcie_settings' );
// Settings page
function wpcie_settings_menu_callback(){
    global $wp_roles, $wpcargo;
    $roles      = $wp_roles->get_names();
    $rest_roles = wpcie_restricted_role();
    ?>
    <div class="wrap">
        <?php require_once( WPCARGO_PLUGIN_PATH.'admin/templates/admin-navigation.tpl.php' ); ?>		
        <?php require_once( WPC_IMPORT_EXPORT_PATH.'admin/templates/settings.tpl.php' ); ?>
    </div>
    <?php
}
function wpcie_settings_menu(){		
    add_submenu_page( 
        'wpcargo-settings', 
        __('Import/Export Settings', 'wpc-import-export'),
        __('Import/Export Settings', 'wpc-import-export'),
        'manage_options',
        'admin.php?page=wpcie-settings'
    );
    add_submenu_page( 
        NULL, 
        __('Import/Export Settings', 'wpc-import-export'),
        __('Import/Export Settings', 'wpc-import-export'),
        'manage_options',
        'wpcie-settings',
        'wpcie_settings_menu_callback'
    );
}
add_action('admin_menu', 'wpcie_settings_menu' );
function wpcie_add_settings_nav_sms() {	
    $view = isset( $_GET['page'] ) ? $_GET['page'] : '';
    ?>
    <a class="nav-tab <?php echo ( $view == 'wpcie-settings') ? 'nav-tab-active' : '' ;  ?>" href="<?php echo admin_url().'admin.php?page=wpcie-settings'; ?>" ><?php _e( 'Import/Export', 'wpc-import-export' ); ?></a>		
    <?php	
}
add_action('wpc_add_settings_nav', 'wpcie_add_settings_nav_sms' );