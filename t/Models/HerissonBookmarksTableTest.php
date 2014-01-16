<?php
/**
 * HerissonBookmarksTableTest
 *
 * PHP Version 5.3
 *
 * @category Test
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 */

require_once __DIR__."/../Env.php";

/**
 * Class: HerissonBookmarksTableTest
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
class HerissonBookmarksTableTest extends HerissonModelTest
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
     * Test countAll and getAll are equals
     *
     * @return void
     */
    public function testCountAllEqualsWhereAll()
    {
        $nb = WpHerissonBookmarksTable::countAll();
        $list = WpHerissonBookmarksTable::getAll();
        $this->assertEquals(sizeof($list), $nb);
    }


    /**
     * Test countWhereUrl and getWherUrl are equals
     *
     * @return void
     */
    public function testCountWhereUrlEqualsWhereUrl()
    {   
        $nb = WpHerissonBookmarksTable::countWhere("url=?", array($this->sampleUrl));
        $list = WpHerissonBookmarksTable::getWhere("url=?", array($this->sampleUrl));
        $this->assertEquals(sizeof($list), $nb);

        // Adding one
        $f = new WpHerissonBookmarks();
        $f->setUrl($this->sampleUrl);
        $f->save();

        $nb = WpHerissonBookmarksTable::countWhere("url=?", array($this->sampleUrl));
        $list = WpHerissonBookmarksTable::getWhere("url=?", array($this->sampleUrl));
        $this->assertEquals(sizeof($list), $nb);
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
     * Test checkDuplicate method
     *
     * @return void
     */
    public function testCheckDuplicate()
    {
        $duplicate = WpHerissonBookmarksTable::checkDuplicate($this->sampleUrl);
        $this->assertFalse($duplicate);

        $f              = new WpHerissonBookmarks();
        $f->title       = $this->sampleName;
        $f->save();

        $duplicate = WpHerissonBookmarksTable::checkDuplicate($this->sampleUrl);
        $this->assertFalse($duplicate);

        // Create a sample bookmark
        $f              = new WpHerissonBookmarks();
        $f->url         = $this->sampleUrl;
        $f->save();

        $duplicate = WpHerissonBookmarksTable::checkDuplicate($this->sampleUrl);
        $this->assertTrue($duplicate);

        $bookmarks = WpHerissonBookmarksTable::getWhere("url=?", array($this->sampleUrl));
        $this->assertEquals(1, sizeof($bookmarks));

        // Delete it and verify it's not there anymore
        foreach ($bookmarks as $f) {
            $f->delete();
        }

        $duplicate = WpHerissonBookmarksTable::checkDuplicate($this->sampleUrl);
        $this->assertFalse($duplicate);
    }


    /**
     * Test getting an id
     *
     * @return void
     */
    public function testGetId()
    {
        // Create a sample bookmark
        $f              = new WpHerissonBookmarks();
        $f->url         = $this->sampleUrl;
        $f->title       = $this->sampleName;
        $f->description = $this->sampleDescription;
        $f->save();

        $g = WpHerissonBookmarksTable::get($f->id);
        $this->assertTrue(is_numeric($g->id));
        $this->assertEquals($f->id, $g->id);
        $this->assertEquals(get_class($g), 'WpHerissonBookmarks');
        $this->assertEquals($f->url, $g->url);
        $this->assertEquals($f->title, $g->title);
        $this->assertEquals($f->description, $g->description);

    }


    /**
     * Test getting an null object
     *
     * @return void
     */
    public function testGetNull()
    {
        $f = WpHerissonBookmarksTable::get(null);
        $this->assertEquals(get_class($f), 'WpHerissonBookmarks');
        $this->assertEmpty($f->url);
        $this->assertEmpty($f->title);
        $this->assertEmpty($f->description);

        $f = WpHerissonBookmarksTable::get(123456789);
        $this->assertEquals(get_class($f), 'WpHerissonBookmarks');
        $this->assertEmpty($f->url);
        $this->assertEmpty($f->title);
        $this->assertEmpty($f->description);

    }


    /**
     * Test that inserting a duplicate fail
     *
     * @return void
     */
    public function testGetIdWithDuplicates()
    {
        // Create a sample bookmark
        $f              = new WpHerissonBookmarks();
        $f->url         = $this->sampleUrl;
        $f->title       = $this->sampleName;
        $f->description = $this->sampleDescription;
        $f->save();

        // Create a duplicate
        $g              = new WpHerissonBookmarks();
        $g->id          = $f->id;
        $g->url         = $this->sampleUrl;
        $g->title       = $this->sampleName;
        $g->description = $this->sampleDescription;

        $this->setExpectedException('Doctrine_Connection_Mysql_Exception');
        $g->save();

    }


    /**
     * Test getTag method
     *
     * @return void
     */
    public function testGetTagSample()
    {
        $bookmarks = WpHerissonBookmarksTable::getTag($this->sampleTag);
        $this->assertEquals(get_class($bookmarks), 'Doctrine_Collection');
        $this->assertCount(0, $bookmarks);

        // Create a sample bookmark
        $f              = new WpHerissonBookmarks();
        $f->url         = $this->sampleUrl;
        $f->title       = $this->sampleName;
        $f->description = $this->sampleDescription;
        $f->addTags('helloworld');
        $f->save();

        $bookmarks = WpHerissonBookmarksTable::getTag($this->sampleTag);
        $this->assertEquals(get_class($bookmarks), 'Doctrine_Collection');
        $this->assertCount(0, $bookmarks);

        // Create a sample bookmark
        $f              = new WpHerissonBookmarks();
        $f->url         = $this->sampleUrl;
        $f->title       = $this->sampleName;
        $f->description = $this->sampleDescription;
        $f->addTags($this->sampleTag);
        $f->save();

        $bookmarks = WpHerissonBookmarksTable::getTag($this->sampleTag);
        $this->assertEquals(get_class($bookmarks), 'Doctrine_Collection');
        $this->assertCount(1, $bookmarks);

        // Create a sample bookmark
        $f              = new WpHerissonBookmarks();
        $f->url         = $this->sampleUrl;
        $f->title       = $this->sampleName;
        $f->description = $this->sampleDescription;
        $f->addTags($this->sampleTag);
        $f->save();

        $bookmarks = WpHerissonBookmarksTable::getTag($this->sampleTag);
        $this->assertEquals(get_class($bookmarks), 'Doctrine_Collection');
        $this->assertCount(2, $bookmarks);

    }


    /**
     * Test checkDuplicate method
     *
     * @return void
     */
    public function testGetTag()
    {
        // TODO
    }


    /**
     * Test checkDuplicate method
     *
     * @return void
     */
    public function testCreateBookmark()
    {

        $data = array(
            'title'   => $this->sampleName,
            'content' => $this->sampleDescription,
        );
        $id = WpHerissonBookmarksTable::createBookmark($this->sampleUrl, $data);
        $this->assertGreaterThanOrEqual(1, $id);
        $bookmark = WpHerissonBookmarksTable::get($id);
        $this->assertEquals($bookmark->url, $this->sampleUrl);
        $this->assertEquals($bookmark->title, $this->sampleName);
        $this->assertEquals($bookmark->content, $this->sampleDescription);


    }

    /**
     * Test checkDuplicate method
     *
     *
     * @return void
     */
    public function testCreateBookmarkDuplicate()
    {
        $data = array(
            'title'   => $this->sampleName,
            'content' => $this->sampleDescription,
        );
        $id = WpHerissonBookmarksTable::createBookmark($this->sampleUrl, $data);
        $this->setExpectedException("HerissonModelException");

        $id = WpHerissonBookmarksTable::createBookmark($this->sampleUrl, $data);


    }


    /**
     * Test checkDuplicate method
     *
     * @return void
     */
    public function testCheckDuplicate4()
    {
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

