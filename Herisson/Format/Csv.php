<?php
/**
 * CSV Format extension
 *
 * @category Tools
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      None
 */

namespace Herisson\Format;

use Herisson\Model\WpHerissonBookmarks;
use Herisson\Export;

/**
 * Class to handle Basic CSV format
 *
 * @category Tools
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      None
 */
class Csv extends \Herisson\Format
{

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->name      = "CSV (Basic format)";
        $this->type      = "file";
        $this->keyword   = "csv";
        $this->filename  = "herisson-bookmarks.csv";
        $this->delimiter = ';';
        $this->columns   = array(
            'title',
            'url',
        );
    }


    /**
     * Export bookmarks and send it to the user
     *
     * @param array $bookmarks a bookmarks array, made of WpHerissonBookmarks items
     *
     * @see WpHerissonBookmarks
     *
     * @return void
     */
    public function export($bookmarks)
    {
        Export::forceDownloadContent($this->exportData($bookmarks), $this->filename);
    }


    /**
     * Generate CSV bookmarks file
     *
     * @param array $bookmarks a bookmarks array, made of WpHerissonBookmarks items
     *
     * @see WpHerissonBookmarks
     *
     * @return void
     */
    public function exportData($bookmarks)
    {
        $filename = tempnam('/tmp/', 'csv');
        $fcsv  = fopen($filename, 'w+');

        //headers
        $line = array();
        foreach ($this->columns as $col) {
            $line[] = $col;
        }
        fputcsv($fcsv, $line, $this->delimiter);

        //bookmark lines
        foreach ($bookmarks as $bookmark) {
            $line = array();
            foreach ($this->columns as $col) {
                $line[] = $bookmark->{$col};
            }
            fputcsv($fcsv, $line, $this->delimiter);
        }
        fclose($fcsv);
        $content = file_get_contents($filename);
        unlink($filename);
        return $content;
    }


    /**
     * Handle the importation of CSV files
     *
     * @return a list of WpHerissonBookmarks
     */
    public function import()
    {
        $this->preImport();

        $fh        = fopen($this->getFilename(), 'r');
        $headers   = fgetcsv($fh, 0, $this->delimiter);
        $bookmarks = array();

        while (($line = fgetcsv($fh, 0, $this->delimiter)) !== false) {
            $bookmark = new WpHerissonBookmarks();
            //print_r($bookmark->toArray());
            foreach ($headers as $fieldNum => $header) {
                if (isset($bookmark->$header) && array_key_exists($fieldNum, $line)) {
                    $bookmark->$header = $line[$fieldNum];
                } else {
                    throw new Exception(__("Unknown column definition « $header » on the first line.", HERISSON_TD));
                }
            }
            $bookmarks[] = $bookmark;
        }
        fclose($fh);
        return $bookmarks;
    }


}


