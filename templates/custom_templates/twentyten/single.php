<?php get_header(); ?>
<?php global $herisson_id; ?>
<div id="container">
<div class="content">
	<div id="content" class="herisson primary narrowcolumn">
	<div class="post">
		<?php if( have_books(intval($herisson_id)) ) : ?>
			<?php while ( have_books(intval(herisson_id)) ) : the_book(); ?>
			<h2 class="entry-title"><?php book_title() ?></h2>
			<?php if( can_herisson_admin() ) : ?>
			<br><?php _e('Library Admin: ', HERISSONTD) ?><a href="<?php manage_library_url() ?>"><?php _e('Manage Books', HERISSONTD) ?></a> &raquo; <a href="<?php book_edit_url() ?>"><?php _e('Edit Book', HERISSONTD) ?></a><br>
			<?php endif; ?>
			<br>
			<table width="100%" style="font-size:14px; border:none;">
			  <tr>
			    <td style="border:none; vertical-align:top;"><a href="<?php book_url() ?>"><img src="<?php book_limage() ?>" alt="<?php book_title() ?>" /></a></td>
				<td style="border:none; vertical-align:top;"><b><?php _e('Title:', HERISSONTD) ?></b> <?php book_title() ?><br>
				    <b><?php _e('Author:', HERISSONTD) ?></b> <?php book_author() ?><br>
			<?php if( !is_custom_book() ): ?>
				<b><?php _e('Description:', HERISSONTD) ?></b> <a target="_blank" href="<?php book_url() ?>"><?php _e('Amazon Detail Page', HERISSONTD) ?></a><br>
			<?php endif; ?>
			<?php if( !is_custom_book() ): ?>
			<b><?php _e('Reviews:', HERISSONTD) ?></b> <a target="_blank" href="<?php book_url() ?>#customerReviews"><?php _e('Amazon Customer Reviews', HERISSONTD) ?></a><br>
			<?php endif; ?>
			<b><?php _e('Progress:', HERISSONTD) ?></b> <?php pages_read() ?><br>
			<b><?php _e('My Rating:', HERISSONTD) ?></b> <?php book_rating() ?> <?php book_review_link() ?>
				</td>
			  </tr>
			</table>
			<br>
			<table width="100%" style="font-size:14px; border: 1px solid #e2e2e2;">
			  <tr>
			    <td><b><?php _e('Started:', HERISSONTD) ?></b> <?php book_started() ?></td>
				<td><b><?php _e('Finished:', HERISSONTD) ?></b> <?php book_finished() ?></td>
			  </tr>
			</table>
			<br>
			<form>
			<input style="font-size:16px;" type="button" value="<?php _e("Complete Library", HERISSONTD) ?>" onclick="window.location.href='<?php library_url() ?>'">
			</form>
<!--			<a href="<?php library_url() ?>"><?php _e('Return to My Library', HERISSONTD) ?></a> --><!-- alternate text version -->
			<?php endwhile; ?>
			<?php else : ?>
			<p><?php _e('That particular book does not exist.', HERISSONTD) ?></p>
		<?php endif; ?>
	</div>
	</div>
</div><!-- #content -->
</div><!-- #container -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>