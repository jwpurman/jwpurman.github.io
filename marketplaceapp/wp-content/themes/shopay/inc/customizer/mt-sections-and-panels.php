<?php
/**
 * Shopay Theme Section Groups and Panel.
 *
 * @package Shopay
 */

add_action ( 'customize_register', 'shopay_customizer_register_sections_and_panels' );

if ( ! function_exists ( 'shopay_customizer_register_sections_and_panels' ) ) :
    
    /**
     * Register customizer panels, section groups and sections.
     */
    function shopay_customizer_register_sections_and_panels ( $wp_customize ) {

    	/**
         * Theme Option Panel
         *
         * Appearance > Customize > Theme Options
         * @since 1.0.0
         */
        $wp_customize->add_panel( 'shopay_theme_options_panel',
        	array(
	            'priority'       => 5,
	            'capability'     => 'edit_theme_options',
	            'theme_supports' => '',
	            'title'          => __( 'Theme Options', 'shopay' )
	        )
        );

        /**
         * General Section Group
         *
         * Theme Options > General
         * @since 1.0.0
         */
        $wp_customize->add_section( new Shopay_Customize_Section (
	        $wp_customize, 'shopay_general_group',
		        array(
		            'priority' 		=> 10,
		            'panel'    		=> 'shopay_theme_options_panel',
		            'capability'	=> 'edit_theme_options',
		            'theme_supports' => '',
		            'title'    		=> esc_html__( 'General', 'shopay' )
		        )
		    )
		);

        /**
         * Header Section Group
         *
         * Theme Options > Header
         * @since 1.0.0
         */
        $wp_customize->add_section( new Shopay_Customize_Section (
            $wp_customize, 'shopay_header_group',
                array(
                    'priority'      => 30,
                    'panel'         => 'shopay_theme_options_panel',
                    'capability'    => 'edit_theme_options',
                    'theme_supports' => '',
                    'title'         => esc_html__( 'Header', 'shopay' )
                )
            )
        );

        /**
         * Placed Site identity section into Header Group
         *
         * Theme Options > Header > Site Identity
         * @since 1.0.0
         */
        $wp_customize->add_section( new Shopay_Customize_Section(
            $wp_customize, 'title_tagline',
                array(
                    'priority'      => 10,
                    'panel'         => 'shopay_theme_options_panel',
                    'section'       => 'shopay_header_group',
                    'capability'    => 'edit_theme_options',
                    'theme_options' => '',
                    'title'         => __( 'Site Identity', 'shopay' )
                )
            )
        );

        /**
         * Checkbox option for site title and tagline
         *
         * Theme Options > Header > Site Identity
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'shopay_display_header_text',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => true,
                'sanitize_callback' => 'shopay_sanitize_checkbox'
            )
        );

        $wp_customize->add_control( 'shopay_display_header_text',
            array(
                'priority'      => 50,
                'section'       => 'title_tagline',
                'settings'      => 'shopay_display_header_text',
                'label'         => __( 'Display Site Title and Tagline', 'shopay' ),
                'type'          => 'checkbox'
            )
        );

        /**
         * Homepage Section Group
         *
         * Theme Options > Homepage
         * @since 1.0.0
         */
        $wp_customize->add_section( new Shopay_Customize_Section (
            $wp_customize, 'shopay_homepage_group',
                array(
                    'priority'      => 40,
                    'panel'         => 'shopay_theme_options_panel',
                    'capability'    => 'edit_theme_options',
                    'theme_supports' => '',
                    'title'         => esc_html__( 'Homepage', 'shopay' )
                )
            )
        );

        /**
         * Design Section Group
         *
         * Theme Options > Design
         * @since 1.0.0
         */
        $wp_customize->add_section( new Shopay_Customize_Section (
            $wp_customize, 'shopay_design_group',
                array(
                    'priority'      => 50,
                    'panel'         => 'shopay_theme_options_panel',
                    'capability'    => 'edit_theme_options',
                    'theme_supports' => '',
                    'title'         => esc_html__( 'Design', 'shopay' )
                )
            )
        );

        /**
         * Footer Section Group
         *
         * Theme Options > Footer
         * @since 1.0.0
         */
        $wp_customize->add_section( new Shopay_Customize_Section (
            $wp_customize, 'shopay_footer_group',
                array(
                    'priority'      => 100,
                    'panel'         => 'shopay_theme_options_panel',
                    'capability'    => 'edit_theme_options',
                    'theme_supports' => '',
                    'title'         => esc_html__( 'Footer', 'shopay' )
                )
            )
        );

    }

endif;
