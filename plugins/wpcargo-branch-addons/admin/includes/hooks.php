<?php
if ( ! defined( 'ABSPATH' ) ) { die; }
add_filter( 'default_wpcargo_columns', 'wpcbm_assigned_branch_columns' );
function wpcbm_assigned_branch_columns( $columns ) {
	$shipment_branch = array( 'shipment_branch' => esc_html__( 'Shipment Branch', 'wpcargo-branches' ) );
	$position = count( $columns ) -1 ;
	$columns = array_slice($columns, 0, $position, true) + $shipment_branch + array_slice( $columns, $position, count($columns) - 1, true );
	return $columns;
}
add_action( 'manage_wpcargo_shipment_posts_custom_column', 'manage_wpcbm_assigned_branch_columns', 10, 2 );
function manage_wpcbm_assigned_branch_columns( $column, $post_id ) {
	if( $column == 'shipment_branch' ){
		echo wpcdm_get_branch_info( get_post_meta( $post_id, 'shipment_branch', true ) );
	}
}
add_filter( 'manage_edit-wpcargo_shipment_sortable_columns', 'wpcbm_assigned_branch_sortable_columns' );
function wpcbm_assigned_branch_sortable_columns( $columns ) {
	$columns['shipment_branch'] = 'shipment_branch';	
	return $columns;
}
/*
** Plugin Auto Update
*/
add_action( 'init', 'wpc_branch_manager_activate_au' );
function wpc_branch_manager_activate_au(){
	require_once( WPC_BRANCHES_PATH. 'admin/classes/class-autoupdate.php');
	$plugin_remote_path = 'http://www.wpcargo.com/repository/wpcargo-branch-addons/updates-php7.2.php';
	new WPCargo_Branch_Manager_AutoUpdate ( WPC_BRANCHES_VERSION, $plugin_remote_path, WPC_BRANCHES_BASENAME );
}
/*
** Load Plugin text domain
*/
function wpcbranch_plugins_loaded_callback(){
	wpc_branch_manager_load_textdomain();
	add_filter( 'wpcfe_get_users_wpcargo_client_list', 'wpcbranch_client_options' );
	add_filter( 'wpcfe_get_users_cargo_agent_list', 'wpcbranch_agent_options' );
	add_filter( 'wpcargo_pod_get_drivers_lists', 'wpcbranch_driver_options' );
	add_filter( 'wpcfe_is_user_shipment', 'wpcbranch_access_shipment_callback', 10, 2 );
}
add_action( 'plugins_loaded', 'wpcbranch_plugins_loaded_callback' );
function wpc_branch_manager_load_textdomain() {
	load_plugin_textdomain( 'wpcargo-branches', false, '/wpcargo-branch-addons/languages' );
}
// Frontend Manager Assignement options filter
function wpcbranch_client_options( $options ){
	$current_roles = wpcbranch_current_user_role();
	if( in_array( 'administrator', $current_roles ) ){
		return $options;
	}elseif( in_array( 'wpcargo_branch_manager', $current_roles ) && get_option('wpcbranch_restrict_all_clients')){
		return wpcbranch_registered_users('client');
	}
	return $options;
}
function wpcbranch_agent_options( $options ){
	$current_roles = wpcbranch_current_user_role();
	if( in_array( 'administrator', $current_roles ) ){
		return $options;
	}elseif( in_array( 'wpcargo_branch_manager', $current_roles ) && get_option('wpcbranch_restrict_all_agents')){
		return wpcbranch_registered_users('agent');
	}
	return $options;
}
function wpcbranch_employee_options( $options ){
	$current_roles = wpcbranch_current_user_role();
	if( in_array( 'administrator', $current_roles ) ){
		return $options;
	}elseif( in_array( 'wpcargo_branch_manager', $current_roles ) && get_option('wpcbranch_restrict_all_employees')){
		return wpcbranch_registered_users('employee');
	}
	return $options;
}
function wpcbranch_driver_options( $options ){
	$current_roles = wpcbranch_current_user_role();
	if( in_array( 'administrator', $current_roles ) ){
		return $options;
	}elseif( in_array( 'wpcargo_branch_manager', $current_roles ) && get_option('wpcbranch_restrict_all_drivers')){
		return wpcbranch_registered_users('driver');
	}
	return $options;
}
function wpcbranch_access_shipment_callback( $result, $shipment_id ){
	$current_roles = wpcbranch_current_user_role();
	if( in_array( 'wpcargo_branch_manager', $current_roles )
		&& get_post_meta( $shipment_id, 'wpcargo_branch_manager', true ) == get_current_user_id() ){
			$result = true;
	}
	return $result;
}
/*
** TABLE FILTERS HOOK
*/
add_action('restrict_manage_posts', 'wpcbm_assigned_branch_filter');
function wpcbm_assigned_branch_filter(){
	global $typenow;
	$post_type = 'wpcargo_shipment'; // change to your post type
	if ($typenow == $post_type) {
		$all_branch = wpcbm_get_all_branch( -1 );
		if( !empty( $all_branch ) ){
			$shipment_branch = isset( $_GET['shipment_branch'] ) ? $_GET['shipment_branch'] : 0 ;
			?>
			<select id="wpc-user-branch" name="shipment_branch">
				<option value=""><?php esc_html_e( 'Select Branch', 'wpcargo-branches' ); ?></option>
				<?php
					foreach ( $all_branch as $branch ) {
						?><option value="<?php echo $branch->id; ?>" <?php selected( $shipment_branch, $branch->id ); ?>><?php echo $branch->name; ?></option><?php
					}
				?>
			</select>
			<?php
		}
	}
}
add_filter('wpcargo_shipment_query_filter', function( $metakey ){
	$metakey[] = 'shipment_branch';
	return $metakey;
});
add_action('wpc_after_shipment_designation', 'wpc_mb_assign_branch_manager');
function wpc_mb_assign_branch_manager( $shipment_id ){
	?>
	<div class="section-wrapper">
		<div class="label-section"><label><strong><label><?php esc_html_e('Branch Manager', 'wpcargo-branches'); ?></label></strong></label></div>
		<div class="select-section">
			<select name="wpcargo_branch_manager" class="mdb-select mt-0 form-control browser-default" id="wpcargo_branch_manager">
			<option value=""><?php esc_html_e('-- Select Branch Manager --','wpcargo-branches'); ?></option>
			<?php if( !empty( wpcargo_get_branch_managers() ) ): ?>
				<?php foreach( wpcargo_get_branch_managers() as $branch_managerID => $branch_manager_name ): ?>
					<option value="<?php echo $branch_managerID; ?>" <?php selected( get_post_meta( $shipment_id, 'wpcargo_branch_manager', TRUE ), $branch_managerID ); ?>><?php echo $branch_manager_name; ?></option>
				<?php endforeach; ?>	
			<?php  endif; ?>	                
			</select>
		</div>
	</div>
	<?php
}
add_filter('wpcfe_registered_scripts', 'wpcbm_frontend_scripts', 10, 1 );
function wpcbm_frontend_scripts( $scripts ){
	$scripts[] = 'wpcbm-frontend-scripts';
	return $scripts;
}
add_action( 'wpcfe_after_designation_dropdown', 'assign_branch_manager_dropdown' );
function assign_branch_manager_dropdown( $shipment_id ){
	$branch = get_post_meta( $shipment_id, 'shipment_branch', true );
	$get_branch = wpcdm_get_branch( $branch );
	$branch_managers = unserialize( $get_branch['branch_manager'] );
	if( can_wpcfe_assign_branch_manager() ): ?>
		<div class="form-group">
			<div class="select-no-margin">
				<label><?php esc_html_e('Branch Manager','wpcargo-branches'); ?></label>
				<?php if( !empty( $branch ) ): ?>
					<select name="wpcargo_branch_manager" class="mdb-select mt-0 form-control browser-default" id="wpcargo_branch_manager">
						<option value=""><?php esc_html_e('-- Select Branch Manager --', 'wpcargo-branches'); ?></option>
						<?php if( !empty( wpcargo_get_branch_managers() ) ): ?>
							<?php foreach( wpcargo_get_branch_managers() as $branch_managerID => $branch_manager_name ): ?>
								<?php if( in_array( $branch_managerID, $branch_managers ) ): ?>
									<option value="<?php echo $branch_managerID; ?>" <?php selected( get_post_meta( $shipment_id, 'wpcargo_branch_manager', TRUE ), $branch_managerID ); ?>><?php echo $branch_manager_name; ?></option>
								<?php endif; ?>
							<?php endforeach; ?>	
						<?php endif; ?>	                
					</select>
				<?php else: ?>
					<select name="wpcargo_branch_manager" class="mdb-select mt-0 form-control browser-default" id="wpcargo_branch_manager" disabled>
						<option value=""><?php esc_html_e('-- Select Branch Manager --', 'wpcargo-branches'); ?></option>	                
					</select>
					<i class="text-danger empty-branch-notice"><?php esc_html_e('Please select branch before assigning branch manager.','wpcargo-branches'); ?></i>
				<?php endif; ?>
			</div>
		</div>
	<?php endif;
}
add_filter( 'wpcfe_assign_agent', 'wpc_branch_capabilities' );
add_filter( 'wpcfe_assign_client', 'wpc_branch_capabilities' );
add_filter( 'wpcfe_add_shipment_role', 'wpc_branch_capabilities' );
function wpc_branch_capabilities( $users ){
	$users[] = 'wpcargo_branch_manager';
	return $users;
}
add_action( 'after_wpcfe_save_shipment', 'wpcb_assign_current_bm_to_shipment', 10, 2 );
function wpcb_assign_current_bm_to_shipment( $shipment_id, $data ){
    $current_user = wp_get_current_user();
    $user_role = $current_user->roles;
    if( in_array( 'wpcargo_branch_manager', $user_role ) ){
        update_post_meta( $shipment_id, 'wpcargo_branch_manager', (int)$current_user->ID );
    }
}
add_action( 'after_wpcfe_save_shipment', 'wpcbm_assign_branch_manager_save', 10, 2 );
function wpcbm_assign_branch_manager_save( $shipment_id, $data ){
	if( isset( $data['wpcargo_branch_manager'] ) && (int)$data['wpcargo_branch_manager'] && can_wpcfe_assign_manager() ){
        $old_manager = get_post_meta( $shipment_id, 'wpcargo_branch_manager', true );
        update_post_meta( $shipment_id, 'wpcargo_branch_manager', (int)$data['wpcargo_branch_manager'] );
        // check if the manager is changed Send email notification
        if( $old_manager != (int)$data['wpcargo_branch_manager'] && wpcdm_can_send_email_branch_manager() ){
            wpcargo_assign_shipment_email( $shipment_id, (int)$data['wpcargo_branch_manager'], esc_html__('Branch Manager', 'wpcargo-branches' ) );
        }
    }
    if( isset( $data['shipment_branch'] ) ){
        update_post_meta( $shipment_id, 'shipment_branch', (int)$data['shipment_branch'] );
    }
}
add_action( 'before_wpcfe_shipment_form_submit', 'wpcbm_assigned_branch', 10, 2 );
function wpcbm_assigned_branch( $shipment_id ){
	$shipment       = new stdClass();
	$all_branch		= wpcbm_get_all_branch( -1 );
	$shipment_branch = get_post_meta( $shipment_id, 'shipment_branch', true ) ? get_post_meta( $shipment_id, 'shipment_branch', true ) : '';
	?>
	<div class="card mb-4">
		<section class="card-header">
			<?php echo wpcdm_assign_branch_label(); ?>
		</section>
		<section class="card-body">
			<div class="form-row">
				<?php if( empty( can_wpcfe_assign_branch_manager() ) ): ?>
					<p><strong><?php echo wpcdm_get_branch_info( $shipment_branch ); ?></strong></p>
				<?php else: ?>
					<p>
					<?php if( !empty( $all_branch ) ): ?>
						<select id="wpc-user-branch" name="shipment_branch" class="mdb-select mt-0 form-control browser-default">
							<option value=""><?php echo wpcdm_select_branch_label(); ?></option>
							<?php foreach ( $all_branch as $branch ): ?>
								<option value="<?php echo $branch->id; ?>" <?php selected( $shipment_branch, $branch->id ); ?>><?php echo $branch->name; ?></option>
							<?php endforeach; ?>
						</select>
					<?php else: ?>
						<i><?php esc_html_e('No available branches.', 'wpcargo-branches' ).' * '; ?></i>
					<?php endif; ?>
					</p>
				<?php endif; ?>
			</div>
		</section>
	</div>
	<?php
}
add_action( 'wpcargo_after_assign_email', 'wpcbm_assign_email_options' );
function wpcbm_assign_email_options( $options ){
	?>
	<tr>
		<th><?php esc_html_e( 'Disable Email for Branch Manager?', 'wpcargo-branches' ) ; ?></th>
		<td>
			<input type="checkbox" name="wpcargo_option_settings[wpcargo_email_branch_manager]" <?php  echo ( !empty( $options['wpcargo_email_branch_manager'] ) && $options['wpcargo_email_branch_manager'] != NULL  ) ? 'checked' : '' ; ?> />
		</td>
	</tr>
	<?php
}
