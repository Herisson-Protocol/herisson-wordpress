<?php
/*
Plugin Name: Herisson
Version: 1.0
Plugin URI: http://www.affordable-techsupport.com/code/
Description: Herisson displays books you have read, are reading, and hope to read, in the sidebar with cover art fetched automatically from Amazon. It allows you develop a library, show the book details, your progress, rate the book, and add link to a WP post of your book review. This Plugin is a heavily modified version of the Now Reading Plugin(s) (Original, Reloaded, Redux).
Author: Scott Olson
Author URI: http://www.affordable-techsupport.com/
License: GPL2
*/
/*  Copyright 2011  Scott Olson  (email : scott@affordable-techsupport.com)

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
#define('HERISSON_XML_DIR', HERISSON_BASE_DIR.'bookxml/');
define('HERISSON_MENU_SINGLE', 4);
define('HERISSON_MENU_MULTIPLE', 2);

/**
 * Load our I18n domain.
 */
add_action('init', 'herisson_init');
function herisson_init() {
	load_plugin_textdomain(HERISSONTD, false, HERISSON_LANG_DIR);
}

/**
 * Array of the statuses that books can be.
 * @global array $GLOBALS['herisson_statuses']
 * @name $herisson_statuses
 */
$herisson_statuses = apply_filters('herisson_statuses', array(
    'unread'	=> __('Future Book', HERISSONTD),
    'onhold'	=> __('Book on Hold', HERISSONTD),
    'reading'	=> __('Current Book', HERISSONTD),
    'read'		=> __('Completed Book', HERISSONTD)
));

/**
 * Array of the domains we can use for Amazon.
 * @global array $GLOBALS['herisson_domains']
 * @name $herisson_domains
 */
$herisson_domains = array(
    '.com'		=> __('International', HERISSONTD),
    '.co.uk'	=> __('United Kingdom', HERISSONTD),
    '.fr'		=> __('France', HERISSONTD),
    '.de'		=> __('Germany', HERISSONTD),
    '.co.jp'	=> __('Japan', HERISSONTD),
    '.ca'		=> __('Canada', HERISSONTD)
);

// Include other functionality
require_once HERISSON_INCLUDES_DIR . 'compat.php';
require_once HERISSON_INCLUDES_DIR . 'rewrite.php';
#require_once HERISSON_INCLUDES_DIR . 'books.php';
#require_once HERISSON_INCLUDES_DIR . 'amazon.php';
require_once HERISSON_INCLUDES_DIR . 'admin.php';
require_once HERISSON_INCLUDES_DIR . 'filters.php';
#require_once HERISSON_INCLUDES_DIR . 'functions.php';
require_once HERISSON_INCLUDES_DIR . 'widget.php';

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

