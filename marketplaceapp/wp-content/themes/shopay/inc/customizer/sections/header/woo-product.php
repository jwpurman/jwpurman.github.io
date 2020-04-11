<?php
/**
 * Add Woo Products section and it's fields inside Header section group.
 * 
 * @package Shopay
 */

add_action( 'customize_register', 'shopay_register_woo_products_fields' );

if ( ! function_exists( 'shopay_register_woo_products_fields' ) ) :

    /**
     * Register Woo Products section's fields.
     */
    function shopay_register_woo_products_fields ( $wp_customize ) {

        /**
         * Woo Products Section
         *
         * Theme Options > Header > Woo Products
         * @since 1.0.0
         */
        $wp_customize->add_section( new Shopay_Customize_Section(
            $wp_customize, 'shopay_section_header_woo_products',
                array(
                    'priority'          => 25,
                    'panel'             => 'shopay_theme_options_panel',
                    'section'           => 'shopay_header_group',
                    'capability'        => 'edit_theme_options',
                    'theme_options'     => '',
                    'title'             => __( 'Woo Products', 'shopay' ),
                    'description'       => __( 'Only manage WooCommerce Highlight Products', 'shopay' ),
                    'active_callback'   => 'shopay_is_woo_acitvated_active_callback'
                )
            )
        );

        /**
         * Toggle option for highlight woo products
         *
         * Theme Options > Header > Woo Products
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'shopay_highlight_products_option',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => false,
                'sanitize_callback' => 'shopay_sanitize_checkbox'
            )
        );

        $wp_customize->add_control( new Shopay_Control_Toggle(
            $wp_customize, 'shopay_highlight_products_option',
                array(
                    'priority'          => 10,
                    'section'           => 'shopay_section_header_woo_products',
                    'settings'          => 'shopay_highlight_products_option',
                    'label'             => __( 'Enable Highlight Woo Products', 'shopay' ),
                    'active_callback'   => 'shopay_is_woo_acitvated_active_callback',
                )
            )
        );

        /**
         * Text field for highlight woo products title
         *
         * Theme Options > Header > Woo Products
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'shopay_highlight_products_title',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => __( 'Trending Products', 'shopay' ),
                'transport'         => 'postMessage',
                'sanitize_callback' => 'sanitize_text_field'
            )
        );
        
        $wp_customize->add_control( 'shopay_highlight_products_title',
            array(
                'priority'          => 20,
                'section'           => 'shopay_section_header_woo_products',
                'settings'          => 'shopay_highlight_products_title',
                'label'             => __( 'Section Title', 'shopay' ),
                'type'              => 'text',
                'active_callback'   => 'shopay_highlight_product_option_with_woo_active_callback',
            )
        );

    }

endif;