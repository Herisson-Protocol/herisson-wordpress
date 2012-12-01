<?php
/**
 * The admin interface for managing and editing backups.
 * @package herisson
 */

function herisson_maintenance_actions() {

 $action = param('action');
 switch ($action) {
	 case 'export': herisson_maintenance_export();
		break;
	 case 'maintenance': herisson_maintenance_maintenance();
		break;
	 case 'maintenance_submit': herisson_maintenance_maintenance_submit();
		break;
	 case 'import': herisson_maintenance_import();
		break;
	 case 'import_submit': herisson_maintenance_import_submit();
		break;
		/*
	 case 'import_firefox': herisson_maintenance_import_firefox();
		break;
	 case 'import_json': herisson_maintenance_import_json();
		break;
		*/
	 case 'import_delicious': herisson_maintenance_import_delicious();
		break;
  default: herisson_maintenance_manage();
	}

}

function herisson_maintenance_manage() {

    echo '
	<div class="wrap">

		<h2>' . __("Import, export, maintenance", HERISSON_TD) . '</h2>

		<table class="form-table" width="100%" cellspacing="2" cellpadding="5">

  <tr><td colspan="3">
   <h3>'.__('Import',HERISSON_TD).'</h3>
		</td></tr>
		<form method="post" action="' . get_option('siteurl') . '/wp-admin/admin.php?page=herisson_maintenance" enctype="multipart/form-data">
		 <input type="hidden" name="action" value="import" />
			<tr valign="top">
				<th scope="row">' . __('Import bookmarks', HERISSON_TD) . ':</th>
				<td style="width: 400px">
				 '.__('Source',HERISSON_TD).' : 
				 <select name="import_source">
					 <option value="firefox">'.__('Firefox',HERISSON_TD).'</option>
					 <option value="json">'.__('JSON',HERISSON_TD).'</option>
					</select><br/>
					'.__('File',HERISSON_TD).' : <input type="file" name="import_file" />
				</td>
				<td>
  			<input type="submit" value="' . __("Import bookmarks", HERISSON_TD) . '" />
				</td>
			</tr>
		</form>

		<form method="post" action="' . get_option('siteurl') . '/wp-admin/admin.php?page=herisson_maintenance">
		 <input type="hidden" name="action" value="import_delicious" />
			<tr valign="top">
				<th scope="row">' . __('Import Delicious bookmarks', HERISSON_TD) . ':</th>
				<td style="width: 200px">
					'.__('Login',HERISSON_TD).' :<input type="text" name="username_delicious" /><br/>
					'.__('Password',HERISSON_TD).' :<input type="password" name="password_delicious" /><br/>
					'.__("Theses informations are not stored by this plugin.",HERISSON_TD).'
				</td>
				<td>
  			<input type="submit" value="' . __("Import bookmarks", HERISSON_TD) . '" />
				</td>
			</tr>
		</form>

  <tr><td colspan="3">
   <h3>'.__('Export',HERISSON_TD).'</h3>
		</td></tr>
		<form method="post" action="' . get_option('siteurl') . '/wp-admin/admin.php?page=herisson_maintenance">
		 <input type="hidden" name="action" value="export" />
		 <input type="hidden" name="nomenu" value="1" />
			<tr valign="top">
				<th scope="row">' . __('Export all bookmarks', HERISSON_TD) . ':</th>
				<td><h4>'.__("Format",HERISSON_TD).' </h4>
				 <input type="radio" name="format" value="json" checked="checked" /> JSON
					&nbsp;&nbsp;&nbsp;
				 <input type="radio" name="format" value="csv" /> CSV
					&nbsp;&nbsp;&nbsp;
				 <input type="radio" name="format" value="Firefox" /> Firefox

				 <h4>'.__("Options",HERISSON_TD).' </h4>
				 '.__("Include private bookmarks : ",HERISSON_TD).' 
				 <input type="radio" name="private" value="1" /> Yes
					&nbsp;&nbsp;&nbsp;
				 <input type="radio" name="private" value="0" checked="checked" /> No
				</td>
				<td>
  			<input type="submit" value="' . __("Export", HERISSON_TD) . '" />
				</td>
			</tr>
		</form>

  <tr><td colspan="3">
   <h3>'.__('Maintenance',HERISSON_TD).'</h3>
		</td></tr>
		<form method="post" action="' . get_option('siteurl') . '/wp-admin/admin.php?page=herisson_maintenance">
		 <input type="hidden" name="action" value="maintenance" />
			<tr valign="top">
				<th scope="row">' . __('Check Maintenance', HERISSON_TD) . ':</th>
				<td>
					
				</td>
				<td>
  			<input type="submit" value="' . __("Start maintenance checks", HERISSON_TD) . '" />
				</td>
			</tr>
		</form>
		</table>



	</div>
	';

}



