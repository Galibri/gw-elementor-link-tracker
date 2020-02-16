<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * First code to run after the plugin installation
 */
function gwelt_activate_plugin(){
    if( version_compare( get_bloginfo( 'version' ), '5.0', '<' ) ){
        wp_die( __('This plugin requires WordPress min version 5.0 to run smoothly.') );
    }
    create_gw_elementor_links_db();
    add_elementor_user_role();

    $table_options = get_option('gw_elementor_links_misc');
    $table_options['gw_elementor_count_per_person'] = 100;
    update_option('gw_elementor_links_misc', $table_options);
}