function herisson_check_api_key() {
    $options = get_option('HerissonOptions');
    $AWSAccessKeyId = $options['AWSAccessKeyId'];
    $SecretAccessKey = $options['SecretAccessKey'];

    if (empty($AWSAccessKeyId) || empty($SecretAccessKey)) {

        function herisson_key_warning() {
            echo "
			<div id='herisson_key_warning' class='updated fade'><p><strong>".__('Herisson has detected a problem.', HERISSONTD)."</strong> ".sprintf(__('You are missing one of both: Amazon Web Services Access Key ID or Secret Access Key. Enter them <a href="%s">here</a>.', HERISSONTD), "admin.php?page=herisson_options")."</p></div>
			";
        }
        add_action('admin_notices', 'herisson_key_warning');
        return;
    }
}
add_action('init','herisson_check_api_key');


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
    dbDelta("
	CREATE TABLE {$wpdb->prefix}herisson (
	b_id bigint(20) NOT NULL auto_increment,
	b_added datetime,
	b_started datetime,
	b_finished datetime,
	b_title VARCHAR(100) NOT NULL,
	b_nice_title VARCHAR(100) NOT NULL,
	b_author VARCHAR(100) NOT NULL,
	b_nice_author VARCHAR(100) NOT NULL,
	b_image text,
	b_limage text,
	b_asin varchar(12) NOT NULL,
	b_status VARCHAR(8) NOT NULL default 'read',
	b_tpages smallint(6) default '0',
	b_cpages smallint(6) default '0',
	b_rating tinyint(4) default '0',
	b_post bigint(20) default '0',
	b_visibility tinyint(1) default '1',
	b_reader tinyint(4) NOT NULL default '1',
	PRIMARY KEY  (b_id),
	INDEX permalink (b_nice_author, b_nice_title),
	INDEX title (b_title),
	INDEX author (b_author)
	);
        ");
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

    $defaultOptions = array(
        'formatDate'	=> 'n/j/Y',
		'ignoreTime'	=> false,
		'hideAddedDate'	=>	false,
        'associate'		=> 'passforchrimi-20',
        'domain'		=> '.com',
        'imageSize'		=> 'Small',
		'limageSize'	=> 'Medium',
        'httpLib'		=> 'snoopy',
        'useModRewrite'	=> false,
        'debugMode'		=> false,
        'menuLayout'	=> HERISSON_MENU_SINGLE,
        'booksPerPage'  => 10,
        'defBookCount'  => 5,
		'hideCurrentBooks' => false,
		'hidePlannedBooks' => false,
		'hideFinishedBooks' => true,
		'hideBooksonHold' => true,
		'hideViewLibrary' => false,
		'templateBase' => 'default_templates/',
        'permalinkBase' => 'my-library/'
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

    // Update our nice titles/authors.
    $books = $wpdb->get_results("
	SELECT
		b_id AS id, b_title AS title, b_author AS author
	FROM
        {$wpdb->prefix}herisson
	WHERE
		b_nice_title = '' OR b_nice_author = ''
        ");
    foreach ( (array) $books as $book ) {
        $nice_title = $wpdb->escape(sanitize_title($book->title));
        $nice_author = $wpdb->escape(sanitize_title($book->author));
        $id = intval($book->id);
        $wpdb->query("
		UPDATE
            {$wpdb->prefix}herisson
		SET
			b_nice_title = '$nice_title',
			b_nice_author = '$nice_author'
		WHERE
			b_id = '$id'
            ");
    }

    // Set an option that stores the current installed versions of the database, options and rewrite.
    $versions = array('db' => HERISSON_DB, 'options' => HERISSON_OPTIONS, 'rewrite' => HERISSON_REWRITE);
    update_option('HerissonVersions', $versions);
}
register_activation_hook('herisson/herisson.php', 'herisson_install');

/**
 * Checks to see if the library/book permalink query vars are set and, if so, loads the appropriate templates.
 */
function herisson_library_init() {
    global $wp, $wpdb, $q, $query, $wp_query;

    $wp->parse_request();

    if ( is_herisson_page() )
        add_filter('wp_title', 'herisson_page_title');
    else
        return;

    if ( get_query_var('herisson_library') ) {
        // Library page:
        herisson_load_template('library.php');
        die;
    }

    if ( get_query_var('herisson_id') ) {
    // Book permalink:
        $GLOBALS['herisson_id'] = intval(get_query_var('herisson_id'));

        $load = herisson_load_template('single.php');
        if ( is_wp_error($load) )
            echo $load->get_error_message();

        die;
    }

    if ( get_query_var('herisson_page') ) {
    // get page name from query string:
        $herissonr_page = get_query_var('herisson_page');

        $load = herisson_load_template($herissonr_page);
        if ( is_wp_error($load) )
            echo $load->get_error_message();

        die;
    }

    if ( get_query_var('herisson_author') && get_query_var('herisson_title') ) {
    // Book permalink with title and author.
        $author				= $wpdb->escape(urldecode(get_query_var('herisson_author')));
        $title				= $wpdb->escape(urldecode(get_query_var('herisson_title')));
        $GLOBALS['herisson_id']	= $wpdb->get_var("
		SELECT
			b_id
		FROM
            {$wpdb->prefix}herisson
		WHERE
			b_nice_title = '$title'
			AND
			b_nice_author = '$author'
            ");

        $load = herisson_load_template('single.php');
        if ( is_wp_error($load) )
            echo $load->get_error_message();

        die;
    }
}
add_action('template_redirect', 'herisson_library_init');

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

/**
 * Provides a simple API for themes to load the sidebar template.
 */
function herisson_display() {
    herisson_load_template('sidebar.php');
}

/**
 * Adds our details to the title of the page - book title/author, "Library" etc.
 */
function herisson_page_title( $title ) {
    global $wp, $wp_query;
    $wp->parse_request();

    $title = '';

    if ( get_query_var('herisson_library') )
        $title = 'Herisson';

    if ( get_query_var('herisson_id') ) {
        $book = get_book(intval(get_query_var('herisson_id')));
        $title = $book->title . ' by ' . $book->author;
    }

    if ( !empty($title) ) {
        $title = apply_filters('herisson_page_title', $title);
        $separator = apply_filters('herisson_page_title_separator', ' | ');
        return $title.$separator;
    }
    return '';
}

/**
 * Adds information to the header for future statistics purposes.
 */
function herisson_header_stats() {
    echo '
	<meta name="herisson-version" content="' . HERISSON_VERSION . '" />
	';
}
add_action('wp_head', 'herisson_header_stats');

if ( !function_exists('robm_dump') ) {
/**
 * Dumps a variable in a pretty way.
 */
    function robm_dump() {
        echo '<pre style="border:1px solid #000; padding:5px; margin:5px; max-height:150px; overflow:auto;" id="' . md5(serialize($object)) . '">';
        $i = 0; $args = func_get_args();
        foreach ( (array) $args as $object ) {
            if ( $i == 0 && count($args) > 1 && is_string($object) )
                echo "<h3>$object</h3>";
            var_dump($object);
            $i++;
        }
        echo '</pre>';
    }
}

?>
