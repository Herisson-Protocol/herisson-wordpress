<?php
/**
 * Book fetching/updating functions
 * @package herisson
 */

/**
 * Fetches bookmarks from the database based on a given query.
 *
 * Example usage:
 * <code>
 * $bookmarks = get_bookmarks('status=reading&orderby=started&order=asc&num=-1&reader=user');
 * </code>
 * @param string $query Query string containing restrictions on what to fetch.
 * 		 	Valid variables: $num, $status, $orderby, $order, $search, $author, $title, $reader.
 * @param bool show_private If true, will show all readers' private bookmarks!
 * @return array Returns a numerically indexed array in which each element corresponds to a bookmark.
 */
function get_herisson_bookmarks($query, $show_private = false) {

    global $wpdb;

    $options = get_option('HerissonOptions');

    parse_str($query);

    // We're fetching a collection of bookmarks, not just one.
    switch ( $status ) {
        case 'unread':
        case 'onhold':
        case 'reading':
        case 'read':
            break;
        default:
            $status = 'all';
            break;
    }
    if ( $status != 'all' )
        $status = "AND b_status = '$status'";
    else
        $status = '';

    if ( !empty($search) ) {
        $search = $wpdb->escape($search);
        $search = "AND ( b_author LIKE '%$search%' OR b_title LIKE '%$search%' OR m_value LIKE '%$search%')";
    } else
        $search = '';

    $order	= ( strtolower($order) == 'desc' ) ? 'DESC' : 'ASC';

    switch ( $orderby ) {
        case 'added':
            $orderby = 'b_added';
            break;
        case 'started':
            $orderby = 'b_started';
            break;
        case 'finished':
            $orderby = 'b_finished';
            break;
        case 'title':
            $orderby = 'b_title';
            break;
        case 'author':
            $orderby = 'b_author';
            break;
        case 'asin':
            $orderby = 'b_asin';
            break;
        case 'status':
            $orderby = "b_status $order, b_added";
            break;
        case 'rating':
            $orderby = 'b_rating';
            break;
        case 'random':
            $orderby = 'RAND()';
            break;
        default:
            $orderby = 'b_added';
            break;
    }

    if (empty($num))
    {
		// The default number of bookmarks if unspecified.
		$num = $options['defBookCount'];
	}

    if ( $num > -1 && $offset >= 0 ) {
        $offset	= intval($offset);
        $num 	= intval($num);
        $limit	= "LIMIT $offset, $num";
    } else
        $limit	= '';

    if ( !empty($author) ) {
        $author	= $wpdb->escape($author);
        $author	= "AND b_author = '$author'";
    }

    if ( !empty($title) ) {
        $title	= $wpdb->escape($title);
        $title	= "AND b_title = '$title'";
    }

	$reader = get_reader_visibility_filter($reader, $show_private);

    $query = "
	SELECT
		COUNT(*) AS count,
		b_id AS id, b_title AS title, b_author AS author, b_image AS image, b_limage AS limage, b_status AS status, b_nice_title AS nice_title,
		b_nice_author AS nice_author, b_added AS added, b_started AS started, b_finished AS finished, b_asin AS asin, b_tpages AS tpages, b_cpages AS cpages,
		b_rating AS rating, b_post AS post, b_reader as reader
	FROM
        {$wpdb->prefix}herisson
	WHERE
		1=1
        $status
        $id
        $search
        $author
        $title
	AND
        $reader
	GROUP BY
		b_id
	ORDER BY
        $orderby $order
        $limit
        ";
	$bookmarks = $wpdb->get_results($query);

    $bookmarks = apply_filters('get_bookmarks', $bookmarks);

    foreach ( (array) $bookmarks as $bookmark ) {
        $bookmark->added = ( herisson_empty_date($bookmark->added) )	? '' : $bookmark->added;
        $bookmark->started = ( herisson_empty_date($bookmark->started) )	? '' : $bookmark->started;
        $bookmark->finished = ( herisson_empty_date($bookmark->finished) )	? '' : $bookmark->finished;
    }

    return $bookmarks;
}

/**
 * Fetches a single bookmark with the given ID.
 * @param int $id The b_id of the bookmark you want to fetch.
 */
