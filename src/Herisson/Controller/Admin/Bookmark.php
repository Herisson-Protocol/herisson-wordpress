<?

require_once __DIR__."/../Admin.php";

class HerissonControllerAdminBookmark extends HerissonControllerAdmin {


    function __construct() {
        $this->name = "bookmark";
        parent::__construct();
    }

    function addAction() {
        $this->setView('edit');
        $this->editAction();
    }

    function deleteAction() {
        $id = intval(param('id'));
        if ($id>0) {
            $bookmark = WpHerissonBookmarksTable::get($id);
            $bookmark->delete();
        }

        # Redirect to Bookmarks list
        $this->indexAction();
        $this->setView('index');
    }

    function downloadAction() {
        $id = intval(param('id'));
        if ($id>0) {
            $bookmark = WpHerissonBookmarksTable::get($id);
            $bookmark->maintenance();
            $bookmark->captureFromUrl();

            $this->editiAction();
            $this->setView('edit');
        }
    }

    function editAction() {
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
            $bookmark->maintenance();
            $bookmark->captureFromUrl();

            $tags = explode(',',post('tags'));
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

    function indexAction() {
        $tag = get('tag');
        if ($tag) {
            $this->view->subtitle = __("Results for tag &laquo;&nbsp;".esc_html($tag)."&nbsp;&raquo;");
            $this->view->countAll = sizeof(WpHerissonBookmarksTable::getTag($tag));
            $this->view->bookmarks = WpHerissonBookmarksTable::getTag($tag,true);
        } else {
            $this->view->bookmarks = WpHerissonBookmarksTable::getAll(true);
            $this->view->countAll = sizeof(WpHerissonBookmarksTable::getAll());
        }
        $this->view->pagination = HerissonPagination::i()->getVars();
    }

    function tagCloudAction() {
        $this->view->tags = WpHerissonTagsTable::getAll();
        $this->layout = false;
    }

    function searchAction() {
        $search = get('search');
        $this->view->bookmarks = WpHerissonBookmarksTable::getSearch($search,true);
        $this->view->countAll = sizeof(WpHerissonBookmarksTable::getSearch($search));
        $this->view->subtitle = __("Search results for &laquo;&nbsp;".esc_html($search)."&nbsp;&raquo;");
        $this->view->pagination = HerissonPagination::i()->getVars();
        $this->setView('index');
    }


    function viewAction() {
        $id = intval(get('id'));
        if (!$id) {
            echo __("Error : Missing id\n",HERISSON_TD);
            exit;
        }
        $bookmark = WpHerissonBookmarksTable::get($id);
        if ($bookmark && $bookmark->content) {
            echo $bookmark->content;
        } else {
            echo sprintf(__("Error : Missing content for bookmark %s\n",HERISSON_TD),$bookmark->id);
        }
        exit;
    }



}


