<div class="col-md-3" >
    <section id="container-status" class="mb-4">
        <div class="card mb-4">
            <section class="card-header">
                <?php echo apply_filters( 'wpcfe_publish_status_label', __('Current Status','wpcargo-shipment-container') ); ?>: <br/><span class="font-weight-bold text-uppercase"><?php echo get_post_meta( $container_id, 'container_status', true ); ?></span>
            </section>
        </div>
    </section>
    <section id="container-history" class="mb-4">
        <div class="card">
            <section class="card-header">
                <?php echo apply_filters( 'wpcfe_publish_header_label', __('Publish','wpcargo-shipment-container') ); ?>
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
                                <label for="<?php echo '_wpcsh_'.$history_name; ?>"><?php echo $history_value['label'];?></label>
                                <?php echo wpcargo_field_generator( $history_value, '_wpcsh_'.$history_name, $value, 'form-control _wpcsh_'.$history_name.' '.$select_class.' '.$picker_class ); ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="form-check">
                    <input id="wpcscapply-shipment" type="checkbox" class="form-check-input" name="apply_shipment" value="1"/>
                    <label for="wpcscapply-shipment"><?php esc_html_e( 'Apply this update for all shipments in the Container.', 'wpcargo-shipment-container' ); ?></label>
                </div>
            </section>
        </div>
    </section>
    <section class="text-center">
        <input type="hidden" id="container_id" name="container_id" value="<?php echo $container_id; ?>">
        <button type="submit" class="btn btn-info btn-fill btn-wd btn-block"><?php echo $submit_label; ?></button>
    </section>
</div>