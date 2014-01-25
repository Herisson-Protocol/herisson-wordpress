<?php
/**
 * Herisson Format extension
 *
 * @category Tools
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      None
 */

namespace Herisson\Format;

use Herisson\Export;
use Herisson\Model\WpHerissonBookmarks;

/**
 * Class to handle complete Herisson JSON bookmarks format
 *
 * @category Tools
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     https://github.com/Rivsen/firefox-json-boomark-read
 * @see      None
 */
class Herisson extends \Herisson\Format
{

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->name     = "Herisson (Complete format)";
        $this->type     = "file";
        $this->keyword  = "herisson";
        $this->filename = "herisson-bookmarks.json";
    }

    /**
     * Export bookmarks and send it to the user
     *
     * @param array $bookmarks a bookmarks array, made of WpHerissonBookmarks items
     *
     * @return void
     */
    public function export($bookmarks)
    {
        Export::forceDownloadContent($this->exportData($bookmarks), $this->filename);
    }

    /**
     * Generate JSON bookmarks file
     *
     * @param array $bookmarks a bookmarks array, made of WpHerissonBookmarks items
     *
     * @see WpHerissonBookmarks
     *
     * @return void
     */
    public function exportData($bookmarks)
    {
        $list = array();
        foreach ($bookmarks as $bookmark) {
            $list[] = $bookmark->toArray();
        }
        return json_encode($list);
    }


    /**
     * Handle the importation of JSON Herisson bookmarks
     *
     * Redirects to importList() to help the user decide which bookmarks to import
     *
     * @return void
     */
    function import()
    {
        $this->preImport();

        $bookmarks = json_decode($this->getFileContent(), 1);

        return $bookmarks;

    }



}


