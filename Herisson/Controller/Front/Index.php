<?php
/**
 * Index controller 
 *
 * @category Controller
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      HerissonControllerFront
 */

require __DIR__."/../Front.php";

/**
 * Class: HerissonControllerAdminFront
 *
 * @category Controller
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      HerissonController
 */
class HerissonControllerFrontIndex extends HerissonControllerFront
{

    /**
     * Constructor
     *
     * Sets controller's name
     */
    function __construct()
    {
        $this->name = "index";
        parent::__construct();
    }

    /**
     * Action to handle the ask from another site
     *
     * Handled via HTTP Response code
     *
     * TODO: Handle HerissonNetwork replies as Exceptions
     *
     * @return void
     */
    function askAction()
    {

        if ($this->options['acceptFriends'] == 0) {
            HerissonNetwork::reply(403, HERISSON_EXIT);
        }
        $signature = post('signature');
        $f = new WpHerissonFriends();
        $f->url = post('url');
        $f->reloadPublicKey();
        if (HerissonEncryption::i()->publicDecrypt($signature, $f->public_key) == $f->url) {
            $f->getInfo();
            if ($this->options['acceptFriends'] == 2) {
                // Friend automatically accepted, so it's a 202 Accepted for further process response
                HerissonNetwork::reply(202);
                $f->is_active=1;
            } else {
                // Friend request need to be manually processed, so it's a 200 Ok response
                HerissonNetwork::reply(200);
                $f->b_wantsyou=1;
                $f->is_active=0;
            }
            $f->save();
        } else {
            HerissonNetwork::reply(417, HERISSON_EXIT);
        }
        exit;
    }


    /**
     * Action to handle the ask from another site
     *
     * TODO: Handle HerissonNetwork replies as Exceptions
     *
     * @return void
     */
    /*
    function getAction()
    {

        if (!is_numeric($id)) {
            return new WpHerissonBookmarks();
        }
        $bookmarks = Doctrine_Query::create()
            ->from('WpHerissonBookmarks')
            ->where("id=$id")
            ->execute();
        foreach ($bookmarks as $bookmark) {
            return $bookmark;
        }
        return new WpHerissonBookmarks();
    }
    */

    /**
     * Action to display homepage of Herisson site
     *
     * This is the default action
     *
     * @return void
     */
    function indexAction()
    {

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
            $params = array($search, $search, $search, $search, $search);
        }
        $this->view->bookmarks = $q->execute($params);
        // $bookmarks = Doctrine_Query::create()->from('WpHerissonBookmarks')->execute();

        $this->view->title = $this->options['sitename'];


        $this->view->friends = WpHerissonFriendsTable::getWhere("is_active=1");

    }

    /**
     * Action to display Herisson site informations
     *
     * This is mandatory for Herisson protocol
     * Outputs JSON
     *
     * @return void
     */
    function infoAction()
    {

        echo json_encode(array(
            'sitename'   => $this->options['sitename'],
            'adminEmail' => $this->options['adminEmail'],
            'version' => HERISSON_VERSION,
        ));
    }

    /**
     * Action to display Herisson site public key
     *
     * This is mandatory for Herisson protocol
     * Outputs Text
     *
     * @return void
     */
    function publicKeyAction()
    {
        echo $this->options['publicKey'];
    }

    /**
     * Action to send all the bookmarks data to a known friend
     *
     * This methods check the given publickey
     * Outputs JSON
     *
     * @return void
     */
    function retrieveAction()
    {
        if (!sizeof($_POST)) {
            exit;
        }
        $key = post('key');
        $friends = WpHerissonFriendsTable::getWhere("public_key=?", array($key));
        foreach ($friends as $friend) {
            echo $friend->generateBookmarksData($_POST);
            // Exit au cas ou le friend est présent plusieurs fois
            exit;
        }
    }

    /**
     * Action to handle validation of a pending request for friendship.
     *
     * Handled via HTTP Response code
     *
     * @return void
     */
    function validateAction()
    {

        $signature = post('signature');
        $url = post('url');

        $f = WpHerissonFriendsTable::getOneWhere("url=? AND b_youwant=1", array($url));
        try {
            if (HerissonEncryption::i()->publicDecrypt($signature, $f->public_key) == $url) {
                $f->b_youwant=0;
                $f->is_active=1;
                $f->save();
                HerissonNetwork::reply(200);
                echo "1";
                exit;
            } else {
                HerissonNetwork::reply(417, HERISSON_EXIT);
            }
        } catch (HerissonEncryptionException $e) {
            HerissonNetwork::reply(417, HERISSON_EXIT);

        }
    }

}



