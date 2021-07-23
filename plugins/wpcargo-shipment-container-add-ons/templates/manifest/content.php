<div id="container-shipments">
    <?php
        if( !empty($shipment_ids) ):
            ?>
            <table>
                <thead>
                    <tr>
                        <td><?php esc_html_e( 'No', 'wpcargo-shipment-container' ); ?></td>
                        <?php do_action( 'wpcsc_manifest_after_counter_header', $container_id ); ?>
                        <td><?php esc_html_e( 'Barcode', 'wpcargo-shipment-container' ); ?></td>
                        <?php do_action( 'wpcsc_manifest_after_barcode_header', $container_id ); ?>
                        <?php 
                        if( !empty( $shipment_fields ) ){
                            foreach ( $shipment_fields  as $field_label ) {
                                $field_data = wpcsc_get_field_data($field_label);
                                ?><td><?php echo $field_data['label']; ?></td><?php
                            }
                        }
                        ?>
                        <?php do_action( 'wpcsc_manifest_after_shipment_header', $container_id ); ?>
                    </tr>
                </thead>	
                <tbody>	
            <?php
            $counter = 1;
            foreach( $shipment_ids as $shipment_id ):
                $shipment_title = get_the_title( $shipment_id );
                ?>
                <tr>
                    <td><?php echo $counter; ?></td>
                    <?php do_action( 'wpcsc_manifest_after_counter_data', $shipment_id, $container_id ); ?>
                    <td>
                        <div style="text-align: center;">
                            <img style="width:120px; height: 20px" src="<?php echo $url_barcode.$shipment_title; ?>">
                            <p><?php echo $shipment_title; ?></p>
                        </div>
                    </td>
                    <?php do_action( 'wpcsc_manifest_after_barcode_data', $shipment_id, $container_id ); ?>
                    <?php 
                        if( !empty( $shipment_fields ) ){
                            foreach ( $shipment_fields  as $field_label ) {
                                $field_data = wpcsc_get_field_data($field_label);
                                $shipment_data = get_post_meta( $shipment_id, $field_data['field_key'], true);
                                if( $field_data['field_type'] == 'cargo_agent' && !empty( $shipment_data ) ){
                                    $shipment_data = $wpcargo->user_fullname( $shipment_data );
                                }
                                ?><td><?php echo $shipment_data; ?></td><?php
                            }
                        }
                    ?>
                    <?php do_action( 'wpcsc_manifest_after_shipment_data', $shipment_id, $container_id ); ?>
                </tr>
                <?php
                $counter++;
            endforeach;
            ?></tbody>	
            </table><?php
        endif;
    ?>
</div>