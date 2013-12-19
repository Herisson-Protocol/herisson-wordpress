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
function herisson_options_manage() {

				if (post('action') == 'submitedit') {
     $options = get_option('HerissonOptions');
				 $new_options = array();
				 $allowedoptions = array('basePath','bookmarksPerPage','sitename','debugMode','adminEmail','search','screenshotTool','convertPath','checkHttpImport','acceptFriends','spiderOptionTextOnly','spiderOptionFullPage','spiderOptionScreenshot');
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
					' . __("<code>Recursive search</code> : Your public bookmarks are available for your friends (for search and view), your private bookmarks always stay private. Moreover, when friends search for bookmarks, you forward their search to all your friends.", HERISSON_TD) . '<br/>
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
				<th scope="row"><label for="search">' . __("Check urls at import", HERISSON_TD) . '</label>:</th>
				<td>
				 <select name="checkHttpImport">
					 <option value="0" '.($options['checkHttpImport'] == "0" ? ' selected="selected"' : '').'>'.__("No (faster)",HERISSON_TD).'</option>
					 <option value="1" '.($options['checkHttpImport'] == "1" ? ' selected="selected"' : '').'>'.__("Yes (slower)",HERISSON_TD).'</option>
					</select>
					<p>
					' . __("If you don't check the urls when importing bookmarks, you might import obsolete bookmarks with 404 errors or non existing domains. We recommend to check urls, but if you have more more than 200 bookmarks to import, it might be too long to wait.", HERISSON_TD) . '<br/>
					</p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="acceptFriends">' . __("Friend requests", HERISSON_TD) . '</label>:</th>
				<td>
				 <select name="acceptFriends">
					 <option value="0" '.($options['acceptFriends'] == "0" ? ' selected="selected"' : '').'>'.__("Never (automatically)",HERISSON_TD).'</option>
					 <option value="1" '.($options['acceptFriends'] == "1" ? ' selected="selected"' : '').'>'.__("Manually",HERISSON_TD).'</option>
					 <option value="2" '.($options['acceptFriends'] == "2" ? ' selected="selected"' : '').'>'.__("Always (automatically)",HERISSON_TD).'</option>
					</select>
					<p>
					' . __("This concerns only people that try to become your friend.",HERISSON_TD).'<br/>
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
				';
				$uname = exec('uname -a');
				$selected = null;
				echo ' <select name="screenshotTool"> ';
						foreach ($screenshots as $tool) {
						 echo '<option value="'.$tool->id.'" '.($options['screenshotTool'] == $tool->id ? ' selected="selected"' : '').">".__($tool->name,HERISSON_TD)."</option>";
							if ($options['screenshotTool'] == $tool->id) { $selected = $tool->name; }
						}
						echo '	</select> ';
  				if (
						 (preg_match("/(amd64|_64)/",$uname) && preg_match("/amd64/",$selected)) ||
						 (preg_match("/386/",$uname) && preg_match("/i386/",$selected))
							) {
  				 echo '<p class="herisson-success">'. sprintf(__("It seems <code>%s</code> is the correct tool for you.",HERISSON_TD),$selected).'</p>';
 				 
  				} else {
  				 echo '<p class="herisson-errors">'. sprintf(__("It seems <code>%s</code> is not the correct tool for you.",HERISSON_TD),$selected).'</p>';
						}

						echo ' <p> ';
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
					 '<p class="herisson-success">'
					 . sprintf(__("Path <code>%s</code> exists",HERISSON_TD),$options['convertPath'])
						.'</p>'
						:
					 '<p class="herisson-errors">'
					 . sprintf(__("Path <code>%s</code> doesn't exist",HERISSON_TD),$options['convertPath'])
						.'</p>'
						).'

					</p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="spiderOption">' . __("Spider Options", HERISSON_TD) . '</label>:</th>
				<td>
				<!-- TODO : Verifier wget 1.12 pour de meilleurs resultats de full HTML -->
				 '.__("When saving a bookmark and running maintenance : ", HERISSON_TD).'
					<p><input type="checkbox" name="spiderOptionTextOnly" id="spiderOptionTextOnly"'.($options['spiderOptionTextOnly'] ? ' checked="checked"' : '').' /> '.__("Save page text only <br><code>This is necessary to make full text search in the bookmarks. Lighter than full page, but no images, css, javascript etc</code>",HERISSON_TD).'</p>
					<p><input type="checkbox" name="spiderOptionFullPage" id="spiderOptionFullPage"'.($options['spiderOptionFullPage'] ? ' checked="checked"' : '').' /> '.__("Save the full HTML page <br/><code>Recommanded to make sure you have a backup of your bookmarks (includes css, images, javascript etc)</code>",HERISSON_TD).'</p>
					<p><input type="checkbox" name="spiderOptionScreenshot" id="spiderOptionScreenshot"'.($options['spiderOptionScreenshot'] ? ' checked="checked"' : '').' /> '.__("<sup>BETA</sup>Save a screenshot of the whole page like in a browser <br/><code>The result is only 50% guaranteed, does not include javascript, and is very slow... but makes nice screenshots</code>",HERISSON_TD).'</p>
					 <p>'.__("Note: After changing theses parameters, you might want to run a maintenance check to save the bookmarks locally.", HERISSON_TD).'</p>

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
