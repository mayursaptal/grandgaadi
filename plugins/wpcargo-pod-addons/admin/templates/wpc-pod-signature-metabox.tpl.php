<div class="wpcargo-pod">
	<?php do_action( 'wpc_pod_before_default_details', $post->ID ); ?>
	<table class="wpcargo form-table">
		<tr>
			<th><?php esc_html_e( 'Signature', 'wpcargo-pod' ); ?></th>
			<td>
				<?php
				if(!empty($get_pod_signature)) {
				?>
					<div id="wpcargo-pod-signature-img">
						<p><?php esc_html_e( 'Proof of Acknowledgment', 'wpcargo-pod' ); ?></p>
						<img src="<?php echo wp_get_attachment_url( $get_pod_signature ); ?>" />
					</div>	<!-- wpcargo-pod-signature-img -->
				<?php
				}
				?>
			</td>
		</tr>
		<tr>
			<?php
			$get_pod_pictures = get_post_meta($post->ID, 'wpcargo-pod-image', true);
			?>
			<th><label><?php esc_html_e( 'POD Image', 'wpcargo-pod' ); ?></label></th>
			<td>
				<div class="wpcargo-uploader">
					<div id="wpcargo-gallery-container_pod">
						<ul class="wpcargo_images">
						<?php
							if (!empty($get_pod_pictures) || $get_pod_pictures != NULL):
								$get_images_id = explode(',', $get_pod_pictures);
								foreach ($get_images_id as $image_id):
									if (!empty($image_id)) {
										?>
										<li class="image" data-attachment_id="<?php echo $image_id; ?>">
											<a href="<?php echo wp_get_attachment_url($image_id); ?>"><?php echo wp_get_attachment_image($image_id, 'thumbnail', TRUE); ?></a>
											<ul class="actions">
												<li><a href="#" class="delete" title="Delete image"><?php esc_html_e( 'Delete', 'wpcargo-pod' ); ?></a></li>
											</ul>
										</li>
										<?php
									}
								endforeach;
							endif;
						?>
					</ul>
				</div>
				<input id="wpcargo_image_gallery_pod" type="hidden" name="wpcargo-pod-image" value="<?php echo $get_pod_pictures; ?>" />
				<a id="wpcargo_select_gallery_pod" class="button" data-delete="Delete image" data-text="Delete" ><?php esc_html_e( 'Add Images / Upload Files', 'wpcargo-pod' ); ?></a> 
				</div>
			</td>
		</tr>
		<script>
			jQuery(document).ready(function($){	
				// Uploading files	
				var file_frame;	
				var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id	
				var set_to_post_id = $("#wpcargo_post_id_pod").val(); // Set this	
				var $image_gallery_ids = $( '#wpcargo_image_gallery_pod' );	
				var $product_images    = $( '#wpcargo-gallery-container_pod' ).find( 'ul.wpcargo_images' );	
				jQuery('#wpcargo_select_gallery_pod').live('click', function( event ) {	
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
								$product_images.append( '<li class="image" data-attachment_id="' + attachment.id + '"><img src="' + attachment_image + '" /><ul class="actions"><li><a href="#" class="delete" title="' + $el.data('delete') + '">' + $el.data('text') + '</a></li></ul></li>' );	
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
				$( '#wpcargo-gallery-container_pod' ).on( 'click', 'a.delete', function() {	
					$( this ).closest( 'li.image' ).remove();	
					var attachment_ids = '';	
					$( '#wpcargo-gallery-container_pod' ).find( 'ul li.image' ).css( 'cursor', 'default' ).each( function() {	
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
	</table>
	<?php do_action( 'wpc_pod_after_default_details', $post->ID ); ?>
</div>	
