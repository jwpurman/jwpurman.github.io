<?php
/**
 * Add Sponsors section and it's fields inside Homepage section group.
 * 
 * @package Shopay
 */

add_action( 'customize_register', 'shopay_register_home_sponsors_fields' );

if ( ! function_exists( 'shopay_register_home_sponsors_fields' ) ) :

    /**
     * Register Sponsors section's fields.
     */
    function shopay_register_home_sponsors_fields ( $wp_customize ) {

    	/**
         * Sponsors Section
         *
         * Theme Options > Homepage > Sponsors Section
         * @since 1.0.0
         */
        $wp_customize->add_section( new Shopay_Customize_Section(
        	$wp_customize, 'shopay_section_home_sponsors',
	            array(
	                'priority'  	=> 10,
	                'panel'     	=> 'shopay_theme_options_panel',
	                'section'		=> 'shopay_homepage_group',
	                'capability'    => 'edit_theme_options',
	                'theme_options' => '',
	                'title'     	=> __( 'Sponsors Section', 'shopay' )
	            )
	        )
        );

        /**
         * Repeater field for Homepage sponsors image
         *
         * Theme Options > Homepage > Sponsors Section
         */
        $wp_customize->add_setting( 'shopay_sponsors_items',
            array(
                'capability'        => 'edit_theme_options',
                'theme_options'     => '',
                'default'           => json_encode(
                    array(
                        array(
                            'mt_item_upload' => ''
                        )
                    )
                ),
                'sanitize_callback' => 'shopay_sanitize_repeater'
            )
        );

        $wp_customize->add_control( new Shopay_Control_Repeater(
            $wp_customize, 
            'shopay_sponsors_items',
                array(
                    'priority'                      => 5,
                    'section'                       => 'shopay_section_home_sponsors',
                    'settings'                      => 'shopay_sponsors_items',
                    'label'                         => __( 'Sponsors Images', 'shopay' ),
                    'shopay_box_label_text'         => __( 'Sponsor Image','shopay' ),
                    'shopay_box_add_control_text'   => __( 'Add New Sponsor','shopay' )
                ),
                array(
                    'mt_item_upload' => array(
                        'type'        => 'upload',
                        'label'       => __( 'Logo/Image', 'shopay' ),
                        'description' => __( 'Choose sponsor logo or related image.', 'shopay' )
                    ),
                )
            )
        );

    }

endif;