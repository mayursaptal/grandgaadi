<?php
if( !isset( $_REQUEST['id'] ) || $_REQUEST['id'] == NULL  ){
	?>
	<div class="notice notice-error">
        <p><?php esc_html_e( 'Sorry you can\'t access this page directly.', 'wpcargo-custom-field' ); ?></p>
    </div>
    <?php
	exit;
}
$form_field = $this->get_field_by_id( $_GET['id'] );
?>
<div class="wpcargo-add-form-fields postbox" style="clear: both;">
    <div class="inside">
    	<h2><?php esc_html_e('Edit Form Field', 'wpcargo-custom-field' ); ?></h2>
        <form method="post" action="<?php echo admin_url(); ?>admin.php?page=wpc-cf-manage-form-field&action=edit&id=<?php echo $_REQUEST['id'] ?>">
        <?php wp_nonce_field( 'wpc_cf_edit_field_action', 'wpc_cf_edit_field' ); ?>
        <table class="wpcargo form-table">
        	<?php do_action('wpc_cf_before_form_field_edit'); ?>
        	<tr>
                <th><?php esc_html_e('Field Type (required)', 'wpcargo-custom-field' ); ?></th>
                <td>
                    <select name="field_type" id="field-type" required >
                    	<option value=""><?php esc_html_e('--Select One--', 'wpcargo-custom-field' ); ?></option>
                        <?php if( !empty( wpccf_field_type_list() ) ): ?>
                            <?php foreach ( wpccf_field_type_list() as $list_key => $list_value): ?>
                                <option value="<?php echo $list_key; ?>" <?php selected( $form_field->field_type, $list_key ); ?>><?php echo $list_value; ?></option>
                            <?php endforeach; ?>    
                        <?php endif; ?>
                    </select>
                </td>
            </tr>
            <tr>
            	<th><?php esc_html_e('Field Label (required)', 'wpcargo-custom-field' ); ?></th>
                <td><input type="text" name="label" required="required" value="<?php echo stripslashes( $form_field->label ); ?>" /></td>
            </tr>
            <tr>
            	<th><?php esc_html_e('Field description (optional)', 'wpcargo-custom-field' ); ?></th>
                <td><textarea name="description" type="text"><?php echo $form_field->description; ?></textarea></td>
            </tr>
            <tr id="select-list">
            	<?php  $field_data = maybe_unserialize( $form_field->field_data );  ?>
            	<th><?php esc_html_e('Field Options for select lists, radio buttons and checkboxes(required)', 'wpcargo-custom-field' ); ?></th>
                <td>
                	<textarea name="field_data" cols="50" rows="2" ><?php echo !empty( $field_data ) ? implode( ',', array_filter( $field_data ) ) : '' ; ?></textarea>
                    <?php esc_html_e('Comma (,) separated list of options', 'wpcargo-custom-field' ); ?>
                </td>
            </tr>
            <tr>
            	<th><?php esc_html_e('Field meta key', 'wpcargo-custom-field' ); ?></th>
                <td>
                	<p id="field-select"><input type="radio" name="field-key-select" value="existing" ><?php _e('Existing', 'wpcargo-custom-field' ); ?> &nbsp;<input type="radio" name="field-key-select" value="new">
    <?php esc_html_e('New', 'wpcargo-custom-field' ); ?></p>	
    				<div id="existing" style="display:none;" >
                	<select name="dummy" id="field-type" >
                    	<option value=""><?php esc_html_e('--Select One--', 'wpcargo-custom-field' ); ?></option>
                    	<?php 
    						foreach( $metakeys as $key){
    							if( empty($key->meta_key) || $key->meta_key == "_edit_lock" ){ continue; }
    							?>
                                <option value="<?php echo $key->meta_key; ?>" <?php echo ($form_field->field_key == $key->meta_key ) ? 'selected' : '' ; ?> ><?php echo $key->meta_key; ?></option>
                                <?php
    						}
    					?>
                    </select>
                    </div>
                    <div id="new" >
                    	<input type="text" name="field_key" value="<?php echo $form_field->field_key; ?>" required />
                    </div>
                </td>
            </tr>
            <tr>
            	<th><?php esc_html_e('Is Field required?', 'wpcargo-custom-field' ); ?></th>
                <td><input type="checkbox" name="required" <?php echo ( !empty ($form_field->required ) ) ? 'checked' : '' ; ?>/></td>
            </tr>
            <?php do_action('wpc_cf_before_display_option_edit'); ?>
            <tr class="table-header">
            	<td colspan="2"><h2><?php esc_html_e('Field display options', 'wpcargo-custom-field' ); ?></h2></td>
            </tr>
            <?php do_action('wpc_cf_after_display_option_edit'); ?>
            <tr>
            	<th><p><?php esc_html_e('What Section do you want to display?', 'wpcargo-custom-field' ); ?></p></th>
                <td>
                	<ul>
                    	<li><input name="section" value="shipper_info" type="radio" <?php echo ($form_field->section == 'shipper_info' ) ? 'checked' : '' ; ?> /> <?php esc_html_e('Shipper Information', 'wpcargo-custom-field' ); ?></li>
                        <li><input name="section" value="receiver_info" type="radio" <?php echo ($form_field->section == 'receiver_info' ) ? 'checked' : '' ; ?> /> <?php esc_html_e('Receiver Information', 'wpcargo-custom-field' ); ?></li>
                        <?php
                            if( !empty( wpccf_additional_sections() ) ){
                                foreach( wpccf_additional_sections() as $section_key => $section_label ){
                                    ?><li><input name="section" value="<?php echo $section_key; ?>" type="radio" <?php checked( $section_key, $form_field->section ) ?>/> <?php echo $section_label; ?></li><?php
                                }
                            }
    					?>
                        <?php do_action('wpc_cf_after_display_edit_section', $form_field ); ?>
                    </ul>
                </td>
            </tr>
            <?php 
    			
    			$display_flags = $form_field->display_flags;
    			$flags = maybe_unserialize( $display_flags ); 
    		?>
            <tr>
            	<th><?php esc_html_e('Do you want to display on tracking page form?', 'wpcargo-custom-field' ); ?></th>
                <td><input name="display_flags[]" value="search" type="checkbox" <?php echo is_array($flags) && in_array( 'search', $flags) ? 'checked' : ''; ?> /></td>
            </tr>
            <tr>
            	<th><?php esc_html_e('Is field required on tracking page form?', 'wpcargo-custom-field' ); ?></th>
                <td><input name="display_flags[]" value="search_required" type="checkbox" <?php echo is_array($flags) && in_array( 'search_required', $flags) ? 'checked' : ''; ?> /></td>
            </tr>
            <tr>
            	<th><?php esc_html_e('Do you want to display it on result page?', 'wpcargo-custom-field' ); ?></th>
                <td><input name="display_flags[]" value="result" type="checkbox" <?php echo is_array($flags) && in_array( 'result', $flags) ? 'checked' : ''; ?> /></td>
            </tr>
            <tr>
                <th><?php esc_html_e('Select user NOT to access this field', 'wpcargo-custom-field' ); ?></th>
                <td>
                    <input name="display_flags[]" value="useraccess_not_logged_in" type="checkbox" <?php echo is_array($flags) && in_array( 'useraccess_not_logged_in', $flags) ? 'checked' : ''; ?>><?php esc_html_e('Not Logged In', 'wpcargo-custom-field' ); ?><br/>
                    <?php foreach ( $wp_roles->roles as $key => $value ): ?>
                        <input name="display_flags[]" value="<?php echo $key; ?>" type="checkbox"  <?php echo is_array($flags) && in_array( $key, $flags) ? 'checked' : ''; ?> ><?php echo $value['name']; ?><br/>
                    <?php endforeach; ?>
                    <p class="description"><?php esc_html_e('Note: This option applies only in the front end form using custom field manager.', 'wpcargo-custom-field' ); ?></p>
                </td>
            </tr>
            <?php do_action('wpc_cf_after_form_field_edit', $flags ); ?>
        </table>
        <input type="hidden" name="form_field_id" value="<?php echo $_GET['id']; ?>" />
        <input class="button button-primary" type="submit" name="submit_form_field" value="<?php esc_html_e('Edit Field', 'wpcargo-custom-field' ); ?>" />
        </form>
    </div>
</div>