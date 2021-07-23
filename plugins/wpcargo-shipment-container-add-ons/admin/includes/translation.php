<?php
/*
 * Language Translation for the Ecncrypted Files
 * Post Type - Shipment Container
 */
function wpc_container_label_singular(){
	return apply_filters( 'wpc_container_label_singular', __( 'Container', 'wpcargo-shipment-container' ) );
}
function wpc_container_label_plural(){
	return apply_filters( 'wpc_container_label_plural', __( 'Containers', 'wpcargo-shipment-container' ) );
}
function wpc_scpt_container_label(){
	return __( 'Container', 'wpcargo-shipment-container' );
}
function wpc_scpt_container_num_label(){
	return __('Container #', 'wpcargo-shipment-container' );
}
function wpc_scpt_container_description_label(){
	return __( 'Container for WPCargo Shipment', 'wpcargo-shipment-container' );
}
function wpc_scpt_name_label(){
	return _x( 'Containers', 'Post Type General Name', 'wpcargo-shipment-container' );
}	
function wpc_scpt_singular_name_label(){
	return _x( 'Container', 'Post Type Singular Name', 'wpcargo-shipment-container' );
}	
function wpc_scpt_menu_name_label(){
	return __( 'Shipment Container', 'wpcargo-shipment-container' );
}	
function wpc_scpt_archives_label(){
	return __( 'Container Archives', 'wpcargo-shipment-container' );
}
function wpc_scpt_attributes_label(){
	return __( 'Container Attributes', 'wpcargo-shipment-container' );
}
function wpc_scpt_parent_item_colon_label(){
	return __( 'Parent Container:', 'wpcargo-shipment-container' );
}
function wpc_scpt_all_items_label(){
	return __( 'All Containers', 'wpcargo-shipment-container' );
}
function wpc_scpt_add_new_item_label(){
	return __( 'Add New Container', 'wpcargo-shipment-container' );
}
function wpc_scpt_add_new_label(){
	return __( 'Add Container', 'wpcargo-shipment-container' );
}
function wpc_scpt_new_item_label(){
	return __( 'New Container', 'wpcargo-shipment-container' );
}
function wpc_scpt_edit_item_label(){
	return __( 'Edit Container', 'wpcargo-shipment-container' );
}
function wpc_scpt_update_item_label(){
	return __( 'Update Container', 'wpcargo-shipment-container' );
}
function wpc_scpt_view_item_label(){
	return __( 'View Container', 'wpcargo-shipment-container' );
}
function wpc_scpt_view_items_label(){
	return __( 'View Containers', 'wpcargo-shipment-container' );
}
function wpc_scpt_search_items_label(){
	return __( 'Search Container', 'wpcargo-shipment-container' );
}
function wpc_scpt_not_found_label(){
	return __( 'Not found', 'wpcargo-shipment-container' );
}
function wpc_scpt_not_found_in_trash_label(){
	return __( 'Not found in Trash', 'wpcargo-shipment-container' );
}
function wpc_scpt_featured_image_label(){
	return __( 'Featured Image', 'wpcargo-shipment-container' );
}
function wpc_scpt_set_featured_image_label(){
	return __( 'Set featured image', 'wpcargo-shipment-container' );
}
function wpc_scpt_remove_featured_image_label(){
	return __( 'Remove featured image', 'wpcargo-shipment-container' );
}
function wpc_scpt_use_featured_image_label(){
	return __( 'Use as featured image', 'wpcargo-shipment-container' );
}
function wpc_scpt_insert_into_item_label(){
	return __( 'Insert into Container', 'wpcargo-shipment-container' );
}
function wpc_scpt_uploaded_to_this_item_label(){
	return __( 'Uploaded to this item', 'wpcargo-shipment-container' );
}
function wpc_scpt_items_list_label(){
	return __( 'Containers list', 'wpcargo-shipment-container' );
}
function wpc_scpt_items_list_navigation_label(){
	return __( 'Containers list navigation', 'wpcargo-shipment-container' );
}
function wpc_scpt_filter_items_list_label(){
	return __( 'Filter Containers list', 'wpcargo-shipment-container' );
}
function wpc_scpt_assinged_shipment_label(){
	return __( 'Assigned Shipments', 'wpcargo-shipment-container' );
}
function wpc_scpt_assinged_container_label(){
	return __( 'Assigned Container', 'wpcargo-shipment-container' );
}
function wpc_scpt_shipment_container_label(){
	return __( 'Shipment Container', 'wpcargo-shipment-container' );
}
function wpc_scpt_container_history_label(){
	return __( 'Container History', 'wpcargo-shipment-container' );
}
function wpc_scpt_container_settings_label(){
	return __( 'Container Settings', 'wpcargo-shipment-container' );
}
function wpc_scpt_print_container_shipment_label(){
	return __( 'Print Container Shipment', 'wpcargo-shipment-container' );
}
function wpc_scpt_wrong_message(){
	return __('Opss. Something went wrong!', 'wpcargo-shipment-container' );
}
function wpc_scpt_no_result_message(){
	return __('No results found!', 'wpcargo-shipment-container');
}
function wpc_scpt_not_assigned_message(){
	return __('Not yet assigned to any container', 'wpcargo-shipment-container' );
}
function wpc_scpt_license_message(){
	return __( 'Please activate your license key', 'wpcargo-shipment-container' ).' <a href="'.admin_url().'admin.php?page=wptaskforce-helper" title="WPCargo license page">'.__('here', 'wpcargo-shipment-container' ).'</a>.';
}
function wpc_scpt_license_helper_plugin_dependent_message(){
	return __('This plugin requires <a href="http://wpcargo.com/" target="_blank">WPTaskForce License Helper</a> plugin to be active!', 'wpcargo-shipment-container' );
}
function wpc_scpt_wpcargo_plugin_dependent_message(){
	return __( 'This plugin requires <a href="https://wordpress.org/plugins/wpcargo/" target="_blank">WPCargo</a> plugin to be active!', 'wpcargo-shipment-container' );
}
function wpc_scpt_custom_field_plugin_dependent_message(){
	return __( 'This plugin requires <strong>WPCargo Custom Field Add-ons</strong> plugin to be active!', 'wpcargo-shipment-container' );
}
function wpc_scpt_frontend_manager_plugin_dependent_message(){
	return __( 'This plugin requires <strong>WPCargo Frontend Manager</strong> plugin to be active!', 'wpcargo-shipment-container' );
}
function wpc_scpt_pod_plugin_dependent_message(){
	return __( 'This plugin requires <strong>WPCargo Proof of Delivery Add-on</strong> plugin to be active!', 'wpcargo-shipment-container' );
}
function wpc_scpt_cheating_plugin_dependent_message(){
	return __( 'Cheating, uh?', 'wpcargo-shipment-container' );
}