<div class="wpcargo-add-form-fields postbox" style="clear:both;">
    <div class="inside">
    	<h2><?php esc_html_e('Add Form Field', 'wpcargo-custom-field' ); ?></h2>
        <form method="post" action="<?php echo admin_url(); ?>admin.php?page=wpc-cf-manage-form-field&action=add">
        <?php wp_nonce_field( 'wpc_cf_custom_field_action', 'wpc_cf_custom_field' ); ?>
        <table class="wpcargo form-table">
        	<?php do_action('wpc_cf_before_form_field_add'); ?>
        	<tr>
                <th><?php esc_html_e('Field Type (required)', 'wpcargo-custom-field' ); ?></th>
                <td>
                    <select name="field_type" id="field-type" required >
                    	<option value=""><?php esc_html_e('--Select One--', 'wpcargo-custom-field' ); ?></option>
                        <?php if( !empty( wpccf_field_type_list() ) ): ?>
                            <?php foreach ( wpccf_field_type_list() as $list_key => $list_value): ?>
                                <option value="<?php echo $list_key; ?>"><?php echo $list_value; ?></option>
                            <?php endforeach; ?>    
                        <?php endif; ?>
                    </select>
                </td>
            </tr>
            <tr>
            	<th><?php esc_html_e('Field Label (required)', 'wpcargo-custom-field' ); ?></th>
                <td><input type="text" name="label" required="required" /></td>
            </tr>
            <tr>
            	<th><?php esc_html_e('Field description (optional)', 'wpcargo-custom-field' ); ?></th>
                 <td><textarea name="description" type="text"></textarea></td>
            </tr>
            <tr id="select-list">
            	<th><?php esc_html_e('Field Options for select lists, radio buttons and checkboxes(required)', 'wpcargo-custom-field' ); ?></th>
                <td>
                	<textarea name="field_data" cols="50" rows="2" readonly="readonly"></textarea>
                    <?php esc_html_e('Comma (,) separated list of options', 'wpcargo-custom-field' ); ?>
                </td>
            </tr>
            <tr>
            	<th><?php esc_html_e('Field meta key', 'wpcargo-custom-field' ); ?></th>
                <td>
                	<p id="field-select">
						<input type="radio" name="field-key-select" value="existing" checked=""><?php esc_html_e('Existing', 'wpcargo-custom-field' ); ?> &nbsp;
						<input type="radio" name="field-key-select" value="new"><?php esc_html_e('New', 'wpcargo-custom-field' ); ?>
					</p>	
    				<div id="existing">
                	<select name="field_key" id="field-type" required >
                    	<option value=""><?php esc_html_e('--Select One--', 'wpcargo-custom-field' ); ?></option>
                    	<?php 
    						foreach( $metakeys as $key){
    							if( empty($key->meta_key) || $key->meta_key == "_edit_lock" ){ continue; }
    							?>
                                <option value="<?php echo $key->meta_key; ?>"><?php echo $key->meta_key; ?></option>
                                <?php
    						}
    					?>
                    </select>
                    </div>
                    <div id="new" style="display:none;">
                    	<input type="text" name="dummy"/>
                    </div>
                </td>
            </tr>
            <tr>
            	<th><?php esc_html_e('Is Field required?', 'wpcargo-custom-field' ); ?></th>
                <td><input type="checkbox" name="required" /></td>
            </tr>
            <?php do_action('wpc_cf_before_display_option_add'); ?>
            <tr class="table-header">
            	<td colspan="2"><h2><?php esc_html_e('Field display options', 'wpcargo-custom-field' ); ?></h2></td>
            </tr>
            <?php do_action('wpc_cf_after_display_option_add'); ?>
            <tr>
            	<th><p><?php esc_html_e('What Section do you want to display?', 'wpcargo-custom-field' ); ?></p></th>
                <td>
                	<ul>
                    	<li><input name="section" value="shipper_info" type="radio"> <?php esc_html_e('Shipper Information', 'wpcargo-custom-field' ); ?></li>
                        <li><input name="section" value="receiver_info" type="radio"> <?php esc_html_e('Receiver Information', 'wpcargo-custom-field' ); ?></li>
                        <?php
                            if( !empty( wpccf_additional_sections() ) ){
                                foreach( wpccf_additional_sections() as $section_key => $section_label ){
                                    ?> <li><input name="section" value="<?php echo $section_key; ?>" type="radio"> <?php echo $section_label; ?></li><?php
                                }
                            }
    					?>
                        <?php do_action('wpc_cf_after_display_add_section'); ?>
                    </ul>
                </td>
            </tr>
            <tr>
            	<th><?php esc_html_e('Do you want to display on tracking page form?', 'wpcargo-custom-field' ); ?></th>
                <td><input name="display_flags[]" value="search" type="checkbox"></td>
            </tr>
            <tr>
            	<th><?php esc_html_e('Is field required on tracking page form?', 'wpcargo-custom-field' ); ?></th>
                <td><input name="display_flags[]" value="search_required" type="checkbox"></td>
            </tr>
            <tr>
            	<th><?php esc_html_e('Do you want to display it on result page?', 'wpcargo-custom-field' ); ?></th>
                <td><input name="display_flags[]" value="result" type="checkbox"></td>
            </tr>
            <tr>
                <th><?php esc_html_e('Select user to NOT access this field', 'wpcargo-custom-field' ); ?></th>
                <td>
                    <input name="display_flags[]" value="useraccess_not_logged_in" type="checkbox"><?php esc_html_e('Not Logged In', 'wpcargo-custom-field' ); ?><br/>
                    <?php foreach ( $wp_roles->roles as $key => $value ): ?>
                        <input name="display_flags[]" value="useraccess_<?php echo $key; ?>" type="checkbox"><?php echo $value['name']; ?><br/>
                    <?php endforeach; ?>
                    <p class="description"><?php esc_html_e('Note: This option applies only in the front end form using custom field manager.', 'wpcargo-custom-field' ); ?></p>
                </td>
            </tr>
            <?php do_action('wpc_cf_after_form_field_add'); ?>
        </table>
        <input class="button button-primary" type="submit" name="submit_form_field" value="<?php esc_html_e('Add Field', 'wpcargo-custom-field' ); ?>" />
        </form>
    </div>
</div>
