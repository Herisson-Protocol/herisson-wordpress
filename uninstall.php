<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit();

function herisson_delete_plugin() {
	global $wpdb;

	delete_option( 'HerissonOptions' );
	delete_option( 'HerissonVersions' );
	delete_option( 'HerissonWidget' );

	$table_name = $wpdb->prefix . "herisson";
	$wpdb->query( "DROP TABLE IF EXISTS $table_name" );
}

herisson_delete_plugin();

?>
