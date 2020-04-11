<?php
/**
 * Add Site Layout section and it's fields inside General section group.
 * 
 * @package Shopay
 */

add_action( 'customize_register', 'shopay_register_site_layout_fields' );

if ( ! function_exists( 'shopay_register_site_layout_fields' ) ) :

    /**
     * Register site layout section's fields.
     */
    function shopay_register_site_layout_fields ( $wp_customize ) {

    	/**
         * Site Layout Section
         *
         * Theme Options > General > Site Layout
         * @since 1.0.0
         */
        $wp_customize->add_section( new Shopay_Customize_Section(
        	$wp_customize, 'shopay_section_site_style',
	            array(
	                'priority'  	=> 10,
	                'panel'     	=> 'shopay_theme_options_panel',
	                'section'		=> 'shopay_general_group',
	                'capability'    => 'edit_theme_options',
	                'theme_options' => '',
	                'title'     	=> __( 'Site Style', 'shopay' )
	            )
	        )
        );

        /**
         * Radio image field for Site Layout
         *
         * Theme Options > General > Site Layout > Site Style
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'shopay_site_layout',
            array(
            	'capability'    	=> 'edit_theme_options',
	            'theme_options' 	=> '',
                'default'           => 'full-width',
                'sanitize_callback' => 'sanitize_key',
            )
        );

        $wp_customize->add_control( new Shopay_Control_Radio_Image(
            $wp_customize, 'shopay_site_layout',
                array(
                	'priority'      => 10,
                    'section'       => 'shopay_section_site_style',
                    'settings'      => 'shopay_site_layout',
                    'label'         => __( 'Site Layout', 'shopay' ),
                    'description'   => __( 'Choose from available layouts', 'shopay' ),
                    'choices'  => array(
                        'full-width'    => array(
                            'title'     => __( 'Fullwidth', 'shopay' ),
                            'src'       => get_template_directory_uri() . '/assets/images/full-width.png'
                        ),
                        'boxed-layout'  => array(
                            'title'     => __( 'Boxed', 'shopay' ),
                            'src'       => get_template_directory_uri() . '/assets/images/boxed-layout.png'
                        )
                    )
                )
            )
        );

        /**
         * placed default background image section
         *
         * Theme Options > General > Site Layout > Site Style
         * @since 1.0.0
         */
        /**
         * Placed Site identity section into Header Group
         *
         * Theme Options > Header > Site Identity
         * @since 1.0.0
         */
        $wp_customize->add_section( new Shopay_Customize_Section(
            $wp_customize, 'background_image',
                array(
                    'priority'          => 20,
                    'panel'             => 'shopay_theme_options_panel',
                    'section'           => 'shopay_section_site_style',
                    'capability'        => 'edit_theme_options',
                    'theme_options'     => '',
                    'title'             => __( 'Background Image', 'shopay' ),
                    'active_callback'   => 'shopay_site_layout_bg_active_callback',
                )
            )
        );

        /**
         * Divider field
         *
         * Theme Options > General > Site Layout > Site Style
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'shopay_site_layout_divider',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'sanitize_callback' => 'sanitize_text_field'
            )
        );

        $wp_customize->add_control( new Shopay_Control_Divider(
            $wp_customize, 'shopay_site_layout_divider',
                array(
                    'priority'      => 20,
                    'section'       => 'shopay_section_site_style',
                    'settings'      => 'shopay_site_layout_divider',
                )
            )
        );

        /**
         * Toggle option for wow animation.
         *
         * Theme Options > General > Site Layout > Site Style
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'shopay_wow_option',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => true,
                'sanitize_callback' => 'shopay_sanitize_checkbox'
            )
        );

        $wp_customize->add_control( new Shopay_Control_Toggle(
            $wp_customize, 'shopay_wow_option',
                array(
                    'priority'      => 30,
                    'section'       => 'shopay_section_site_style',
                    'settings'      => 'shopay_wow_option',
                    'label'         => __( 'Enable Wow Animation', 'shopay' )
                )
            )
        );

        /**
         * Toggle option for sidebar sticky.
         *
         * Theme Options > General > Site Layout > Site Style
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'shopay_sticky_sidebar_option',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => true,
                'sanitize_callback' => 'shopay_sanitize_checkbox'
            )
        );

        $wp_customize->add_control( new Shopay_Control_Toggle(
            $wp_customize, 'shopay_sticky_sidebar_option',
                array(
                    'priority'      => 40,
                    'section'       => 'shopay_section_site_style',
                    'settings'      => 'shopay_sticky_sidebar_option',
                    'label'         => __( 'Enable Sticky Sidebar', 'shopay' )
                )
            )
        );

    }

endif;