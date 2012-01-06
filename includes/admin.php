<?php
/**
 * Adds our admin menus, and some stylesheets and JavaScript to the admin head.
 * @package herisson
 */

/**
 * Adds our stylesheets and JS to admin pages.
 */

require_once HERISSON_ADMIN_DIR . 'admin-add.php';
require_once HERISSON_ADMIN_DIR . 'admin-manage.php';
require_once HERISSON_ADMIN_DIR . 'admin-options.php';

/**
 * Manages the various admin pages Herisson uses.
 */
function herisson_add_pages() {
    $options = get_option('HerissonOptions');
 #add_menu_page($odlinksadmin_page_name,$odlinksadmin_page_name,$odlinksuser_level,__FILE__,'process_odlinkssettings','../wp-content/plugins/odlinks/images/odl.gif');

	if (!$options['multiuserMode']) {
		
		if ( $options['menuLayout'] == HERISSON_MENU_SINGLE ) {

			add_menu_page(__('Herisson', HERISSONTD), __('Herisson', HERISSONTD), 'manage_options', 'library_menu', 'herisson_add_book');
			add_submenu_page('library_menu', __('Add a Book', HERISSONTD), __('Add a Book', HERISSONTD), 'manage_options', 'library_menu', 'herisson_add_book');
			add_submenu_page('library_menu', __('Manage Books', HERISSONTD), __('Manage Books', HERISSONTD), 'manage_options', 'manage_books', 'herisson_manage_books');
			add_submenu_page('library_menu', __('Library Options', HERISSONTD), __('Options', HERISSONTD), 'manage_options', 'herisson_options', 'herisson_manage_options');
		
		} else {

			add_submenu_page('post-new.php', __('Add to Library', HERISSONTD), __('Add to Library', HERISSONTD), 'manage_options', 'library_menu', 'herisson_add_book');
			add_management_page(__('Manage Library', HERISSONTD), __('Manage Library', HERISSONTD), 'manage_options', 'manage_books', 'herisson_manage_books');
			add_options_page(__('Library Options', HERISSONTD), __('Library Options', HERISSONTD), 'manage_options', 'herisson_options', 'herisson_manage_options');

		}

	} else {

		if ( $options['menuLayout'] == HERISSON_MENU_SINGLE ) {

			add_menu_page(__('Herisson', HERISSONTD), __('Herisson', HERISSONTD), 'publish_posts', 'library_menu', 'herisson_add_book');
			add_submenu_page('library_menu', __('Add a Book', HERISSONTD), __('Add a Book', HERISSONTD), 'publish_posts', 'library_menu', 'herisson_add_book');
			add_submenu_page('library_menu', __('Manage Books', HERISSONTD), __('Manage Books', HERISSONTD), 'publish_posts', 'manage_books', 'herisson_manage_books');
			add_submenu_page('library_menu', __('Library Options', HERISSONTD), __('Options', HERISSONTD), 'manage_options', 'herisson_options', 'herisson_manage_options');
		
		} else {

			add_submenu_page('post-new.php', __('Add to Library', HERISSONTD), __('Add to Library', HERISSONTD), 'publish_posts', 'library_menu', 'herisson_add_book');
			add_management_page(__('Manage Library', HERISSONTD), __('Manage Library', HERISSONTD), 'publish_posts', 'manage_books', 'herisson_manage_books');
			add_options_page(__('Library Options', HERISSONTD), __('Library Options', HERISSONTD), 'manage_options', 'herisson_options', 'herisson_manage_options');

		}
		
	}

}

add_action('admin_menu', 'herisson_add_pages');
	
?>
