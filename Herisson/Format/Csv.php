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
class HerissonFormatCsv extends HerissonFormat
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
        $this->delimiter = ';';
        $this->columns   = array(
            'title',
            'url',
        );
    }



    /**
     * Generate CSV bookmarks file and send it to the user
     *
     * @param array $bookmarks a bookmarks array, made of WpHerissonBookmarks items
     *
     * @see WpHerissonBookmarks
     *
     * @return void
     */
    public function export($bookmarks)
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
        Herisson\Export::forceDownload($filename, "herisson-bookmarks.csv");
        unlink($filename);
    }


    /**
     * Handle the importation of CSV files
     *
     * @return a list of WpHerissonBookmarks
     */
    public function import()
    {
        $this->preImport();

        $filename  = $_FILES['import_file']['tmp_name'];
        $fh        = fopen($filename, 'r');
        $headers   = fgetcsv($fh, 0, $this->delimiter);
        $bookmarks = array();

        while (($line = fgetcsv($fh, 0, $this->delimiter)) !== false) {
            $bookmark = new WpHerissonBookmarks();
            //print_r($bookmark->toArray());
            foreach ($headers as $fieldNum => $header) {
                if (isset($bookmark->$header) && array_key_exists($fieldNum, $line)) {
                    $bookmark->$header = $line[$fieldNum];
                } else {
                    throw new HerissonFormatException(__("Unknown column definition « $header » on the first line.", HERISSON_TD));
                }
            }
            $bookmarks[] = $bookmark;
        }
        fclose($fh);
        return $bookmarks;
    }


}


