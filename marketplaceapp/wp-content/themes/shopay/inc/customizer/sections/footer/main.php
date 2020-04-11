<?php
/**
 * Add Main Area section and it's fields inside Footer section group.
 * 
 * @package Shopay
 */

add_action( 'customize_register', 'shopay_register_footer_main_fields' );

if ( ! function_exists( 'shopay_register_footer_main_fields' ) ) :

    /**
     * Register Main Area section's fields.
     */
    function shopay_register_footer_main_fields ( $wp_customize ) {

        /**
         * Main Area Section
         *
         * Theme Options > Footer > Main Area
         * @since 1.0.0
         */
        $wp_customize->add_section( new Shopay_Customize_Section(
            $wp_customize, 'shopay_section_footer_main',
                array(
                    'priority'      => 10,
                    'panel'         => 'shopay_theme_options_panel',
                    'section'       => 'shopay_footer_group',
                    'capability'    => 'edit_theme_options',
                    'theme_options' => '',
                    'title'         => __( 'Main Area', 'shopay' )
                )
            )
        );

        /**
         * Toggle option for widget area
         *
         * Theme Options > Footer > Main Area
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'shopay_footer_widget_area_option',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => true,
                'sanitize_callback' => 'shopay_sanitize_checkbox'
            )
        );

        $wp_customize->add_control( new Shopay_Control_Toggle(
            $wp_customize, 'shopay_footer_widget_area_option',
                array(
                    'priority'      => 10,
                    'section'       => 'shopay_section_footer_main',
                    'settings'      => 'shopay_footer_widget_area_option',
                    'label'         => __( 'Enable Widget Area', 'shopay' )
                )
            )
        );

        /**
         * Radio image field for footer widget area.
         *
         * Theme Options > Footer > Main Area
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'shopay_footer_widget_layout',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => 'five-column',
                'sanitize_callback' => 'sanitize_key',
            )
        );

        $wp_customize->add_control( new Shopay_Control_Radio_Image(
            $wp_customize, 'shopay_footer_widget_layout',
                array(
                    'priority'      => 20,
                    'section'       => 'shopay_section_footer_main',
                    'settings'      => 'shopay_footer_widget_layout',
                    'label'         => __( 'Widget Area Layout', 'shopay' ),
                    'description'   => __( 'Choose layout from available options.', 'shopay' ),
                    'choices'  => array(
                        'one-column'    => array(
                            'title'     => __( 'One Column', 'shopay' ),
                            'src'       => get_template_directory_uri() . '/assets/images/one-column.png'
                        ),
                        'two-columns'  => array(
                            'title'     => __( 'Two Columns', 'shopay' ),
                            'src'       => get_template_directory_uri() . '/assets/images/two-column.png'
                        ),
                        'three-columns'  => array(
                            'title'     => __( 'Three Columns', 'shopay' ),
                            'src'       => get_template_directory_uri() . '/assets/images/three-column.png'
                        ),
                        'four-columns'  => array(
                            'title'     => __( 'Four Columns', 'shopay' ),
                            'src'       => get_template_directory_uri() . '/assets/images/four-column.png'
                        ),
                        'five-columns'  => array(
                            'title'     => __( 'Five Columns', 'shopay' ),
                            'src'       => get_template_directory_uri() . '/assets/images/five-column.png'
                        )
                    )
                )
            )
        );

        /**
         * Divider field
         *
         * Theme Options > Footer > Main Area
         */
        $wp_customize->add_setting( 'shopay_footer_main_divider_one',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'sanitize_callback' => 'sanitize_text_field'
            )
        );

        $wp_customize->add_control( new Shopay_Control_Divider(
            $wp_customize, 'shopay_footer_main_divider_one',
                array(
                    'priority'      => 30,
                    'section'       => 'shopay_section_footer_main',
                    'settings'      => 'shopay_footer_main_divider_one',
                )
            )
        );

        /**
         * Textarea field for footer description
         *
         * Theme Options > Footer > Main Area
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'shopay_footer_description',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => __( 'Write a short description about your site here.', 'shopay' ),
                'transport'         => 'postMessage',
                'sanitize_callback' => 'wp_kses_post'
            )
        );
        
        $wp_customize->add_control( 'shopay_footer_description',
            array(
                'priority'  => 40,
                'section'   => 'shopay_section_footer_main',
                'settings'  => 'shopay_footer_description',
                'label'     => __( 'Short Description', 'shopay' ),
                'type'      => 'textarea'
            )
        );

        /**
         * Toggle option for Social Icons
         *
         * Theme Options > Footer > Main Area
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'shopay_footer_social_media_option',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => true,
                'sanitize_callback' => 'shopay_sanitize_checkbox'
            )
        );

        $wp_customize->add_control( new Shopay_Control_Toggle(
            $wp_customize, 'shopay_footer_social_media_option',
                array(
                    'priority'      => 50,
                    'section'       => 'shopay_section_footer_main',
                    'settings'      => 'shopay_footer_social_media_option',
                    'label'         => __( 'Enable Social Icons', 'shopay' )
                )
            )
        );

    }

endif;