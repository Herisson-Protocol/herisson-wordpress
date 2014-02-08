<?php
/**
 * Backup controller 
 *
 * @category Controller
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      HerissonControllerAdmin
 */

namespace Herisson\Controller\Admin;

use Doctrine_Query;
use Herisson\Doctrine;
use Herisson\Export;
use Herisson\Message;
use Herisson\Model\WpHerissonBackups;
use Herisson\Model\WpHerissonBackupsTable;
use Herisson\Model\WpHerissonBookmarksTable;
use Herisson\Model\WpHerissonFriendsTable;

require_once __DIR__."/../Admin.php";

/**
 * Class: HerissonControllerAdminBackup
 *
 * @category Controller
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      HerissonControllerAdmin
 */
class Backup extends \Herisson\Controller\Admin
{

    /**
     * Constructor
     *
     * Sets controller's name
     */
    function __construct()
    {
        $this->name = "backup";
        parent::__construct();
    }


    /**
     * Add a new backup
     *
     * Start a backup of all bookmarks to a remote friend
     *
     * @return void
     */
    function addAction()
    {
        $friend = WpHerissonFriendsTable::get(post('friend_id'));
        if (! $friend->id) {
            Message::i()->addError(__("Friend could not be found", HERISSON_TD));
            $this->indexAction();
            $this->setView('index');
            return;
        }

        $acceptsBackups = $friend->acceptsBackups();
        switch ($acceptsBackups) {
        case 0:
            Message::i()->addError(__("Friend doesn't accept backups", HERISSON_TD));
            $this->indexAction();
            $this->setView('index');
            return;
        case 1:
            Message::i()->addSucces(__("Friend accepts backups", HERISSON_TD));
            break;
        case 2:
            Message::i()->addError(__("Friend backup directory is full", HERISSON_TD));
            $this->indexAction();
            $this->setView('index');
            return;
        }

        $bookmarks = WpHerissonBookmarksTable::getAll();
        include_once HERISSON_BASE_DIR."/Herisson/Format/Herisson.php";
        $format = new \Herisson\Format\Herisson();
        $herissonBookmarks = $format->exportData($bookmarks);
        //print_r($herissonBookmarks);
        $res = $friend->sendBackup($herissonBookmarks);
        //echo $res;
        if ($res) {
            // TODO : Delete backups from that friend before adding a new one
            $backup            = new WpHerissonBackups();
            $backup->friend_id = $friend->id;
            $backup->size      = strlen($herissonBookmarks);
            $backup->nb        = sizeof($bookmarks);
            $backup->creation  = date('Y-m-d H:i:s');
            $backup->save();
        }


        // Redirects to Backups list
        $this->indexAction();
        $this->setView('index');
    }


    /**
     * Download a backup from friend
     *
     * @return void
     */
    private function _retrieve()
    {

        $friend = WpHerissonFriendsTable::get(get('id'));
        if (! $friend->id) {
            Message::i()->addError(__("Friend could not be found", HERISSON_TD));
            $this->indexAction();
            $this->setView('index');
            return;
        }

        return $friend->downloadBackup();

    }


    /**
     * Download a backup from a friend
     *
     * @return void
     */
    function downloadAction()
    {
        $data         = $this->_retrieve();
        $this->layout = false;
        // FIXME This fails
        Export::forceDownloadContent($data, 'herisson.tar.gz');

    }


    /**
     * Download and import a backup from a friend
     *
     * @return void
     */
    function importAction()
    {
        $data       = $this->_retrieve();
        $bookmarks  = json_decode($data, 1);
        $controller = new \Herisson\Controller\Admin\Import();
        $controller->importList($bookmarks);
        $controller->route();
    }


    /**
     * Action to list existing backups
     *
     * This is the default action
     *
     * @return void
     */
    function indexAction()
    {
        $this->view->backups = \Doctrine_Query::create()
            ->from('Herisson\Model\WpHerissonBackups b')
            ->execute();

        $this->view->localbackups = \Doctrine_Query::create()
            ->from('Herisson\Model\WpHerissonLocalbackups b')
            ->execute();

        $this->view->nbBookmarks   = WpHerissonBookmarksTable::countAll();
        $this->view->sizeBookmarks = WpHerissonBookmarksTable::getTableSize();
        
        $friends = \Doctrine_Query::create()
            ->from('Herisson\Model\WpHerissonFriends f')
            ->orderby('name')
            ->execute();
        $this->view->friends = array();
        foreach ($friends as $friend) {
            $this->view->friends[$friend->id] = $friend;
        }
    }

}


