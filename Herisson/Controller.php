<?php
/**
 * Controller 
 *
 * PHP Version 5.3
 *
 * @category Controller
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      None
 */

require_once "View.php";

/**
 * Class: HerissonController
 *
 * @category Controller
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      None
 */
class HerissonController
{

    /**
     * Action name (parsed from the URL)
     */
    public $action;

    /**
     * Controller name
     */
    public $name;

    /**
     * The Herisson view
     * 
     * @see HerissonView
     */
    public $view;

    /**
     * The Wordpress plugin options (got from get_option('HerissonOptions'))
     */
    public $options;

    /**
     * The application name
     */
    public $app;

    /**
     * Boolean whether we should display layout or not
     */
    public $layout;

    /**
     * Constructor
     *
     * Parse the action, set the default Action, 
     * Handle the layout, and create the view
     */
    function __construct()
    {
        $this->options = get_option('HerissonOptions');
        $path          = explode("/", $_SERVER['REQUEST_URI']);
        if (array_key_exists(2, $path) && strlen($path[2])) {
            $this->action = $path[2];
        } else {
            $this->action = "index";
        }
        $this->layout = true;
        $this->setView();
    }

    /**
     * Create the HerissonView
     *
     * Create a new HerissonView by default, by can be used to switch action view.
     *
     * @param string $action     the action name
     * @param string $controller the controller name
     *
     * @see HerissonView
     * @return void
     */
    protected function setView($action=null, $controller=null)
    {
        if ($action) {
            $this->action = $action;
        }
        if ($controller) {
            $this->name = $controller;
        }
        if (!is_a($this->view, "HerissonView")) {
            $this->view = new HerissonView($this->app, $this->name, $this->action);
        } else {
            $this->view->setAction($this->action);
            $this->view->setController($this->name);
        }
    }

    /**
     * Get the action method name according to the action name
     *
     * @param string $actionName the action name
     *
     * @return the method name of the action that should be called
     */
    protected function getActionName($actionName)
    {
        return $actionName."Action";
    }

    /**
     * Route the controller to the right action
     *
     * Calls the action method, and display the view
     * Exit if no layout must be display
     *
     * @return void
     */
    public function route()
    {
        if ($this->action) {
            $method = $this->getActionName($this->action);
            if (method_exists($this, $method)) {
                call_user_func(array($this, $method));
            } else {
                $this->indexAction();
            }
        } else {
            $this->indexAction();
        }

        $this->view->display();

        if (! $this->layout) {
            exit;
        }

    }

    /**
     * Debugging method
     *
     * Display controller properties
     * This method should be used for development only
     *
     * @return void
     */
    public function debug()
    {
        foreach (get_object_vars($this) as $attr=>$value) {
            if (is_string($value)) {
                print "$attr = $value<br/>";
            }
        }

    }

}


