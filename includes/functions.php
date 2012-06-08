<?php
#/**
# * Functions for theming and templating.
# * @package herisson
# */
#
#/**
# * The array index of the current bookmark in the {@link $bookmarks} array.
# * @global integer $GLOBALS['current_bookmark']
# * @name $current_bookmark
# */
#$current_bookmark = 0;
#/**
# * The array of bookmarks for the current query.
# * @global array $GLOBALS['bookmarks']
# * @name $bookmarks
# */
#$bookmarks = null;
#/**
# * The current bookmark in the loop.
# * @global object $GLOBALS['bookmark']
# * @name $bookmark
# */
#$bookmark = null;
#
#/**
# * Formats a date according to the date format option.
# * @param string The date to format, in any string recogniseable by strtotime.
# */
#function herisson_format_date( $date ) {
#    $options = get_option('HerissonOptions');
#    if ( !is_numeric($date) )
#        $date = strtotime($date);
#    if ( empty($date) )
#        return '';
#    return apply_filters('herisson_format_date', date($options['formatDate'], $date));
#}
#
#/**
# * Returns true if the date is a valid one; false if it isn't.
# * @param string The date to check.
# */
#function herisson_empty_date( $date ) {
#    return ( empty($date) || $date == "0000-00-00 00:00:00" );
#}
#
#/**
# * Prints the bookmark's title.
# * @param bool $echo Whether or not to echo the results.
# */
#function bookmark_title( $echo = true ) {
#    global $bookmark;
#    $title = stripslashes(apply_filters('bookmark_title', $bookmark->title));
#    if ( $echo )
#        echo $title;
#    return $title;
#}
#
#/**
# * Prints the bookmark's reader.
# * @param bool $echo Wether or not to echo the results.
# */
#function bookmark_reader( $echo=true ) {
#    global $bookmark;
#
#    $user_info = get_userdata($bookmark->reader);
#
#    if ( $echo )
#        echo $user_info->display_name;
#    return $user_info->display_name;
#
#}
#
#/**
# * Prints the user name
# * @param int $reader_id Wordpress ID of the reader. If 0, prints the current user name.
# */
#function print_reader( $echo=true, $reader_id = 0) {
#    global $userdata;
#
#    $username='';
#
#    if (!$reader_id) {
#        get_currentuserinfo();
#        $username = $userdata->user_login;
#    } else {
#        $user_info = get_userdata($reader_id);
#        $username = $user_info->user_login;
#    }
#
#    if ($echo)
#        echo $username;
#    return $username;
#}
#
#/**
# * Prints the author of the bookmark.
# * @param bool $echo Whether or not to echo the results.
# */
#function bookmark_author( $echo = true ) {
#    global $bookmark;
#    $author = apply_filters('bookmark_author', $bookmark->author);
#    if ( $echo )
#        echo $author;
#    return $author;
#}
#
#/**
# * Prints a URL to the bookmark's Widget image, usually used within an HTML img element.
# * @param bool $echo Whether or not to echo the results.
# */
#function bookmark_image( $echo = true ) {
#    global $bookmark;
#    $image = apply_filters('bookmark_image', $bookmark->image);
#    if ( $echo )
#        echo $image;
#    return $image;
#}
#
#// Added Begin
#/**
# * Prints a URL to the bookmark's Library image, usually used within an HTML img element.
# * @param bool $echo Whether or not to echo the results.
# */
#function bookmark_limage( $echo = true ) {
#    global $bookmark;
#    $limage = apply_filters('bookmark_limage', $bookmark->limage);
#    if ( $echo )
#        echo $limage;
#    return $limage;
#}
#// Added End
#
#/**
# * Prints the date when the bookmark was added to the database.
# * @param bool $echo Whether or not to echo the results.
# */
#function bookmark_added( $echo = true ) {
#    global $bookmark;
#    $added = apply_filters('bookmark_added', $bookmark->added);
#    if ( $echo )
#        echo $added;
#    return $added;
#}
#
#/**
# * Prints the date when the bookmark's status was changed from unread to reading.
# * @param bool $echo Whether or not to echo the results.
# */
#function bookmark_started( $echo = true ) {
#    global $bookmark;
#    if ( herisson_empty_date($bookmark->started) )
#        $started = __('Not Started', HERISSONTD);
#    else
#        $started = apply_filters('bookmark_started', $bookmark->started);
#    if ( $echo )
#        echo $started;
#    return $started;
#
#}
#
#/**
# * Prints the date when the bookmark's status was changed from reading to read.
# * @param bool $echo Whether or not to echo the results.
# */
#function bookmark_finished( $echo = true ) {
#    global $bookmark;
#    if ( herisson_empty_date($bookmark->finished) )
#        $finished = __('Not Finished', HERISSONTD);
#    else
#        $finished = apply_filters('bookmark_finished', $bookmark->finished);
#    if ( $echo )
#        echo $finished;
#    return $finished;
#}
#
#/**
# * Prints the current bookmark's status with optional overrides for messages.
# * @param bool $echo Whether or not to echo the results.
# */
#function bookmark_status( $echo = true, $unread = '', $reading = '', $read = '', $onhold = '' ) {
#    global $bookmark, $herisson_statuses;
#
#    if ( empty($unread) )
#        $unread = $herisson_statuses['unread'];
#    if ( empty($reading) )
#        $reading = $herisson_statuses['reading'];
#    if ( empty($read) )
#        $read = $herisson_statuses['read'];
#    if ( empty($onhold) )
#        $onhold = $herisson_statuses['onhold'];
#
#    switch ( $bookmark->status ) {
#        case 'unread':
#            $text = $unread;
#            break;
#        case 'onhold':
#            $text = $onhold;
#            break;
#        case 'reading':
#            $text = $reading;
#            break;
#        case 'read':
#            $text = $read;
#            break;
#        default:
#            return;
#    }
#
#    if ( $echo )
#        echo $text;
#    return $text;
#}
#
#/**
# * Prints the number of bookmarks started and finished within a given time period.
# * @param string $interval The time interval, eg  "1 year", "3 month"
# * @param bool $echo Whether or not to echo the results.
# */
#function bookmarks_read_since( $interval, $echo = true ) {
#    global $wpdb;
#
#    $interval = $wpdb->escape($interval);
#    $num = $wpdb->get_var("
#	SELECT
#		COUNT(*) AS count
#	FROM
#        {$wpdb->prefix}herisson
#	WHERE
#		DATE_SUB(CURDATE(), INTERVAL $interval) <= b_finished
#        ");
#
#    if ( $echo )
#//        echo "$num bookmark".($num != 1 ? 's' : '');
#        echo "<b>$num</b>";
#    return $num;
#}
#
#/**
# * Prints bookmark reading statistics.
# * @param string $time_period The period to measure average over, eg "year", "month".
# */
#function print_bookmark_stats($time_period = 'year')
#{
#	echo '<br>' . __("Total bookmarks in all categories: ", HERISSONTD);
#	total_bookmarks(0);
#	echo '<br>' . __("Books read in the last year: ", HERISSONTD);
#	bookmarks_read_since('1 year');
#	echo '<br>' . __("Books read in the last month: ", HERISSONTD);
#	bookmarks_read_since('1 month');
#	echo '<br>' . __("Average bookmarks read per year: ", HERISSONTD);
#	average_bookmarks($time_period, true, false);
#}
#
#/**
# * Prints the total number of bookmarks in the library.
# * @param string $status A comma-separated list of statuses to include in the count. If ommitted, all statuses will be counted.
# * @param bool $echo Whether or not to echo the results.
# * @param int $userID Counting only userID's bookmarks.
# */
#function total_bookmarks($status = '', $echo = true , $userID = 0) {
#    global $wpdb;
#
#	$reader = get_reader_visibility_filter($userID, false);
#
#    if ($status)
#	{
#        if (strpos($status, ',') === false)
#		{
#            $status = 'WHERE b_status = "' . $wpdb->escape($status) . '"';
#        }
#		else
#		{
#            $statuses = explode(',', $status);
#
#            $status = 'WHERE 1=0';
#            foreach ( (array) $statuses as $st )
#			{
#                $status .= ' OR b_status = "' . $wpdb->escape(trim($st)) . '" ';
#            }
#        }
#
#		if (!empty($reader))
#		{
#			$status .= ' AND ' . $reader;
#		}
#	}
#	else
#	{
#		if (!empty($reader))
#		{
#			$status = ' WHERE ' . $reader;
#		}
#    }
#
#    $num = $wpdb->get_var("
#	SELECT
#		COUNT(*) AS count
#	FROM
#        {$wpdb->prefix}herisson
#        $status
#        ");
#
#    if ($echo)
#    {
#		echo "<b>$num</b>";
#	}
#
#    return $num;
#}
#
#/**
# * Prints the average number of bookmarks read in the given time limit.
# * Unless $absolute is true, the average is computed based on the weighted average of
# * bookmarks read witin the last 365 days and those read within the last 30 days.
# * @param string $time_period The period to measure average over, eg "year", "month".
# * @param bool $echo Whether or not to echo the results.
# * @param bool $absolute If true, the average is computed based on the oldest finished date.
# */
#function average_bookmarks($time_period = 'week', $echo = true, $absolute = true)
#{
#    global $wpdb;
#
#	if ($absolute)
#	{
#		$bookmarks_per_day = $wpdb->get_var("
#		SELECT
#			( COUNT(*) / ( TO_DAYS(CURDATE()) - TO_DAYS(MIN(b_finished)) ) ) AS bookmarks_per_day_in_year
#		FROM
#			{$wpdb->prefix}herisson
#		WHERE
#			b_status = 'read'
#		AND b_finished > 0
#			");
#	}
#	else
#	{
#		$bookmarks_per_day_in_year = $wpdb->get_var("
#		SELECT
#			( COUNT(*) / ( TO_DAYS(CURDATE()) - TO_DAYS(MIN(b_finished)) ) ) AS bookmarks_per_day_in_year
#		FROM
#			{$wpdb->prefix}herisson
#		WHERE
#			b_status = 'read'
#		AND TO_DAYS(b_finished) >= (TO_DAYS(CURDATE()) - 365)
#			");
#
#		$bookmarks_per_day_in_month = $wpdb->get_var("
#		SELECT
#			( COUNT(*) / ( TO_DAYS(CURDATE()) - TO_DAYS(MIN(b_finished)) ) ) AS bookmarks_per_day_in_month
#		FROM
#			{$wpdb->prefix}herisson
#		WHERE
#			b_status = 'read'
#		AND TO_DAYS(b_finished) >= (TO_DAYS(CURDATE()) - 30)
#			");
#
#		// Give twice the weight for the last month's average than the total of last year's.
#		$bookmarks_per_day = ((2.0 * $bookmarks_per_day_in_month) + $bookmarks_per_day_in_year) / 3.0;
#	}
#
#    $average = 0;
#    switch ( $time_period ) {
#        case 'year':
#            $average = round($bookmarks_per_day * 365);
#            break;
#
#        case 'month':
#            $average = round($bookmarks_per_day * 31);
#            break;
#
#        case 'week':
#            $average = round($bookmarks_per_day * 7);
#			break;
#
#        case 'day':
#            $average = round($bookmarks_per_day * 1);
#            break;
#
#        default:
#            return 0;
#    }
#
#    if($echo)
#    {
#		if ($absolute)
#		{
#			$type = __("an absolute", HERISSONTD);
#		}
#		else
#		{
#			$type = __("a current", HERISSONTD);
#		}
#		printf(__("<b>%s</b><br><br>", HERISSONTD), $average);
#	}
#
#    return $average;
#}
#
#/**
# * Prints the URL to an internal page displaying data about the bookmark.
# * @param bool $echo Whether or not to echo the results.
# * @param int $id The ID of the bookmark to link to. If ommitted, the current bookmark's ID will be used.
# */
#function bookmark_permalink( $echo = true, $id = 0 ) {
#    global $bookmark, $wpdb;
#    $options = get_option('HerissonOptions');
#
#    if ( !empty($bookmark) && empty($id) )
#        $the_bookmark = $bookmark;
#    elseif ( !empty($id) )
#        $the_bookmark = get_bookmark(intval($id));
#
#    if ( $the_bookmark->id < 1 )
#        return;
#
#    $author = $the_bookmark->nice_author;
#    $title = $the_bookmark->nice_title;
#
#
#
#    if ( $options['useModRewrite'] )
#        $url = get_option('home') . "/" . preg_replace("/^\/|\/+$/", "", $options['permalinkBase'])  . "/$author/$title/";
#    else
#        $url = get_option('home') . "/index.php?herisson_author=$author&amp;herisson_title=$title";
#
#    $url = apply_filters('bookmark_permalink', $url);
#    if ( $echo )
#        echo $url;
#    return $url;
#}
#
#
#/**
# * Prints the URL to an internal page displaying bookmarks by a certain reader.
# * @param bool $echo Wether or not to echo the results.
# * @param int $reader The reader id. If omitted, links to all bookmarks.
# */
#function bookmark_reader_permalink( $echo = true, $reader = 0) {
#    global $bookmark, $wpdb;
#
#    $options = get_option('HerissonOptions');
#
#    if ( !$reader )
#        $reader = $bookmark->reader;
#
#    if ( !$reader )
#        return;
#
#    if ($options['multiuserMode']) {
#        $url = get_option('home') . "/" . preg_replace("/^\/|\/+$/", "", $options['permalinkBase']) . "/reader/$reader/";
#    } else {
#        $url = get_option('home') . "/index.php?herisson_library=1&herisson_reader=$reader";
#    }
#
#    if ($echo)
#        echo $url;
#    return $url;
#}
#
#/**
# * Prints a URL to the bookmark's Amazon detail page. If the bookmark is a custom one, it will print a URL to the bookmark's permalink page.
# * @param bool $echo Whether or not to echo the results.
# * @param string $domain The Amazon domain to link to. If ommitted, the default domain will be used.
# * @see bookmark_permalink()
# * @see is_custom_bookmark()
# */
#function bookmark_url( $echo = true, $domain = null ) {
#    global $bookmark;
#    $options = get_option('HerissonOptions');
#
#    if ( empty($domain) )
#        $domain = $options['domain'];
#
#    if ( is_custom_bookmark() )
#        return bookmark_permalink($echo);
#    else {
#        $url = apply_filters('bookmark_url', "http://www.amazon{$domain}/exec/obidos/ASIN/{$bookmark->asin}/ref=nosim/{$options['associate']}");
#        if ( $echo )
#            echo $url;
#        return $url;
#    }
#}
#
#// Added Begin
#/**
# * Prints the target for the URL to the bookmark's page (Amazon detail page or details page).
# * @param bool $echo Whether or not to echo the results.
# */
#function bookmark_target( $echo = true ) {
#	global $bookmark;
#
#	if ( is_custom_bookmark() )
#		$target_window = "_self";
#	else
#		$target_window = "_blank";
#	
#	if ( $echo )
#		echo $target_window;
#	return $target_window;
#}
#// Added End
#
#/**
# * Returns true if the current bookmark is linked to a post, false if it isn't.
# */
#function bookmark_has_post() {
#    global $bookmark;
#
#    return ( $bookmark->post > 0 );
#}
#
#/**
# * Returns or prints the permalink of the post linked to the current bookmark.
# * @param bool $echo Whether or not to echo the results.
# */
#function bookmark_post_url( $echo = true ) {
#    global $bookmark;
#
#    if ( !bookmark_has_post() )
#        return;
#
#    $permalink = get_permalink($bookmark->post);
#
#    if ( $echo )
#        echo $permalink;
#    return $permalink;
#}
#
#/**
# * Returns or prints the title of the post linked to the current bookmark.
# * @param bool $echo Whether or not to echo the results.
# */
#function bookmark_post_title( $echo = true ) {
#    global $bookmark;
#
#    if ( !bookmark_has_post() )
#        return;
#
#    $post = get_post($bookmark->post);
#
#    if ( $echo )
#        echo $post->post_title;
#    return $post->post_title;
#}
#
#/**
# * If the current bookmark is linked to a post, prints an HTML link to said post.
# * @param bool $echo Whether or not to echo the results.
# */
#function bookmark_post_link( $echo = true ) {
#    global $bookmark;
#
#    if ( !bookmark_has_post() )
#        return;
#
#    $link = '<a href="' . bookmark_post_url(0) . '">' . bookmark_post_title(0) . '</a>';
#
#    if ( $echo )
#        echo $link;
#    return $link;
#}
#
#// Added Begin
#function bookmark_review_link( $echo = true ) {
#    global $bookmark;
#
#    if ( !bookmark_has_post() )
#      $review_link = apply_filters('bookmark_review_link', __('(No Review)', HERISSONTD));
#	else
#      $review_link = '(<a href="' . bookmark_post_url(0) . '">' . __('Review', HERISSONTD) . '</a>)';
#
#    if ( $echo )
#        echo $review_link;
#    return $review_link;
#}
#// Added End
#
#/**
# * If the user has the correct permissions, prints a URL to the Manage -> Herisson page of the WP admin.
# * @param bool $echo Whether or not to echo the results.
# */
#function manage_library_url( $echo = true ) {
#    global $herisson_url;
#    if ( can_herisson_admin() )
#        echo apply_filters('bookmark_manage_url', $herisson_url->urls['manage']);
#}
#
#/**
# * If the user has the correct permissions, prints a URL to the review-writing screen for the current bookmark.
# * @param bool $echo Whether or not to echo the results.
# */
#function bookmark_edit_url( $echo = true ) {
#    global $bookmark, $herisson_url;
#    if ( can_herisson_admin() )
#        echo apply_filters('bookmark_edit_url', $herisson_url->urls['manage'] . '&amp;action=editsingle&amp;id=' . $bookmark->id);
#}
#
#/**
# * Returns true if the bookmark is a custom one or false if it is one from Amazon.
# */
#function is_custom_bookmark() {
#    global $bookmark;
#    return empty($bookmark->asin);
#}
#
#/**
# * Returns true if the user has the correct permissions to view the Herisson admin panel.
# */
#function can_herisson_admin() {
#
#//depends on multiuser mode
#    $options = get_option('HerissonOptions');
#    $herisson_level = $options['multiuserMode'] ? 'level_2' : 'level_9';
#
#    return current_user_can($herisson_level);
#}
#
#/**
# * Returns true if the current bookmark is owned by the current user
# * Meaningful only when a user is logged in.
# * Works for both multi-user and single-user modes.
# */
#function is_my_bookmark()
#{
#    global $bookmark, $userdata;
#
#	if (is_user_logged_in())
#	{
#        get_currentuserinfo();
#        return $bookmark->reader == $userdata->ID;
#	}
#	else
#	{
#		return false;
#	}
#}
#
#/**
# * Prints a URL pointing to the main library page that respects the useModRewrite option.
# * @param bool $echo Whether or not to echo the results.
# */
#function library_url( $echo = true ) {
#    $options = get_option('HerissonOptions');
#
#    if ( $options['useModRewrite'] )
#        $url = get_option('home') . "/" . preg_replace("/^\/|\/+$/", "", $options['permalinkBase']);
#    else
#        $url = get_option('home') . '/index.php?herisson_library=true';
#
#    $url = apply_filters('bookmark_library_url', $url);
#
#    if ( $echo )
#        echo $url;
#    return $url;
#}
#
#// Added Begins
#/**
# * Prints the reader's progress (xx of xxx pages read).
# * @param bool $echo Whether or not to echo the results.
# */
#function pages_read( $echo = true ) {
#    global $bookmark;
#	if ( $bookmark->cpages == 0 )
#	  $pages_completed = apply_filters('pages_read', __('Planned Book', HERISSONTD));
#	elseif ( $bookmark->cpages == $bookmark->tpages )
#	  $pages_completed = apply_filters('pages_read', __('Completed Book', HERISSONTD));
#    else
#      $pages_completed = apply_filters('pages_read', $bookmark->cpages . __(' of ', HERISSONTD) . $bookmark->tpages . __(' Pages Read', HERISSONTD));
#
#    if ( $echo )
#        echo $pages_completed;
#	return $pages_completed;
#}
#// Added Ends
#
#/**
# * Prints the bookmark's rating or "Unrated" if the bookmark is unrated.
# * @param bool $echo Whether or not to echo the results.
# */
#function bookmark_rating( $echo = true ) {
#    global $bookmark;
#    if ( $bookmark->rating )
#        $rate = apply_filters('bookmark_rating', $bookmark->rating . ' of 10');
#    else
#        $rate = apply_filters('bookmark_rating', __('Unrated', HERISSONTD));
#
#    if ( $echo )
#        echo $rate;
#	return $rate;
#}
#
#/**
# * Returns a URL to the permalink for a given (custom) page.
# * @param string $page Page name (e.g. custom.php) to create URL for.
# * @param bool $echo Whether or not to echo the results.
# */
#function library_page_url( $page, $echo = true ) {
#    $options = get_option('HerissonOptions');
#
#    if ( $options['useModRewrite'] )
#        $url = get_option('home') . "/" . preg_replace("/^\/|\/+$/", "", $options['permalinkBase']) . "/page/" . urlencode($page);
#    else
#        $url = get_option('home') . '/index.php?herisson_page=' . urlencode($page);
#
#    $url = apply_filters('library_page_url', $url);
#
#    if ( $echo )
#        echo $url;
#    return $url;
#}
#
#/**
# * Returns or prints the currently viewed author.
# * @param bool $echo Whether or not to echo the results.
# */
#function the_bookmark_author( $echo = true ) {
#    $author = htmlentities(stripslashes($GLOBALS['herisson_author']));
#    $author = apply_filters('the_bookmark_author', $author);
#    if ( $echo )
#        echo $author;
#    return $author;
#}
#
#/**
# * Use in the main template loop; if un-fetched, fetches bookmarks for given $query and returns true whilst there are still bookmarks to loop through.
# * @param string $query The query string to pass to get_bookmarks()
# * @return boolean True if there are still bookmarks to loop through, false at end of loop.
# */
#function have_bookmarks( $query ) {
#    global $bookmarks, $current_bookmark;
#    if ( !$bookmarks ) {
#        if ( is_numeric($query) )
#            $GLOBALS['bookmarks'] = get_bookmark($query);
#        else
#            $GLOBALS['bookmarks'] = get_bookmarks($query);
#    }
#    if (is_a($bookmarks, 'stdClass'))
#        $bookmarks = array($bookmarks);
#    $have_bookmarks = ( !empty($bookmarks[$current_bookmark]) );
#    if ( !$have_bookmarks ) {
#        $GLOBALS['bookmarks']			= null;
#        $GLOBALS['current_bookmark']	= 0;
#    }
#    return $have_bookmarks;
#}
#
#/**
# * Advances counter used by have_bookmarks(), and sets the global variable $bookmark used by the template functions. Be sure to call it each template loop to avoid infinite loops.
# */
#function the_bookmark() {
#    global $bookmarks, $current_bookmark;
#    $GLOBALS['bookmark'] = $bookmarks[$current_bookmark];
#    $GLOBALS['current_bookmark']++;
#}

