<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Enqueue plugin scripts
 * @return scripts
 */
function gwelt_frontend_scripts() {
	wp_enqueue_style( 'gw-elementor-link-tracker-style', GW_ELEMENTOR_LINK_TRACKER_PLUGIN_DIR_URL . 'includes/assets/frontend/css/gwelt-style.css' );
}
add_action( 'wp_enqueue_scripts', 'gwelt_frontend_scripts' );

function gwelt_backend_scripts($hook) {
	if($hook != 'gw-elementor-link-tracker_page_gw-elementor-view-statistics') {
		return;
	}
	wp_enqueue_style( 'gw-elementor-link-style', GW_ELEMENTOR_LINK_TRACKER_PLUGIN_DIR_URL . 'includes/assets/backend/css/admin-style.css' );
}
add_action( 'admin_enqueue_scripts', 'gwelt_backend_scripts' );