<?php
/**
 * WpHerissonBookmarks
 * 
 * PHP Version 5.3
 * 
 * @category Models
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 */

namespace Herisson\Model;

use Doctrine_Query;

use Herisson\Message;
use Herisson\Network;
use Herisson\Shell;

/**
 * ORM class to handle Bookmarks objects
 * 
 * @category Models
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 */
class WpHerissonBookmarks extends \BaseWpHerissonBookmarks
{

    public $prefix = null;
    public $tags = null;
    public $screenshot = "_screenshot.png";
    public $screenshotSmall  = "_screenshot_small.png";
    public $screenshotSmall0 = "_screenshot_small-0.png";

    /**
     * Setup method to initiate tables relations.
     *
     * @author Doctrine
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->hasMany('WpHerissonTags', array(
            'local' => 'id',
            'foreign' => 'bookmark_id'
        ));
    }

    /**
     * Create a new bookmark based on an url and options
     *
     * @param array $fields the fields parameters
     *
     * @throws Herisson\Model\Exception if bookmark is duplicate
     *
     * @return the id of the bookmark created
     */
    public static function createBookmark($fields=array())
    {

        if (!isset($fields['url'])) {
            throw new Exception(__("Missing Url. Can't create bookmark"));
        }
        $url = $fields['url'];
        if (WpHerissonBookmarksTable::checkDuplicate($url)) {
            throw new Exception(__("Ignoring duplicate entry : $url"));
        }
        $bookmark = new WpHerissonBookmarks();
        $bookmark->setProperties($fields);
        $bookmark->save();
        if (array_key_exists('tags', $fields) && $fields['tags']) {
            $bookmark->setTags($fields['tags']);
        }
        return $bookmark->id;
    }


    /** Properties **/
    /**
     * Set the URL and create the url hash
     *
     * @param string $url the url of the bookmark
     *
     * @return void
     */
    public function setUrl($url)
    {
        parent::_set('url', $url);
        $this->setHashFromUrl();
        // $this->checkUrl();
    }


    /**
     * Check the bookmark url, and set the error attribute if the bookmark doesn't exists anymore
     *
     * @param array $properties a list of properties to set for the bookmark
     *
     * @return void
     */
    public function setProperties($properties)
    {
        $fields = array_keys($this->_data);
        foreach ($properties as $property => $value) {
            if (in_array($property, $fields)) {
                $this->$property = $value;
            } else {
                error_log("Unknown property $property => $value");
            }
        }
    }



    /*******************
     *   Maintenance   *
     *******************/

    /**
     * Check the bookmark url, and set the error attribute it the bookmark doesn't exists anymore
     *
     * @return void
     */
    public function checkUrl()
    {
        $network = new Network();
        $status = $network->check($this->url);
        if ($status['error']) {
            $this->error = 1;
        }
    }

    /**
     * Start maintenance for this bookmark
     *
     * 1/ Check url
     * 2/ Get content from Url
     * 3/ Get title from content
     * 4/ Get favicon url from content
     * 5/ Get favicon content from favicon url
     * 6/ Get full content (css, image, js)
     * 7/ Set hash from url
     *
     * @param boolean $verbose flag to set mode verbose (default true)
     *
     * @return void
     */
    public function maintenance($verbose=true)
    {
        $this->checkUrl();
        $this->setHashFromUrl();
        $this->getContentFromUrl($verbose);
        $this->getTitleFromContent($verbose);
        $this->getFaviconUrlFromContent($verbose);
        $this->getFaviconImageFromUrl($verbose);
        $this->getFullContentFromUrl($verbose);
        $this->captureFromUrl($verbose);
        $this->save();
    }


    /**
     * Generate URL hash (md5)
     *
     * @return void
     */
    public function setHashFromUrl()
    {
        $this->_set('hash', md5($this->url));
    }


    /**
     * Parse page title from HTML content
     *
     * This method does nothing in the following cases:
     * - there is no content yet
     * - the title already exists
     * - it's a binary bookmark
     *
     * @param boolean $verbose flag to set mode verbose (default true)
     *
     * @return true if title was newly found, false otherwise
     */
    public function getTitleFromContent($verbose=true)
    {
        if (!$this->content || $this->title) {
            return false;
        }
        if ($this->is_binary) {
            return false; 
        }
        if (preg_match("#<title>([^<]*)</title>#", $this->content, $match)) {
            $this->title = $match[1];
            if ($verbose) {
                Message::i()->addSucces(sprintf(__("Setting title : %s", HERISSON_TD), $this->title));
            }
            return true;
        }
        return false;
    }


