<?php
/**
 * Maintenance controller 
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
 * Class: HerissonControllerAdminMaintenance
 *
 * @category Controller
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      HerissonControllerAdmin
 */
class HerissonControllerAdminMaintenance extends HerissonControllerAdmin
{

    /**
     * Constructor
     *
     * Sets controller's name
     */
    function __construct()
    {
        $this->name = "maintenance";
        parent::__construct();
    }

    /**
     * Action to export this site's bookmarks to a file
     *
     * Redirects to indexAction() if format is not supplied
     * Redirects to indexAction() if an unknown format is supplied
     * Dispatch to an HerissonExport method according to given format
     *
     * @see HerissonExport
     *
     * @return void
     */
    function exportAction()
    {
        if (!post('format')) {
            $this->indexAction();
            $this->setView('index');
            exit;
        }

        include_once __DIR__."/../../Export.php";
        $bookmarks = WpHerissonBookmarksTable::getAll();

        switch (post('format')) {
        case 'firefox':
            HerissonExport::exportFirefox($bookmarks);
            break;
        case 'json':
            HerissonExport::exportJson($bookmarks);
            break;
        case 'csv':
            HerissonExport::exportCsv($bookmarks);
            break;
        default:
            $this->indexAction();
            $this->setView('index');
        }
    }


    /**
     * Action to import bookmarks into this site
     *
     * Redirects to indexAction() if format is not supplied
     * Redirects to indexAction() if an unknown format is supplied
     * Dispatch to the right method according to given format
     *
     * @return void
     */
    function importAction()
    {
        if (!post('import_source')) {
            $this->indexAction();
            $this->setView('index');
            exit; 
        }

        switch (post('import_source')) {
        case 'firefox':
            $this->importFirefoxAction();
            break;
        case 'json':
            $this->importJsonAction();
            break;
        default:
            $this->indexAction();
            $this->setView('index');
        }
    }

    /**
     * Handle the importation of Delicious bookmarks, from username/password provided by the user
     *
     * Redirects to importList() to help the user decide which bookmarks to import
     * Use external library DeliciousBrownies to talk to Delicious API
     *
     * @see DeliciousBrownies
     *
     * @return void
     */
    function importDeliciousAction()
    {
        $username = post('username_delicious');
        $password = post('password_delicious');
        if (!$username || !$password) {
            echo __("Delicious login and password not complete.", HERISSON_TD);
            $this->indexAction();
            $this->setView('index');
            exit;
        }
        include HERISSON_INCLUDES_DIR."delicious/DeliciousBrownies.php";
        $d = new DeliciousBrownies;
        $d->setUsername($username);
        $d->setPassword($password);
        // Call https://api.del.icio.us/v1/posts/all
        $deliciousBookmarks = $d->getAllPosts();

        if (!$deliciousBookmarks) {
            echo __("Someting went wrong while fetching Delicious bookmarks. (Eg. Wrong login/password, no bookmarks etc)", HERISSON_TD);
            exit;
        }

        $list = array();

        $page_title = __("Importation results from Delicious bookmarks", HERISSON_TD);

        foreach ($deliciousBookmarks as $b) {
            $bookmark = array();
            $bookmark['url'] = $b['href'];
            $bookmark['title'] = $b['description'];
            $bookmark['description'] = $b['extended'];
            $bookmark['is_public'] = $b['private'] == 'yes' ? 0 : 1;
            $bookmark['tags'] = preg_replace("/ +/", ",", $b['tag']);
            $bookmark['prefix'] = false;
            $bookmark['favicon_url'] = "";
            $bookmark['favicon_image'] = "";

            $list[] = $bookmark;
        }
        unset($deliciousBookmarks);
        $this->importList($list);

    }

    /**
     * Handle the importation of Firefox bookmarks
     *
     * Redirects to importList() to help the user decide which bookmarks to import
     * Use external library firefox/bookmarks.class.php to parse html files
     *
     * @see firefox/bookmarks.class.php
     *
     * @return void
     */
    function importFirefoxAction()
    {
        if (!isset($_FILES['import_file'])) { 
            echo __("Bookmarks file not found.", HERISSON_TD);
            $this->indexAction();
            $this->setView('index');
            exit;
        }
        include HERISSON_INCLUDES_DIR."firefox/bookmarks.class.php";
        $filename = $_FILES['import_file']['tmp_name'];
        // Parsing bookmarks file
        $bookmarks = new Bookmarks();
        $bookmarks->parse($filename);
        $bookmarks->bookmarksFileMd5 = md5_file($filename);

        $list = array();

        $page_title = __("Importation results from Firefox bookmarks", HERISSON_TD);

        $i=0;
        $spacer = "&nbsp;&nbsp;&nbsp;&nbsp;";
        while ($bookmarks->hasMoreItems()) {
            $item = $bookmarks->getNextElement();
            $bookmark = array();
            $bookmark['title'] = $item->name;

            if ($item->_isFolder) { 
                $space = str_repeat($spacer, $item->depth-1);
                $bookmark['prefix'] = $space;
                $bookmark['url'] = "";
                $bookmark['description'] = "";
                $bookmark['is_public'] = 1;
                $bookmark['favicon_image'] = "";
                $bookmark['favicon_url'] = "";
                $bookmark['tags'] = "";
            } else {
                $bookmark['url'] = $item->HREF;
                $bookmark['description'] = "";
                $bookmark['is_public'] = 1;
                $bookmark['favicon_image'] = $item->ICON_DATA;
                $bookmark['favicon_url'] = $item->ICON_URI;
                $bookmark['tags'] = "";
            }
            $list[] = $bookmark;
        }
        unset($bookmarks);
        $this->importList($list);

    }

