<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
add_action( 'plugins_loaded', 'wpcfe_load_print_hooks' );
function wpcfe_load_print_hooks(){
    // Print Label Hooks  
    add_action( 'wpcfe_before_label_content', 'wpcfe_before_label_content_callback', 10, 1 );
    add_action( 'wpcfe_label_site_info', 'wpcfe_label_site_info_callback', 10, 1 );
    add_action( 'wpcfe_label_from_info', 'wpcfe_label_from_info_callback', 10, 1 );
    add_action( 'wpcfe_label_to_info', 'wpcfe_label_to_info_callback', 10, 1 );
    add_action( 'wpcfe_end_label_section', 'wpcfe_end_label_section_callback', 100, 1 );
    // Print Label Hooks - Pagination
    add_action( 'wpcfe_label_from_info', 'wpcfe_label_pagination_callback', 5, 4 );
    // Print Invoice Hooks
    add_action( 'wpcfe_before_invoice_content', 'wpcfe_before_invoice_content_callback', 10, 1 );
    add_action( 'wpcfe_invoice_site_info', 'wpcfe_invoice_site_info_callback', 10, 1 );
    add_action( 'wpcfe_invoice_barcode_info', 'wpcfe_invoice_barcode_info_callback', 10, 1 );
    add_action( 'wpcfe_invoice_shipper_info', 'wpcfe_invoice_shipper_info_callback', 10, 1 );
    add_action( 'wpcfe_invoice_receiver_info', 'wpcfe_invoice_receiver_info_callback', 10, 1 );
    add_action( 'wpcfe_end_invoice_section', 'wpcfe_end_invoice_section_callback', 100, 1 );
    // Print Bill of Lading
    add_action( 'wpcfe_before_table_bol_section', 'wpcfe_before_table_bol_section_callback', 10, 1 );
    add_action( 'wpcfe_start_bol_section', 'wpcfe_start_bol_section_callback', 10, 1 );
    add_action( 'wpcfe_bol_from_info', 'wpcfe_bol_from_info_callback', 10, 1 );
    add_action( 'wpcfe_bol_to_info', 'wpcfe_bol_to_info_callback', 10, 1 );
    add_action( 'wpcfe_bol_barcode_info', 'wpcfe_bol_barcode_info_callback', 10, 1 );
    add_action( 'wpcfe_middle_bol_section', 'wpcfe_bol_shipment_info_callback', 10, 1 );
    add_action( 'wpcfe_middle_bol_section', 'wpcfe_bol_shipment_package_callback', 10, 1 );
}
// Print Label hook callback
function wpcfe_before_label_content_callback(){
    ?>
    <style>
        table{ border-collapse: collapse; }
        table td{ vertical-align:top; border:1px solid #000; padding: 8px; }
        table td *{ margin:0; padding:0; }
        img#log{ width:50% !important; }
    </style>
    <?php
}
function wpcfe_label_site_info_callback( $shipmentDetails ){
    ?>
    <section style="text-align:center"><?php echo $shipmentDetails['siteInfo']; ?></section>
    <?php
}
function wpcfe_label_from_info_callback( $shipmentDetails ){
    global $WPCCF_Fields, $wpcargo;
    ?>
    <h1 style="margin-bottom:18px;"><?php esc_html_e('From:', 'wpcargo-frontend-manager'); ?></h1>
    <?php
    echo wpcfe_print_data( 'shipper_info', $shipmentDetails['shipmentID']);
}
function wpcfe_label_to_info_callback( $shipmentDetails ){
    global $WPCCF_Fields, $wpcargo;
    ?>
    <h1 style="margin-bottom:18px;"><?php esc_html_e('To:', 'wpcargo-frontend-manager'); ?></h1>
    <section id="section-to"><?php echo wpcfe_print_data( 'receiver_info', $shipmentDetails['shipmentID']); ?></section>
    <?php
    
}
function wpcfe_end_label_section_callback( $shipmentDetails  ){
    global $wpcargo;
    ?>
    <tr>
        <td colspan="2" class="barcode" style="text-align:center;padding-top:18px;">
            <img id="frontend-label-barcode" class="label-barcode" style="height: 120px;padding:0;margin:0;" src="<?php echo $wpcargo->barcode_url( $shipmentDetails['shipmentID'] ); ?>">
            <p style="font-size:24px;padding:0;margin:0;"><?php echo get_the_title( $shipmentDetails['shipmentID']); ?><p>
        </td>
    </tr>
    <?php
}

// Label pagination hook
function wpcfe_label_pagination_callback( $shipmentDetails, $packages, $package, $counter  ){
    if( !$packages ){
        return false;
    }
    $totalCount     = count( $packages );
    $str_find       = array_keys( wpcfe_package_shortcode() );
    $str_replce     = wpcfe_package_shortcode_map( $package, $counter, $totalCount );
    $ppage          = str_replace($str_find, $str_replce, wpcfe_label_pagination_template() );
    ?>
    <p style="float:right;padding-right:12px;"><?php echo $ppage ; ?></p>
    <?php
}


// Print Invoice hook callback
function wpcfe_before_invoice_content_callback(){
    ?>
    <style>
        table{ border-collapse: collapse; }
        table td{ vertical-align:top; padding: 8px; }
        table td *{ margin:0; padding:0; }
        table#package-table td,
        table#package-table th{ border:1px solid #000; padding: 6px; }
        table#package-table th{ white-space:nowrap; }
        .border-bottom{ border-bottom: 1px solid #000; }
        .space-topbottom{ padding-top:18px; padding-bottom:18px; }
        img#log{ width:50% !important; }
    </style>
    <?php
}
function wpcfe_invoice_site_info_callback( $shipmentDetails ){
    ?>
    <section style="text-align:<?php echo is_rtl() ? 'right' : 'left'; ?>"><?php echo $shipmentDetails['siteInfo']; ?></section>
    <?php
}
function wpcfe_invoice_shipper_info_callback( $shipmentDetails ){
    global $WPCCF_Fields, $wpcargo;
    ?>
    <h1 style="margin-bottom:18px;"><?php esc_html_e('SHIPPER DETAILS:', 'wpcargo-frontend-manager'); ?></h1>
    <?php
    echo wpcfe_print_data( 'shipper_info', $shipmentDetails['shipmentID']);
}
function wpcfe_invoice_receiver_info_callback( $shipmentDetails ){
    global $WPCCF_Fields, $wpcargo;
    ?>
    <h1 style="margin-bottom:18px;"><?php esc_html_e('RECEIVER DETAILS:', 'wpcargo-frontend-manager'); ?></h1>
    <section id="section-to"><?php echo wpcfe_print_data( 'receiver_info', $shipmentDetails['shipmentID']); ?></section>
    <?php
}
function wpcfe_invoice_barcode_info_callback( $shipmentDetails ){
    global $wpcargo;
    $barcode_height   = wpcfe_print_barcode_sizes('invoice')['height'];
    $barcode_width    = wpcfe_print_barcode_sizes('invoice')['width'];
    ?>
    <section style="text-align:center;" >
        <img id="frontend-invoice-barcode" class="invoice-barcode" style="height: <?php echo absint($barcode_height).'px'; ?>; width: <?php echo absint($barcode_width).'px'; ?>" src="<?php echo $wpcargo->barcode_url( $shipmentDetails['shipmentID'] ); ?>">
        <p style="font-size:18px;"><?php echo get_the_title( $shipmentDetails['shipmentID']); ?><p>
    </section>
    <?php
}
function wpcfe_end_invoice_section_callback( $shipmentDetails ){
    if( empty(wpcargo_get_package_data( $shipmentDetails['shipmentID'] ))){
        return false;
    }
    ?>
    <tr>
        <td colspan="2">
            <h1 style="margin-bottom:18px;"><?php esc_html_e('PACKAGE DETAILS:', 'wpcargo-frontend-manager'); ?></h1>
            <table id="package-table" style="width:100%;">
                <thead>
                    <tr>
                        <?php foreach ( wpcargo_package_fields() as $key => $value): ?>
                            <?php  if( in_array( $key, wpcargo_package_dim_meta() ) && !wpcargo_package_settings()->dim_unit_enable ){ continue; }
                            ?>
                            <th><?php echo $value['label']; ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( wpcargo_get_package_data( $shipmentDetails['shipmentID'] ) as $data_key => $data_value): ?>
                    <tr>
                        <?php foreach ( wpcargo_package_fields() as $field_key => $field_value): ?>
                            <?php if( in_array( $field_key, wpcargo_package_dim_meta() ) && !wpcargo_package_settings()->dim_unit_enable ){ continue; } ?>
                            <td>
                                <?php 
                                    $package_data = array_key_exists( $field_key, $data_value ) ? $data_value[$field_key] : '' ;
                                    echo is_array( $package_data ) ? implode(',', $package_data ) : $package_data; 
                                ?>

                            </td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </td>
    </tr>
    <?php
}
// Bill of Lading Callback
function wpcfe_before_table_bol_section_callback(){
    ?>
    <style>
        .padding-default{ padding:8px; }
        table{ border-collapse: collapse; }
        table td{ vertical-align:top; border:1px solid #000; }
        table td *{ margin:0; padding:0; }
        table#package-table th{ white-space:nowrap; }
        .shipment-info td { padding:6; border:none; }
        #package-table td, #package-table th{ padding:6px; border:1px solid #000; }
        .border-bottom{ border-bottom: 1px solid #000; }
        .space-topbottom{ padding-top:18px; padding-bottom:18px; }
        img#log{ width:50% !important; }
        .section-title{ margin-bottom:8px; text-align:center; background-color:#333; color:#fff; padding: 4px 0; }        
    </style>
    <?php
}
function wpcfe_start_bol_section_callback( $shipmentDetails ){
    ?>
    <tr>
        <td colspan="2" class="padding-default" style="text-transform: uppercase;text-align:center; font-size:24px;"><?php esc_html_e('Bill of Lading', 'wpcargo-frontend-manager') ?></td>
    </tr>
    <?php
}
function wpcfe_bol_from_info_callback( $shipmentDetails ){
    global $WPCCF_Fields, $wpcargo;
    ?>
    <h1 class="section-title"><?php esc_html_e('SHIP FROM', 'wpcargo-frontend-manager'); ?></h1>
    <div class="padding-default">
        <?php echo wpcfe_print_data( 'shipper_info', $shipmentDetails['shipmentID']); ?>
    </div>
    <?php
}
function wpcfe_bol_to_info_callback( $shipmentDetails ){
    global $WPCCF_Fields, $wpcargo;
    ?>
    <h1 class="section-title"><?php esc_html_e('SHIP TO', 'wpcargo-frontend-manager'); ?></h1>
    <div class="padding-default">
        <?php echo wpcfe_print_data( 'receiver_info', $shipmentDetails['shipmentID']); ?>
    </div>
    <?php
}
function wpcfe_bol_barcode_info_callback( $shipmentDetails ){
    global $WPCCF_Fields, $wpcargo;
    $barcode_height   = wpcfe_print_barcode_sizes('waybill')['height'];
    $barcode_width    = wpcfe_print_barcode_sizes('waybill')['width'];
    ?>
    <section class="padding-default" style="text-align:center;" >
        <img id="frontend-bol-barcode" class="invoice-bol" style="height: <?php echo absint($barcode_height).'px'; ?>; width: <?php echo absint($barcode_width).'px'; ?>" src="<?php echo $wpcargo->barcode_url( $shipmentDetails['shipmentID'] ); ?>">
        <p style="font-size:18px;"><?php echo get_the_title( $shipmentDetails['shipmentID']); ?><p>
    </section>
    <?php
}
function wpcfe_bol_shipment_info_callback( $shipmentDetails ){
    global $WPCCF_Fields, $wpcargo;
    ?>
    <tr>
        <td colspan="2">
            <h1 class="section-title"><?php esc_html_e('ADDITIONAL INFORMATION', 'wpcargo-frontend-manager'); ?></h1>
            <div class="padding-default">
                <table style="width:100%;" class="shipment-info">
                    <?php
                    $field_keys = $WPCCF_Fields->get_custom_fields( 'shipment_info' );
                    if( !empty( $field_keys ) ){
                        $counter = 1;
                        foreach ( $field_keys as $field ) {
                            $field_data = maybe_unserialize( get_post_meta( $shipmentDetails['shipmentID'], $field['field_key'], TRUE ) );
                            if( is_array( $field_data ) ){
                                $field_data = implode(", ", $field_data);
                            }
                            if( $counter == 1 ){
                                echo '<tr>';
                            }

                            // table data
                            echo '<td>';
                                if( $field['field_type'] == 'file' ){
                                    $files = array_filter( array_map( 'trim', explode(",", $field_data) ) );
                                    if( !empty( $files ) ){
                                        ?>
                                        <div class="wpccfe-files-data">
                                            <label><?php echo $field['label']; ?></label><br/>
                                            <div id="wpcargo-gallery-container_<?php echo $field['id'];?>">
                                                <ul class="wpccf_uploads">
                                                    <?php
                                                        foreach ( $files as $file_id ) {
                                                            $att_meta = wp_get_attachment_metadata( $file_id );
                                                            ?>
                                                            <li class="image">
                                                                <?php echo get_the_title($file_id); ?>
                                                            </li>
                                                            <?php
                                                        }
                                                    ?>
                                                </ul>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                }elseif( $field['field_type'] == 'url' ){
                                    $url_data = maybe_unserialize( get_post_meta( $shipment_id, $field['field_key'], TRUE ) );
                                    $target   = count( $url_data ) > 2 ? '_blank' : '' ;
                                    $url 	  = $url_data[1] ? $url_data[1] : '#' ;
                                    $label 	  = $url_data[0];
                                    ?><p><?php echo $field['label']; ?>:<br/><a href="<?php echo $url; ?>" target="<?php echo $target; ?>"><?php echo $label; ?></a></p><?php
                                }else{
                                    ?><p><?php echo $field['label']; ?>:<br/><?php echo $field_data; ?></p><?php
                                }	
                            echo '</td>';
                            if( $counter == 3 ){
                                echo '</tr>';
                                $counter = 1;
                                continue;
                            }
                            $counter++;
                        }
                    }
                    ?>
                </table>
            </div>
        </td>
    </tr>
    <?php
}
function wpcfe_bol_shipment_package_callback( $shipmentDetails ){
    if( empty(wpcargo_get_package_data( $shipmentDetails['shipmentID'] ))){
        return false;
    }
    ?>
    <tr>
        <td colspan="2">
            <h1 class="section-title no-margin"><?php esc_html_e('PACKAGE DETAILS', 'wpcargo-frontend-manager'); ?></h1>
            <div class="padding-default">
                <table id="package-table" style="width:100%;">
                    <thead>
                        <tr>
                            <?php foreach ( wpcargo_package_fields() as $key => $value): ?>
                                <?php  if( in_array( $key, wpcargo_package_dim_meta() ) && !wpcargo_package_settings()->dim_unit_enable ){ continue; }
                                ?>
                                <th><?php echo $value['label']; ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( wpcargo_get_package_data( $shipmentDetails['shipmentID'] ) as $data_key => $data_value): ?>
                        <tr>
                            <?php foreach ( wpcargo_package_fields() as $field_key => $field_value): ?>
                                <?php if( in_array( $field_key, wpcargo_package_dim_meta() ) && !wpcargo_package_settings()->dim_unit_enable ){ continue; } ?>
                                <td>
                                    <?php 
                                        $package_data = array_key_exists( $field_key, $data_value ) ? $data_value[$field_key] : '' ;
                                        echo is_array( $package_data ) ? implode(',', $package_data ) : $package_data; 
                                    ?>

                                </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </td>
    </tr>
    <?php
}
// PDF Function Helpers
function wpcfe_pdf_pagination( $dompdf, $print_type ){
    $has_pagination = apply_filters( 'wpcfe_has_pdf_pagination', '__return_true' );
    if( !$has_pagination ){
        return false;
    }

    $font_family    = apply_filters( 'wpcfe_pdf_pagination_font_family', 'Helvetica', $print_type );
    $font_type      = apply_filters( 'wpcfe_pdf_pagination_font_type', 'normal', $print_type );
    $x              = apply_filters( 'wpcfe_pdf_pagination_x_axis', 505, $print_type );
    $y              = apply_filters( 'wpcfe_pdf_pagination_y_axis', 790, $print_type );
    $text           = apply_filters( 'wpcfe_pdf_pagination_label', "{PAGE_NUM} of {PAGE_COUNT}", $print_type );   
    $font           = $dompdf->getFontMetrics()->get_font($font_family, $font_type);   
    $size           = apply_filters( 'wpcfe_pdf_pagination_font_size', 10, $print_type );    
    $color          = array(0,0,0);
    $word_space     = 0.0;
    $char_space     = 0.0;
    $angle          = 0.0;
    $dompdf->getCanvas()->page_text(
        $x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle
    );
}