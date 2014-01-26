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

use Herisson\Model\WpHerissonBookmarksTable;
use Herisson\Model\WpHerissonFriendsTable;
use Doctrine_Query;
use Herisson\Doctrine;
use Herisson\Message;

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
        $herissonBookmarks = Herisson\Format\Herisson::exportData($bookmarks);
        //$friend->sendBackup($herissonBookmarks);


        // Redirects to Backups list
        $this->indexAction();
        $this->setView('index');
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

        $this->view->nbBookmarks = WpHerissonBookmarksTable::countAll();

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


