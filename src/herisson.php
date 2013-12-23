<?php
/*
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
?>
<?php


define('HERISSON_VERSION', '0.1');
define('HERISSON_DB', 1);
define('HERISSON_OPTIONS', 1);
define('HERISSON_REWRITE', 1);
define('HERISSON_TD', 'herisson');
#define('HERISSON_BASE_DIR', dirname(__FILE__).'/');
#define('HERISSON_BASE_DIR', "/var/www/".$_SERVER['HTTP_HOST']."/wp-content/plugins/herisson/");
define('HERISSON_BASE_DIR', ABSPATH."/wp-content/plugins/herisson/");
define('HERISSON_WP_BASE_DIR', ABSPATH);
define('HERISSON_INCLUDES_DIR', HERISSON_BASE_DIR.'includes/');
define('HERISSON_TEMPLATES_DIR', HERISSON_BASE_DIR.'templates/');
define('HERISSON_ADMIN_DIR', HERISSON_BASE_DIR.'admin/');
define('HERISSON_LANG_DIR', HERISSON_BASE_DIR.'languages/');
define('HERISSON_DATA_DIR', HERISSON_BASE_DIR.'data/');
define('HERISSON_SCREENSHOTS_DIR', 'screenshots/');
define('HERISSON_MENU_SINGLE', 4);
define('HERISSON_MENU_MULTIPLE', 2);

define('HERISSON_EXIT', 1);

define('HERISSON_PLUGIN_URL', plugin_dir_url( __FILE__ ));

require_once HERISSON_WP_BASE_DIR."/wp-includes/plugin.php";
#require_once HERISSON_WP_BASE_DIR."/wp-load.php";
require_once HERISSON_WP_BASE_DIR."/wp-includes/pluggable.php";
require_once HERISSON_WP_BASE_DIR."/wp-includes/functions.php";
require_once HERISSON_WP_BASE_DIR."/wp-includes/cache.php";
wp_cache_init();
require_once HERISSON_WP_BASE_DIR."/wp-includes/wp-db.php";
require_once HERISSON_WP_BASE_DIR."/wp-admin/includes/plugin.php";

/**
 * Load our I18n domain.
 */
add_action('init', 'herisson_init');
function herisson_init() {
	load_plugin_textdomain(HERISSON_TD, false, HERISSON_LANG_DIR);
}


// Include other functionality
require_once HERISSON_BASE_DIR . 'doctrine/doctrine.php';
require_once HERISSON_INCLUDES_DIR . 'admin.php';
require_once HERISSON_INCLUDES_DIR . 'functions.php';
require_once HERISSON_INCLUDES_DIR . 'encryption.php';
require_once HERISSON_INCLUDES_DIR . 'network.php';
require_once HERISSON_INCLUDES_DIR . 'screenshots.php';
require_once HERISSON_INCLUDES_DIR . 'maintenance.php';
#require_once HERISSON_INCLUDES_DIR . 'db.php';

/**
 * Checks if the install needs to be run by checking the `HerissonVersions` option, which stores the current installed database, options and rewrite versions.
 */
function herisson_check_versions()
{
    $versions = get_option('HerissonVersions');
    if (empty($versions) ||
		$versions['db'] < HERISSON_DB ||
		$versions['options'] < HERISSON_OPTIONS ||
		$versions['rewrite'] < HERISSON_REWRITE)
    {
		herisson_install();
    }
}
add_action('init', 'herisson_check_versions');
add_action('plugins_loaded', 'herisson_check_versions');

/**
 * Handler for the activation hook. Installs/upgrades the database table and adds/updates the HerissonOptions option.
 */
