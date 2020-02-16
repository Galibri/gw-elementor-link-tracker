<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Add new admin pages
 */
function gw_add_menu_page() {
	add_menu_page('GW Elementor Link Tracker', 'GW Elementor Link Tracker', 'read', 'gw-elementor-link-tracker', 'gw_elementor_link_tracker_dashboard', 'dashicons-admin-links', 28);
	add_submenu_page('gw-elementor-link-tracker', 'Add New Link', 'Add New Link', 'read', 'gw-elementor-add-new-link', 'gw_elementor_add_new_link');
	add_submenu_page('gw-elementor-link-tracker', 'View Statistics', 'View Statistics', 'read', 'gw-elementor-view-statistics', 'gw_elementor_view_statistics');
}
add_action('admin_menu', 'gw_add_menu_page');

function gw_elementor_add_new_link() {
	gw_elementor_add_new_link_callback();
}

function gw_elementor_view_statistics() {
	gw_elementor_view_statistics_callback();
}

function gw_elementor_link_tracker_dashboard() {

	ob_start();
	require_once GW_ELEMENTOR_LINK_TRACKER_PLUGIN_PATH . 'includes/admin/gw-table-list.php';
	$template = ob_get_contents();
	ob_end_clean();
	echo $template;
}