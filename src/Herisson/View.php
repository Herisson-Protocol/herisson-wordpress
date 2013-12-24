<?php
/**
 * View
 *
 * @category Controller
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      None
 */


/**
 * Class: HerissonView
 *
 * @category Controller
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      None
 */
class HerissonView
{

    /**
     * The application name
     */
    public $app;

    /**
     * The controller name
     */
    public $controller;

    /**
     * The action name
     */
    public $action;

    /**
     * The view file to be included
     */
    public $viewFile;

    /**
     * The Wordpression options array
     */
    public $options;

    /**
     * Constructor
     *
     * Set the basic properties
     *
     * @param string $app        the application name
     * @param string $controller the controller name
     * @param string $action     the action name
     */
    function __construct($app, $controller, $action)
    {
        $this->app = $app;
        $this->controller = $controller ? $controller : "index";
        $this->action = $action ? $action : "index";
        $this->options = get_option('HerissonOptions');
    }

    /**
     * Setter for the $action property
     *
     * @param string $action the action name
     *
     * @return void
     */
    function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * Setter for the $controller property
     *
     * @param string $controller the controller name
     *
     * @return void
     */
    function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * Display the view
     *
     * Include the file using the $app, $controller and $action properties
     * Set the view properties as variables
     *
     * @return void
     */
    function display()
    {
        $this->viewFile = HERISSON_BASE_DIR."views/".$this->app."/".$this->controller."/".$this->action.".php";
        foreach (get_object_vars($this) as $attr=>$value) {
            $$attr = $value;
        }
        echo "viewFile : $this->viewFile<br>";
        if (file_exists($this->viewFile)) {
            include $this->viewFile;
        }
    }

}

