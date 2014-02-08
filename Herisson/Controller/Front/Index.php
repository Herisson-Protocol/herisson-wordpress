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

namespace Herisson\Controller\Front;

use Herisson\Encryption;
use Herisson\Export;
use Herisson\Folder;
use Herisson\Model\WpHerissonBookmarksTable;
use Herisson\Model\WpHerissonFriends;
use Herisson\Model\WpHerissonFriendsTable;
use Herisson\Model\WpHerissonLocalbackups;
use Herisson\Model\WpHerissonLocalbackupsTable;
use Herisson\Network;

require_once __DIR__."/../Front.php";

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
class Index extends \Herisson\Controller\Front
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
     * Action to handle the accepsBackups requests
     *
     * Handled via HTTP Response code
     *
     * TODO: Handle Network replies as Exceptions
     *
     * @return void
     */
    function acceptsbackupsAction()
    {
        $this->_checkBackup();

        echo "1";

    }


    /**
     * Action to handle the sendBackup requests
     *
     * Handled via HTTP Response code
     *
     * @return void
     */
    function sendbackupAction()
    {

        // TODO : Check ini_get(post_max_size)
        $this->_checkBackup();
        
        $signature  = post('signature');
        $url        = post('url');
        $backupData = post('backupData');
        //error_log($backupData);

        $friend = WpHerissonFriendsTable::getOneWhere("url=?", array($url));
        try {
            if (Encryption::i()->publicDecrypt($signature, $friend->public_key) == $url) {

                // Save the file in backup folder
                $filename = hash('md5', $backupData).".data";
                $fullfilename = HERISSON_BACKUP_DIR."/".$filename;
                file_put_contents($fullfilename, $backupData);
                
                // Insert localbackup into
                $backup            = new WpHerissonLocalbackups();
                $backup->friend_id = $friend->id;
                $backup->size      = strlen($backupData);
                $backup->filename  = $fullfilename;
                $backup->creation  = date('Y-m-d H:i:s');
                $backup->save();
                
                Network::reply(200);
                echo "1";
                exit;
            } else {
                Network::reply(417, HERISSON_EXIT);
            }
        } catch (Encryption\Exception $e) {
            Network::reply(417, HERISSON_EXIT);

        }

    }



    /**
     * Action to handle the sendBackup requests
     *
     * Handled via HTTP Response code
     *
     * @return void
     */
    function downloadbackupAction()
    {

        $signature  = post('signature');
        $url        = post('url');

        $friend = WpHerissonFriendsTable::getOneWhere("url=?", array($url));
        try {
            if (Encryption::i()->publicDecrypt($signature, $friend->public_key) == $url) {

                $backup = WpHerissonLocalbackupsTable::getOneWhere('friend_id=?', array($friend->id));
                Export::forceDownload($backup->filename, 'herisson.data');

            } else {
                Network::reply(417, HERISSON_EXIT);
            }
        } catch (Encryption\Exception $e) {
            Network::reply(417, HERISSON_EXIT);

        }

    }


    /**
     * Checks wether this site accept backups, and if there is enough rooms left
     *
     * Handled via HTTP Response code
     *
     * @return void
     */
    private function _checkBackup()
    {
        
        if ($this->options['acceptBackups'] == 0) {
            Network::reply(403, HERISSON_EXIT);
            exit;
        }

        $dirsize = Folder::getFolderSize(HERISSON_BACKUP_DIR);
        if ($dirsize > $this->options['backupFolderSize']) {
            Network::reply(406, HERISSON_EXIT);
            exit;
        }

    }


    /**
     * Action to handle the ask from another site
     *
     * Handled via HTTP Response code
     *
     * TODO: Handle Network replies as Exceptions
     *
     * @return void
     */
    function askAction()
    {

        if ($this->options['acceptFriends'] == 0) {
            Network::reply(403, HERISSON_EXIT);
        }
        $signature = post('signature');
        $f = new WpHerissonFriends();
        $f->url = post('url');
        $f->reloadPublicKey();
        if (Encryption::i()->publicDecrypt($signature, $f->public_key) == $f->url) {
            $f->getInfo();
            if ($this->options['acceptFriends'] == 2) {
                // Friend automatically accepted, so it's a 202 Accepted for further process response
                Network::reply(202);
                $f->is_active=1;
            } else {
                // Friend request need to be manually processed, so it's a 200 Ok response
                Network::reply(200);
                $f->b_wantsyou=1;
                $f->is_active=0;
            }
            $f->save();
        } else {
            Network::reply(417, HERISSON_EXIT);
        }
        exit;
    }


    /**
     * Action to handle the ask from another site
     *
     * TODO: Handle Network replies as Exceptions
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
        if ($tag) {
            $bookmarks = WpHerissonBookmarksTable::getTag(array($tag),true);
        } else if ($search) {
            $bookmarks = WpHerissonBookmarksTable::getSearch($search,true);
        } else {
            $bookmarks = WpHerissonBookmarksTable::getAll(true);
        }

        $this->view->bookmarks = $bookmarks;

        $this->view->title = $this->options['sitename'];

        $this->view->friends = WpHerissonFriendsTable::getWhere("is_active=1");
        foreach ($this->view->friends as $friendId => $friend) {
            $this->view->friendBookmarks[$friend->id] = $friend->retrieveBookmarks($_GET);

        }

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
        $this->view->infos = array(
            'sitename'   => $this->options['sitename'],
            'adminEmail' => $this->options['adminEmail'],
            'version' => HERISSON_VERSION,
        );
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
            if (Encryption::i()->publicDecrypt($signature, $f->public_key) == $url) {
                $f->b_youwant=0;
                $f->is_active=1;
                $f->save();
                Network::reply(200);
                echo "1";
                exit;
            } else {
                Network::reply(417, HERISSON_EXIT);
            }
        } catch (Encryption\Exception $e) {
            Network::reply(417, HERISSON_EXIT);

        }
    }


}