    /**
     * Parse HTML Content to get Favicon URL
     *
     * This method does nothing in the following cases:
     * - there is no content
     * - favicon_url is already set
     * - it's a binary bookmark
     *
     * @param boolean $verbose flag to set mode verbose (default true)
     *
     * @return true if a new valid favicon url is found, false otherwise
     */
    public function getFaviconUrlFromContent($verbose=true)
    {
        if (!$this->content && $this->favicon_url) {
            return false;
        }
        if ($this->is_binary) {
            return false;
        }
        $network = new Network();

        preg_match_all('#<link[^>]*href="([^"]*)"#', $this->content, $match);
        $parsedUrl = parse_url($this->url);

        $possibleFavicons = array();

        // We try to get it from the <link> tag
        foreach ($match[0] as $i=>$m) {
            if (preg_match("#(favicon|shortcut)#", $m)) {
                $faviconUrl = $match[1][$i];
                // Absolute path
                if (preg_match("#^/#", $faviconUrl)) {
                    $faviconUrl = $parsedUrl['scheme'].'://'.$parsedUrl['host'].$faviconUrl;
                } else if (preg_match("#https?://#", $faviconUrl)) {
                    // Full path
                } else {
                    // Relative path
                    $faviconUrl = dirname($this->url)."/".$faviconUrl;
                }
                $possibleFavicons[] = $faviconUrl;
            }
        }

        // We try to guess and get /favicon.ico
        $possibleFavicons[] = $parsedUrl['scheme'].'://'.$parsedUrl['host']."/favicon.ico";
        // We try to use google caching system.
        $possibleFavicons[] = "http://www.google.com/s2/favicons?domain=".$parsedUrl['host'];

        foreach ($possibleFavicons as $faviconUrl) {
            if (!$this->favicon_url) {
                $status = $network->check($faviconUrl);
                if (!$status['error']) {
                    $this->_set('favicon_url', $faviconUrl);
                    if ($verbose) {
                        Message::i()->addSucces(sprintf(__("Setting favicon url : %s", HERISSON_TD), $faviconUrl));
                    }
                    return true;
                }
            }
        }
        return false;
    }


    /**
     * Parse Favicon image from Favicon URL
     *
     * This method does nothing in the following cases:
     * - favicon_image already exists
     * - there is no favicon_url
     * - it's a binary bookmark
     *
     * @param boolean $verbose flag to set mode verbose (default true)
     *
     * @return true if a new valid favicon url is found, false otherwise
     */
    public function getFaviconImageFromUrl($verbose=true)
    {
        if (
             !$this->favicon_url
            || $this->favicon_image
            || $this->is_binary) {
            return false;
        }
        $network = new Network();
        try {
            $content = $network->download($this->favicon_url);
            $this->_set('favicon_image', base64_encode($content['data']));
            if ($verbose) {
                Message::i()->addSucces(__("Retrieving favicon image URL", HERISSON_TD));
            }
            return true;
        } catch (Network\Exception $e) {
            Message::i()->addError($e->getMessage());
        }
        return false;
    }


    /**
     * Get HTML content from URL
     *
     * Retrieve content via Curl simple HTTP request
     * Do nothing if the bookmark has $this->error=1
     *
     * @param boolean $verbose flag to set mode verbose (default true)
     *
     * @return true if the content is retrieve succesfully
     */
    public function getContentFromUrl($verbose=true)
    {
        $options = get_option('HerissonOptions');
        if (! $options['spiderOptionTextOnly']) {
            return false;
        }
        if ($this->error) {
            return false;
        }
        if (!$this->content) {
            $network = new Network();
            try {
                $content = $network->download($this->url);
                $this->_set('content_type', $content['type']);
                if (preg_match('#^text#', $content['type'])) {
                    $this->_set('content', $content['data']);
                    if ($verbose) {
                        Message::i()->addSucces(__("Setting content from URL", HERISSON_TD));
                    }
                } else {
                    $this->saveBinary($content);
                }
                return true;
                //$this->save();
            } catch (Network\Exception $e) {
                Message::i()->addError($e->getMessage());
                return false;
            }
        }
        return false;
    }


