<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
function wpcapi_allowed_role()
{
	$roles = array(
		'administrator', 'wpcargo_api_manager', 'wpcargo_driver', 'cargo_agent', 'wpcargo_client', 'wpc_shipment_manager', 'wpcargo_branch_manager'
	);
	$roles = apply_filters('wpcapi_allowed_role', $roles);
	return $roles;
}
function wpcapi_exist($str)
{
	global $wpdb;
	$api = $wpdb->get_var($wpdb->prepare("SELECT `meta_value` FROM `{$wpdb->prefix}usermeta` WHERE `meta_key` LIKE 'wpcargo_api' AND `meta_value` = '%s'", $str));
	if ($api &&  $api !== $str) {
		$api = NULL;
	}
	return $api;
}
function wpcapi_shipment_exist($str)
{
	global $wpdb;
	$shipmentID     = $wpdb->get_var($wpdb->prepare("SELECT `ID` FROM `{$wpdb->prefix}posts` WHERE `post_status` LIKE 'publish' AND `post_type` LIKE 'wpcargo_shipment' AND  `post_title` LIKE '%s' LIMIT 1", $str));
	return $shipmentID;
}
function wpcapi_user_id($str)
{
	global $wpdb;
	$userID = $wpdb->get_var($wpdb->prepare("SELECT `user_id` FROM `{$wpdb->prefix}usermeta` WHERE `meta_key` LIKE 'wpcargo_api' AND `meta_value` = '%s'", $str));
	return $userID;
}
function wpcapi_display_user_name($userID)
{
	$user_info = get_userdata($userID);
	$display_name = $user_info->display_name;
	if ($user_info->last_name && $user_info->first_name) {
		$display_name = $user_info->first_name . ' ' . $user_info->last_name;
	}
	return $display_name;
}
function wpcapi_get_user_api($userID)
{
	return get_user_meta($userID, 'wpcargo_api', true);
}
function wpcapi_update_user_api($userID)
{
	$api_key 	= wpcapi_generate_api_key();
	$api_reset 	=  update_user_meta($userID, 'wpcargo_api', $api_key);
	if ($api_reset) {
		$api_reset = wpcapi_get_user_api($userID);
	}
	return $api_reset;
}
function wpcapi_get_agents()
{
	$wpc_agent_args   	= array('role' => 'cargo_agent', 'orderby' => 'user_nicename', 'order' => 'ASC');
	$wpc_agents     	= get_users($wpc_agent_args);
	$reg_agents 		= array();
	if (!empty($wpc_agents)) {
		foreach ($wpc_agents as $agent) {
			$reg_agents[$agent->ID] = wpcapi_display_user_name($agent->ID);
		}
	}
	return $reg_agents;
}
function wpcapi_get_apikey_user($apikey = '')
{
	global $wpdb;
	$sql = "SELECT `user_id` FROM `{$wpdb->prefix}usermeta` WHERE `meta_key` LIKE 'wpcargo_api' AND `meta_value` LIKE '{$apikey}' LIMIT 1";
	$result = $wpdb->get_var($sql);
	return $result;
}
function wpcapi_get_user_shipments($userID, $page = 1, $all = false, $status = '')
{
	global $wpdb;
	$userdata 	= get_userdata($userID);
	$user_roles = $userdata->roles;
	$per_page 	= 1000;
	$offset 	= ($page - 1) * $per_page;
	$sql 		= '';
	$admin_role = array('administrator', 'wpcargo_api_manager', 'wpc_shipment_manager');



	if (array_intersect($admin_role, $user_roles)) {
		if (!empty($status)) {
			$sql .= "SELECT tbl1.ID, tbl1.post_author, tbl1.post_date, tbl1.post_date_gmt, tbl1.post_title, tbl1.post_name, tbl1.post_modified, tbl1.post_modified_gmt";
			$sql .= " FROM `{$wpdb->prefix}posts` AS tbl1";
			$sql .= " LEFT JOIN `{$wpdb->prefix}postmeta` AS tbl2 ON tbl1.ID = tbl2.post_id";
			$sql .= " WHERE tbl1.post_status LIKE 'publish' AND tbl1.post_type LIKE 'wpcargo_shipment'";
			$sql .= " AND tbl2.meta_key LIKE 'wpcargo_status' AND tbl2.meta_value LIKE %s";
			$sql .= " ORDER BY `post_date`";
			if (!$all) {
				$sql .= " DESC LIMIT {$per_page} OFFSET {$offset}";
			}
			return $wpdb->get_results($wpdb->prepare($sql, $status), ARRAY_A);
		} else {
			$sql .= "SELECT `ID`, `post_author`, `post_date`, `post_date_gmt`, `post_title`, `post_name`, `post_modified`, `post_modified_gmt` FROM `{$wpdb->prefix}posts` WHERE `post_status` LIKE 'publish' AND `post_type` LIKE 'wpcargo_shipment' ORDER BY `post_date`";
			if (!$all) {
				$sql .= " DESC LIMIT {$per_page} OFFSET {$offset}";
			}
			return $wpdb->get_results($sql, ARRAY_A);
		}
	} elseif (in_array('wpcargo_driver', $user_roles)) {
		if (!empty($status)) {
			$sql .= "SELECT tbl1.ID, tbl1.post_author, tbl1.post_date, tbl1.post_date_gmt, tbl1.post_title, tbl1.post_name, tbl1.post_modified, tbl1.post_modified_gmt";
			$sql .= " FROM `{$wpdb->prefix}posts` tbl1";
			$sql .= " INNER JOIN `{$wpdb->prefix}postmeta` tbl2 ON tbl1.ID = tbl2.post_id";
			$sql .= " INNER JOIN `{$wpdb->prefix}postmeta` tbl3 ON tbl1.ID = tbl3.post_id";
			$sql .= " WHERE tbl1.post_status LIKE 'publish' AND tbl1.post_type LIKE 'wpcargo_shipment'";
			$sql .= " AND tbl2.meta_key LIKE 'wpcargo_driver' AND tbl2.meta_value LIKE %d";
			$sql .= " AND tbl3.meta_key LIKE 'wpcargo_status' AND tbl3.meta_value LIKE %s";
			$sql .= " ORDER BY tbl1.post_date";
			if (!$all) {
				$sql .= " DESC LIMIT {$per_page} OFFSET {$offset}";
			}
			return $wpdb->get_results($wpdb->prepare($sql, $userID, $status), ARRAY_A);
		} else {
			$sql .= "SELECT tbl1.ID, tbl1.post_author, tbl1.post_date, tbl1.post_date_gmt, tbl1.post_title, tbl1.post_name, tbl1.post_modified, tbl1.post_modified_gmt  FROM `{$wpdb->prefix}posts` tbl1 INNER JOIN `{$wpdb->prefix}postmeta` tbl2 ON tbl1.ID = tbl2.post_id WHERE tbl1.post_status LIKE 'publish' AND tbl1.post_type LIKE 'wpcargo_shipment' AND tbl2.meta_key LIKE 'wpcargo_driver' AND tbl2.meta_value LIKE %d ORDER BY tbl1.post_date";
			if (!$all) {
				$sql .= " DESC LIMIT {$per_page} OFFSET {$offset}";
			}
			return $wpdb->get_results($wpdb->prepare($sql, $userID), ARRAY_A);
		}
	} elseif (in_array('cargo_agent', $user_roles)) {
		if (!empty($status)) {
			$sql .= "SELECT tbl1.ID, tbl1.post_author, tbl1.post_date, tbl1.post_date_gmt, tbl1.post_title, tbl1.post_name, tbl1.post_modified, tbl1.post_modified_gmt";
			$sql .= " FROM `{$wpdb->prefix}posts` tbl1";
			$sql .= " INNER JOIN `{$wpdb->prefix}postmeta` tbl2 ON tbl1.ID = tbl2.post_id";
			$sql .= " INNER JOIN `{$wpdb->prefix}postmeta` tbl3 ON tbl1.ID = tbl3.post_id";
			$sql .= " WHERE tbl1.post_status LIKE 'publish' AND tbl1.post_type LIKE 'wpcargo_shipment'";
			$sql .= " AND tbl2.meta_key LIKE 'agent_fields' AND tbl2.meta_value LIKE %d";
			$sql .= " AND tbl3.meta_key LIKE 'wpcargo_status' AND tbl3.meta_value LIKE %s";
			$sql .= " ORDER BY tbl1.post_date";
			if (!$all) {
				$sql .= " DESC LIMIT {$per_page} OFFSET {$offset}";
			}
			return $wpdb->get_results($wpdb->prepare($sql, $userID, $status), ARRAY_A);
		} else {
			$sql .= "SELECT tbl1.ID, tbl1.post_author, tbl1.post_date, tbl1.post_date_gmt, tbl1.post_title, tbl1.post_name, tbl1.post_modified, tbl1.post_modified_gmt  FROM `{$wpdb->prefix}posts` tbl1 INNER JOIN `{$wpdb->prefix}postmeta` tbl2 ON tbl1.ID = tbl2.post_id WHERE tbl1.post_status LIKE 'publish' AND tbl1.post_type LIKE 'wpcargo_shipment' AND tbl2.meta_key LIKE 'agent_fields' AND tbl2.meta_value LIKE %d ORDER BY tbl1.post_date";
			if (!$all) {
				$sql .= " DESC LIMIT {$per_page} OFFSET {$offset}";
			}
			return $wpdb->get_results($wpdb->prepare($sql, $userID), ARRAY_A);
		}
	} elseif (in_array('wpcargo_client', $user_roles)) {
		if (!empty($status)) {
			$sql .= "SELECT tbl1.ID, tbl1.post_author, tbl1.post_date, tbl1.post_date_gmt, tbl1.post_title, tbl1.post_name, tbl1.post_modified, tbl1.post_modified_gmt";
			$sql .= " FROM `{$wpdb->prefix}posts` tbl1";
			$sql .= " INNER JOIN `{$wpdb->prefix}postmeta` tbl2 ON tbl1.ID = tbl2.post_id";
			$sql .= " INNER JOIN `{$wpdb->prefix}postmeta` tbl3 ON tbl1.ID = tbl3.post_id";
			$sql .= " WHERE tbl1.post_status LIKE 'publish' AND tbl1.post_type LIKE 'wpcargo_shipment'";
			$sql .= " AND tbl2.meta_key LIKE 'registered_shipper' AND ( tbl2.meta_value LIKE %d OR tbl1.post_author LIKE %d )";
			$sql .= " AND tbl3.meta_key LIKE 'wpcargo_status' AND tbl3.meta_value LIKE %s";
			$sql .= " ORDER BY tbl1.post_date";
			if (!$all) {
				$sql .= " DESC LIMIT {$per_page} OFFSET {$offset}";
			}
			return $wpdb->get_results($wpdb->prepare($sql, $userID, $userID, $status), ARRAY_A);
		} else {
			$sql .= "SELECT tbl1.ID, tbl1.post_author, tbl1.post_date, tbl1.post_date_gmt, tbl1.post_title, tbl1.post_name, tbl1.post_modified, tbl1.post_modified_gmt FROM `{$wpdb->prefix}posts` tbl1 INNER JOIN `{$wpdb->prefix}postmeta` tbl2 ON tbl1.ID = tbl2.post_id WHERE tbl1.post_status LIKE 'publish' AND tbl1.post_type LIKE 'wpcargo_shipment' AND tbl2.meta_key LIKE 'registered_shipper' AND ( tbl2.meta_value LIKE %d OR tbl1.post_author LIKE %d ) ORDER BY tbl1.post_date";
			if (!$all) {
				$sql .= " DESC LIMIT {$per_page} OFFSET {$offset}";
			}
			return $wpdb->get_results($wpdb->prepare($sql, $userID, $userID), ARRAY_A);
		}
	}
	return NULL;
}
function wpcapi_get_shipment_id($shipment_number)
{
	global $wpdb;
	$result = $wpdb->get_var("SELECT `ID` FROM `{$wpdb->prefix}posts` WHERE `post_status` LIKE 'publish' AND `post_type` LIKE 'wpcargo_shipment' AND  `post_title` LIKE '" . $shipment_number . "' LIMIT 1");
	return $result;
}
function wpcapi_get_shipment_history($shipment_id)
{
	$shipment_history     = maybe_unserialize(get_post_meta($shipment_id, 'wpcargo_shipments_update', true));
	$history              = array();
	if (!empty($shipment_history)) {
		foreach ($shipment_history as $_history) {
			$row = [];
			if (!empty(wpcargo_history_fields())) {
				foreach (wpcargo_history_fields() as $key => $value) {
					$value = '';
					if (array_key_exists($key, $_history)) {
						$value = $_history[$key];
					}
					$row[$key] = $value;
				}
			}
			$history[] = $row;
		}
	}
	return $history;
}
function wpcapi_extract_shipment_data($shipments)
{
	global $wpcargo;
	$shipment_data = array();
	$counter = 0;
	foreach ($shipments as $key => $value) {
		$registered_shipper   = get_post_meta($value['ID'], 'registered_shipper', true);
		$registered_receiver  = get_post_meta($value['ID'], 'registered_receiver', true);
		$wpcargo_driver       = get_post_meta($value['ID'], 'wpcargo_driver', true);
		$wpcargo_agent       	= get_post_meta($value['ID'], 'agent_fields', true);
		$status       		= get_post_meta($value['ID'], 'wpcargo_status', true);
		$history              = wpcapi_get_shipment_history($value['ID']);
		$shipment_data[$counter]['ID']            = $value['ID'];
		$shipment_data[$counter]['post_author']   = $value['post_author'];
		$shipment_data[$counter]['post_date']     = $value['post_date'];
		$shipment_data[$counter]['post_date_gmt'] = $value['post_date_gmt'];
		$shipment_data[$counter]['post_title']    = $value['post_title'];
		$shipment_data[$counter]['post_name']     = $value['post_name'];
		$shipment_data[$counter]['post_modified'] = $value['post_modified'];
		$shipment_data[$counter]['post_modified_gmt']   = $value['post_modified_gmt'];
		$shipment_data[$counter]['registered_shipper']  = $wpcargo->user_fullname($registered_shipper);
		$shipment_data[$counter]['registered_receiver'] = $wpcargo->user_fullname($registered_receiver);
		$shipment_data[$counter]['wpcargo_agent']       = $wpcargo->user_fullname($wpcargo_agent);
		$shipment_data[$counter]['wpcargo_driver']      = $wpcargo->user_fullname($wpcargo_driver);
		$shipment_data[$counter]['status']   			  = $status;
		$shipment_data[$counter]['shipment_history']    = $history;
		if (!empty(wpcapi_get_registered_metakeys())) {
			foreach (wpcapi_get_registered_metakeys() as $metakey => $field_info) {
				$metavalue  = maybe_unserialize(get_post_meta($value['ID'], $metakey, true));
				$shipment_data[$counter][$metakey] = $metavalue;
			}
		}
		$shipment_images  = maybe_unserialize(get_post_meta($value['ID'], 'shipment_images', true));
		$shipment_data[$counter]['shipment_images'] = $shipment_images;
		$shipment_data[$counter]['shipment_packages'] = wpcargo_get_package_data($value['ID']);
		$shipment_data[$counter] = apply_filters('wpcargo_api_shipment_data', $shipment_data[$counter], $value['ID']);
		$counter++;
	}
	return apply_filters('wpcargo_api_shipments_data', $shipment_data);
}
function wpcapi_upload_shipment_images($shipmentID, $uploaded_files)
{
	$upload_errors  = array();
	$attachments_url = array();
	$allowed_file   = array('image/png', 'image/jpeg', 'image/jpg');
	$phpFileUploadErrors = array(
		0 => wpcapi_phperror_0_label(),
		1 => wpcapi_phperror_1_label(),
		2 => wpcapi_phperror_2_label(),
		3 => wpcapi_phperror_3_label(),
		4 => wpcapi_phperror_4_label(),
		6 => wpcapi_phperror_5_label(),
		7 => wpcapi_phperror_6_label(),
		8 => wpcapi_phperror_7_label(),
	);
	if (isset($uploaded_files['shipment_images'])) {
		if (count($uploaded_files['shipment_images']) == 1) {
			if ($uploaded_files['shipment_images']['error'] > 0) {
				$upload_errors[] = $phpFileUploadErrors[$uploaded_files['shipment_images']['error']];
			} else {
				if (in_array($uploaded_files['shipment_images']['type'], $allowed_file)) {
					//$attachment[] = $uploaded_files['shipment_images'];
					$file = $uploaded_files['shipment_images']['tmp_name'];
					$filename = basename($uploaded_files['shipment_images']['name']);
					$upload_file = wp_upload_bits($filename, null, file_get_contents($file));
					if (!$upload_file['error']) {
						$wp_filetype = wp_check_filetype($filename, null);
						$attachment = array(
							'post_mime_type' => $wp_filetype['type'],
							'post_title' => preg_replace('/\.[^.]+$/', '', $filename),
							'post_content' => '',
							'post_status' => 'inherit'
						);
						$attachment_id = wp_insert_attachment($attachment, $upload_file['file'], $shipmentID);
						if (!is_wp_error($attachment_id)) {
							require_once(ABSPATH . "wp-admin" . '/includes/image.php');
							$attachment_data = wp_generate_attachment_metadata($attachment_id, $upload_file['file']);
							wp_update_attachment_metadata($attachment_id,  $attachment_data);
							$attachments_url[] = wp_get_attachment_url($attachment_id);
							// added by mayur
							$old = get_post_meta($shipmentID, 'wpcargo-pod-image');
							if ($old) {
								update_post_meta($shipmentID, 'wpcargo-pod-image', $old . ',' . $attachment_id);
							} else {
								update_post_meta($shipmentID, 'wpcargo-pod-image',  $attachment_id);
							}
						}
					}
				} else {
					$fileError = array(
						'filename' => $uploaded_files['shipment_images']['name'],
						'error'    => wpcapi_image_error_message()
					);
					$upload_errors[] = $fileError;
				}
			}
		} else {
			for ($i = 0; $i < count($uploaded_files['shipment_images']['name']); $i++) {
				if ($uploaded_files['shipment_images']['error'][$i] > 0) {
					$upload_errors[] = $phpFileUploadErrors[$uploaded_files['shipment_images']['error'][$i]];
				} else {
					if (in_array($uploaded_files['shipment_images']['type'][$i], $allowed_file)) {
						//$attachment[] = $uploaded_files['shipment_images'];
						$file         = $uploaded_files['shipment_images']['tmp_name'][$i];
						$filename     = basename($uploaded_files['shipment_images']['name'][$i]);
						$upload_file  = wp_upload_bits($filename, null, file_get_contents($file));
						if (!$upload_file['error']) {
							$wp_filetype = wp_check_filetype($filename, null);
							$attachment = array(
								'post_mime_type' => $wp_filetype['type'],
								'post_title' => preg_replace('/\.[^.]+$/', '', $filename),
								'post_content' => '',
								'post_status' => 'inherit'
							);
							$attachment_id = wp_insert_attachment($attachment, $upload_file['file'], $shipmentID);
							if (!is_wp_error($attachment_id)) {
								require_once(ABSPATH . "wp-admin" . '/includes/image.php');
								$attachment_data = wp_generate_attachment_metadata($attachment_id, $upload_file['file']);
								wp_update_attachment_metadata($attachment_id,  $attachment_data);
								$attachments_url[] = wp_get_attachment_url($attachment_id);

								// added by mayur

								$old = get_post_meta($shipmentID, 'wpcargo-pod-image');
								if ($old) {
									update_post_meta($shipmentID, 'wpcargo-pod-image', $old . ',' . $attachment_id);
								} else {
									update_post_meta($shipmentID, 'wpcargo-pod-image',  $attachment_id);
								}
							}
						}
					} else {
						$fileError = array(
							'filename' => $uploaded_files['shipment_images']['name'][$i],
							'error'    => wpcapi_image_error_message()
						);
						$upload_errors[] = $fileError;
					}
				}
			}
		}
	}

	return array(
		'upload_errors' => $upload_errors,
		'attachments_url' => $attachments_url
	);
}
function wpcapi_get_user_shipment_total($userID)
{
	global $wpdb;
	$userdata 	= get_userdata($userID);
	$user_roles = $userdata->roles;
	$sql 		= '';
	$admin_role = array('administrator', 'wpcargo_api_manager', 'wpc_shipment_manager');
	if (array_intersect($admin_role, $user_roles)) {

		$sql .= "SELECT count(*) FROM `{$wpdb->prefix}posts` WHERE `post_status` LIKE 'publish' AND `post_type` LIKE 'wpcargo_shipment'";
	} elseif (in_array('wpcargo_driver', $user_roles)) {

		$sql .= "SELECT count( * ) FROM `{$wpdb->prefix}posts` tbl1 INNER JOIN `{$wpdb->prefix}postmeta` tbl2 ON tbl1.ID = tbl2.post_id WHERE tbl1.post_status LIKE 'publish' AND tbl1.post_type LIKE 'wpcargo_shipment' AND tbl2.meta_key LIKE 'wpcargo_driver' AND tbl2.meta_value LIKE '{$userID}'";
	} elseif (in_array('cargo_agent', $user_roles)) {

		$sql .= "SELECT count( * ) FROM `{$wpdb->prefix}posts` tbl1 INNER JOIN `{$wpdb->prefix}postmeta` tbl2 ON tbl1.ID = tbl2.post_id WHERE tbl1.post_status LIKE 'publish' AND tbl1.post_type LIKE 'wpcargo_shipment' AND tbl2.meta_key LIKE 'agent_fields' AND tbl2.meta_value LIKE '{$userID}' ORDER BY tbl1.post_date";
	} elseif (in_array('wpcargo_client', $user_roles)) {

		$sql .= "SELECT count( * ) FROM `{$wpdb->prefix}posts` tbl1 INNER JOIN `{$wpdb->prefix}postmeta` tbl2 ON tbl1.ID = tbl2.post_id WHERE tbl1.post_status LIKE 'publish' AND tbl1.post_type LIKE 'wpcargo_shipment' AND tbl2.meta_key LIKE 'registered_shipper' AND ( tbl2.meta_value LIKE {$userID} OR tbl1.post_author LIKE {$userID} ) ORDER BY tbl1.post_date";
	}
	$shipments = $wpdb->get_var($sql);
	return $shipments;
}
function wpcapi_get_user_address_total($userID, $book)
{
	global $wpdb;
	$userdata 	= get_userdata($userID);
	$user_roles = $userdata->roles;
	$sql 		= '';
	$admin_role = array('administrator', 'wpcargo_api_manager', 'wpc_shipment_manager');
	if (array_intersect($admin_role, $user_roles)) {

		$sql .= "SELECT count( * ) FROM `{$wpdb->prefix}posts` as tbl1 INNER JOIN `{$wpdb->prefix}postmeta` as tbl2 ON tbl1.ID = tbl2.post_id WHERE tbl1.post_status LIKE 'publish' AND ( tbl1.post_type LIKE 'wpc_address_book' OR tbl1.post_type LIKE 'pq_book_address' ) AND tbl2.meta_key LIKE 'book' AND tbl2.meta_value LIKE '{$book}' ORDER BY tbl1.post_date";
	} else {

		$sql .= "SELECT count( * ) FROM `{$wpdb->prefix}posts` as tbl1 INNER JOIN `{$wpdb->prefix}postmeta` as tbl2 ON tbl1.ID = tbl2.post_id WHERE tbl1.post_status LIKE 'publish' AND tbl1.post_author = {$userID} AND ( tbl1.post_type LIKE 'wpc_address_book' OR tbl1.post_type LIKE 'pq_book_address' ) AND tbl2.meta_key LIKE 'book' AND tbl2.meta_value LIKE '{$book}' ORDER BY tbl1.post_date";
	}
	$addresses = $wpdb->get_var($sql);
	return $addresses;
}

