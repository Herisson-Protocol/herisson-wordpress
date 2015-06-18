<?php
/**
 * Herisson\Controller\Front\IndexTest
 *
 * PHP Version 5.3
 *
 * @category Test
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 */

namespace Herisson\Controller\Front;

use Herisson\ControllerTest;

require_once __DIR__."/../../../Env.php";


/**
 * Class: Herisson\Controller\Front\IndexTest
 * 
 * Test Herisson\Controller classes
 *
 * @category Test
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      PHPUnit_Framework_TestCase
 */
class IndexTest extends ControllerTest
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
        $this->controller = new Index();
    }

    
    /**
     * Test index Action
     * 
     * @return void
     */
    public function testIndexAction()
    {
        $this->callAction('index');
    }

    /**
     * Test index Action
     * 
     * @return void
     */
    public function testInfoAction()
    {
        $this->callAction('info');
    }

}