    /**
     * Handle the importation of JSON Herisson bookmarks
     *
     * Redirects to importList() to help the user decide which bookmarks to import
     *
     * @return void
     */
    function importJsonAction()
    {
        if (!isset($_FILES['import_file'])) { 
            echo __("Bookmarks file not found.", HERISSON_TD);
            $this->indexAction();
            $this->setView('index');
            exit;
        }
        $filename = $_FILES['import_file']['tmp_name'];
        $content = file_get_contents($filename);

        $bookmarks = json_decode($content, 1);

        $page_title = __("Importation results from JSON bookmarks", HERISSON_TD);

        foreach ($bookmarks as $i=>$bookmark) {
            $bookmarks[$i]['is_public'] = $bookmark['public'];
            $bookmarks[$i]['tags'] = implode(',', $bookmark['tags']);
            $bookmarks[$i]['favicon_image'] = "";
            $bookmarks[$i]['favicon_url'] = "";
        }
        $this->importList($bookmarks);

    }


    /** 
     * Display the imported bookmarks list to make the user decide which bookmarks he wants to import into his Herisson site
     * 
     * @param array $bookmarks the list of bookmarks to display
     *
     * @return void
     */
    function importList($bookmarks)
    {
        $this->view->bookmarks = $bookmarks;
        $this->setView('importList.php');
    }

    /**
     * Handle the validation of bookmarks to import after the user choose which bookmarks he wants to import
     *
     * @return void
     */
    function importValidateAction()
    {
        $bookmarks = post('bookmarks');
        $nb = 0;
        foreach ($bookmarks as $bookmark) {
            if (array_key_exists('import', $bookmark) && $bookmark['import']) { 
                $nb++;
                $tags = array_key_exists('tags', $bookmark) ? explode(",", $bookmark['tags']) : array();
                if (!strlen($bookmark['url'])) {
                    print_r($bookmark);
                }
                WpHerissonBookmarksTable::createBookmark($bookmark['url'], array(
                    'favicon_url'=> array_key_exists('favicon_url', $bookmark) ? $bookmark['favicon_url'] : "",
                    'favicon_image'=>array_key_exists('favicon_image', $bookmark) ? $bookmark['favicon_image'] : "",
                    'title'=>$bookmark['title'],
                    'is_public'=>array_key_exists('private', $bookmark) && $bookmark['private'] ? 0 : 1,
                    'tags'=> $tags,
                ));
            }
        }
        echo '<p class="herisson-success">'.sprintf(__("Successfully add %s bookmarks !", HERISSON_TD), $nb).'</p>';
        $this->indexAction();
        $this->setView('index');

    }

    /**
     * Display import and maintenance options page
     *
     * This is the default Action
     *
     * @return void
     */
    function indexAction()
    {

    }

    /**
     * Display maintenance statistics page
     * 
     * This allows to start maintenance checks to
     *
     * @return void
     */
    function maintenanceAction()
    {

        if (post('maintenance')) {
            $condition = "
                LENGTH(favicon_url)=0 or favicon_url is null or
                LENGTH(favicon_image)=0 or favicon_image is null or
                LENGTH(content)=0 or content is null or
                LENGTH(content_image)=0 or content_image is null";

            $this->view->bookmarks_errors   = WpHerissonBookmarksTable::getWhere($condition);
            
        }
        $this->view->bookmarks_all              = WpHerissonBookmarksTable::getAll();
        $this->view->bookmarks_no_favicon_url   = WpHerissonBookmarksTable::getWhere("LENGTH(favicon_url)=0 or favicon_url is null");
        $this->view->bookmarks_no_favicon_image = WpHerissonBookmarksTable::getWhere("LENGTH(favicon_image)=0 or favicon_image is null");
        $this->view->bookmarks_no_content       = WpHerissonBookmarksTable::getWhere("LENGTH(content)=0 or content is null");
        $this->view->bookmarks_no_content_image = WpHerissonBookmarksTable::getWhere("LENGTH(content_image)=0 or content_image is null");

    }

}


