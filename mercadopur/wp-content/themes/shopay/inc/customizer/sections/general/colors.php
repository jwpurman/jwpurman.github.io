<?php
/**
 * Add Colors section and it's fields inside General section group.
 * 
 * @package Shopay
 */

add_action( 'customize_register', 'shopay_register_colors_fields' );

if ( ! function_exists( 'shopay_register_colors_fields' ) ) :

    /**
     * Register Colors section's fields.
     */
    function shopay_register_colors_fields ( $wp_customize ) {

    	/**
         * Colors Section
         *
         * Theme Options > General > Colors
         * @since 1.0.0
         */
        $wp_customize->add_section( new Shopay_Customize_Section(
        	$wp_customize, 'shopay_section_colors',
	            array(
	                'priority'  	=> 20,
	                'panel'     	=> 'shopay_theme_options_panel',
	                'section'		=> 'shopay_general_group',
	                'capability'    => 'edit_theme_options',
	                'theme_options' => '',
	                'title'     	=> __( 'Colors', 'shopay' )
	            )
	        )
        );

        /**
         * Base Colors Section
         *
         * Theme Options > General > Colors > Base Colors
         * @since 1.0.0
         */
        $wp_customize->add_section( new Shopay_Customize_Section(
            $wp_customize, 'shopay_section_base_colors',
                array(
                    'priority'      => 10,
                    'panel'         => 'shopay_theme_options_panel',
                    'section'       => 'shopay_section_colors',
                    'capability'    => 'edit_theme_options',
                    'theme_options' => '',
                    'title'         => __( 'Base Colors', 'shopay' )
                )
            )
        );

        /**
         * Changed the section for default color control
         *
         * Theme Options > General > Colors > Base Colors
         * @since 1.0.0
         */
        $wp_customize->get_control( 'background_color' )->section = 'shopay_section_base_colors';

        /**
         * Color option for Header Text color.
         *
         * Theme Options > General > Colors > Base Colors
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'shopay_header_textcolor',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => '#000000',
                'sanitize_callback' => 'sanitize_hex_color'
            )
        );

        $wp_customize->add_control( new WP_Customize_Color_Control(
            $wp_customize, 'shopay_header_textcolor',
                array(
                    'priority'      => 5,
                    'section'       => 'shopay_section_base_colors',
                    'settings'      => 'shopay_header_textcolor',
                    'label'         => __( 'Header Text Color', 'shopay' )
                )
            )
        );

        /**
         * Color option for primary theme color.
         *
         * Theme Options > General > Colors > Base Colors
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'shopay_primary_color',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => '#ed6d23',
                'sanitize_callback' => 'sanitize_hex_color'
            )
        );

        $wp_customize->add_control( new WP_Customize_Color_Control(
            $wp_customize, 'shopay_primary_color',
                array(
                    'priority'      => 20,
                    'section'       => 'shopay_section_base_colors',
                    'settings'      => 'shopay_primary_color',
                    'label'         => __( 'Theme Color', 'shopay' )
                )
            )
        );

    }

endif;