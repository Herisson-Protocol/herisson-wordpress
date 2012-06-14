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
				 $allowedoptions = array('basePath','bookmarksPerPage','sitename','debugMode','adminEmail','search','screenshotTool','convertPath');
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
			 $screenshots = herisson_screenshots_all();

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
				<th scope="row">' . __('Admin email', HERISSONTD) . ':</th>
				<td>
					<input type="text" name="adminEmail" style="width:30em" value="' .$options['adminEmail']. '" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="search">' . __("Search depth", HERISSONTD) . '</label>:</th>
				<td>
				 <select name="search">
					 <option value="0" '.($options['search'] == "0" ? ' selected="selected"' : '').'>'.__("No public search",HERISSONTD).'</option>
					 <option value="1" '.($options['search'] == "1" ? ' selected="selected"' : '').'>'.__("Public search",HERISSONTD).'</option>
					 <option value="2" '.($options['search'] == "2" ? ' selected="selected"' : '').'>'.__("Recursive search",HERISSONTD).'</option>
					</select>
					<p>
					' . __("<code>No public search</code> : Your public and private bookmarks are not available for you friends (for search and view).", HERISSONTD) . '<br/>
					' . __("<code>Public search</code> : Your public bookmarks are available for your friends (for search and view), your private bookmarks always stay private.", HERISSONTD) . '<br/>
					' . __("<code>Recursive search</code> : Your public bookmarks are available for your friends (for search and view), your private bookmarks always stay private. Moreover, friends search for bookmarks, you forward their search to all your friends.", HERISSONTD) . '<br/>
					</p>
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
					' . sprintf(__("This is the path where you want your bookmarks page to display publicly on your blog. Visit: <a href=\"%s/%s\">%s/%s</a>", HERISSONTD), get_option('siteurl'),$options['basePath'],get_option('siteurl'),$options['basePath']).'<br/>
					' . __("Be careful this path doesn't override an already existing path from your blog.", HERISSONTD).'
					</p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="basePath">' . __("Screenshot generator", HERISSONTD) . '</label>:</th>
				<td>
				 <select name="screenshotTool">
					 ';
						foreach ($screenshots as $tool) {
						 echo '<option value="'.$tool->id.'" '.($options['screenshotTool'] == $tool->id ? ' selected="selected"' : '').">".__($tool->name,HERISSONTD)."</option>";
						}
						echo '	
					</select>
					<p>
					 ';
						foreach ($screenshots as $tool) {
					  echo __(sprintf("%s description",$tool->name), HERISSONTD).'<br>';
						}
						echo '	
					</p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="convertPath">' . __("Thumbnail generator", HERISSONTD) . '</label>:</th>
				<td>
					<input type="text" name="convertPath" id="convertPath" style="width:30em;" value="'.$options['convertPath'].'" />
					'.(file_exists($options['convertPath']) ?  
					 '<p class="success">'
					 . sprintf(__("Path <code>%s</code> exists",HERISSONTD),$options['convertPath'])
						.'</p>'
						:
					 '<p class="error">'
					 . sprintf(__("Path <code>%s</code> doesn't exist",HERISSONTD),$options['convertPath'])
						.'</p>'
						).'

					</p>
				</td>
			</tr>

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
