<?php
class WPC_IE_Scripts{	
	function __construct(){
		add_action( 'admin_enqueue_scripts', array($this,'admin_scripts'), 25 );
		add_action( 'wp_enqueue_scripts', array($this,'frontend_scripts') );
	}
	function admin_scripts() {
		$screen = get_current_screen();	
		if( $screen->base == 'wpcargo_shipment_page_wpcie-import' || $screen->base == 'wpcargo_shipment_page_wpcie-export' ){
			wp_register_style( 'wpc_import_export_css', WPC_IMPORT_EXPORT_URL . 'admin/assets/css/wpcargo-import-export-style.css', false, WPC_IMPORT_EXPORT_VERSION );			
			wp_enqueue_style( 'wpc_import_export_css' );			
			wp_register_script( 'multiselect_js', WPC_IMPORT_EXPORT_URL . 'admin/assets/js/jquery.multiselect.js', array('jquery'), WPC_IMPORT_EXPORT_VERSION );		
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-autocomplete' );
			wp_enqueue_script( 'multiselect_js' );
			
			//** Ajax script
			wp_register_script( 'wpc-import-export-admin-script', WPC_IMPORT_EXPORT_URL . 'admin/assets/js/admin-ajax.js', array('jquery', 'wpcargo-datetimepicker') );
			wp_enqueue_script( 'wpc-import-export-admin-script' );
			wp_localize_script( 'wpc-import-export-admin-script', 'wpc_ie_ajaxscripthandler', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		}	
		if( $screen->base == 'admin_page_wpcie-settings' ){
			// Styles
			wp_register_style( 'wpcie_select2_css', WPC_IMPORT_EXPORT_URL . 'admin/assets/css/select2.min.css', false, WPC_IMPORT_EXPORT_VERSION );			
			wp_enqueue_style( 'wpcie_select2_css' );
			// Scripts
			wp_register_script( 'wpcie-selects-script', WPC_IMPORT_EXPORT_URL . 'admin/assets/js/select2.min.js', array('jquery') );
			wp_register_script( 'wpcie-settings-script', WPC_IMPORT_EXPORT_URL . 'admin/assets/js/settings.js', array('jquery') );
			wp_enqueue_script( 'wpcie-selects-script' );	
			wp_enqueue_script( 'wpcie-settings-script' );	
		}
	}
	function frontend_scripts(){
		global $post;
		wp_enqueue_style( 'wpcie_styles', WPC_IMPORT_EXPORT_URL . 'assets/css/style.css', false, WPC_IMPORT_EXPORT_VERSION );			
		if( !empty( $post ) && $post->ID == wpcie_get_frontend_page() ){
			wp_register_script( 'multiselect_js', WPC_IMPORT_EXPORT_URL . 'admin/assets/js/jquery.multiselect.js', array('jquery'), WPC_IMPORT_EXPORT_VERSION );
			wp_enqueue_script( 'multiselect_js' );
		}
		//** Ajax script
		wp_register_script( 'wpc-import-export-frontend-script', WPC_IMPORT_EXPORT_URL . 'assets/js/scripts.js', array('jquery'), WPC_IMPORT_EXPORT_VERSION, true );		
		wp_enqueue_script( 'wpc-import-export-frontend-script' );
		wp_localize_script( 'wpc-import-export-frontend-script', 'wpcieAjaxHandler', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	}
}
new WPC_IE_Scripts;