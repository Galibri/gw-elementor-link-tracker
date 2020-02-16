<?php
ob_start();
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Add new Link and process it
 */
function gw_elementor_add_new_link_callback() {
	echo "<div class='wrap'>";
	if(isset($_REQUEST['action']) && isset($_REQUEST['id']) && $_REQUEST['action'] == 'edit') {
		global $wpdb;
		$prev_link = '';
		if(!empty($_REQUEST['id'])) {
			$id = $_REQUEST['id'];
			$u_link = $wpdb->get_row("SELECT * from {$wpdb->prefix}gw_elementor_links WHERE id={$id}");
			$prev_link = $u_link->link;
			echo "<h4>Previous Link: <i>". $u_link->link . "</i></h4>";
		}

		if(isset($_POST['submit'])) {

			if( empty($_POST['link']) ) {
				echo '<div id="setting-error-settings_updated" class="notice notice-warning settings-error is-dismissible"> 
					<p><strong>Field should not be empty!</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
				</div>';
			} else {
				global $wpdb;
				$table_name = $wpdb->prefix . "gw_elementor_links";
				$link = trim($_POST['link']);

				$success = $wpdb->update($table_name, array(
					'link' => $link
				), array(
					'id'	=> $u_link->id
				));

				if($success) {
					header('Location: ?page=gw-elementor-link-tracker&update_success=1');
					// wp_redirect('?page=gw-elementor-link-tracker&update_success=1');
				} else {
					echo '<div id="setting-error-settings_updated" class="notice notice-warning settings-error is-dismissible"> 
						<p><strong>Can not update the link, try again later!</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
					</div>';
				}
			}
		}
		?>
		<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
			<?php wp_nonce_field('gw_add_new_link_nonce_action', 'gw_add_new_link_nonce'); ?>
			<table class="form-table">
				<tbody>
					<tr>
						<th><label for="gw_link">Link to be updated</label></th>
						<td><input type="url" value="<?php echo $prev_link; ?>" class="regular-text" name="link" autocomplete="off"></td>
					</tr>
				</tbody>
			</table>
			<p class="submit">
				<input type="submit" name="submit" id="submit" class="button button-primary" value="Update Link">
			</p>
		</form>
	<?php } else {
	if(isset($_POST['gw_add_new_link_nonce']) && wp_verify_nonce( $_REQUEST['gw_add_new_link_nonce'], 'gw_add_new_link_nonce_action' )) {

		if(isset($_POST['submit'])) {

			if( empty($_POST['link']) ) {
				echo '<div id="setting-error-settings_updated" class="notice notice-warning settings-error is-dismissible"> 
					<p><strong>Field should not be empty!</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
				</div>';
			} else {
				global $wpdb;
				$table_name = $wpdb->prefix . "gw_elementor_links";
				$link = trim($_POST['link']);
				$current_user = wp_get_current_user();
				$user_id = $current_user->ID;
				$email = $current_user->user_email;

				$success = $wpdb->insert($table_name, array(
					'link' => $link,
					'user_id' => $user_id,
					'email' => $email,
					'link_status' => 'published'
				));

				if($success) {
					 header('Location: ?page=gw-elementor-link-tracker&add_success=1');
				} else {
					echo '<div id="setting-error-settings_updated" class="notice notice-warning settings-error is-dismissible"> 
						<p><strong>Failed, try again!</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
					</div>';
				}
			}
		}
	}

	?>
	<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
		<?php wp_nonce_field('gw_add_new_link_nonce_action', 'gw_add_new_link_nonce'); ?>
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="gw_link">Elementor Activated Website Link</label></th>
					<td><input type="url" class="regular-text" name="link" autocomplete="off"></td>
				</tr>
			</tbody>
		</table>
		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Link">
		</p>
	</form>
	<?php
	}
	echo "</div>";
}