<?php
/**
 * URL/mod_rewrite functions
 * @package herisson
 */

/**
 * Handles our URLs, depending on what menu layout we're using
 * @package herisson
 */
class herisson_url {
/**
 * The current URL scheme.
 * @access public
 * @var array
 */
    var $urls;

    /**
     * The scheme for a multiple menu layout.
     * @access private
     * @var array
     */
    var $multiple;
    /**
     * The scheme for a single menu layout.
     * @access private
     * @var array
     */
    var $single;

    /**
     * Constructor. Populates {@link $multiple} and {@link $single}.
     */
    function herisson_url() {
#        $this->multiple = array(
#            'add'		=> get_option('siteurl') . '/wp-admin/post-new.php?page=herisson_menu',
#            'manage'	=> get_option('siteurl') . '/wp-admin/admin.php?page=manage_bookmarks',
#            'options'	=> get_option('siteurl') . '/wp-admin/options-general.php?page=herisson_options'
#        );
        $this->single = array(
            'add_bookmark'		=> get_option('siteurl') . '/wp-admin/admin.php?page=herisson_add_bookmark',
            'manage_bookmarks'	=> get_option('siteurl') . '/wp-admin/admin.php?page=herisson_manage_bookmarks',
            'options'	=> get_option('siteurl') . '/wp-admin/admin.php?page=herisson_options'
        );
    }

    /**
     * Loads the given scheme, populating {@link $urls}
     * @param integer $scheme The scheme to use, either HERISSON_MENU_SINGLE or HERISSON_MENU_MULTIPLE
     */
    function load_scheme( $option ) {
        if ( $option == HERISSON_MENU_SINGLE )
            $this->urls = $this->single;
        else
            $this->urls = $this->multiple;
    }
}
/**
 * Global singleton to access our current scheme.
 * @global herisson_url $GLOBALS['herisson_url']
 * @name $herisson_url
 */
$herisson_url	= new herisson_url();
$options	= get_option('HerissonOptions');
$herisson_url->load_scheme($options['menuLayout']);

/**
 * Registers our query vars so we can redirect to the library and book permalinks.
 * @param array $vars The existing array of query vars
 * @return array The modified array of query vars with our additions.
 */
function herisson_query_vars( $vars ) {
    $vars[] = 'herisson_library';
    $vars[] = 'herisson_id';
    $vars[] = 'herisson_page';   
    $vars[] = 'herisson_title';
    $vars[] = 'herisson_author';
    $vars[] = 'herisson_reader'; //in order to filter books by reader
    return $vars;
}
add_filter('query_vars', 'herisson_query_vars');

/**
 * Adds our rewrite rules for the library and book permalinks to the regular WordPress ones.
 * @param array $rules The existing array of rewrite rules we're filtering
 * @return array The modified rewrite rules with our additions.
 */
function herisson_mod_rewrite( $rules ) {
    $options = get_option('HerissonOptions');
    add_rewrite_rule(preg_quote($options['permalinkBase']) . '([0-9]+)/?$', 'index.php?herisson_id=$matches[1]', 'top');
    add_rewrite_rule(preg_quote($options['permalinkBase']) . 'page/([^/]+)/?$', 'index.php?herisson_page=$matches[1]', 'top');   
    add_rewrite_rule(preg_quote($options['permalinkBase']) . 'reader/([^/]+)/?$', 'index.php?herisson_reader=$matches[1]', 'top');
    add_rewrite_rule(preg_quote($options['permalinkBase']) . '([^/]+)/([^/]+)/?$', 'index.php?herisson_author=$matches[1]&herisson_title=$matches[2]', 'top');
    add_rewrite_rule(preg_quote($options['permalinkBase']) . '([^/]+)/?$', 'index.php?herisson_author=$matches[1]', 'top');
    add_rewrite_rule(preg_quote($options['permalinkBase']) . '?$', 'index.php?herisson_library=1', 'top');
}
add_action('init', 'herisson_mod_rewrite');

/**
 * Returns true if we're on a Herisson page.
 */
function is_herisson_page() {
    global $wp;
    $wp->parse_request();

    return (
    get_query_var('herisson_library') ||
        get_query_var('herisson_id')      ||
        get_query_var('herisson_page')    ||        
        get_query_var('herisson_title')   ||
        get_query_var('herisson_author')  ||
		get_query_var('herisson_reader')
	);  
}

?>