/** MAINTENANCE OPERATIONS **/

function herisson_maintenance_maintenance_submit() {
 $condition = "
	 LENGTH(favicon_url)=0 or favicon_url is null or
	 LENGTH(favicon_image)=0 or favicon_image is null or
		LENGTH(content)=0 or content is null or
		LENGTH(content_image)=0 or content_image is null";

	$bookmarks_errors   = herisson_bookmark_get_where($condition);
?>

 <div style="width: 1000px; height: 300px; overflow:scroll; ">
<?

 foreach ($bookmarks_errors as $b) {
	 $b->maintenance();
		$b->captureFromUrl();
		$b->save();
	}
	?>
	</div>

	<p class="herisson-success">
	 <?=__("Maintenance has been done, here are the results after the maintenance operation. Some of the errors may not be fixable.",HERISSON_TD)?>
	</p>
	<?
 herisson_maintenance_maintenance();

}


function herisson_maintenance_maintenance() {
 
	$bookmarks_all              = herisson_bookmark_all();
	$bookmarks_no_favicon_url   = herisson_bookmark_get_where("LENGTH(favicon_url)=0 or favicon_url is null");
	$bookmarks_no_favicon_image = herisson_bookmark_get_where("LENGTH(favicon_image)=0 or favicon_image is null");
	$bookmarks_no_content       = herisson_bookmark_get_where("LENGTH(content)=0 or content is null");
	$bookmarks_no_content_image = herisson_bookmark_get_where("LENGTH(content_image)=0 or content_image is null");

 $options = get_option('HerissonOptions');
 ?>
	<h1><?=__("Maintenance",HERISSON_TD)?></h1>
	<h2><?=__("Favicon url",HERISSON_TD)?></h2>
	<p><?=__("Bookmarks with no favicon URL",HERISSON_TD)?> : <?=sizeof($bookmarks_no_favicon_url)?> / <?=sizeof($bookmarks_all)?></p>

	<h2><?=__("Favicon images",HERISSON_TD)?></h2>
	<p><?=__("Bookmarks with no favicon Image",HERISSON_TD)?> : <?=sizeof($bookmarks_no_favicon_image)?> / <?=sizeof($bookmarks_all)?></p>

	<h2><?=__("Contents",HERISSON_TD)?></h2>
	<p><?=__("Bookmarks with no content",HERISSON_TD)?> : <?=sizeof($bookmarks_no_content)?> / <?=sizeof($bookmarks_all)?></p>

	<h2><?=__("Screenshots",HERISSON_TD)?></h2>
	<p><?=__("Bookmarks with no screenshots",HERISSON_TD)?> : <?=sizeof($bookmarks_no_content_image)?> / <?=sizeof($bookmarks_all)?></p>
	
	
	<p><b style="color:red"><?=__("Warning, this operation can take several minutes (especially for screenshots). If you stop it during the process it's ok and you can do another maintenance operation to finish the maintenance.",HERISSON_TD);?></b></p>
	<form method="post" action="<?=get_option('siteurl')?>/wp-admin/admin.php?page=herisson_maintenance">
	<input type="hidden" name="action" value="maintenance_submit" />
	<input type="submit" value="Correct theses errors" />
	</form>

		<?
	

}

/** DELICIOUS IMPORTATION **/

function herisson_maintenance_import_submit() {
 $bookmarks = post('bookmarks');
 $nb = 0;
	foreach ($bookmarks as $bookmark) {
	 if (array_key_exists('import',$bookmark) && $bookmark['import']) { 
		 $nb++;
		 $tags = array_key_exists('tags',$bookmark) ? explode(",",$bookmark['tags']) : array();
			if (!strlen($bookmark['url'])) { print_r($bookmark); }
   herisson_bookmark_create($bookmark['url'],array(
  	 'favicon_url'=> array_key_exists('favicon_url',$bookmark) ? $bookmark['favicon_url'] : "",
  	 'favicon_image'=>array_key_exists('favicon_image',$bookmark) ? $bookmark['favicon_image'] : "",
  		'title'=>$bookmark['title'],
  		'is_public'=>array_key_exists('private',$bookmark) && $bookmark['private'] ? 0 : 1,
				'tags'=> $tags,
 		));
		}
	}
	echo '<p class="herisson-success">'.sprintf(__("Successfully add %s bookmarks !",HERISSON_TD),$nb).'</p>';
	herisson_maintenance_manage();

}


