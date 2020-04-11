<?php
/**
 * Add Bottom Area section and it's fields inside Footer section group.
 * 
 * @package Shopay
 */

add_action( 'customize_register', 'shopay_register_footer_bottom_fields' );

if ( ! function_exists( 'shopay_register_footer_bottom_fields' ) ) :

    /**
     * Register Bottom Area section's fields.
     */
    function shopay_register_footer_bottom_fields ( $wp_customize ) {

        /**
         * Bottom Area Section
         *
         * Theme Options > Footer > Bottom Area
         * @since 1.0.0
         */
        $wp_customize->add_section( new Shopay_Customize_Section(
            $wp_customize, 'shopay_section_footer_bottom',
                array(
                    'priority'      => 20,
                    'panel'         => 'shopay_theme_options_panel',
                    'section'       => 'shopay_footer_group',
                    'capability'    => 'edit_theme_options',
                    'theme_options' => '',
                    'title'         => __( 'Bottom Area', 'shopay' )
                )
            )
        );

        /**
         * Text field for copyright
         *
         * Theme Options > Footer > Bottom Area
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'shopay_copyright_text',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => __( 'Shopay Store', 'shopay' ),
                'transport'         => 'postMessage',
                'sanitize_callback' => 'sanitize_text_field'
            )
        );
        
        $wp_customize->add_control( 'shopay_copyright_text',
            array(
                'priority'  => 10,
                'section'   => 'shopay_section_footer_bottom',
                'settings'  => 'shopay_copyright_text',
                'label'     => __( 'Copyright Text', 'shopay' ),
                'type'      => 'text'
            )
        );

        /**
         * Image field for payment method image
         *
         * Theme Options > Footer > Bottom Area
         * @since 1.0.0
         */
        $wp_customize->add_setting( 'shopay_payment_image',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'sanitize_callback' => 'esc_url_raw'
            )
        );

        $wp_customize->add_control( new WP_Customize_Image_Control(
            $wp_customize, 'shopay_payment_image',
                array(
                    'priority'      => 20,
                    'section'       => 'shopay_section_footer_bottom',
                    'settings'      => 'shopay_payment_image',
                    'label'         => __( 'Payment Method', 'shopay' ),
                    'description'   => __( 'Upload image where include all payment method which do you have used.', 'shopay' )
                )
            )
        );

    }

endif;