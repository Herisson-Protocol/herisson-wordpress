<?php
/**
 * Handles the adding of new bookmarks.
 * @package herisson
 */

require '../../../../wp-config.php';

$_POST = stripslashes_deep($_POST);

if ( !empty($_POST['title']) ) {

    check_admin_referer('herisson-add-bookmark');

    $author = $wpdb->escape($_POST['author']);
    $title = $wpdb->escape($_POST['title']);
    $url = $wpdb->escape($_POST['url']);
    $description = $wpdb->escape($_POST['description']);
    $added = date('Y-m-d H:i:s');
#    $status = 'unread';
    $nice_title = $wpdb->escape(sanitize_title($_POST['title']));
    $nice_author = $wpdb->escape(sanitize_title($_POST['author']));

    foreach ( (array) compact('url', 'title', 'description', 'tags', 'added') as $field => $value )
        $query .= "$field=$value&";

    $id = add_bookmark($query);
    if ( $id > 0 ) {
        wp_redirect($herisson_url->urls['add'] . '&added=' . intval($id));
        die;
    } else {
        wp_redirect($herisson_url->urls['add'] . '&error=true');
        die;
    }
}

?>
