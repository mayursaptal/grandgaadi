<form id="wpc-ie-form" method="POST" action="<?php echo admin_url(); ?>edit.php?post_type=wpcargo_shipment&page=wpcie-export" >
    <?php wp_nonce_field( 'wpc_import_ie_results_callback', 'wpc_ie_nonce' ); ?>
    <?php do_action( 'wpcie_admin_before_export_form_field' ); ?>
    <p><strong class="left-lbl"><?php esc_html_e( 'Shipper Name', 'wpc-import-export' ); ?>: </strong><input id="search-shipper" type="text" name="search-shipper" value="<?php echo isset($_REQUEST['search-shipper']) ? $_REQUEST['search-shipper'] : '';  ?>" /></p>
    <p><strong class="left-lbl"><?php esc_html_e( 'Registered Shipper', 'wpc-import-export' ); ?>: </strong>
        <?php
        $user_args = array(
            'meta_key' => 'first_name',
            'orderby'  => 'meta_value',
			'role__in' => array( 'wpcargo_client' ),
            );
        $all_users = get_users( $user_args );
        if( !empty($all_users) && is_array($all_users) ) {
            ?><select name="shipment_author"><?php
            ?><option value=""><?php esc_html_e('-- Registered Shipper --', 'wpc-import-export' ); ?></option><?php
                foreach($all_users as $user){
                    $fullname = $wpcargo->user_fullname( $user->ID );
                    ?><option value="<?php  echo trim($user->ID); ?>" <?php echo isset( $_REQUEST['shipment_author'] ) && $_REQUEST['shipment_author'] == $user->ID ? 'selected' : '';  ?> ><?php echo $fullname; ?></option><?php
                }
            ?></select><?php
        }
        ?>
    </p>
    <p><strong class="left-lbl"><?php esc_html_e( 'Status', 'wpc-import-export' ); ?>: </strong>
        <select id="wpcargo_status" name="wpcargo_status">
            <option value=""><?php esc_html_e('Select Status', 'wpc-import-export' ); ?></option>
            <?php 
            if ( !empty( $shipment_status ) ){
                foreach ($shipment_status as $status ) {
                    ?><option value="<?php echo $status; ?>" <?php selected( $selected_status, $status ); ?>><?php echo $status; ?></option><?php
                }
            }
            ?>
        </select>
    </p>
    <p><strong class="left-lbl"><?php esc_html_e('Date Range', 'wpc-import-export' ); ?>: </strong>
    <span id="import-datepicker"><label for="date-from" ><?php esc_html_e('From : ', 'wpc-import-export'); ?></label>
    <input class="import-datepicker wpcie-datepicker" type="text" id="wpcargo-import-form" name="date-from" value="<?php echo isset($_REQUEST['date-from']) ? $_REQUEST['date-from'] : ''; ?>" required="required" autocomplete="off" placeholder="YYYY-MM-DD" />
    <label for="date-to"><?php esc_html_e('To : ', 'wpc-import-export'); ?></label>
    <input class="import-datepicker wpcie-datepicker" type="text" id="wpcargo-import-to" name="date-to" value="<?php echo isset($_REQUEST['date-to']) ? $_REQUEST['date-to'] : ''; ?>" required="required" autocomplete="off" placeholder="YYYY-MM-DD" />
    </span>
    </p>
    <?php do_action( 'wpcie_admin_after_export_form_field' ); ?>
    <?php do_action( 'wpc_export_form_field' ); ?>
    <div id="multi-select-export">
        <p><strong><?php esc_html_e("Select Fields", 'wpc-import-export'); ?> </strong></p>
        <div class="row">
            <div class="col-xs-5">
                <select name="from[]" id="multiselect" class="form-control" size="8" multiple="multiple">
                    <?php
                    ksort($fields);
                    if($fields) {
                        foreach( $fields as $key => $value ){
                            ?><option value="<?php echo $value['meta_key']; ?>"><?php echo $value['label']; ?></option><?php
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="col-xs-2">
                <button type="button" id="multiselect_rightAll" class="btn btn-block"><span class="dashicons dashicons-controls-skipforward"></span></button>
                <button type="button" id="multiselect_rightSelected" class="btn btn-block"><span class="dashicons dashicons-controls-forward"></span></button>
                <button type="button" id="multiselect_leftSelected" class="btn btn-block"><span class="dashicons dashicons-controls-back"></span></button>
                <button type="button" id="multiselect_leftAll" class="btn btn-block"><span class="dashicons dashicons-controls-skipback"></span></button>
            </div>
            <div class="col-xs-5">
                <select name="meta-fields[]" id="multiselect_to" class="form-control" size="8" multiple="multiple">
                    <?php 
                        if(!empty( $options ) ) {
                            foreach ($options as $optkey => $optvalue ) {
                                echo "<option value='".$optkey."'>".$optvalue."</option>";
                            }
                        }
                    ?>
                </select>
            </div>
        </div>
    </div>
    <div style="clear:both;"></div>
    <input type="hidden" name="post_type" value="wpcargo_shipment" />
    <input type="hidden" name="page" value="<?php echo $page; ?>" />
    <p><input style="margin-top: 24px;" class="button button-primary button-large" type="submit" name="submit" value="<?php esc_html_e('Export Shipment', 'wpc-import-export' ); ?>" /></p>
</form>
<script type="text/javascript">
jQuery(document).ready(function($) {
$('#multiselect').multiselect({
    sort: false,
    autoSort: false,
    autoSortAvailable: false,
});	
$("#multiselect_to, #multiselect").on('change',function() {		
    setTimeout(function(){			
        var selectoptions= {};				
        $.each($("#multiselect_to option"), function( ) {
            var metaKey = $(this).attr("value");
            var metaValue = $(this).text();	
            selectoptions[metaKey] = metaValue;		
        });
        jQuery.ajax({				
            url : 'admin-ajax.php',				
            type : 'post',				
            data : {				
                action : 'update_import_option_ajax_request',				
                multiselect_settings: selectoptions				
            },				
            success : function( response ) {							
            }				
        });				
    }, 1000);		
});  
});
</script>