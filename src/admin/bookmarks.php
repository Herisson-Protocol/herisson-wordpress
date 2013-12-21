<?php

error_reporting(E_ALL);
ini_set("display_errors",1);
/**
 * The admin interface for managing and editing bookmarks.
 * @package herisson
 */

function herisson_bookmark_actions() {
    $action = param('action');
    switch ($action) {
    case 'add':
        herisson_bookmark_add();
        break;
    case 'edit':
        herisson_bookmark_edit();
        break;
    case 'view':
        herisson_bookmark_view();
        break;
    case 'submitedit':
        herisson_bookmark_submitedit();
        break;
    case 'list':
        herisson_bookmark_list();
        break;
    case 'delete':
        herisson_bookmark_delete();
        break;
    case 'download':
        herisson_bookmark_download();
        break;
    case 'tagcloud':
        herisson_bookmark_tagcloud();
        break;
    default:
        herisson_bookmark_list();
    }
}


function herisson_bookmark_list() {

    echo "ok";
    if (get('tag')) {
        $bookmarks = herisson_bookmark_get_tag(get('tag'));
    } else {
        $bookmarks = herisson_bookmark_all();
    }

    require __DIR__."/views/bookmark-list.php";
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
        $tags = array();
    } else {
        $existing = herisson_bookmark_get($id);
        $tags = $existing->getTagsArray();
    }
    require __DIR__."/views/bookmark-edit.php";
}


function herisson_bookmark_submitedit() {

    $id = intval(post('id'));

    $bookmark = herisson_bookmark_get($id);
    $bookmark->title = post('title');
    $bookmark->url = post('url');
    $bookmark->description = post('description');
    $bookmark->is_public = post('is_public');
    $bookmark->save();
    $bookmark->maintenance();
    $bookmark->captureFromUrl();

    $tags = explode(',',post('tags'));
    $bookmark->setTags($tags);

    herisson_bookmark_edit($bookmark->id);
}


function herisson_bookmark_view() {
    $id = intval(get('id'));
    if (!$id) {
        echo __("Error : Missing id\n",HERISSON_TD);
        exit;
    }
    $bookmark = herisson_bookmark_get($id);
    if ($bookmark && $bookmark->content) {
        echo $bookmark->content;
    } else {
        echo sprintf(__("Error : Missing content for bookmark %s\n",HERISSON_TD),$bookmark->id);
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


function herisson_bookmark_download() {
    $id = intval(param('id'));
    if ($id>0) {
        $bookmark = herisson_bookmark_get($id);
        $bookmark->maintenance();
        $bookmark->captureFromUrl();
        herisson_bookmark_edit($bookmark->id);
    }
}

function herisson_bookmark_tagcloud() {

    # select count(*) as c ,name from wp_herisson_tags group by name order by name;
    $tags = Doctrine_Query::create()
        ->select('count(*) as c, name')
        ->from('WpHerissonTags')
        ->groupby('name')
        ->orderby('name')
        ->execute();
    $string="";
    foreach ($tags as $tag) {
        $string.='<a href="#" class="tag-link-'.$tag->id.'" title="3 sujets" style="font-size: '.( 10+$tag->c*2).'pt">'.$tag->name.'</a>&nbsp;';
    }
    echo $string;
    exit;
}


