<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package executive
 */

if ( ! function_exists( 'executive_paging_nav' ) ) :
/**
 * Display navigation to next/previous projects in Portfolio page template.
 *
 * @return void
 */
function executive_paging_nav( $max_num_pages = '' ) {
	// Get max_num_pages if not provided
	if ( '' == $max_num_pages ) {
		$max_num_pages = $GLOBALS['wp_query']->max_num_pages;
	}
	// Don't print empty markup if there's only one page.
	if ( $max_num_pages < 2 ) {
		return;
	}
	?>
	<nav class="navigation posts-navigation clearfix" role="navigation">
		<h2 class="screen-reader-text"><?php _e( 'Posts navigation', 'executive' ); ?></h2>
		<div class="nav-links">

			<?php if ( get_next_posts_link( '', $max_num_pages ) ) : ?>
			<div class="nav-previous"><?php next_posts_link( __( 'Previous', 'executive' ), $max_num_pages ); ?></div>
			<?php endif; ?>

			<?php if ( get_previous_posts_link( '', $max_num_pages ) ) : ?>
			<div class="nav-next"><?php previous_posts_link( __( 'Next', 'executive' ), $max_num_pages ); ?></div>
			<?php endif; ?>

		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;

if ( ! function_exists( 'the_posts_navigation' ) ) :
/**
 * Display navigation to next/previous set of posts when applicable.
 *
 * @todo Remove this function when WordPress 4.3 is released.
 */
function the_posts_navigation() {
	// Don't print empty markup if there's only one page.
	if ( $GLOBALS['wp_query']->max_num_pages < 2 ) {
		return;
	}
	?>
	<nav class="navigation posts-navigation" role="navigation">
		<h2 class="screen-reader-text"><?php _e( 'Posts navigation', 'executive' ); ?></h2>
		<div class="nav-links">

			<?php if ( get_next_posts_link() ) : ?>
			<div class="nav-previous"><?php next_posts_link( __( 'Older posts', 'executive' ) ); ?></div>
			<?php endif; ?>

			<?php if ( get_previous_posts_link() ) : ?>
			<div class="nav-next"><?php previous_posts_link( __( 'Newer posts', 'executive' ) ); ?></div>
			<?php endif; ?>

		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;

if ( ! function_exists( 'executive_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function executive_posted_on() {
	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
	}

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);

	$posted_on = sprintf(
		_x( '%s', 'post date', 'executive' ),
		'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
	);

	$byline = sprintf(
		_x( 'by %s', 'post author', 'executive' ),
		'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
	);

	echo '<span class="posted-on">' . $posted_on . '</span>';

}
endif;

if ( ! function_exists( 'executive_entry_footer' ) ) :
/**
 * Prints HTML with meta information for the categories, tags and comments.
 */
function executive_entry_footer() {
	// Hide category and tag text for pages.
	if ( 'post' == get_post_type() ) {
		/* translators: used between list items, there is a space after the comma */
		$categories_list = get_the_category_list( __( ', ', 'executive' ) );
		if ( $categories_list && executive_categorized_blog() ) {
			printf( '<span class="cat-links">' . __( 'Posted in %1$s', 'executive' ) . '</span>', $categories_list );
		}

		/* translators: used between list items, there is a space after the comma */
		$tags_list = get_the_tag_list( '', __( ', ', 'executive' ) );
		if ( $tags_list ) {
			printf( '<span class="tags-links">' . __( 'Tagged %1$s', 'executive' ) . '</span>', $tags_list );
		}
	}

	if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
		echo '<span class="comments-link">';
		comments_popup_link( __( 'Leave a comment', 'executive' ), __( '1 Comment', 'executive' ), __( '% Comments', 'executive' ) );
		echo '</span>';
	}

	edit_post_link( __( 'Edit', 'executive' ), '<span class="edit-link">', '</span>' );
}
endif;

/**
 * Display info pages on Homepage.
 */
function executive_info_pages() {
	$featured_page_1 = esc_attr( get_theme_mod( 'executive_featured_page_one_front_page', '0' ) );
	$featured_page_2 = esc_attr( get_theme_mod( 'executive_featured_page_two_front_page', '0' ) );
	$featured_page_3 = esc_attr( get_theme_mod( 'executive_featured_page_three_front_page', '0' ) );

	if ( 0 == $featured_page_1 && 0 == $featured_page_2 && 0 == $featured_page_3 ) {
		return;
	}
?>

	<div class="featured-page-area">
		<?php for ( $page_number = 1; $page_number <= 3; $page_number++ ) : ?>
			<?php if ( 0 != ${'featured_page_' . $page_number} ) : // Check if a featured page has been set in the customizer ?>

				<?php
					// Create new argument using the page ID of the page set in the customizer
					$featured_page_args = array(
						'page_id' => ${'featured_page_' . $page_number},
					);
					// Create a new WP_Query using the argument previously created
					$featured_page_query = new WP_Query( $featured_page_args );
				?>
				<?php while ( $featured_page_query->have_posts() ) : $featured_page_query->the_post(); ?>

					<div class="featured-page">
					<?php if ( '' != get_the_post_thumbnail() ) : ?>
						<div class="front-featured"><a class="post-thumbnail" href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'executive-front-featured' ); ?></a></div>
					<?php endif; ?>
						<?php get_template_part( 'template-parts/content', 'info-pages' ); ?>
					</div>
				<?php
					endwhile;
					wp_reset_postdata();
				?>

			<?php endif; ?>
		<?php endfor; ?>
	</div>

<?php
}

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function executive_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'executive_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,

			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'executive_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so executive_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so executive_categorized_blog should return false.
		return false;
	}
}

/**
 * Flush out the transients used in executive_categorized_blog.
 */
function executive_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( 'executive_categories' );
}
add_action( 'edit_category', 'executive_category_transient_flusher' );
add_action( 'save_post',     'executive_category_transient_flusher' );
