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
	 case 'maintenance': herisson_backup_maintenance();
		break;
	 case 'maintenance_submit': herisson_backup_maintenance_submit();
		break;
	 case 'import_firefox': herisson_backup_import_firefox();
		break;
	 case 'import_firefox_submit': herisson_backup_import_firefox_submit();
		break;
	 case 'import_delicious': herisson_backup_import_delicious();
		break;
	 case 'import_delicious_submit': herisson_backup_import_delicious_submit();
		break;
  default: herisson_manage_backup();
	}

}

function herisson_manage_backup() {

    global $wpdb;

    echo '
	<div class="wrap">

		<h2>' . __("Import, export, maintenance and backup", HERISSONTD) . '</h2>
	';

    if ( function_exists('wp_nonce_field') )
        wp_nonce_field('herisson-update-backup');

    echo '
		<table class="form-table" width="100%" cellspacing="2" cellpadding="5">

		<form method="post" action="' . get_option('siteurl') . '/wp-admin/admin.php?page=herisson_backup" enctype="multipart/form-data">
		 <input type="hidden" name="action" value="import_firefox" />
			<tr valign="top">
				<th scope="row">' . __('Import Firefox bookmarks', HERISSONTD) . ':</th>
				<td style="width: 200px">
					<input type="file" name="firefox" />
				</td>
				<td>
  			<input type="submit" value="' . __("Import bookmarks", HERISSONTD) . '" />
				</td>
			</tr>
		</form>

		<form method="post" action="' . get_option('siteurl') . '/wp-admin/admin.php?page=herisson_backup" enctype="multipart/form-data">
		 <input type="hidden" name="action" value="import_delicious" />
			<tr valign="top">
				<th scope="row">' . __('Import Delicious bookmarks', HERISSONTD) . ':</th>
				<td style="width: 200px">
					<input type="text" name="username_delicious" />
					<input type="password" name="password_delicious" />
					'.__("Theses informations are not stored by this plugins.",HERISSONTD).'
				</td>
				<td>
  			<input type="submit" value="' . __("Import bookmarks", HERISSONTD) . '" />
				</td>
			</tr>
		</form>

		<form method="post" action="' . get_option('siteurl') . '/wp-admin/admin.php?page=herisson_backup" enctype="multipart/form-data">
		 <input type="hidden" name="action" value="maintenance" />
			<tr valign="top">
				<th scope="row">' . __('Check Maintenance', HERISSONTD) . ':</th>
				<td>
					
				</td>
				<td>
  			<input type="submit" value="' . __("Start maintenance checks", HERISSONTD) . '" />
				</td>
			</tr>
		</form>
		</table>



	</div>
	';

}



/** MAINTENANCE OPERATIONS **/

function herisson_backup_maintenance_submit() {
 $condition = "
	 LENGTH(favicon_url)=0 or favicon_url is null or
	 LENGTH(favicon_image)=0 or favicon_image is null or
		LENGTH(content)=0 or content is null or
		LENGTH(content_image)=0 or content_image is null";

	$bookmarks_errors   = herisson_bookmark_get_where($condition);
 foreach ($bookmarks_errors as $b) {
	 $b->maintenance();
		$b->captureFromUrl();
		$b->save();
	}
	?>

	<p class="success">
	 <?=__("Maintenance has been done, here are the results after the maintenance operation. Some of the errors may not be fixable.",HERISSONTD)?>
	</p>
	<?
 herisson_backup_maintenance();

}


