<?php
/**
 * Adds our admin menus, and some stylesheets and JavaScript to the admin head.
 *
 * @package Herisson
 */

/**
 * Adds our stylesheets and JS to admin pages.
 */

require_once HERISSON_BASE_DIR."/Herisson/Router.php";
require_once HERISSON_BASE_DIR."/Herisson/Controller/Admin/Bookmark.php";
require_once HERISSON_BASE_DIR."/Herisson/Controller/Admin/Friend.php";
require_once HERISSON_BASE_DIR."/Herisson/Controller/Admin/Maintenance.php";
require_once HERISSON_BASE_DIR."/Herisson/Controller/Admin/Backup.php";
require_once HERISSON_BASE_DIR."/Herisson/Controller/Admin/Option.php";

/**
 * Manages the various admin pages Herisson uses.
 */
function herissonAddPages()
{

    $update = '<span class="update-plugins count-%s" title="title"><span class="update-count">%s</span></span>';
    $friends = WpHerissonFriendsTable::getWhere('b_wantsyou=1');
    $nb = sizeof($friends);
    $friends_waiting = sprintf($update, $nb, $nb);
    $icon_url = plugin_dir_url("herisson")."/herisson/html/images/herisson_logo_mini_16x16.png";

    $r = new HerissonRouter();

    add_menu_page(__('Herisson', HERISSON_TD), __('Herisson', HERISSON_TD), 'manage_options', 'herisson_menu', array(&$r, 'route'), $icon_url);
    add_submenu_page('herisson_menu', '', '', 'manage_options', 'herisson_menu', array(&$r, 'route'));
    add_submenu_page('herisson_menu', __('Bookmarks', HERISSON_TD), __('Bookmarks', HERISSON_TD), 'manage_options', 'herisson_bookmark', array(&$r, 'route'));

    add_submenu_page('herisson_menu', __('Friends', HERISSON_TD), __('Friends', HERISSON_TD).$friends_waiting, 'manage_options', 'herisson_friend', array(&$r, 'route'));
    add_submenu_page('herisson_menu', __('Import/Maintenance', HERISSON_TD), __('Import/Maintenance', HERISSON_TD), 'manage_options', 'herisson_maintenance', array(&$r, 'route'));
    add_submenu_page('herisson_menu', __('Backups', HERISSON_TD), __('Backups', HERISSON_TD), 'manage_options', 'herisson_backup', array(&$r, 'route'));
    add_submenu_page('herisson_menu', __('Options', HERISSON_TD), __('Options', HERISSON_TD), 'manage_options', 'herisson_option', array(&$r, 'route'));

    wp_register_style('herissonStylesheet', plugins_url().'/herisson/html/css/stylesheet.css');
    wp_enqueue_style('herissonStylesheet');

}

add_action('admin_menu', 'herissonAddPages');


?>
