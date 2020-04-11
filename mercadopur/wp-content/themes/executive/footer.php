<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package executive
 */

?>

	</div><!-- #content -->

	<footer id="colophon" class="site-footer" role="contentinfo">
		<div class="site-credits">
			<div class="site-info">
				<?php wp_nav_menu( array( 'theme_location' => 'footer', 'fallback_cb' => 'false', 'depth' => 1 ) ); ?>

				<div class="copyright">
					<?php printf( esc_html__( '%1$s by %2$s.', 'executive' ), 'Powered', '<a href="https://wordpress.org/" rel="designer">WordPress</a>' ); ?>

					<?php printf( esc_html__( '%1$s by %2$s.', 'executive' ), 'Executive Theme', '<a href="https://portwebdev.com/" rel="designer">PortWebDev</a>' ); ?>
				</div>
			</div><!-- .site-info -->

			<?php
				wp_nav_menu( array(
					'theme_location' => 'social',
					'depth' => 1,
					'link_before' => '<span class="screen-reader-text">',
					'link_after' => '</span>',
					'container_class' => 'social-links',
					'fallback_cb' => 'false',
				) );
			?>

		</div><!-- .site-credits -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
