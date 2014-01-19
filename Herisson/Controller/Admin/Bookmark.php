<?php
/**
 * Bookmark controller 
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
 * Class: HerissonControllerAdminBookmark
 *
 * @category Controller
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      HerissonControllerAdmin
 */
class HerissonControllerAdminBookmark extends HerissonControllerAdmin
{

    /**
     * Constructor
     *
     * Sets controller's name
     */
    function __construct()
    {
        $this->name = "bookmark";
        parent::__construct();
    }

    /**
     * Action to add a new bookmark
     *
     * Redirects to editAction()
     *
     * @return void
     */
    function addAction()
    {
        $this->setView('edit');
        $this->editAction();
    }

    /**
     * Action to add delete a bookmark
     *
     * Redirects to indexAction()
     *
     * @return void
     */
    function deleteAction()
    {
        $id = intval(param('id'));
        if ($id>0) {
            $bookmark = WpHerissonBookmarksTable::get($id);
            $bookmark->delete();
        }

        // Redirects to Bookmarks list
        $this->indexAction();
        $this->setView('index');
    }

    /**
     * Action to download a bookmark URL content
     *
     * Redirects to editAction()
     *
     * @return void
     */
    function downloadAction()
    {
        $id = intval(param('id'));
        if ($id>0) {
            $bookmark = WpHerissonBookmarksTable::get($id);
            $bookmark->maintenance();

            $this->editAction();
            $this->setView('edit');
        }
    }

    /**
     * Action to edit a bookmark
     *
     * If POST method used, update the given bookmark with the POST parameters,
     * otherwise just display the bookmark properties
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
            $bookmark = WpHerissonBookmarksTable::get($id);
            $bookmark->title = post('title');
            $bookmark->url = post('url');
            $bookmark->description = post('description');
            $bookmark->is_public = post('is_public');
            $bookmark->save();
            $id = $bookmark->id;
            $bookmark->maintenance();

            $tags = explode(',', post('tags'));
            $bookmark->setTags($tags);
        }

        if ($id == 0) {
            $this->view->existing = new WpHerissonBookmarks();
            $this->view->tags = array();
        } else {
            $this->view->existing = WpHerissonBookmarksTable::get($id);
            $this->view->tags = $this->view->existing->getTagsArray();
        }
        $this->view->id = $id;
    }

    /**
     * Action to list bookmarks
     *
     * This is the default action
     *
     * @return void
     */
    function indexAction()
    {
        $tag = get('tag');
        if ($tag) {
            $this->view->subtitle = __("Results for tag &laquo;&nbsp;".esc_html($tag)."&nbsp;&raquo;");
            $this->view->countAll = sizeof(WpHerissonBookmarksTable::getTag($tag));
            $this->view->bookmarks = WpHerissonBookmarksTable::getTag($tag, true);
        } else {
            $this->view->bookmarks = WpHerissonBookmarksTable::getAll(true);
            $this->view->countAll = sizeof(WpHerissonBookmarksTable::getAll());
        }
        $this->view->pagination = Herisson\Pagination::i()->getVars();
    }

    /**
     * Action to display the tags list
     *
     * @return void
     */
    function tagCloudAction()
    {
        $this->view->tags = WpHerissonTagsTable::getAll();
        $this->layout = false;
    }

    /**
     * Action to search a keyword through bookmarks
     *
     * @return void
     */
    function searchAction()
    {
        $search = get('search');
        $this->view->bookmarks = WpHerissonBookmarksTable::getSearch($search, true);
        $this->view->countAll = sizeof(WpHerissonBookmarksTable::getSearch($search));
        $this->view->subtitle = __("Search results for &laquo;&nbsp;".esc_html($search)."&nbsp;&raquo;");
        $this->view->pagination = Herisson\Pagination::i()->getVars();
        $this->setView('index');
    }

    /**
     * Action to display a bookmark content
     *
     * @return void
     */
    function viewAction()
    {
        $id = intval(get('id'));
        if (!$id) {
            echo __("Error : Missing id\n", HERISSON_TD);
            exit;
        }
        $bookmark = WpHerissonBookmarksTable::get($id);
        if ($bookmark && $bookmark->content) {
            echo $bookmark->content;
        } else {
            echo sprintf(__("Error : Missing content for bookmark %s\n", HERISSON_TD), $bookmark->id);
        }
        exit;
    }



}


