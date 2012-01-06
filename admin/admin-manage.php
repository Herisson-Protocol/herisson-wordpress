<?php
/**
 * The admin interface for managing and editing books.
 * @package herisson
 */

/**
 * Creates the manage admin page.
 */

function herisson_manage_books() {

    global $wpdb, $herisson_statuses, $userdata;

    get_currentuserinfo();

    $_POST = stripslashes_deep($_POST);

    $options = get_option('HerissonOptions');

    if (!$herisson_url)
	{
        $herisson_url = new herisson_url();
        $herisson_url->load_scheme($options['menuLayout']);
    }

    if (!empty($_GET['updated']))
	{
        $updated = intval($_GET['updated']);

        if ( $updated == 1 )
            $updated .= __(' Book', HERISSONTD);
        else
            $updated .= __(' Books', HERISSONTD);

        echo '
		<div id="message" class="updated fade">
			<p><strong>' . $updated . __(' Updated', HERISSONTD) . '</strong></p>
		</div>
		';
    }

    if (!empty($_GET['deleted']))
	{
        $deleted = intval($_GET['deleted']);

        if ($deleted == 1)
            $deleted .= __(' Book', HERISSONTD);
        else
            $deleted .= __(' Books', HERISSONTD);

        echo '
		<div id="message" class="updated fade">
			<p><strong>' . $deleted . __(' Deleted', HERISSONTD) . '</strong></p>
		</div>
		';
    }

    $action = $_GET['action'];
    herisson_reset_vars(array('action'));

	$options = get_option('HerissonOptions');
	$dateTimeFormat = 'Y-m-d H:i:s';
	if ($options['ignoreTime'])
	{
		$dateTimeFormat = 'Y-m-d';
	}

    switch ($action)
	{
		// Edit Book.
        case 'editsingle':
        {
			$id = intval($_GET['id']);
            $existing = get_book($id);

            echo '
			<div class="wrap">
				<h2>' . __("Edit Book", HERISSONTD) . '</h2>

				<form method="post" action="' . get_option('siteurl') . '/wp-content/plugins/herisson/admin/function-edit.php">
			';

            if ( function_exists('wp_nonce_field') )
                wp_nonce_field('herisson-edit');
            if ( function_exists('wp_referer_field') )
                wp_referer_field();

            echo '
				<div class="book-image">
					<img style="float:left; margin-right: 10px;" id="book-image-0" alt="Book Cover" src="' . $existing->image . '" />
				</div>

				<h3>' . __("Book", HERISSONTD) . ' ' . $existing->id . ':<br /> <cite>' . $existing->title . '</cite><br /> by ' . $existing->author . '</h3>

				<table class="form-table" cellspacing="2" cellpadding="5">

				<input type="hidden" name="action" value="update" />
				<input type="hidden" name="count" value="1" />
				<input type="hidden" name="id[]" value="' . $existing->id . '" />

				<tbody>
				';

			// Title.
            echo '
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="title-0">' . __("Book Title", HERISSONTD) . ':</label>
					</th>
					<td>
						<input type="text" class="main" id="title-0" name="title[]" value="' . $existing->title . '" />
					</td>
				</tr>
				';

			// Author.
            echo '
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="author-0">' . __("Book Author", HERISSONTD) . ':</label>
					</th>
					<td>
						<input type="text" class="main" id="author-0" name="author[]" value="' . $existing->author . '" />
					</td>
				</tr>
				';

			// ASIN.
            echo '
				<tr class="form-field">
					<th valign="top" scope="row">
					<label for="asin-0">' . __("Book ASIN", HERISSONTD) . ':</label>
					</th>
					<td>
					<input type="text" class="main" id="asin-0" name="asin[]" value="' . $existing->asin . '" />
					</td>
				</tr>
				';

			// Image URL.
            echo '
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="image-0">' . __("Book Image URL", HERISSONTD) . ':</label>
					</th>
					<td>
						<input type="text" class="main" id="image-0" name="image[]" value="' . htmlentities($existing->image) . '" />
					</td>
				</tr>

				';

			// Visibility.
            echo '
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="visibility-0">' . __("Book Visibility", HERISSONTD) . ':</label>
					</th>
					<td>
						<select name="visibility[]" id="visibility-0">
							';

					if ($existing->visibility)
					{
						// Public.
						echo '
									<option value="0">' . __("Private", HERISSONTD) . '</option>
									<option value="1" selected="selected">' . __("Public", HERISSONTD) . '</option>
								';
					}
					else
					{
						// Private.
						echo '
									<option value="0" selected="selected">' . __("Private", HERISSONTD) . '</option>
									<option value="1">' . __("Public", HERISSONTD) . '</option>
								';
					}

				echo '
						</select>
						<br><small>' . __("<code>Public Visibility</code> enables a book to appear publicly within the sidebar widget and library.", HERISSONTD) . '</small>
						<br><small>' . __("<code>Private Visibility</code> restricts the visibility of a book to within the administrative interface.", HERISSONTD) . '</small>
					</td>
				</tr>';

			// Status.
            echo '
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="status-0">' . __("Book Status", HERISSONTD) . ':</label>
					</th>
					<td>
						<select name="status[]" id="status-0">
							';
				foreach ( (array) $herisson_statuses as $status => $name ) {
					$selected = '';
					if ( $existing->status == $status )
						$selected = ' selected="selected"';

					echo '
									<option value="' . $status . '"' . $selected . '>' . $name . '</option>
								';
				}

				echo '
						</select>
					</td>
				</tr>';

			// Added Date.
			if (!$options['hideAddedDate'])
			{
				$added = ( herisson_empty_date($existing->added) ) ? '' : date($dateTimeFormat, strtotime($existing->added));
				echo '
					<tr class="form-field">
						<th valign="top" scope="row">
							<label for="added[]">' . __("Date Added to Library", HERISSONTD) . ':</label>
						</th>
						<td>
							<input type="text" id="added-0" name="added[]" value="' . htmlentities($added, ENT_QUOTES, "UTF-8") . '" />
							<br><small>' . __("This date should have been automatically added when the book was added to the Library via the Amazon search form or manual book form. Otherwise, the date must be manually added with the format: <code>YYYY-MM-DD</code>", HERISSONTD) . '</small>
						</td>
					</tr>
					';
			}

			// Started Reading Date.
			$started = ( herisson_empty_date($existing->started) ) ? '' : date($dateTimeFormat, strtotime($existing->started));
            echo '
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="started[]">' . __("Date Book Started", HERISSONTD) . ':</label>
					</th>
					<td>
						<input type="text" id="started-0" name="started[]" value="' . htmlentities($started, ENT_QUOTES, "UTF-8") . '" />
						<br><small>' . __("This date should be automatically added when the <code>Book Status</code> is changed from <code>Future Book</code> to <code>Current Book</code>. Otherwise, the date must be manually added with the format: <code>YYYY-MM-DD</code>", HERISSONTD) . '</small>
					</td>
				</tr>

				';

			// Finished Reading Date.
			$finished = ( herisson_empty_date($existing->finished) ) ? '' : date($dateTimeFormat, strtotime($existing->finished));
            echo '
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="finished[]">' . __("Date Book Completed", HERISSONTD) . ':</label>
					</th>
					<td>
						<input type="text" id="finished-0" name="finished[]" value="' . htmlentities($finished, ENT_QUOTES, "UTF-8") . '" />
						<br><small>' . __("This date should be automatically added when the <code>Book Status</code> is changed from <code>Current Book</code> to <code>Completed Book</code>. Otherwise, the date must be manually added with the format: <code>YYYY-MM-DD</code>", HERISSONTD) . '</small>
					</td>
				</tr>

				';

// Added Begins
			// Total Pages.
            echo '
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="tpages[]">' . __("Book Total Pages", HERISSONTD) . ':</label>
					</th>
					<td>
						<input type="text" name="tpages[]" value="' . $existing->tpages . '" />
					</td>
				</tr>';
				
			// Completed Pages.
            echo '
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="cpages[]">' . __("Book Completed Pages", HERISSONTD) . ':</label>
					</th>
					<td>
						<input type="text" name="cpages[]" value="' . $existing->cpages . '" />
					</td>
				</tr>';
// Added Ends

			// Rating.
            echo '
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="rating[]"><label for="rating">' . __("Book Rating", HERISSONTD) . ':</label></label>
					</th>
					<td>
						<select name="rating[]" id="rating-' . $i . '" style="width:100px;">
							<option value="unrated">&nbsp;</option>
							';
            for ($i = 10; $i >=1; $i--) {
                $selected = ($i == $existing->rating) ? ' selected="selected"' : '';
                echo "
										<option value='$i'$selected>$i</option>";
            }
            echo '
						</select>
					</td>
				</tr>';

			// Book Review.
            echo '
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="posts[]">' . __("Book Review", HERISSONTD) . ':</label>
					</th>
					<td>
						<input type="text" name="posts[]" value="' . intval($existing->post) . '" />
						<br><small>' . __("If you desire, you may link this book to a <code>blog post review</code> by entering the <code>blog post's ID</code>. In order to discover the blog post's ID, list 'All Posts' (within the 'Posts' menu) and hover your mouse over the review's blog post title. The ID will appear in the status bar of your browser within the link (i.e. post=23 where '23' is the post ID). The blog post review will be linked within the sidebar widget and the book's library page.", HERISSONTD) . '</small>
					</td>
				</tr>';
			
// Added Begin
			if ($options['multiuserMode']) {
			    if ( current_user_can('administrator') ) {
			// Book Reader.
            echo '
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="status-0">' . __("Book Reader", HERISSONTD) . ':</label>
					</th>
					<td>';

					wp_dropdown_users(array('selected' => $existing->reader, 'name' => 'reader[]'));

			echo '
					<br><small>' . __("The current user responsible for reading and reviewing this book is selected in the dropdown.", HERISSONTD) . '</small>
					<br><small>' . __("You may change the user responsible for reading and reviewing this book by selecting the user in the dropdown.", HERISSONTD) . '</small>
					</td>
				</tr>';
				}
			}
// Added End

			echo '
				</tbody>
				</table>

				<p class="submit">
					<input class="button" type="submit" value="' . __("Save", NRTD) . '" />
				</p>

				</form>

			</div>
				';
		}
		break;

		// Book Manager.
		default:
		{
			//depends on multiusermode
			if ($options['multiuserMode']) {
			    if ( current_user_can('administrator') ) {
					$count = total_books(0, 0); //counting all books
				} else {
					$count = total_books(0, 0, $userdata->ID); //counting only current users books
				}
			} else {
				$count = total_books(0, 0); //counting all books
			}


			if ( $count ) {
				if ( !empty($_GET['q']) )
					$search = '&search=' . urlencode($_GET['q']);
				else
					$search = '';

				if ( empty($_GET['p']) )
					$page = 1;
				else
					$page = intval($_GET['p']);

				if ( empty($_GET['o']) )
					$order = 'desc';
				else
					$order = urlencode($_GET['o']);

				if ( empty($_GET['s']) )
					$orderby = 'started';
				else
					$orderby = urlencode($_GET['s']);

				// Filter by Author.
				if (empty($_GET['author']))
					$author = '';
				else
					$author = "&author=" . urlencode($_GET['author']);

				// Filter by Status.
				if (empty($_GET['status']))
					$status = '';
				else
					$status = "&status=" . urlencode($_GET['status']);

				$perpage = $options['booksPerPage'];
				$offset = ($page * $perpage) - $perpage;
				$num = $perpage;
				$pageq = "&num=$num&offset=$offset";

// Added Begin
				// Depends on multiuser mode.
				if ($options['multiuserMode']) {
					if ( current_user_can('administrator') ) {
						$reader = '';
				// Filter by Reader Begin.
						if (empty($_GET['reader'])) {
							$reader = '';
						} else {
							$reader = "&reader=" . urlencode($_GET['reader']);
						}
				// Filter by Reader End.
					} else {
						$reader = "&reader=".$userdata->ID;
					}
				} else {
					$reader = '';
				}
// Added End

				$books = get_books("num=-1&status=all&orderby={$orderby}&order={$order}{$search}{$pageq}{$reader}{$author}{$status}");
				$count = count($books);

				$numpages = ceil(total_books(0, 0, $userdata->ID) / $perpage);

				$pages = '<span class="displaying-num">' . __("Pages", HERISSONTD) . '</span>';

				if ( $page > 1 ) {
					$previous = $page - 1;
					$pages .= " <a class='page-numbers prev' href='{$herisson_url->urls['manage']}&p=$previous&s=$orderby&o=$order'>&laquo;</a>";
				}

				for ( $i = 1; $i <= $numpages; $i++) {
					if ( $page == $i )
						$pages .= "<span class='page-numbers current'>$i</span>";
					else
						$pages .= " <a class='page-numbers' href='{$herisson_url->urls['manage']}&p=$i&s=$orderby&o=$order'>$i</a>";
				}

				if ( $numpages > $page ) {
					$next = $page + 1;
					$pages .= " <a class='page-numbers next' href='{$herisson_url->urls['manage']}&p=$next&s=$orderby&o=$order'>&raquo;</a>";
				}

				echo '
				<div class="wrap">

					<h2>' . __("Herisson", HERISSONTD) . '</h2>

						<form method="get" action="" onsubmit="location.href += \'&q=\' + document.getElementById(\'q\').value; return false;">
							<p class="search-box"><label class="hidden" for="q">' . __("Search Books", HERISSONTD) . ':</label> <input type="text" name="q" id="q" value="' . htmlentities($_GET['q']) . '" /> <input class="button" type="submit" value="' . __('Search Books', HERISSONTD) . '" /></p>
						</form>

							<ul>
				';
				if (!empty($_GET['q']) || !empty($_GET['author']) || !empty($_GET['status']) || !empty($_GET['reader']))
				{
					echo '
								<li><a href="' . $herisson_url->urls['manage'] . '">' . __('Show All Books', HERISSONTD) . '</a></li>
					';
				}

				echo '
								<li><a href="' . library_url(0) . '">' . __('View Library Page', HERISSONTD) . '</a></li>
							</ul>

						<div class="tablenav">
							<div class="tablenav-pages">
								' . $pages . '
							</div>
						</div>


					<br style="clear:both;" />

					<form method="post" action="' . get_option('siteurl') . '/wp-content/plugins/herisson/admin/function-edit.php">
				';

				if ( function_exists('wp_nonce_field') )
					wp_nonce_field('herisson-edit');
				if ( function_exists('wp_referer_field') )
					wp_referer_field();

				echo '
					<input type="hidden" name="action" value="update" />
					<input type="hidden" name="count" value="' . $count . '" />
				';

				$i = 0;

				if ( $order == 'desc' )
					$new_order = 'asc';
				else
					$new_order = 'desc';

				$title_sort_link = "{$herisson_url->urls['manage']}&p=$page&s=book&o=$new_order$author";
				$author_sort_link = "{$herisson_url->urls['manage']}&p=$page&s=author&o=$new_order$author";
				$added_sort_link = "{$herisson_url->urls['manage']}&p=$page&s=added&o=$new_order$author";
				$started_sort_link = "{$herisson_url->urls['manage']}&p=$page&s=started&o=$new_order$author";
				$finished_sort_link = "{$herisson_url->urls['manage']}&p=$page&s=finished&o=$new_order$author";
				$status_sort_link = "{$herisson_url->urls['manage']}&p=$page&s=status&o=$new_order$author";
				$reader_sort_link = "{$herisson_url->urls['manage']}&p=$page&s=reader&o=$new_order$author";

				echo '
					<table class="widefat post fixed" cellspacing="0">
						<thead>
							<tr>
								<th></th>
								<th class="manage-column column-title"><a class="manage_books" href="'. $title_sort_link .'">' . __("Book", HERISSONTD) . '</a></th>
								<th class="manage-column column-author"><a class="manage_books" href="'. $author_sort_link .'">' . __("Author", HERISSONTD) . '</a></th>
								<th><a class="manage_books" href="'. $status_sort_link .'">' . __("Status", HERISSONTD) . '</a></th>';

// Added Begin
							if ($options['multiuserMode']) {
								if ( current_user_can('administrator') ) {
							echo '
								<th><a class="manage_books" href="'. $reader_sort_link .'">' . __("Reader", HERISSONTD) . '</a></th>';
								}
							}
// Added End

							echo '
								<th><a class="manage_books" href="'. $started_sort_link .'">' . __("Started", HERISSONTD) . '</a></th>
								<th><a class="manage_books" href="'. $finished_sort_link .'">' . __("Finished", HERISSONTD) . '</a></th>';

				if (!$options['hideAddedDate'])
				{
					echo '
								<th><a class="manage_books" href="'. $added_sort_link .'">' . __("Added", HERISSONTD) . '</a></th>';
				}

				echo '
							</tr>
						</thead>
						<tbody>
				';

				foreach ((array)$books as $book)
				{

					$alt = ( $i % 2 == 0 ) ? ' alternate' : '';

					$delete = get_option('siteurl') . '/wp-content/plugins/herisson/admin/function-edit.php?action=delete&id=' . $book->id;
					$delete = wp_nonce_url($delete, 'herisson-delete-book_' .$book->id);


					echo '
						<tr class="manage-book' . $alt . '">

							<input type="hidden" name="id[]" value="' . $book->id . '" />
							<input type="hidden" name="title[]" value="' . $book->title . '" />
							<input type="hidden" name="author[]" value="' . $book->author . '" />

							<td>
								<img style="max-width:100px;" id="book-image-' . $i . '" class="small" alt="' . __('Book Cover', HERISSONTD) . '" src="' . $book->image . '" />
							</td>

							<td class="post-title column-title">
								<strong>' . stripslashes($book->title) . '</strong>
								<div class="row-actions">
									<a href="' . book_permalink(0, $book->id) . '">' . __('View', HERISSONTD) . '</a> |
										<a href="' . $herisson_url->urls['manage'] . '&amp;action=editsingle&amp;id=' . $book->id . '">' . __('Edit', HERISSONTD) . '</a> | <a href="' . $delete . '" onclick="return confirm(\'' . __("Are you sure you wish to delete this book permanently?", HERISSONTD) . '\')">' . __("Delete", HERISSONTD) . '</a>
								</div>
							</td>

							<td>
								<a href="' . $herisson_url->urls['manage'] . '&amp;author=' . $book->author . '">' . $book->author . '</a>
							</td>';
							
// Added Begin
					foreach ( (array) $herisson_statuses as $current_status => $status_name ) {
						if ( $book->status == $current_status )
					echo '
							<td>
								<a href="' . $herisson_url->urls['manage'] . '&amp;status=' . $book->status . '">' . $status_name . '</a>
							</td>';
					}

					if ($options['multiuserMode']) {
						if ( current_user_can('administrator') ) {
						$reader_name = get_userdata( $book->reader );
					echo '
							<td>
								<a href="' . $herisson_url->urls['manage'] . '&amp;reader=' . $book->reader . '">' . $reader_name->display_name . '</a>
							</td>';
						}
					}
// Added End

					echo '
							<td>
							' . ( ( herisson_empty_date($book->started) ) ? '' : date($dateTimeFormat, strtotime($book->started)) ) . '
							</td>

							<td>
							' .( ( herisson_empty_date($book->finished) ) ? '' : date($dateTimeFormat, strtotime($book->finished)) ) . '
							</td>';

						if (!$options['hideAddedDate'])
						{
							echo '
							<td>
							' . ( ( herisson_empty_date($book->added) ) ? '' : date($dateTimeFormat, strtotime($book->added)) ) . '
							</td>';
						}

					echo '
						</tr>
					';

					$i++;

				}

				echo '
					</tbody>
					</table>

					</form>
				';

			} else {
				echo '
				<div class="wrap">
					<h2>' . __("Manage Books", HERISSONTD) . '</h2>
					<p>' . sprintf(__("No books to display. To add some books, head over <a href='%s'>here</a>.", HERISSONTD), $herisson_url->urls['add']) . '</p>
				</div>
				';
			}

			echo '
			</div>
			';
		}
		break;
    }
}
?>