<div id="assigned-shipment" class="col-md-12 mb-4">
    <h1 class="mb-2 h3"><?php echo wpc_scpt_assinged_container_label(); ?> <a id="showShipmentList" class="btn btn-info btn-sm float-right" data-id="<?php echo $container_id; ?>" data-toggle="modal" data-target="#shipmentListModalPreview"><?php esc_html_e( 'Add Shipment', 'wpcargo-shipment-container' ); ?></a></h1>
    <div class="container py-4 px-0">
        <section id="shipment-info-wrapper" class="w-100 m-0">
            <?php $shipment_count = wpcshcon_shipment_count( $container_id ); ?>
            <i class="fa fa-list"></i> <span class="shipment-count"><?php echo $shipment_count; ?></span> <?php  _e( 'Shipments', 'wpcargo-shipment-container' ); ?> <a href="#" id="wpcsc-toggle" class="text-info" data-stat="show" ><?php _e( 'Show', 'wpcargo-shipment-container' ); ?></a>
        </section>
        <section id="shipment-list-wrapper" class="row w-100 m-0 display-none">
            <?php do_action('wpc_admin_before_assigned_shipments'); ?>
                <table id="shipment-list" class="table table-hover table-sm">
                    <thead>
                        <tr>
                            <th style="white-space: nowrap;width: 1%;">&nbsp;</th>
                            <?php do_action( 'wpcsc_before_header_shipment_content_section' ); ?>
                            <th class="text-center"><?php esc_html_e('Shipment', 'wpcargo-shipment-container'); ?></th>
                            <th><?php esc_html_e('Status', 'wpcargo-shipment-container'); ?></th>
                            <th class="text-center"><?php esc_html_e('Print', 'wpcargo-shipment-container'); ?></th>
                            <th class="text-center"><?php esc_html_e('Update Status', 'wpcargo-shipment-container'); ?></th>
                            <th class="text-center"><?php esc_html_e('Remove', 'wpcargo-shipment-container'); ?></th>
                            <?php do_action( 'wpcsc_after_header_shipment_content_section'); ?>
                        </tr>
                    </thead>
                    <tbody id="container-shipment-list-wrapper">
                        <?php if( !empty( $shipments )): ?>
                            <?php foreach( $shipments as $shipment_id ): ?>
                                <?php
                                    $shipment_title = get_the_title($shipment_id);
                                    $status = get_post_meta( $shipment_id, 'wpcargo_status', true );
                                    $wpcfe_print_options = wpcfe_print_options();
                                ?>
                                <tr id="shipment-<?php echo $shipment_id; ?>" data-shipment="<?php echo $shipment_id; ?>" class="selected-shipment p-1 col-md-4" >
                                    <td class="align-middle"><i class="fa fa-sort mr-3"></i></td>
                                    <td class="text-center">
                                        <?php do_action( 'wpcsc_before_shipment_content_section', $shipment_id ); ?>
                                        <h3 class="shipment-title h6"><a style="text-decoration: none;" href="<?php echo get_the_permalink( wpcfe_admin_page() ).'?wpcfe=track&num='.$shipment_title; ?>" target="_blank"><?php echo $shipment_title; ?></a></h3>
                                        <?php do_action( 'wpcsc_after_shipment_content_section', $shipment_id ); ?>
                                    </td>
                                    <td><?php echo $status; ?></td>
                                    <td class="text-center print-shipment">
                                        <div class="dropdown">
                                            <!--Trigger-->
                                            <button class="btn btn-default btn-sm dropdown-toggle m-0 py-1 px-2" type="button" id="dropdownPrint-<?php echo $shipment_id; ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-list"></i></button>
                                            <!--Menu-->
                                            <div class="dropdown-menu dropdown-primary">
                                                <?php foreach( $wpcfe_print_options as $print_key => $print_label ): ?>
                                                    <a class="dropdown-item print-<?php echo $print_key; ?> py-1" data-id="<?php echo $shipment_id; ?>" data-type="<?php echo $print_key; ?>" href="#"><?php echo $print_label; ?></a>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <!--Trigger-->
                                            <button class="btn btn-success btn-sm dropdown-toggle m-0 py-1 px-2" type="button" id="update-<?php echo $shipment_id; ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-edit"></i></button>
                                            <!--Menu-->
                                            <div class="dropdown-menu dropdown-primary">
                                                <?php foreach( $wpcargo->status as $status ): ?>
                                                    <a class="update-shipment dropdown-item py-1" data-id="<?php echo $shipment_id; ?>" data-value="<?php echo $status; ?>" href="#"><?php echo $status; ?></a>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm m-0 py-1 px-2 remove-shipment" data-id="<?php echo $shipment_id; ?>" title="<?php esc_html_e('Remove', 'wpcargo-shipment-container'); ?>"><i class="fa fa-trash"></i></button>
                                    </td>
                                    <?php do_action( 'wpcsc_after_shipment_content_section', $shipment_id ); ?>
                                </tr>
                            <?php endforeach ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            <?php do_action( 'wpc_admin_after_assigned_shipments', $container_id ); ?>
        </section>  
        <input type="hidden" name="wpcc_sorted_shipments" id="wpcc_sorted_shipments" value="<?php echo wpc_shipment_container_sorted_shipment( $container_id ); ?>" /> 
    </div>
</div>