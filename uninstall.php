<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit();

function herisson_delete_plugin() {
	global $wpdb;

	delete_option( 'HerissonOptions' );
	delete_option( 'HerissonVersions' );
	delete_option( 'HerissonWidget' );

 $tables = array('bookmarks', 'bookmarks_tags', 'friends', 'tags', 'types');
 $table_name = $wpdb->prefix . "herisson";
 foreach ($tables as $table) {
	 $wpdb->query( "DROP TABLE IF EXISTS ${table_name}_$table" );
	};
}

herisson_delete_plugin();

?>
