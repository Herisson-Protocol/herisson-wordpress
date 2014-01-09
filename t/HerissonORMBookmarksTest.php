<?php
/**
 * HerissonORMBookmarksTest
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
 * Class: HerissonORMBookmarksTest
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
class HerissonORMBookmarksTest extends HerissonORMTest
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
     * Test counting all bookmarks
     *
     * @return void
     */
    public function testCountList()
    {
        $list = WpHerissonBookmarksTable::getAll();
        $this->assertSame(get_class($list), 'Doctrine_Collection');
        $this->assertCount(20, $list->toArray());
    }


    /**
     * Test counting all bookmarks
     *
     * @return void
     */
    public function testCountAll()
    {
        $nb = WpHerissonBookmarksTable::countAll();
        $this->assertEquals(20, $nb);
    }


    /**
     * Test counting bookmarks with an Url condition
     *
     * @return void
     */
    public function testCountWhereUrl()
    {
        // No bookmark with sample URL
        $nb = WpHerissonBookmarksTable::countWhere("url=?", array($this->sampleUrl));
        $this->assertEquals(0, $nb);

        // Adding one
        $f = new WpHerissonBookmarks();
        $f->setUrl($this->sampleUrl);
        $f->save();

        // Count should be 1
        $nb = WpHerissonBookmarksTable::countWhere("url=?", array($this->sampleUrl));
        $this->assertEquals(1, $nb);

        // Adding another one
        $f = new WpHerissonBookmarks();
        $f->setUrl($this->sampleUrl);
        $f->save();

        // List should contains 2 elements, with the sampleUrl
        $bookmarks = WpHerissonBookmarksTable::getWhere("url=?", array($this->sampleUrl));
        $this->assertSame(get_class($bookmarks), 'Doctrine_Collection');
        $this->assertCount(2, $bookmarks->toArray());
        foreach ($bookmarks as $bookmark) {
            $this->assertSame($bookmark->url, $this->sampleUrl);
        }
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
     * Test adding a new bookmark and delete it
     *
     * @return void
     */
    public function testSearch()
    {
        // Check it's saved in the DB
        $bookmarks = WpHerissonBookmarksTable::getSearch('example');
        $this->assertEquals(0, sizeof($bookmarks));

        // Create a sample bookmark
        $f              = new WpHerissonBookmarks();
        $f->url         = $this->sampleUrl;
        $f->save();

        $f              = new WpHerissonBookmarks();
        $f->title       = $this->sampleName;
        $f->save();

        $f              = new WpHerissonBookmarks();
        $f->description = $this->sampleDescription;
        $f->save();

        // Check it's saved in the DB
        $bookmarks = WpHerissonBookmarksTable::getSearch('example');
        $this->assertEquals(3, sizeof($bookmarks));

        // Delete it and verify it's not there anymore
        foreach ($bookmarks as $f) {
            $f->delete();
        }
        $bookmarks = WpHerissonBookmarksTable::getSearch('example');
        $this->assertEquals(0, sizeof($bookmarks));
    }





}

