<?php
/**
 * Admin interface for managing options.
 * @package herisson
 */

if( !isset($_SERVER['REQUEST_URI']) ) {
    $arr = explode("/", $_SERVER['PHP_SELF']);
    $_SERVER['REQUEST_URI'] = "/" . $arr[count($arr) - 1];
    if ( !empty($_SERVER['argv'][0]) )
        $_SERVER['REQUEST_URI'] .= "?{$_SERVER['argv'][0]}";
}

/**
 * Creates the options admin page and manages the updating of options.
 */
function herisson_manage_options() {

    global $wpdb;

				if (post('action') == 'submitedit') {
     $options = get_option('HerissonOptions');
				 $new_options = array();
				 $allowedoptions = array('basePath','bookmarksPerPage','sitename','debugMode');
					foreach ($allowedoptions as $option) {
					 $new_options[$option] = post($option);
					}
					$complete_options=array_merge($options,$new_options);
					if (!array_key_exists('privateKey',$complete_options)) {
      list($publicKey,$privateKey) = herisson_generate_keys_pair();
 					$complete_options['publicKey'] = $publicKey;
 					$complete_options['privateKey'] = $privateKey;
						echo "<b>Warning</b> : public/private keys have been regenerated<br>";
					}
	    update_option('HerissonOptions', $complete_options);
				}

    $options = get_option('HerissonOptions');

    echo '
	<div class="wrap">

		<h2>' . __("Herisson options", HERISSONTD) . '</h2>
	';

    echo '
		<form method="post" action="' . get_option('siteurl') . '/wp-admin/admin.php?page=herisson_options">
	';

    if ( function_exists('wp_nonce_field') )
        wp_nonce_field('herisson-update-options');

    echo '
		<table class="form-table" width="100%" cellspacing="2" cellpadding="5">
			<tr valign="top">
				<th scope="row">' . __('Site name', HERISSONTD) . ':</th>
				<td>
					<input type="text" name="sitename" style="width:30em" value="' .$options['sitename']. '" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="bookmarks_per_page">' . __("Bookmarks per page", HERISSONTD) . '</label>:</th>
				<td>
					<input type="text" name="bookmarks_per_page" id="books_per_page" style="width:4em;" value="' . ( intval($options['bookmarksPerPage']) ) . '" />
					<p>
					' . __("Limits the total number of bookmarks displayed <code>per page</code> within the administrative 'Bookmarks' menu.", HERISSONTD) . '
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="basePath">' . __("Base Path", HERISSONTD) . '</label>:</th>
				<td>
					<input type="text" name="basePath" id="basePath" style="width:30em;" value="' .$options['basePath'] . '" />
					<p>
					' . sprintf(__("This is the path where you want your bookmarks page to display publicly on your blog : Visit <a href=\"%s/%s\">%s/%s</a>", HERISSONTD), get_option('siteurl'),$options['basePath'],get_option('siteurl'),$options['basePath']).'
					</p>
				</td>
			</tr>';


	echo '
			<tr valign="top">
				<th scope="row">' . __("Debug Mode", HERISSONTD) . ':</th>
				<td>
					<input type="checkbox" name="debug_mode" id="debug_mode"' . ( ($options['debugMode']) ? ' checked="checked"' : '' ) . ' />
				</td>
			</tr>
		</table>

		<input type="hidden" name="action" value="submitedit" />

		<p class="submit">
			<input type="submit" value="' . __("Update Options", HERISSONTD) . '" />
		</p>

		</form>

	</div>
	';

}

?>
