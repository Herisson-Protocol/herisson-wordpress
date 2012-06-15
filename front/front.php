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
	 case 'info': herisson_front_info();
		break;
	 case 'ask': herisson_front_ask();
		break;
	 case 'validate': herisson_front_validate();
		break;
	 case 'retrieve': herisson_front_retrieve();
		break;
  default: herisson_front_list();
	}
}


function herisson_front_get($id) {
 if (!is_numeric($id)) { return new WpHerissonBookmarks(); }
 $bookmarks = Doctrine_Query::create()
		->from('WpHerissonBookmarks')
		->where("id=$id")
		->execute();
	foreach ($bookmarks as $bookmark) {
	 return $bookmark;
	}
	return new WpHerissonBookmarks();
}

function herisson_front_get_where($where) {
 $bookmarks = Doctrine_Query::create()
		->from('WpHerissonFriends')
		->where("$where")
		->execute();
	foreach ($bookmarks as $bookmark) {
	 return $bookmark;
	}
	return new WpHerissonFriends();
}


function herisson_front_validate() {
 $signature = post('signature');
 $url = post('url');
	$f = herisson_front_get_where("url='$url' AND b_youwant=1");
 if (herisson_check_short($url,$signature,$f->public_key)) {
  $f->b_youwant=0;
		$f->is_active=1;
  $f->save();
		echo "1";
 }
 else { echo "no check !\n"; }
}

function herisson_front_ask() {
 $signature = post('signature');
 $f = new WpHerissonFriends();
 $f->url = post('url');
 $f->reloadPublicKey();
 if (herisson_check_short($f->url,$signature,$f->public_key)) {
  $f->getInfo();
  $f->b_wantsyou=1;
		$f->is_active=0;
  $f->save();
		echo "1";
 }
 else { echo "no check !\n"; }
}

function herisson_front_info() {
 $options = get_option('HerissonOptions');
 echo json_encode(array(
 'sitename'   => $options['sitename'],
 'adminEmail' => $options['adminEmail'],
 'version' => HERISSON_VERSION,
 ));
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

	$bookmarks = Doctrine_Query::create()->from('WpHerissonBookmarks')->execute();
	$options = get_option('HerissonOptions');
 echo '
	<div class="wrap">
				<h1>' . sprintf(__("%s bookmarks", HERISSON_TD),$options['sitename']).'</h1>
				';
 if (sizeof($bookmarks)) {
  ?>
 <?
  foreach ($bookmarks as $bookmark) {
 ?> 
	<div>
	 <a href="<?=$bookmark->url; ?>"><?=$bookmark->title?></a><br/>
		<? foreach ($bookmark->getTags() as $tag) { ?><a href="/tag/<?=$tag?>"><?=$tag?></a> &nbsp; <? } ?>

	</div>
 <?
 
 	}
		?>
		</div>
		<?
 } else {
	 echo __("No bookmark",HERISSON_TD);
 }

 
	$friends = Doctrine_Query::create()->from('WpHerissonFriends')->execute();
	if (sizeof($friends)) { 
 echo "<h2>".__("Friend's bookmarks",HERISSON_TD)."</h2>";
	foreach ($friends as $friend) {
		echo $friend->name."'s bookmarks<br>";
		$bookmarks = $friend->retrieveBookmarks();
		if (sizeof($bookmarks)) {
 		foreach ($bookmarks as $bookmark) {
    echo '<a href="'.$bookmark['url'].'">'.$bookmark['title'].'</a> : '.$bookmark['description'].'<br>';
 		}
		} else { echo __("No bookmark",HERISSON_TD); }
 }
	} else { echo __("No friend",HERISSON_TD); }

}



