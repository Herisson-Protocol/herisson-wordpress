<?


require "DeliciousBrownies.php";

# https://api.del.icio.us/v1/posts/all
function herisson_delicious_posts_all($username,$password) {

	$d = new DeliciousBrownies;
	$d->setUsername($username);
	$d->setPassword($password);
	$res = $d->getAllPosts();

	if (!$res) {
  echo __("Someting went wrong while fetching Delicious bookmarks. (Eg. Wrong login/password, no bookmarks etc)",HERISSON_TD);
  exit;
	}
	return $res;

}

