<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package Shopay
 */

get_header();
?>
<div class="shopay-all-content-wrapper">
	<section id="primary" class="content-area">
		<main id="main" class="site-main">

		<?php if ( have_posts() ) : ?>

			<header class="page-header">
				<h1 class="page-title">
					<?php
						/* translators: %s: search query. */
						printf( esc_html__( 'Search Results for: %s', 'shopay' ), '<span>' . get_search_query() . '</span>' );
					?>
				</h1>
			</header><!-- .page-header -->

	<div class="search-article-wrapper">
		<?php
			/* Start the Loop */
			while ( have_posts() ) :
			the_post();

				/**
				 * Run the loop for the search to output the results.
				 * If you want to overload this in a child theme then include a file
				 * called content-search.php and that will be used instead.
				 */
				if ( 'product' == get_post_type() ) {
					echo '<article>';
						wc_get_template_part( 'content', 'product' );
					echo '</article>';
				} else {
					get_template_part( 'template-parts/content', 'search' );
				}

			endwhile;

			the_posts_navigation();

		else :

			get_template_part( 'template-parts/content', 'none' );

		endif;
		?>
	</div><!-- search-article-wrapper -->
		</main><!-- #main -->
	</section><!-- #primary -->

<?php
	get_sidebar();
?>
</div><!-- .shopay-all-content-wrapper -->
<?php
	get_footer();