<?php
/**
 * Add Services Section and it's fields inside Homepage section group.
 * 
 * @package Shopay
 */

add_action( 'customize_register', 'shopay_register_home_services_fields' );

if ( ! function_exists( 'shopay_register_home_services_fields' ) ) :

    /**
     * Register Services Section's fields.
     */
    function shopay_register_home_services_fields ( $wp_customize ) {

    	/**
         * Services Section
         *
         * Theme Options > Homepage > Services Section
         * @since 1.0.0
         */
        $wp_customize->add_section( new Shopay_Customize_Section(
        	$wp_customize, 'shopay_section_home_services',
	            array(
	                'priority'  	=> 15,
	                'panel'     	=> 'shopay_theme_options_panel',
	                'section'		=> 'shopay_homepage_group',
	                'capability'    => 'edit_theme_options',
	                'theme_options' => '',
	                'title'     	=> __( 'Services Section', 'shopay' )
	            )
	        )
        );

        /**
         * Repeater field for Homepage services items
         *
         * Theme Options > Homepage > Services Section
         */
        $wp_customize->add_setting( 'shopay_services_items',
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
            'shopay_services_items',
                array(
                    'priority'                      => 5,
                    'section'                       => 'shopay_section_home_services',
                    'settings'                      => 'shopay_services_items',
                    'label'                         => __( 'Services Items', 'shopay' ),
                    'shopay_box_label_text'         => __( 'Service Item','shopay' ),
                    'shopay_box_add_control_text'   => __( 'Add New Service','shopay' )
                ),
                array(
                    'mt_item_icon' => array(
                        'type'        => 'icon',
                        'label'       => __( 'Service Icon', 'shopay' ),
                        'description' => __( 'Choose required icon from available list.', 'shopay' )
                    ),
                    'mt_item_desc' => array(
                        'type'        => 'text',
                        'label'       => __( 'Description', 'shopay' ),
                        'description' => __( 'Add description for service item.', 'shopay' )
                    )
                )
            )
        );

    }

endif;