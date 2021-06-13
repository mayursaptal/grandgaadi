<div id="editBranchModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="header">
			<h1>
				<?php esc_html_e('Update Branch', 'wpcargo-branches' ); ?>
				<span class="close">x</span>
			</h1>
        </div>
        <div class="content">
            <form id="edit-branch">
				<table class="add-branch-table" width="100%">
					<tr>
						<td><label for="update-branch-name"><?php esc_html_e('Branch Name', 'wpcargo-branches' ); ?></label>:</td>
						<td><input id="update-branch-name" type="text" name="name" placeholder="<?php esc_html_e('Branch Name', 'wpcargo-branches' ); ?>" required="required"></td>
					</tr>
					<tr>
						<td><label for="update-branch-code"><?php esc_html_e('Code', 'wpcargo-branches' ); ?></label>:</td>
						<td><input id="update-branch-code" type="text" name="code" placeholder="<?php esc_html_e('Code', 'wpcargo-branches' ); ?>" required="required"></td>
					</tr>
					<tr>
						<td><label for="update-branch-phone"><?php esc_html_e('Phone', 'wpcargo-branches' ); ?></label>:</td>
						<td><input id="update-branch-phone" type="text" name="phone" placeholder="<?php esc_html_e('Phone', 'wpcargo-branches' ); ?>" required="required"></td>
					</tr>
					<tr>
						<td><label for="update-branch-address1"><?php esc_html_e('Address1', 'wpcargo-branches' ); ?></label>:</td>
						<td><input type="text" id="update-branch-address1" name="address1" placeholder="<?php esc_html_e('Address1', 'wpcargo-branches' ); ?>" required="required"></td>
					</tr>
					<tr>
						<td><label for="update-branch-address2"><?php esc_html_e('Address2', 'wpcargo-branches' ); ?></label>:</td>
						<td><input type="text" id="update-branch-address2" name="address2" placeholder="<?php esc_html_e('Address2', 'wpcargo-branches' ); ?>" ></td>
					</tr>
					<tr>
						<td><label for="update-branch-city"><?php esc_html_e('City', 'wpcargo-branches' ); ?></label>:</td>
						<td><input type="text" id="update-branch-city" name="city" placeholder="<?php esc_html_e('City', 'wpcargo-branches' ); ?>" required="required"></td>
					</tr>
					<tr>
						<td><label for="update-branch-postcode"><?php esc_html_e('Postcode / ZIP', 'wpcargo-branches' ); ?></label>:</td>
						<td><input type="text" id="update-branch-postcode" name="postcode" placeholder="<?php esc_html_e('Postcode / ZIP', 'wpcargo-branches' ); ?>" required="required"></td>
					<tr>
						<td><label for="update-branch-country"><?php esc_html_e('Country', 'wpcargo-branches' ); ?></label>:</td>
						<td><input type="text" id="update-branch-country" name="country" placeholder="<?php esc_html_e('Country', 'wpcargo-branches' ); ?>" required="required"></td>
					</tr>
					<tr>
						<td><label for="update-branch-state"><?php esc_html_e('State / County', 'wpcargo-branches' ); ?></label>:</td>
						<td><input type="text" id="update-branch-state" name="state" placeholder="<?php esc_html_e('State / County', 'wpcargo-branches' ); ?>" required="required"></td>
					</tr>
					<tr>
						<td><label for="update-branch-manager"><?php esc_html_e('Branch Manager(s)', 'wpcargo-branches' ); ?></label>:</td>
						<td>
							<select name="update-branch-manager" class="select-bm" id="update-branch-manager" multiple>
								<option value="" disabled>-- <?php esc_html_e('Select Branch Manager', 'wpcargo-branches' );?> --</option>
								<?php foreach( wpcargo_get_branch_managers() as $user_id => $username ): ?>
									<option value="<?php echo $user_id; ?>"><?php echo $username; ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<td><label for="update-branch-employee"><?php esc_html_e('Branch Employee(s)', 'wpcargo-branches' ); ?></label>:</td>
						<td>
							<select name="update-branch-employee" class="select-bm" id="update-branch-employee" multiple>
								<option value="" disabled>-- <?php esc_html_e('Select Branch Employee', 'wpcargo-branches' );?> --</option>
								<?php foreach( wpcbranch_get_user_list( 'wpcargo_employee' ) as $user_id => $username ): ?>
									<option value="<?php echo $user_id; ?>"><?php echo $username; ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<td><label for="update-branch-agent"><?php esc_html_e('Branch Agent(s)', 'wpcargo-branches' ); ?></label>:</td>
						<td>
							<select name="update-branch-agent" class="select-bm" id="update-branch-agent" multiple>
								<option value="" disabled>-- <?php esc_html_e('Select Branch Agent', 'wpcargo-branches' );?> --</option>
								<?php foreach( wpcbranch_get_user_list( 'cargo_agent' ) as $user_id => $username ): ?>
									<option value="<?php echo $user_id; ?>"><?php echo $username; ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<td><label for="update-branch-client"><?php esc_html_e('Branch Client(s)', 'wpcargo-branches' ); ?></label>:</td>
						<td>
							<select name="update-branch-client" class="select-bm" id="update-branch-client" multiple>
								<option value="" disabled>-- <?php esc_html_e('Select Branch Client', 'wpcargo-branches' );?> --</option>
								<?php foreach( wpcbranch_get_user_list( 'wpcargo_client' ) as $user_id => $username ): ?>
									<option value="<?php echo $user_id; ?>"><?php echo $username; ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<td><label for="update-branch-driver"><?php esc_html_e('Branch Driver(s)', 'wpcargo-branches' ); ?></label>:</td>
						<td>
							<select name="update-branch-driver" class="select-bm" id="update-branch-driver" multiple>
								<option value="" disabled>-- <?php esc_html_e('Select Branch Driver', 'wpcargo-branches' );?> --</option>
								<?php foreach( wpcbranch_get_user_list( 'wpcargo_driver' ) as $user_id => $username ): ?>
									<option value="<?php echo $user_id; ?>"><?php echo $username; ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<input type="hidden" id="branchid" name="branchid" value="">
							<input type="submit" class="button button-primary button-large" name="submit" value="<?php esc_html_e('Update Branch', 'wpcargo-branches' ); ?>">
						</td>
					</tr>
				</table>
            </form>
        </div>
  	</div>
</div>