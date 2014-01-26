<?php
/**
Plugin Name: Herisson
Version: 0.1
Plugin URI: 
Description: Herisson displays bookmarks you own. It allows you to develop a complete list of tagged bookmarks and friends you are sharing them with.
Author: Thibault Taillandier
Author URI: http://blog.taillandier.name/
License: GPL2
*/
/*  Copyright 2012  Thibault Taillandier  (email : thibault@taillandier.name)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


define('HERISSON_VERSION', '0.1');
define('HERISSON_DB', 1);
define('HERISSON_OPTIONS', 1);
define('HERISSON_REWRITE', 1);
define('HERISSON_TD', 'herisson');
define('HERISSON_BASE_DIR', ABSPATH."/wp-content/plugins/herisson/");
define('HERISSON_WP_BASE_DIR', ABSPATH);
define('HERISSON_INCLUDES_DIR', HERISSON_BASE_DIR.'includes/');
define('HERISSON_VENDOR_DIR', HERISSON_BASE_DIR.'vendor/');
define('HERISSON_TEMPLATES_DIR', HERISSON_BASE_DIR.'templates/');
define('HERISSON_ADMIN_DIR', HERISSON_BASE_DIR.'admin/');
define('HERISSON_LANG_DIR', HERISSON_BASE_DIR.'languages/');
define('HERISSON_DATA_DIR', HERISSON_BASE_DIR.'data/');
define('HERISSON_BACKUP_DIR', HERISSON_BASE_DIR.'backup/');
define('HERISSON_SCREENSHOTS_DIR', 'screenshots/');
define('HERISSON_MENU_SINGLE', 4);
define('HERISSON_MENU_MULTIPLE', 2);

define('HERISSON_EXIT', 1);

define('HERISSON_PLUGIN_URL', plugin_dir_url( __FILE__ ));

require_once HERISSON_WP_BASE_DIR."/wp-includes/plugin.php";
//require_once HERISSON_WP_BASE_DIR."/wp-load.php";
require_once HERISSON_WP_BASE_DIR."/wp-includes/pluggable.php";
require_once HERISSON_WP_BASE_DIR."/wp-includes/functions.php";
require_once HERISSON_WP_BASE_DIR."/wp-includes/cache.php";
wp_cache_init();
require_once HERISSON_WP_BASE_DIR."/wp-includes/wp-db.php";
require_once HERISSON_WP_BASE_DIR."/wp-admin/includes/plugin.php";


// Include other functionality
require_once HERISSON_BASE_DIR . 'Herisson.php';
require_once HERISSON_BASE_DIR . 'Herisson/Doctrine.php';
require_once HERISSON_BASE_DIR . 'Herisson/Pagination.php';
require_once HERISSON_BASE_DIR . 'Herisson/Message.php';
require_once HERISSON_BASE_DIR . 'Herisson/Folder.php';
require_once HERISSON_BASE_DIR . 'Herisson/Encryption.php';
require_once HERISSON_BASE_DIR . 'Herisson/Encryption/Exception.php';
require_once HERISSON_BASE_DIR . 'Herisson/Shell.php';
require_once HERISSON_BASE_DIR . 'Herisson/Shell/Exception.php';
require_once HERISSON_BASE_DIR . 'Herisson/Network.php';
require_once HERISSON_BASE_DIR . 'Herisson/Network/Exception.php';
require_once HERISSON_BASE_DIR . 'Herisson/Format.php';
require_once HERISSON_BASE_DIR . 'Herisson/Format/Exception.php';
require_once HERISSON_INCLUDES_DIR . 'functions.php';
require_once HERISSON_INCLUDES_DIR . 'screenshots.php';


// Routing and Controller classes
require_once HERISSON_BASE_DIR."/Herisson/Router.php";
require_once HERISSON_BASE_DIR."/Herisson/Controller/Admin/Bookmark.php";
require_once HERISSON_BASE_DIR."/Herisson/Controller/Admin/Friend.php";
require_once HERISSON_BASE_DIR."/Herisson/Controller/Admin/Maintenance.php";
require_once HERISSON_BASE_DIR."/Herisson/Controller/Admin/Import.php";
require_once HERISSON_BASE_DIR."/Herisson/Controller/Admin/Backup.php";
require_once HERISSON_BASE_DIR."/Herisson/Controller/Admin/Option.php";



// Initiate the database connexion with the same informations as the Wordpress installation.
define('HERISSON_DOCTRINE_DSN', 'mysql://' . DB_USER . ':' . DB_PASSWORD . '@' . DB_HOST . '/' . DB_NAME);
$doctrine = new Herisson\Doctrine(HERISSON_DOCTRINE_DSN);
$doctrine->loadlibrary();


$options = get_option('HerissonOptions');
define("HERISSON_LOCAL_URL", get_option('siteurl')."/".$options['basePath']);

add_action('init', array('Herisson', 'init'));

add_action('init', array('Herisson', 'checkVersions'));
add_action('plugins_loaded', array('Herisson', 'checkVersions'));

register_activation_hook('herisson/herisson.php', array('Herisson', 'install'));


add_action( 'send_headers', array('Herisson', 'router'));

add_action('admin_menu', array('Herisson', 'addPages'));


if (param('nomenu')) {
    $c = new Herisson\Router();
    $c->routeRaw();
}

    

