
<?php do_action( 'wpcfe_before_label_content', $shipmentDetails, null, null, null ); ?>
<table style="width:100%;">
    <?php do_action( 'wpcfe_start_label_section', $shipmentDetails, null, null, null ); ?>
    <tr>
        <td style="width:50% !important; vertical-align: middle !important; text-align: center !important;">
            <?php do_action( 'wpcfe_label_site_info', $shipmentDetails, null, null, null ); ?>
        </td>
        <td style="width:50% !important; padding-right:18px;">
            <?php do_action( 'wpcfe_label_from_info', $shipmentDetails, null, null, null ); ?>
        </td>
    </tr>
    <?php do_action( 'wpcfe_middle_label_section', $shipmentDetails, null, null, null ); ?>
    <tr>
        <td colspan="2" style="padding-left:28px;">
            <?php do_action( 'wpcfe_label_to_info', $shipmentDetails, null, null, null ); ?>
        </td>
    </tr>
    <?php do_action( 'wpcfe_end_label_section', $shipmentDetails, null, null, null ); ?>
</table>
<?php do_action( 'wpcfe_after_label_content', $shipmentDetails, null, null, null ); ?>
 