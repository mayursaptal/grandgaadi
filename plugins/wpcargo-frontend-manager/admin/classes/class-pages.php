<?php
class WPCFE_Admin{
	public static function add_wpcfe_custom_pages() {
		$wpcfe_admin =  get_option( 'wpcfe_admin' );
		if ( get_page_by_path('dashboard') == NULL && !$wpcfe_admin ) {
			$dashboard_args    = array(
				'comment_status' => 'closed',
				'ping_status' => 'closed',
				'post_author' => 1,
				'post_date' => date('Y-m-d H:i:s'),
				'post_name' => 'dashboard',
				'post_status' => 'publish',
				'post_title' => 'Dashboard',
				'post_type' => 'page',
			);
			$dashboard = wp_insert_post( $dashboard_args, false );
			update_option( 'wpcfe_admin', $dashboard );
			update_post_meta( $dashboard, '_wp_page_template', 'dashboard.php' );
		}
	}
}