<?php
/**
 * traversify-lite Theme Customizer
 *
 * @package traversify-lite
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function traversify_lite_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial( 'blogname', array(
			'selector'        => '.site-title a',
			'render_callback' => 'traversify_lite_customize_partial_blogname',
		) );
		$wp_customize->selective_refresh->add_partial( 'blogdescription', array(
			'selector'        => '.site-description',
			'render_callback' => 'traversify_lite_customize_partial_blogdescription',
		) );
	}

/**
* Adding the customizer option
*/

	$wp_customize->add_panel(
        'theme_options',
        array(
            'title' => esc_html__( 'Theme Options','traversify-lite' ),
            'priority' => 2,
        )
    );

/* General Section */
    $wp_customize->add_section(
        'general_option',
        array(
            'title'    => esc_html__( 'General Options','traversify-lite' ),
            'panel' => 'theme_options',
            'priority' => 2,
        )
    );
/* Destination Section */
	$wp_customize->add_section(
        'destination_section',
        array(
            'title'    => esc_html__( 'Destination Options','traversify-lite' ),
            'panel' => 'theme_options',
            'priority' => 3,
        )
    );

    /* Trip Section */
    $wp_customize->add_section(
        'trip_section',
        array(
            'title'    => esc_html__( 'Trip Options','traversify-lite' ),
            'panel' => 'theme_options',
            'priority' => 4,
        )
    );

     /* Banner Section */
    $wp_customize->add_section(
        'banner_section',
        array(
            'title'    => esc_html__( 'Banner Options','traversify-lite' ),
            'panel' => 'theme_options',
            'priority' => 1.1,
        )
    );

     /* Blog Section */
    $wp_customize->add_section(
        'blog_section',
        array(
            'title'    => esc_html__( 'Blog Options','traversify-lite' ),
            'panel' => 'theme_options',
            'priority' => 5,
        )
    );

      /* Blog Section */
    $wp_customize->add_section(
        'about_section',
        array(
            'title'    => esc_html__( 'About Us Options','traversify-lite' ),
            'panel' => 'theme_options',
            'priority' => 5,
        )
    );

     /* CTA Section */
    $wp_customize->add_section(
        'cta_section',
        array(
            'title'    => esc_html__( 'CTA Options','traversify-lite' ),
            'panel' => 'theme_options',
            'priority' => 5,
        )
    );


    require get_template_directory() . '/inc/settings/customizer-options.php';
    require get_template_directory() . '/inc/settings/customizer-additional.php';


}
add_action( 'customize_register', 'traversify_lite_customize_register' );

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function traversify_lite_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function traversify_lite_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function traversify_lite_customize_preview_js() {
	wp_enqueue_script( 'traversify-lite-customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20151215', true );
}
add_action( 'customize_preview_init', 'traversify_lite_customize_preview_js' );

