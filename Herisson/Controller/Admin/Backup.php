<?php
/**
 * Backup controller 
 *
 * @category Controller
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      HerissonControllerAdmin
 */

namespace Herisson\Controller\Admin;

require_once __DIR__."/../Admin.php";

/**
 * Class: HerissonControllerAdminBackup
 *
 * @category Controller
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      HerissonControllerAdmin
 */
class Backup extends \Herisson\Controller\Admin
{

    /**
     * Constructor
     *
     * Sets controller's name
     */
    function __construct()
    {
        $this->name = "backup";
        parent::__construct();
    }

    /**
     * Action to list existing backups
     *
     * This is the default action
     *
     * @return void
     */
    function indexAction()
    {
        $this->view->backups = \Doctrine_Query::create()
            ->from('Herisson\Model\WpHerissonBackups')
            ->execute();

    }

}


