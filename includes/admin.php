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

			add_menu_page(__('Herisson', HERISSONTD), __('Herisson', HERISSONTD), 'manage_options', 'herisson_menu','herisson_bookmark_actions');
#			add_submenu_page('herisson_menu', __('Add a Book', HERISSONTD), __('Add a Book', HERISSONTD), 'manage_options', 'herisson_menu', 'herisson_');
#			add_submenu_page('herisson_menu', __('Add a Book', HERISSONTD), __('Add a Book', HERISSONTD), 'manage_options', 'herisson_add_bookmark', 'herisson_add_bookmark');
#			add_submenu_page('herisson_menu', __('Add a Book', HERISSONTD), __('Add a Book', HERISSONTD), 'manage_options', 'herisson_add_bookmark', 'herisson_add_bookmark');
			add_submenu_page('herisson_menu', __('Bookmarks', HERISSONTD), __('Bookmarks', HERISSONTD), 'manage_options', 'herisson_bookmarks', 'herisson_bookmark_actions');
			add_submenu_page('herisson_menu', __('Friends', HERISSONTD), __('Friends', HERISSONTD).$friends_waiting, 'manage_options', 'herisson_friends', 'herisson_friend_actions');
			add_submenu_page('herisson_menu', __('Import/Backup', HERISSONTD), __('Import/Backup', HERISSONTD), 'manage_options', 'herisson_backup', 'herisson_backup_actions');
			add_submenu_page('herisson_menu', __('Options', HERISSONTD), __('Options', HERISSONTD), 'manage_options', 'herisson_options', 'herisson_manage_options');
		
}

add_action('admin_menu', 'herisson_add_pages');

?>
