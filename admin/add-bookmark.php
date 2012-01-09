<?php	
/**
 * Our admin interface for adding books.
 * @package herisson
 */

if ( !function_exists('herisson_add_bookark') ) {
/**
 * The write admin page deals with the searching for and ultimate addition of books to the database.
 */
    function herisson_add_bookmark() {

        $_POST = stripslashes_deep($_POST);

        global $wpdb;

        $options = get_option('HerissonOptions');

        if( !$herisson_url ) {
            $herisson_url = new herisson_url();
            $herisson_url->load_scheme($options['menuLayout']);
        }

        if ( !empty($_GET['error']) ) {
            echo '
			<div id="message" class="error fade">
				<p><strong>' . __("Error adding book!", HERISSONTD) . '</strong></p>
			</div>
			';
        }

        if ( !empty($_GET['added']) ) {
            echo '
			<div id="message" class="updated fade">
				<p><strong>' . __("Book Added", HERISSONTD) . '</strong></p>
				<ul>
					<li><a href="' . $herisson_url->urls['manage'] . '">' . __("Manage Books", HERISSONTD) . ' &raquo;</a></li>
					<li><a href="' . apply_filters('book_edit_url', $herisson_url->urls['manage'] . '&action=editsingle&id=' . intval($_GET['added'])) . '">' . __("Edit This Book", HERISSONTD) . ' &raquo;</a></li>
					<li><a href="' . library_url(0) . '">' . __("View Library", HERISSONTD) . ' &raquo;</a></li>
					<li><a href="' . get_option('home') . '">' . __("View Site", HERISSONTD) . ' &raquo;</a></li>
				</ul>
			</div>
			';
        }

        echo '
		<div class="wrap">

			<h2>' . __("Herisson", HERISSONTD) . '</h2>
		';

        if (  !empty($_POST['u_isbn']) || !empty($_POST['u_author']) || !empty($_POST['u_title']) ) {

            echo '<h3>' . __("Search Results", HERISSONTD) . '</h3>';

            $isbn	= $_POST['u_isbn'];
            $author	= $_POST['u_author'];
            $title	= $_POST['u_title'];
            if ( !empty($_POST['u_isbn']) )
                $using_isbn = true;

            if ( $using_isbn )
                $results = query_amazon("isbn=$isbn");
            else
                $results = query_amazon("title=$title&author=$author");

            if ( is_wp_error($results) ) {
                foreach ( (array) $results->get_error_codes() as $code ) {
                    if ( $code == 'curl-not-installed' ) {
                        echo '
							<div id="message" class="error fade">
								<p><strong>' . __("Oops!", HERISSONTD) . '</strong></p>
								<p>' . __("I couldn't fetch the results for your search, because you don't have cURL installed!", HERISSONTD) . '</p>
								<p>' . __("To solve this problem, please switch your <strong>HTTP Library</strong> setting to <strong>Snoopy</strong>, which works on virtually all server setups.", HERISSONTD) . '</p>
								<p>' . sprintf(__("You can change your options <a href='%s'>here</a>.", HERISSONTD), $herisson_url->urls['options']) . '</p>
							</div>
						';
                    }
                }
            } else {
                if ( !$results ) {
                    if ( $using_isbn )
                        echo '<div class="error"><p>' . sprintf(__("Sorry, but amazon%s did not return any results for the ISBN number <code>%s</code>.", HERISSONTD), $options['domain'], $isbn) . '</p></div>';
                    else
                        echo '<div class="error"><p>' . sprintf(__("Sorry, but amazon%s did not return any results for the book &ldquo;%s&rdquo;", HERISSONTD), $options['domain'], $title) . '</p></div>';
                } else {
                    if ( $using_isbn )
                        echo '<p>' . sprintf(__("You searched for the ISBN <code>%s</code>. amazon%s returned these results:", HERISSONTD), $isbn, $options['domain']) . '</p>';
                    else
                        echo '<p>' . sprintf(__("You searched for the book &ldquo;%s&rdquo;. amazon%s returned these results:", HERISSONTD), $title, $options['domain']) . '</p>';

                    foreach ( (array) $results as $result ) {
                        extract($result);
                        $data = serialize($result);
                        echo '
						<form method="post" action="' . get_option('siteurl') . '/wp-content/plugins/herisson/admin/function-add.php" style="border:1px solid #ccc; padding:5px; margin:5px;">
						';

                        if ( function_exists('wp_nonce_field') )
                            wp_nonce_field('herisson-add');

                        echo '
							<input type="hidden" name="amazon_data" value="' . htmlentities($data, ENT_QUOTES, "UTF-8") . '" />

							<img src="' . htmlentities($image, ENT_QUOTES, "UTF-8") . '" alt="" style="float:left; margin:8px; padding:2px; width:46px; height:70px; border:1px solid #ccc;" />

							<h3>' . htmlentities($title, ENT_QUOTES, "UTF-8") . '</h3>
							' . (($author) ? '<p>' . __("by ", HERISSONTD) . '<strong>' . htmlentities($author, ENT_QUOTES, "UTF-8") . '</strong></p>' : '<p>(' . __("No author", HERISSONTD) . ')</p>') . '
							' . (($ed) ? htmlentities($ed, ENT_QUOTES, "UTF-8") . ' ' : '
							') . (($binding) ? htmlentities($binding, ENT_QUOTES, "UTF-8") . ' ' : '
							') . (($date) ? ' - ' . htmlentities($date, ENT_QUOTES, "UTF-8") . ' ' : '
							') . (($publisher) ? ' (' . htmlentities($publisher, ENT_QUOTES, "UTF-8") . ') ' : '
							') . '
							<p style="clear:left;"><input class="button" type="submit" value="' . __("Use This Result", HERISSONTD) . '" /></p>

						</form>
						';
                    }
                }
            }

        }

        echo '

		<div class="herisson-add-grouping">

			<h3>' . __("Add a bookmark", HERISSONTD) . '</h3>

			<form method="post" action="' . get_option('siteurl') . '/wp-content/plugins/herisson/admin/action-add-bookmark.php">
			 ';

    if ( function_exists('wp_nonce_field') ) wp_nonce_field('herisson-add-bookmark');

    echo '
				<p><label for="title">' . __("Title", HERISSONTD) . ':</label><br />
				<input type="text" name="title" id="title" size="50" /></p>

				<p><label for="author">' . __("Url", HERISSONTD) . ':</label><br />
				<input type="text" name="url" id="url" size="50" /></p>

				<p><label for="tags">' . __("Tags", HERISSONTD) . ':</label><br />
				<input type="text" name="tags" id="tags" size="50" /></p>

				<p><label for="description">' . __("Description", HERISSONTD) . ':</label><br />
				<textarea rows="3" cols="55" name="description"></textarea></p>

				<p><input class="button" type="submit" value="' . __("Add bookmark", HERISSONTD) . '" /></p>

			</form>

			</div>

		<div class="herisson-add-grouping">

			<h3>' . __("Import from Del.icio.us", HERISSONTD) . '</h3>

			<form method="post" action="' . get_option('siteurl') . '/wp-content/plugins/herisson/admin/action-import-bookmarks.php">
			 ';

    if ( function_exists('wp_nonce_field') ) wp_nonce_field('herisson-import-bookmarks');

    echo '
				<p><label for="title">' . __("Login", HERISSONTD) . ':</label><br />
				<input type="text" name="title" id="title" size="50" /></p>

				<p><label for="author">' . __("Password", HERISSONTD) . ':</label><br />
				<input type="password" name="password" id="password" size="50" /></p>

				<p><input class="button" type="submit" value="' . __("Import bookmarks", HERISSONTD) . '" /></p>

			</form>

			</div>

		</div>
		';

    }
}

?>
