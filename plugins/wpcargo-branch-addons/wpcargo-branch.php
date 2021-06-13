<?php
/*
 * Plugin Name: WPCargo Branches Add-ons
 * Plugin URI: http://wpcargo.com/
 * Description: This will add the lists of other branches on admin dashbard and generate shipment tracking number base on the Branches. This will work well with WPCargo Receiving Add-ons.
 * Author: <a href="http://wptaskforce.com/">WPTaskForce</a>
 * Text Domain: wpcargo-branches
 * Domain Path: /languages
 * Version: 4.2.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	die;
}
define( 'WPC_BRANCHES_VERSION', "4.2.0");
define( 'WPC_BRANCHES_DB_VERSION', '5.2.0' );
define( 'WPC_BRANCHES_TEXTDOMAIN', 'wpcargo-branches' );
define( 'WPC_BRANCHES_URL', plugin_dir_url( __FILE__ ) );
define( 'WPC_BRANCHES_PATH', plugin_dir_path( __FILE__ ) );
define( 'WPC_BRANCHES_FILE', __FILE__ );
define( 'WPC_BRANCHES_BASENAME', plugin_basename( __FILE__ ) );
define( 'WPC_BRANCHES_TABLE', 'wpcargo_branch' );
require_once(WPC_BRANCHES_PATH.'/admin/wpc-branch-admin.php');
add_action( 'init', 'wpc_braches_database_updates' );
register_activation_hook( WPC_BRANCHES_FILE, 'wpcbranch_activation_callback' );
register_deactivation_hook( WPC_BRANCHES_FILE, 'wpcbranch_deactivation_callback' );