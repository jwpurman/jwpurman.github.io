<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Shopay
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<?php
		/**
		 * Shim for wp_body_open, ensuring backwards compatibility with versions of WordPress older than 5.2.
		 */
		if ( function_exists( 'wp_body_open' ) ) {
			wp_body_open();
		}
		
		/**
		 * hook - shopay_before_page
		 * 
		 * @hooked - shopay_preloader - 10
		 */
		do_action( 'shopay_before_page' );
	?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'shopay' ); ?></a>	

<?php
	/**
	 * hook - shopay_header_section
	 * 
	 * @hooked - shopay_top_header - 10
	 * @hooked - shopay_main_header - 20
	 * 
	 */
	do_action( 'shopay_header_section' );

	/**
	 * hook - shopay_after_header
	 */
	do_action( 'shopay_after_header' );
?>

	<div id="content" class="site-content">
	<?php
		if ( ! is_front_page() ) {
			echo '<div class="mt-container">';
		}
	?>