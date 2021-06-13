<div id="wpc-branch-wrapper">
	<table class="wpcargo-table branch-manager-list" style="border-collapse:collapse;">
		<thead>
			<tr>
				<th rowspan="2"><?php esc_html_e( 'Branch Name', 'wpcargo-branches' ); ?></th>
				<th rowspan="2"><?php esc_html_e( 'Branch Code', 'wpcargo-branches' ); ?></th>
				<th rowspan="2"><?php esc_html_e( 'Phone', 'wpcargo-branches' ); ?></th>
				<th rowspan="2"><?php esc_html_e( 'Address', 'wpcargo-branches' ); ?></th>
				<th rowspan="2"><?php esc_html_e( 'Branch Manager(s)', 'wpcargo-branches' ); ?></th>
				<th colspan="4" style="text-align:center;"><?php esc_html_e( 'Branch Manager Assigned Users', 'wpcargo-branches' ); ?></th>
				<th rowspan="2"><?php esc_html_e( 'Actions', 'wpcargo-branches' ); ?></th>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Branch Employee(s)', 'wpcargo-branches' ); ?></th>
				<th><?php esc_html_e( 'Branch Agent(s)', 'wpcargo-branches' ); ?></th>
				<th><?php esc_html_e( 'Branch Client(s)', 'wpcargo-branches' ); ?></th>
				<th><?php esc_html_e( 'Branch Driver(s)', 'wpcargo-branches' ); ?></th>	
			</tr>
		</thead>
		<tbody>
		<?php
			if( !empty( $all_branches ) ){
				foreach ( $all_branches as $branch ) {
					$branch_manager 	= array();
					$branch_employees 	= array();
					$branch_agents 		= array();
					$branch_clients 	= array();
					$branch_drivers 	= array();
					$unserialize_branch 	= unserialize( $branch->branch_manager );
					$unserialize_clients 	= unserialize( $branch->branch_client );
					$unserialize_agents 	= unserialize( $branch->branch_agent );
					$unserialize_employees 	= unserialize( $branch->branch_employee );
					$unserialize_drivers 	= unserialize( $branch->branch_driver );
					// Branch Manager
					if( $unserialize_branch ){
						foreach( $unserialize_branch as $branch_data ){
							$branch_manager[] = $wpcargo->user_fullname( $branch_data );
						}
					}
					if( $unserialize_clients ){
						foreach( $unserialize_clients as $client_data ){
							$branch_clients[] = $wpcargo->user_fullname( $client_data );
						}
					}
					if( $unserialize_agents ){
						foreach( $unserialize_agents as $agent_data ){
							$branch_agents[] = $wpcargo->user_fullname( $agent_data );
						}
					}
					if( $unserialize_employees ){
						foreach( $unserialize_employees as $employee_data ){
							$branch_employees[] = $wpcargo->user_fullname( $employee_data );
						}
					}
					if( $unserialize_drivers ){
						foreach( $unserialize_drivers as $driver_data ){
							$branch_drivers[] = $wpcargo->user_fullname( $driver_data );
						}
					}
					$assigned_bm 		= !empty( $branch_manager ) ? join('<br/>', $branch_manager ) : esc_html__( '--', 'wpcargo-branches' );
					$assigned_employee 	= !empty( $branch_employees ) ? join('<br/>', $branch_employees ) : esc_html__( '--', 'wpcargo-branches' );
					$assigned_agents 	= !empty( $branch_agents ) ? join('<br/>', $branch_agents ) : esc_html__( '--', 'wpcargo-branches' );
					$assigned_clients	= !empty( $branch_clients ) ? join('<br/>', $branch_clients ) : esc_html__( '--', 'wpcargo-branches' );
					$assigned_drivers	= !empty( $branch_drivers ) ? join('<br/>', $branch_drivers ) : esc_html__( '--', 'wpcargo-branches' );
					?>
					<tr id="branch-<?php echo $branch->id; ?>" class="branches">
						<td><?php echo $branch->name ?></td>
						<td><?php echo $branch->code; ?></td>
						<td><?php echo $branch->phone; ?></td>
						<td><?php echo wpcdm_display_address_format($branch->id); ?></td>
						<td><?php echo $assigned_bm; ?></td>
						<td><?php echo $assigned_employee; ?></td>
						<td><?php echo $assigned_agents; ?></td>
						<td><?php echo $assigned_clients; ?></td>
						<td><?php echo $assigned_drivers; ?></td>
						<td>
							<div class="action">
								<a href="#" class="edit" data-id="<?php echo $branch->id; ?>" ><span class="dashicons dashicons-edit"></span></a>
								<a href="#" class="delete" data-id="<?php echo $branch->id; ?>" ><span class="dashicons dashicons-trash"></span></a>
							</div>
						</td>
					</tr>
					<?php
				}
			}
		?>
		</tbody>
	</table>
</div>