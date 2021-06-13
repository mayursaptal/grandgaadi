<div id="assigned-driver-wrapper" class="col-md-12 mb-4">
	<div class="card">
		<?php do_action( 'wpc_pod_before_driver_default_section', $shipment_id ); ?>
		<section class="card-header">
			<?php echo apply_filters( 'pod_proof_delivery_label', __('Proof of Delivery', 'wpcargo-pod' ) ); ?>
		</section>
		<section class="card-body">
			<div class="row">
				<section id="pod-signature-wrapper" class="col-md-6">
					<p class="h6 text-center"><?php echo apply_filters( 'pod_signature_label', __('Signature', 'wpcargo-pod' ) ); ?></p>
					<div id="pod-signature">
						<img src="<?php echo wp_get_attachment_url( $signature ); ?>" class="signature-generated-img" />
					</div>
				</section>
				<section id="pod-images-wrapper" class="col-md-6">
					<p class="h6 text-center" ><?php echo apply_filters( 'pod_images_label', __('Images', 'wpcargo-pod' ) ); ?></p>
					<?php if (!empty($images)): ?>
					<div class="container">	
						<div id="pod-images" class="row">
								<?php
								$images = explode(',', $images);
								foreach ($images as $image):
									if(!is_numeric($image)) {
										continue;
									}
									?>
									<section class="col-md-4">
										<a href="<?php echo wp_get_attachment_url($image); ?>"><?php echo wp_get_attachment_image($image, 'thumbnail', TRUE); ?></a>
									</section>
									<?php
								endforeach;
								?>
						</div>
					</div>
					<?php endif; ?>
				</section>
			</div>
		</section>
		<?php do_action( 'wpc_pod_after_driver_default_section', $shipment_id ); ?>
	</div>
</div>