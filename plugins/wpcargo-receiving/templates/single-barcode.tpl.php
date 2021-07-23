<form method="post" id="wpc-receiving">
    <div class="row wpc-receiving-tbl">
        <div class="col-md-12 mb-4">
            <div class="receiving-input">
                <input type="checkbox" id="clear-fields" name="clear-fields" value="1" class="form-check-input "> 
                <label for="clear-fields"><strong><?php esc_html_e('Clear all fields after scanned.', 'wpcargo-receiving' );?></strong></label>
            </div>
            <div class="receiving-input">
                <input type="checkbox" id="add-not-found" name="add-not-found" value="1"  class="form-check-input">
                <label for="add-not-found"><strong> <?php esc_html_e('Add when shipment is not found.', 'wpcargo-receiving'); ?> </strong></label>
            </div>
        </div>
        <div class="row col-md-12 mb-4">
            <?php do_action( 'wpcr_before_receiving_form_fields'  ); ?>
            <?php foreach( $wpcr_dfields as $history_name => $history_value ): ?>
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
                    $width_class = ( $history_name == 'remarks' ) ? 'col-md-12' : 'col-md-6';
                    $location_class = ( $history_name == 'location' ) ? 'status_location' : '';
                    $value = trim(str_replace('am' , '' ,  str_replace('pm' , '' , $value)));

                ?>
                <div class="<?php echo $width_class; ?> mb-4 receiving-input">
                    <label for="wpc-receiving-<?php echo $history_name; ?>"><?php echo $history_value['label'];?></label><br />
                    <?php if( $history_name != 'updated-name' ): ?>
                        <?php echo wpcargo_field_generator( $history_value, $history_name, $value, 'form-control wpc-receiving-'.$history_name.' '.$select_class.' '.$picker_class.' '.$location_class ); ?>
                    <?php else: ?>
                        <input readonly="readonly" class="form-control wpc-receiving-current-user-name" type="text" name="updated-name" value="<?php echo $wpcargo->user_fullname( get_current_user_id() );?>" />
                        <input class="wpc-receiving-current-user-id" type="hidden" name="updated-by" value="<?php echo get_current_user_id(); ?>"/>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            <?php do_action( 'wpcr_after_receiving_form_fields'  ); ?>
        </div>
        <div class="col-md-12 mb-4 alert alert-info">
            <h3><?php _e('Enter Shipment Number', 'wpcargo-receiving' );?>: </h3>
            <div class="receiving-input">
                <input type="text" class="form-control wpc-receiving-shipment"  placeholder="<?php esc_html_e('Scan your shipment barcode to update or enter the tracking number and press ENTER', 'wpcargo-receiving');?>" id="wpc-tracking-number" name="wpc-tracking-number">
            </div>
        </div>
        <?php do_action( 'wpcr_after_receiving_shipment_fields'  ); ?>
    </div>
</form>
<div class="wpc-receiver-notif alert"></div>
<div class="wpc-receivier-notes">
    <p><?php esc_html_e('Notes:', 'wpcargo-receiving');?></p>
    <ol>
        <li><?php esc_html_e('If you have connected your barcode scanner please scan directly to the barcode and it will automatically update the Shipment Status', 'wpcargo-receiving');?></li>
        <li>
            <?php
				esc_html_e(
					"If you don't have a barcode scanner please input the tracking number on the field and press <i>Enter</i> on your keyboard", 'wpcargo-receiving'
				);
			?>
		</li>
    </ol>
</div>
<script>
    /*
    ** Google map Script Auto Complete address
    */
    var placeSearch, autocomplete, map, geocoder;
    function wpcSHinitMap() {
        geocoder = new google.maps.Geocoder();
        getPlace_dynamic();
    }
    function getPlace_dynamic() {
        var defaultBounds = new google.maps.LatLngBounds(
            new google.maps.LatLng(-33.8902, 151.1759),
            new google.maps.LatLng(-33.8474, 151.2631)
        );
        var input = document.getElementsByClassName('status_location');
        var options = {
            bounds: defaultBounds,
            types: ['geocode']
        };
        for (i = 0; i < input.length; i++) {
            autocomplete = new google.maps.places.Autocomplete(input[i], options);
        }
    }
</script>