<?php
/**
 * Add Preloader section and it's fields inside General section group.
 * 
 * @package Shopay
 */

add_action( 'customize_register', 'shopay_register_preloader_fields' );

if ( ! function_exists( 'shopay_register_preloader_fields' ) ) :

    /**
     * Register preloader section's fields.
     */
    function shopay_register_preloader_fields ( $wp_customize ) {

    	/**
         * Preloader Section
         *
         * Theme Options > General > Preloader
         * @since 1.0.0
         */
        $wp_customize->add_section( new Shopay_Customize_Section(
        	$wp_customize, 'shopay_section_preloader',
	            array(
	                'priority'  	=> 15,
	                'panel'     	=> 'shopay_theme_options_panel',
	                'section'		=> 'shopay_general_group',
	                'capability'    => 'edit_theme_options',
	                'theme_options' => '',
	                'title'     	=> __( 'Preloader', 'shopay' )
	            )
	        )
        );

        /**
         * Toggle option for preloader.
         *
         * Theme Options > General > Preloader
         */
        $wp_customize->add_setting( 'shopay_preloader_option',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => true,
                'sanitize_callback' => 'shopay_sanitize_checkbox'
            )
        );

        $wp_customize->add_control( new Shopay_Control_Toggle(
            $wp_customize, 'shopay_preloader_option',
                array(
                    'priority'      => 10,
                    'section'       => 'shopay_section_preloader',
                    'settings'      => 'shopay_preloader_option',
                    'label'         => __( 'Enable Preloader', 'shopay' )
                )
            )
        );

    }

endif;