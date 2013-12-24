<?

require __DIR__."/../Front.php";

class HerissonControllerFrontIndex extends HerissonControllerFront {


    function __construct() {
        $this->name = "index";
        parent::__construct();
    }

    function askAction() {

        if ($this->options['acceptFriends'] == 0) {
            HerissonNetwork::reply(403,HERISSON_EXIT);
        }
        $signature = post('signature');
        $f = new WpHerissonFriends();
        $f->url = post('url');
        $f->reloadPublicKey();
        if (herisson_check_short($f->url,$signature,$f->public_key)) {
            $f->getInfo();
            if ($this->options['acceptFriends'] == 2) {
                HerissonNetwork::reply(202);
                $f->is_active=1;
            } else {
                # Friend request need to be manually processed, so it's a 202 Accepted for further process response
                HerissonNetwork::reply(200);
                $f->b_wantsyou=1;
                $f->is_active=0;
            }
            $f->save();
        } else { 
            HerissonNetwork::reply(417,HERISSON_EXIT);
        }
        exit;
    }


    function friendsGetWhere($where,$data=array()) {
        $friends = Doctrine_Query::create()
            ->from('WpHerissonFriends')
            ->where("$where")
            ->execute($data);
        foreach ($friends as $friend) {
            return $friend;
        }
        return new WpHerissonFriends();

    }

    function getAction() {

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


    function indexAction() {

        $tag = get('tag');
        $search = get('search');
        $params = array();
        $q = Doctrine_Query::create()
            ->from('WpHerissonBookmarks as b');
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
        $this->view->bookmarks = $q->execute($params);
    #    $bookmarks = Doctrine_Query::create()->from('WpHerissonBookmarks')->execute();
    
        $this->view->title = $this->options['sitename'];
    
     
        $this->view->friends = Doctrine_Query::create()->from('WpHerissonFriends')->where("is_active=1")->execute();

    }

    function infoAction() {

        echo json_encode(array(
            'sitename'   => $this->options['sitename'],
            'adminEmail' => $this->options['adminEmail'],
            'version' => HERISSON_VERSION,
        ));
    }


    function publicKeyAction() {
        echo $this->options['publicKey'];
    }

    function retrieveAction() {
        if (!sizeof($_POST)) {
            exit;
        }
        $key = post('key');
        $friends = Doctrine_Query::create()
            ->from('WpHerissonFriends')
            ->where("public_key=?")
            ->execute(array($key));
        foreach ($friends as $friend) {
            echo $friend->generateBookmarksData($_POST);
            # Exit au cas ou le friend est présent plusieurs fois
            exit;
        }
    }

    function validateAction() {

        $signature = post('signature');
        $url = post('url');
        $f = $this->friendsGetWhere("url=? AND b_youwant=1",array($url));
        if (herisson_check_short($url,$signature,$f->public_key)) {
            $f->b_youwant=0;
            $f->is_active=1;
            $f->save();
            HerissonNetwork::reply(200);
            echo "1";
            exit;
        } else { 
            HerissonNetwork::reply(417,HERISSON_EXIT);
        }
    }

}



