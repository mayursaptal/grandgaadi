<form id="wpcfe-filters" action="<?php echo $page_url; ?>" class="form-inline" style="width: 100%">
	<div class="col-md-9 mt-0">
		<div class="row">
			<?php do_action( 'wpcfe_before_shipment_filters' ); ?>
			<?php if( !empty( $wpcargo->status ) ): ?>
				<div class="form-group wpcfe-filter status-filter p-0 mx-1">
					<label class="sr-only" for="status"><?php esc_html_e('Status', 'wpcargo-frontend-manager' ); ?></label>
					<select id="status" name="status" class="form-control md-form wpcfe-select">
						<option value=""><?php echo esc_html__('All Status', 'wpcargo-frontend-manager' ); ?></option>
						<?php 
							foreach ( $wpcargo->status as $status ) {
								?><option value="<?php echo $status; ?>"><?php echo $status; ?></option><?php
							}
						?>
					</select>
				</div>
			<?php endif; ?>
			<div class="form-group wpcfe-filter shipper-filter p-0 mx-1">
				<label class="sr-only" for="shipper"><?php echo $shipper_data['label']; ?></label>
				<select id="shipper" name="shipper" class="form-control md-form wpcfe-select-ajax" data-filter="shipper">
					<option value=""><?php echo esc_html__('All', 'wpcargo-frontend-manager' ).' '.$shipper_data['label']; ?></option>
				</select>
			</div>
			<div class="form-group wpcfe-filter receiver-filter p-0 mx-1">
				<label class="sr-only" for="receiver"><?php echo $receiver_data['label']; ?></label>
				<select id="receiver" name="receiver" class="form-control md-form wpcfe-select-ajax" data-filter="receiver">
					<option value=""><?php echo esc_html__('All', 'wpcargo-frontend-manager' ).' '.$receiver_data['label']; ?></option>
				</select>
			</div>
			<?php do_action( 'wpcfe_after_shipment_filters' ); ?>
			<div class="form-group submit-filter p-0 mx-1">
				<button id="wpcfe-submit-filter" type="submit" class="btn btn-primary btn-fill btn-sm"><?php esc_html_e('Filter', 'wpcargo-frontend-manager' ); ?></button>
			</div>
		</div>
	</div>
	<div class="col-md-3 mt-0 p-0">
		<div class="float-md-none float-lg-right">
			<select id="wpcfesort" name="wpcfesort" class="form-control md-form browser-default">
				<option ><?php echo __('Show entries', 'wpcargo-frontend-manager' ); ?></option>
				<?php foreach( $wpcfesort_list as $list ): ?>
				<option value="<?php echo $list ?>" <?php echo $list == $wpcfesort ? 'selected' : '' ;?>><?php echo $list ?> <?php echo __('entries', 'wpcargo-frontend-manager' ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>
</form>