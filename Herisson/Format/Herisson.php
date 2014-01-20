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
        $this->name    = "Herisson (Complete format)";
        $this->type    = "file";
        $this->keyword = "herisson";
    }

    /**
     * Generate JSON bookmarks file and send it to the user
     *
     * @param array $bookmarks a bookmarks array, made of WpHerissonBookmarks items
     *
     * @see WpHerissonBookmarks
     *
     * @return void
     */
    public function export($bookmarks)
    {
        $list = array();
        foreach ($bookmarks as $bookmark) { 
            $list[] = $bookmark->toArray();
        }
        Herisson\Export::forceDownloadContent(json_encode($list), "herisson-bookmarks.json");
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
        $filename = $_FILES['import_file']['tmp_name'];
        $content = file_get_contents($filename);

        $bookmarks = json_decode($content, 1);

        foreach ($bookmarks as $i=>$bookmark) {
            $bookmarks[$i]['is_public'] = $bookmark['public'];
            $bookmarks[$i]['tags'] = implode(',', $bookmark['tags']);
            $bookmarks[$i]['favicon_image'] = "";
            $bookmarks[$i]['favicon_url'] = "";
        }
        return $bookmarks;

    }



}


