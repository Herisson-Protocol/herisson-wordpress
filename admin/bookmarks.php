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
	 case 'view': herisson_bookmark_view();
		break;
	 case 'submitedit': herisson_bookmark_submitedit();
		break;
		case 'list': herisson_bookmark_list();
		break;
		case 'delete': herisson_bookmark_delete();
		break;
		case 'tagcloud': herisson_bookmark_tagcloud();
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
  <td><b><a href="<?=get_option('siteurl')?>/wp-admin/admin.php?page=herisson_bookmarks&action=edit&id=<?=$bookmark->id?>"><? echo $bookmark->title; ?></a></b></td>
  <td><a href="<? echo $bookmark->url; ?>"><? echo $bookmark->url; ?></a></td>
  <td>
		<!--
		 <a href="<?=get_option('siteurl')?>/wp-admin/admin.php?page=herisson_bookmarks&action=edit&id=<?=$bookmark->id?>"><?=__('Edit',HERISSONTD)?></a>
			-->
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
	 <? echo __(sizeof($bookmarks)." bookmarks.",HERISSONTD); ?>
		</div>
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

				<form method="post" action="' . get_option('siteurl') . '/wp-admin/admin.php?page=herisson_bookmarks">
			';


 if ( function_exists('wp_nonce_field') ) wp_nonce_field('bookmark-edit');
 if ( function_exists('wp_referer_field') ) wp_referer_field();


            echo '
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
					'.($existing->id && file_exists($existing->getImage()) && filesize($existing->getImage()) ? '
					<td rowspan="5" style="text-align: center; vertical-align: top">
					 <br/>
						<b><a href="/wp-admin/admin.php?page=herisson_bookmarks&action=view&id='.$existing->id.'&nomenu=1" target="_blank">'.__('View archive',HERISSONTD).'</a></b><br/><br/>
						<b>'.__('Capture',HERISSONTD).'</b><br/>
					 <a href="'.$existing->getImageUrl().'"><img alt="Capture" src="'.$existing->getThumbUrl().'" style="border:0.5px solid black"/></a>
					</td>
				</tr>' : '').'
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
									<option value="0"'.(!$existing->is_public ? ' selected="selected"' : '').'>' . __("Private", HERISSONTD) . '</option>
									<option value="1"'.($existing->is_public ? ' selected="selected"' : '').'>' . __("Public", HERISSONTD) . '</option>
								';

				echo '
						</select>
						<br><small>' . __("<code>Public Visibility</code> enables a bookmark to appear publicly within the herisson page.", HERISSONTD) . '</small>
						<br><small>' . __("<code>Private Visibility</code> restricts the visibility of a book to within the administrative interface.", HERISSONTD) . '</small>
					</td>
				</tr>';

            echo '
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="visibility-0">' . __("Tags", HERISSONTD) . ':</label>
					</th>
					<td>
					';
				echo "
<script src=\"/wp-content/plugins/herisson/js/herisson.dev.js\" type=\"text/javascript\"></script>
<script>
 jQuery(document).ready(function($) {
  $('#tagsdiv-post_tag, #categorydiv').children('h3, .handlediv').click(function(){
   $(this).siblings('.inside').toggle();
  });
 });
</script>";

 ?>
   <div id="tagsdiv-post_tag" class="postbox">
    <div class="handlediv" title="<?php esc_attr_e( 'Click to toggle' ); ?>"><br /></div>
    <h3><span><?php _e('Tags'); ?></span></h3>
    <div class="inside">
     <div class="tagsdiv" id="post_tag">
      <div class="jaxtag">
       <label class="screen-reader-text" for="newtag"><?php _e('Tags'); ?></label>
       <input type="hidden" name="tax_input[post_tag]" class="the-tags" id="tax-input[post_tag]" value="" />
       <div class="ajaxtag">
        <input type="text" name="newtag[post_tag]" class="newtag form-input-tip" size="16" autocomplete="off" value="" />
        <input type="button" class="button tagadd" value="<?php esc_attr_e('Add'); ?>" tabindex="3" />
       </div>
      </div>
      <div class="tagchecklist"></div>
     </div>
     <p class="tagcloud-link"><a href="#titlediv" class="tagcloud-link" id="link-post_tag"><?php _e('Choose from the most used tags'); ?></a></p>
    </div>
   </div>
			 </td>
				</tr>

				<?

   echo "
    </tbody>
    </table>";


echo '


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
		if ($id == 0) {
 		$bookmark->capture();
		}

	 herisson_bookmark_edit($bookmark->id);
#header('Location: /' . get_option('siteurl') . '/wp-admin/admin.php?page=herisson_bookmarks?action=edit&id='.$id);
#exit;

}

function herisson_bookmark_view() {
# add_action('admin_menu', 'remove_menus');
 $id = intval(get('id'));
 if (!$id) {
  echo __("Error : Missing id\n",HERISSONTD);
		exit;
	}

 $bookmark = herisson_bookmark_get($id);
	if ($bookmark && $bookmark->content) {
 	echo $bookmark->content;
	} else {
  echo sprintf(__("Error : Missing content for bookmark %s\n",HERISSONTD),$bookmark->id);
	}
	exit;
}

function herisson_bookmark_delete() {

 		$id = intval(param('id'));
			if ($id>0) {
    $bookmark = herisson_bookmark_get($id);
 			$bookmark->delete();
			}
		
			herisson_bookmark_list();
}


function herisson_bookmark_import() {
if ( !empty($_POST['login']) && !empty($_POST['password'])) {

}


}

function herisson_bookmark_tagcloud() {

 echo "toto";
 if ( post('tax') ) {
  $taxonomy = sanitize_key( post('tax'));
  $tax = get_taxonomy( $taxonomy );
  if ( ! $tax )
   die( '0' );
  if ( ! current_user_can( $tax->cap->assign_terms ) )
   die( '-1' );
 } else {
  die('0');
 }

 $tags = get_terms( $taxonomy, array( 'number' => 45, 'orderby' => 'count', 'order' => 'DESC' ) );

 if ( empty( $tags ) )
  die( isset( $tax->no_tagcloud ) ? $tax->no_tagcloud : __('No tags found!') );

 if ( is_wp_error( $tags ) )
  die( $tags->get_error_message() );

 foreach ( $tags as $key => $tag ) {
  $tags[ $key ]->link = '#';
  $tags[ $key ]->id = $tag->term_id;
 }

 // We need raw tag names here, so don't filter the output
 $return = wp_generate_tag_cloud( $tags, array('filter' => 0) );

 if ( empty($return) )
  die('0');

 echo $return;

}


