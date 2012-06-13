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

	</div>
	';

}





function herisson_backup_import() {
 if (isset($_FILES['firefox'])) { 
	 herisson_backup_import_firefox();
	}

}


function herisson_backup_import_firefox_submit() {
	require HERISSON_INCLUDES_DIR."firefox/bookmarks.class.php";
 $bookmarks = post('bookmarks');

}


function herisson_backup_import_firefox() {
	require HERISSON_INCLUDES_DIR."firefox/bookmarks.class.php";
#	print_r($_FILES['firefox']);
	$filename = $_FILES['firefox']['tmp_name'];
	# Creating new bookmarks
	$bookmarks = new Bookmarks();
	$bookmarks->parse($filename);
	$bookmarks->bookmarksFileMd5 = md5_file($filename);


# herisson_bookmark_create("http://www.linuxfr.org");
#	exit;

?>
<!--
 <link href="<?=get_option('siteurl')?>/wp-content/plugins/herisson/includes/firefox/styles.css" rel="stylesheet" type="text/css" />
	-->
 <table class="widefat post">
	 <tr>
		 <th style="width: 50px"><?=__('Add',HERISSONTD)?></th>
		 <th style="width: 50px"><?=__('Status',HERISSONTD)?></th>
		 <th style="width: 80px"><?=__('Private',HERISSONTD)?></th>
		 <th style="width: 50px"><?=__('Icon',HERISSONTD)?></th>
		 <th><?=__('Title',HERISSONTD)?></th>
		</tr>

  <tr>
	<? 
	$i=0;
	while($bookmarks->hasMoreItems()) {
	 $i++;

	$item = $bookmarks->getNextElement();
	 $spacer = "&nbsp;&nbsp;&nbsp;&nbsp;";
#		if ($i > 20) { break; }
	 if($item->_isFolder) { 
 	 $space = str_repeat($spacer,$item->depth-1);
		 ?>
			 <td></td>
			 <td></td>
			 <td></td>
			 <td></td>
			 <td>
   		<b><?=$space." ".$item->name?></b>
				</td>
		<? 
		 } else {
		 $statut = herisson_network_check($item->HREF);
 	 $space = str_repeat($spacer,$item->depth-1);
	/*
 herisson_bookmark_create($item->HREF,array(
	 'favicon_url'=>$item->ICON_URI,
	 'favicon_image'=>$item->ICON_DATA,
		'title'=>$item->name
		));
	}
	continue;
		*/
	?>
	<tr>

 	<td>
 		<input type="checkbox" name="bookmarks[<?=$i?>][import]" <? if (!$statut['error']) { ?> checked="checked" <? } ?>/>
	 </td>

 	<td style="background-color:<?=$statut['color']?>">
		 <span title="<?=$statut['message']?>" style="font-weight:bold; color:black"><?=$statut['code']?></span>
		</td>

 	<td>
			<input type="checkbox" name="bookmarks[<?=$i?>][private]" />&nbsp;<?=__('Private?')?>
		</td>

 	<td>
	<?  # Icon
		 if ($item->ICON_DATA) { ?>
			 <input type="hidden" name="bookmarks[<?=$i?>][favicon_image]" value="<?=$item->ICON_DATA?>"/>
			 <input type="hidden" name="bookmarks[<?=$i?>][favicon_url]" value="<?=$item->ICON_URI?>"/>
 		<img src="data:image/png;base64,<?=$item->ICON_DATA?>" alt="" />
			<? } else if ($item->ICON_URI) { ?>
			 <input type="hidden" name="bookmarks[<?=$i?>][favicon_url]" value="<?=$item->ICON_URI?>"/>
  		<img src="<?=$item->ICON_URI?>" alt="" />
			<? } ?>
	 </td>
	
	 <td>
			<input type="hidden" name="bookmarks[<?=$i?>][url]" value="<?=$item->HREF?>"/>
			<input type="hidden" name="bookmarks[<?=$i?>][title]" value="<?=$item->name?>"/>
			<a href="<?=$item->HREF?>" target="_blank"><span class="txt" title="<?=$item->name?>"><?=$space?><?=shortname($item->name)?></span></a>
		</td>

	<? } ?>
	</tr>

<? 
 flush();
 }
?>
	</table>
	</form>
<?		
		

}

