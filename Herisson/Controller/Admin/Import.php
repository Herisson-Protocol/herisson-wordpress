<?php
/**
 * Import controller 
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
 * Class: HerissonControllerAdminImport
 *
 * @category Controller
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      HerissonControllerAdmin
 */
class HerissonControllerAdminImport extends HerissonControllerAdmin
{

    /**
     * Constructor
     *
     * Sets controller's name
     */
    function __construct()
    {
        $this->name = "import";
        parent::__construct();
    }

    /**
     * Action to export this site's bookmarks to a file
     *
     * Redirects to indexAction() if format is not supplied
     * Redirects to indexAction() if an unknown format is supplied
     * Dispatch to an Herisson\Export method according to given format
     *
     * @see HerissonFormat
     *
     * @throws HerissonFormatException if given export_format is unknown
     *
     * @return void
     */
    function exportAction()
    {
        $export_format = strtolower(post('export_format'));
        if (!$export_format) {
            $this->indexAction();
            $this->setView('index');
            return;
        }

        try {
            include_once __DIR__."/../../Export.php";
            $options = post('exportOptions');
            $where = array();
            $params = array();
            if (isset($options['private']) && !$options['private']) {
                $where[] = "is_public=?";
                $params[] = 1;
            }
            if (isset($options['keyword']) && $options['keyword']) {
                $where[] = '(t.name LIKE ? OR b.title LIKE ? OR b.url LIKE ?)';
                $params[] = "%".$options['keyword']."%";
                $params[] = "%".$options['keyword']."%";
                $params[] = "%".$options['keyword']."%";
            }
            $where = implode(' AND ', $where);
            $bookmarks = WpHerissonBookmarksTable::getWhere($where, $params);
            $format = HerissonFormat::getFormatByKey($export_format);
            $format->export($bookmarks);
        } catch(HerissonFormatException $e) {
            HerissonMessage::i()->addError($e->getMessage());
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
     * @throws HerissonFormatException if given export_format is unknown
     *
     * @return void
     */
    function importAction()
    {
        $import_format = strtolower(post('import_format'));
        if (!$import_format) {
            $this->indexAction();
            $this->setView('index');
            return;
        }

        $format = HerissonFormat::getFormatByKey($import_format);
        try {
            $bookmarks = $format->import();
            $this->view->format = $format;
            $this->importList($bookmarks);
        } catch(HerissonFormatException $e) {
            HerissonMessage::i()->addError($e->getMessage());
            $this->indexAction();
            $this->setView('index');
        }
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
        $this->setView('importList');
    }

    /**
     * Handle the validation of bookmarks to import after the user choose which bookmarks he wants to import
     *
     * @return void
     */
    function importValidateAction()
    {
        $bookmarks = post('bookmarks');
        $nb        = 0;
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

        $correctFormats = array();
        $formats = HerissonFormat::getList();

        // Check for problems in format list
        foreach ($formats as $format) {
            try {
                $format->check();
                if (!array_key_exists($format->keyword, $correctFormats)) {
                    $correctFormats[$format->keyword] = $format;
                } else {
                    $format1 = $correctFormats[$format->keyword];
                    HerissonMessage::i()
                        ->addError(sprintf(__('Format « %s » defined in « %s » already exists in format « %s ». It will be ignored.', HERISSON_TD),
                        $format->keyword, $format->name, $format1->name));
                }
            } catch (HerissonFormatException $e) {
                HerissonMessage::i()->addError($e->getMessage());
            }
        }

        $this->view->formatList = $correctFormats;

    }


}



