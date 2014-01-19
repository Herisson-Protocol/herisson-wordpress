<?php
/**
 * HTML Format extension
 *
 * @category Tools
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      None
 */


/**
 * Class to handle Firefox HTML bookmarks format
 * See HTML file format in the @link parameter
 *
 * @category Tools
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     http://msdn.microsoft.com/en-us/library/aa753582%28VS.85%29.aspx
 * @link     https://support.mozilla.org/fr/kb/exporter-marque-pages-firefox-fichier-html
 * @see      None
 */
class HerissonFormatFirefoxHtml extends HerissonFormat
{

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->name    = "HTML (Firefox format)";
        $this->type    = "file";
        $this->keyword = "firefox_html";
    }

    /**
     * Generate Firefox bookmarks file and send it to the user
     *
     * @param array $bookmarks a bookmarks array, made of WpHerissonBookmarks items
     *
     * @see WpHerissonBookmarks
     *
     * @return void
     */
    public function export($bookmarks)
    {
         //$bookmarks = WpHerissonBookmarksTable::getAll();
         $now       = time();
         $name      = "Herisson bookmarks ".date('Y-m-d H-i-s');
         $content   = '
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Bookmarks</title>
<h1>Bookmarks menu</h1>

<dl><p>
    <dt><h3 add_date="'.$now.'" last_modified="'.$now.'">'.$name.'</h3>
    <dl><p>';
        foreach ($bookmarks as $bookmark) {
            $content .= '<DT><A HREF="'.$bookmark->url.'" ADD_DATE="'.$now.'"'
                .' LAST_MODIFIED="'.$now.'" ICON_URI="'.$bookmark->favicon_url.'"'
                .' ICON="data:image/png;base64,'.$bookmark->favicon_image.'">'.$bookmark->title.'</A>'."\n"
                .'  <dd>'.$bookmark->description.' ';
        }
        $content .= '</dl>
        </dl>
        ';

        Herisson\Export::forceDownloadContent($content, "herisson-bookmarks-firefox.html");
    }


    /**
     * Handle the importation of Firefox HTML bookmarks
     *
     * Use external library firefox/bookmarks.class.php to parse html files
     *
     * @see firefox/bookmarks.class.php
     *
     * @return a list of WpHerissonBookmarks
     */
    public function import()
    {
        $this->preImport();
        include HERISSON_VENDOR_DIR."firefox/bookmarks.class.php";
        $filename = $_FILES['import_file']['tmp_name'];
        // Parsing bookmarks file
        $bookmarks = new Bookmarks();
        $bookmarks->parse($filename);
        $bookmarks->bookmarksFileMd5 = md5_file($filename);

        $list = array();

        //$page_title = __("Importation results from Firefox bookmarks", HERISSON_TD);

        $i = 0;
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
        return $list;
    }


}


