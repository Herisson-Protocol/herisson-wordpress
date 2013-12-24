<?php
/**
 * Friend controller 
 *
 * @category Controller
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      HerissonControllerAdmin
 */

require_once __DIR__."/../Admin.php";

/**
 * Class: HerissonControllerAdminFriend
 *
 * @category Controller
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      HerissonControllerAdmin
 */
class HerissonControllerAdminFriend extends HerissonControllerAdmin
{

    /**
     * Constructor
     *
     * Sets controller's name
     */
    function __construct()
    {
        $this->name = "friend";
        parent::__construct();
    }

    /**
     * Action to add a new friend
     *
     * Redirect to editAction()
     *
     * @return void
     */
    function addAction()
    {
        $this->setView('edit');
        $this->editAction();
    }

    /**
     * Action to approve a new friend
     *
     * Redirect to editAction()
     *
     * @return void
     */
    function approveAction()
    {
        $id = intval(param('id'));
        if ($id>0) {
            $friend = WpHerissonFriendsTable::get($id);
            if ($friend->validateFriend()) {
                HerissonMessage::i()->addSucces(__("Friend has been notified of your approvement"));
            } else {
                HerissonMessage::i()->addError(__("Something went wrong while adding friendFriend has been notified of your approvement"));
            }
        }
        // Redirect to Friends list
        $this->indexAction();
        $this->setView('index');
    }

    /**
     * Action to delete a friend
     *
     * Redirect to indexAction()
     *
     * @return void
     */
    function deleteAction()
    {
        $id = intval(param('id'));
        if ($id>0) {
            $friend = WpHerissonFriendsTable::get($id);
            $friend->delete();
        }

        // Redirect to Friends list
        $this->indexAction();
        $this->setView('index');
    }

    /**
     * Action to edit a friend
     *
     * If POST method used, update the given friend with the POST parameters,
     * otherwise just display the friend properties
     *
     * @return void
     */
    function editAction()
    {
        $id = intval(param('id'));
        if (!$id) {
            $id = 0;
        }
        if (sizeof($_POST)) {
            $url = post('url');
            $alias = post('alias');

            $new = $id == 0 ? true : false;
            if ($new) {
                $friend = new WpHerissonFriends();
                $friend->is_active = 0;
            } else {
                $friend = WpHerissonFriendsTable::get($id);
            }
            $friend->alias = $alias;
            $friend->url = $url;
            if ($new) {
                $friend->getInfo();
                $friend->askForFriend();
            }
            $friend->save();
            if ($new) { 
                if ($new && $friend->is_active) {
                    HerissonMessage::i()->addSucces(__("Friend has been added and automatically validated"));
                } else {
                    HerissonMessage::i()->addSucces(__("Friend has been added, but needs to be validated by its owner"));
                }
            } else {
                HerissonMessage::i()->addSucces(__("Friend saved"));
            }
        }

        if ($id == 0) {
            $this->view->existing = new WpHerissonFriends();
        } else {
            $this->view->existing = WpHerissonFriendsTable::get($id);
        }
        $this->view->id = $id;
    }

    /**
     * Action to list friends
     *
     * This is the default action
     *
     * @return void
     */
    function indexAction()
    {
        $this->view->actives  = WpHerissonFriendsTable::getWhere("is_active=1");
        $this->view->youwant  = WpHerissonFriendsTable::getWhere("b_youwant=1");
        $this->view->wantsyou = WpHerissonFriendsTable::getWhere("b_wantsyou=1");
        $this->view->errors   = WpHerissonFriendsTable::getWhere("b_wantsyou!=1 and b_youwant!=1 and is_active!=1");
    }

    /**
     * Action to import friends
     *
     * Not implemented yet
     *
     * @return void
     */
    function importAction()
    {
        if ( !empty($_POST['login']) && !empty($_POST['password'])) {
        }
    }


}


