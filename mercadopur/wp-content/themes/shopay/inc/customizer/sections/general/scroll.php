<?php
/**
 * Add Scroll to Top section and it's fields inside General section group.
 * 
 * @package Shopay
 */

add_action( 'customize_register', 'shopay_register_general_scroll_fields' );

if ( ! function_exists( 'shopay_register_general_scroll_fields' ) ) :

    /**
     * Register Scroll to Top section's fields.
     */
    function shopay_register_general_scroll_fields ( $wp_customize ) {

        /**
         * Scroll to Top Section
         *
         * Theme Options > General > Scroll to Top
         * @since 1.0.0
         */
        $wp_customize->add_section( new Shopay_Customize_Section(
            $wp_customize, 'shopay_section_general_scroll',
                array(
                    'priority'      => 70,
                    'panel'         => 'shopay_theme_options_panel',
                    'section'       => 'shopay_general_group',
                    'capability'    => 'edit_theme_options',
                    'theme_options' => '',
                    'title'         => __( 'Scroll to Top', 'shopay' )
                )
            )
        );

        /**
         * Toggle option for scroll to top
         *
         * Theme Options > Footer > Scroll to Top
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'shopay_scroll_to_top',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => true,
                'sanitize_callback' => 'shopay_sanitize_checkbox'
            )
        );

        $wp_customize->add_control( new Shopay_Control_Toggle(
            $wp_customize, 'shopay_scroll_to_top',
                array(
                    'priority'      => 10,
                    'section'       => 'shopay_section_general_scroll',
                    'settings'      => 'shopay_scroll_to_top',
                    'label'         => __( 'Enable Scroll to Top', 'shopay' )
                )
            )
        );

    }

endif;