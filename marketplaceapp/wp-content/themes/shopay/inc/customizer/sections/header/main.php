<?php
/**
 * Add Main Area section and it's fields inside Header section group.
 * 
 * @package Shopay
 */

add_action( 'customize_register', 'shopay_register_main_area_fields' );

if ( ! function_exists( 'shopay_register_main_area_fields' ) ) :

    /**
     * Register Main Area section's fields.
     */
    function shopay_register_main_area_fields ( $wp_customize ) {

    	/**
         * Main Area Section
         *
         * Theme Options > Header > Main Area
         * @since 1.0.0
         */
        $wp_customize->add_section( new Shopay_Customize_Section(
        	$wp_customize, 'shopay_section_header_main_area',
	            array(
	                'priority'  	=> 15,
	                'panel'     	=> 'shopay_theme_options_panel',
	                'section'		=> 'shopay_header_group',
	                'capability'    => 'edit_theme_options',
	                'theme_options' => '',
	                'title'     	=> __( 'Main Area', 'shopay' )
	            )
	        )
        );

        /**
         * Sticky Header Sidebar Section
         *
         * Theme Options > Header > Main Area
         * @since 1.0.0
         */
        $wp_customize->add_section( new Shopay_Customize_Section(
            $wp_customize, 'sidebar-widgets-sticky-header-sidebar',
                array(
                    'priority'          => 30,
                    'panel'             => 'shopay_theme_options_panel',
                    'section'           => 'shopay_section_header_main_area',
                    'capability'        => 'edit_theme_options',
                    'theme_options'     => '',
                    'title'             => __( 'Sticky Header Sidebar', 'shopay' ),
                    'description'       => __( 'Add widgets to show on header sticky sidebar', 'shopay' ),
                    'active_callback'   => 'shopay_header_sticky_sidebar_active_callback'
                )
            )
        );

        /**
         * Toggle option for sticky header sidebar.
         *
         * Theme Options > Header > Main Area
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'shopay_sticky_header_sidebar_option',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => true,
                'sanitize_callback' => 'shopay_sanitize_checkbox'
            )
        );

        $wp_customize->add_control( new Shopay_Control_Toggle(
            $wp_customize, 'shopay_sticky_header_sidebar_option',
                array(
                    'priority'      => 40,
                    'section'       => 'shopay_section_header_main_area',
                    'settings'      => 'shopay_sticky_header_sidebar_option',
                    'label'         => __( 'Show Sticky Header Sidebar', 'shopay' )
                )
            )
        );

        /**
         * Divider field
         *
         * Theme Options > Header > Main Area
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'shopay_header_main_divider_one',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'sanitize_callback' => 'sanitize_text_field'
            )
        );

        $wp_customize->add_control( new Shopay_Control_Divider(
            $wp_customize, 'shopay_header_main_divider_one',
                array(
                    'priority'      => 50,
                    'section'       => 'shopay_section_header_main_area',
                    'settings'      => 'shopay_header_main_divider_one',
                )
            )
        );

        /**
         * Toggle option for site information.
         *
         * Theme Options > Header > Main Area
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'shopay_site_info_option',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => true,
                'sanitize_callback' => 'shopay_sanitize_checkbox'
            )
        );

        $wp_customize->add_control( new Shopay_Control_Toggle(
            $wp_customize, 'shopay_site_info_option',
                array(
                    'priority'      => 60,
                    'section'       => 'shopay_section_header_main_area',
                    'settings'      => 'shopay_site_info_option',
                    'label'         => __( 'Show Site Information', 'shopay' )
                )
            )
        );

        /**
         * Text field for contact info.
         *
         * Theme Options > Header > Main Area
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'shopay_header_site_contact_info',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => __( 'Support ( +123-465465464)', 'shopay' ),
                'transport'         => 'postMessage',
                'sanitize_callback' => 'sanitize_text_field'
            )
        );
        
        $wp_customize->add_control( 'shopay_header_site_contact_info',
            array(
                'priority'  => 70,
                'section'   => 'shopay_section_header_main_area',
                'settings'  => 'shopay_header_site_contact_info',
                'label'     => __( 'Contact Info', 'shopay' ),
                'type'      => 'text'
            )
        );

        /**
         * Text field for Email info.
         *
         * Theme Options > Header > Main Area
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'shopay_header_site_email_info',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => __( 'Email: noreply@example.com', 'shopay' ),
                'transport'         => 'postMessage',
                'sanitize_callback' => 'sanitize_text_field'
            )
        );
        
        $wp_customize->add_control( 'shopay_header_site_email_info',
            array(
                'priority'  => 80,
                'section'   => 'shopay_section_header_main_area',
                'settings'  => 'shopay_header_site_email_info',
                'label'     => __( 'Email Info', 'shopay' ),
                'type'      => 'text'
            )
        );

        /**
         * Divider field
         *
         * Theme Options > Header > Main Area
         */
        $wp_customize->add_setting( 'shopay_header_main_divider_two',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'sanitize_callback' => 'sanitize_text_field'
            )
        );

        $wp_customize->add_control( new Shopay_Control_Divider(
            $wp_customize, 'shopay_header_main_divider_two',
                array(
                    'priority'      => 85,
                    'section'       => 'shopay_section_header_main_area',
                    'settings'      => 'shopay_header_main_divider_two',
                )
            )
        );

        /**
         * Toggle option for category menu
         *
         * Theme Options > Header > Main Area
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'shopay_cat_menu_option',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => true,
                'sanitize_callback' => 'shopay_sanitize_checkbox'
            )
        );

        $wp_customize->add_control( new Shopay_Control_Toggle(
            $wp_customize, 'shopay_cat_menu_option',
                array(
                    'priority'          => 90,
                    'section'           => 'shopay_section_header_main_area',
                    'settings'          => 'shopay_cat_menu_option',
                    'label'             => __( 'Enable Category Menu', 'shopay' ),
                    'active_callback'   => 'shopay_is_woo_acitvated_active_callback',
                )
            )
        );

        /**
         * Text field for category menu title
         *
         * Theme Options > Header > Main Area
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'shopay_cat_menu_title',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => __( 'Main Categories', 'shopay' ),
                'transport'         => 'postMessage',
                'sanitize_callback' => 'sanitize_text_field'
            )
        );
        
        $wp_customize->add_control( 'shopay_cat_menu_title',
            array(
                'priority'          => 100,
                'section'           => 'shopay_section_header_main_area',
                'settings'          => 'shopay_cat_menu_title',
                'label'             => __( 'Category Menu Title', 'shopay' ),
                'type'              => 'text',
                'active_callback'   => 'shopay_cat_menu_title_active_callback',
            )
        );

        /**
         * Divider field
         *
         * Theme Options > Header > Main Area
         */
        $wp_customize->add_setting( 'shopay_header_main_divider_three',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'sanitize_callback' => 'sanitize_text_field'
            )
        );

        $wp_customize->add_control( new Shopay_Control_Divider(
            $wp_customize, 'shopay_header_main_divider_three',
                array(
                    'priority'          => 110,
                    'section'           => 'shopay_section_header_main_area',
                    'settings'          => 'shopay_header_main_divider_three',
                    'active_callback'   => 'shopay_is_woo_acitvated_active_callback'
                )
            )
        );

        /**
         * Toggle option for search field
         *
         * Theme Options > Header > Main Area
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'shopay_search_bar_option',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => true,
                'sanitize_callback' => 'shopay_sanitize_checkbox'
            )
        );

        $wp_customize->add_control( new Shopay_Control_Toggle(
            $wp_customize, 'shopay_search_bar_option',
                array(
                    'priority'          => 120,
                    'section'           => 'shopay_section_header_main_area',
                    'settings'          => 'shopay_search_bar_option',
                    'label'             => __( 'Enable Search Field', 'shopay' )
                )
            )
        );

        /**
         * Select field for search field type
         *
         * Theme Options > Header > Main Area
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'shopay_search_bar_type',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => 'default-search',
                'sanitize_callback' => 'shopay_sanitize_select'
            )
        );
        
        $wp_customize->add_control( 'shopay_search_bar_type',
            array(
                'priority'          => 130,
                'section'           => 'shopay_section_header_main_area',
                'settings'          => 'shopay_search_bar_type',
                'label'             => __( 'Search Field Type', 'shopay' ),
                'type'              => 'select',
                'choices'           => shopay_search_bar_type_choices(),
                'active_callback'   => 'shopay_search_field_type_active_callback',
            )
        );

        /**
         * Divider field
         *
         * Theme Options > Header > Main Area
         */
        $wp_customize->add_setting( 'shopay_header_main_divider_four',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'sanitize_callback' => 'sanitize_text_field'
            )
        );

        $wp_customize->add_control( new Shopay_Control_Divider(
            $wp_customize, 'shopay_header_main_divider_four',
                array(
                    'priority'      => 140,
                    'section'       => 'shopay_section_header_main_area',
                    'settings'      => 'shopay_header_main_divider_four',
                )
            )
        );

        /**
         * Toggle option for woo cart link
         *
         * Theme Options > Header > Main Area
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'shopay_cart_link_option',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => false,
                'sanitize_callback' => 'shopay_sanitize_checkbox'
            )
        );

        $wp_customize->add_control( new Shopay_Control_Toggle(
            $wp_customize, 'shopay_cart_link_option',
                array(
                    'priority'          => 150,
                    'section'           => 'shopay_section_header_main_area',
                    'settings'          => 'shopay_cart_link_option',
                    'label'             => __( 'Enable Woo Cart', 'shopay' ),
                    'active_callback'   => 'shopay_is_woo_acitvated_active_callback',
                )
            )
        );

        /**
         * Toggle option for yith wishlist
         *
         * Theme Options > Header > Main Area
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'shopay_wishlist_link_option',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => false,
                'sanitize_callback' => 'shopay_sanitize_checkbox'
            )
        );

        $wp_customize->add_control( new Shopay_Control_Toggle(
            $wp_customize, 'shopay_wishlist_link_option',
                array(
                    'priority'          => 160,
                    'section'           => 'shopay_section_header_main_area',
                    'settings'          => 'shopay_wishlist_link_option',
                    'label'             => __( 'Enable Yith Wishlist', 'shopay' ),
                    'active_callback'   => 'shopay_yith_active_callback',
                )
            )
        );

        /**
         * Toggle option for sticky header.
         *
         * Theme Options > Header > Main Area
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'shopay_sticky_menu_option',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => true,
                'sanitize_callback' => 'shopay_sanitize_checkbox'
            )
        );

        $wp_customize->add_control( new Shopay_Control_Toggle(
            $wp_customize, 'shopay_sticky_menu_option',
                array(
                    'priority'      => 200,
                    'section'       => 'shopay_section_header_main_area',
                    'settings'      => 'shopay_sticky_menu_option',
                    'label'         => __( 'Enable Sticky Menu', 'shopay' )
                )
            )
        );
        
    }

endif;
