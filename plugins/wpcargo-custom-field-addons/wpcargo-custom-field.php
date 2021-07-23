<?php
/*
 * Plugin Name: WPCargo Custom Field Add-ons
 * Plugin URI: http://wpcargo.com/
 * Description: Allows you to customized the fields and your needs to display at the front-end. Requires WPCargo plugin to work.
 * Author: <a href="http://wptaskforce.com/">WPTaskForce</a>
 * Text Domain: wpcargo-custom-field
 * Domain Path: /languages
 * Version: 4.8.4
 */
if ( ! defined( 'ABSPATH' ) ) {
 exit;
}
//* Defined constant
define( 'WPCARGO_CUSTOM_FIELD_VERSION', "4.8.4");
define( 'WPCARGO_CUSTOM_FIELD_TEXTDOMAIN', 'wpcargo-custom-field' );
define( 'WPCARGO_CUSTOM_FIELD_FILE', __FILE__ );
define( 'WPCARGO_CUSTOM_FIELD_URL', plugin_dir_url( __FILE__ ) );
define( 'WPCARGO_CUSTOM_FIELD_PATH', plugin_dir_path( __FILE__ ) );
define( 'WPCARGO_CUSTOM_FIELD_BASENAME', plugin_basename( __FILE__ ) );

//** Include necessary files
require_once(WPCARGO_CUSTOM_FIELD_PATH.'admin/includes/functions.php');
require_once(WPCARGO_CUSTOM_FIELD_PATH.'admin/admin.php');
require_once(WPCARGO_CUSTOM_FIELD_PATH.'admin/classes/wpccf-fields.php');
require_once(WPCARGO_CUSTOM_FIELD_PATH.'admin/classes/wpc-cf-check-installed-plugin.php');
require_once(WPCARGO_CUSTOM_FIELD_PATH.'admin/classes/wpc-cf-install-db.php');
require_once(WPCARGO_CUSTOM_FIELD_PATH.'admin/classes/wpc-cf-form-builder.php');
require_once(WPCARGO_CUSTOM_FIELD_PATH.'admin/classes/wpc-cf-settings.php');
require_once(WPCARGO_CUSTOM_FIELD_PATH.'/classes/wpc-cf-scripts.php');
require_once(WPCARGO_CUSTOM_FIELD_PATH.'/classes/wpc-cf-filters.php');
require_once(WPCARGO_CUSTOM_FIELD_PATH.'/classes/wpc-cf-hooks.php');
require_once(WPCARGO_CUSTOM_FIELD_PATH.'/includes/wpc-cf-functions.php');
require_once(WPCARGO_CUSTOM_FIELD_PATH.'/includes/dropzone.php');
add_action( 'plugins_loaded', 'wpcargo_custom_fields_load_textdomain' );
function wpcargo_custom_fields_load_textdomain() {
	load_plugin_textdomain( 'wpcargo-custom-field', false, '/wpcargo-custom-field-addons/languages' );
}
add_action('wpc_add_settings_nav','wpc_cf_settings_navigation');
add_action( 'plugins_loaded', function(){
	remove_action( 'wpcargo_fields_option_settings_group', 'wpcargo_fields_option_settings_group_callback', 10 );
}, 100 );
function wpc_cf_settings_navigation(){
	$view = $_GET['page'];
	?>
    <a class="nav-tab <?php echo ( $view == 'wpc-custom-field-settings') ? 'nav-tab-active' : '' ;  ?>" href="<?php echo admin_url(); ?>admin.php?page=wpc-custom-field-settings"><?php esc_html_e('Custom Field Settings', 'wpcargo-custom-field'); ?></a>
    <?php
}
register_activation_hook( __FILE__, array( $wpcargo_cf_check_installed_plugin, 'check_wpcargo') );
register_activation_hook(__FILE__, array( 'WPCargo_Custom_Fields_Install_DB', 'plugin_activated' ) );
register_activation_hook(__FILE__, array( 'WPCargo_Custom_Fields_Install_DB', 'add_sample_shipment' ) );
add_action( 'plugins_loaded', array( 'WPCargo_Custom_Fields_Install_DB', 'update_table' ) );