    /**
     * Get full content from URL
     *
     * @param boolean $verbose flag to set mode verbose (default true)
     *
     * @return void
     */
    public function getFullContentFromUrl($verbose=true)
    {
        $options = get_option('HerissonOptions');
        if (! $options['spiderOptionFullPage']) {
            return false;
        }
        $directory = $this->getDir();
        if ($this->hasFullContent()) {
            return false;
        }
        if ($this->createDir()) {
            Shell::shellExec("wget",
                "--no-parent --timestamping --convert-links --page-requisites --no-directories --no-host-directories ".
                "-erobots=off -P $directory ".'"'.$this->url.'"');
            $this->calculateDirSize();
            $urlData = parse_url($this->url);
            if (isset($urlData['path'])) {
                $file = $urlData['path'];
                Shell::shellExec("mv", "\"$directory/$file\" \"".$this->getFullContentFile()."\"");
                if ($verbose) {
                    Message::i()->addSucces(sprintf(__('<b>Downloading bookmark : <a href="%s">%s</a></b>', HERISSON_TD),
                        "/wp-admin/admin.php?page=herisson_bookmarks&action=edit&id=".$this->id, $this->title));
                }
            }
        }
    }


    /**
     * Create the screenshot of the webpage url
     *
     * Use the wkhtmltoimage tool to create the PNG image
     * If the screenshot has been created, create a thumbnail and calculate the new dir size.
     *
     * @param boolean $verbose wether this method should verbose messages
     *
     * @return void
     */
    public function captureFromUrl($verbose)
    {
        if (!$this->id
            || $this->error
            || !$this->hash
            || $this->is_binary) {
            return false;
        }

        $options = get_option('HerissonOptions');
        if (! $options['spiderOptionScreenshot']) {
            return false;
        }

        // return false if screenshot already exists
        if ($this->hasImage()) {
            return false;
        }
        $image = $this->getImage();
        $screenshotTool = WpHerissonScreenshotsTable::get($options['screenshotTool']);
        call_user_func($screenshotTool->fonction, $this->url, $image);
 
        if (file_exists($image) && filesize($image)) {
            herisson_screenshots_thumb($image, $this->getThumb());
            $this->content_image = $image;
            $this->calculateDirSize();
        } else {
            $this->content_image = null;
        }
        $this->save();
    }


    /**
     * Calculate the directory size of the full page downloaded content
     *
     * @return void
     */
    public function calculateDirSize()
    {
        $size = Shell::shellExec("du", " -b ".$this->getDir());
        $this->_set('dirsize', $size);
        $this->save();
    }


    /**
     * Save binary content into a file, for this bookmark
     *
     * @param string $content the binary content
     *
     * @return void
     */
    public function saveBinary($content)
    {
        $data = $content['data'];
        $type = $content['type'];
        $filename = preg_replace("#/#", ".", $type);
        $this->_set('content', $filename);
        $this->_set('is_binary', 1);
        $fh = fopen($this->getDir()."/".$filename, "w+b");
        fwrite($fh, $data);
        fclose($fh);
    }


    /**************
     *   Export   *
     **************/

    /**
     * Export the bookmark as an array with all fields
     *
     * @param boolean $deep      param to comply with strict standards Doctrine_Record::toArray()
     * @param boolean $prefixKey param to comply with strict standards Doctrine_Record::toArray()
     *
     * @return array the bookmark as an array
     */
    public function toArray($deep = true, $prefixKey = false)
    {
        return parent::toArray($deep, $prefixKey);
    }

    /**
     * Export the bookmark as an array with limited fields
     *
     * @return array the bookmark as an array
     */
    public function toSmallArray()
    {
        return array(
            "title"         => $this->title,
            "url"           => $this->url,
            "description"   => $this->description,
            "tags"          => $this->getTagsArray(),
        );
    }

    /**
     * Export the bookmark as a json string with toArray() structure
     *
     * @return string the JSON string for the bookmark datas
     */
    public function toJSON()
    {
        return json_encode($this->toArray());
    }


    /**************
     *    Tags    *
     **************/

    /**
     * Get the complete list of tags
     *
     * @return void
     */
    public function getTagsArray()
    {
        $tags = $this->getTags();
        $list = array();
        foreach ($tags as $tag) {
            $list[] = $tag->name;
        }
        return $list;
    }

    /**
     * Add a list of tags to the bookmark
     *
     * The given bookmarks can be one tag, coma separated tags, or an array
     *
     * @param mixed $new the new tag to add
     *
     * @return void
     */
    public function addTags($new)
    {
        if (!is_array($new)) {
            $new = explode(',', $new);
        }
        $current = $this->getTagsArray();
        $all     = array_unique(array_merge($current, $new));
        $this->setTags($all);
    }

    /**
     * Set the given list of tags, this replaces all the bookmarks tags with these
     *
     * @param array $tags the complete list of tags for this bookmark
     *
     * @return void
     */
    public function setTags($tags)
    {
        if (!is_array($tags)) {
            throw new HerissonException("setTags argument should be an array");
        }
        if (!$this->id) {
            $this->save();
        }
        $this->delTags();
        foreach ($tags as $tag) {
            if (!trim($tag)) {
                continue; 
            }
            $t              = new \WpHerissonTags();
            $t->name        = $tag;
            $t->bookmark_id = $this->id;
            $t->save();
        }
    }


