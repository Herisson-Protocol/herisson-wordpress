<?php
/**
 * The admin interface for managing and editing bookmarks.
 * @package herisson
 */

function herisson_front_actions() {
 $path =explode("/",$_SERVER['REQUEST_URI']);
	if (array_key_exists(2,$path)) {
 	$action = $path[2];
	} else {
	 $action = null;
	}
 switch ($action) {
	 case 'publickey': herisson_front_publickey();
		break;
  default: herisson_front_list();
	}
}


function herisson_front_get($id) {
 if (!is_numeric($id)) { return new Object(); }
 $bookmarks = Doctrine_Query::create()
		->from('WpHerissonBookmarks')
		->where("id=$id")
		->execute();
	foreach ($bookmarks as $bookmark) {
	 return $bookmark;
	}
	return new Object();
}


function herisson_front_publickey() {
 $options = get_option('HerissonOptions');
	echo $options['publicKey'];
	exit;
}

function herisson_front_list() {
 global $wpdb;

	$bookmarks = Doctrine_Query::create()->from('WpHerissonBookmarks')->execute();
 echo '
	<div class="wrap">
				<h2>' . __("All bookmarks", HERISSONTD).'<a href="'.get_option('siteurl').'/wp-admin/admin.php?page=herisson_bookmarks&action=add&id=0" class="add-new-h2">'.__('Add',HERISSONTD).'</a></h2>
<!--
 <a href="'.get_option('siteurl').'/wp-admin/admin.php?page=herisson_bookmarks&action=add&id=0">'.__('Add new bookmark',HERISSONTD).'</a></td>
	-->
				';
 if (sizeof($bookmarks)) {
  ?>
 <table class="widefat post " cellspacing="0">
 <tr>
  <th><?=__('Title',HERISSONTD)?></th>
  <th><?=__('URL',HERISSONTD)?></th>
  <th><?=__('Action',HERISSONTD)?></th>
 </tr>
 <?
  foreach ($bookmarks as $bookmark) {
 ?> 
 <tr>
  <td><? echo $bookmark->title; ?></td>
  <td><? echo $bookmark->url; ?></td>
  <td>
		 <a href="<?=get_option('siteurl')?>/wp-admin/admin.php?page=herisson_bookmarks&action=edit&id=<?=$bookmark->id?>"><?=__('Edit',HERISSONTD)?></a>
		 <a href="<?=get_option('siteurl')?>/wp-admin/admin.php?page=herisson_bookmarks&action=delete&id=<?=$bookmark->id?>" onclick="if (confirm('<?=__('Are you sure ? ',HERISSONTD)?>')) { return true; } return false;"><?=__('Delete',HERISSONTD)?></a>
		</td>
		<!--
  <td><a href="<?=get_option('siteurl')?>/wp-content/plugins/herisson/admin/bookmark-edit.php"><?=__('Edit',HERISSONTD)?></a></td>
		-->
 </tr>
 <?
 
 	}
		?>
		</table>
		</div>
		<?
 } else {
	 echo __("No bookmark",HERISSONTD);
 }

}



