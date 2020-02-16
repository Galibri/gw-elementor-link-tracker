<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class GW_Elementor_Link_Tracker extends WP_List_Table {

	/** Class constructor */
	public function __construct() {
		parent::__construct( [
			'singular' => __( 'Link', 'gw-link-tracker' ),
			'plural'   => __( 'Links', 'gw-link-tracker' ),
			'ajax'     => false
		] );

	}

	/**
	 * Retrieve links data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_links( $per_page = 10, $page_number = 1 ) {

		global $wpdb;

		$sql = "SELECT * FROM {$wpdb->prefix}gw_elementor_links";

		if( in_array('administrator', wp_get_current_user()->roles) ) {
			if ( ! empty( $_REQUEST['s'] ) ) {
				$search_term = esc_sql( $_REQUEST['s'] );

				$sql .= " WHERE email LIKE '%$search_term%'";
			}

		} else {
			$user_email = wp_get_current_user()->user_email;
			if ( ! empty( $_REQUEST['s'] ) ) {
				$search_term = esc_sql( $_REQUEST['s'] );

				$sql .= " WHERE email = '{$user_email}' AND email LIKE '%$search_term%' ";
			} else {
				$sql .= " WHERE email = '{$user_email}'";
			}
		}

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


		$result = $wpdb->get_results( $sql, 'ARRAY_A' );

		return $result;
	}

	/**
	 * Trash a link record.
	 *
	 * @param int $id link ID
	 */
	public static function trash_link( $id ) {
		global $wpdb;

		$wpdb->update(
			"{$wpdb->prefix}gw_elementor_links",
			['link_status' => 'trashed'],
			[ 'id' => $id ],
			[ '%s' ],
			[ '%d' ]
		);
	}

	/**
	 * Publish a link record.
	 *
	 * @param int $id link ID
	 */
	public static function publish_link( $id ) {
		global $wpdb;

		$wpdb->update(
			"{$wpdb->prefix}gw_elementor_links",
			['link_status' => 'published'],
			[ 'id' => $id ],
			[ '%s' ],
			[ '%d' ]
		);
	}

	/**
	 * Delete a link record.
	 *
	 * @param int $id link ID
	 */
	public static function delete_link( $id ) {
		global $wpdb;

		$wpdb->delete(
			"{$wpdb->prefix}gw_elementor_links",
			[ 'id' => $id ],
			[ '%d' ]
		);
	}

	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count() {
		global $wpdb;

		$user_email = wp_get_current_user()->user_email;

		if( in_array('administrator', wp_get_current_user()->roles) ) {
			$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}gw_elementor_links";
		} else {
			$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}gw_elementor_links WHERE email = '{$user_email}'";
		}

		

		return $wpdb->get_var( $sql );
	}

	/** Text displayed when no link data is available */
	public function no_items() {
		_e( 'No links avaliable.', 'gw-link-tracker' );
	}

	/**
	 * Render a column when no column specific method exist.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
		switch ($column_name) {
			case 'id':
			case 'email':
			case 'link':
			case 'link_status':
				return $item[$column_name];
			
			default:
				return "no value";
		}
	}

	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	public function column_email( $item ) {

		$delete_nonce = wp_create_nonce( 'gw_elementor_delete_link' );

		$action = [
			'edit' => sprintf('<a href="?page=%s&action=%s&id=%s">Edit</a>', 'gw-elementor-add-new-link', 'edit', $item['id']),
			'delete' => sprintf( '<a href="?page=%s&action=%s&id=%s&_wpnonce=%s">Delete Permanently</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce )
		];

		if($item['link_status'] === 'published') {
			$action += [
				'trash' => sprintf( '<a href="?page=%s&action=%s&id=%s&_wpnonce=%s">Trash</a>', esc_attr( $_REQUEST['page'] ), 'trash', absint( $item['id'] ), $delete_nonce ),
			];
		} else {
			$action += [
				'publish' => sprintf( '<a href="?page=%s&action=%s&id=%s&_wpnonce=%s">Publish</a>', esc_attr( $_REQUEST['page'] ), 'publish', absint( $item['id'] ), $delete_nonce ),
			];
		}

		return sprintf('%1$s %2$s', $item['email'], $this->row_actions($action));
	}

	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
		);
	}

	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {
		$columns = [
			'cb'      => '<input type="checkbox" />',
			'id' => 'ID',
			'email' => 'Email',
			'link' => 'Link',
			'link_status' => 'Link Status'
		];
		return $columns;
	}

	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'email' => array( 'email', true ),
			'link' => array( 'link', false ),
			'link_status' => array( 'link_status', false ),
		);

		return $sortable_columns;
	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = [
			'bulk-trash' => 'Trash Items',
			'bulk-delete' => 'Delete Permanently'
		];

		return $actions;
	}

	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {

		$hidden = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = [$this->get_columns(), $hidden, $sortable];

		/** Process bulk action */
		$this->process_bulk_action();

		$per_page     = $this->get_items_per_page( 'links_per_page', 5 );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args( [
			'total_items' => $total_items,
			'per_page'    => $per_page
		] );

		$this->items = self::get_links( $per_page, $current_page );
	}

	/**
	 * Set Hidden columns
	 */
	public function get_hidden_columns() {
		return ['id'];
	}


	public function process_bulk_action() {

		//Trash when a bulk action is being triggered...
		if ( 'publish' === $this->current_action() ) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'gw_elementor_delete_link' ) ) {
				die( 'Go get a life script kiddies' );
			}
			else {
				self::publish_link( absint( $_GET['id'] ) );
				header('Location: ?page=gw-elementor-link-tracker');
			}

		}

		//Trash when a bulk action is being triggered...
		if ( 'trash' === $this->current_action() ) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'gw_elementor_delete_link' ) ) {
				die( 'Go get a life script kiddies' );
			}
			else {
				self::trash_link( absint( $_GET['id'] ) );
				header('Location: ?page=gw-elementor-link-tracker');
			}

		}

		//Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'gw_elementor_delete_link' ) ) {
				die( 'Go get a life script kiddies' );
			}
			else {
				self::delete_link( absint( $_GET['id'] ) );
				header('Location: ?page=gw-elementor-link-tracker');
			}

		}

		// If the trash bulk action is triggered
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-trash' )
		     || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-trash' )
		) {

			$delete_ids = esc_sql( $_POST['bulk-delete'] );

			// loop over the array of record IDs and delete them
			foreach ( $delete_ids as $id ) {
				self::trash_link( $id );

			}
		    wp_redirect( esc_url_raw(add_query_arg()) );
			exit;
		}

		// If the delete bulk action is triggered
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
		     || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
		) {

			$delete_ids = esc_sql( $_POST['bulk-delete'] );

			// loop over the array of record IDs and delete them
			foreach ( $delete_ids as $id ) {
				self::delete_link( $id );

			}
		    wp_redirect( esc_url_raw(add_query_arg()) );
			exit;
		}
	}


}

