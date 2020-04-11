<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package executive
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function executive_body_classes( $classes ) {
	// Adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	if ( is_page_template( 'template-parts/homepage-template.php' ) ) {
		$classes[] = 'homepage';
	}

	if ( is_page_template( 'template-parts/portfolio-page.php' ) ) {
		$classes[] = 'portfolio';
	}

	if ( is_page_template( 'template-parts/full-width-page.php' ) ) {
		$classes[] = 'full-width';
	}

	return $classes;
}
add_filter( 'body_class', 'executive_body_classes' );

/**
 * Extend the default WordPress post classes for pre 3.9
 *
 * Adds a post class to denote:
 * 1. Non-password protected page with a post thumbnail.
 *
 * @param array $classes A list of existing post class values.
 * @return array The filtered post class list.
 */
function executive_post_classes( $classes ) {

	if ( ! post_password_required() && has_post_thumbnail() ) {
		$classes[] = 'has-post-thumbnail';
	}

	return $classes;
}
add_filter( 'post_class', 'executive_post_classes' );

if( ! function_exists( 'executive_excerpt_length' ) ) {
	function executive_excerpt_length( $length ) {
		return 30;
	}
	add_filter( 'excerpt_length', 'executive_excerpt_length' );
}

if ( ! function_exists( 'executive_continue_reading_link' ) ) :
/**
 * Returns an ellipsis and "Continue reading" plus off-screen title link for excerpts
 */
function executive_continue_reading_link() {
	return '<a href="'. esc_url( get_permalink() ) . '" class="more-link">' . sprintf( __( 'Read More <span class="screen-reader-text">%1$s</span>', 'executive' ), esc_attr( strip_tags( get_the_title() ) ) ) . '</a>';
}
endif; // executive_continue_reading_link

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with executive_continue_reading_link().
 *
 * To override this in a child theme, remove the filter and add your own
 * function tied to the excerpt_more filter hook.
 */
function executive_auto_excerpt_more( $more ) {
	return executive_continue_reading_link();
}
add_filter( 'excerpt_more', 'executive_auto_excerpt_more' );

/**
 * Adds a pretty "Continue Reading" link to custom post excerpts.
 *
 * To override this link in a child theme, remove the filter and add your own
 * function tied to the get_the_excerpt filter hook.
 */
function executive_custom_excerpt_more( $output ) {
	if ( has_excerpt() && ! is_attachment() ) {
		$output .= executive_continue_reading_link();
	}
	return $output;
}
add_filter( 'get_the_excerpt', 'executive_custom_excerpt_more' );

/**
 * Custom Logo
 */
if ( ! function_exists( 'executive_the_custom_logo' ) ) :
/**
 * Displays the optional custom logo.
 *
 * Does nothing if the custom logo is not available.
 *
 * @since executive 1.0.2
 */
function executive_the_custom_logo() {
	if ( function_exists( 'the_custom_logo' ) ) {
		the_custom_logo();
	}
}
endif;
