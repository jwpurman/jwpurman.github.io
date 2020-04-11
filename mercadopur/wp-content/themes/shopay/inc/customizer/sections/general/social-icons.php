<?php
/**
 * Add Social Icons section and it's fields inside General section group.
 * 
 * @package Shopay
 */

add_action( 'customize_register', 'shopay_register_social_icons_fields' );

if ( ! function_exists( 'shopay_register_social_icons_fields' ) ) :

    /**
     * Register social icons section's fields.
     */
    function shopay_register_social_icons_fields ( $wp_customize ) {

        /**
         * Social Icons Section
         *
         * Theme Options > General > Social Icons
         * @since 1.0.0
         */
        $wp_customize->add_section( new Shopay_Customize_Section(
            $wp_customize, 'shopay_section_social_icons',
                array(
                    'priority'      => 30,
                    'panel'         => 'shopay_theme_options_panel',
                    'section'       => 'shopay_general_group',
                    'capability'    => 'edit_theme_options',
                    'theme_options' => '',
                    'title'         => __( 'Social Icons', 'shopay' )
                )
            )
        );

        /**
         * Repeater field for Social Icons
         *
         * Theme Options > General > Social Icons
         */
        $wp_customize->add_setting( 'shopay_social_media',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => json_encode(
                    array(
                        array(
                            'mt_item_icon' => 'fab fa-twitter',
                            'mt_item_link' => '',
                        )
                    )
                ),
                'sanitize_callback' => 'shopay_sanitize_repeater'
            )
        );

        /**
         * Toggle option for social icons open on new tab.
         *
         * Theme Options > General > Social Icons
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'shopay_social_link_target',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => 'new',
                'sanitize_callback' => 'shopay_sanitize_select'
            )
        );

        $wp_customize->add_control( new Shopay_Control_Toggle(
            $wp_customize, 'shopay_social_link_target',
                array(
                    'priority'      => 10,
                    'section'       => 'shopay_section_social_icons',
                    'settings'      => 'shopay_social_link_target',
                    'label'         => __( 'Social Link Target', 'shopay' ),
                    'type'              => 'select',
                    'choices'       => array(
                        'new'       => __( 'New Window', 'shopay' ),
                        'same'      => __( 'Same Window', 'shopay' )
                    )
                )
            )
        );

        $wp_customize->add_control( new Shopay_Control_Repeater(
            $wp_customize, 
            'shopay_social_media',
                array(
                    'priority'                      => 20,
                    'section'                       => 'shopay_section_social_icons',
                    'settings'                      => 'shopay_social_media',
                    'label'                         => __( 'Social Icons', 'shopay' ),
                    'shopay_box_label_text'         => __( 'Social Icon','shopay' ),
                    'shopay_box_add_control_text'   => __( 'Add New Icon','shopay' )
                ),
                array(
                    'mt_item_icon' => array(
                        'type'        => 'social_icon',
                        'label'       => __( 'Icon', 'shopay' ),
                        'description' => __( 'Choose required icon from available list.', 'shopay' )
                    ),
                    'mt_item_link' => array(
                        'type'        => 'url',
                        'label'       => __( 'Icon Link', 'shopay' ),
                        'description' => __( 'Add social icon link.', 'shopay' )
                    )
                )
            )
        );

    }

endif;