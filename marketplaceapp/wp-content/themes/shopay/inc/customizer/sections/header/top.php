<?php
/**
 * Add Top Bar section and it's fields inside Header section group.
 * 
 * @package Shopay
 */

add_action( 'customize_register', 'shopay_register_top_bar_fields' );

if ( ! function_exists( 'shopay_register_top_bar_fields' ) ) :

    /**
     * Register Top Bar section's fields.
     */
    function shopay_register_top_bar_fields ( $wp_customize ) {

    	/**
         * Top Bar Section
         *
         * Theme Options > Header > Top Bar
         * @since 1.0.0
         */
        $wp_customize->add_section( new Shopay_Customize_Section(
        	$wp_customize, 'shopay_section_header_top_bar',
	            array(
	                'priority'  	=> 5,
	                'panel'     	=> 'shopay_theme_options_panel',
	                'section'		=> 'shopay_header_group',
	                'capability'    => 'edit_theme_options',
	                'theme_options' => '',
	                'title'     	=> __( 'Top Bar', 'shopay' )
	            )
	        )
        );

        /**
         * Toggle option for top bar.
         *
         * Theme Options > Header > Top Bar
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'shopay_top_header_option',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => true,
                'sanitize_callback' => 'shopay_sanitize_checkbox'
            )
        );

        $wp_customize->add_control( new Shopay_Control_Toggle(
            $wp_customize, 'shopay_top_header_option',
                array(
                    'priority'      => 10,
                    'section'       => 'shopay_section_header_top_bar',
                    'settings'      => 'shopay_top_header_option',
                    'label'         => __( 'Enable Top Bar', 'shopay' )
                )
            )
        );

        /**
         * Text field for short description.
         *
         * Theme Options > Header > Top Bar
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'shopay_top_header_description',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => __( 'Welcome To Worldwide Store', 'shopay' ),
                'transport'         => 'postMessage',
                'sanitize_callback' => 'shopay_sanitize_textarea_field'
            )
        );
        
        $wp_customize->add_control( 'shopay_top_header_description',
            array(
                'priority'  => 20,
                'section'   => 'shopay_section_header_top_bar',
                'settings'  => 'shopay_top_header_description',
                'label'     => __( 'Short Description', 'shopay' ),
                'type'      => 'text'
            )
        );

        /**
         * Text field for site location
         *
         * Theme Options > Header > Top Bar
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'shopay_top_header_location',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => __( 'Store Locater', 'shopay' ),
                'transport'         => 'postMessage',
                'sanitize_callback' => 'sanitize_text_field'
            )
        );
        
        $wp_customize->add_control( 'shopay_top_header_location',
            array(
                'priority'  => 30,
                'section'   => 'shopay_section_header_top_bar',
                'settings'  => 'shopay_top_header_location',
                'label'     => __( 'Site Location', 'shopay' ),
                'type'      => 'text'
            )
        );

        /**
         * Text field for site service
         *
         * Theme Options > Header > Top Bar
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'shopay_top_header_service',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => __( 'Free Delivery', 'shopay' ),
                'transport'         => 'postMessage',
                'sanitize_callback' => 'sanitize_text_field'
            )
        );
        
        $wp_customize->add_control( 'shopay_top_header_service',
            array(
                'priority'  => 40,
                'section'   => 'shopay_section_header_top_bar',
                'settings'  => 'shopay_top_header_service',
                'label'     => __( 'Site Service', 'shopay' ),
                'type'      => 'text'
            )
        );

    }

endif;