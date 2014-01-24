<?php
/**
 * Herisson 
 *
 * PHP Version 5.3
 *
 * @category Herisson
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      None
 */

use Herisson\Model\WpHerissonFriendsTable;
use Herisson\Router;
use Herisson\Encryption;

/**
 * Class: Herisson
 *
 * @category Herisson
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      None
 */
class Herisson
{

    /**
     * Manages the various admin pages Herisson uses.
     *
     * Adds stylesheets and JS for admin pages
     *
     * @return void
     */
    public static function addPages()
    {

        $update = '<span class="update-plugins count-%s" title="title"><span class="update-count">%s</span></span>';
        $friends = WpHerissonFriendsTable::getWhere('b_wantsyou=1');
        $nb = sizeof($friends);
        $friends_waiting = sprintf($update, $nb, $nb);
        $icon_url = plugin_dir_url("herisson")."/herisson/html/images/herisson_logo-16.png";

        unescapeGlobals();

        $r = new Router();

        add_menu_page(__('Herisson', HERISSON_TD), __('Herisson', HERISSON_TD), 'manage_options', 'herisson_menu', array(&$r, 'route'), $icon_url);
        add_submenu_page('herisson_menu', '', '', 'manage_options', 'herisson_menu', array(&$r, 'route'));
        add_submenu_page('herisson_menu', __('Bookmarks', HERISSON_TD), __('Bookmarks', HERISSON_TD), 'manage_options', 'herisson_bookmark', array(&$r, 'route'));

        add_submenu_page('herisson_menu', __('Friends', HERISSON_TD), __('Friends', HERISSON_TD).$friends_waiting, 'manage_options', 'herisson_friend', array(&$r, 'route'));
        add_submenu_page('herisson_menu', __('Import/Export', HERISSON_TD), __('Import/Export', HERISSON_TD), 'manage_options', 'herisson_import', array(&$r, 'route'));
        add_submenu_page('herisson_menu', __('Maintenance', HERISSON_TD), __('Maintenance', HERISSON_TD), 'manage_options', 'herisson_maintenance', array(&$r, 'route'));
        add_submenu_page('herisson_menu', __('Backups', HERISSON_TD), __('Backups', HERISSON_TD), 'manage_options', 'herisson_backup', array(&$r, 'route'));
        add_submenu_page('herisson_menu', __('Options', HERISSON_TD), __('Options', HERISSON_TD), 'manage_options', 'herisson_option', array(&$r, 'route'));

        wp_register_style('herissonStylesheet', plugins_url().'/herisson/html/css/stylesheet.css');
        wp_enqueue_style('herissonStylesheet');
        wp_register_style('herissonSwitches', plugins_url().'/herisson/html/css/switches.css');
        wp_enqueue_style('herissonSwitches');

    }


    /**
     * Checks if the install needs to be run by checking the `HerissonVersions` option, which stores the current installed database, options and rewrite versions.
     *
     * @return void
     */
    public static function checkVersions()
    {
        $versions = get_option('HerissonVersions');
        if (empty($versions)
            || $versions['db'] < HERISSON_DB
            || $versions['options'] < HERISSON_OPTIONS
            || $versions['rewrite'] < HERISSON_REWRITE) {
            Herisson::install();
        }
    }


    /**
     * Initialize the plugin
     *
     * @return void
     */
    public static function init()
    {
        // Load i18n domain
        load_plugin_textdomain(HERISSON_TD, false, HERISSON_LANG_DIR);
    }


