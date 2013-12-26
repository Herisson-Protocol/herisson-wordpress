<?

require_once __DIR__."/../../../../wp-config.php";
require_once __DIR__."/../src/Herisson/Doctrine.php";
require_once __DIR__."/../src/Herisson/Network.php";
require_once __DIR__."/../src/Herisson/Message.php";
require_once __DIR__."/../src/Herisson/Encryption.php";
require_once __DIR__."/../src/Herisson/Pagination.php";
require_once __DIR__."/../src/includes/functions.php";
require_once __DIR__."/../src/Herisson/Models/WpHerissonFriends.php";
require_once __DIR__."/../src/Herisson/Models/WpHerissonFriendsTable.php";

/**
 * Dummy variable to avoid errors
 */
if (! defined('HERISSON_TD')) {
    define("HERISSON_TD","dummy");
}

$options = get_option('HerissonOptions');
define("HERISSON_URL", get_option('siteurl')."/".$options['basePath']);


