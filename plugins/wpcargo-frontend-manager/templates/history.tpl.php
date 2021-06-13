<?php
	global $wpcargo , $WPCCF_Fields;
	$user_role = wpcfe_current_user_role();
?>
<div id="wpcfe-misc-history" class="card mb-4">
	<section class="card-header">
		<?php echo apply_filters( 'wpcfe_history_header_label', __('History','wpcargo-frontend-manager') ); ?> <span class="float-right font-weight-bold text-uppercase"><?php echo $shipment->ID ? wpcfe_get_shipment_status( $shipment->ID ) : ''; ?></span>
	</section>
	<section class="card-body">
		<div class="form-row">
			<?php foreach( wpcargo_history_fields() as $history_name => $history_value ): ?>
				<?php 
					$picker_class = '';
					$value = '';
					if( $history_name == 'date' ){
						$picker_class = 'wpccf-datepicker';
						$value = current_time( $wpcargo->date_format );
					}elseif( $history_name == 'time' ){
						$picker_class = 'wpccf-timepicker';
						$value = current_time( $wpcargo->time_format );
					}
					$select_class = ( $history_value['field'] == 'select' ) ? 'browser-default' : '';
				?>
				<div class="form-group col-md-12">
					<?php if( $history_name != 'updated-name' ): ?>
						<label for="status-<?php echo $history_name; ?>"><?php echo $history_value['label'];?></label>
						<?php echo wpcargo_field_generator( $history_value, $history_name, $value, 'form-control status_'.$history_name.' '.$select_class.' '.$picker_class ); ?>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</section>
</div>