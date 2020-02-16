<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Code to run after plugin deactivatio is requested
 */
function gwelt_deactivate_plugin() {
	global $wpdb;

	$delete_plugin_data_array = get_option('gw_elementor_links_misc');
	$delete_plugin_data = $delete_plugin_data_array['delete_plugin_data'];

	if($delete_plugin_data == 'on') {
		$table_names = [$wpdb->prefix .'gw_elementor_links'];

		foreach ($table_names as $table_name) {
			$wpdb->query("DROP TABLE IF EXISTS {$table_name}");
		}
	}
	delete_option('gw_elementor_links_misc');
}