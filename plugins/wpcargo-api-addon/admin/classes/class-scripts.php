<?php
if (!defined('ABSPATH')){
    exit; // Exit if accessed directly
}
class WPCargo_API_Scripts{
	public $text_domain = 'wpcargo-shipment-container';
	function __construct(){
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
	}
	function frontend_scripts() {
		global $post;
		wp_enqueue_style( 'wpcapi-styles', WPC_API_URL . 'assets/css/wpcapi-styles.css', array(), WPC_API_VERSION );
		if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'wpcargo_api_account') ) {
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'wpcapi-scripts', WPC_API_URL . 'assets/js/wpcapi-scripts.js', array( 'jquery' ), WPC_API_VERSION, true );
			$translation = array(
				'ajaxurl' 			=> admin_url( 'admin-ajax.php' ),
			);
			wp_localize_script( 'wpcapi-scripts', 'wpcaAPIAjaxHandler', $translation );
		}
	}
	function admin_scripts( $hooks ){

		wp_enqueue_style( 'wpcapi-styles', WPC_API_URL . 'admin/assets/css/wpcapi-admin-styles.css', array(), WPC_API_VERSION );

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'wpcapi-scripts', WPC_API_URL . 'admin/assets/js/wpcapi-admin-scripts.js', array( 'jquery' ), WPC_API_VERSION, true );
		$translation = array(
			'ajaxurl' 			=> admin_url( 'admin-ajax.php' ),
		);
		wp_localize_script( 'wpcapi-scripts', 'wpcaAPIAjaxHandler', $translation );

	}
}
new WPCargo_API_Scripts;