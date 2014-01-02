<?php
/**
 * JSON Format extension
 *
 * @category Tools
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      None
 */


/**
 * Class to handle Firefox JSON bookmarks format
 *
 * @category Tools
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     https://github.com/Rivsen/firefox-json-boomark-read
 * @see      None
 */
class HerissonFormatJson extends HerissonFormat
{

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->name    = "JSON (Firefox format)";
        $this->type    = "file";
        $this->keyword = "json_firefox";
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
        HerissonExport::forceDownloadContent(json_encode($list), "herisson-bookmarks.json");
    }




}