function herisson_maintenance_import_delicious() {
 $username = post('username_delicious');
 $password = post('password_delicious');
 if (!$username || !$password) {
	 echo __("Delicious login and password not complete.",HERISSON_TD);
		herisson_maintenance_manage();
		exit;
	}
	require HERISSON_INCLUDES_DIR."delicious/delicious.php";
	$delicious_bookmarks = herisson_delicious_posts_all($username,$password);
 $list = array();

?>
	<div class="wrap">
		<h2><?= __("Importation results from Delicious bookmarks", HERISSON_TD); ?></h2>
<? 
	foreach ($delicious_bookmarks as $b) {
	 $bookmark = array();
		$bookmark['url'] = $b['href'];
		$bookmark['title'] = $b['description'];
		$bookmark['description'] = $b['extended'];
		$bookmark['is_public'] = $b['private'] == 'yes' ? 0 : 1;
		$bookmark['tags'] = preg_replace("/ +/",",",$b['tag']);
		$bookmark['prefix'] = false;
 	$bookmark['favicon_url'] = "";
 	$bookmark['favicon_image'] = "";

		$list[] = $bookmark;
	}
	unset($delicious_bookmarks);
	herisson_maintenance_import_list($list);
?>
	</div>
<?		

}


/** IMPORTATION **/
function herisson_maintenance_import() {
 if (!post('import_source')) {
 	herisson_maintenance_manage();
 	exit; 
 }

	switch (post('import_source')) {
	 case 'firefox' : herisson_maintenance_import_firefox();
		break;
	 case 'json' : herisson_maintenance_import_json();
		break;
  default: herisson_maintenance_manage();
	}
}

/** FIREFOX IMPORTATION **/


function herisson_maintenance_import_firefox() {
 if (!isset($_FILES['import_file'])) { 
	 echo __("Bookmarks file not found.",HERISSON_TD);
		herisson_maintenance_manage();
		exit;
	}
	require HERISSON_INCLUDES_DIR."firefox/bookmarks.class.php";
	$filename = $_FILES['import_file']['tmp_name'];
	# Parsing bookmarks file
	$bookmarks = new Bookmarks();
	$bookmarks->parse($filename);
	$bookmarks->bookmarksFileMd5 = md5_file($filename);

 $list = array();

?>
	<div class="wrap">
		<h2><?= __("Importation results from Firefox bookmarks", HERISSON_TD); ?></h2>
	<? 
	$i=0;
	$spacer = "&nbsp;&nbsp;&nbsp;&nbsp;";
	while($bookmarks->hasMoreItems()) {
 	$item = $bookmarks->getNextElement();
	 $bookmark = array();
		$bookmark['title'] = $item->name;

	 if($item->_isFolder) { 
 	 $space = str_repeat($spacer,$item->depth-1);
 		$bookmark['prefix'] = $space;
 		$bookmark['url'] = "";
 		$bookmark['description'] = "";
 		$bookmark['is_public'] = 1;
 		$bookmark['favicon_image'] = "";
 		$bookmark['favicon_url'] = "";
 		$bookmark['tags'] = "";
  } else {
 		$bookmark['url'] = $item->HREF;
 		$bookmark['description'] = "";
 		$bookmark['is_public'] = 1;
 		$bookmark['favicon_image'] = $item->ICON_DATA;
 		$bookmark['favicon_url'] = $item->ICON_URI;
 		$bookmark['tags'] = "";
		}
		$list[] = $bookmark;
	}
	unset($bookmarks);
	herisson_maintenance_import_list($list);
?>
	</div>
<?		

}


function herisson_maintenance_import_json() {
 if (!isset($_FILES['import_file'])) { 
	 echo __("Bookmarks file not found.",HERISSON_TD);
		herisson_maintenance_manage();
		exit;
	}
	$filename = $_FILES['import_file']['tmp_name'];
	$content = file_get_contents($filename);

	$bookmarks = json_decode($content,1);

?>
	<div class="wrap">
		<h2><?= __("Importation results from JSON bookmarks", HERISSON_TD); ?></h2>
	<? 

 foreach ($bookmarks as $i=>$bookmark) {
	 $bookmarks[$i]['is_public'] = $bookmark['public'];
	 $bookmarks[$i]['tags'] = implode(',',$bookmark['tags']);
 	$bookmarks[$i]['favicon_image'] = "";
 	$bookmarks[$i]['favicon_url'] = "";
	}
	herisson_maintenance_import_list($bookmarks);
?>
	</div>
<?		

}


