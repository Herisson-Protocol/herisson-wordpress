<?php
/**
 * Test environment
 *
 * @category Test
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 */


require_once __DIR__."/../../../../wp-config.php";
require_once __DIR__."/../Herisson/Doctrine.php";
require_once __DIR__."/../Herisson/Network.php";
require_once __DIR__."/../Herisson/Message.php";
require_once __DIR__."/../Herisson/Encryption.php";
require_once __DIR__."/../Herisson/Pagination.php";
require_once __DIR__."/../includes/functions.php";

define("HERISSON_TEST_DB", "herisson_test");
define('HERISSON_DOCTRINE_DSN_TEST', 'mysql://' . DB_USER . ':' . DB_PASSWORD . '@' . DB_HOST . '/'. HERISSON_TEST_DB);

$doctrine = new Herisson\Doctrine(HERISSON_DOCTRINE_DSN_TEST);
$doctrine->loadlibrary();

// Model
require_once __DIR__."/Herisson/ModelTest.php";
require_once __DIR__."/../Herisson/Model/Exception.php";
require_once __DIR__."/../Herisson/Model/WpHerissonFriends.php";
require_once __DIR__."/../Herisson/Model/WpHerissonFriendsTable.php";


// Controller
require_once __DIR__."/Herisson/ControllerTest.php";
require_once __DIR__."/../Herisson/View.php";
require_once __DIR__."/../Herisson/Router.php";
require_once __DIR__."/../Herisson/Controller.php";
require_once __DIR__."/../Herisson/Controller/Admin.php";
require_once __DIR__."/../Herisson/Controller/Admin/Backup.php";
require_once __DIR__."/../Herisson/Controller/Admin/Bookmark.php";
require_once __DIR__."/../Herisson/Controller/Admin/Friend.php";
require_once __DIR__."/../Herisson/Controller/Admin/Import.php";
require_once __DIR__."/../Herisson/Controller/Admin/Maintenance.php";
require_once __DIR__."/../Herisson/Controller/Admin/Option.php";
require_once __DIR__."/../Herisson/Controller/Front.php";
require_once __DIR__."/../Herisson/Controller/Front/Index.php";

// Format files
require_once __DIR__."/Herisson/FormatTest.php";
require_once __DIR__."/../Herisson/Export.php";
require_once __DIR__."/../Herisson/Format.php";
require_once __DIR__."/../Herisson/Format/Csv.php";
require_once __DIR__."/../Herisson/Format/Herisson.php";


/**
 * Dummy variable to avoid errors
 */
if (! defined('HERISSON_TD')) {
    define("HERISSON_TD", "dummy");
}

$options = get_option('HerissonOptions');
define("HERISSON_URL", get_option('siteurl')."/".$options['basePath']);


