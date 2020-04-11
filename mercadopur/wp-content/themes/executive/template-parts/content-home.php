<?php
/**
 * The template for displaying Projects on index view
 *
 * @package executive
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php  if ( '' != get_the_post_thumbnail() ) : ?>
		<a href="<?php the_permalink(); ?>" rel="bookmark" class="image-link" tabindex="-1">
			<?php the_post_thumbnail( 'executive-portfolio-img' ); ?>
		</a>
	<?php endif; ?>

	<h2 class="entry-title">
		<a href="<?php the_permalink(); ?>" rel="bookmark">
			<?php the_title(); ?>
		</a>
	</h2>

	<div class="entry-content">
		<?php the_excerpt(); ?>
	</div>
</article><!-- #post-## -->

