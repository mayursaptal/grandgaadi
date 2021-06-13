<form method="post" action="">
	<?php wp_nonce_field( 'wpcargo_pod_sign_action', 'wpcargo_pod_sign_nonce' ); ?>
	<div id="pod-pop-up">
		<?php do_action( 'wpcpod_before_popup_header' ); ?>
		<?php	
		//$get_sid = isset($_REQUEST['sid']) ? $_REQUEST['sid'] : '';
		$get_sid = $shipment_id;
		$get_pod_img = get_post_meta($get_sid, 'wpcargo-pod-image', true);
		$wpcargo_get_signature 	= get_post_meta($get_sid, 'wpcargo-pod-signature', true);
		$get_shipment_update = unserialize( get_post_meta( $get_sid, 'wpcargo_shipments_update', true ) );
		$latest_update = end( $get_shipment_update );
		if ( is_plugin_active( 'wpcargo-custom-field-addons/wpcargo-custom-field.php' ) ) {
			require_once(WPCARGO_POD_PATH.'templates/wpc-pod-sign-header-cf.tpl.php');
		}else{
			require_once(WPCARGO_POD_PATH.'templates/wpc-pod-sign-header.tpl.php');
		}
		?>
		<?php do_action( 'wpcpod_after_popup_header', $get_sid ); ?>
		<?php do_action( 'wpcpod_before_upload_container', $get_sid ); ?>
		<div class="wpcargo-upload container">
			<input type="hidden" id="wpcargo-shipment-id" name="wpcargo-shipment-id" value="<?php echo $get_sid;?>">
			<input type="hidden" id="wpcargo-pod-image" name="wpcargo-pod-image" value="<?php //echo isset($_REQUEST['wpcargo-pod-image']) ? $_REQUEST['wpcargo-pod-image'] : $get_pod_img;?>">
			<input type="hidden" id="wpcargo-pod-signature" name="wpcargo-pod-signature" value="<?php //echo isset($_REQUEST['wpcargo-pod-signature']) ? $_REQUEST['wpcargo-pod-signature'] : $wpcargo_get_signature; ?>">		
			<div class="wpcargo-add-signature">
				<?php require_once( WPCARGO_POD_PATH.'templates/wpc-pod-signature-form.tpl.php'); ?>
			</div>	
			<div id="images-section">
				<a href="#" id="wpcargo-pod-img-btn" class="wpcargo-btn wpcargo-btn-success"><?php esc_html_e( 'ADD IMAGES', 'wpcargo-pod' ); ?></a>	
				<div id="wpcargo-pod-images">			
					<p class="header-pod-result"><?php esc_html_e('Your current captured images:', 'wpcargo-pod' ); ?></p>
					<?php
					if(!empty($get_pod_img)) {
						$explode_pod_img = array_filter( explode(",", $get_pod_img) );
						if(is_array($explode_pod_img)) {
							foreach($explode_pod_img as $pod_img) {
								echo '<div class="gallery-thumb" data-id="'.$pod_img.'"><div class="single-img"><img width="250" src="'.wp_get_attachment_url( $pod_img ).'"/></div><span class="remove-gallery-img" title="Remove">x</span></div>';
							}
						}
					} else {
						?><img src="<?php echo WPCARGO_POD_URL. 'assets/img/no-image.jpg'; ?>"><?php
					}
					?>	
				</div>
			</div>
		</div>
		<?php do_action( 'wpcpod_after_upload_container', $get_sid ); ?>
		<?php do_action( 'wpcpod_before_status_container', $get_sid ); ?>
		<div class="pod-status container">	
			<div class="pod-details row">
				<div class="col-md-6 mb-4">
					<p>
						<label><?php esc_html_e( 'Location:', 'wpcargo-pod' ); ?> </label><br/>
						<input name="wpcargo-pod-location" id="wpcargo-pod-location" required="required" class="form-control wpcargo-pod-location" value="<?php echo $latest_update['location']; ?>">
					</p>		
				</div>
				<div class="col-md-6 mb-4">
					<p><label><?php esc_html_e( 'Status', 'wpcargo-pod' ); ?> </label><br/>
						<select name="wpcargo-pod-status" id="wpcargo-pod-status" required="required" class="form-control browser-default">
							<option value=""> <?php esc_html_e('-- Select Status --', 'wpcargo-pod' ); ?> </option>
							<?php if( !empty( wpcargo_pod_status() ) ): ?>
								<?php foreach( wpcargo_pod_status() as $status): ?>
									<option value="<?php echo $status; ?>" <?php echo ( $wpcargo_get_status == $status ) ? 'selected' : ''; ?>> <?php echo $status; ?> </option>
								<?php endforeach; ?>
							<?php endif; ?>
						</select>
					</p>
				</div>
				<div class="col-md-12 mb-4">
					<p>
						<label><?php esc_html_e( 'Remarks:', 'wpcargo-pod' ); ?> </label><br/>
						<textarea name="wpcargo-pod-notes" id="wpcargo-pod-notes" required="required" class="form-control wpcargo-pod-notes"><?php echo $latest_update['remarks']; ?></textarea>
					</p>	
				</div>
				<input type="hidden" name="delivery-location" value="<?php //echo $wpcargo_receiver_add; ?>" />
			</div>
		</div>
		<?php do_action( 'wpcpod_after_status_container', $get_sid ); ?>
		<div class="pod-submit container">	
			<div class="status-btn pt-sm-4">
				<input type="button" id="pod-sign-submit" class="delivered-btn btn btn-success" name="submit" value="<?php esc_html_e('Update', 'wpcargo-pod' ); ?>">
			</div>
		</div>
    </div>
