<?php
/**
 * Adds our admin menus, and some stylesheets and JavaScript to the admin head.
 * @package herisson
 */

/**
 * Adds our stylesheets and JS to admin pages.
 */

require_once HERISSON_ADMIN_DIR . 'bookmarks.php';
require_once HERISSON_ADMIN_DIR . 'friends.php';
require_once HERISSON_ADMIN_DIR . 'maintenance.php';
require_once HERISSON_ADMIN_DIR . 'backup.php';
require_once HERISSON_ADMIN_DIR . 'options.php';

/**
 * Manages the various admin pages Herisson uses.
 */
function herisson_add_pages() {
#    $options = get_option('HerissonOptions');

 $update = '<span class="update-plugins count-%s" title="title"><span class="update-count">%s</span></span>';
	$friends = Doctrine_Query::create()->from('WpHerissonFriends')->where('b_wantsyou=1')->execute();
	$nb = sizeof($friends);
 $friends_waiting = sprintf($update,$nb,$nb);
	$icon_url = plugin_dir_url("herisson")."/herisson/images/herisson_logo_mini_16x16.png";

			add_menu_page(__('Herisson', HERISSON_TD), __('Herisson', HERISSON_TD), 'manage_options', 'herisson_menu','herisson_bookmark_actions',$icon_url);
			add_submenu_page('herisson_menu', __('Bookmarks', HERISSON_TD), __('Bookmarks', HERISSON_TD), 'manage_options', 'herisson_bookmarks', 'herisson_bookmark_actions');
			add_submenu_page('herisson_menu', __('Friends', HERISSON_TD), __('Friends', HERISSON_TD).$friends_waiting, 'manage_options', 'herisson_friends', 'herisson_friend_actions');
			add_submenu_page('herisson_menu', __('Import/Maintenance', HERISSON_TD), __('Import/Maintenance', HERISSON_TD), 'manage_options', 'herisson_maintenance', 'herisson_maintenance_actions');
			add_submenu_page('herisson_menu', __('Backups', HERISSON_TD), __('Backups', HERISSON_TD), 'manage_options', 'herisson_backup', 'herisson_backup_actions');
			add_submenu_page('herisson_menu', __('Options', HERISSON_TD), __('Options', HERISSON_TD), 'manage_options', 'herisson_options', 'herisson_options_manage');
		
}

add_action('admin_menu', 'herisson_add_pages');

?>