function herisson_backup_maintenance() {
 
	$bookmarks_no_favicon_url   = herisson_bookmark_get_where("LENGTH(favicon_url)=0 or favicon_url is null");
	$bookmarks_no_favicon_image = herisson_bookmark_get_where("LENGTH(favicon_image)=0 or favicon_image is null");
	$bookmarks_no_content       = herisson_bookmark_get_where("LENGTH(content)=0 or content is null");
	$bookmarks_no_content_image = herisson_bookmark_get_where("LENGTH(content_image)=0 or content_image is null");

 ?>
	<h1><?=__("Maintenance",HERISSONTD)?></h1>
	<h2><?=__("Favicon url",HERISSONTD)?></h2>
	<p><?=__("Bookmarks with no favicon URL",HERISSONTD)?> : <?=sizeof($bookmarks_no_favicon_url)?> </p>

	<h2><?=__("Favicon images",HERISSONTD)?></h2>
	<p><?=__("Bookmarks with no favicon Image",HERISSONTD)?> : <?=sizeof($bookmarks_no_favicon_image)?> </p>

	<h2><?=__("Contents",HERISSONTD)?></h2>
	<p><?=__("Bookmarks with no content",HERISSONTD)?> : <?=sizeof($bookmarks_no_content)?> </p>

	<h2><?=__("Screenshots",HERISSONTD)?></h2>
	<p><?=__("Bookmarks with no screenshots",HERISSONTD)?> : <?=sizeof($bookmarks_no_content_image)?> </p>
	
	
	<p><b style="color:red"><?=__("Warning, this operation can take several minutes. If you stop it during the process it's ok and you can do another maintenance operation to finish the maintenance.",HERISSONTD);?></b></p>
	<form method="post" action="<?=get_option('siteurl')?>/wp-admin/admin.php?page=herisson_backup">
	<input type="hidden" name="action" value="maintenance_submit" />
	<input type="submit" value="Correct theses errors" />
	</form>

		<?
	

}

/** DELICIOUS IMPORTATION **/

function herisson_backup_import_delicious_submit() {
 $bookmarks = post('bookmarks');

 $nb = 0;
	foreach ($bookmarks as $bookmark) {
	 if (array_key_exists('import',$bookmark) && $bookmark['import']) { 
		 $tags = array_key_exists('tags',$bookmark) ? explode(" ",$bookmark['tags']) : array();
   herisson_bookmark_create($bookmark['url'],array(
  	 'favicon_url'=> array_key_exists('favicon_url',$bookmark) ? $bookmark['favicon_url'] : "",
  	 'favicon_image'=>array_key_exists('favicon_image',$bookmark) ? $bookmark['favicon_image'] : "",
  		'title'=>$bookmark['title'],
  		'is_public'=>array_key_exists('private',$bookmark) && $bookmark['private'] ? 0 : 1,
				'tags'=> $tags,
 		));
		}
	}
	herisson_manage_backup();

}


