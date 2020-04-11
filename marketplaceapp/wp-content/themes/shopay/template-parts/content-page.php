<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Shopay
 */
if ( has_post_thumbnail() ) {
	$thumb_class = 'has-thumbnail';
} else {
	$thumb_class = 'no-thumbnail';
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( $thumb_class ); ?>>
	<?php if ( has_post_thumbnail() ) { ?>
			<figure class="post-thumb shopay-bg-image cover-image" style="background-image:url( <?php echo esc_url( get_the_post_thumbnail_url() ); ?> )">
			</figure>
	<?php } ?>
	<header class="entry-header">
		<?php the_title( '<h1 class="entry-title cover-font">', '</h1>' ); ?>
	</header><!-- .entry-header -->
	<div class="entry-content">
		<?php
			the_content();

			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'shopay' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->

	<?php if ( get_edit_post_link() ) : ?>
		<footer class="entry-footer">
			<?php
				edit_post_link(
					sprintf(
						wp_kses(
							/* translators: %s: Name of current post. Only visible to screen readers */
							__( 'Edit <span class="screen-reader-text">%s</span>', 'shopay' ),
							array(
								'span' => array(
									'class' => array(),
								),
							)
						),
						get_the_title()
					),
					'<span class="edit-link">',
					'</span>'
				);
			?>
		</footer><!-- .entry-footer -->
	<?php endif; ?>
</article><!-- #post-<?php the_ID(); ?> -->