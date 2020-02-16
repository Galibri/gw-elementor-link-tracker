<?php
/*
Plugin Name:  GW Elementor Link Tracker
Plugin URI:   https://galibweb.com/plugins/gw-elementor-link-tracker
Description:  Track elementor license keys
Version:      1.0
Author:       Asadullah Al Galib
Author URI:   https://galibweb.com
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  gw-elementor-link-tracker
*/
if( !function_exists( 'add_action' ) ){
    die( 'Hello there! I am just a plugin and I can\'t do anything without WordPress installation.' );
}

/**
 * Define constants
 */
define( 'GW_ELEMENTOR_LINK_TRACKER_PLUGIN_URL', __FILE__ );
define( 'GW_ELEMENTOR_LINK_TRACKER_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'GW_ELEMENTOR_LINK_TRACKER_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );

/**
 * Includes files
 */
require_once GW_ELEMENTOR_LINK_TRACKER_PLUGIN_PATH . 'includes/activation.php';
require_once GW_ELEMENTOR_LINK_TRACKER_PLUGIN_PATH . 'includes/init.php';
require_once GW_ELEMENTOR_LINK_TRACKER_PLUGIN_PATH . 'includes/deactivation.php';
require_once GW_ELEMENTOR_LINK_TRACKER_PLUGIN_PATH . 'includes/assets/enqueue-scripts.php';
require_once GW_ELEMENTOR_LINK_TRACKER_PLUGIN_PATH . 'includes/options/src/class.settings-api.php';
require_once GW_ELEMENTOR_LINK_TRACKER_PLUGIN_PATH . 'includes/options/options-settings/settings.php';
require_once GW_ELEMENTOR_LINK_TRACKER_PLUGIN_PATH . 'includes/admin/main-page.php';
require_once GW_ELEMENTOR_LINK_TRACKER_PLUGIN_PATH . 'includes/admin/add-new-link.php';
require_once GW_ELEMENTOR_LINK_TRACKER_PLUGIN_PATH . 'includes/admin/view-statistics.php';

/**
 * Hooks
 */
register_activation_hook( __FILE__, 'gwelt_activate_plugin' );
register_deactivation_hook( __FILE__, 'gwelt_deactivate_plugin' );
add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'gwelt_action_links' );

/**
 * Initialize Plugin Settings
 */
new GWELT_Settings_API_Options();