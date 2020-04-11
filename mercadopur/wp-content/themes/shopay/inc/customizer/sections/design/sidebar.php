<?php
/**
 * Add Sidebar Layout section and it's fields inside Design section group.
 * 
 * @package Shopay
 */

add_action( 'customize_register', 'shopay_register_sidebar_layout_fields' );

if ( ! function_exists( 'shopay_register_sidebar_layout_fields' ) ) :

    /**
     * Register Sidebar layout section's fields.
     */
    function shopay_register_sidebar_layout_fields ( $wp_customize ) {

    	/**
         * Sidebar Layout Section
         *
         * Theme Options > Design > Sidebar Layout
         * @since 1.0.0
         */
        $wp_customize->add_section( new Shopay_Customize_Section(
        	$wp_customize, 'shopay_section_sidebar_layout',
	            array(
	                'priority'  	=> 10,
	                'panel'     	=> 'shopay_theme_options_panel',
	                'section'		=> 'shopay_design_group',
	                'capability'    => 'edit_theme_options',
	                'theme_options' => '',
	                'title'     	=> __( 'Sidebar Layout', 'shopay' )
	            )
	        )
        );

        /**
         * Radio image field for archive sidebar layout
         *
         * Theme Options > Design > Sidebar Layout
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'shopay_archive_sidebar_layout',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => 'right-sidebar',
                'sanitize_callback' => 'sanitize_key',
            )
        );

        $wp_customize->add_control( new Shopay_Control_Radio_Image(
            $wp_customize, 'shopay_archive_sidebar_layout',
                array(
                    'priority'      => 10,
                    'section'       => 'shopay_section_sidebar_layout',
                    'settings'      => 'shopay_archive_sidebar_layout',
                    'label'         => __( 'Archive Sidebar Layout', 'shopay' ),
                    'description'   => __( 'Choose from available layouts', 'shopay' ),
                    'choices'  => array(
                        'right-sidebar'    => array(
                            'title'     => __( 'Right Sidebar', 'shopay' ),
                            'src'       => get_template_directory_uri() . '/assets/images/right-sidebar.png'
                        ),
                        'left-sidebar'  => array(
                            'title'     => __( 'Left Sidebar', 'shopay' ),
                            'src'       => get_template_directory_uri() . '/assets/images/left-sidebar.png'
                        ),
                        'no-sidebar'  => array(
                            'title'     => __( 'No Sidebar', 'shopay' ),
                            'src'       => get_template_directory_uri() . '/assets/images/no-sidebar.png'
                        ),
                        'no-sidebar-center'  => array(
                            'title'     => __( 'No Sidebar Center', 'shopay' ),
                            'src'       => get_template_directory_uri() . '/assets/images/no-sidebar-center.png'
                        )
                    )
                )
            )
        );

        /**
         * Divider field
         *
         * Theme Options > Design > Sidebar Layout
         */
        $wp_customize->add_setting( 'shopay_design_sidebar_divider_one',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'sanitize_callback' => 'sanitize_text_field'
            )
        );

        $wp_customize->add_control( new Shopay_Control_Divider(
            $wp_customize, 'shopay_design_sidebar_divider_one',
                array(
                    'priority'      => 20,
                    'section'       => 'shopay_section_sidebar_layout',
                    'settings'      => 'shopay_design_sidebar_divider_one',
                )
            )
        );

        /**
         * Radio image field for page sidebar layout
         *
         * Theme Options > Design > Sidebar Layout
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'shopay_page_sidebar_layout',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => 'right-sidebar',
                'sanitize_callback' => 'sanitize_key',
            )
        );

        $wp_customize->add_control( new Shopay_Control_Radio_Image(
            $wp_customize, 'shopay_page_sidebar_layout',
                array(
                    'priority'      => 30,
                    'section'       => 'shopay_section_sidebar_layout',
                    'settings'      => 'shopay_page_sidebar_layout',
                    'label'         => __( 'Page Sidebar Layout', 'shopay' ),
                    'description'   => __( 'Choose from available layouts', 'shopay' ),
                    'choices'  => array(
                        'right-sidebar'    => array(
                            'title'     => __( 'Right Sidebar', 'shopay' ),
                            'src'       => get_template_directory_uri() . '/assets/images/right-sidebar.png'
                        ),
                        'left-sidebar'  => array(
                            'title'     => __( 'Left Sidebar', 'shopay' ),
                            'src'       => get_template_directory_uri() . '/assets/images/left-sidebar.png'
                        ),
                        'no-sidebar'  => array(
                            'title'     => __( 'No Sidebar', 'shopay' ),
                            'src'       => get_template_directory_uri() . '/assets/images/no-sidebar.png'
                        ),
                        'no-sidebar-center'  => array(
                            'title'     => __( 'No Sidebar Center', 'shopay' ),
                            'src'       => get_template_directory_uri() . '/assets/images/no-sidebar-center.png'
                        )
                    )
                )
            )
        );

        /**
         * Divider field
         *
         * Theme Options > Design > Sidebar Layout
         */
        $wp_customize->add_setting( 'shopay_design_sidebar_divider_two',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'sanitize_callback' => 'sanitize_text_field'
            )
        );

        $wp_customize->add_control( new Shopay_Control_Divider(
            $wp_customize, 'shopay_design_sidebar_divider_two',
                array(
                    'priority'      => 40,
                    'section'       => 'shopay_section_sidebar_layout',
                    'settings'      => 'shopay_design_sidebar_divider_two',
                )
            )
        );

        /**
         * Radio image field for post sidebar layout
         *
         * Theme Options > Design > Sidebar Layout
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'shopay_post_sidebar_layout',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => 'right-sidebar',
                'sanitize_callback' => 'sanitize_key',
            )
        );

        $wp_customize->add_control( new Shopay_Control_Radio_Image(
            $wp_customize, 'shopay_post_sidebar_layout',
                array(
                    'priority'      => 50,
                    'section'       => 'shopay_section_sidebar_layout',
                    'settings'      => 'shopay_post_sidebar_layout',
                    'label'         => __( 'Post Sidebar Layout', 'shopay' ),
                    'description'   => __( 'Choose from available layouts', 'shopay' ),
                    'choices'  => array(
                        'right-sidebar'    => array(
                            'title'     => __( 'Right Sidebar', 'shopay' ),
                            'src'       => get_template_directory_uri() . '/assets/images/right-sidebar.png'
                        ),
                        'left-sidebar'  => array(
                            'title'     => __( 'Left Sidebar', 'shopay' ),
                            'src'       => get_template_directory_uri() . '/assets/images/left-sidebar.png'
                        ),
                        'no-sidebar'  => array(
                            'title'     => __( 'No Sidebar', 'shopay' ),
                            'src'       => get_template_directory_uri() . '/assets/images/no-sidebar.png'
                        ),
                        'no-sidebar-center'  => array(
                            'title'     => __( 'No Sidebar Center', 'shopay' ),
                            'src'       => get_template_directory_uri() . '/assets/images/no-sidebar-center.png'
                        )
                    )
                )
            )
        );

    }

endif;