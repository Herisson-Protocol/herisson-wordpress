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

		<h2>' . __("Herisson options", HERISSON_TD) . '</h2>

		<form method="post" action="' . get_option('siteurl') . '/wp-admin/admin.php?page=herisson_options">

		<table class="form-table" width="100%" cellspacing="2" cellpadding="5">
			<tr valign="top">
				<th scope="row">' . __('Site name', HERISSON_TD) . ':</th>
				<td>
					<input type="text" name="sitename" style="width:30em" value="' .$options['sitename']. '" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">' . __('Admin email', HERISSON_TD) . ':</th>
				<td>
					<input type="text" name="adminEmail" style="width:30em" value="' .$options['adminEmail']. '" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="search">' . __("Search depth", HERISSON_TD) . '</label>:</th>
				<td>
				 <select name="search">
					 <option value="0" '.($options['search'] == "0" ? ' selected="selected"' : '').'>'.__("No public search",HERISSON_TD).'</option>
					 <option value="1" '.($options['search'] == "1" ? ' selected="selected"' : '').'>'.__("Public search",HERISSON_TD).'</option>
					 <option value="2" '.($options['search'] == "2" ? ' selected="selected"' : '').'>'.__("Recursive search",HERISSON_TD).'</option>
					</select>
					<p>
					' . __("<code>No public search</code> : Your public and private bookmarks are not available for you friends (for search and view).", HERISSON_TD) . '<br/>
					' . __("<code>Public search</code> : Your public bookmarks are available for your friends (for search and view), your private bookmarks always stay private.", HERISSON_TD) . '<br/>
					' . __("<code>Recursive search</code> : Your public bookmarks are available for your friends (for search and view), your private bookmarks always stay private. Moreover, friends search for bookmarks, you forward their search to all your friends.", HERISSON_TD) . '<br/>
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="bookmarks_per_page">' . __("Bookmarks per page", HERISSON_TD) . '</label>:</th>
				<td>
					<input type="text" name="bookmarks_per_page" id="books_per_page" style="width:4em;" value="' . ( intval($options['bookmarksPerPage']) ) . '" />
					<p>
					' . __("Limits the total number of bookmarks displayed <code>per page</code> within the administrative 'Bookmarks' menu.", HERISSON_TD) . '
					</p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="basePath">' . __("Base Path", HERISSON_TD) . '</label>:</th>
				<td>
					<input type="text" name="basePath" id="basePath" style="width:30em;" value="' .$options['basePath'] . '" />
					<p>
					' . sprintf(__("This is the path where you want your bookmarks page to display publicly on your blog. Visit: <a href=\"%s/%s\">%s/%s</a>", HERISSON_TD), get_option('siteurl'),$options['basePath'],get_option('siteurl'),$options['basePath']).'<br/>
					' . __("Be careful this path doesn't override an already existing path from your blog.", HERISSON_TD).'
					</p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="basePath">' . __("Screenshot generator", HERISSON_TD) . '</label>:</th>
				<td>
				 <select name="screenshotTool">
					 ';
						foreach ($screenshots as $tool) {
						 echo '<option value="'.$tool->id.'" '.($options['screenshotTool'] == $tool->id ? ' selected="selected"' : '').">".__($tool->name,HERISSON_TD)."</option>";
						}
						echo '	
					</select>
					<p>
					 ';
						foreach ($screenshots as $tool) {
					  echo __(sprintf("%s description",$tool->name), HERISSON_TD).'<br>';
						}
						echo '	
					</p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="convertPath">' . __("Thumbnail generator", HERISSON_TD) . '</label>:</th>
				<td>
					<input type="text" name="convertPath" id="convertPath" style="width:30em;" value="'.$options['convertPath'].'" />
					'.(file_exists($options['convertPath']) ?  
					 '<p class="success">'
					 . sprintf(__("Path <code>%s</code> exists",HERISSON_TD),$options['convertPath'])
						.'</p>'
						:
					 '<p class="error">'
					 . sprintf(__("Path <code>%s</code> doesn't exist",HERISSON_TD),$options['convertPath'])
						.'</p>'
						).'

					</p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">' . __("Debug Mode", HERISSON_TD) . ':</th>
				<td>
					<input type="checkbox" name="debug_mode" id="debug_mode"' . ( ($options['debugMode']) ? ' checked="checked"' : '' ) . ' />
				</td>
			</tr>
		</table>

		<input type="hidden" name="action" value="submitedit" />

		<p class="submit">
			<input type="submit" value="' . __("Update Options", HERISSON_TD) . '" />
		</p>

		</form>

	</div>
	';

}

?>
