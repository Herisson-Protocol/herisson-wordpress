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

namespace Herisson\Model;

use Doctrine_Query;
use Doctrine_Table;
use Doctrine_Core;
use Herisson\Doctrine;

use Herisson\Pagination;
use Herisson\Folder;

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
            ->from('Herisson\\Model\\WpHerissonBookmarks')
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

    /*
    public static function getSearch($search, $paginate=false)
    {
        $q = Doctrine_Query::create();
        $q = self::addSearch($q, $params=array(), $search);
        return $q->execute($params
        return self::getWhere(implode(' OR ', $where), $params, $paginate);
    }

    public static function addSearch($query, &$params, $search) {
        $where = array(
            't.name LIKE ?',
            'b.title LIKE ?',
            'b.url LIKE ?',
            'b.description LIKE ?',
            //'b.content LIKE ?',
        );
        
        $params = array_merge($params,
            array(
                "%".$search."%",
                "%".$search."%",
                "%".$search."%",
                "%".$search."%",
                //"%".$search."%",
            )
        );
        $query->where(implode(' OR ', $where));
        return $query;
    }
     */

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
            ->from('Herisson\Model\WpHerissonBookmarks b')
            ->leftJoin('b.WpHerissonTags t')
            ->where($where);
        if ($paginate) {
            $pagination = Pagination::i()->getVars();
            $q->limit($pagination['limit'])->offset($pagination['offset']);
        }
        $bookmarks = $q->execute($values);
        return $bookmarks;
    }

    /**
     * Truncate the table, delete all bookmarks from database
     *
     * @return void
     */
    public static function truncate()
    {
        $bookmarks = self::getAll();
        foreach ($bookmarks as $bookmark) {
            $bookmark->delete();
        }
    }


    /**
     * Get one item with where paremeters
     *
     * @param array   $options the options array
     * @param integer $public  wether the bookmark is public or not
     *
     * @return the corresponding instance of WpHerissonFriends or a new one
     */
    public static function getBookmarksData($options, $public)
    {
        $params = array($public);
        $q = Doctrine_Query::create()
            ->from('Herisson\Model\WpHerissonBookmarks as b')
            ->where('is_public=?');
        $bookmarks = $q->execute($params);
        return $bookmarks;

        if (array_key_exists('tag', $options)) {
            $q->leftJoin('b.WpHerissonTags t')
                ->where("t.name=?");
            $params[] = $options['tag'];
        }

        if (array_key_exists('search', $options)) {
            $search = "%".$options['search']."%";
            $q->leftJoin('b.WpHerissonTags t')
                ->where("(t.name LIKE ? OR b.url like ? OR b.title LIKE ? OR b.description LIKE ? OR b.content LIKE ?)");
            $params = array_merge($params,
                array($search, $search, $search, $search, $search));
        }
        $bookmarks = $q->execute($params);
        return $bookmarks;
    }


    /**
     * Get size of the bookmarks table
     *
     * @return the formatted table size
     */
    public static function getTableSize()
    {
        $res = Doctrine::execute("SHOW TABLE STATUS Where Name like '%_bookmarks'");
        $tableInfo = $res->fetch();
        $size = $tableInfo['Data_length'];
        return Folder::formatSize($size);
    }



}
