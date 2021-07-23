<?php $wpcfe_print_options = wpcfe_print_options(); ?>
<?php do_action('wpcfe_before_shipment_table'); ?>
<div id="shipment-filters" class="filters-card mb-4">
	<div class="filters-body row wpcfe-filter">
		<?php require_once( wpcfe_include_template( 'filter-shipment' ) ); ?>
	</div>
</div>
<div class="shipments-wrapper mb-4" style="visibility: visible; animation-name: fadeIn;">
    <div class="shipments-body">
		<div id="shipments-table-list" class="content">
			<?php if ( $wpc_shipments->have_posts() ) : ?>
			<div class="table-top form-group">
				<form id="wpcfe-search" class="float-md-none float-lg-right" action="<?php echo $page_url; ?>" method="get">
					<div class="form-sm">
						<label for="search-shipment" class="sr-only"><?php esc_html_e('Shipment Number', 'wpcargo-frontend-manager' ); ?></label>
						<input type="text" class="form-control form-control-sm" name="wpcfes" id="search-shipment" placeholder="<?php esc_html_e('Shipment Number', 'wpcargo-frontend-manager' ); ?>">
						<button type="submit" class="btn btn-primary btn-sm mx-md-0 ml-2"><?php esc_html_e('Search', 'wpcargo-frontend-manager' ); ?></button>
					</div>
				</form>
				<?php if( !empty( $wpcfe_print_options ) ): ?>
				<div class="wpcfe-bulkprint-wrapper dropdown" style="display:inline-block !important;">
	`			<!--Trigger-->
					<button class="btn btn-default btn-lg dropdown-toggle m-0 py-1 px-2" type="button" data-toggle="dropdown"
						aria-haspopup="true" aria-expanded="false"><i class="fa fa-print"></i><span class="mx-2"><?php esc_html_e('Print', 'wpcargo-frontend-manager'); ?></span></button>
					<!--Menu-->
					<div class="dropdown-menu dropdown-primary">
						<?php foreach( $wpcfe_print_options as $print_key => $print_label ): ?>
							<a class="wpcfe-bulk-print dropdown-item print-<?php echo $print_key; ?> py-1" data-type="<?php echo $print_key; ?>" href="#"><?php echo $print_label; ?></a>
						<?php endforeach; ?>
					</div>
				</div>
				<?php endif; ?>
				<?php if( can_wpcfe_delete_shipment() ): ?>
					<button class="remove-shipments btn btn-danger btn-sm"><i class="fa fa-trash text-white"></i> <?php _e('Delete', 'wpcargo-frontend-manager'); ?></button>
				<?php endif; ?>
				<?php do_action( 'wpcfe_before_after_shipment_table' ); ?>
			</div>
			<div class="card">
				<div class="card-body table-responsive">
					<table id="shipment-list" class="table table-hover table-sm">
						<thead>
							<tr>
								<th class="form-check">
									<input class="form-check-input " id="wpcfe-select-all" type="checkbox"/>
									<label class="form-check-label" for="materialChecked2"></label>
								</th>
								<?php do_action( 'wpcfe_shipment_before_tracking_number_header' ); ?>
								<?php do_action( 'wpcfe_shipment_after_tracking_number_header' ); ?>
								<?php do_action( 'wpcfe_shipment_table_header' ); ?>
								<?php do_action( 'wpcfe_shipment_table_header_action' ); ?>
							</tr>
						</thead>
						<tbody>
							<?php	
							do_action( 'wpcfe_before_shipment_table_row', $wpc_shipments ); 				
							while ( $wpc_shipments->have_posts() ) {
								$wpc_shipments->the_post();
								$shipment_title 		= apply_filters( 'wpcfe_shipment_number', get_the_title(), get_the_ID() );
								?>
								<tr id="shipment-<?php echo get_the_ID(); ?>" class="shipment-row <?php echo wpcfe_to_slug( $status ); ?>">
									<td class="form-check">
									  <input class="wpcfe-shipments form-check-input " type="checkbox" name="wpcfe-shipments[]" value="<?php echo get_the_ID(); ?>" data-number="<?php echo $shipment_title; ?>">
									  <label class="form-check-label" for="materialChecked2"></label>
									</td>
									<?php do_action( 'wpcfe_shipment_before_tracking_number_data', get_the_ID() ); ?>
									<?php do_action( 'wpcfe_shipment_after_tracking_number_data', get_the_ID() ); ?>
									<?php do_action( 'wpcfe_shipment_table_data', get_the_ID() ); ?>
									<?php do_action( 'wpcfe_shipment_table_data_action', get_the_ID() ); ?>				
								</tr>
								<?php
							} // end while
							do_action( 'wpcfe_after_shipment_table_row', $wpc_shipments );
							?>
						</tbody>
					</table>
				</div>
			</div>
			<?php if( !empty( $wpcfe_print_options ) ): ?>
				<div class="wpcfe-bulkprint-wrapper dropdown" style="display:inline-block !important;">
	`			<!--Trigger-->
					<button class="btn btn-default btn-lg dropdown-toggle m-0 py-1 px-2" type="button" data-toggle="dropdown"
						aria-haspopup="true" aria-expanded="false"><i class="fa fa-print"></i><span class="mx-2"><?php esc_html_e('Print', 'wpcargo-frontend-manager'); ?></span></button>
					<!--Menu-->
					<div class="dropdown-menu dropdown-primary">
						<?php foreach( $wpcfe_print_options as $print_key => $print_label ): ?>
							<a class="wpcfe-bulk-print dropdown-item print-<?php echo $print_key; ?> py-1" data-type="<?php echo $print_key; ?>" href="#"><?php echo $print_label; ?></a>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>
			<?php if( can_wpcfe_delete_shipment() ): ?>
				<button class="remove-shipments btn btn-danger btn-sm"><i class="fa fa-trash text-white"></i> <?php esc_html_e('Delete', 'wpcargo-frontend-manager'); ?></button>
			<?php endif; ?>
			<?php do_action( 'wpcfe_before_after_shipment_table' ); ?>
			<div class="row">
				<section class="col-md-5">
					<?php
						printf(
							'<p class="note note-primary">Showing %s to %s of %s entries.</p>',
							$record_start,
							$record_end,
							number_format($number_records)
						);
					?>
				</section>
				<section class="col-md-7"><?php wpcfe_bootstrap_pagination( array( 'custom_query' => $wpc_shipments ) ); ?></section>
			</div>
			<?php else: ?>
				<i class="fa fa-inbox d-block p-2 text-center text-danger" style="font-size: 4rem;"></i>
				<h3 class="text-center text-danger"><?php _e('No shipment found!', 'wpcargo-frontend-manager' ); ?></h3>
			<?php endif; ?>			
		</div>
	</div>
</div>
<?php do_action('wpcfe_after_shipment_data'); ?>