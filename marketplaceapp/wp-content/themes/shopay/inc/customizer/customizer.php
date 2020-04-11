<?php
/**
 * Shopay Theme Customizer
 *
 * @package Shopay
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function shopay_customize_register( $wp_customize ) {

	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	$wp_customize->register_section_type( 'Shopay_Section_Upsell' );

    /**
     * Register theme upsell sections.
     *
     * @since 1.0.2
     */
    $wp_customize->add_section( new Shopay_Section_Upsell(
        $wp_customize,
            'shopay_theme_upsell',
            array(
                'title'    	=> esc_html__( 'Shopay Pro', 'shopay' ),
                'pro_text' 	=> esc_html__( 'Buy Now', 'shopay' ),
                'pro_url'  	=> 'https://mysterythemes.com/wp-themes/shopay-pro/',
                'priority' 	=> 1,
            )
        )
    );

}

add_action( 'customize_register', 'shopay_customize_register' );

/*---------------------------------------------------------------------------------------------------------------------------------*/
/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function shopay_customize_preview_js() {
	global $shopay_theme_version;
	
	wp_enqueue_script( 'shopay-customizer', get_template_directory_uri() . '/assets/js/customizer.js', array( 'customize-preview' ), esc_attr( $shopay_theme_version ), true );
}
add_action( 'customize_preview_init', 'shopay_customize_preview_js' );

/*-----------------------------------------------------------------------------------------------------------------------------------*/
/**
 * Enqueue required scripts/styles for customizer panel
 *
 * @since 1.0.0
 */
function shopay_customize_backend_scripts() {
 	
    global $shopay_theme_version;

	wp_enqueue_style( 'shopay-font-awesome', get_template_directory_uri() . '/assets/library/font-awesome/css/all.min.css', '', '5.10.2', 'all' );

	wp_enqueue_style( 'shopay-customizer-style', get_template_directory_uri() . '/assets/css/mt-customizer-style.css', array(), esc_attr( $shopay_theme_version ) );

	wp_enqueue_script( 'extend-customizer', get_template_directory_uri(). '/assets/js/mt-customizer-extend.js', array('jquery'), esc_attr( $shopay_theme_version ), true );

     wp_enqueue_style( 'shopay-theme-upsell-style', get_template_directory_uri() . '/inc/customizer/custom-controls/theme-upsell/theme-upsell.css', null );
    
    wp_enqueue_script( 'shopay-theme-upsell-script', get_template_directory_uri() . '/inc/customizer/custom-controls/theme-upsell/theme-upsell.js', array( 'jquery' ), false, true );

    wp_enqueue_script( 'shopay-customizer-control-script', get_template_directory_uri() . '/assets/js/customizer-control.js', array( 'jquery' ), esc_attr( $shopay_theme_version ), true );
}
add_action( 'customize_controls_enqueue_scripts', 'shopay_customize_backend_scripts', 10 );

/*-----------------------------------------------------------------------------------------------------------------------------------*/
/**
 * Load Customizer files.
 */
require get_template_directory(). '/inc/customizer/custom-controls/custom-controls.php';
require get_template_directory(). '/inc/customizer/callback.php';
require get_template_directory(). '/inc/customizer/sanitize.php';

require get_template_directory(). '/inc/customizer/custom-controls/class-mt-customize-panel.php';
require get_template_directory(). '/inc/customizer/custom-controls/class-mt-customize-section.php';
require get_template_directory(). '/inc/customizer/mt-sections-and-panels.php';

$shopay_sub_sections = array(
	'general'		=> array( 'site-layout', 'preloader', 'colors', 'social-icons', 'scroll' ),
	'header'		=> array( 'top', 'main', 'woo-product', 'breadcrumb' ),
	'homepage'		=> array( 'home-widget-area', 'sponsors', 'services' ),
	'design'		=> array( 'sidebar' ),
	'footer'		=> array( 'main', 'bottom' )
);

foreach ( $shopay_sub_sections as $key => $value ) {
	foreach ( $value as $k => $v ) {
		require get_template_directory() . '/inc/customizer/sections/'. $key . '/' . $v .'.php';
	}
}