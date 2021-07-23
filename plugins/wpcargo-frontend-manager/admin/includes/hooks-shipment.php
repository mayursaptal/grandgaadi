<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
// Shipment table Callback
function wpcfe_shipper_receiver_shipment_header_callback(){
    $shipper_data   = wpcfe_table_header('shipper');
    $receiver_data  = wpcfe_table_header('receiver');
    ?>
    <th class="no-space"><?php echo apply_filters( 'wpcfe_shipper_table_header_label', $shipper_data['label'] ); ?></th>
	<th class="no-space"><?php echo apply_filters( 'wpcfe_receiver_table_header_label', $receiver_data['label'] ); ?></th>
    <?php
}
function wpcfe_shipper_receiver_shipment_data_callback( $shipment_id ){
    $shipper_data   = wpcfe_table_header('shipper');
    $receiver_data  = wpcfe_table_header('receiver');
    $shipper_meta 	= apply_filters( 'wpcfe_shipper_table_cell_data', get_post_meta( $shipment_id, $shipper_data['field_key'], true ), $shipment_id );
	$receiver_meta 	= apply_filters( 'wpcfe_receiver_table_cell_data', get_post_meta( $shipment_id, $receiver_data['field_key'], true ), $shipment_id );
    ?>
    <td class="no-space"><?php echo $shipper_meta; ?></td>
	<td class="no-space"><?php echo $receiver_meta; ?></td>
    <?php
}
function wpcfe_shipment_number_header_callback(){
    echo '<th>'.apply_filters( 'wpcfe_shipment_number_label', __('Tracking Number', 'wpcargo-frontend-manager' ) ).'</th>';
}
function wpcfe_shipment_number_data_callback( $shipment_id ){
    $page_url           = get_the_permalink( wpcfe_admin_page() );
    $shipment_title     = apply_filters( 'wpcfe_shipment_number', get_the_title(), $shipment_id );
    echo '<td><a href="'.$page_url.'?wpcfe=track&num='.$shipment_title.'" class="text-primary font-weight-bold">'.$shipment_title.'</a></td>';
}
function wpcfe_shipment_table_header_status(){
    ?><th><?php _e('Status', 'wpcargo-frontend-manager' ); ?></th><?php
}
function wpcfe_shipment_table_data_status( $shipment_id  ){
    $status = get_post_meta( $shipment_id, 'wpcargo_status', true );
    ?><td class="shipment-status <?php echo wpcfe_to_slug( $status ); ?>"><?php echo $status; ?></td><?php
}
function wpcfe_shipment_table_header_view(){
    ?><th class="text-center"><?php _e('View', 'wpcargo-frontend-manager' ); ?></th><?php
}
function wpcfe_shipment_table_data_view( $shipment_id  ){
    $page_url           = get_the_permalink( wpcfe_admin_page() );
    $shipment_title     = apply_filters( 'wpcfe_shipment_number', get_the_title(), $shipment_id );
    ?>
    <td class="text-center">
        <a href="<?php echo $page_url; ?>?wpcfe=track&num=<?php echo $shipment_title; ?>" title="<?php echo __('View', 'wpcargo-shipment-rate' ); ?>">
            <i class="fa fa-list text-success"></i>
        </a>
    </td>
    <?php
}
function wpcfe_shipment_table_header_type(){
    ?><th><?php _e('Shipment Type', 'wpcargo-frontend-manager' ); ?></th><?php
}
function wpcfe_shipment_table_data_type( $shipment_id ){
    ?><td class="shipment-type <?php echo wpcfe_to_slug( wpcfe_get_shipment_type( $shipment_id ) ); ?>"><?php echo wpcfe_get_shipment_type( $shipment_id ); ?></td><?php
}
function wpcfe_shipment_table_header_action_print(){
    if( empty( wpcfe_print_options() ) ) return false;
    ?>
    <th class="text-center"><?php _e('Print', 'wpcargo-frontend-manager' ); ?></th>
    <?php
}   
function wpcfe_shipment_table_action_print( $shipment_id ){
    $print_options = wpcfe_print_options();
    if( empty( $print_options ) ) return false;
    ?>
    <td class="text-center print-shipment">
        <div class="dropdown" style="display:inline-block !important;">
            <!--Trigger-->
            <button class="btn btn-default btn-sm dropdown-toggle m-0 py-1 px-2" type="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false"><i class="fa fa-print"></i></button>
            <!--Menu-->
            <div class="dropdown-menu dropdown-primary">
                <?php foreach( $print_options as $print_key => $print_label ): ?>
                    <a class="dropdown-item print-<?php echo $print_key; ?> py-1" data-id="<?php echo $shipment_id; ?>" data-type="<?php echo $print_key; ?>" href="#"><?php echo $print_label; ?></a>
                <?php endforeach; ?>
            </div>
        </div>
    </td>
    <?php
}
function wpcfe_shipment_table_header_action_update(){
    if( !can_wpcfe_update_shipment() ) return false;
    ?>
    <th class="text-center"><?php _e('Update', 'wpcargo-frontend-manager'); ?></th>
    <?php
}   
function wpcfe_shipment_table_action_update( $shipment_id ){
    if( !can_wpcfe_update_shipment() ) return false;
    $page_url = get_the_permalink( wpcfe_admin_page() );
    ?>
    <td class="text-center wpcfe-action wpcfe-action-update"><?php echo apply_filters( 'wpcfe_update_shipment_action', wpcfe_update_shipment_action( $shipment_id, $page_url ), $shipment_id, $page_url ); ?></td>
    <?php
}
function wpcfe_shipment_table_header_action_delete(){
    if( !can_wpcfe_delete_shipment() ) return false;
    ?>
    <th class="text-center"><?php _e('Delete', 'wpcargo-frontend-manager'); ?></th>
    <?php
}   
function wpcfe_shipment_table_action_delete( $shipment_id ){
    if( !can_wpcfe_delete_shipment() ) return false;
    ?>
    <td class="text-center wpcfe-action wpcfe-action-delete">
        <a href="#" class="wpcfe-delete-shipment" data-id="<?php echo $shipment_id; ?>" title="<?php _e('Delete', 'wpcargo-frontend-manager'); ?>"><i class="fa fa-trash text-danger"></i></a>
    </td>	
    <?php
}
// Update shipment hooks
add_action( 'plugins_loaded', 'wpcfe_initialize_table_hooks' );
function wpcfe_initialize_table_hooks(){  
    // Shipment table Hook
    add_action( 'wpcfe_shipment_before_tracking_number_header', 'wpcfe_shipment_number_header_callback', 25 );
    add_action( 'wpcfe_shipment_before_tracking_number_data', 'wpcfe_shipment_number_data_callback', 25 );
    // Shipment Shipper / Receiver Column
    add_action( 'wpcfe_shipment_after_tracking_number_header', 'wpcfe_shipper_receiver_shipment_header_callback', 25 );
    add_action( 'wpcfe_shipment_after_tracking_number_data', 'wpcfe_shipper_receiver_shipment_data_callback', 25 );
    // Shipment Type Column
    add_action( 'wpcfe_shipment_table_header', 'wpcfe_shipment_table_header_type', 25 ); 
    add_action( 'wpcfe_shipment_table_data', 'wpcfe_shipment_table_data_type', 25 );
    // Shipment Status Column
    add_action( 'wpcfe_shipment_table_header', 'wpcfe_shipment_table_header_status', 25 ); 
    add_action( 'wpcfe_shipment_table_data', 'wpcfe_shipment_table_data_status', 25 );
    // Shipment View Column
    add_action( 'wpcfe_shipment_table_header', 'wpcfe_shipment_table_header_view', 25 );
    add_action( 'wpcfe_shipment_table_data','wpcfe_shipment_table_data_view', 25 );
    // Shipment Print Column
    add_action( 'wpcfe_shipment_table_header_action', 'wpcfe_shipment_table_header_action_print', 25 ); 
    add_action( 'wpcfe_shipment_table_data_action', 'wpcfe_shipment_table_action_print', 25 );
    // Shipment Update Column
    add_action( 'wpcfe_shipment_table_header_action', 'wpcfe_shipment_table_header_action_update', 25 ); 
    add_action( 'wpcfe_shipment_table_data_action', 'wpcfe_shipment_table_action_update', 25 );
    // Shipment Delete Column
    add_action( 'wpcfe_shipment_table_header_action', 'wpcfe_shipment_table_header_action_delete', 25 ); 
    add_action( 'wpcfe_shipment_table_data_action', 'wpcfe_shipment_table_action_delete', 25 );
}