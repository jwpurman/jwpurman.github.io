<?php
/**
 * Add Breadcrumb section and it's fields inside Header section group.
 * 
 * @package Shopay
 */

add_action( 'customize_register', 'shopay_register_breadcrumb_fields' );

if ( ! function_exists( 'shopay_register_breadcrumb_fields' ) ) :

    /**
     * Register Breadcrumb section's fields.
     */
    function shopay_register_breadcrumb_fields ( $wp_customize ) {

        /**
         * Breadcrumb Section
         *
         * Theme Options > Header > Breadcrumb
         * @since 1.0.0
         */
        $wp_customize->add_section( new Shopay_Customize_Section(
            $wp_customize, 'shopay_section_header_breadcrumb',
                array(
                    'priority'          => 50,
                    'panel'             => 'shopay_theme_options_panel',
                    'section'           => 'shopay_header_group',
                    'capability'        => 'edit_theme_options',
                    'theme_options'     => '',
                    'title'             => __( 'Breadcrumb', 'shopay' )
                )
            )
        );

        /**
         * Toggle option for breadcrumb.
         *
         * Theme Options > Header > Breadcrumb
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'shopay_breadcrumbs',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => true,
                'sanitize_callback' => 'shopay_sanitize_checkbox'
            )
        );

        $wp_customize->add_control( new Shopay_Control_Toggle(
            $wp_customize, 'shopay_breadcrumbs',
                array(
                    'priority'      => 10,
                    'section'       => 'shopay_section_header_breadcrumb',
                    'settings'      => 'shopay_breadcrumbs',
                    'label'         => __( 'Enable Breadcrumb', 'shopay' )
                )
            )
        );

        /**
         * Text field for breadcrumb home label
         *
         * Theme Options > Header > Breadcrumb
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'shopay_breadcrumbs_home_lable',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => __( 'Home', 'shopay' ),
                'sanitize_callback' => 'sanitize_text_field'
            )
        );
        
        $wp_customize->add_control( 'shopay_breadcrumbs_home_lable',
            array(
                'priority'  => 20,
                'section'   => 'shopay_section_header_breadcrumb',
                'settings'  => 'shopay_breadcrumbs_home_lable',
                'label'     => __( 'Home Label', 'shopay' ),
                'type'      => 'text'
            )
        );

    }

endif;