<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function gw_elementor_view_statistics_callback() {
	global $wpdb;
	$user_roles = wp_get_current_user()->roles;
	$user_email = wp_get_current_user()->user_email;
	$max_allowed_count = get_option('gw_elementor_links_misc')['gw_elementor_count_per_person'];
	$row_number = 1;

	echo '<div class="wrap">';
	echo '<h1 class="wp-heading-inline">Statistics</h1>';
	echo '<a href="?page=gw-elementor-link-tracker" class="page-title-action">All Links</a>';
	echo '<hr class="wp-header-end">';

	if( in_array( 'administrator', $user_roles ) ) {
		?>
		<table class="widefat fixed">
			<thead>
				<tr>
					<th class="manage-column column-user" scope="col">User Email</th>
					<th class="manage-column column-used-license" scope="col">Used Licenses(Trashed Not included)</th>
					<th class="manage-column column-available" scope="col">Available</th>
					<th class="manage-column column-trashed" scope="col">Trashed</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th class="manage-column column-user" scope="col">User Email</th>
					<th class="manage-column column-used-license" scope="col">Used Licenses(Trashed Not included)</th>
					<th class="manage-column column-available" scope="col">Available</th>
					<th class="manage-column column-trashed" scope="col">Trashed</th>
				</tr>
			</tfoot>
			<tbody>
			<?php
				$sql = "SELECT DISTINCT email FROM {$wpdb->prefix}gw_elementor_links";
				$results = $wpdb->get_results( $sql, 'ARRAY_A' );

				foreach($results as $result) {
					$result_email = $result['email'];
					$count_used = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}gw_elementor_links WHERE email = '{$result_email}' AND link_status = 'published'" );
					$count_available = $max_allowed_count - $count_used;
					$count_trashed = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}gw_elementor_links WHERE email = '{$result_email}' AND link_status = 'trashed'" );
					if($row_number % 2) {
						$alternate_class = 'class="alternate"';
					} else {
						$alternate_class = '';
					}

					echo '<tr '.$alternate_class.'>';
					 	echo '<td>'. $result_email .'</td>';
					 	echo '<td>'. $count_used .'</td>';
					 	echo '<td>'. $count_available .'</td>';
					 	echo '<td>'. $count_trashed .'</td>';
					 echo '</tr>';

					 $row_number++;
				}
			?>
			</tbody>
		</table>
		<?php
	} else {
		$count_used = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}gw_elementor_links WHERE email = '{$user_email}' AND link_status = 'published'" );
		$count_available = $max_allowed_count - $count_used;
		$count_trashed = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}gw_elementor_links WHERE email = '{$user_email}' AND link_status = 'trashed'" );
		?>
		<div class="gw-admin-page-card">
			<h3>If you have any issue, contact the administrator.</h3>
			<table class="widefat fixed">
				<tbody>
					<tr class="alternate">
						<th>User Email</th>
						<td><?php echo $user_email; ?></td>
					</tr>
					<tr>
						<th>Used Licenses(Trashed Not included)</th>
						<td><?php echo $count_used; ?></td>
					</tr>
					<tr class="alternate">
						<th>Available</th>
						<td><?php echo $count_available; ?></td>
					</tr>
					<tr>
						<th>Trashed</th>
						<td><?php echo $count_trashed; ?></td>
					</tr>
				</tbody>
			</table>
		</div>
		<?php
	}

	echo '</div>';
}