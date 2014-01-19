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

namespace Herisson\Format;

/**
 * Class to handle Firefox JSON bookmarks format
 *
 * @category Tools
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     https://github.com/Rivsen/firefox-json-boomark-read
 * @link     http://docs.services.mozilla.com/sync/objectformats.html
 * @see      None
 */
class FirefoxJson extends \Herisson\Format
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
        $this->keyword = "firefox_json";
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

        $root = array(
            'title' => 'Herisson-export-'.date('Y-m-d'),
            'type' => 'text/x-moz-place-container',
            'children' => array(),
        );

        foreach ($bookmarks as $bookmark) { 
            $root['children'][] = array(
                'title' => $bookmark->title,
                'url'   => $bookmark->url,
                'type'  => 'text/x-moz-place',
                );
        }
        \Herisson\Export::forceDownloadContent(json_encode($root), "herisson-bookmarks.json");
    }

    /**
     * Handle the importation of Firefox JSON bookmarks
     *
     * @return a list of WpHerissonBookmarks
     */
    public function import()
    {
        $this->preImport();
        $filename = $_FILES['import_file']['tmp_name'];
        $items = json_decode(file_get_contents($filename), true);

        $bookmarks = array();
        if (isset($items['children'])) {
            $this->_parse($items['children'], $bookmarks);
        }

        return $bookmarks;
    }


    /**
     * Recursively parse items and children items to get text/x-moz-place bookmarks only
     *
     * Feed the $bookmarks passed in referenced
     *
     * @param array $items      json item to parse looking for bookmarks
     * @param array &$bookmarks array to fill with found bookmarks
     *
     * @return void
     */
    private function _parse($items, &$bookmarks)
    {
        foreach ($items as $item) {
            if (isset($item['type'])
                && isset($item['uri'])
                && !preg_match('#^place:#', $item['uri'])
                && $item['type'] == 'text/x-moz-place') {

                $bookmark = new WpHerissonBookmarks();
                $bookmark->url = $item['uri'];
                $bookmark->title = $item['title'];
                $bookmarks[] = $bookmark;

            }
            if (isset($item['children'])) {
                $this->_parse($item['children'], $bookmarks);
            }
        }
    }


}


