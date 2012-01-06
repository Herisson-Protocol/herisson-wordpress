<?php
/**
 * Handles the POST of editing an existing books.
 * @package herisson
 */

require '../../../../wp-config.php';

$_POST = stripslashes_deep($_POST);

$options = get_option('HerissonOptions');
$action = $_POST['action'];
herisson_reset_vars(array('action'));

switch ( $action ) {
    case 'delete':
        $id = intval($_GET['id']);

        check_admin_referer('herisson-delete-book_' . $id);

        $wpdb->query("
		DELETE FROM {$wpdb->prefix}herisson
		WHERE b_id = $id
            ");

        wp_redirect($herisson_url->urls['manage'] . '&deleted=1');
        die;
        break;

    case 'update':
        check_admin_referer('herisson-edit');

        $count = intval($_POST['count']);

        if ( $count > total_books(0, 0) )
            die;

        $updated = 0;

        for ( $i = 0; $i < $count; $i++ ) {

            $id = intval($_POST['id'][$i]);
            if ( $id == 0 )
                continue;

            $author			= $wpdb->escape($_POST['author'][$i]);
            $title			= $wpdb->escape($_POST['title'][$i]);
            $asin			= $wpdb->escape($_POST['asin'][$i]);

            $nice_author	= $wpdb->escape(sanitize_title($_POST['author'][$i]));
            $nice_title		= $wpdb->escape(sanitize_title($_POST['title'][$i]));

            $status			= $wpdb->escape($_POST['status'][$i]);

// Added Begin
			if ($options['multiuserMode']) {
				if ( current_user_can('administrator') ) {
            $reader			= $wpdb->escape($_POST['reader'][$i]);
				}
			}
// Added End

            $added			= ( herisson_empty_date($_POST['added'][$i]) )	? '' : $wpdb->escape(date('Y-m-d H:i:s', strtotime($_POST['added'][$i])));
            $started		= ( herisson_empty_date($_POST['started'][$i]) )	? '' : $wpdb->escape(date('Y-m-d H:i:s', strtotime($_POST['started'][$i])));
            $finished		= ( herisson_empty_date($_POST['finished'][$i]) )	? '' : $wpdb->escape(date('Y-m-d H:i:s', strtotime($_POST['finished'][$i])));

            if (!empty($_POST['posts'][$i]))
            {
                $post = 'b_post = "' . intval($_POST["posts"][$i]) . '",';
			}
			else
			{
			    $post = 'b_post = "0",';
			}

// Added Begin
            if (!empty($_POST['tpages'][$i]))
            {
                $tpages = 'b_tpages = "' . $_POST["tpages"][$i] . '",';
			}
			else
			{
			    $tpages = 'b_tpages = "0",';
			}

            if (!empty($_POST['cpages'][$i]))
            {
                $cpages = 'b_cpages = "' . $_POST["cpages"][$i] . '",';
			}
			else
			{
			    $cpages = 'b_cpages = "0",';
			}
// Added End
			
            if (!empty($_POST['visibility'][$i]))
            {
                $visibility = 'b_visibility = "' . intval($_POST["visibility"][$i]) . '",';
			}
			else
			{
				// By default, Private.
                $visibility = 'b_visibility = "0",';
			}

            if (!empty($_POST['rating'][$i]))
            {
				$rating	= 'b_rating = "' . intval($_POST["rating"][$i]) . '",';
			}

            if ( !empty($_POST['image'][$i]) )
                $image = 'b_image = "' . $wpdb->escape($_POST['image'][$i]) . '",';

// Added Begin
            if ( !empty($_POST['limage'][$i]) )
                $image = 'b_limage = "' . $wpdb->escape($_POST['limage'][$i]) . '",';
// Added End
			
            $current_status = $wpdb->get_var("
			SELECT b_status
			FROM {$wpdb->prefix}herisson
			WHERE b_id = $id
                ");

            // If the book is currently "unread" but is being changed to "reading", and the user didn't set a started date, we need to add a b_started value.
            if ( $current_status == 'unread' && $status == 'reading' && empty($started))
                $started = 'b_started = "' . date('Y-m-d H:i:s') . '",';
            else
                $started = "b_started = '$started',";

            // If the book is currently "reading" but is being changed to "read", and the user didn't set a finished date, we need to add a b_finished value.
            if ( $current_status == 'reading' && $status == 'read' && empty($finished))
                $finished = 'b_finished = "' . date('Y-m-d H:i:s') . '",';
            else
                $finished = "b_finished = '$finished',";

			$query = "
			UPDATE {$wpdb->prefix}herisson
			SET
                $started
                $finished
                $rating
                $image
				$limage
                $post
				$visibility
				$tpages
				$cpages
				b_author = '$author',
				b_asin = '$asin',
				b_title = '$title',
				b_nice_author = '$nice_author',
				b_nice_title = '$nice_title',
				b_status = '$status',				
				b_added = '$added'
			WHERE
				b_id = $id
                ";
			//echo $query;
            $result = $wpdb->query($query);
            if ( $wpdb->rows_affected > 0 )
                $updated++;

// Added Begin
			if ($options['multiuserMode']) {
				if ( current_user_can('administrator') ) {
			$query1 = "
			UPDATE {$wpdb->prefix}herisson
			SET
				b_reader = '$reader'
			WHERE
				b_id = $id
                ";
			//echo $query1;
            $result = $wpdb->query($query1);
            if ( $wpdb->rows_affected > 0 )
                $updated++;
				}
			}
// Added End
        }
		
        $referer = wp_get_referer();
        if ( empty($referer) )
            $forward = $herisson_url->urls['manage'] . '&updated=' . $updated;
        else
            $forward = preg_replace('/&updated=([0-9]*)/i', '', wp_get_referer()) . '&updated=' . $updated;

        header("Location: $forward");
        die;
        break;
}

die;


?>
