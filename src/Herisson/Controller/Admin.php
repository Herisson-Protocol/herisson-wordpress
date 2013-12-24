<?php
/**
 * Admin controller 
 *
 * @category Controller
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      HerissonController
 */

require_once __DIR__."/../Controller.php";

/**
 * Class: HerissonControllerAdmin
 *
 * @category Controller
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      HerissonController
 */
class HerissonControllerAdmin extends HerissonController
{

    /**
     * Constructor
     *
     * Sets controller's app, and the default action if none given.
     * Creates the view
     */
    function __construct()
    {
        $this->app = "admin";
        parent::__construct();

        $action = param('action');
        if ($action) {
            $this->action = $action;
        } else {
            $this->action = "index";
        }
        $this->setView();

    }



}
