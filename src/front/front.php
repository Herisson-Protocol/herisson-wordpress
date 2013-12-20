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

function herisson_front_friends_get_where($where,$data=array()) {
    $friends = Doctrine_Query::create()
        ->from('WpHerissonFriends')
        ->where("$where")
        ->execute($data);
    foreach ($friends as $friend) {
        return $friend;
    }
    return new WpHerissonFriends();
}


function herisson_front_validate() {
    $signature = post('signature');
    $url = post('url');
    $f = herisson_front_friends_get_where("url=? AND b_youwant=1",array($url));
    if (HerissonEncrypt::i()->checkShort($url,$signature,$f->public_key)) {
        $f->b_youwant=0;
        $f->is_active=1;
        $f->save();
        HerissonNetwork::reply(200);
        echo "1";
        exit;
    }
    else {
        HerissonNetwork::reply(417,HERISSON_EXIT);
    }
}

function herisson_front_ask() {
    $options = get_option('HerissonOptions');
#    print_r($options);
    if ($options['acceptFriends'] == 0) {
        HerissonNetwork::reply(403,HERISSON_EXIT);
    }
    $signature = post('signature');
    $f = new WpHerissonFriends();
    $f->url = post('url');
    $f->reloadPublicKey();
    if (HerissonEncrypt::i()->checkShort($f->url,$signature,$f->public_key)) {
        $f->getInfo();
        if ($options['acceptFriends'] == 2) {
            HerissonNetwork::reply(202);
            $f->is_active=1;
        } else {
            # Friend request need to be manually processed, so it's a 202 Accepted for further process response
            HerissonNetwork::reply(200);
            $f->b_wantsyou=1;
            $f->is_active=0;
        }
        $f->save();
    }
    else { 
        HerissonNetwork::reply(417,HERISSON_EXIT);
    }
    exit;
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
    $friends = Doctrine_Query::create()->from('WpHerissonFriends')->where("public_key=?")->execute(array($key));
#    $friends = Doctrine_Query::create()->from('WpHerissonFriends');# ->where("public_key=?")->execute(array($key));
    foreach ($friends as $friend) {
        echo $friend->generateBookmarksData($_POST);
        # Exit au cas ou le friend est présent plusieurs fois
        exit;
    }
}

function herisson_front_list() {

    $tag = get('tag');
    $search = get('search');
    $params = array();
    $q = Doctrine_Query::create()->from('WpHerissonBookmarks as b');
    if ($tag) {
        $q = $q->leftJoin('b.WpHerissonTags t');
        $q = $q->where("t.name=?");
        $params = array($tag);
    } else if ($search) {
        $search = "%$search%";
        $q = $q->leftJoin('b.WpHerissonTags t');
        $q = $q->where("t.name LIKE ? OR b.url like ? OR b.title LIKE ? OR b.description LIKE ? OR b.content LIKE ?");
        $params = array($search,$search,$search,$search,$search);
    }
    $bookmarks = $q->execute($params);
#    $bookmarks = Doctrine_Query::create()->from('WpHerissonBookmarks')->execute();
    $options = get_option('HerissonOptions');

    $title = $options['sitename'];
    include("header.php");
    echo '
    <div id="page">
        <h1>' . sprintf(__("%s bookmarks", HERISSON_TD),$options['sitename']).'</h1>
        <div id="search">
            <form action="" method="get">
                Recherche <input type="text" name="search" value="" /><input type="submit" value="OK"/>
            </form>
        </div>

    <div id="mybookmarks">
                ';
    if (sizeof($bookmarks)) {
        ?>
    <?
        foreach ($bookmarks as $bookmark) {
    ?> 
    <div class="bookmark">
        <span class="title"><a href="<?=$bookmark->url; ?>"><?=$bookmark->title?></a></span><br/>
        <span class="tag">Tags</span> : <span class="tags"><? foreach ($bookmark->getTagsArray() as $tag) { ?><a href="?tag=<?=$tag?>"><?=$tag?></a> &nbsp; <? } ?></span>
        <? if ($bookmark->description) { ?><p class="description"><?=$bookmark->description?></p><? } ?>
    </div>
    <?
 
        }
        ?>
        </div>
        <?
    } else {
        echo __("No bookmark",HERISSON_TD);
    }

 
    $friends = Doctrine_Query::create()->from('WpHerissonFriends')->where("is_active=1")->execute();
    if (sizeof($friends)) { 
        echo "<h2>".__("Friend's bookmarks",HERISSON_TD)."</h2>";
        foreach ($friends as $friend) {
            echo "<div class=\"friend\">\n";
            echo $friend->name."'s bookmarks<br>";
            $bookmarks = $friend->retrieveBookmarks($_GET);
            if (sizeof($bookmarks)) {
                foreach ($bookmarks as $bookmark) { ?>
                    <div class="bookmark">
                        <a href="<?=$bookmark['url']?>"><?=$bookmark['title']?></a><? echo $bookmark['description'] ? $bookmark['description'] : ''; ?><br>
                    </div>
                <?
                }
            } else { echo __("No bookmark",HERISSON_TD); }
            echo "</div>\n";
        }
    } else { echo __("No friend",HERISSON_TD); }
    echo "</div>\n";
    include("footer.php");
}