function wpcapi_get_user_address($userID, $page = 1, $book = 'shipper', $all = false)
{
	global $wpdb;
	$userdata 	= get_userdata($userID);
	$user_roles = $userdata->roles;
	$per_page 	= 1000;
	$offset 	= ($page - 1) * $per_page;
	$sql 		= '';
	$admin_role = array('administrator', 'wpcargo_api_manager', 'wpc_shipment_manager');
	if (array_intersect($admin_role, $user_roles)) {
		$sql .= "SELECT `ID` FROM `{$wpdb->prefix}posts` as tbl1 INNER JOIN `{$wpdb->prefix}postmeta` as tbl2 ON tbl1.ID = tbl2.post_id WHERE tbl1.post_status LIKE 'publish' AND ( tbl1.post_type LIKE 'wpc_address_book' OR tbl1.post_type LIKE 'pq_book_address' ) AND tbl2.meta_key LIKE 'book' AND tbl2.meta_value LIKE '{$book}' ORDER BY tbl1.post_date";
	} else {
		$sql .= "SELECT `ID` FROM `{$wpdb->prefix}posts` as tbl1 INNER JOIN `{$wpdb->prefix}postmeta` as tbl2 ON tbl1.ID = tbl2.post_id WHERE tbl1.post_status LIKE 'publish' AND tbl1.post_author = {$userID} AND ( tbl1.post_type LIKE 'wpc_address_book' OR tbl1.post_type LIKE 'pq_book_address' ) AND tbl2.meta_key LIKE 'book' AND tbl2.meta_value LIKE '{$book}' ORDER BY tbl1.post_date";
	}
	if (!$all) {
		$sql .= " DESC LIMIT {$per_page} OFFSET {$offset}";
	}
	$addresses = $wpdb->get_col($sql);
	return $addresses;
}
function wpcapi_get_user_shipment_page_number($userID)
{
	$total_items = wpcapi_get_user_shipment_total($userID);
	$per_page 	 = 1000;
	return ceil($total_items / $per_page);
}

