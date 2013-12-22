
	<div class="wrap">

		<h2><? echo __("Herisson options", HERISSON_TD); ?></h2>

		<form method="post" action="<? echo get_option('siteurl'); ?>/wp-admin/admin.php?page=herisson_options">

		<table class="form-table" width="100%" cellspacing="2" cellpadding="5">
			<tr valign="top">
				<th scope="row"><? echo __('Site name', HERISSON_TD); ?>:</th>
				<td>
					<input type="text" name="sitename" style="width:30em" value="<? echo $options['sitename']; ?>" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><? echo __('Admin email', HERISSON_TD); ?>:</th>
				<td>
					<input type="text" name="adminEmail" style="width:30em" value="<? echo $options['adminEmail']; ?>" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="search"><? echo __("Search depth", HERISSON_TD); ?></label>:</th>
				<td>
				 <select name="search">
					 <option value="0" <? echo ($options['search'] == "0" ? ' selected="selected"' : ''); ?>><? echo __("No public search",HERISSON_TD); ?></option>
					 <option value="1" <? echo ($options['search'] == "1" ? ' selected="selected"' : ''); ?>><? echo __("Public search",HERISSON_TD); ?></option>
					 <option value="2" <? echo ($options['search'] == "2" ? ' selected="selected"' : ''); ?>><? echo __("Recursive search",HERISSON_TD); ?></option>
					</select>
					<p>
					<? echo __("<code>No public search</code> : Your public and private bookmarks are not available for you friends (for search and view).", HERISSON_TD); ?><br/>
					<? echo __("<code>Public search</code> : Your public bookmarks are available for your friends (for search and view), your private bookmarks always stay private.", HERISSON_TD); ?><br/>
					<? echo __("<code>Recursive search</code> : Your public bookmarks are available for your friends (for search and view), your private bookmarks always stay private. Moreover, when friends search for bookmarks, you forward their search to all your friends.", HERISSON_TD); ?><br/>
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="bookmarksPerPage"><? echo __("Bookmarks per page", HERISSON_TD); ?></label>:</th>
				<td>
					<input type="text" name="bookmarksPerPage" id="books_per_page" style="width:4em;" value="<? echo intval($options['bookmarksPerPage']); ?>" />
					<p>
					<? echo __("Limits the total number of bookmarks displayed <code>per page</code> within the administrative 'Bookmarks' menu.", HERISSON_TD); ?>
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="search"><? echo __("Check urls at import", HERISSON_TD); ?></label>:</th>
				<td>
				 <select name="checkHttpImport">
					 <option value="0" <? echo ($options['checkHttpImport'] == "0" ? ' selected="selected"' : '');?>><? echo __("No (faster)",HERISSON_TD); ?></option>
					 <option value="1" <? echo ($options['checkHttpImport'] == "1" ? ' selected="selected"' : '');?>><? echo __("Yes (slower)",HERISSON_TD); ?></option>
					</select>
					<p>
					<? echo __("If you don't check the urls when importing bookmarks, you might import obsolete bookmarks with 404 errors or non existing domains. We recommend to check urls, but if you have more more than 200 bookmarks to import, it might be too long to wait.", HERISSON_TD); ?><br/>
					</p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="acceptFriends"><? echo __("Friend requests", HERISSON_TD); ?></label>:</th>
				<td>
				 <select name="acceptFriends">
					 <option value="0" <? echo ($options['acceptFriends'] == "0" ? ' selected="selected"' : '');?>><? echo __("Never (automatically)",HERISSON_TD); ?></option>
					 <option value="1" <? echo ($options['acceptFriends'] == "1" ? ' selected="selected"' : '');?>><? echo __("Manually",HERISSON_TD); ?></option>
					 <option value="2" <? echo ($options['acceptFriends'] == "2" ? ' selected="selected"' : '');?>><? echo __("Always (automatically)",HERISSON_TD); ?></option>
					</select>
					<p>
					<? echo __("This concerns only people that try to become your friend.",HERISSON_TD); ?><br/>
					</p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="basePath"><? echo __("Base Path", HERISSON_TD); ?></label>:</th>
				<td>
					<input type="text" name="basePath" id="basePath" style="width:30em;" value="<? echo $options['basePath']; ?>" />
					<p>
					<? echo sprintf(__("This is the path where you want your bookmarks page to display publicly on your blog. Visit: <a href=\"%s/%s\">%s/%s</a>", HERISSON_TD), get_option('siteurl'),$options['basePath'],get_option('siteurl'),$options['basePath']); ?><br/>
					<? echo __("Be careful this path doesn't override an already existing path from your blog.", HERISSON_TD); ?>
					</p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="basePath"><? echo __("Screenshot generator", HERISSON_TD); ?></label>:</th>
				<td>

    <?
				$uname = exec('uname -a');
				$selected = null;
    ?>
				<select name="screenshotTool">
    <?  foreach ($screenshots as $tool) { ?>
    
					<option value="<? echo $tool->id; ?>" <? echo ($options['screenshotTool'] == $tool->id ? ' selected="selected"' : ''); ?>><? echo __($tool->name,HERISSON_TD); ?></option>
     <?
							if ($options['screenshotTool'] == $tool->id) { $selected = $tool->name; }
						}
      ?>
				</select>
    <?
  				if (
						 (preg_match("/(amd64|_64)/",$uname) && preg_match("/amd64/",$selected)) ||
						 (preg_match("/386/",$uname) && preg_match("/i386/",$selected))
							) {
       ?>
  				 <p class="herisson-success"><? echo sprintf(__("It seems <code>%s</code> is the correct tool for you.",HERISSON_TD),$selected); ?></p>
 				 <?  } else { ?>
  				 <p class="herisson-errors">
        <? echo sprintf(__("It seems <code>%s</code> is not the correct tool for you.",HERISSON_TD),$selected); ?>
        </p>
     <?  } ?>

						<p>
      <?  foreach ($screenshots as $tool) { ?>
        <? echo __(sprintf("%s description",$tool->name), HERISSON_TD); ?><br/>
        <? } ?>
					</p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="convertPath"><? echo __("Thumbnail generator", HERISSON_TD); ?></label>:</th>
				<td>
					<input type="text" name="convertPath" id="convertPath" style="width:30em;" value="<? echo $options['convertPath']; ?>" />
					<? if (file_exists($options['convertPath'])) { ?>  
					 <p class="herisson-success">
					 <? echo sprintf(__("Path <code>%s</code> exists",HERISSON_TD),$options['convertPath']); ?>
						</p>
						<? } else { ?>
					 <p class="herisson-errors">
        <? echo sprintf(__("Path <code>%s</code> doesn't exist",HERISSON_TD),$options['convertPath']); ?>
						</p>
						<? } ?>

					</p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="spiderOption"><? echo __("Spider Options", HERISSON_TD); ?></label>:</th>
				<td>
				<!-- TODO : Verifier wget 1.12 pour de meilleurs resultats de full HTML -->
				 <? echo __("When saving a bookmark and running maintenance : ", HERISSON_TD); ?>
					<p>
        <input type="checkbox" name="spiderOptionTextOnly" id="spiderOptionTextOnly"<? echo ($options['spiderOptionTextOnly'] ? ' checked="checked"' : ''); ?> />
        <? echo __("Save page text only <br/><code>This is necessary to make full text search in the bookmarks. Lighter than full page, but no images, css, javascript etc</code>",HERISSON_TD); ?>
    </p>
					<p>
        <input type="checkbox" name="spiderOptionFullPage" id="spiderOptionFullPage"<? echo ($options['spiderOptionFullPage'] ? ' checked="checked"' : ''); ?> /> 
        <? echo __("Save the full HTML page <br/><code>Recommanded to make sure you have a backup of your bookmarks (includes css, images, javascript etc)</code>",HERISSON_TD); ?>
        </p>
					<p>
         <input type="checkbox" name="spiderOptionScreenshot" id="spiderOptionScreenshot"<? echo ($options['spiderOptionScreenshot'] ? ' checked="checked"' : ''); ?> />
         <? echo __("<sup>BETA</sup>Save a screenshot of the whole page like in a browser <br/><code>The result is only 50% guaranteed, does not include javascript, and is very slow... but makes nice screenshots</code>",HERISSON_TD); ?>
    </p>
					 <p>
        <? echo __("Note: After changing theses parameters, you might want to run a maintenance check to save the bookmarks locally.", HERISSON_TD); ?>
    </p>

					</p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><? echo __("Debug Mode", HERISSON_TD); ?>:</th>
				<td>
					<input type="checkbox" name="debug_mode" id="debug_mode"<? if ($options['debugMode']) { ?> checked="checked"<? } ?> />
				</td>
			</tr>
		</table>

		<input type="hidden" name="action" value="submitedit" />

		<p class="submit">
			<input type="submit" value="<? echo __("Update Options", HERISSON_TD); ?>" />
		</p>

		</form>

	</div>

