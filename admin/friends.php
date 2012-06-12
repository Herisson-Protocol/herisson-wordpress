<?php
/**
 * The admin interface for managing and editing friends.
 * @package herisson
 */

function herisson_friend_actions() {

 $action = param('action');
 switch ($action) {
	 case 'add': herisson_friend_add();
		break;
	 case 'edit': herisson_friend_edit();
		break;
	 case 'submitedit': herisson_friend_submitedit();
		break;
		case 'list': herisson_friend_list();
		break;
		case 'delete': herisson_friend_delete();
		break;
		case 'approve': herisson_friend_approve();
		break;
  default: herisson_friend_list();
	}

}


function herisson_friend_get($id) {
 if (!is_numeric($id)) { return new Object(); }
 $friends = Doctrine_Query::create()
		->from('WpHerissonFriends')
		->where("id=$id")
		->execute();
	foreach ($friends as $friend) {
	 return $friend;
	}
	return new Object();
}

function herisson_friend_list_active() {
	$friends = Doctrine_Query::create()->from('WpHerissonFriends')
	->where('is_active=1')->execute();
	herisson_friend_list_custom( __("Active friends", HERISSONTD),$friends);
}

function herisson_friend_list_youwant() {
	$friends = Doctrine_Query::create()->from('WpHerissonFriends')
	->where('b_youwant=1')->execute();
	herisson_friend_list_custom( __("Waiting for friend approval", HERISSONTD),$friends);
}

function herisson_friend_list_wantsyou() {
	$friends = Doctrine_Query::create()->from('WpHerissonFriends')
	->where('b_wantsyou=1')->execute();
	herisson_friend_list_custom( __("Asking your permission", HERISSONTD),$friends);
}


function herisson_friend_list() {
 echo '
	<div class="wrap">
				<h1>' . __("All friends", HERISSONTD).'<a href="'.get_option('siteurl').'/wp-admin/admin.php?page=herisson_friends&action=add&id=0" class="add-new-h2">'.__('Add',HERISSONTD).'</a></h1>
				';

		herisson_friend_list_active();
		herisson_friend_list_youwant();
		herisson_friend_list_wantsyou();
		echo'
		</div>';

}


function herisson_friend_list_custom($title,$friends) {

 echo '
				<h2>'.$title.'</h2>
				';
 if (sizeof($friends)) {
  ?>
 <table class="widefat post " cellspacing="0">
 <tr>
  <th><?=__('Alias',HERISSONTD)?></th>
  <th><?=__('Official name',HERISSONTD)?></th>
  <th><?=__('URL',HERISSONTD)?></th>
  <th><?=__('Action',HERISSONTD)?></th>
 </tr>
 <?
  foreach ($friends as $friend) {
 ?> 
 <tr>
  <td><b><a href="<?=get_option('siteurl')?>/wp-admin/admin.php?page=herisson_friends&action=edit&id=<?=$friend->id?>"><? echo $friend->alias ? $friend->alias : 'Unnamed-'.$friend->id; ?></a></b></td>
  <td><? echo $friend->name; ?></td>
  <td><a href="<? echo $friend->url; ?>"><? echo $friend->url; ?></a></td>
  <td>
		 <a href="<?=get_option('siteurl')?>/wp-admin/admin.php?page=herisson_friends&action=delete&id=<?=$friend->id?>" onclick="if (confirm('<?=__('Are you sure ? ',HERISSONTD)?>')) { return true; } return false;"><?=__('Delete',HERISSONTD)?></a>
			<? if ($friend->b_wantsyou) { ?>
		 <a href="<?=get_option('siteurl')?>/wp-admin/admin.php?page=herisson_friends&action=approve&id=<?=$friend->id?>" onclick="if (confirm('<?=__('Are you sure ? ',HERISSONTD)?>')) { return true; } return false;"><?=__('Approve',HERISSONTD)?></a>
			<? } ?>
		</td>
 </tr>
 <?
 
 	}
		?>
		</table>
	 <? echo __(sizeof($friends)." friends.",HERISSONTD); ?>
		<?
 } else {
	 echo __("No friend",HERISSONTD);
 }
	echo "<br>";

}


function herisson_friend_add() {
 herisson_friend_edit(0);
}