function wpcapi_get_user_address_page_number($userID, $book)
{
	$total_items = wpcapi_get_user_address_total($userID, $book);
	$per_page 	 = 1000;
	return ceil($total_items / $per_page);
}

function wpcapi_can_user_update_address($userID, $addressID, $book)
{
	global $wpdb;
	$userdata 	= get_userdata($userID);
	$user_roles = $userdata->roles;
	$access     = false;
	$admin_role = array('administrator', 'wpcargo_api_manager', 'wpc_shipment_manager');
	if (array_intersect($admin_role, $user_roles)) {
		$sql = "SELECT `ID` FROM `{$wpdb->prefix}posts` AS tbl1 INNER JOIN `{$wpdb->prefix}postmeta` AS tbl2 ON tbl1.ID = tbl2.post_id WHERE tbl1.post_status LIKE 'publish' AND ( tbl1.post_type LIKE 'wpc_address_book' OR tbl1.post_type LIKE 'pq_book_address' ) AND  tbl1.ID = {$addressID} AND tbl2.meta_key LIKE 'book' AND tbl2.meta_value LIKE '{$book}'";
		$access = $wpdb->get_var($sql);
	} else {
		$sql = "SELECT `ID` FROM `{$wpdb->prefix}posts` AS tbl1 INNER JOIN `{$wpdb->prefix}postmeta` AS tbl2 ON tbl1.ID = tbl2.post_id WHERE tbl1.post_status LIKE 'publish' AND tbl1.post_author = {$userID} AND ( tbl1.post_type LIKE 'wpc_address_book' OR tbl1.post_type LIKE 'pq_book_address' ) AND  tbl1.ID = {$addressID} AND tbl2.meta_key LIKE 'book' AND tbl2.meta_value LIKE '{$book}'";
		$access = $wpdb->get_var($sql);
	}

	return $access;
}


