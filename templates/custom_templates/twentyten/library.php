<?php get_header(); ?>
<div id="container">
<div class="content">
	<div id="content" class="herisson primary narrowcolumn">
	<div class="post">
		<h2 class="entry-title"><?php _e('My Library', HERISSONTD) ?></h2>
		<?php if( can_herisson_admin() ) : ?>
		<br><?php _e('Library Admin: ', HERISSONTD) ?><a href="<?php manage_library_url() ?>"><?php _e('Manage Books', HERISSONTD) ?></a><br>
		<?php endif; ?>
		<?php print_book_stats() ?>
		<h3><b><u><?php _e('Current Books ', HERISSONTD) ?>(<?php echo total_books('reading', 0) ?>):</u></b></h3>
		<?php if( have_books('status=reading&orderby=desc&num=-1') ) : ?>
			<ol>
			<?php while( have_books('status=reading&orderby=desc&num=-1') ) : the_book(); ?>
			<li><a href="<?php book_permalink() ?>"><?php book_title() ?></a><?php _e(' by ', HERISSONTD) ?><?php book_author() ?></li>
			<?php endwhile; ?>
			</ol>
		<?php else : ?>
			<p><?php _e('None', HERISSONTD) ?></p>
		<?php endif; ?>

		<h3><b><u><?php _e('Planned Books ', HERISSONTD) ?>(<?php echo total_books('unread', 0) ?>):</u></b></h3>
		<?php if( have_books('status=unread&orderby=desc&num=-1') ) : ?>
			<ol>
			<?php while( have_books('status=unread&orderby=desc&num=-1') ) : the_book(); ?>
			<li><a href="<?php book_permalink() ?>"><?php book_title() ?></a><?php _e(' by ', HERISSONTD) ?><?php book_author() ?></li>
			<?php endwhile; ?>
			</ol>
		<?php else : ?>
			<p><?php _e('None', HERISSONTD) ?></p>
		<?php endif; ?>

		<h3><b><u><?php _e('Completed Books ', HERISSONTD) ?>(<?php echo total_books('read', 0) ?>):</u></b></h3>
		<?php if( have_books('status=read&orderby=finished&order=desc&num=-1') ) : ?>
			<ol>
			<?php while( have_books('status=read&orderby=finished&order=desc&num=-1') ) : the_book(); ?>
			<li><a href="<?php book_permalink() ?>"><?php book_title() ?></a><?php _e(' by ', HERISSONTD) ?><?php book_author() ?></li>
			<?php endwhile; ?>
			</ol>
		<?php else : ?>
			<p><?php _e('None', HERISSONTD) ?></p>
		<?php endif; ?>

		<h3><b><u><?php _e('Books on Hold ', HERISSONTD) ?>(<?php echo total_books('onhold', 0) ?>):</u></b></h3>
		<?php if( have_books('status=onhold&orderby=desc&num=-1') ) : ?>
			<ol>
			<?php while( have_books('status=onhold&orderby=desc&num=-1') ) : the_book(); ?>
			<li><a href="<?php book_permalink() ?>"><?php book_title() ?></a><?php _e(' by ', HERISSONTD) ?><?php book_author() ?></li>
			<?php endwhile; ?>
			</ol>
		<?php else : ?>
			<p><?php _e('None', HERISSONTD) ?></p>
		<?php endif; ?>
	</div>
	</div>
</div><!-- #content -->
</div><!-- #container -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>