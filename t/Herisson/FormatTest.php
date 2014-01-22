<?php
/**
 * Format
 *
 * PHP Version 5.3
 *
 * @category Test
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 */

namespace Herisson;

use Herisson\Model\WpHerissonBookmarksTable;
use Herisson\ModelTest;

require_once __DIR__."/../Env.php";

/**
 * FormatTest
 * 
 * @category Test
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      PHPUnit_Framework_TestCase
 */
class FormatTest extends ModelTest
{

    /**
     * Format object
     */
    public $format;

    /**
     * Short text sample
     */
    public $sample;

    /**
     * Long text sample
     */
    public $sampleLong;

    /**
     * File with sample content
     */
    public $sampleFile;

    /**
     * Configuration
     *
     * Create sample data, and Encryption object
     *
     * @return void
     */
    protected function setUp()
    {
        $this->table = 'wp_herisson_bookmarks';

        $this->sample     = "Hello World! This is a sample.";
        $this->sampleLong = "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?";
        parent::setUp();
    }


    /**
     * Method to get bookmarks
     * 
     * @return void
     */
    public function _getBookmarks()
    {
        $bookmarks = WpHerissonBookmarksTable::getAll(true);
        $this->assertTrue(is_array($bookmarks->toArray()));
        $this->assertCount(20, $bookmarks);
        return $bookmarks;
    }




}

