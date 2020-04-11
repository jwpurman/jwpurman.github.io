<?php
/**
 * executive Theme Customizer
 *
 * @package executive
 */
/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function executive_customize_register( $wp_customize ) {
    $wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
    $wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
    $wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

    $wp_customize->add_section( 'executive_theme_options', array(
        'title'    => __( 'Executive Options', 'executive' ),
        'priority' => 130,
    ) );

    /* Additional page on homepage */
    $wp_customize->add_setting( 'executive_show_pages', array(
        'default' => '',
        'sanitize_callback' => 'executive_sanitize_pages',
    ) );

    $wp_customize->add_control( 'executive_show_pages', array(
        'type' => 'dropdown-pages',
        'label' => 'Choose a  secondary page for your homepage template:',
        'section' => 'executive_theme_options',
    ) );

    /* Front Page: Featured Page One */
    $wp_customize->add_setting( 'executive_featured_page_one_front_page', array(
        'default'           => '',
        'sanitize_callback' => 'executive_sanitize_pages',
    ) );
    $wp_customize->add_control( 'executive_featured_page_one_front_page', array(
        'label'             => __( 'Front Page: Featured Page One', 'executive' ),
        'section'           => 'executive_theme_options',
        'priority'          => 2,
        'type'              => 'dropdown-pages',
    ) );

    /* Front Page: Featured Page Two */
    $wp_customize->add_setting( 'executive_featured_page_two_front_page', array(
        'default'           => '',
        'sanitize_callback' => 'executive_sanitize_pages',
    ) );
    $wp_customize->add_control( 'executive_featured_page_two_front_page', array(
        'label'             => __( 'Front Page: Featured Page Two', 'executive' ),
        'section'           => 'executive_theme_options',
        'priority'          => 3,
        'type'              => 'dropdown-pages',
    ) );

    /* Front Page: Featured Page Three */
    $wp_customize->add_setting( 'executive_featured_page_three_front_page', array(
        'default'           => '',
        'sanitize_callback' => 'executive_sanitize_pages',
    ) );
    $wp_customize->add_control( 'executive_featured_page_three_front_page', array(
        'label'             => __( 'Front Page: Featured Page Three', 'executive' ),
        'section'           => 'executive_theme_options',
        'priority'          => 4,
        'type'              => 'dropdown-pages',
    ) );
}
add_action( 'customize_register', 'executive_customize_register' );

/**
 * Sanitize the drop down select
 */
function executive_sanitize_pages( $input ) {
    if( is_numeric( $input ) ) {
        return intval( $input );
    }
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function executive_customize_preview_js() {
    wp_enqueue_script( 'executive_customizer', get_template_directory_uri() . '/assets/js/customizer.js', array( 'customize-preview' ), '20130508', true );
}
add_action( 'customize_preview_init', 'executive_customize_preview_js' );
