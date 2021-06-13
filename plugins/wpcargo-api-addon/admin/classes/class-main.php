<?php
if (!defined('ABSPATH')){
    exit; // Exit if accessed directly
}
class WPCargo_API_Main {
	function __construct(){
		register_activation_hook( WPC_API_FILE_DIR, array( $this, 'add_roles_on_plugin_activation' ) );
		register_deactivation_hook( WPC_API_FILE_DIR, array( $this, 'remove_roles_on_plugin_deactivation' ) );
		add_action( 'show_user_profile', array( $this, 'api_profile_fields' ) );
		add_action( 'edit_user_profile', array( $this, 'api_profile_fields' ) ) ;
		add_action( 'personal_options_update', array( $this, 'save_api_profile_fields' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_api_profile_fields' ) );

		add_shortcode( 'wpcargo_api_account', array( $this, 'api_account_callback' ) );
	}
	function add_roles_on_plugin_activation(){
		add_role(
			'wpcargo_api_manager',
			__('WPCargo API Manager','wpcargo-api' ),
			array(
			'read' 		=> true,
			'level_0' 	=> true
			)
		);
	}
	function remove_roles_on_plugin_deactivation(){
		remove_role( 'wpcargo_api_manager' );
	}
	function api_profile_fields( $user ){
		$user_roles 	= $user->roles;
		$allowed_roles 	= wpcapi_allowed_role();
		if( !array_intersect( $user_roles, $allowed_roles) ){
			return false;
		}
		$user_api_key = get_user_meta( $user->ID, 'wpcargo_api', true );
		?>
		<h3><?php _e( 'WPCargo API', 'wpcargo-api' ); ?></h3>
	    <table class="form-table">
	   	 <tr>
	   		 <th><label for="wpcargo_api"><?php _e( 'API Key', 'wpcargo-api' ); ?></label></th>
	   		 <td><input class="regular-text" type="text" name="wpcargo_api" id="wpcargo_api" value="<?php echo $user_api_key; ?>" /> <button id="generate-api-key" class="button button-secondary button-wpcargo"><?php _e( 'Generate API Key', 'wpcargo-api' ); ?></button></td>
	   	 </tr>
	    </table>
		<?php
	}
	function save_api_profile_fields( $user_id ){
		if ( !current_user_can( 'edit_user', $user_id ) )
		return false;
		if( isset( $_POST['wpcargo_api'] ) ){
			update_usermeta( $user_id, 'wpcargo_api', sanitize_text_field( $_POST['wpcargo_api'] ) );
		}
	}
	function api_account_callback( ) {
		ob_start();
		?><div id="wpcapi-wrapper"><?php
			if( !is_user_logged_in() ){
				?><div id="login" style="width:360px;"><?php
					wp_login_form();
				?></div><?php
			}else{
				$current_user 	=	wp_get_current_user();
				$user_roles 	= $current_user->roles;
				$allowed_roles 	= wpcapi_allowed_role();
				if( !array_intersect( $user_roles, $allowed_roles) ){
					?>
					<div id="wpcapi-info" style="text-align:center;maring:36px 0;">
						<img src="<?php echo WPC_API_URL; ?>assets/images/lock.png" alt="Lock">
						<p><?php _e("Sorry you don't have enough permission to access this page. Please Contact Support or Administrator.", 'wpcargo-api' ); ?></p>
					</div>
					<?php
				}else{
					$user_info 		= get_userdata( get_current_user_id() );
					$display_name   = wpcapi_display_user_name( $user_info->ID );
					$user_api 		= wpcapi_get_user_api( $user_info->ID );
					require_once( WPC_API_PATH.'/templates/account-tpl.php');
				}
			}
			$output = ob_get_clean();
		?></div><?php
		return $output;
	}
}
new WPCargo_API_Main;