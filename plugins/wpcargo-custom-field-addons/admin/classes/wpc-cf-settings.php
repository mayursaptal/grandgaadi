<?php
if (!defined('ABSPATH')){
    exit; // Exit if accessed directly
}
class WPC_CF_Settings{
	public $text_domain = 'wpcargo-custom-field';
	public function __construct(){
		add_action('admin_menu', array( $this, 'register_custom_field_submenu_page' ) );
		add_action( 'admin_init', array( $this, 'register_custom_field_settings') );
	}
	public function register_custom_field_submenu_page() {
		add_submenu_page(
			NULL,
			__( 'Custom Field Setting', 'wpcargo-custom-field' ),
			__( 'Custom Field Setting', 'wpcargo-custom-field' ),
			'manage_options',
			'wpc-custom-field-settings',
			array( $this, 'register_custom_field_submenu_page_callback' )
		);
		add_submenu_page( 
			'wpcargo-settings',
			__( 'CF Setting', 'wpcargo-custom-field' ),
			__( 'CF Setting', 'wpcargo-custom-field' ),
			'manage_options',
			'admin.php?page=wpc-custom-field-settings'
		);
	}
	public function register_custom_field_submenu_page_callback(){
		$options = get_option('wpcargo_cf_option_settings');
		$additional_sections    = '';
        if( !empty( $options ) ){
            if( array_key_exists( 'wpc_cf_additional_options', $options )){
                $additional_sections    = $options['wpc_cf_additional_options'];
            }
        }
		ob_start();
		?>
        <div class="wrap">
        	<h1><?php esc_html_e('Custom Field Settings', 'wpcargo-custom-field' ); ?></h1>
            <?php require_once( WPCARGO_CUSTOM_FIELD_PATH.'../wpcargo/admin/templates/admin-navigation.tpl.php'); ?>
            <?php require_once( WPCARGO_CUSTOM_FIELD_PATH.'admin/templates/wpc-cf-settings.tpl.php'); ?>
        </div>
        <?php
		echo ob_get_clean();
	}
	function register_custom_field_settings() {
		//register our settings
		register_setting( 'wpcargo_custom_field_settings_group', 'wpcargo_cf_option_settings' );
		register_setting( 'wpcargo_custom_field_settings_group', 'wpcargo_cf_label_settings' );
	}
}
new WPC_CF_Settings;
