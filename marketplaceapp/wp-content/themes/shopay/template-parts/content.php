<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Shopay
 */
if ( has_post_thumbnail() ) {
	$shopay_thumb_class = 'has-thumbnail';
} else {
	$shopay_thumb_class = 'no-thumbnail';
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( $shopay_thumb_class ); ?>>
	<div class="article-thumb">
     	<?php
			if ( has_post_thumbnail() ) {
		?>
				<figure class="post-thumb shopay-bg-image cover-image" style="background-image:url( <?php echo esc_url( get_the_post_thumbnail_url() ); ?> )">
				</figure>
		<?php
			}
		?>
	</div><!-- article-thumb -->
	
	<header class="entry-header">
		<?php
			if ( is_singular() ) :
				the_title( '<h1 class="entry-title cover-font">', '</h1>' );
			else :
				the_title( '<h2 class="entry-title cover-font"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
			endif;
		?>
	</header><!-- .entry-header -->
	
		<?php
		if ( 'post' === get_post_type() ) :
		?>
			<div class="entry-meta">
				<?php shopay_posted_on(); ?>
				<?php shopay_entry_footer(); ?>
			</div><!-- .entry-meta -->
		<?php endif; ?>
	<div class="entry-content">
		<?php
			the_excerpt( sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers */
					__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'shopay' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				get_the_title()
			) );

			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'shopay' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->

</article><!-- #post-<?php the_ID(); ?> -->