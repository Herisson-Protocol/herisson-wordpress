<?php
/**
 * The admin interface for managing and editing backups.
 * @package herisson
 */

function herisson_maintenance_actions() {

    $action = param('action');
    switch ($action) {
    case 'export':
        herisson_maintenance_export();
        break;
    case 'maintenance':
        herisson_maintenance_maintenance();
        break;
    case 'maintenance_submit':
        herisson_maintenance_maintenance_submit();
        break;
    case 'import':
        herisson_maintenance_import();
        break;
    case 'import_submit':
        herisson_maintenance_import_submit();
        break;
        /*
    case 'import_firefox':
        herisson_maintenance_import_firefox();
        break;
    case 'import_json':
        herisson_maintenance_import_json();
        break;
        */
    case 'import_delicious':
        herisson_maintenance_import_delicious();
        break;
    default: herisson_maintenance_manage();
    }

}

function herisson_maintenance_manage() {

    require __DIR__."/views/maintenance-manage.php";
}



/** MAINTENANCE OPERATIONS **/

function herisson_maintenance_maintenance_submit() {
    $condition = "
        LENGTH(favicon_url)=0 or favicon_url is null or
        LENGTH(favicon_image)=0 or favicon_image is null or
        LENGTH(content)=0 or content is null or
        LENGTH(content_image)=0 or content_image is null";

    $bookmarks_errors   = herisson_bookmark_get_where($condition);

    require __DIR__."/views/maintenance-maintenance-submit.php";
    herisson_maintenance_maintenance();

}


function herisson_maintenance_maintenance() {
 
    $bookmarks_all              = herisson_bookmark_all();
    $bookmarks_no_favicon_url   = herisson_bookmark_get_where("LENGTH(favicon_url)=0 or favicon_url is null");
    $bookmarks_no_favicon_image = herisson_bookmark_get_where("LENGTH(favicon_image)=0 or favicon_image is null");
    $bookmarks_no_content       = herisson_bookmark_get_where("LENGTH(content)=0 or content is null");
    $bookmarks_no_content_image = herisson_bookmark_get_where("LENGTH(content_image)=0 or content_image is null");

    $options = get_option('HerissonOptions');

    require __DIR__."/views/maintenance-maintenance.php";

}

/** DELICIOUS IMPORTATION **/

function herisson_maintenance_import_submit() {
    $bookmarks = post('bookmarks');
    $nb = 0;
    foreach ($bookmarks as $bookmark) {
        if (array_key_exists('import',$bookmark) && $bookmark['import']) { 
            $nb++;
            $tags = array_key_exists('tags',$bookmark) ? explode(",",$bookmark['tags']) : array();
            if (!strlen($bookmark['url'])) {
                print_r($bookmark);
            }
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

    $page_title = __("Importation results from Delicious bookmarks", HERISSON_TD);

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

}


/** IMPORTATION **/
function herisson_maintenance_import() {
    if (!post('import_source')) {
        herisson_maintenance_manage();
        exit; 
    }

    switch (post('import_source')) {
    case 'firefox':
        herisson_maintenance_import_firefox();
        break;
    case 'json':
        herisson_maintenance_import_json();
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

    $page_title = __("Importation results from Firefox bookmarks", HERISSON_TD);

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

    $page_title = __("Importation results from JSON bookmarks", HERISSON_TD);

    foreach ($bookmarks as $i=>$bookmark) {
        $bookmarks[$i]['is_public'] = $bookmark['public'];
        $bookmarks[$i]['tags'] = implode(',',$bookmark['tags']);
        $bookmarks[$i]['favicon_image'] = "";
        $bookmarks[$i]['favicon_url'] = "";
    }
    herisson_maintenance_import_list($bookmarks);

}


function herisson_maintenance_import_list($bookmarks) {
    $options = get_option('HerissonOptions');
    require __DIR__."/views/maintenance-input-list.php";
}


/** EXPORT **/
function herisson_maintenance_export() {
    if (!post('format')) {
        herisson_maintenance_manage();
    }

    $bookmarks = herisson_bookmark_all();

    switch (post('format')) {
    case 'firefox':
        herisson_export_firefox($bookmarks);
        break;
    case 'json':
        herisson_export_json($bookmarks);
        break;
    case 'csv':
        herisson_export_csv($bookmarks);
        break;
    default: herisson_maintenance_manage();
    }
}

