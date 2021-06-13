<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}
class WPCCF_Fields{
	function get_custom_fields( $flag = '' ){
		global $wpdb;
		$table_prefix = $wpdb->prefix;
		//** Flag value Parameter
		//* @shipper_info
		//* @receiver_info
		//* @shipment_info
		$result_fields = $wpdb->get_results( 'SELECT * FROM `'.$table_prefix.'wpcargo_custom_fields` WHERE `section` LIKE "%'.$flag.'%" ORDER BY ABS(weight)', ARRAY_A );
		$fields = array();
		$user_role = array( 'useraccess_not_logged_in' );
		if( is_user_logged_in() ){
			$current_user = wp_get_current_user();
			$user_role = $current_user->roles;
		}
		$counter = 0;
		foreach ($result_fields as $value) {
			$flags 				= maybe_unserialize( $value['display_flags'] ) ? maybe_unserialize( $value['display_flags'] ) : array() ;
			$role_intersected 	= array_intersect($flags, $user_role);
			if( !empty( $role_intersected ) && count( $role_intersected ) <= count( $user_role )  ){
				continue;
			}
			$fields[$counter] = $value;
			$counter++;
		}
		return $fields;
	}
	function get_fields_data( $flag = '', $shipment_id = 0, $attachment_image = true ){
		global $wpcargo;
		$field_keys = $this->get_custom_fields( $flag );
		ob_start();
		if( !empty( $field_keys ) ){
			foreach( $field_keys as $field ){
				$field_data = maybe_unserialize( get_post_meta( $shipment_id, $field['field_key'], TRUE ) );
				if( is_array( $field_data ) ){
					$field_data = implode(", ", $field_data);
				}
				if( $field['field_type'] == 'file' ){
					$files = array_filter( array_map( 'trim', explode(",", $field_data) ) );
					if( !empty( $files ) ){
						?>
						<div class="wpccfe-files-data">
							<label><strong><?php echo stripslashes( $field['label'] ); ?></strong></label>
							<div id="wpcargo-gallery-container_<?php echo $field['id'];?>">
								<ul class="wpccf_uploads">
									<?php
										foreach ( $files as $file_id ) {
											$att_meta = wp_get_attachment_metadata( $file_id );
											?>
											<li class="image">
												<a href="<?php echo wp_get_attachment_url($file_id); ?>" download>
													<?php if( $attachment_image ): ?>
													<?php echo wp_get_attachment_image($file_id, 'thumbnail', TRUE); ?>
													<?php endif; ?>
													<span class="img-title" title="<?php echo get_the_title($file_id); ?>"><?php echo get_the_title($file_id); ?></span>
												</a>
											</li>
											<?php
										}
									?>
								</ul>
							</div>
						</div>
						<?php
					}
				}elseif( $field['field_type'] == 'url' ){
					$url_data = maybe_unserialize( get_post_meta( $shipment_id, $field['field_key'], TRUE ) );
					$target   = count( $url_data ) > 2 ? '_blank' : '' ;
					$url 	  = $url_data[1] ? $url_data[1] : '#' ;
					$label 	  = $url_data[0];
					?><p><strong><?php echo stripslashes( $field['label'] ); ?>:</strong> <a href="<?php echo $url; ?>" target="<?php echo $target; ?>"><?php echo $label; ?></a></p><?php
				}else{
					?><p><strong><?php echo stripslashes( $field['label'] ); ?>:</strong> <?php echo $field_data; ?></p><?php
				}	
			}
		}
		$output = ob_get_clean();
		return $output;
	}
	function get_field_key( $key = '' ){
		global $wpdb;
		$table_prefix = $wpdb->prefix;
		$result = '';
		if( !empty($key) || $key != '' ){
			$result= $wpdb->get_results( 'SELECT * FROM `'.$table_prefix.'wpcargo_custom_fields` WHERE `section` LIKE "%'.$key.'%"', ARRAY_A );
		}
		return $result;
	}
	function get_field_key_list(  ){
		global $wpdb;
		$table_prefix = $wpdb->prefix;
		$field_keys = $wpdb->get_col( 'SELECT `field_key` FROM `'.$table_prefix.'wpcargo_custom_fields`' );
		return $field_keys;
	}
	function get_invoice_id( $shipment_id ){
		global $wpdb;
		$orderID = $wpdb->get_var( "SELECT tbl1.ID FROM `$wpdb->posts` AS tbl1 INNER JOIN `$wpdb->postmeta` AS tbl2 WHERE tbl1.ID = tbl2.post_id AND tbl1.post_type LIKE 'pq_order' AND tbl2.meta_key LIKE 'shipment_id' AND tbl2.meta_value = ".$shipment_id );
		return $orderID;
	}
	function get_field_options( $meta_key ){
		global $wpdb;
		$table_prefix = $wpdb->prefix;
		$field_options = $wpdb->get_var( "SELECT `field_data` FROM `".$table_prefix."wpcargo_custom_fields` WHERE `field_key`='".$meta_key."'" );
		$unserialized_field_options = array();
		if( is_serialized( $field_options ) ){
			$unserialized_field_options = maybe_unserialize( $field_options );
		}
		return $unserialized_field_options;
	}
	function convert_to_form_fields( $fields = array(), $post_id = '', $class="", $id="" ){
		global $wpcargo;
		ob_start();
		foreach( $fields as $field):
			$value = '';
			if( !empty( $post_id ) ){
				$value = maybe_unserialize( get_post_meta( $post_id, $field['field_key'], TRUE ) );
			}
			$required = ( $field['required'] ) ? 'required' : '' ;
			if( $field['field_type'] == 'text' ){
				$value = is_array( $value ) ? implode(", ", $value) : $value;
				?>
			    <div id="form-<?php echo $id.$field['id']; ?>" class="form-group <?php echo $class; ?>">
			        <label for="<?php echo $id.$field['field_key']; ?>" ><?php echo stripslashes( stripslashes( $field['label'] ) ); ?></label>
			        <input id="<?php echo $id.$field['field_key']; ?>" type="text" class="form-control <?php echo $field['field_key']; ?>" name="<?php echo $field['field_key']; ?>" value="<?php echo $value; ?>" <?php echo $required; ?> >
			        <?php
					if( !empty( $field['description'] ) ){
						?><p class="field-desc"><?php echo $field['description']; ?></p><?php
					}
					?>
			    </div>
			<?php
			}elseif( $field['field_type'] == 'checkbox' ){
				?>
			    <div id="form-<?php echo $id.$field['id']; ?>" class="form-check <?php echo $class; ?>">
			    	<p><?php echo stripslashes( $field['label'] ); ?></p>
			        <?php
					$checkbox_options = array_filter( maybe_unserialize($field['field_data']) );
					if( empty( $value ) ){
						$value = array();
					}
					$checkbox_options = apply_filters( 'wpccf_field_options', $checkbox_options, $field['field_key'] );
					if( !empty( $checkbox_options ) ){
						?><ul><?php
						$checkbox_option_counter=0;
						foreach( $checkbox_options as $checkbox_option ){
							$option_id = strtolower( preg_replace('/[^a-zA-Z0-9]/', '', $checkbox_option) );
							?>
							<li><input id="<?php echo $id.$field['id'].'-'.$option_id; ?>" type="<?php echo $field['field_type']; ?>" class="form-check-input <?php echo $field['field_key']; ?>" name="<?php echo $field['field_key']; ?>[]" value="<?php echo trim($checkbox_option); ?>" <?php echo ( $checkbox_option_counter == 0 ) ? $required : ''; ?> <?php echo in_array( trim($checkbox_option), $value ) ? 'checked' : '' ; ?> /> <label for="<?php echo $id.$field['id'].'-'.$option_id; ?>" class="form-check-label" ><?php echo $checkbox_option; ?></label>
							</li><?php
							$checkbox_option_counter++;
						}
						?></ul><?php
					}else{
						?><p class="field-desc"><?php esc_html__('No options available', 'wpcargo-custom-field') ; ?></p><?php
					}
					
					if( !empty( $field['description'] ) ){
						?><p class="field-desc"><?php echo $field['description']; ?></p><?php
					}
					?>
			    </div>
				<?php
			}elseif( $field['field_type'] == 'radio' ){
				?>
			    <div id="form-<?php echo $id.$field['id']; ?>" class="form-check <?php echo $class; ?>">
			        <p><?php echo stripslashes( $field['label'] ); ?></p>
			        <?php 
					$radio_options = array_filter( maybe_unserialize($field['field_data']) );
					$radio_options = apply_filters( 'wpccf_field_options', $radio_options, $field['field_key'] );
			        if( !empty( $radio_options ) ){
			        	?><ul><?php
						$radio_option_counter=0;
						foreach( $radio_options as $radio_option ){
							$option_id = strtolower( preg_replace('/[^a-zA-Z0-9]/', '', $radio_option) );
							?>
							<li>
								<input id="<?php echo $id.$field['id'].'-'.$option_id; ?>" type="<?php echo $field['field_type']; ?>" class="form-check-input <?php echo $field['field_key']; ?>" name="<?php echo $field['field_key']; ?>" value="<?php echo trim($radio_option); ?>" <?php echo ( $radio_option_counter == 0 ) ? $required : ''; ?> <?php echo ( trim($radio_option) == $value ) ? 'checked' : '' ; ?> /> <label for="<?php echo $id.$field['id'].'-'.$option_id; ?>" class="form-check-label" ><?php echo $radio_option; ?></label>
							</li><?php
							$radio_option_counter++;
						}
						?></ul>
				        <?php
			        }else{
			        	?><p class="field-desc"><?php esc_html__('No options available', 'wpcargo-custom-field') ; ?></p><?php
			        }
					if( !empty( $field['description'] ) ){
						?><p class="field-desc"><?php echo $field['description']; ?></p><?php
					}
					?>
			    </div>
				<?php
			}elseif( $field['field_type'] == 'textarea' ){
				$value = is_array( $value ) ? implode(", ", $value) : $value;
				?>
			    <div id="form-<?php echo $id.$field['id']; ?>" class="form-group <?php echo $class; ?>">
			        <label for="<?php echo $id.$field['field_key']; ?>" ><?php echo stripslashes( $field['label'] ); ?></label>
			        <textarea id="<?php echo $id.$field['field_key']; ?>" class="md-textarea form-control <?php echo $field['field_key']; ?>" name="<?php echo $field['field_key']; ?>" <?php echo $required; ?> ><?php echo $value; ?></textarea>
			        <?php
					if( !empty( $field['description'] ) ){
						?><p class="field-desc"><?php echo $field['description']; ?></p><?php
					}
					?>
			    </div>
			<?php
			}elseif( $field['field_type'] == 'select' ){
				?>
			    <div id="form-<?php echo $id.$field['id']; ?>" class="form-group <?php echo $class; ?>">
			        <label for="<?php echo $id.$field['field_key']; ?>" ><?php echo stripslashes( $field['label'] ); ?></label>
			        <?php 
					$select_options = array_filter( maybe_unserialize($field['field_data']) );
					$select_options = apply_filters( 'wpccf_field_options', $select_options, $field['field_key'] );
			        if( !empty( $select_options ) ){
			        	?>
				        <select name="<?php echo $field['field_key']; ?>" class="form-control browser-default custom-select <?php echo $field['field_key']; ?>" id="<?php echo $id.$field['field_key']; ?>" <?php echo $required; ?> >
		                	<option value=""><?php esc_html_e('-- Select One --', 'wpcargo-custom-field'  ); ?></option>
							<?php
		                    foreach( $select_options as $select_option ){
		                        ?><option value="<?php echo trim($select_option); ?>" <?php selected( $value, trim($select_option) ); ?> ><?php echo trim($select_option); ?></option><?php
		                    }
		                    ?>
		                </select>
				        <?php
			        }else{
			        	?><p class="field-desc"><?php esc_html__('No options available', 'wpcargo-custom-field') ; ?></p><?php
			        }
					if( !empty( $field['description'] ) ){
						?><p class="field-desc"><?php echo $field['description']; ?></p><?php
					}
					?>
			    </div>
				<?php
			}elseif( $field['field_type'] == 'multiselect' ){
				?>
			    <div id="form-<?php echo $id.$field['id']; ?>" class="form-group <?php echo $class; ?>">
			        <label for="<?php echo $id.$field['field_key']; ?>" ><?php echo stripslashes( $field['label'] ); ?></label>
			        <?php
					$multiselect_options = array_filter( maybe_unserialize($field['field_data']) );
					$multiselect_options = apply_filters( 'wpccf_field_options', $multiselect_options, $field['field_key'] );
			        if( empty( $value ) ){
						$value = array();
					}
			        if( !empty( $multiselect_options ) ){
			        	?>
				        <select id="<?php echo $id.$field['field_key']; ?>" class="form-control browser-default custom-select <?php echo $field['field_key']; ?>" name="<?php echo $field['field_key']; ?>[]" multiple size="6" <?php echo $required; ?> >
				        	<option style="display: none !important;" value=" " selected="selected" ></option>
			                <?php
							foreach( $multiselect_options as $multiselect_option ){
								?><option value="<?php echo trim($multiselect_option); ?>" <?php echo in_array( trim($multiselect_option), $value ) ? 'selected' : '' ; ?> ><?php echo trim($multiselect_option); ?></option><?php
							}
							?>
		                </select>
				        <?php
			        }else{
			        	?><p class="field-desc"><?php esc_html__('No options available', 'wpcargo-custom-field') ; ?></p><?php
			        }
					if( !empty( $field['description'] ) ){
						?><p class="field-desc"><?php echo $field['description']; ?></p><?php
					}
					?>
			    </div>
				<?php
			}elseif( $field['field_type'] == 'number' ){
				$value = is_array( $value ) ? implode(", ", $value) : $value;
				?>
			    <div id="form-<?php echo $id.$field['id']; ?>" class="form-group <?php echo $class; ?>">
			        <label for="<?php echo $id.$field['field_key']; ?>" ><?php echo stripslashes( $field['label'] ); ?></label>
			        <input id="<?php echo $id.$field['field_key']; ?>" type="number" class="form-control wpccf-number <?php echo $field['field_key']; ?>" name="<?php echo $field['field_key']; ?>" value="<?php echo $value; ?>" autocomplete="off" <?php echo $required; ?> >
			        <?php
					if( !empty( $field['description'] ) ){
						?><p class="field-desc"><?php echo $field['description']; ?></p><?php
					}
					?>
			    </div>
				<?php
			}elseif( $field['field_type'] == 'date' ){
				$value = is_array( $value ) ? implode(", ", $value) : $value;
				?>
			    <div id="form-<?php echo $id.$field['id']; ?>" class="form-group <?php echo $class; ?>">
			        <label for="<?php echo $id.$field['field_key']; ?>" ><?php echo stripslashes( $field['label'] ); ?></label>
			        <input id="<?php echo $id.$field['field_key']; ?>" type="text" class="form-control wpccf-datepicker <?php echo $field['field_key']; ?>" name="<?php echo $field['field_key']; ?>" value="<?php echo $value; ?>"  autocomplete="off" <?php echo $required; ?> >
			        <?php
					if( !empty( $field['description'] ) ){
						?><p class="field-desc"><?php echo $field['description']; ?></p><?php
					}
					?>
			    </div>
				<?php
			}elseif( $field['field_type'] == 'time' ){
				$value = is_array( $value ) ? implode(", ", $value) : $value;
				?>				
			    <div id="form-<?php echo $id.$field['id']; ?>" class="form-group <?php echo $class; ?>">
			        <label for="<?php echo $id.$field['field_key']; ?>" ><?php echo stripslashes( $field['label'] ); ?></label>
			        <input id="<?php echo $id.$field['field_key']; ?>" type="text" class="form-control wpccf-timepicker <?php echo $field['field_key']; ?>" name="<?php echo $field['field_key']; ?>" value="<?php echo $value; ?>" autocomplete="off" <?php echo $required; ?>>
			        <?php
					if( !empty( $field['description'] ) ){
						?><p class="field-desc"><?php echo $field['description']; ?></p><?php
					}
					?>
			    </div>
				<?php
			}elseif( $field['field_type'] == 'datetime' ){
				$value = is_array( $value ) ? implode(", ", $value) : $value;
				?>				
			    <div id="form-<?php echo $id.$field['id']; ?>" class="form-group <?php echo $class; ?>">
			        <label for="<?php echo $id.$field['field_key']; ?>" ><?php echo stripslashes( $field['label'] ); ?></label>
			        <input id="<?php echo $id.$field['field_key']; ?>" type="text" class="form-control wpccf-datetimepicker <?php echo $field['field_key']; ?>" name="<?php echo $field['field_key']; ?>" value="<?php echo $value; ?>" autocomplete="off" <?php echo $required; ?>>
			        <?php
					if( !empty( $field['description'] ) ){
						?><p class="field-desc"><?php echo $field['description']; ?></p><?php
					}
					?>
			    </div>
			<?php
			}elseif( $field['field_type'] == 'url' ){
				?>				
			    <div id="form-<?php echo $id.$field['id']; ?>" class="form-group <?php echo $class; ?>">
			        <label for="label-<?php echo $id.$field['field_key']; ?>" ><?php echo stripslashes( $field['label'] ); ?></label>
					<input type="text" id="label-<?php echo $id.$field['field_key']; ?>" class="form-control <?php echo $field['field_key']; ?>" name="<?php echo $field['field_key'].'[]'; ?>" value="<?php echo is_array($value) ? $value[0] : ''; ?>" size="25" <?php echo ( $required ) ? 'required' : '' ; ?> placeholder="<?php esc_html_e('URL Label', 'wpcargo-custom-field' ); ?>" style="margin-bottom: 5px;" />
					<input type="text" id="<?php echo $id.$field['field_key']; ?>" class="form-control <?php echo $field['field_key']; ?>" name="<?php echo $field['field_key'].'[]'; ?>" value="<?php echo is_array($value) ? $value[1] : ''; ?>" size="25" <?php echo ( $required ) ? 'required' : '' ; ?> placeholder="<?php esc_html_e('http://www.sample.com', 'wpcargo-custom-field' ); ?>" style="margin-bottom: 5px;" />
					<input type="checkbox" id="<?php echo $id; ?>new-window-link" class="form-check-input <?php echo $field['field_key']; ?>" name="<?php echo $field['field_key'].'[]'; ?>" <?php echo is_array($value) && !empty($value[2]) ? 'checked' : ''?> ><label for="<?php echo $id; ?>new-window-link" class="form-check-label" ><?php esc_html_e('Open new window?', 'wpcargo-custom-field' ); ?></label>
					<?php
	                    if( !empty( $field['description'] ) ){
	                        ?><p class="field-desc"><?php echo $field['description']; ?></p><?php
	                    }
	                ?>
			    </div>
			<?php
			}elseif( $field['field_type'] == 'file' ){
				$files = array_filter( array_map( 'trim', explode(",", $value) ) );
				?>
				<div id="form-<?php echo $id.$field['id']; ?>" class="form-group <?php echo $class; ?>" >
					<label><?php echo stripslashes( $field['label'] ); ?></label>
					<div class="wpcargo-uploader">
						<div id="wpcargo-gallery-container_<?php echo $id.$field['id'];?>">
							<ul class="wpccf_uploads">
								<?php
								if( !empty( $files ) ){
									foreach ( $files as $file_id ) {
										?>
										<li class="image" data-attachment_id="<?php echo $file_id; ?>">
											<a href="<?php echo wp_get_attachment_url($file_id); ?>" target="_blank"><?php echo wp_get_attachment_image($file_id, array('120', '120'), TRUE); ?></a>
											<span class="img-title" title="<?php echo get_the_title($file_id); ?>"><?php echo substr( get_the_title($file_id), 0, 18 ); ?></span>
											<span class="actions"><a href="#" class="delete" data-imgID="<?php echo $file_id; ?>" data-section="<?php echo $field['id']; ?>">x</a></span>
										</li>
										<?php
									}
								}
								?>
							</ul>
						</div>
						<input id="wpcpq_upload_ids_<?php echo $id.$field['id'];?>" class="<?php echo $field['field_key']; ?>" type="hidden" name="<?php echo $field['field_key']; ?>" value="<?php echo $value; ?>" />
						<a id="wpcargo_select_gallery_<?php echo $id.$field['id'];?>" class="wpccf-upload-attachment btn btn-sm btn-secondary" data-section="<?php echo $field['id']; ?>" ><?php esc_html_e('Add Images / Upload Files', 'wpcargo-custom-field' ); ?></a>
					</div>
					<?php //include( WPCARGO_CUSTOM_FIELD_PATH.'templates/script.tpl.php'); ?>
				</div>
				<?php
			}elseif( $field['field_type'] == 'email' ){
				$value = is_array( $value ) ? implode(", ", $value) : $value;
				?>
			    <div id="form-<?php echo $id.$field['id']; ?>" class="form-group <?php echo $class; ?>">
			        <label for="<?php echo $id.$field['field_key']; ?>" ><?php echo stripslashes( $field['label'] ); ?></label>
			        <input id="<?php echo $id.$field['field_key']; ?>" type="email" class="form-control <?php echo $field['field_key']; ?>" name="<?php echo $field['field_key']; ?>" value="<?php echo $value; ?>" <?php echo $required; ?> >
			        <?php
					if( !empty( $field['description'] ) ){
						?><p class="field-desc"><?php echo $field['description']; ?></p><?php
					}
					?>
			    </div>
				<?php
			}elseif( $field['field_type'] == 'address' ){
				$wpcargo_country = explode( ', ', wpcargo_country_list() );
				$address 		 = wpccf_extract_address( $post_id, $field['field_key'] );
				if( !empty( wpccf_address_fields_data() ) ){
					?><div id="address-form-<?php echo $id.$field['id']; ?>" class="form-group <?php echo $class; ?>"><?php
					$counter = 1;
					foreach ( wpccf_address_fields_data() as $_addr_meta => $_addr_label ) {
						if( $_addr_meta == 'country' ){
							?>
							<div class="country_address-section p-0 col-md-12">
								<label for="<?php echo $id.$field['field_key']; ?>[<?php echo $_addr_meta; ?>]"><?php echo $_addr_label; ?></label>
								<select class="form-control browser-default custom-select <?php echo $field['field_key']; ?>" id="<?php echo $id.$field['field_key']; ?>[<?php echo $_addr_meta; ?>]" name="<?php echo $field['field_key']; ?>[<?php echo $_addr_meta; ?>]" <?php echo $required; ?>>
									<option value =""><?php esc_html_e(' -- Select Country -- ', 'wpcargo-custom-field'); ?></option>
									<?php foreach ($wpcargo_country as $country) : ?>
										<option value="<?php echo $country ?>" <?php echo selected( $address[$_addr_meta], $country ); ?>><?php echo $country ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<?php
						}else{
							?>
							<div class="form-group street_address-section p-0 col-md-12">
								<label for="<?php echo $id.$field['field_key']; ?>[<?php echo $_addr_meta; ?>]" ><?php echo $_addr_label; ?></label>
								<input id="<?php echo $id.$field['field_key']; ?>[<?php echo $_addr_meta; ?>]" type="text" class="form-control <?php echo $_addr_meta.' '.$field['field_key']; ?>" name="<?php echo $field['field_key']; ?>[<?php echo $_addr_meta; ?>]" value="<?php echo $address[$_addr_meta]; ?>" <?php echo ( $required && $counter == 1 ) ? 'required' : '' ; ?>>
							</div>
							<?php
						}
						$counter ++;
					}
					?></div><?php
				}
			}
		endforeach;
		echo ob_get_clean();
	}
}
$WPCCF_Fields = new WPCCF_Fields;