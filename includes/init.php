<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Create database
 */
function create_gw_elementor_links_db() {
	global $wpdb;

	$table_name = $wpdb->prefix . "gw_elementor_links";
	global $charset_collate;
	$charset_collate = $wpdb->get_charset_collate();

	global $db_version;

	if( $wpdb->get_var("SHOW TABLES LIKE '" . $table_name . "'") !=  $table_name) {   

        $create_sql = "CREATE TABLE " . $table_name . " (
            id INT(11) NOT NULL auto_increment,
            user_id INT(11) NOT NULL,
            email TEXT NOT NULL,
            link TEXT NOT NULL,
            link_status VARCHAR(10) DEFAULT 'published',
            PRIMARY KEY (id))$charset_collate;";
        require_once(ABSPATH . "wp-admin/includes/upgrade.php");
        dbDelta( $create_sql );

        //register the new table with the wpdb object
        if (!isset($wpdb->gw_elementor_links)) {
            $wpdb->gw_elementor_links = $table_name;
            //add the shortcut so you can use $wpdb->stats
            $wpdb->tables[] = str_replace($wpdb->prefix, '', $table_name);
        }

    }
}

/**
 * create elementor_user Role
 */
function add_elementor_user_role() {
    if( ! role_exists('elementor_user') ) {
        add_role( 'elementor_user', 'Elementor User', ['read' => true, 'level_0' => true] );
    }
}

/**
 * if role exists or not
 */
function role_exists( $role ) {

  if( ! empty( $role ) ) {
    return $GLOBALS['wp_roles']->is_role( $role );
  }
  
  return false;
}

/**
 * Add plugin page
 * @param  [string] $links [return links]
 */
function gwelt_action_links( $links ) {

    $links = array_merge( array(
        '<a href="' . esc_url( admin_url( '/admin.php?page=gw-elementor-link-options' ) ) . '">' . __( 'GWELT Options', 'gw-elementor-link-tracker' ) . '</a>'
    ), $links );

    return $links;
}