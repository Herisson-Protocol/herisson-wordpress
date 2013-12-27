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
require_once __DIR__."/../Herisson/Models/WpHerissonFriends.php";
require_once __DIR__."/../Herisson/Models/WpHerissonFriendsTable.php";

/**
 * Dummy variable to avoid errors
 */
if (! defined('HERISSON_TD')) {
    define("HERISSON_TD", "dummy");
}

$options = get_option('HerissonOptions');
define("HERISSON_URL", get_option('siteurl')."/".$options['basePath']);