/**
 * Récupération et escaping d'une variable en POST
 * @param var le nom de la variable
 * @return la variable POST escapée
 */
function post($var) { return (! isset($_POST[$var]) ? '' : escape($_POST[$var])); }

/**
 * Récupération et escaping d'une variable en GET
 * @param var le nom de la variable
 * @return la variable GET escapée
 */
function get($var) { return (! isset($_GET[$var]) ? '' : escape($_GET[$var])); }

/**
 * Récupération et escaping d'une variable en POST (ou GET si pas de POST)
 * @param var le nom de la variable
 * @return la variable escapée
 */
function param($var) { $p = post($var); return $p ? $p : get($var); }

/**
 * Escaping en fonction du type de la variable, et de l'environnement.
 * @param str la variable string a escapée
 * @return la variable str escapée correctement
 */
function escape($str) {
 global $wpdb;
# if (! is_array($str) && !get_magic_quotes_gpc()) { return addslashes($str); }
        return $wpdb->escape($str);
}

function remove_menus () {
global $menu;
	$restricted = array(__('Dashboard'), __('Posts'), __('Media'), __('Links'), __('Pages'), __('Appearance'), __('Tools'), __('Users'), __('Settings'), __('Comments'), __('Plugins'), __('Herisson'));
	end ($menu);
	while (prev($menu)){
		$value = explode(' ',$menu[key($menu)][0]);
		if(in_array($value[0] != NULL?$value[0]:"" , $restricted)){unset($menu[key($menu)]);}
	}
}
#add_action('admin_menu', 'remove_menus');

?>