function herisson_maintenance_import_list($bookmarks) {
 $options = get_option('HerissonOptions');

?>
	<form method="post" action="<?=get_option('siteurl')?>/wp-admin/admin.php?page=herisson_maintenance">
	 <input type="hidden" name="action" value="import_submit" />
 <table class="widefat post">
	 <tr>
		 <th style="width: 50px"><?=__('Add',HERISSON_TD)?></th>
		 <th style="width: 50px"><?=__('Status',HERISSON_TD)?></th>
		 <th style="width: 80px"><?=__('Private',HERISSON_TD)?></th>
		 <th style="width: 50px"><?=__('Icon',HERISSON_TD)?></th>
		 <th><?=__('Title',HERISSON_TD)?></th>
		</tr>

  <tr>
	<? 
	$i=0;
	foreach ($bookmarks as $bookmark) {
	 $i++;
 
	  if ($bookmark['url']) { 
		  if (herisson_bookmark_check_duplicate($bookmark['url'])) { 
					$status = array("code" => __("Duplicate",HERISSON_TD), "message" => __('This bookmark already exist'), "color" => "red", "error"=>1);
				} else if ($options['checkHttpImport']) {
				 $network = new HerissonNetwork();
  		 $status = $network->check($bookmark['url']);
				} else {
				$status = array("code" => "No&nbsp;check", "message" => "No check has been processed. See options for more information", "color" => "orange", "error"=>0);
				}
			} else {
				$status = array("code" => "", "message" => "", "color" => "white", "error"=>1);
			}

		 ?>
	<tr>

 	<td>
   <? if ($bookmark['url']) { ?>
  		<input type="checkbox" name="bookmarks[<?=$i?>][import]" <? if (!$status['error']) { ?> checked="checked" <? } ?>/>
			<? } ?>
	 </td>
  
 	<td style="background-color:<?=$status['color']?>">
		 <span title="<?=sprintf(__('HTTP Error %s : %s',HERISSON_TD),$status['code'],$status['message'])?>" style="font-weight:bold; color:black"><?=$status['code']?></span>
		</td>

 	<td>
			<input type="checkbox" name="bookmarks[<?=$i?>][private]" <? if (!$bookmark['is_public']) { ?> checked="checked" <? } ?>/>&nbsp;<?=__('Private?')?>
		</td>


 	<td>
			 <input type="hidden" name="bookmarks[<?=$i?>][favicon_image]" value="<?=$bookmark['favicon_image']?>"/>
			 <input type="hidden" name="bookmarks[<?=$i?>][favicon_url]" value="<?=$bookmark['favicon_url']?>"/>
  	<? # Icon
		 if ($bookmark['favicon_image']) { ?>
  		<img src="data:image/png;base64,<?=$bookmark['favicon_image']?>" alt="" />
   <? } else if ($bookmark['favicon_url']) { ?>
  		<img src="<?=$bookmark['favicon_url']?>" alt="" />
			<? } ?>
	 </td>
	
	 <td>
			<input type="hidden" name="bookmarks[<?=$i?>][url]" value="<?=$bookmark['url']?>"/>
			<input type="hidden" name="bookmarks[<?=$i?>][title]" value="<?=$bookmark['title']?>"/>
			<input type="hidden" name="bookmarks[<?=$i?>][description]" value="<?=$bookmark['description']?>"/>
			<input type="hidden" name="bookmarks[<?=$i?>][tags]" value="<?=$bookmark['tags']?>"/>
			<? if (isset($bookmark['prefix'])) { echo $bookmark['prefix']; } ?>
			<a href="<?=$bookmark['url']?>" target="_blank"><span class="txt" title="<?=$bookmark['title']?>"><?=$bookmark['title']?></span></a>
		</td>

	</tr>

<? 
 flush();
 }
	?>
	</table>
	</table>
	 <input type="submit" value="<?=__('Import theses bookmarks',HERISSON_TD);?>" />
	</form>
<?		
		

}


/** EXPORT **/
function herisson_maintenance_export() {
 if (!post('format')) {
 	herisson_maintenance_manage();
 }

 $bookmarks = herisson_bookmark_all();

	switch (post('format')) {
	 case 'firefox' : herisson_export_firefox($bookmarks);
		break;
	 case 'json' : herisson_export_json($bookmarks);
		break;
	 case 'csv' : herisson_export_csv($bookmarks);
		break;
  default: herisson_maintenance_manage();
	}
}