function gw_list_table_layout() {

	echo '<div class="wrap">';
	echo '<h1 class="wp-heading-inline">Elementor Links</h1>';
	echo '<a href="?page=gw-elementor-add-new-link" class="page-title-action">Add New</a>';
	echo '<hr class="wp-header-end">';

	$gw_list_table = new GW_Elementor_Link_Tracker();

	$gw_list_table->prepare_items();

	if(isset($_REQUEST['update_success']) && $_REQUEST['update_success'] == 1) {
		echo '<div id="setting-error-settings_updated" class="notice notice-success settings-error is-dismissible"> 
				<p><strong>Link Updated Successfully!</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
			</div>';
	}
	if(isset($_REQUEST['add_success']) && $_REQUEST['add_success'] == 1) {
		echo '<div id="setting-error-settings_updated" class="notice notice-success settings-error is-dismissible"> 
				<p><strong>Link added successfully!</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
			</div>';
	}

	if( in_array('administrator', wp_get_current_user()->roles) ) {
		echo '<form class="frm_search_user" method="post" action="'. $_SERVER['PHP_SELF'] .'?page=gw-elementor-link-tracker">';

		$gw_list_table->search_box("Search User", 'search_link_user');

		echo "</form>";
	}

	echo '<form id="wpse-list-table-form" method="post">';

	$gw_list_table->display();

	echo '</form>';
	echo '</div>';
}

gw_list_table_layout();