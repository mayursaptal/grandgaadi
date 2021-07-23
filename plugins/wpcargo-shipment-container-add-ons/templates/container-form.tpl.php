<form method="POST" id="container-form" class="row">
    <?php $submit_label = isset( $_GET['wpcsc'] ) && $_GET['wpcsc'] == 'edit' ? esc_html__('Update Container', 'wpcargo-shipment-container') : esc_html__('Add Container', 'wpcargo-shipment-container'); ?>
    <?php wp_nonce_field( 'wpcsc_form_action', 'wpcsc_nonce_field_value' ); ?>
    <div class="col-md-9">
        <div class="row">
            <div class="col-md-12 mb-4">
                <!-- Default input -->
                <label class="sr-only" for="wpcsc_number"><?php echo wpc_scpt_container_num_label(); ?></label>
                <div class="input-group">
                    <div class="input-group-prepend">
                    <div class="input-group-text"><i class="fa fa-barcode mr-3"></i><?php echo wpc_scpt_container_num_label(); ?></div>
                    </div>
                    <input type="text" class="form-control py-0" id="wpcsc_number" name="wpcsc_number" value="<?php echo get_the_title( $container_id ); ?>">
                </div>
            </div>
            <?php include_once( WPCARGO_SHIPMENT_CONTAINER_PATH.'templates/container-form-shipments.tpl.php' ); ?>
			<!-- #container-info -->
            <div id="container-info" class="col-md-6 mb-4">
                <div class="card">
                    <section class="card-header">
                        <?php echo __( 'Container Information', 'wpcargo-shipment-container' ); ?>
                    </section>
                    <section class="card-body">
                        <?php $WPCCF_Fields->convert_to_form_fields( wpc_container_info_fields(), $container_id ); ?>
                        <?php do_action('wpc_shipment_container_after_container_info'); ?>
                    </section>  
                </div>    
            </div>
            <div id="trip-info" class="col-md-6 mb-4">
                <div class="card">
                    <section class="card-header">
                        <?php esc_html_e( 'Trip Information', 'wpcargo-shipment-container' ); ?>
                    </section>
                    <section class="card-body">
                        <?php $WPCCF_Fields->convert_to_form_fields( wpc_trip_info_fields(), $container_id ); ?>
                        <?php do_action( 'wpc_shipment_container_after_trip_info', $container_id ); ?>
                    </section> 
                </div>           
            </div>
            <div id="time-info" class="col-md-12 mb-4">
                <div class="card">
                    <section class="card-header">
                        <?php esc_html_e( 'Time Information', 'wpcargo-shipment-container' ); ?>
                    </section>
                    <section class="card-body">
                        <?php $WPCCF_Fields->convert_to_form_fields( wpc_time_info_fields(), $container_id ); ?>
                        <?php do_action( 'wpc_shipment_container_after_time_info', $container_id ); ?>
                    </section>
                </div>
            </div>
			<?php do_action( 'wpc_shipment_additional_container_info', $container_id ); ?>
            <?php do_action( 'before_wpcargo_shipment_history', $container_id); ?>
			<!-- #container-info -->
        </div> <!-- End Row -->
    </div> <!-- End col-md-9 -->
    <?php include_once( WPCARGO_SHIPMENT_CONTAINER_PATH.'templates/container-form-misc.tpl.php' ); ?>
</form>
<?php include_once( WPCARGO_SHIPMENT_CONTAINER_PATH.'templates/container-form-modal.tpl.php' ); ?>