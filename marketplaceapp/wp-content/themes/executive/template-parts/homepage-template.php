<?php
/*
 * Template Name: Homepage Template
 *
 * @package executive
*/

get_header();
?>

<?php while ( have_posts() ) : the_post(); ?>

	<?php get_template_part( 'template-parts/content', 'hero' ); ?>

<?php endwhile; ?>

<?php rewind_posts(); ?>

<div class="wrap">

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php while ( have_posts() ) : the_post(); ?>

				<div class="page-content">
					<?php
						the_content();
						wp_link_pages( array(
							'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'executive' ) . '</span>',
							'after'       => '</div>',
							'link_before' => '<span>',
							'link_after'  => '</span>',
						) );
					?>
				</div><!-- .page-content -->

			<?php endwhile; wp_reset_postdata(); ?>

			<div class="our-services">
				<?php executive_info_pages(); ?>
			</div><!-- .recent-posts -->

			<div class="featured-secondary-content">
			<?php
				// $page_option gets a list of published pages from inc/customizer
				$page_option =  esc_attr( get_theme_mod( 'executive_show_pages', '0' ) );
				if ( $page_option ) :

				$page_args = array(
					'page_id' => $page_option,
				);

				$page_query = new WP_Query ( $page_args ); ?>

				<?php while ( $page_query -> have_posts() ) : $page_query -> the_post(); ?>
					<div class="secondary-featured">
						<h2 class="page-title"><?php the_title(); ?></h2>

						<section <?php post_class(); ?>>
							<div class="page-content">
								<?php the_content(); ?>
							</div>
						</section>

						<?php  if ( '' != get_the_post_thumbnail() ) : ?>
							<div class="featured-thumbnail">
								<?php the_post_thumbnail( 'executive-featured-entry-img' ); ?>
							</div>
						<?php endif; ?>
					</div>

				<?php endwhile; endif; wp_reset_postdata(); ?>
			</div><!-- .featured-secondary-content -->

		</main>
	</div>

</div><!-- .wrap -->

<?php
get_footer();
