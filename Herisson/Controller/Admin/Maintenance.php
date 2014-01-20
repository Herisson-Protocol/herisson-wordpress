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

namespace Herisson\Controller\Admin;

use Herisson\Model\WpHerissonBookmarksTable;
use Herisson\Model\WpHerissonBookmarks;

require_once __DIR__."/../Admin.php";

/**
 * Class: Herisson\Controller\Admin\Maintenance
 *
 * @category Controller
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      HerissonControllerAdmin
 */
class Maintenance extends \Herisson\Controller\Admin
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
     * Display import and maintenance options page
     *
     * This is the default Action
     *
     * @return void
     */
    function indexAction()
    {
        if (post('maintenance')) {
            $condition = "
                LENGTH(favicon_url)=?   or favicon_url is null or
                LENGTH(favicon_image)=? or favicon_image is null or
                LENGTH(content)=?       or content is null or
                LENGTH(content_image)=? or content_image is null";

            $bookmarks_errors   = WpHerissonBookmarksTable::getWhere($condition, array(0, 0, 0, 0));
            foreach ($bookmarks_errors as $b) {
                $b->maintenance(false);
                //$b->captureFromUrl();
                $b->save();
            }
        }

        $bookmarks         = WpHerissonBookmarksTable::getAll();
        $this->view->total = sizeof($bookmarks);
        $favicon           = WpHerissonBookmarksTable::getWhere("LENGTH(favicon_image)=?   or favicon_image is null", array(0));
        $html_content      = WpHerissonBookmarksTable::getWhere("LENGTH(content)=?         or content is null", array(0));
        $full_content      = WpHerissonBookmarksTable::getWhere("LENGTH(content)=?         or content is null", array(0));
        $screenshot        = WpHerissonBookmarksTable::getWhere("LENGTH(content_image)=?   or content_image is null", array(0));
        $this->view->stats = array(
            'favicon'           => sizeof($favicon),
            'html_content'      => sizeof($html_content),
            'full_content'      => sizeof($full_content),
            'screenshot'        => sizeof($screenshot),
        );

    }


}