function herisson_install() {
    global $wpdb, $wp_rewrite, $wp_version;

    if ( version_compare('3.3', $wp_version) == 1 && strpos($wp_version, 'wordpress-mu') === false ) {
        echo "
		<p>".__('(Herisson only works with WordPress 3.3 and above)', HERISSON_TD)."</p>
		";
        return;
    }

    // WP's dbDelta function takes care of installing/upgrading our DB table.
    $upgrade_file = file_exists(ABSPATH . 'wp-admin/includes/upgrade.php') ? ABSPATH . 'wp-admin/includes/upgrade.php' : ABSPATH . 'wp-admin/upgrade-functions.php';
    require_once $upgrade_file;
    // Until the nasty bug with duplicate indexes is fixed, we should hide dbDelta output.
    ob_start();
    $sql = file_get_contents(HERISSON_BASE_DIR.'install/init_db.sql');
    $sql = preg_replace("/#PREFIX#/",$wpdb->prefix,$sql);
    dbDelta($sql);

    $sql = file_get_contents(HERISSON_BASE_DIR.'install/init_data.sql');
    $sql = preg_replace("/#PREFIX#/",$wpdb->prefix,$sql);
    $wpdb->query($sql);

    $log = ob_get_contents();
    ob_end_clean();

    $log_file = dirname(__FILE__) . '/install-log-' . date('Y-m-d') . '.txt';
    if ( is_writable($log_file) ) {
        $fh = @fopen( $log_file, 'w' );
        if ( $fh ) {
            fwrite($fh, strip_tags($log));
            fclose($fh);
        }
    }

  # Generate a couple of public/private key to handle encryption between this site and friends
  list($publicKey,$privateKey) = herisson_generate_keys_pair();

    $defaultOptions = array(
		'formatDate'	=> 'd/m/Y',
		'sitename'	=> 'Herisson new instance',
#		'httpLib'	=> 'snoopy',
		'useModRewrite'	=> true,
		'debugMode'	=> false,
		'bookmarksPerPage'	=> 50,
		'templateBase'		=> 'default_templates/',
		#'permalinkBase'		=> 'bookmarks/',
		'basePath'		=> 'bookmarks',
		'publicKey'		=> $publicKey,
		'privateKey'		=> $privateKey,
		'adminEmail'		=> '',
		'screenshotTool'		=> 'wkhtmltoimage-amd64',
		'convertPath'		=> '/usr/bin/convert',
		'search'		=> '1',
		'checkHttpImport'		=> '1',
		'acceptFriends'		=> '1',
		'spiderOptionTextOnly'		=> '1',
		'spiderOptionFullPage'		=> '1',
		'spiderOptionScreenshot'		=> '0',
    );
    add_option('HerissonOptions', $defaultOptions);

    // Merge any new options to the existing ones.
    $options = get_option('HerissonOptions');
    $options = array_merge($defaultOptions, $options);
    update_option('herissonOptions', $options);

	// May be unset if called during plugins_loaded action.
	if (isset($wp_rewrite))
    {
		// Update our .htaccess file.
		$wp_rewrite->flush_rules();
	}

    // Set an option that stores the current installed versions of the database, options and rewrite.
    $versions = array('db' => HERISSON_DB, 'options' => HERISSON_OPTIONS, 'rewrite' => HERISSON_REWRITE);
    update_option('HerissonVersions', $versions);
}
register_activation_hook('herisson/herisson.php', 'herisson_install');


/**
 * Loads the given filename from The Herisson templates directory.
 * @param string $filename The filename of the template to load.
 */
	/*
function herisson_load_template( $filename ) {
    $template_option = get_option('herissonOptions');
	$template_directory = $template_option['templateBase'];

    $template = HERISSON_TEMPLATES_DIR . "$template_directory" . "$filename";

    if ( !file_exists($template) )
        return new WP_Error('template-missing', sprintf(__("Oops! The template file %s could not be found in the Herisson default_template or custom_template directories.", HERISSON_TD), "<code>$filename</code>"));

    load_template($template);
}
*/

function herisson_router() {
 # Routing : http://blog.defaultroute.com/2010/11/25/custom-page-routing-in-wordpress/
 global $route,$wp_query,$window_title;
 $options = get_option('HerissonOptions');
 $path =explode("/",$_SERVER['REQUEST_URI']);
	if (sizeof($path) && $path[1] == $options['basePath']) { # && array_key_exists(2,$path) && $path[2]) {
#  require HERISSON_BASE_DIR."/Herisson/Controller.php";
  require HERISSON_BASE_DIR."/Herisson/Controller/Front/Index.php";

  $c = new HerissonControllerFrontIndex();
  $c->route();
  exit;
  /*
  require_once HERISSON_BASE_DIR."/front/front.php";
	 herisson_front_actions();
		die();
  */
	}
}

add_action( 'send_headers', 'herisson_router');



#add_filter('show_admin_bar', '__return_false');
#add_action('admin_menu', 'remove_menus');

if (param('nomenu')) {
    $c = new HerissonRouter();
    $c->routeRaw();
    /*
 if (param('page') == "herisson_bookmarks") {
  herisson_bookmark_actions();
 } else if (param('page') == "herisson_friends") {
#  require HERISSON_BASE_DIR."/Herisson/Controller.php";
  require HERISSON_BASE_DIR."/Herisson/Controller/Admin/Friend.php";
  $c = new HerissonControllerAdminFriend();
  $c->route();
    
#  herisson_friend_actions();
	} else if (param('page') == 'herisson_maintenance') {
  herisson_maintenance_actions();
	} else if (param('page') == 'herisson_backup') {
  herisson_backup_actions();
	} else if (param('page') == 'front') {
  herisson_front_actions();
	}
	exit;
 */
}


/**
 * Hook to display the [herisson] content
	*/
/*
require_once HERISSON_BASE_DIR."/front/front.php";
function herisson_front_test($content) {
 if (preg_match("#(.*)\[herisson\](.*)#mis",$content,$match)) {
  ob_start();
	 echo $match[1];
		herisson_front_list();
	 echo $match[2];
  $text = ob_get_contents();
  ob_end_clean();
		return $text;
	} else {
	 return $content;
	}
}
add_action('the_content','herisson_front_test');
*/
	
?>