function herisson_friend_edit($id=0) {


	$options = get_option('HerissonOptions');
	$dateTimeFormat = 'Y-m-d H:i:s';

   if ($id == 0) {
 			$id = intval(param('id'));
			}
			if ($id == 0) {
			 $existing = new WpHerissonFriends();
			} else {
    $existing = herisson_friend_get($id);
			}

            echo '
			<div class="wrap">
				<h2>' . __("Edit Friend", HERISSONTD) . '</h2>

				<!--<form method="post" action="' . get_option('siteurl') . '/wp-content/plugins/herisson/admin/action-friend-edit.php">-->
				<form method="post" action="' . get_option('siteurl') . '/wp-admin/admin.php?page=herisson_friends">
				<!-- ?page=herisson_friends&action=submitedit&id='.$id.'"> -->
			';


 if ( function_exists('wp_nonce_field') ) wp_nonce_field('friend-edit');
 if ( function_exists('wp_referer_field') ) wp_referer_field();


            echo '
				<div class="book-image">
				<!--
					<img style="float:left; margin-right: 10px;" id="book-image-0" alt="Book Cover" src="' . /*$existing->image*/'' . '" />
					-->
				</div>

				<h3>' . __("Friend", HERISSONTD) . ' ' . $existing->id . ':<cite> &laquo;&nbsp;' . $existing->name . '&nbsp;&raquo;</cite></h3>

				<table class="form-table" cellspacing="2" cellpadding="5">

				<input type="hidden" name="action" value="submitedit" />
				<input type="hidden" name="page" value="herisson_friends" />
				<input type="hidden" name="id" value="' . $existing->id . '" />

				<tbody>
				';
				

			// Alias.
            echo '
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="name-0">' . __("Alias", HERISSONTD) . ':</label>
					</th>
					<td>
						<input type="text" class="main" id="alias-0" name="alias" value="' . $existing->alias . '" />
					</td>
				</tr>
				';

			// Sitename.
            echo '
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="name-0">' . __("Official name", HERISSONTD) . ':</label>
					</th>
					<td>
						<i>'.$existing->name.'</i>
					</td>
				</tr>
				';

			// URL
            echo '
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="url-0">' . __("URL", HERISSONTD) . ':</label>
					</th>
					<td>
						<input type="text" size="80" class="main" id="url-0" name="url" value="' . $existing->url . '" />
						<br/><small><a href="'.$existing->url.'" style="text-decoration:none">Visit '.$existing->url.'</a></small>
					</td>
				</tr>
				';

			// Active
            echo '
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="url-0">' . __("Active", HERISSONTD) . ':</label>
					</th>
					<td>
      '.($existing->is_active ?
        '<span style="color:green">'.
        __("This friend is active and considered as a Herisson site",HERISSONTD)
        .'</span>'  :
        '<span style="color:red">'.
        __("This friend is inactive. Maybe it is not a Herisson site",HERISSONTD) 
        .'</span>'
      ).'
					</td>
				</tr>
				';

   echo '
    </tbody>
    </table>

    <p class="submit">
     <input class="button" type="submit" value="' . __("Save", HERISSONTD) . '" />
    </p>

    </form>

   </div>
    ';

}

function herisson_friend_submitedit() {


  $id = intval(post('id'));
  $url			= post('url');
  $alias			= post('alias');

  if ( $id == 0 )  {
		 $friend = new WpHerissonFriends();
		 $friend->is_active = 0;
  } else {
		 $friend = herisson_friend_get($id);
		}
		$friend->alias = $alias;
		$friend->url = $url;
  $friend->getInfo();
  $friend->askForFriend();
		$friend->save();

	 herisson_friend_edit($friend->id);
#header('Location: /' . get_option('siteurl') . '/wp-admin/admin.php?page=herisson_friends?action=edit&id='.$id);
#exit;

}

function herisson_friend_delete() {
 		$id = intval(param('id'));
			if ($id>0) {
    $friend = herisson_friend_get($id);
 			$friend->delete();
			}
			herisson_friend_list();
}


function herisson_friend_approve() {
 		$id = intval(param('id'));
			if ($id>0) {
    $friend = herisson_friend_get($id);
				$friend->approve();
			}
			herisson_friend_list();
}


function herisson_friend_import() {
if ( !empty($_POST['login']) && !empty($_POST['password'])) {

}


}

