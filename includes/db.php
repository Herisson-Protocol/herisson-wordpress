<?

/** Bookmarks **/
function herisson_bookmark_all() {
 return herisson_bookmark_get_where("1=1");
}

function herisson_bookmark_get_tag($tag) {
 $bookmarks = Doctrine_Query::create()
  ->from('WpHerissonBookmarks b')
  ->leftJoin('b.WpHerissonTags t')
  ->where("name= ?",$tag)
  ->execute();
 return $bookmarks;
}

function herisson_bookmark_get_where($where) {
 $bookmarks = Doctrine_Query::create()
  ->from('WpHerissonBookmarks')
  ->where($where)
  ->execute();
 return $bookmarks;
}

function herisson_bookmark_check_duplicate($url) {
 $bookmarks = herisson_bookmark_get_where("hash='".md5($url)."'");
 if (sizeof($bookmarks)) { return true; }
 return false;
}

function herisson_bookmark_get($id) {
 if (!is_numeric($id)) { return new WpHerissonBookmarks(); }
 $bookmarks = herisson_bookmark_get_where("id=$id");
 foreach ($bookmarks as $bookmark) {
  return $bookmark;
 }
 return new WpHerissonBookmarks();
}

function herisson_bookmark_del_tags($id) {
 Doctrine_Query::create()
  ->delete()
  ->from('WpHerissonTags')
  ->where("bookmark_id=$id")
  ->execute();
}

function herisson_bookmark_create($url,$options=array()) {

 if (herisson_bookmark_check_duplicate($url)) {
  echo "Ignoring duplicate entry : $url<br>";
 }
 $bookmark = new WpHerissonBookmarks();
 $bookmark->url = $url;
 if (sizeof($options)) {
  if (array_key_exists('favicon_url',$options) && $options['favicon_url']) {
   $bookmark->favicon_url = $options['favicon_url'];
  }
  if (array_key_exists('favicon_image',$options) && $options['favicon_image']) {
   $bookmark->favicon_image = $options['favicon_image'];
  }
  if (array_key_exists('title',$options) && $options['title']) {
   $bookmark->title = $options['title'];
  }
 }
 $bookmark->save();
 if (array_key_exists('tags',$options) && $options['tags']) {
  $bookmark->setTags($options['tags']);
 }
}


/** Friends **/
function herisson_friend_get($id) {
 if (!is_numeric($id)) { return new WpHerissonFriends(); }
 $friends = Doctrine_Query::create()
  ->from('WpHerissonFriends')
  ->where("id=$id")
  ->execute();
 foreach ($friends as $friend) {
  return $friend;
 }
 return new WpHerissonFriends();
}

function herisson_friend_list_active() {
 $friends = Doctrine_Query::create()->from('WpHerissonFriends')
 ->where('is_active=1')->execute();
 herisson_friend_list_custom( __("Active friends", HERISSON_TD),$friends);
}

function herisson_friend_list_youwant() {
 $friends = Doctrine_Query::create()->from('WpHerissonFriends')
 ->where('b_youwant=1')->execute();
 herisson_friend_list_custom( __("Waiting for friend approval", HERISSON_TD),$friends);
}

function herisson_friend_list_wantsyou() {
 $friends = Doctrine_Query::create()->from('WpHerissonFriends')
 ->where('b_wantsyou=1')->execute();
 herisson_friend_list_custom( __("Asking your permission", HERISSON_TD),$friends);
}


/** Screenshots **/
function herisson_screenshots_all() {
 $screenshots = Doctrine_Query::create()
  ->from('WpHerissonScreenshots')
  ->orderby("id")
  ->execute();
 return $screenshots;
}

function herisson_screenshots_get($id) {
 if (!is_numeric($id)) { return new WpHerissonScreenshots(); }
 $screenshots = Doctrine_Query::create()
  ->from('WpHerissonScreenshots')
  ->where("id=$id")
  ->execute();
 foreach ($screenshots as $screenshot) { return $screenshot; }
 return new WpHerissonScreenshots();
}

