<?php
/**
 * Adds our admin menus, and some stylesheets and JavaScript to the admin head.
 * @package herisson
 */

/**
 * Adds our stylesheets and JS to admin pages.
 */

require_once HERISSON_ADMIN_DIR . 'add-bookmark.php';
require_once HERISSON_ADMIN_DIR . 'manage-bookmarks.php';
require_once HERISSON_ADMIN_DIR . 'options.php';

/**
 * Manages the various admin pages Herisson uses.
 */
function herisson_add_pages() {
    $options = get_option('HerissonOptions');
 #add_menu_page($odlinksadmin_page_name,$odlinksadmin_page_name,$odlinksuser_level,__FILE__,'process_odlinkssettings','../wp-content/plugins/odlinks/images/odl.gif');

		if ( $options['menuLayout'] == HERISSON_MENU_SINGLE ) {

			add_menu_page(__('Herisson', HERISSONTD), __('Herisson', HERISSONTD), 'manage_options', 'herisson_menu', 'herisson_add_bookmark');
#			add_submenu_page('herisson_menu', __('Add a Book', HERISSONTD), __('Add a Book', HERISSONTD), 'manage_options', 'herisson_menu', 'herisson_');
			add_submenu_page('herisson_menu', __('Add a Book', HERISSONTD), __('Add a Book', HERISSONTD), 'manage_options', 'herisson_add_bookmark', 'herisson_add_bookmark');
			add_submenu_page('herisson_menu', __('Manage Books', HERISSONTD), __('Manage Books', HERISSONTD), 'manage_options', 'herisson_manage_bookmarks', 'herisson_manage_bookmarks');
			add_submenu_page('herisson_menu', __('Library Options', HERISSONTD), __('Options', HERISSONTD), 'manage_options', 'herisson_options', 'herisson_manage_options');
		
		} else {

#			add_submenu_page('post-new.php', __('Add to Library', HERISSONTD), __('Add to Library', HERISSONTD), 'manage_options', 'herisson_menu', 'herisson_add_bookmark');
#			add_management_page(__('Manage Library', HERISSONTD), __('Manage Library', HERISSONTD), 'manage_options', 'manage_bookmarks', 'herisson_manage_bookmarks');
#			add_options_page(__('Library Options', HERISSONTD), __('Library Options', HERISSONTD), 'manage_options', 'herisson_options', 'herisson_manage_options');

  }
}

add_action('admin_menu', 'herisson_add_pages');
	
?>
