<?php do_action( 'wpcsc_pdf_after_header_manifest', $container_id ); ?>
<table class="container-info">
    <tr>
        <td><?php esc_html_e( 'ROUTE', 'wpcargo-shipment-container' ); ?>: <?php echo $origin; ?> - <?php echo $destination; ?></td>
        <td><strong><?php echo apply_filters('wpcsc_manifest_title', __( 'DELIVERY MANIFEST', 'wpcargo-shipment-container' ) ); ?></strong></td>
    </tr>
    <tr>
        <td><?php esc_html_e( 'VEHICLE NO.', 'wpcargo-shipment-container' ); ?>: <?php echo $container_no; ?></td>
        <td><?php esc_html_e( 'NO.', 'wpcargo-shipment-container' ); ?>: <?php echo $tracknumber; ?></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td><?php esc_html_e( 'DATE', 'wpcargo-shipment-container' ); ?>: <?php echo get_post_meta( $container_id, 'date', true ); ?></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td><?php esc_html_e( 'DEVICE ID', 'wpcargo-shipment-container' ); ?>: </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td><strong><?php esc_html_e( 'EMPLOYEE NAME', 'wpcargo-shipment-container' ); ?>:</strong></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td><strong><?php esc_html_e( 'EMPLOYEE SIGNATURE', 'wpcargo-shipment-container' ); ?>:</strong></td>
    </tr>
</table>