function wpcapi_default_metakeys($formatted = false)
{
	global $wpcargo;
	$wpcargo_option_settings 	= get_option('wpcargo_option_settings');
	$shipment_type 				= array();
	$shipment_mode 				= array();
	$shipment_country 			= array();
	$shipment_carrier 			= array();
	$shipment_payment 			= array();
	if ($wpcargo_option_settings) {
		if (array_key_exists('settings_shipment_type', $wpcargo_option_settings)) {
			$shipment_type 	= $wpcargo_option_settings['settings_shipment_type'];
			$shipment_type	= array_map('trim', explode(",", $shipment_type));
		}
		if (array_key_exists('settings_shipment_wpcargo_mode', $wpcargo_option_settings)) {
			$shipment_mode 	= $wpcargo_option_settings['settings_shipment_wpcargo_mode'];
			$shipment_mode	= array_map('trim', explode(",", $shipment_mode));
		}
		if (array_key_exists('settings_shipment_country', $wpcargo_option_settings)) {
			$shipment_country 	= $wpcargo_option_settings['settings_shipment_country'];
			$shipment_country	= array_map('trim', explode(",", $shipment_country));
		}
		if (array_key_exists('settings_shipment_wpcargo_carrier', $wpcargo_option_settings)) {
			$shipment_carrier 	= $wpcargo_option_settings['settings_shipment_wpcargo_carrier'];
			$shipment_carrier	= array_map('trim', explode(",", $shipment_carrier));
		}
		if (array_key_exists('settings_shipment_wpcargo_payment_mode', $wpcargo_option_settings)) {
			$shipment_payment 	= $wpcargo_option_settings['settings_shipment_wpcargo_payment_mode'];
			$shipment_payment	= array_map('trim', explode(",", $shipment_payment));
		}
	}
	$wpcargo_meta_data = array(
		'wpcargo_shipper_name' => array(
			'field_type' 	=> 'text',
			'options' 		=> null
		),
		'wpcargo_shipper_phone' => array(
			'field_type' 	=> 'text',
			'options' 		=> null
		),
		'wpcargo_shipper_address' => array(
			'field_type' 	=> 'text',
			'options' 		=> null
		),
		'wpcargo_shipper_email' => array(
			'field_type' 	=> 'email',
			'options' 		=> null
		),
		'wpcargo_receiver_name' => array(
			'field_type' 	=> 'text',
			'options' 		=> null
		),
		'wpcargo_receiver_phone' => array(
			'field_type' 	=> 'text',
			'options' 		=> null
		),
		'wpcargo_receiver_address' => array(
			'field_type' 	=> 'text',
			'options' 		=> null
		),
		'wpcargo_receiver_email' => array(
			'field_type' 	=> 'email',
			'options' 		=> null
		),
		'agent_fields' => array(
			'field_type' 	=> 'select',
			'options' 		=> wpcapi_get_agents()
		),
		'wpcargo_type_of_shipment' => array(
			'field_type' 	=> 'select',
			'options' 		=> $shipment_type
		),
		'wpcargo_courier' => array(
			'field_type' 	=> 'text',
			'options' 		=> null
		),
		'wpcargo_mode_field' => array(
			'field_type' 	=> 'select',
			'options' 		=> $shipment_mode
		),
		'wpcargo_qty' => array(
			'field_type' 	=> 'text',
			'options' 		=> null
		),
		'wpcargo_total_freight' => array(
			'field_type' 	=> 'text',
			'options' 		=> null
		),
		'wpcargo_carrier_ref_number' => array(
			'field_type' 	=> 'text',
			'options' 		=> null
		),
		'wpcargo_origin_field' => array(
			'field_type' 	=> 'select',
			'options' 		=> $shipment_country
		),
		'wpcargo_pickup_date_picker' => array(
			'field_type' 	=> 'text',
			'options' 		=> null
		),
		'wpcargo_status' => array(
			'field_type' 	=> 'select',
			'options' 		=> $wpcargo->status
		),
		'wpcargo_comments' => array(
			'field_type' 	=> 'text',
			'options' 		=> null
		),
		'wpcargo_weight' => array(
			'field_type' 	=> 'text',
			'options' 		=> null
		),
		'wpcargo_packages' => array(
			'field_type' 	=> 'text',
			'options' 		=> null
		),
		'wpcargo_product' => array(
			'field_type' 	=> 'text',
			'options' 		=> null
		),
		'payment_wpcargo_mode_field' => array(
			'field_type' 	=> 'select',
			'options' 		=> $shipment_payment
		),
		'wpcargo_carrier_field' => array(
			'field_type' 	=> 'select',
			'options' 		=> $shipment_carrier
		),
		'wpcargo_departure_time_picker' => array(
			'field_type' 	=> 'text',
			'options' 		=> null
		),
		'wpcargo_destination' => array(
			'field_type' 	=> 'select',
			'options' 		=> $shipment_country
		),
		'wpcargo_pickup_time_picker' => array(
			'field_type' 	=> 'text',
			'options' 		=> null
		),
		'wpcargo_expected_delivery_date_picker' => array(
			'field_type' 	=> 'text',
			'options' 		=> null
		),
	);
	if ($formatted) {
		$wpcargo_meta_data = array(
			'shipper_info'	=> array(
				'wpcargo_shipper_name' => array(
					'field_type' 	=> 'text',
					'options' 		=> null
				),
				'wpcargo_shipper_phone' => array(
					'field_type' 	=> 'text',
					'options' 		=> null
				),
				'wpcargo_shipper_address' => array(
					'field_type' 	=> 'text',
					'options' 		=> null
				),
				'wpcargo_shipper_email' => array(
					'field_type' 	=> 'email',
					'options' 		=> null
				)
			),
			'receiver_info'	=> array(
				'wpcargo_receiver_name' => array(
					'field_type' 	=> 'text',
					'options' 		=> null
				),
				'wpcargo_receiver_phone' => array(
					'field_type' 	=> 'text',
					'options' 		=> null
				),
				'wpcargo_receiver_address' => array(
					'field_type' 	=> 'text',
					'options' 		=> null
				),
				'wpcargo_receiver_email' => array(
					'field_type' 	=> 'email',
					'options' 		=> null
				)
			),
			'delivery_info'	=> array(
				'agent_fields' => array(
					'field_type' 	=> 'select',
					'options' 		=> wpcapi_get_agents()
				),
				'wpcargo_type_of_shipment' => array(
					'field_type' 	=> 'select',
					'options' 		=> $shipment_type
				),
				'wpcargo_courier' => array(
					'field_type' 	=> 'text',
					'options' 		=> null
				),
				'wpcargo_mode_field' => array(
					'field_type' 	=> 'select',
					'options' 		=> $shipment_mode
				),
				'wpcargo_qty' => array(
					'field_type' 	=> 'text',
					'options' 		=> null
				),
				'wpcargo_total_freight' => array(
					'field_type' 	=> 'text',
					'options' 		=> null
				),
				'wpcargo_carrier_ref_number' => array(
					'field_type' 	=> 'text',
					'options' 		=> null
				),
				'wpcargo_origin_field' => array(
					'field_type' 	=> 'select',
					'options' 		=> $shipment_country
				),
				'wpcargo_pickup_date_picker' => array(
					'field_type' 	=> 'text',
					'options' 		=> null
				),
				'wpcargo_status' => array(
					'field_type' 	=> 'select',
					'options' 		=> $wpcargo->status
				),
				'wpcargo_comments' => array(
					'field_type' 	=> 'text',
					'options' 		=> null
				),
				'wpcargo_weight' => array(
					'field_type' 	=> 'text',
					'options' 		=> null
				),
				'wpcargo_packages' => array(
					'field_type' 	=> 'text',
					'options' 		=> null
				),
				'wpcargo_product' => array(
					'field_type' 	=> 'text',
					'options' 		=> null
				),
				'payment_wpcargo_mode_field' => array(
					'field_type' 	=> 'select',
					'options' 		=> $shipment_payment
				),
				'wpcargo_carrier_field' => array(
					'field_type' 	=> 'select',
					'options' 		=> $shipment_carrier
				),
				'wpcargo_departure_time_picker' => array(
					'field_type' 	=> 'text',
					'options' 		=> null
				),
				'wpcargo_destination' => array(
					'field_type' 	=> 'select',
					'options' 		=> $shipment_country
				),
				'wpcargo_pickup_time_picker' => array(
					'field_type' 	=> 'text',
					'options' 		=> null
				),
				'wpcargo_expected_delivery_date_picker' => array(
					'field_type' 	=> 'text',
					'options' 		=> null
				),
			)
		);
	}
	return  $wpcargo_meta_data;
}
function wpcapi_get_sections()
{
	global $wpdb;
	$table_prefix = $wpdb->prefix;
	$field_keys 	= $wpdb->get_col('SELECT `section` FROM `' . $table_prefix . 'wpcargo_custom_fields` GROUP BY `section` ORDER BY `id`');
	return $field_keys;
}
function wpcapi_get_section_fields($section)
{
	global $wpdb;
	$table_prefix = $wpdb->prefix;
	$field_keys 	= $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}wpcargo_custom_fields` WHERE `section` LIKE '{$section}'", ARRAY_A);
	return $field_keys;
}
function wpcapi_customfield_metakeys()
{
	$fields = array();
	if (!empty(wpcapi_get_sections())) {
		foreach (wpcapi_get_sections() as $section) {
			if (!empty(wpcapi_get_section_fields($section))) {
				$counter = 0;
				foreach (wpcapi_get_section_fields($section) as $_section) {
					$_section_fields = array();
					foreach ($_section as $key => $value) {
						$_section_fields[$key] = maybe_unserialize($value);
					}
					$fields[$section][$counter] = $_section_fields;
					$counter++;
				}
			}
		}
	}
	return $fields;
}
function wpcapi_get_formatted_registered_metakeys()
{
	$metakeys = array();
	if (class_exists('WPCargo_CF_Form_Builder')) {
		$metakeys = wpcapi_customfield_metakeys();
	} else {
		$metakeys = wpcapi_default_metakeys(true);
	}
	$metakeys = apply_filters('wpcapi_shipment_metakeys', $metakeys);
	return $metakeys;
}
function wpcapi_get_registered_metakeys()
{
	$metakeys = array();
	if (class_exists('WPCargo_CF_Form_Builder')) {
		foreach (wpccf_get_all_fields() as $fields) {
			$metakeys[$fields['field_key']] = array(
				'field_type' => $fields['field_type'],
				'options' => maybe_unserialize($fields['field_data'])
			);
		}
	} else {
		$metakeys = wpcapi_default_metakeys();
	}
	$metakeys = apply_filters('wpcapi_shipment_metakeys', $metakeys);
	return $metakeys;
}
function wpcapi_generate_api_key()
{
	$api_key = wp_generate_password(26, false);
	if (wpcapi_exist($api_key)) {
		$api_key = wpcapi_generate_api_key();
	}
	return $api_key;
}
/*
** AJAX Handler
*/
function wpcapi_generate_wpc_api_callback()
{
	$api_key = wpcapi_generate_api_key();
	echo $api_key;
	wp_die();
}
add_action('wp_ajax_generate_wpcapi', 'wpcapi_generate_wpc_api_callback');
function wpcapi_reset_wpc_api_callback()
{
	$user_api 		= wpcapi_update_user_api(get_current_user_id());
	echo $user_api;
	wp_die();
}
add_action('wp_ajax_reset_wpcapi', 'wpcapi_reset_wpc_api_callback');

function wpcapi_user_registration_generate_api_callback($user_id)
{
	$wpcargo_api = get_user_meta($user_id, 'wpcargo_api', true);
	if (!$wpcargo_api && function_exists('wpcapi_generate_api_key')) {
		$api_key = wpcapi_generate_api_key();
		update_user_meta($user_id, 'wpcargo_api', $api_key);
	}
}
add_action('user_register', 'wpcapi_user_registration_generate_api_callback', 10, 1);
/*
 * Register wpcargo_api meta
 */
register_meta('user', 'wpcargo_api', array(
	"type" 			=> "string",
	"show_in_rest" 	=> true
));
/*
 * Language Translation for the Ecncrypted Files
 */
function wpcapi_username_required_message()
{
	return __("Username field 'username' is required.", 'wpcargo-api');
}
function wpcapi_username_exist_message()
{
	return __("Username is already exists, please try another username", 'wpcargo-api');
}
function wpcapi_email_required_message()
{
	return __("Email field 'email' is required.", 'wpcargo-api');
}
function wpcapi_email_exist_message()
{
	return __("Email is already exists, please try another email", 'wpcargo-api');
}
function wpcapi_password_required_message()
{
	return __("Password field 'password' is required.", 'wpcargo-api');
}
function wpcapi_activate_license_message()
{
	return __('Warning! Please activate license for WPCargo API add on to be able to use WPCargo API', 'wpcargo-api') . ' <a href="' . admin_url() . 'admin.php?page=wptaskforce-helper" title="WPCargo license page">' . __('here', 'wpcargo-api') . '</a>.';
}
function wpcapi_permission_error_message()
{
	return esc_html__('You do not have permissions to view this data please purchase license.', 'wpcargo-api');
}
function wpcapi_data_permission_error_message()
{
	return esc_html__('You do not have permissions to view this data.', 'wpcargo-api');
}
function wpcapi_route_permission_error_message()
{
	return esc_html__('No route was found matching the URL and request method.', 'wpcargo-api');
}
function wpcapi_permission_missing_message()
{
	return esc_html__('You do not have permissions to view this data. Something is missing.', 'wpcargo-api');
}
function wpcapi_access_denied_message()
{
	return __('Access Denied!', 'wpcargo-api');
}
function wpcapi_access_granted_message()
{
	return __('Access Granted!', 'wpcargo-api');
}
function wpcapi_image_error_message()
{
	return __("Shipment Image can't be save. Wrong file format.", 'wpcargo-api');
}
function wpcapi_shipment_not_found_message()
{
	return __('Shipment not Found', 'wpcargo-api');
}
function wpcapi_shipment_exist_message()
{
	return __('Shipment already Exist', 'wpcargo-api');
}
function wpcapi_shipment_added_message()
{
	return __('Shipment Added', 'wpcargo-api');
}
function wpcapi_address_added_message()
{
	return __('Address Added', 'wpcargo-api');
}
function wpcapi_address_updated_message()
{
	return __('Address Updated', 'wpcargo-api');
}
function wpcapi_shipment_updated_message()
{
	return __('Shipment Updated', 'wpcargo-api');
}
function wpcapi_shipment_add_failed_message()
{
	return __('Add Shipment Failed', 'wpcargo-api');
}
function wpcapi_address_add_failed_message()
{
	return __('Add Address Failed', 'wpcargo-api');
}
function wpcapi_address_update_failed_message()
{
	return __('Update Address Failed', 'wpcargo-api');
}
function wpcapi_user_label()
{
	return __('User', 'wpcargo-api');
}
function wpcapi_success_registration_label()
{
	return __('Registration was Successful', 'wpcargo-api');
}
function wpcapi_phperror_0_label()
{
	return __('There is no error, the file uploaded with success.', 'wpcargo-api');
}
function wpcapi_phperror_1_label()
{
	return __('The uploaded file exceeds the upload_max_filesize directive in php.ini.', 'wpcargo-api');
}
function wpcapi_phperror_2_label()
{
	return __('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.', 'wpcargo-api');
}
function wpcapi_phperror_3_label()
{
	return __('The uploaded file was only partially uploaded.', 'wpcargo-api');
}
function wpcapi_phperror_4_label()
{
	return __('No file was uploaded.', 'wpcargo-api');
}
function wpcapi_phperror_5_label()
{
	return __('Missing a temporary folder.', 'wpcargo-api');
}
function wpcapi_phperror_6_label()
{
	return __('Failed to write file to disk.', 'wpcargo-api');
}
function wpcapi_phperror_7_label()
{
	return __('A PHP extension stopped the file upload.', 'wpcargo-api');
}
function wpcapi_license_helper_plugin_dependent_label()
{
	return __('This plugin requires <a href="http://wpcargo.com/" target="_blank">WPTaskForce License Helper</a> plugin to be active!', 'wpcargo-api');
}
function wpcapi_wpcargo_plugin_dependent_label()
{
	return __('This plugin requires <a href="https://wordpress.org/plugins/wpcargo/" target="_blank">WPCargo</a> plugin to be active!', 'wpcargo-api');
}
function wpcapi_cheating_dependent_label()
{
	return __('Cheating, uh?', 'wpcargo-api');
}
