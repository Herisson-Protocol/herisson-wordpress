<?php
/**
 * The admin interface for managing and editing backups.
 * @package herisson
 */

function herisson_backup_actions() {

 $action = param('action');
 switch ($action) {
	 case 'backup': herisson_backup_backup();
		break;
	 case 'export': herisson_backup_export();
		break;
	 case 'import': herisson_backup_import();
		break;
  default: herisson_manage_backup();
	}

}

function herisson_manage_backup() {

    global $wpdb;

    echo '
	<div class="wrap">

		<h2>' . __("Import, export and backup", HERISSONTD) . '</h2>
	';

    echo '
		<form method="post" action="' . get_option('siteurl') . '/wp-admin/admin.php?page=herisson_backup" enctype="multipart/form-data">
	';

    if ( function_exists('wp_nonce_field') )
        wp_nonce_field('herisson-update-backup');

    echo '
		<table class="form-table" width="100%" cellspacing="2" cellpadding="5">
			<tr valign="top">
				<th scope="row">' . __('Import Firefox bookmarks', HERISSONTD) . ':</th>
				<td>
					<input type="file" name="firefox" />
				</td>
			</tr>
		</table>

		<input type="hidden" name="action" value="import" />

		<p class="submit">
			<input type="submit" value="' . __("Import bookmarks", HERISSONTD) . '" />
		</p>

		</form>
		</table>
		<table>
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
					' . __("No public search : Your public and private bookmarks are not available for you friends (for search and view).", HERISSONTD) . '
					' . __("Public search : Your public bookmarks are available for your friends (for search and view), your private bookmarks always stay private.", HERISSONTD) . '
					' . __("Recursive search : Your public bookmarks are available for your friends (for search and view), your private bookmarks always stay private. Moreover, friends search for bookmarks, you forward their search to all your friends.", HERISSONTD) . '
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





function herisson_backup_import() {
 if (isset($_FILES['firefox'])) { 
	 herisson_backup_import_firefox();
	}

}


function herisson_backup_import_firefox() {
	require HERISSON_INCLUDES_DIR."firefox/bookmarks.class.php";
	print_r($_FILES['firefox']);
	$filename = $_FILES['firefox']['tmp_name'];
	# Creating new bookmarks
	$bookmarks = new Bookmarks();
	$bookmarks->parse($filename);
	$bookmarks->bookmarksFileMd5 = md5_file($filename);


# herisson_bookmark_create("http://www.linuxfr.org");
#	exit;

?>
 <link href="<?=get_option('siteurl')?>/wp-content/plugins/herisson/includes/firefox/styles.css" rel="stylesheet" type="text/css" />
 <table class="widefat post">

	<?  while($bookmarks->hasMoreItems()) {

	$item = $bookmarks->getNextElement();
	if (!$item->_isFolder) {
 herisson_bookmark_create($item->HREF,array('favicon_image'=>$item->ICONDATA,'title'=>$item->name));
	}
	continue;
	?>
	<tr>
 	<td>
	<?  if(!$item->_isFolder) { ?>
		<input type="checkbox" name=""/>
	<? } ?>
	 </td>
	
	 <td>
	<? 
	 echo str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;",$item->depth);

	 if($item->_isFolder) { ?>
		<b><?=$item->name?></b>
	<? } else { 
		if($item->ICON) {
		 ?>
			<a href="<?=$item->HREF?>" target="_blank"><img src="icons/<?=$item->ICON?>.ico" alt="" /><span class="txt" title="<?=$item->name?>"><?=shortname($item->name)?></span></a>

		<? } else { ?>
			<a href="<?=$item->HREF?>" target="_blank"><img src="page.png" alt="" /><span class="txt" title="<?=$item->name?>"><?=shortname($item->name)?></span></a>
			<?
		}
	}
	?>
	</td></tr>

<?

}
		
		

}

