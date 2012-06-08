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
	 case 'retrieve': herisson_front_retrieve();
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
}

function herisson_front_retrieve() {
 if (!sizeof($_POST)) { exit; }
	$key = post('key');
	$friends = Doctrine_Query::create()->from('WpHerissonFriends')->where("public_key='$key'")->execute();
	foreach ($friends as $friend) {
	 echo $friend->generateBookmarksData();
	}
}

function herisson_front_list() {
 global $wpdb;

	$bookmarks = Doctrine_Query::create()->from('WpHerissonBookmarks')->execute();
	$options = get_option('HerissonOptions');
 echo '
	<div class="wrap">
				<h1>' . sprintf(__("%s bookmarks", HERISSONTD),$options['sitename']).'</h1>
				';
 if (sizeof($bookmarks)) {
  ?>
 <table class="widefat post " cellspacing="0">
 <tr>
  <th><?=__('Title',HERISSONTD)?></th>
  <th><?=__('URL',HERISSONTD)?></th>
 </tr>
 <?
  foreach ($bookmarks as $bookmark) {
 ?> 
 <tr>
  <td><? echo $bookmark->title; ?></td>
  <td><? echo $bookmark->url; ?></td>
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

 echo "<h2>".__("Friend's bookmarks",HERISSONTD)."</h2>";
 $options = get_option('HerissonOptions');
	$friends = Doctrine_Query::create()->from('WpHerissonFriends')->execute();
	foreach ($friends as $friend) {
		echo $friend->name."'s bookmarks<br>";
		$bookmarks = $friend->retrieveBookmarks();
		if (sizeof($bookmarks)) {
 		foreach ($bookmarks as $bookmark) {
    echo '<a href="'.$bookmark['url'].'">'.$bookmark['title'].'</a> : '.$bookmark['description'].'<br>';
 		}
		} else { echo "No bookmark"; }
 }

}



