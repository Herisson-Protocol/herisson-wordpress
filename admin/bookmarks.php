<?php
/**
 * The admin interface for managing and editing bookmarks.
 * @package herisson
 */

function herisson_bookmark_actions() {

 $action = param('action');
 switch ($action) {
	 case 'add': herisson_bookmark_add();
		break;
	 case 'edit': herisson_bookmark_edit();
		break;
	 case 'submitedit': herisson_bookmark_submitedit();
		break;
		case 'list': herisson_bookmark_list();
		break;
  default: herisson_bookmark_list();
	}

}


function herisson_bookmark_get($id) {
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

function herisson_bookmark_list() {

	$bookmarks = Doctrine_Query::create()->from('WpHerissonBookmarks')->execute();
 echo '
				<h2>' . __("All bookmarks", HERISSONTD).'</h2>

 <a href="'.get_option('siteurl').'/wp-admin/admin.php?page=herisson_bookmarks&action=add&id=0">'.__('Add new bookmark',HERISSONTD).'</a></td>
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
  <td><a href="<?=get_option('siteurl')?>/wp-admin/admin.php?page=herisson_bookmarks&action=edit&id=<?=$bookmark->id?>"><?=__('Edit',HERISSONTD)?></a></td>
		<!--
  <td><a href="<?=get_option('siteurl')?>/wp-content/plugins/herisson/admin/bookmark-edit.php"><?=__('Edit',HERISSONTD)?></a></td>
		-->
 </tr>
 <?
 
 	}
		?>
		</table>
		<?
 } else {
	 echo __("No bookmark",HERISSONTD);
 }

}


function herisson_bookmark_add() {
 herisson_bookmark_edit(0);
}

function herisson_bookmark_edit($id=0) {


	$options = get_option('HerissonOptions');
	$dateTimeFormat = 'Y-m-d H:i:s';

   if ($id == 0) {
 			$id = intval(param('id'));
			}
			if ($id == 0) {
			 $existing = new WpHerissonBookmarks();
			} else {
    $existing = herisson_bookmark_get($id);
			}

            echo '
			<div class="wrap">
				<h2>' . __("Edit Bookmark", HERISSONTD) . '</h2>

				<!--<form method="post" action="' . get_option('siteurl') . '/wp-content/plugins/herisson/admin/action-bookmark-edit.php">-->
				<form method="post" action="' . get_option('siteurl') . '/wp-admin/admin.php?page=herisson_bookmarks">
				<!-- ?page=herisson_bookmarks&action=submitedit&id='.$id.'"> -->
			';


 if ( function_exists('wp_nonce_field') ) wp_nonce_field('bookmark-edit');
 if ( function_exists('wp_referer_field') ) wp_referer_field();


            echo '
				<div class="book-image">
				<!--
					<img style="float:left; margin-right: 10px;" id="book-image-0" alt="Book Cover" src="' . /*$existing->image*/'' . '" />
					-->
				</div>

				<h3>' . __("Bookmark", HERISSONTD) . ' ' . $existing->id . ':<cite> &laquo;&nbsp;' . $existing->title . '&nbsp;&raquo;</cite></h3>

				<table class="form-table" cellspacing="2" cellpadding="5">

				<input type="hidden" name="action" value="submitedit" />
				<input type="hidden" name="page" value="herisson_bookmarks" />
				<input type="hidden" name="id" value="' . $existing->id . '" />

				<tbody>
				';
				

			// Title.
            echo '
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="title-0">' . __("Title", HERISSONTD) . ':</label>
					</th>
					<td>
						<input type="text" class="main" id="title-0" name="title" value="' . $existing->title . '" />
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

			// Description
            echo '
				<tr class="form-field">
					<th valign="top" scope="row">
					<label for="description-0">' . __("Description", HERISSONTD) . ':</label>
					</th>
					<td>
					<textarea class="main" id="description-0" name="description">'. $existing->description.'</textarea>
					</td>
				</tr>
				';
/*
			// Image URL.
            echo '
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="image-0">' . __("Book Image URL", HERISSONTD) . ':</label>
					</th>
					<td>
						<input type="text" class="main" id="image-0" name="image" value="' . htmlentities($existing->image) . '" />
					</td>
				</tr>

				';
*/
			// Visibility.
            echo '
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="visibility-0">' . __("Visibility", HERISSONTD) . ':</label>
					</th>
					<td>
						<select name="is_public" id="visibility-0">
							';

						echo '
									<option value="0"'.($existing->is_public ? ' selected="selected"' : '').'>' . __("Private", HERISSONTD) . '</option>
									<option value="0"'.($existing->is_public ? ' selected="selected"' : '').'>' . __("Public", HERISSONTD) . '</option>
									<option value="1" selected="selected">' . __("Public", HERISSONTD) . '</option>
								';

				echo '
						</select>
						<br><small>' . __("<code>Public Visibility</code> enables a bookmark to appear publicly within the herisson page.", HERISSONTD) . '</small>
						<br><small>' . __("<code>Private Visibility</code> restricts the visibility of a book to within the administrative interface.", HERISSONTD) . '</small>
					</td>
				</tr>';

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

function herisson_bookmark_submitedit() {


  $id = intval(post('id'));
  $url			= post('url');
  $title			= post('title');
  $description			= post('description');

  $is_public = intval(post('is_public'));

  if ( $id == 0 )  {
		 $bookmark = new WpHerissonBookmarks();
  } else {
		 $bookmark = herisson_bookmark_get($id);
		}
		$bookmark->title = $title;
		$bookmark->url = $url;
		$bookmark->description = $description;
		$bookmark->is_public = $is_public;
		$bookmark->save();

	 herisson_bookmark_edit($bookmark->id);
#header('Location: /' . get_option('siteurl') . '/wp-admin/admin.php?page=herisson_bookmarks?action=edit&id='.$id);
#exit;

}



function herisson_bookmark_import() {
if ( !empty($_POST['login']) && !empty($_POST['password'])) {

}


}