    /**
     * Handler for the activation hook. Installs/upgrades the database table and adds/updates the HerissonOptions option.
     *
     * @return void
     */
    public static function install()
    {
        global $wpdb, $wp_rewrite, $wp_version;

        if ( version_compare('3.3', $wp_version) == 1 && strpos($wp_version, 'wordpress-mu') === false ) {
            echo "
            <p>".__('(Herisson only works with WordPress 3.3 and above)', HERISSON_TD)."</p>
            ";
            return;
        }

        // WP's dbDelta function takes care of installing/upgrading our DB table.
        $upgrade_file = file_exists(ABSPATH . 'wp-admin/includes/upgrade.php') ? ABSPATH . 'wp-admin/includes/upgrade.php' : ABSPATH . 'wp-admin/upgrade-functions.php';
        include_once $upgrade_file;
        // Until the nasty bug with duplicate indexes is fixed, we should hide dbDelta output.
        ob_start();
        $sql = file_get_contents(HERISSON_BASE_DIR.'install/init_db.sql');
        $sql = preg_replace("/#PREFIX#/", $wpdb->prefix, $sql);
        dbDelta($sql);

        $sql = file_get_contents(HERISSON_BASE_DIR.'install/init_data.sql');
        $sql = preg_replace("/#PREFIX#/", $wpdb->prefix, $sql);
        $wpdb->query($sql);

        $log = ob_get_contents();
        ob_end_clean();

        $log_file = dirname(__FILE__) . '/install-log-' . date('Y-m-d') . '.txt';
        if ( is_writable($log_file) ) {
            $fh = @fopen($log_file, 'w');
            if ( $fh ) {
                fwrite($fh, strip_tags($log));
                fclose($fh);
            }
        }

        // Generate a couple of public/private key to handle encryption between this site and friends
        $encryption = Encryption::i()->generateKeyPairs();

        $defaultOptions = array(
            'formatDate'                => 'd/m/Y',
            'sitename'                  => 'Herisson new instance',
            //'httpLib'                   => 'snoopy',
            'useModRewrite'             => true,
            'debugMode'                 => false,
            'bookmarksPerPage'          => 50,
            'templateBase'              => 'default_templates/',
            //'permalinkBase'             => 'bookmarks/',
            'basePath'                  => 'bookmarks',
            'publicKey'                 => $encryption->public,
            'privateKey'                => $encryption->private,
            'adminEmail'                => '',
            'screenshotTool'            => 'wkhtmltoimage-amd64',
            'convertPath'               => '/usr/bin/convert',
            'search'                    => '1',
            'checkHttpImport'           => '1',
            'acceptFriends'             => '1',
            'spiderOptionTextOnly'      => '1',
            'spiderOptionFullPage'      => '1',
            'spiderOptionFaivon'        => '1',
            'spiderOptionScreenshot'    => '0',
        );
        add_option('HerissonOptions', $defaultOptions);

        // Merge any new options to the existing ones.
        $options = get_option('HerissonOptions');
        $options = array_merge($defaultOptions, $options);
        update_option('herissonOptions', $options);

        // May be unset if called during plugins_loaded action.
        if (isset($wp_rewrite)) {
            // Update our .htaccess file.
            $wp_rewrite->flush_rules();
        }

        // Set an option that stores the current installed versions of the database, options and rewrite.
        $versions = array('db' => HERISSON_DB, 'options' => HERISSON_OPTIONS, 'rewrite' => HERISSON_REWRITE);
        update_option('HerissonVersions', $versions);
    }


    /**
     * Routing method for front controller
     *
     * @return void
     */
    public static function router()
    {
        // Routing : http://blog.defaultroute.com/2010/11/25/custom-page-routing-in-wordpress/
        global $route, $wp_query, $window_title;
        $options = get_option('HerissonOptions');
        $path =explode("/", $_SERVER['REQUEST_URI']);
        if (sizeof($path) && $path[1] == $options['basePath']) {
            include_once HERISSON_BASE_DIR."/Herisson/Controller/Front/Index.php";
            $c = new Herisson\Controller\Front\Index();
            $c->route();
            exit;
        }
    }


    /**
     * Uninstall Herisson plugin
     *
     * @return void
     */
    function uninstall()
    {
        global $wpdb;

        delete_option('HerissonOptions');
        delete_option('HerissonVersions');
        delete_option('HerissonWidget');

        $tables = array('bookmarks', 'bookmarks_tags', 'friends', 'tags', 'types');
        $table_name = $wpdb->prefix . "herisson";
        foreach ($tables as $table) {
            $wpdb->query("DROP TABLE IF EXISTS ${table_name}_$table");
        };
    }


}

