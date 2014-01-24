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

require_once __DIR__."/Env.php";


/**
 * Class: HerissonTest
 * 
 * Test Herisson classes
 *
 * @category Test
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      PHPUnit_Framework_TestCase
 */
class HerissonTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The Herisson object
     */
    public $herisson;


    /**
     * Configuration
     *
     * Create Herisson object
     *
     * @return void
     */
    protected function setUp()
    {
        $this->herisson = new Herisson();
    }

    
    /**
     * Test all action method of the controller
     *
     * @return void
     */
    public function testAddPages()
    {
        Herisson::addPages();
    }

    /**
     * Test all action method of the controller
     *
     * @return void
     */
    public function testInit()
    {
        Herisson::init();
    }

    /**
     * Test all action method of the controller
     *
     * @return void
     */
    public function testRouter()
    {
        $_SERVER['REQUEST_URI'] = '/bookmarks/';
        //Herisson::router();
    }

}

