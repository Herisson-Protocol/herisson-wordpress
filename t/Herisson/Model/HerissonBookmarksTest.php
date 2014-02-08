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

namespace Herisson\Model;

use Herisson\ModelTest;
use Herisson\Shell;

require_once __DIR__."/../../Env.php";

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
class HerissonBookmarksTest extends ModelTest
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

        $this->fakeFields = array(
            'url'           => $this->sampleUrl,
            'hash'          => '4a50a998251d5f8ef307f0dbf977b981',
            'title'         => $this->sampleName,
            'description'   => $this->sampleDescription,
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

        $this->hashDir  = "4/4a/4a50a998251d5f8ef307f0dbf977b981";
        $this->dataPath = __DIR__."/../../../data/".$this->hashDir;

        ob_start();
    }

    /**
     * Put back initial configuration
     *
     * Set options as they were 
     *
     * @return void
     */
    public function tearDown()
    {
        // Cleanup getContentFromUrl data
        Shell::shellExec('rm', ' -rf '.$this->dataPath);

        parent::tearDown();
        ob_clean();
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
            'url'     => $this->sampleUrl,
        );
        $id = WpHerissonBookmarks::createBookmark($data);
        $this->assertGreaterThanOrEqual(1, $id);
        $bookmark = WpHerissonBookmarksTable::get($id);
        $this->assertEquals($bookmark->url, $this->sampleUrl);
        $this->assertEquals($bookmark->title, $this->sampleName);
        $this->assertEquals($bookmark->content, $this->sampleDescription);
    }


    /**
     * Test createBookmark static method, and verify we can't add a duplicate
     *
     * @return void
     */
    public function testCreateBookmarkDuplicate()
    {
        $data = array(
            'title'   => $this->sampleName,
            'content' => $this->sampleDescription,
            'url'     => $this->sampleUrl,
        );
        $id = WpHerissonBookmarks::createBookmark($data);
        $this->setExpectedException("Herisson\Model\Exception");

        $id = WpHerissonBookmarks::createBookmark($data);
    }


    /**
     * Test createBookmark static method, and verify url is mandatory
     *
     * @return void
     */
    public function testCreateBookmarkMissingUrl()
    {
        $data = array(
            'title'   => $this->sampleName,
            'content' => $this->sampleDescription,
        );
        $this->setExpectedException("Herisson\Model\Exception");
        $id = WpHerissonBookmarks::createBookmark($data);
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
            $this->assertEquals(get_class($bookmark), 'Herisson\\Model\\WpHerissonBookmarks');
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
        $datas = $this->fakeFields;
        $b = $this->_getBookmark($datas);

        $sql = array();
        foreach ($datas as $key => $value) {
            $sql[] = "$key=?";
        }
        // Check it's saved in the DB, with all parameters
        $bookmarks = WpHerissonBookmarksTable::getWhere(implode(' AND ', $sql),
            array_values($datas));
        $this->assertEquals(1, sizeof($bookmarks));

        // Retrieve the id
        $g = WpHerissonBookmarksTable::get($b->id);
        foreach ($datas as $key => $value) {
            $this->assertEquals($value, $g->$key);
        }

        // Cleanup
        $g->delete();

    }


    /**
     * Create a sample bookmark
     *
     * @param array $fields an array of fields/values to feed the bookmark with fake data
     *
     * @return a bookmark object
     */
    private function _getBookmark($fields=array())
    {
        // Create a sample bookmark
        $b     = new WpHerissonBookmarks();
        if (sizeof($fields)) {
            foreach ($fields  as $key => $value) {
                $b->$key = $value;
            }
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
     * Testing setProperties method with getFakeFields() method
     *
     * @return void
     */
    public function testSetProperties()
    {
        $properties = $this->fakeFields;
        $b = $this->_getBookmark();
        $b->setProperties($properties);
        $b->save();
        foreach ($properties as $property => $value) {
            $this->assertEquals($b->$property, $value);
        }
    }

    /**
     * Test that our fake fields is complete
     *
     * @return void
     */
    public function testThatFakeFieldsIsComplete()
    {
        $b = new WpHerissonBookmarks();
        $datas = $b->_data;
        // Except that id is not in the fake fields
        unset($datas['id']);
        $objectsProperties = array_keys($datas);
        $fakeProperties = array_keys($this->fakeFields);
        $this->assertEquals($objectsProperties, $fakeProperties);
    }

    /**
     * Basic test to call all maintenance method and check for Exception / Errors
     *
     * @return void
     */
    public function testMaintenance()
    {
        $b = $this->_getBookmark($this->fakeFields);
        $b->save();
        $b->maintenance();
    }

    /**
     * Test getFullContentFromUrl and check that content exists
     *
     * @return void
     */
    public function testGetFullContentFromUrl()
    {
        $b = $this->_getBookmark($this->fakeFields);
        $b->save();
        $b->getFullContentFromUrl();
        $this->assertTrue(file_exists($this->dataPath));
    }

    /**
     * Test getHashDir()
     *
     * @return void
     */
    public function testGetHashDir()
    {
        $b = $this->_getBookmark($this->fakeFields);
        $b->save();
        $this->assertEquals($b->getHashDir(), $this->hashDir);
    }


}