function herisson_backup_import_delicious() {
 $username = post('username_delicious');
 $password = post('password_delicious');
 if (!$username || !$password) {
	 echo __("Delicious login and password not complete.",HERISSONTD);
		herisson_manage_backup();
		exit;
	}
	require HERISSON_INCLUDES_DIR."delicious/delicious.php";
	$bookmarks = herisson_delicious_posts_all($username,$password);
#	print_r($bookmarks);



?>
<!--
 <link href="<?=get_option('siteurl')?>/wp-content/plugins/herisson/includes/firefox/styles.css" rel="stylesheet" type="text/css" />
	-->
	<div class="wrap">
		<h2><?= __("Importation results from Delicious bookmarks", HERISSONTD); ?></h2>
	<form method="post" action="<?=get_option('siteurl')?>/wp-admin/admin.php?page=herisson_backup">
	 <input type="hidden" name="action" value="import_delicious_submit" />
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
	foreach ($bookmarks as $bookmark) {
	 $i++;

		  if (herisson_bookmark_check_duplicate($bookmark['href'])) { 
					$status = array("code" => __("Duplicate",HERISSONTD), "message" => __('This bookmark already exist'), "color" => "red", "error"=>1);
				} else {
  		 $status = herisson_network_check($bookmark['href']);
				}
	?>
	<tr>

 	<td>
  		<input type="checkbox" name="bookmarks[<?=$i?>][import]" <? if (!$status['error']) { ?> checked="checked" <? } ?>/>
	 </td>

 	<td style="background-color:<?=$status['color']?>">
		 <span title="<?=sprintf(__('HTTP Error %s : %s',HERISSONTD),$status['code'],$status['message'])?>" style="font-weight:bold; color:black"><?=$status['code']?></span>
		</td>

 	<td>
			<input type="checkbox" name="bookmarks[<?=$i?>][private]" <? if ($bookmark['private'] == "yes") { ?> checked="checked" <? } ?>/>&nbsp;<?=__('Private?')?>
		</td>

 	<td>
	 </td>
	
	 <td>
			<input type="hidden" name="bookmarks[<?=$i?>][url]" value="<?=$bookmark['href']?>"/>
			<input type="hidden" name="bookmarks[<?=$i?>][title]" value="<?=$bookmark['description']?>"/>
			<input type="hidden" name="bookmarks[<?=$i?>][description]" value="<?=$bookmark['extended']?>"/>
			<input type="hidden" name="bookmarks[<?=$i?>][tags]" value="<?=$bookmark['tag']?>"/>
			<a href="<?=$bookmark['href']?>" target="_blank"><span class="txt" title="<?=$bookmark['description']?>"><?=$bookmark['description']?></span></a>
		</td>

	</tr>

<? 
 flush();
 }
?>
	</table>
	 <input type="submit" value="<?=__('Import theses bookmarks',HERISSONTD);?>" />
	</form>
	</div>
<?		
		

}

/** FIREFOX IMPORTATION **/

function herisson_backup_import_firefox_submit() {
 $bookmarks = post('bookmarks');

 $nb = 0;
	foreach ($bookmarks as $bookmark) {
	 if (array_key_exists('import',$bookmark) && $bookmark['import']) { 
   herisson_bookmark_create($bookmark['url'],array(
  	 'favicon_url'=> array_key_exists('favicon_url',$bookmark) ? $bookmark['favicon_url'] : "",
  	 'favicon_image'=>array_key_exists('favicon_image',$bookmark) ? $bookmark['favicon_image'] : "",
  		'title'=>$bookmark['title'],
  		'is_public'=>array_key_exists('private',$bookmark) && $bookmark['private'] ? 0 : 1,
 		));
		}
	}
	herisson_manage_backup();

}


function herisson_backup_import_firefox() {
 if (!isset($_FILES['firefox'])) { 
	 echo __("Firefox bookmarks file not found.",HERISSONTD);
		herisson_manage_backup();
		exit;
	}
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
	<div class="wrap">
		<h2><?= __("Importation results from Firefox bookmarks", HERISSONTD); ?></h2>
	<form method="post" action="<?=get_option('siteurl')?>/wp-admin/admin.php?page=herisson_backup">
	 <input type="hidden" name="action" value="import_firefox_submit" />
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
		  if (herisson_bookmark_check_duplicate($item->HREF)) { 
					$status = array("code" => __("Duplicate",HERISSONTD), "message" => __('This bookmark already exist'), "color" => "red", "error"=>1);
				} else {
  		 $status = herisson_network_check($item->HREF);
				}
 	 $space = str_repeat($spacer,$item->depth-1);
	?>
	<tr>

 	<td>
  		<input type="checkbox" name="bookmarks[<?=$i?>][import]" <? if (!$status['error']) { ?> checked="checked" <? } ?>/>
	 </td>

  
 	<td style="background-color:<?=$status['color']?>">
		 <span title="<?=sprintf(__('HTTP Error %s : %s',HERISSONTD),$status['code'],$status['message'])?>" style="font-weight:bold; color:black"><?=$status['code']?></span>
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
	 <input type="submit" value="<?=__('Import theses bookmarks',HERISSONTD);?>" />
	</form>
	</div>
<?		
		

}