function get_herisson_bookmark($id) {
    global $wpdb;

    $options = get_option('HerissonOptions');

    $id = intval($id);

    $bookmark = apply_filters('get_single_bookmark', $wpdb->get_row("
	SELECT
		COUNT(*) AS count,
		b_id AS id, b_title AS title, b_author AS author, b_image AS image, b_limage AS limage, b_status AS status, b_nice_title AS nice_title,
		b_nice_author AS nice_author, b_added AS added, b_started AS started, b_finished AS finished, b_asin AS asin, b_tpages AS tpages, b_cpages AS cpages,
		b_rating AS rating, b_post AS post, b_reader as reader, b_visibility AS visibility
	FROM {$wpdb->prefix}herisson
	WHERE b_id = $id
	GROUP BY b_id
        "));

    $bookmark->added = ( herisson_empty_date($bookmark->added) )	? '' : $bookmark->added;
    $bookmark->started = ( herisson_empty_date($bookmark->started) )	? '' : $bookmark->started;
    $bookmark->finished = ( herisson_empty_date($bookmark->finished) )	? '' : $bookmark->finished;

    return $bookmark;
}

/**
 * Returns a string to be used in a WHERE clause to
 * select bookmarks based on a reader (or all) and visibility.
 * @param int $userID Interested only in the given userID's bookmarks.
 * @param bool show_private If true, will show all readers' private bookmarks!
 */
function get_reader_visibility_filter($userID = 0, $show_private = false)
{
	global $user_ID;

	if (!$show_private)
	{
		// Show publics only.
		$visibility = "b_visibility = 1";
	}

    if (!empty($userID))
	{
		// we're only interested in this reader.
		$reader = "b_reader = '$userID'";

		if (is_user_logged_in())
		{
			get_currentuserinfo();
			if ($show_private || ($userID == $user_ID))
			{
				// Privates are shown, so don't filter them.
				return $reader;
			}
		}
    }
	else
	{
		// No specific reader, see if there is a logged-user, then get her private
		// bookmarks too, otherwise, get everyeone's publics.
		if (is_user_logged_in())
		{
			get_currentuserinfo();

			// Either it's the owner, or we get public bookmarks only.
			return "(b_reader = '$user_ID' OR b_visibility = 1)";
		}
	}

	if (!empty($reader) && !empty($visibility))
	{
		return $reader . ' AND ' . $visibility;
	}

	return !empty($reader) ? $reader : $visibility;
}

/**
 * Adds a bookmark to the database.
 * @param string $query Query string containing the fields to add.
 * @return boolean True on success, false on failure.
 */
function add_bookmark( $query ) {
    return update_bookmark($query);
}

/**
 * Updates a given bookmark's database entry
 * @param string $query Query string containing the fields to add.
 * @return boolean True on success, false on failure.
 */
function update_bookmark( $query ) {
    global $wpdb, $query, $fields, $userdata;

    parse_str($query, $fields);

    $fields = apply_filters('add_bookmark_fields', $fields);

    // If an ID is specified, we're doing an update; otherwise, we're doing an insert.
    $insert = empty($fields['b_id']);

    $valid_fields = array('b_id', 'b_added', 'b_started', 'b_finished', 'b_title', 'b_nice_title', 'b_author', 'b_nice_author',
						'b_image', 'b_limage', 'b_asin', 'b_status', 'b_tpages', 'b_cpages', 'b_rating', 'b_post');

    if ( $insert ) {
        $colums = $values = '';
        foreach ( (array) $fields as $field => $value ) {
            if ( empty($field) || empty($value) || !in_array($field, $valid_fields) )
                continue;
            $value = $wpdb->escape($value);
            $columns .= ", $field";
            $values .= ", '$value'";
        }

        get_currentuserinfo();
        $reader_id = $userdata->ID;
        $columns .= ", b_reader";
        $values .= ", '$reader_id'";

        $columns = preg_replace('#^, #', '', $columns);
        $values = preg_replace('#^, #', '', $values);

        $wpdb->query("
		INSERT INTO {$wpdb->prefix}herisson
		($columns)
		VALUES($values)
            ");

        $id = $wpdb->get_var("SELECT MAX(b_id) FROM {$wpdb->prefix}herisson");


        if ( $id > 0 ) {
            do_action('bookmark_added', $id);
            return $id;
        } else {
            return false;
        }
    } else {
        $id = intval($fields['b_id']);
        unset($fields['b_id']);

        $set = '';
        foreach ( (array) $fields as $field => $value ) {
            if ( empty($field) || empty($value) || !in_array($field, $valid_fields) )
                continue;
            $value = $wpdb->escape($value);
            $set .= ", $field = '$value'";
        }

        $set = preg_replace('#^, #', '', $set);

        $wpdb->query("
		UPDATE {$wpdb->prefix}herisson
		SET $set
		WHERE b_id = $id
            ");

        do_action('bookmark_updated', $id);

        return $id;
    }
}

?>
