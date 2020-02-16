<?php
/**
 * Settings for portfolios plugin
 */
if ( !class_exists('GWELT_Settings_API_Options' ) ):
class GWELT_Settings_API_Options {

    private $settings_api;

    function __construct() {
        $this->settings_api = new GWELT_Settings_API;

        add_action( 'admin_init', array($this, 'admin_init') );
        add_action( 'admin_menu', array($this, 'admin_menu') );
    }

    function admin_init() {

        //set the settings
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );

        //initialize settings
        $this->settings_api->admin_init();
    }

    function admin_menu() {
        add_submenu_page(
        'gw-elementor-link-tracker',
        __( 'GWELT Options', 'gw-elementor-link-tracker' ),
        __( 'GWELT Options', 'gw-elementor-link-tracker' ),
        'manage_options',
        'gw-elementor-link-options',
        array($this, 'plugin_page')
    );
    }
    
    function get_settings_sections() {
        $sections = array(
            array(
                'id'    => 'gw_elementor_links_misc',
                'title' => __( 'Misc Settings', 'gw-elementor-link-tracker' )
            )
        );
        return $sections;
    }

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    function get_settings_fields() {
        $settings_fields = array(
            'gw_elementor_links_misc'     => array(
                array(
                    'name' => 'gw_elementor_count_per_person',
                    'label'     => __( 'License per person', 'gw-elementor-link-tracker' ),
                    'type'    => 'number',
                    'default' => 100
                ),
                array(
                    'name'      => 'delete_plugin_data',
                    'label'     => __( 'Delete Plugin Data on uninstall?', 'gw-elementor-link-tracker' ),
                    'type'    => 'checkbox',
                ),
            ),
        );

        return $settings_fields;
    }

    function plugin_page() {
        echo '<div class="wrap">';

        $this->settings_api->show_navigation();
        $this->settings_api->show_forms();

        echo '</div>';
    }

    /**
     * Get all the pages
     *
     * @return array page names with key value pairs
     */
    function get_pages() {
        $pages = get_pages();
        $pages_options = array();
        if ( $pages ) {
            foreach ($pages as $page) {
                $pages_options[$page->ID] = $page->post_title;
            }
        }

        return $pages_options;
    }

}
endif;

function gwelt_get_option( $option, $section, $default = '' ) {
 
    $options = get_option( $section );
 
    if ( isset( $options[$option] ) ) {
    return $options[$option];
    }
 
    return $default;
}