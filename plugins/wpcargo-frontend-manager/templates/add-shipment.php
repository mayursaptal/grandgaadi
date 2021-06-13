<form method="post" action="" enctype="multipart/form-data" class="add-shipment">
	<?php wp_nonce_field( 'wpcfe_add_action', 'wpcfe_add_form_fields' ); ?>
	<div class="row">
		<div class="col-md-9 mb-3">
			<section class="row"> 
				<?php if( has_action( 'before_wpcfe_shipment_form_fields' ) ): ?>
					<?php do_action( 'before_wpcfe_shipment_form_fields', 0 ); ?>
				<?php
				endif;
				$counter = 1;
				$row_class = '';
				foreach ( wpcfe_get_shipment_sections() as $section => $section_header ) {		
					if( empty( $section ) ){
						continue;
					}
					$section_class = 'col-md-6';
					$column = 12;
					if( ( $section == 'shipper_info' || $section == 'receiver_info' ) && $counter <= 2 && count( wpcfe_get_shipment_sections() ) > 1 ){
						$column = 6;
						$section_class = '';
					}
					if( $section != 'shipper_info' && $section != 'receiver_info' ){
						$row_class = 'row';
					}
					$column = apply_filters( 'wpcfe_shipment_form_column', $column, $section ); 
					?>
					<div id="<?php echo $section; ?>" class="col-md-<?php echo $column; ?> mb-4">
						<div class="card">
							<section class="card-header">
								<?php echo $section_header; ?>
							</section>				
							<section class="card-body <?php echo $row_class; ?>">
								<?php if( has_action( 'before_wpcfe_'.$section.'_form_fields' ) ): ?>
									<?php do_action( 'before_wpcfe_'.$section.'_form_fields', 0 ); ?>
								<?php endif; ?>
								<?php $section_fields = $WPCCF_Fields->get_custom_fields( $section ); ?>
								<?php $WPCCF_Fields->convert_to_form_fields( $section_fields, '', $section_class ); ?>
								<?php if( has_action( 'after_wpcfe_'.$section.'_form_fields' ) ): ?>
									<?php do_action( 'after_wpcfe_'.$section.'_form_fields', 0 ); ?>
								<?php endif; ?>
							</section>
						</div>
					</div>
					<?php
					$counter++;
				}
				if( has_action( 'after_wpcfe_shipment_form_fields' ) ): ?>
					<?php do_action( 'after_wpcfe_shipment_form_fields', 0 ); ?>
				<?php endif; ?>
			</section>
		</div>
		<div class="col-md-3 mb-3">
			<section class="row"> 
				<?php if( has_action( 'before_wpcfe_shipment_form_submit' ) ): ?>
					<div class="after-shipments-info col-md-12 mb-4">
						<?php do_action( 'before_wpcfe_shipment_form_submit' ); ?>
					</div>
				<?php endif; ?>
				<div class="col-md-12 mb-5 text-right">
					<button type="submit" class="btn btn-info btn-fill btn-wd btn-block"><?php esc_html_e('Add Shipment', 'wpcargo-frontend-manager'); ?></button>
				</div>
			</section>
		</div>
	</div>
	<div class="clearfix"></div>
</form>
<?php do_action( 'before_wpcargo_shipment_history', 0); ?>