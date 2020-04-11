<?php
/**
 * The template for displaying Projects on index view
 *
 * @package executive
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<a href="<?php the_permalink(); ?>" rel="bookmark" class="image-link" tabindex="-1">
		<div class="content-wrap">
			<?php  if ( '' != get_the_post_thumbnail() ) : ?>
				<div class="port-thumbnail">
					<?php the_post_thumbnail( 'executive-portfolio-image' ); ?>
				</div>
			<?php endif; ?>

			<div class="port-content">
				<h2 class="port-title"><?php the_title(); ?></h2>
			</div>
		</div>
	</a>
</article><!-- #post-## -->
