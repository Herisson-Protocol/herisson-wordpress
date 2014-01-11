<?php
/**
 * HerissonBookmarksTest
 *
 * PHP Version 5.3
 *
 * @category Test
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 */

require_once __DIR__."/Env.php";

/**
 * Class: HerissonBookmarksTest
 * 
 * Test WpHerissonBookmarks class and ORM
 * Test bookmarks requests and validation
 *
 * @category Test
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      PHPUnit_Framework_TestCase
 */
class HerissonBookmarksTest extends HerissonORMTest
{

    /**
     * Configuration
     *
     * Create sample data, and herisson demo website
     * Clean up sample url bookmarks
     *
     * @return void
     */
    protected function setUp()
    {
        $this->table = 'wp_herisson_bookmarks';
        parent::setUp();

    }



    /**
     * Test adding a new bookmark and delete it
     *
     * @return void
     */
    public function testBookmarkStructure()
    {
        // Adding one
        $f = new WpHerissonBookmarks();
        $f->setUrl($this->sampleUrl);
        $f->save();

        $bookmarks = WpHerissonBookmarksTable::getWhere('url=?', array($this->sampleUrl));
        foreach ($bookmarks as $bookmark) {
            $this->assertEquals(get_class($bookmark), 'WpHerissonBookmarks');
            $this->assertTrue(is_array($bookmark->toArray()));
        }
    }


    /**
     * Test adding a new bookmark and delete it
     *
     * @return void
     */
    public function testAddBookmarkAndDelete()
    {
        // Create a sample bookmark
        $f = new WpHerissonBookmarks();
        $f->setUrl($this->sampleUrl);
        $f->save();

        // Check it's saved in the DB
        $bookmarks = WpHerissonBookmarksTable::getWhere('url=?', array($this->sampleUrl));
        $this->assertEquals(1, sizeof($bookmarks));

        // Delete it and verify it's not there anymore
        foreach ($bookmarks as $f) {
            $f->delete();
        }
        $bookmarks = WpHerissonBookmarksTable::getWhere('url=?', array($this->sampleUrl));
        $this->assertEquals(0, sizeof($bookmarks));
    }


    /**
     * Test setting and retrieving data
     *
     * @return void
     */
    public function testCreateSaveAndRetrieve()
    {
        $id = $b->id;

        // Check it's saved in the DB, with all parameters
        $bookmarks = WpHerissonBookmarksTable::getWhere(implode(' AND ', $sql),
            array_values($datas));
        $this->assertEquals(1, sizeof($bookmarks));

        // Retrieve the id
        $g = WpHerissonBookmarksTable::get($id);
        foreach ($datas as $key => $value) {
            $this->assertEquals($value, $g->$key);
        }

        // Cleanup
        $g->delete();

    }


    /**
     * Create a sample bookmark
     *
     * @return a bookmark object
     */
    private function _getBookmark()
    {
        // Create a sample bookmark
        $b     = new WpHerissonBookmarks();
        $datas = array(
            'url'           => 'url',
            'hash'          => 'hash',
            'title'         => 'title',
            'description'   => 'description',
            'content'       => 'content',
            'favicon_url'   => 'favicon_url',
            'favicon_image' => 'favicon_image',
            'is_public'     => 12,
            'is_binary'     => 34,
            'content_image' => 'content_image',
            'error'         => 56,
            'expires_at'    => '2012-12-12 12:12:12',
            'created'       => '2011-11-11 11:11:11',
            'updated'       => '2010-10-10 10:10:10',
            'type_id'       => 78,
            'content_type'  => 'content_type',
            'dirsize'       => 90,
        );

        $sql = array();
        foreach ($datas as $key => $value) {
            $b->$key = $value;
            $sql[] = "$key=?";
        }
        $b->save();

        return $b;

    }


    /**
     * Test the md5 hashing of the URL
     *
     * @return void
     */
    public function testHashMD5()
    {
        $b = $this->_getBookmark();
        $b->setHashFromUrl();
        $this->assertEquals(md5($b->url), $b->hash);
    }


    /**
     * Test the md5 hashing of the URL
     *
     * @return void
     */
    public function testHashUrl()
    {
        $b = $this->_getBookmark();
        $b->setUrl($this->sampleUrl);
        $this->assertEquals($b->url, $this->sampleUrl);
        $this->assertEquals(md5($b->url), $b->hash);
    }

    /**
     *
     * @return void
     */
    public function testBookmark2()
    {
    }

    /**
     *
     * @return void
     */
    public function testBookmark3()
    {
    }

    /**
     *
     * @return void
     */
    public function testBookmark4()
    {
    }

    /**
     *
     * @return void
     */
    public function testBookmark5()
    {
    }

    /**
     *
     * @return void
     */
    public function testBookmark6()
    {
    }


}

