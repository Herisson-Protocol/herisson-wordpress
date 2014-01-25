<?php
/**
 * HerissonTest
 *
 * PHP Version 5.3
 *
 * @category Test
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 */

namespace Herisson\Format;

use Herisson\FormatTest;
use Herisson\Model\WpHerissonBookmarksTable;

require_once __DIR__."/../../Env.php";

/**
 * Class: HerissonTest
 * 
 * Test HerissonEncryption class
 *
 * @category Test
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      PHPUnit_Framework_TestCase
 */
class HerissonTest extends FormatTest
{


    /**
     * Configuration
     *
     * Create sample data, and Encryption object
     *
     * @return void
     */
    protected function setUp()
    {
        $this->format = new Herisson();
        $this->sampleFile = __DIR__."/../../fixtures/format.herisson.json";
        parent::setUp();
    }


    /**
     * Test size of the export method
     * 
     * @return void
     */
    public function testExport()
    {
        ob_start();
        $this->format->export($this->_getBookmarks());
        $output = ob_get_clean();
        //file_put_contents($this->sampleFile, $output);
        $this->assertRegexp('/fdn/', $output);
        $bookmarks = json_decode($output, true);
        $this->assertCount(20, $bookmarks);
    }


    /**
     * Test size of the exportData method
     * 
     * @return void
     */
    public function testExportData()
    {
        $output = $this->format->exportData($this->_getBookmarks());
        $this->assertRegexp('/fdn/', $output);
        $bookmarks = json_decode($output, true);
        $this->assertCount(20, $bookmarks);
    }


    /**
     * Test import method
     * 
     * @return void
     */
    public function testImport()
    {
        WpHerissonBookmarksTable::truncate();
        $_FILES['import_file']['tmp_name'] = $this->sampleFile;
        $bookmarks = $this->format->import();
        $this->assertCount(20, $bookmarks);
    }


}

