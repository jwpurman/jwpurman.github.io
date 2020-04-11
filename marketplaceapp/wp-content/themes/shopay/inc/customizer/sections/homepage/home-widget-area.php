<?php
/**
 * Add Homepage Sections Area section and it's fields inside Homepage Settings section group.
 * 
 * @package Shopay
 */

add_action( 'customize_register', 'shopay_register_homepage_widgets_fields' );

if ( ! function_exists( 'shopay_register_homepage_widgets_fields' ) ) :

    /**
     * Register Homepage Sections section's fields.
     */
    function shopay_register_homepage_widgets_fields ( $wp_customize ) {

        /**
         * Homepage Sections Section
         *
         * Theme Options > Homepage > Homepage Sections
         * @since 1.0.0
         */
        $wp_customize->add_section( new Shopay_Customize_Section(
            $wp_customize, 'sidebar-widgets-homepage-section',
                array(
                    'priority'      => 10,
                    'panel'         => 'shopay_theme_options_panel',
                    'section'       => 'shopay_homepage_group',
                    'capability'    => 'edit_theme_options',
                    'theme_options' => '',
                    'title'         => __( 'Homepage Sections', 'shopay' ),
                    'description'   => __( 'Add widgets to show on homepage content area.', 'shopay' )
                )
            )
        );

        /**
         * Toggle option for widget area
         *
         * Customize > Homepage Settings
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'shopay_front_latest_posts_option',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => true,
                'sanitize_callback' => 'shopay_sanitize_checkbox'
            )
        );

        $wp_customize->add_control( new Shopay_Control_Toggle(
            $wp_customize, 'shopay_front_latest_posts_option',
                array(
                    'priority'      => 10,
                    'section'       => 'static_front_page',
                    'settings'      => 'shopay_front_latest_posts_option',
                    'label'         => __( 'Enable Latest Posts', 'shopay' )
                )
            )
        );

    }

endif;