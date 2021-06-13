<?php
if (!defined('ABSPATH')){
    exit; // Exit if accessed directly
}
add_action( 'wp_head', 'wpc_cf_remove_default_metadata' );
add_action( 'admin_head', 'wpc_cf_remove_default_metadata' );
add_action('wpcargo_before_metabox_section', 'wpc_cf_metabox');
//** Remove default WPCargo metabox
function wpc_cf_remove_default_metadata() {
	global $wpcargo_metabox;
    remove_action( 'wpcargo_shipper_meta_section', array( $wpcargo_metabox, 'wpc_shipper_meta_template' ), 10 );
	remove_action( 'wpcargo_receiver_meta_section', array( $wpcargo_metabox, 'wpc_receiver_meta_template' ), 10 );
	remove_action( 'wpcargo_shipment_meta_section', array( $wpcargo_metabox, 'wpc_shipment_meta_template' ), 10 );
	remove_filter( 'wpcargo_after_reciever_meta_section_sep', array( $wpcargo_metabox, 'wpc_after_reciever_meta_sep' ), 10 );
}
function wpc_cf_metabox(){
	global $post;
	$options 				= get_option('wpcargo_cf_option_settings');
	$additional_sections  	= wpccf_additional_sections();
	?>
    <div id="wrap">
        <div id="wpc-cf-metabox">
            <?php
			if( empty( $options ) || ( !empty( $options ) && !array_key_exists( 'wpc_cf_disable_shipper', $options ) ) ){
				require_once(WPCARGO_CUSTOM_FIELD_PATH. 'admin/templates/shipper-metabox.tpl.php');
			}
			if( empty( $options ) || ( !empty( $options ) && !array_key_exists( 'wpc_cf_disable_receiver', $options ) ) ) {
				require_once(WPCARGO_CUSTOM_FIELD_PATH. 'admin/templates/receiver-metabox.tpl.php');
			}
			?><div class="clear-line"></div><?php
			do_action('wpcargo_before_shipment_meta_section', $post->ID );
            ?>
            <?php
			//** Additonal section based on the settings
			if( !empty( $additional_sections ) ){
				foreach( $additional_sections as $slug => $label ){
					?>
                    <div id="<?php echo $slug; ?>" class="wpcargo-field-section">
						<h1 class="section-title"><?php echo $label; ?></h1>
						<?php if( has_action( 'before_wpccf_'.$slug.'_form_fields' ) ): ?>
							<?php do_action( 'before_wpccf_'.$slug.'_form_fields', $post->ID ); ?>
						<?php endif; ?>
						<?php wpc_cf_show_fields( $slug ); ?>
						<?php if( has_action( 'after_wpccf_'.$slug.'_form_fields' ) ): ?>
							<?php do_action( 'after_wpccf_'.$slug.'_form_fields', $post->ID ); ?>
						<?php endif; ?>
                    </div>
                    <?php
				}
			}
			do_action('wpcargo_after_shipment_meta_section', $post->ID );
			?>
        </div>
    </div>
    <?php	
}
function wpc_cf_get_field_section(){
	global $wpdb;
	$table_name = $wpdb->prefix.'wpcargo_custom_fields';
	$section = $wpdb->get_results("SELECT `section` FROM `".$table_name."` GROUP BY `section`", OBJECT);
	return $section;
}
function wpc_cf_show_fields( $section ){
	global $wpdb, $post, $option, $wpcargo;
	$table_name = $wpdb->prefix.'wpcargo_custom_fields';
	$fields 	= $wpdb->get_results("SELECT * FROM `".$table_name."` WHERE `section` LIKE '$section' AND `field_key` NOT LIKE 'wpcargo_status' ORDER BY `weight`", OBJECT);
	?>
	<table class="wpcargo form-table">
	<?php
	foreach( $fields as $field ){
		if( $field->field_type == 'text' ){
			$value = maybe_unserialize( get_post_meta($post->ID, $field->field_key, true) );
			$value = is_array( $value ) ? implode(", ", $value) : $value;
			?>
            <tr>
                <th><label><?php echo stripslashes( $field->label ) ; ?></label></th>
                <td>
                	<input type="text" id="<?php echo $field->field_key; ?>" class="<?php echo $field->field_key; ?>" name="<?php echo $field->field_key; ?>" value="<?php echo $value; ?>" size="25" <?php echo ( $field->required ) ? 'required' : '' ; ?> />
				<?php
                    if( $field->description ){
                        ?><p class="wpc-cf-desc"><?php echo $field->description; ?></p><?php
                    }
                ?>
                </td>
            </tr>
            <?php
		}elseif( $field->field_type == 'email' ){
			$value = maybe_unserialize( get_post_meta($post->ID, $field->field_key, true) );
			$value = is_array( $value ) ? implode(", ", $value) : $value;
			?>
			<tr>
                <th><label><?php echo stripslashes( $field->label ) ; ?></label></th>
                <td>
                	<input type="email" id="<?php echo $field->field_key; ?>" class="<?php echo $field->field_key; ?>" name="<?php echo $field->field_key; ?>" value="<?php echo $value; ?>" <?php echo ( $field->required ) ? 'required' : '' ; ?>/>
				<?php
                    if( $field->description ){
                        ?><p class="wpc-cf-desc"><?php echo $field->description; ?></p><?php
                    }
                ?>
                </td>
            </tr>
            <?php
		}elseif( $field->field_type == 'url' ) {
			?>
            <tr>
                <th><label><?php echo stripslashes( $field->label ) ; ?></label></th>
                <td>
				<?php
					$get_url_key = get_post_meta($post->ID, $field->field_key, true);
					$url_key_unserialized = unserialize($get_url_key);
				?>
					<input type="text" id="<?php echo $field->field_key; ?>" class="<?php echo $field->field_key; ?>" name="<?php echo $field->field_key.'[]'; ?>" value="<?php echo is_array($url_key_unserialized) ? $url_key_unserialized[0] : ''; ?>" size="25" <?php echo ( $field->required ) ? 'required' : '' ; ?> placeholder="<?php esc_html_e('URL Label', 'wpcargo-custom-field' ); ?>" />
					<br />
                	<input type="text" id="<?php echo $field->field_key; ?>" class="<?php echo $field->field_key; ?>" name="<?php echo $field->field_key.'[]'; ?>" value="<?php echo is_array($url_key_unserialized) ? $url_key_unserialized[1] : ''; ?>" size="25" <?php echo ( $field->required ) ? 'required' : '' ; ?> placeholder="<?php esc_html_e('http://www.sample.com', 'wpcargo-custom-field' ); ?>"/>
					<br />
					<input type="checkbox" name="<?php echo $field->field_key.'[]'; ?>" class="<?php echo $field->field_key; ?>" <?php echo is_array($url_key_unserialized) && !empty($url_key_unserialized[2]) ? 'checked' : ''?> > - <?php esc_html_e('Open new window?', 'wpcargo-custom-field' ); ?>
				<?php
                    if( $field->description ){
                        ?><p class="wpc-cf-desc"><?php echo $field->description; ?></p><?php
                    }
                ?>
                </td>
            </tr>
            <?php
		}elseif( $field->field_type == 'textarea' ){
			$value = maybe_unserialize( get_post_meta($post->ID, $field->field_key, true) );
			$value = is_array( $value ) ? implode(", ", $value) : $value;
			?>
			<tr>
                <th><label><?php echo stripslashes( $field->label ) ; ?></label></th>
                <td>
                	<textarea id="<?php echo $field->field_key; ?>" class="<?php echo $field->field_key; ?>" name="<?php echo $field->field_key; ?>" <?php echo ( $field->required ) ? 'required' : '' ; ?> ><?php echo $value; ?></textarea>
                <?php
					if( $field->description ){
						?><p class="wpc-cf-desc"><?php echo $field->description; ?></p><?php
					}
				?>
                </td>
            </tr>
            <?php
		}elseif( $field->field_type == 'number' ){
			$value = maybe_unserialize( get_post_meta($post->ID, $field->field_key, true) );
			$value = is_array( $value ) ? implode(", ", $value) : $value;
			?>
			<tr>
                <th><label><?php echo stripslashes( $field->label ) ; ?></label></th>
                <td>
                	<input type="number" id="<?php echo $field->field_key; ?>" class="<?php echo $field->field_key; ?>" name="<?php echo $field->field_key; ?>" value="<?php echo $value; ?>" <?php echo ( $field->required ) ? 'required' : '' ; ?> />
                <?php
					if( $field->description ){
						?><p class="wpc-cf-desc"><?php echo $field->description; ?></p><?php
					}
				?>
                </td>
            </tr>
            <?php
		}elseif( $field->field_type == 'date' ){
			
			$value = maybe_unserialize( get_post_meta($post->ID, $field->field_key, true) );
			$value = is_array( $value ) ? implode(", ", $value) : $value;
			?>
			<tr>
                <th><label><?php echo stripslashes( $field->label ) ; ?></label></th>
                <td>
                	<input class="wpcargo-datepicker" type="text" id="<?php echo $field->field_key; ?>" class="<?php echo $field->field_key; ?>" name="<?php echo $field->field_key; ?>" value="<?php echo $value; ?>" <?php echo ( $field->required ) ? 'required' : '' ; ?> autocomplete="off" />
                 <?php
					if( $field->description ){
						?><p class="wpc-cf-desc"><?php echo $field->description; ?></p><?php
					}
				?>
                </td>
            </tr>
            <?php
		}elseif( $field->field_type == 'time' ){
			$value = maybe_unserialize( get_post_meta($post->ID, $field->field_key, true) );
			$value = is_array( $value ) ? implode(", ", $value) : $value;
			?>
			<tr>
                <th><label><?php echo stripslashes( $field->label ) ; ?></label></th>
                <td>
                	<input class="wpcargo-timepicker" type="text" id="<?php echo $field->field_key; ?>" class="<?php echo $field->field_key; ?>" name="<?php echo $field->field_key; ?>" value="<?php echo $value; ?>" <?php echo ( $field->required ) ? 'required' : '' ; ?> autocomplete="off" />
                 <?php
					if( $field->description ){
						?><p class="wpc-cf-desc"><?php echo $field->description; ?></p><?php
					}
				?>
                </td>
            </tr>
            <?php
		}elseif( $field->field_type == 'datetime' ){
			$value = maybe_unserialize( get_post_meta($post->ID, $field->field_key, true) );
			$value = is_array( $value ) ? implode(", ", $value) : $value;
			?>
			<tr>
                <th><label><?php echo stripslashes( $field->label ) ; ?></label></th>
                <td>
                	<input class="wpcargo-datetimepicker" type="text" id="<?php echo $field->field_key; ?>" class="<?php echo $field->field_key; ?>" name="<?php echo $field->field_key; ?>" value="<?php echo $value; ?>" <?php echo ( $field->required ) ? 'required' : '' ; ?> autocomplete="off" />
                 <?php
					if( $field->description ){
						?><p class="wpc-cf-desc"><?php echo $field->description; ?></p><?php
					}
				?>
                </td>
            </tr>
            <?php
		}elseif( $field->field_type == 'select' ){
			?>
			<tr>
                <th><label><?php echo stripslashes( $field->label ) ; ?></label></th>
                <td>
                <?php
				$field_data 	= maybe_unserialize( $field->field_data );
				$field_data 	= apply_filters( 'wpccf_field_options', $field_data, $field->field_key );
				if( !empty( $field_data ) ){
				?>
                <select id="<?php echo $field->field_key; ?>" class="<?php echo $field->field_key; ?>" name="<?php echo $field->field_key; ?>" <?php echo ( $field->required ) ? 'required' : '' ; ?>>
                	<option value="" ><?php _e('-- Select One --', 'wpcargo-custom-field' ); ?></option>
                    <?php
					foreach( array_filter($field_data) as $data ){
						?><option value="<?php echo trim($data); ?>" <?php echo ( get_post_meta($post->ID, $field->field_key, true) == trim($data) ) ? 'selected' : '' ; ?> ><?php echo $data;  ?></option><?php
					}
                    ?>
                </select>
				<?php
				}else{
					?>
					<span class="meta-box error"><strong><?php echo esc_html__('No Selection setup, Please add selection', 'wpcargo-custom-field' ).' <a href="'.admin_url().'admin.php?page=wpc-cf-manage-form-field&action=edit&id='.$field->id.'">'.esc_html__('Here', 'wpcargo-custom-field').'</a>.'; ?></strong></span>
                    <?php
				}
                ?>
                 <?php
					if( $field->description ){
						?><p class="wpc-cf-desc"><?php echo $field->description; ?></p><?php
					}
				?>
                </td>
            </tr>
            <?php
		}elseif( $field->field_type == 'agent' ){
			$wpc_agent_args  	= array( 'role' => 'cargo_agent', 'orderby' => 'user_nicename', 'order' => 'ASC' );
			$wpc_agents 		= get_users($wpc_agent_args);
			?>
			<tr>
                <th><label><?php echo stripslashes( $field->label ) ; ?></label></th>
                <td>
                <?php
				if( !empty( $wpc_agents ) ){
				?>
                    <select id="<?php echo $field->field_key; ?>" class="<?php echo $field->field_key; ?>" name="<?php echo $field->field_key; ?>" <?php echo ( $field->required ) ? 'required' : '' ; ?>>
                    	<option value="" ><?php _e('-- Select One --', 'wpcargo-custom-field' ); ?></option>
						<?php
						foreach( $wpc_agents as $wpc_agent ){
							?><option value="<?php echo $wpc_agent->ID;  ?>" <?php selected( get_post_meta($post->ID, $field->field_key, true), $wpc_agent->ID); ?>><?php echo $wpc_agent->display_name;  ?></option><?php
						}
						?>
                    </select>
				<?php
				}else{
					?>
					<span class="meta-box error"><strong><?php echo esc_html__('No WPCargo agents, Please add Agents', 'wpcargo-custom-field' ).' <a href="'.admin_url().'/user-new.php">'.esc_html__('Here', 'wpcargo-custom-field' ).'</a> '.esc_html__('make sure the role assign is "WPCargo Agent".', 'wpcargo-custom-field' ); ?></strong></span>
                    <?php
				}
                ?>
                 <?php
					if( $field->description ){
						?><p class="wpc-cf-desc"><?php echo $field->description; ?></p><?php
					}
				?>
                </td>
            </tr>
            <?php
		}elseif( $field->field_type == 'radio' ){
			?>
			<tr>
                <th><label><?php echo stripslashes( $field->label ) ; ?></label></th>
                <td>
                <?php
				$field_data 	= maybe_unserialize( $field->field_data );
				$field_data 	= array_filter($field_data);
				$field_data 	= apply_filters( 'wpccf_field_options', $field_data, $field->field_key );
				if( !empty( $field_data ) ){
					foreach( $field_data as $data ){
						?><input class="<?php echo $field->field_key; ?>" class="<?php echo $field->field_key; ?>" type="radio" name="<?php echo $field->field_key; ?>" value="<?php echo trim($data); ?>" <?php echo ( get_post_meta($post->ID, $field->field_key, true) == trim($data) ) ? 'checked' : '' ; ?> <?php echo ( $field->required ) ? 'required' : '' ; ?> > <?php echo trim($data); ?><br/><?php
					}
				}else{
					?>
					<span class="meta-box error"><strong><?php esc_html_e('No Selection setup, Please add selection', 'wpcargo-custom-field' ).' <a href="'.admin_url().'admin.php?page=wpc-cf-manage-form-field&action=edit&id='.$field->id.'">'.esc_html__('Here', 'wpcargo-custom-field').'</a>.'; ?></strong></span>
                    <?php
				}
                ?>
                 <?php
					if( $field->description ){
						?><p class="wpc-cf-desc"><?php echo $field->description; ?></p><?php
					}
				?>
                </td>
            </tr>
            <?php
		}elseif( $field->field_type == 'checkbox' ){
			$data_selection = maybe_unserialize( get_post_meta($post->ID, $field->field_key, true) );
			if( is_array( $data_selection ) && !empty( $data_selection ) ){
				$data_selection = array_filter( $data_selection );
				$data_selection = $data_selection;
			}else{
				$data_selection = array();
			}
			?>
			<tr>
                <th><label><?php echo stripslashes( $field->label ) ; ?></label></th>
                <td>
                <?php
				$field_data = maybe_unserialize( $field->field_data );
				$field_data = array_filter($field_data);
				$field_data = apply_filters( 'wpccf_field_options', $field_data, $field->field_key );
				if( !empty( $field_data ) ){
					foreach( $field_data as $data ){
						?><input class="<?php echo $field->field_key; ?>" class="<?php echo $field->field_key; ?>" type="checkbox" name="<?php echo $field->field_key; ?>[]" value="<?php echo trim($data); ?>" <?php echo ( in_array( trim($data), $data_selection ) ) ? 'checked' : '' ; ?> > <?php echo trim($data); ?><br/><?php
					}
				}else{
					?>
					<span class="meta-box error"><strong><?php echo esc_html__('No Selection setup, Please add selection', 'wpcargo-custom-field' ).' <a href="'.admin_url().'admin.php?page=wpc-cf-manage-form-field&action=edit&id='.$field->id.'">'.esc_html__('Here', 'wpcargo-custom-field' ).'</a>'; ?></strong></span>
                    <?php
				}
                ?>
                 <?php
					if( $field->description ){
						?><p class="wpc-cf-desc"><?php echo $field->description; ?></p><?php
					}
				?>
                </td>
            </tr>
            <?php
		}elseif( $field->field_type == 'multiselect' ){
			$data_selection = maybe_unserialize( get_post_meta($post->ID, $field->field_key, true) );
			if( is_array( $data_selection ) ){
				$data_selection = array_filter( $data_selection );
				if( !empty( $data_selection ) ){
					$data_selection = maybe_unserialize( get_post_meta($post->ID, $field->field_key, true) );
				}
			}else{
				$data_selection = array();
			}
			?>
			<tr>
                <th><label><?php echo stripslashes( $field->label ) ; ?></label></th>
                <td>
                <?php
				$field_data =  maybe_unserialize( $field->field_data );
				$field_data = array_filter($field_data);
				$field_data = apply_filters( 'wpccf_field_options', $field_data, $field->field_key );
				if( !empty( $field_data ) ){
					?>
					<select id="<?php echo $field->field_key; ?>" class="<?php echo $field->field_key; ?>" name="<?php echo $field->field_key; ?>[]" multiple="multiple" <?php echo ( $field->required ) ? 'required' : '' ; ?> >
						<?php
                        foreach( array_filter($field_data) as $data ){
                            ?><option value="<?php echo trim($data); ?>" <?php echo ( in_array( trim($data), $data_selection ) ) ? 'selected' : '' ; ?> ><?php echo trim($data);  ?></option><?php
                        }
                        ?>
                    </select>
                    <?php
				}else{
					?>
					<span class="meta-box error"><strong><?php echo esc_html__('No Selection setup, Please add selection', 'wpcargo-custom-field' ).' <a href="'.admin_url().'admin.php?page=wpc-cf-manage-form-field&action=edit&id='.$field->id.'">'.esc_html__('Here', 'wpcargo-custom-field').'</a>.'; ?></strong></span>
                    <?php
				}
                ?>
                 <?php
					if( $field->description ){
						?><p class="wpc-cf-desc"><?php echo $field->description; ?></p><?php
					}
				?>
                </td>
            </tr>
            <?php
		}elseif( $field->field_type == 'address' ){
			$wpcargo_country = wpcargo_country_list();
			$address 	 	 = wpccf_extract_address( $post->ID, $field->field_key );
			if( !empty( wpccf_address_fields_data() ) ){
				?>
				<tr>
	                <th><label><?php echo stripslashes( $field->label ) ; ?></label></th>
	                <td>
						<div id="address-form-<?php echo $field->id; ?>" class="wpccf-address-group">
							<?php 
							$counter = 1;
							foreach ( wpccf_address_fields_data() as $_addr_meta => $_addr_label ) {
								if( $_addr_meta == 'country' ){
									?>
									<div class="country_address-section">
										<p><label for="<?php echo $field->field_key; ?>[<?php echo $_addr_meta; ?>]"><?php echo $_addr_label; ?></label></p>
										<select class="form-control browser-default custom-select" id="<?php echo $field->field_key; ?>[<?php echo $_addr_meta; ?>]" class="<?php echo $field->field_key; ?>" name="<?php echo $field->field_key; ?>[<?php echo $_addr_meta; ?>]" <?php echo ( $field->required ) ? 'required' : '' ; ?>>
											<option value =""><?php esc_html_e(' -- Select Country -- ', 'wpcargo-custom-field'); ?></option>
											<?php foreach ($wpcargo_country as $country) : ?>
												<option value="<?php echo $country ?>" <?php echo selected( $address[$_addr_meta], $country ); ?>><?php echo $country ?></option>
											<?php endforeach; ?>
										</select>
									</div>
									<?php
								}else{
									?>
									<div class="md-form form-group street_address-section">
										<p><label for="<?php echo $field->field_key; ?>[<?php echo $_addr_meta; ?>]" ><?php echo $_addr_label; ?></label></p>
										<input id="<?php echo $field->field_key; ?>[<?php echo $_addr_meta; ?>]" type="text" class="form-control <?php echo $_addr_meta.' '.$field->field_key; ?>" name="<?php echo $field->field_key; ?>[<?php echo $_addr_meta; ?>]" value="<?php echo $address[$_addr_meta]; ?>" <?php echo ( $field->required && $counter == 1 ) ? 'required' : '' ; ?> >
									</div>
									<?php
								}
								$counter ++;
							}
							?>
						</div>
	                </td>
	            </tr>
	            <?php
       		}
		}elseif( $field->field_type == 'file' ) {
				
            $field_data 	= maybe_unserialize($field->field_data);
            $options    	= isset($field_data['options']) ? $field_data['options'] : '';
			$get_meta_value = get_post_meta($post->ID, $field->field_key, true);
			?>
			<tr class="image-tr-upload">
				<th><label><?php echo stripslashes( $field->label ) ; ?></label></th>
				<td>
					<div class="wpcargo-uploader">
						<div id="wpcargo-gallery-container_<?php echo $field->id;?>">
							<ul class="wpccf_uploads">
								<?php
								if (!empty($get_meta_value) || $get_meta_value != NULL):
									$get_images_id = explode(',', $get_meta_value);
									foreach ($get_images_id as $image_id):
										if (!empty($image_id)) {
											?>
											<li class="image" data-attachment_id="<?php echo $image_id; ?>">
												<a href="<?php echo wp_get_attachment_url($image_id); ?>" target="_blank"><?php echo wp_get_attachment_image($image_id, 'thumbnail', TRUE); ?></a>
												<span class="actions"><a href="#" class="delete" title="<?php esc_html_e('Delete image', 'wpcargo-custom-field');  ?>">X</a></span>
											</li>
											<?php
										}
									endforeach;
								endif;
							?>
							</ul>
						</div>
						<input id="wpcargo_image_gallery_<?php echo $field->id;?>" class="<?php echo $field->field_key; ?>" type="hidden" name="<?php echo $field->field_key; ?>" value="<?php echo $get_meta_value; ?>" />
						<a id="wpcargo_select_gallery_<?php echo $field->id;?>" class="button" data-delete="Delete image" data-text="Delete" ><?php esc_html_e('Add Images / Upload Files', 'wpcargo-custom-field');  ?></a> 
					</div>
				</td>
				<script>
					jQuery(document).ready(function($){
						// Uploading files
						var file_frame;
						var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
						var set_to_post_id = $("#wpcargo_post_id_<?php echo $field->id;?>").val(); // Set this
						var $image_gallery_ids = $( '#wpcargo_image_gallery_<?php echo $field->id;?>' );
						var $product_images    = $( '#wpcargo-gallery-container_<?php echo $field->id;?>' ).find( 'ul.wpccf_uploads' );
						jQuery('#wpcargo_select_gallery_<?php echo $field->id;?>').on('click', function( event ) {
							var $el = $( this );
							event.preventDefault();
							// If the media frame already exists, reopen it.
							if ( file_frame ) {
								// Set the post ID to what we want
								file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
								// Open frame
								file_frame.open();
								return;
							} else {
								// Set the wp.media post id so the uploader grabs the ID we want when initialised
								wp.media.model.settings.post.id = set_to_post_id;
							}
								// Create the media frame.
								file_frame = wp.media.frames.file_frame = wp.media({
									title: jQuery( this ).data( 'uploader_title' ),
									button: {
										text: jQuery( this ).data( 'uploader_button_text' ),
									},
									multiple: true  // Set to true to allow multiple files to be selected
								});
								// When an image is selected, run a callback.
								file_frame.on( 'select', function() {
									var selection = file_frame.state().get( 'selection' );
									var attachment_ids = $image_gallery_ids.val();
									selection.map( function( attachment ) {
										attachment = attachment.toJSON();
										if ( attachment.id ) {
											attachment_ids   = attachment_ids ? attachment_ids + ',' + attachment.id : attachment.id;
											var attachment_image = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
											$product_images.append( '<li class="image" data-attachment_id="' + attachment.id + '"><img src="' + attachment_image + '" /><span class="actions"><a href="#" class="delete" title="' + $el.data('delete') + '">X</a></span></li>' );
										}
									});
									$image_gallery_ids.val( attachment_ids );
								});
								// Finally, open the modal
								file_frame.open();
							});
							// Restore the main ID when the add media button is pressed
							jQuery('a.add_media').on('click', function() {
							wp.media.model.settings.post.id = wp_media_post_id;
							});
							// Remove images
							$( '#wpcargo-gallery-container_<?php echo $field->id;?>' ).on( 'click', 'a.delete', function() {
							$( this ).closest( 'li.image' ).remove();
							var attachment_ids = '';
							$( '#wpcargo-gallery-container_<?php echo $field->id;?>' ).find( 'ul li.image' ).css( 'cursor', 'default' ).each( function() {
							var attachment_id = jQuery( this ).attr( 'data-attachment_id' );
							attachment_ids = attachment_ids + attachment_id + ',';
							});
							$image_gallery_ids.val( attachment_ids );
							// remove any lingering tooltips
							$( '#tiptip_holder' ).removeAttr( 'style' );
							$( '#tiptip_arrow' ).removeAttr( 'style' );
							return false;
						});
					});
				</script>
			</tr>
            <?php
		}
		
		if(class_exists('WPC_Signature')) {
			$get_field_type = $field->field_type;
			$get_field_key = $field->field_key;
		}else{
			$get_field_type ='';
			$get_field_key 	= '';
		}
		echo apply_filters( 'wpc_add_field_generation', $get_field_type, stripslashes( $field->label ), $get_field_key );
	}
	?>
    </table>
    <?php
}
function wpc_cf_get_fields( $section ){
	global $wpdb;
	$table_name = $wpdb->prefix.'wpcargo_custom_fields';
	$fields = $wpdb->get_results("SELECT label, field_type, field_key FROM `".$table_name."` WHERE `section` LIKE '$section' AND display_flags LIKE '%result%' ORDER BY `weight`", OBJECT);
	return $fields;
}