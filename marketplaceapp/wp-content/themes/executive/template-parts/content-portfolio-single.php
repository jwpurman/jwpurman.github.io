<?php
/**
 * @package executive
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<header class="entry-header">
		<?php
			echo get_the_term_list( $post->ID, 'jetpack-portfolio-type', '<div class="entry-meta"><span class="portfolio-type-links">', _x(', ', 'Used between list items, there is a space after the comma.', 'executive' ), '</span></div>' );
		?>
	</header>

	<div class="entry-content">
		<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'executive' ) ); ?>
		<?php
			wp_link_pages( array(
				'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'executive' ) . '</span>',
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
			) );
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php
			echo get_the_term_list( $post->ID, 'jetpack-portfolio-tag', '<span class="tags-links">', _x(', ', 'Used between list items, there is a space after the comma.', 'executive' ), '</span>' );
		?>

		<?php edit_post_link( __( 'Edit', 'executive' ), '<span class="edit-link">', '</span>' ); ?>
	</footer><!-- .entry-meta -->
</article><!-- #post-## -->