    /**
     * Get all tags for the bookmark
     *
     * @return the list of WpHerissonTag objects
     */
    public function getTags()
    {
        if (!$this->id) {
            return array();
        }
        return Doctrine_Query::create()
            ->from('WpHerissonTags')
            ->where("bookmark_id=?")
            ->orderby("name")
            ->execute(array($this->id));
    }


    /**
     * Delete all tags of the bookmark
     *
     * @return void
     */
    public function delTags()
    {
        Doctrine_Query::create()
            ->delete()
            ->from('WpHerissonTags')
            ->where("bookmark_id=".$this->id)
            ->execute();
    }


    /***********************
     * Directories methods *
     ***********************/

    /**
     * Get the Web Url to view the bookmark
     *
     * @return string the full URL
     */
    public function getDirUrl()
    {
        return get_option('siteurl')."/wp-content/plugins/herisson/data/".$this->getHashDir();
    }

    /**
     * Get the URL of the thumbnail screenshot of the bookmark
     *
     * @return string the full URL of the thumbnail
     */
    public function getThumbUrl()
    {
        return $this->getDirUrl()."/".$this->getThumbName();
    }

    /**
     * Get the URL of the screenshot of the bookmark
     *
     * @return string the full URL of the screenshot
     */
    public function getImageUrl()
    {
        return $this->getDirUrl()."/".$this->screenshot;
    }

    /**
     * Get the dirname of bookmark files
     *
     * @return string the dirname
     */
    public function getDir()
    {
        return HERISSON_DATA_DIR.$this->getHashDir();
    }

    /**
     * Create the bookmark dir of the bookmark files
     *
     * @return boolean true if the directory was succesfully created, false otherwise
     */
    public function createDir()
    {
        if (!file_exists($this->getDir())) {
            // Create dir recursively
            mkdir($this->getDir(), 0775, true);
            return true;
        } else if (file_exists($this->getDir()) && !is_dir($this->getDir())) {
            Message::i()->addError(__("Can't create directory ".$this->getDir().". A file already exists", HERISSON_TD));
            return false;
        } else if (!is_writeable($this->getDir())) {
            Message::i()->addError(__("Directory ".$this->getDir()." exists, but is not writable.", HERISSON_TD));
            return false;
        }
        return true;
    }

    /**
     * Get the hash directory name
     *
     * Constructed with <1st letter>/<2 letters>/hash/
     * Eg with the hash : 098f6bcd4621d373cade4e832627b4f6
     * Dirname will be 0/09/098f6bcd4621d373cade4e832627b4f6
     *
     * @return the dirname of the hash
     */
    public function getHashDir()
    {
        $this->setHashFromUrl();
        return substr($this->hash, 0, 1)."/".substr($this->hash, 0, 2)."/".$this->hash;
    }

    /**
     * Get the thumbnail filename (in case the screenshot was to big and was splitted
     *
     * @return string the thumbnail name
     */
    public function getThumbName()
    {
        $thumb  = $this->getDir()."/".$this->screenshotSmall;
        $thumb0 = $this->getDir()."/".$this->screenshotSmall0;
        if (file_exists($thumb)) {
            return $this->screenshotSmall;
        } else if (file_exists($thumb0)) {
            return $this->screenshotSmall0;
        }
    }

    /**
     * Get full name of the thumbnail filename
     *
     * @return string the full thumbnail filename
     */
    public function getThumb()
    {
        return $this->getDir()."/".$this->getThumbName();
    }

    /**
     * Get full name of the screenshot filename
     *
     * @return string the full screenshot filename
     */
    public function getImage()
    {
        return $this->getDir()."/".$this->screenshot;
    }

    /**
     * Check if a screenshot exists for the bookmark
     *
     * @return boolean true if the screenshot exists, false otherwise
     */
    public function hasImage()
    {
        return file_exists($this->getImage());
    }


    /**
     * Check if the full content of the bookmark exists
     *
     * @return true if the bookmarks content exists, false otherwise
     */
    public function hasFullContent()
    {
        return file_exists($this->getFullContentFile());
    }


    /**
     * Get the filename of the full content file
     *
     * @return the filename for full content
     */
    public function getFullContentFile()
    {
        return $this->getDir()."/index.html";

    }

    /**
     * Save the bookmark into the database
     *
     * @return void
     */
    public function save(Doctrine_Connection $conn = null)
    {
        $this->setHashFromUrl();
        parent::save($conn);
    }

}
