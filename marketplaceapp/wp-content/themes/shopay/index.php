<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Shopay
 */
get_header();

/**
 * hook - shopay_before_content
 *
 * @hooked - shopay_homepage_section - 10
 */
do_action( 'shopay_before_content' );

if ( is_front_page() ) {
	$shopay_front_latest_posts_option = get_theme_mod( 'shopay_front_latest_posts_option', true );
} else {
	$shopay_front_latest_posts_option = apply_filters( 'shopay_front_latest_posts_option', true );
}

if ( true == $shopay_front_latest_posts_option ) {
?>
	<div class="mt-container">
		<div class="shopay-all-content-wrapper">
			<div id="primary" class="content-area">
				<main id="main" class="site-main">

				<?php
				if ( have_posts() ) :

					if ( is_home() && ! is_front_page() ) :
						?>
						<header>
							<h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
						</header>
						<?php
					endif;

					/* Start the Loop */
					while ( have_posts() ) :
						the_post();

						/*
						* Include the Post-Type-specific template for the content.
						* If you want to override this in a child theme, then include a file
						* called content-___.php (where ___ is the Post Type name) and that will be used instead.
						*/
						get_template_part( 'template-parts/content', get_post_type() );

					endwhile;

					the_posts_navigation();

				else :

					get_template_part( 'template-parts/content', 'none' );

				endif;
				?>

				</main><!-- #main -->
			</div><!-- #primary -->

			<?php get_sidebar(); ?>
		</div><!-- .shopay-all-content-wrapper -->
	</div><!-- .mt-container -->
<?php
}
get_footer();