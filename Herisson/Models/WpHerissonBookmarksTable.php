<?php
/**
 * WpHerissonBookmarksTable
 * 
 * PHP Version 5.3
 * 
 * @category Models
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 */

/**
 * WpHerissonBookmarksTable
 *
 * ORM class to handle Bookmarks Table
 * 
 * @category Models
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 */
class WpHerissonBookmarksTable extends Doctrine_Table
{
    /**
     * Returns an instance of this class.
     *
     * @return object WpHerissonBookmarksTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('WpHerissonBookmarks');
    }


    /**
     * Check if the given url already exists in the bookmark database
     *
     * @param string $url the url to test
     *
     * @return true if the bookmarks exists, false otherwise
     */
    public static function checkDuplicate($url)
    {

        $bookmarks = self::getWhere("hash=?", array(md5($url)));
        if (sizeof($bookmarks)) {
            return true;
        }
        return false;
    }

    /**
     * Create a new bookmark based on an url and options
     *
     * @param string $url     the bookmark url
     * @param array  $options the options parameters
     *
     * @throws HerissonModelException if bookmark is duplicate
     *
     * @return the id of the bookmark created
     */
    public static function createBookmark($url, $options=array())
    {

        if (self::checkDuplicate($url)) {
            throw new HerissonModelException("Ignoring duplicate entry : $url");
        }
        $bookmark = new WpHerissonBookmarks();
        $bookmark->url = $url;
        $bookmark->setProperties($options);
        $bookmark->save();
        if (array_key_exists('tags', $options) && $options['tags']) {
            $bookmark->setTags($options['tags']);
        }
        return $bookmark->id;
    }

    /**
     * Get a bookmark from the id
     *
     * @param integer $id the bookmark id
     *
     * @return WpHerissonBookmarks the bookmark object matching the id, or a new one
     */
    public static function get($id)
    {
        if (!is_numeric($id)) {
            return new WpHerissonBookmarks();
        }
        $bookmarks = self::getWhere("id=?", array($id));
        foreach ($bookmarks as $bookmark) {
            return $bookmark;
        }
        return new WpHerissonBookmarks();
    }

    /**
     * Count all the bookmarks in the table
     *
     * @return integer the number of total bookmarks
     */
    public static function countAll()
    {
        return self::countWhere("1=1");
    }

    /**
     * Count the bookmarks with a specific condition
     *
     * @param string $where  the condition
     * @param array  $values the values for the where conditions
     *
     * @return integer the number of bookmarks matching the condition
     */
    public static function countWhere($where, $values=array())
    {
        $bookmarks = Doctrine_Query::create()
            ->select('COUNT(*)')
            ->from('WpHerissonBookmarks')
            ->where($where)
            ->execute($values, Doctrine_Core::HYDRATE_NONE);
        return $bookmarks[0][0];
    }

    /**
     * Retrieve all bookmarks
     *
     * @param boolean $paginate wether we should paginate this select
     * 
     * @return a list of all WpHerissonBookmarks objects
     */
    public static function getAll($paginate=false)
    {
        return self::getWhere("1=1", null, $paginate);
    }


    /**
     * Search for a bookmark keyword
     *
     * @param string  $search   the keyword
     * @param boolean $paginate wether the result should use pagination (optional)
     *
     * @return the list of matching bookmark objects
     */
    public static function getSearch($search, $paginate=false)
    {
        $where = array(
            't.name LIKE ?',
            'b.title LIKE ?',
            'b.url LIKE ?',
            'b.description LIKE ?',
            //'b.content LIKE ?',
        );
        
        $params = array(
            "%".$search."%",
            "%".$search."%",
            "%".$search."%",
            "%".$search."%",
            //"%".$search."%",
        );
        return self::getWhere(implode(' OR ', $where), $params, $paginate);
    }

    /**
     * Search for bookmarks based on tag name
     *
     * @param string  $tag      the tag name
     * @param boolean $paginate wether the result should use pagination (optional)
     *
     * @return the list of matching bookmark objects
     */
    public static function getTag($tag, $paginate=false)
    {
        return self::getWhere("t.name = ?", $tag, $paginate);
    }


    /**
     * Search for bookmarks based on where condition
     *
     * @param string  $where    the where string
     * @param array   $values   the values to create the prepared request
     * @param boolean $paginate wether the result should use pagination (optional)
     *
     * @return the list of matching bookmark objects
     */
    public static function getWhere($where, $values, $paginate=false)
    {
        $q = Doctrine_Query::create()
            ->from('WpHerissonBookmarks b')
            ->leftJoin('b.WpHerissonTags t')
            ->where($where);
        if ($paginate) {
            $pagination = HerissonPagination::i()->getVars();
            $q->limit($pagination['limit'])->offset($pagination['offset']);
        }
        $bookmarks = $q->execute($values);
        return $bookmarks;
    }

}
