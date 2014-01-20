<?php
/**
 * Herisson\ControllerTest
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

require_once __DIR__."/../Env.php";


/**
 * Class: Herisson\ControllerTest
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
abstract class ControllerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The HerissonEncryption object
     */
    public $controller;


    /**
     * Configuration
     *
     * Create sample data, and Encryption object
     *
     * @return void
     */
    protected function setUp()
    {
        $this->sample     = "Hello World! This is a sample.";
    }

    
    /**
     * Test all action method of the controller
     *
     * Ignored right now.
     * 
     * @return void
     */
    public function ignoreTestCallAllActions()
    {
        $methodNames = get_class_methods($this->controller);
        foreach ($methodNames as $methodName) {
            if (!preg_match("#Action$#", $methodName)) {
                continue;
            }
            $method = new \ReflectionMethod(get_class($this->controller), $methodName);
            $method->invoke($this->controller);
        }
    }

    /**
     * Call an action method of the current controller
     *
     * @param string $actionName the name of the action to call (eg index)
     * 
     * @return void
     */
    public function callAction($actionName)
    {
        $methodName = $actionName."Action";
        $method = new \ReflectionMethod(get_class($this->controller), $methodName);
        $method->invoke($this->controller);
    }

}

