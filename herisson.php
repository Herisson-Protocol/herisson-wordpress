<?php
/*
Plugin Name: Herisson
Version: 1.0
Plugin URI: 
Description: Herisson displays bookmarks you own. It allows you to develop a complete list of tagged bookmarks and friends you are sharing them with.
Author: Thibault Taillandier
Author URI: http://blog.taillandier.name/
License: GPL2
*/
/*  Copyright 2012  Scott Olson  (email : thibault@taillandier.name)

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


define('HERISSON_VERSION', '1.0');
define('HERISSON_DB', 54);
define('HERISSON_OPTIONS', 22);
define('HERISSON_REWRITE', 8);
define('HERISSONTD', 'herisson');
define('HERISSON_BASE_DIR', dirname(__FILE__).'/');
define('HERISSON_INCLUDES_DIR', HERISSON_BASE_DIR.'includes/');
define('HERISSON_TEMPLATES_DIR', HERISSON_BASE_DIR.'templates/');
define('HERISSON_ADMIN_DIR', HERISSON_BASE_DIR.'admin/');
define('HERISSON_LANG_DIR', HERISSON_BASE_DIR.'languages/');
define('HERISSON_SCREENSHOTS_DIR', HERISSON_BASE_DIR.'screenshots/');
#define('HERISSON_XML_DIR', HERISSON_BASE_DIR.'bookmarkxml/');
define('HERISSON_MENU_SINGLE', 4);
define('HERISSON_MENU_MULTIPLE', 2);

#echo "<br><br>";
require_once HERISSON_BASE_DIR."../../../wp-includes/plugin.php";
require_once HERISSON_BASE_DIR."../../../wp-includes/pluggable.php";
require_once HERISSON_BASE_DIR."../../../wp-includes/functions.php";
require_once HERISSON_BASE_DIR."../../../wp-includes/cache.php";
wp_cache_init();
require_once HERISSON_BASE_DIR."../../../wp-includes/wp-db.php";
require_once HERISSON_BASE_DIR."../../../wp-admin/includes/plugin.php";

/**
 * Load our I18n domain.
 */
add_action('init', 'herisson_init');
function herisson_init() {
	load_plugin_textdomain(HERISSONTD, false, HERISSON_LANG_DIR);
}