</form>
<script>
	jQuery(document).ready(function ($) {
    	function remove_gallery_img(){
			$('#pod-pop-up').on('click', '.remove-gallery-img', function(){
        	    var id_array = [];
        		var get_shipment_id = $( '#wpcargo-shipment-id' ).val();
        		var remove_id = $(this).parent().attr('data-id');
        		var image_ids = $('#wpcargo-pod-image').val();
        		id_array = image_ids.split(',');
        		
        	    id_array = $.grep(id_array, function(value) {
        		  return value != remove_id;
        		});
        		var new_ids = id_array.join(',');
        		
        		$.ajax({
                    type: "POST",
                    url: wpcargoPODAJAXHandler.ajaxurl,
                    data:{
                        action: 'wpcargo_pod_images',
                        get_shipment_id : get_shipment_id,
                        get_images_id: id_array,
				    	get_action_type : 'remove'
                    },
                    success:function(response){
        				$('#wpcargo-pod-image').val(new_ids);
                    }
                });
        		$(this).parent().remove();
        	});
    	}
    	remove_gallery_img();
	
		var ajax_url_profile_picture = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
		$( '#wpcargo-pod-img-btn' ).click(function(e) {
			e.preventDefault();
			var get_shipment_id = $( '#wpcargo-shipment-id' ).val();
			var image_ids = $('#wpcargo-pod-image').val();
			var insertImage = wp.media.controller.Library.extend({
				defaults :  _.defaults({					
				}, wp.media.controller.Library.prototype.defaults )
			});
			
			var media_upload = wp.media({
				title: "<?php esc_html_e('Upload Images', 'wpcargo-pod' ); ?>",
				multiple: true, 
				button : { text : "<?php esc_html_e('Upload Images', 'wpcargo-pod' ); ?>" },
			
			}).open().on( 'select', function() {
				attachment = media_upload.state().get( 'selection' ).toJSON();
				var ids = [];
				for (i = 0; i < attachment.length; i++) {
					if(attachment[i]['subtype'] == 'png' || attachment[i]['subtype'] == 'jpeg' || attachment[i]['subtype'] == 'jpg' || attachment[i]['subtype'] == 'gif' || attachment[i]['subtype'] == 'gif' || attachment[i]['subtype'] == 'svg'){
						ids[i] = attachment[i]['id'];
					}
				}
				
				if( ! ids ){ return; }
                
				var data = {
					'action': 'wpcargo_setpost_images',
					'get_images': ids,
					'get_shipment_id': get_shipment_id
				};
				
				jQuery.post(ajax_url_profile_picture, data , function(response){
					jQuery("#wpcargo-pod-images").html( response );
				});	
				
				var data2 = {
					'action': 'wpcargo_pod_images',
					'get_images_id': ids,
					'get_shipment_id': get_shipment_id,
				};
				
				var saved_ids = image_ids.split(',');
				var merge_ids = $.merge(saved_ids, ids);
				var new_ids = merge_ids.join(',');
				jQuery.post(ajax_url_profile_picture, data2 , function(response){
					jQuery("#wpcargo-pod-image").val(new_ids);
					remove_gallery_img();
				});
			});
		});	
	});
</script>