// Include other functionality
require_once HERISSON_BASE_DIR . 'doctrine/doctrine.php';
require_once HERISSON_INCLUDES_DIR . 'admin.php';
require_once HERISSON_INCLUDES_DIR . 'functions.php';
require_once HERISSON_INCLUDES_DIR . 'encryption.php';
require_once HERISSON_INCLUDES_DIR . 'network.php';
require_once HERISSON_INCLUDES_DIR . 'screenshots.php';
require_once HERISSON_INCLUDES_DIR . 'db.php';

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

    if ( version_compare('3.0', $wp_version) == 1 && strpos($wp_version, 'wordpress-mu') === false ) {
        echo "
		<p>".__('(Herisson only works with WordPress 3.0 and above)', HERISSONTD)."</p>
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

#/**
# * Checks to see if the library/bookmark permalink query vars are set and, if so, loads the appropriate templates.
# */
#function herisson_library_init() {
#    global $wp, $wpdb, $q, $query, $wp_query;
#
#    $wp->parse_request();
#
##    if ( is_herisson_page() )
##        add_filter('wp_title', 'herisson_page_title');
##    else
##        return;
#
#    if ( get_query_var('herisson_library') ) {
#        // Library page:
#        herisson_load_template('library.php');
#        die;
#    }
#
#    if ( get_query_var('herisson_id') ) {
#    // Book permalink:
#        $GLOBALS['herisson_id'] = intval(get_query_var('herisson_id'));
#
#        $load = herisson_load_template('single.php');
#        if ( is_wp_error($load) )
#            echo $load->get_error_message();
#
#        die;
#    }
#
#    if ( get_query_var('herisson_page') ) {
#    // get page name from query string:
#        $herissonr_page = get_query_var('herisson_page');
#
#        $load = herisson_load_template($herissonr_page);
#        if ( is_wp_error($load) )
#            echo $load->get_error_message();
#
#        die;
#    }
#
#    if ( get_query_var('herisson_author') && get_query_var('herisson_title') ) {
#    // Book permalink with title and author.
#        $author				= $wpdb->escape(urldecode(get_query_var('herisson_author')));
#        $title				= $wpdb->escape(urldecode(get_query_var('herisson_title')));
#        $GLOBALS['herisson_id']	= $wpdb->get_var("
#		SELECT
#			b_id
#		FROM
#            {$wpdb->prefix}herisson
#		WHERE
#			b_nice_title = '$title'
#			AND
#			b_nice_author = '$author'
#            ");
#
#        $load = herisson_load_template('single.php');
#        if ( is_wp_error($load) )
#            echo $load->get_error_message();
#
#        die;
#    }
#}
#add_action('template_redirect', 'herisson_library_init');

/**
 * Loads the given filename from The Herisson templates directory.
 * @param string $filename The filename of the template to load.
 */
function herisson_load_template( $filename ) {
    $template_option = get_option('herissonOptions');
	$template_directory = $template_option['templateBase'];

    $template = HERISSON_TEMPLATES_DIR . "$template_directory" . "$filename";

    if ( !file_exists($template) )
        return new WP_Error('template-missing', sprintf(__("Oops! The template file %s could not be found in the Herisson default_template or custom_template directories.", HERISSONTD), "<code>$filename</code>"));

    load_template($template);
}

#/**
# * Provides a simple API for themes to load the sidebar template.
# */
#function herisson_display() {
#    herisson_load_template('sidebar.php');
#}

#/**
# * Adds our details to the title of the page - bookmark title/author, "Library" etc.
# */
#function herisson_page_title( $title ) {
#    global $wp, $wp_query;
#    $wp->parse_request();
#
#    $title = '';
#
#    if ( get_query_var('herisson_library') )
#        $title = 'Herisson';
#
#    if ( get_query_var('herisson_id') ) {
#        $bookmark = get_herisson_bookmark(intval(get_query_var('herisson_id')));
#        $title = $bookmark->title . ' by ' . $bookmark->author;
#    }
#
#    if ( !empty($title) ) {
#        $title = apply_filters('herisson_page_title', $title);
#        $separator = apply_filters('herisson_page_title_separator', ' | ');
#        return $title.$separator;
#    }
#    return '';
#}

#/**
# * Adds information to the header for future statistics purposes.
# */
#function herisson_header_stats() {
#    echo '
#	<meta name="herisson-version" content="' . HERISSON_VERSION . '" />
#	';
#}
#add_action('wp_head', 'herisson_header_stats');


function herisson_router() {
 # Routing : http://blog.defaultroute.com/2010/11/25/custom-page-routing-in-wordpress/
 global $route,$wp_query,$window_title;
 $options = get_option('HerissonOptions');
#print_r($options);
 $path =explode("/",$_SERVER['REQUEST_URI']);
	if (sizeof($path) && $path[1] == $options['basePath'] && array_key_exists(2,$path) && $path[2]) {

  require_once HERISSON_BASE_DIR."/front/front.php";
	 herisson_front_actions();
		die();
	
	}
}

add_action( 'send_headers', 'herisson_router');


#if ( !function_exists('robm_dump') ) {
#/**
# * Dumps a variable in a pretty way.
# */
#    function robm_dump() {
#        echo '<pre style="border:1px solid #000; padding:5px; margin:5px; max-height:150px; overflow:auto;" id="' . md5(serialize($object)) . '">';
#        $i = 0; $args = func_get_args();
#        foreach ( (array) $args as $object ) {
#            if ( $i == 0 && count($args) > 1 && is_string($object) )
#                echo "<h3>$object</h3>";
#            var_dump($object);
#            $i++;
#        }
#        echo '</pre>';
#    }
#}

#add_filter('show_admin_bar', '__return_false');
#add_action('admin_menu', 'remove_menus');

if (param('nomenu')) {
 if (param('page') == "herisson_bookmarks") {
  herisson_bookmark_actions();
 } else if (param('page') == "herisson_friends") {
  herisson_friend_actions();
	} else if (param('page') == 'front') {
  herisson_front_actions();
	}
	exit;
}

/**
 * Hook to display the [herisson] content
	*/
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
	
?>
