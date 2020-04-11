<?php
/**
 * Define path for required files for Custom Control
 * 
 * @package Shopay
*/

if ( ! function_exists( 'shopay_register_custom_controls' ) ) :
    
    /**
     * Register Custom Controls
     * 
     * @since 1.0.0
    */
    function shopay_register_custom_controls( $wp_customize ) {
        
        // Load toggle control
        require_once get_template_directory() . '/inc/customizer/custom-controls/toggle/class-toggle-control.php';
        
        // Load repeater control
        require_once get_template_directory() . '/inc/customizer/custom-controls/repeater/class-repeater-control.php';
        
        // Load radio image control
        require_once get_template_directory() . '/inc/customizer/custom-controls/radio-image/class-radio-image-control.php';
        
        // Load divider control
        require_once get_template_directory() . '/inc/customizer/custom-controls/divider/class-divider-control.php';
        
        // Register toggle control
        $wp_customize->register_control_type( 'Shopay_Control_Toggle' );
        
        // Register radio image control
        $wp_customize->register_control_type( 'Shopay_Control_Radio_Image' );
        
        // Register divider control
        $wp_customize->register_control_type( 'Shopay_Control_Divider' );
    }

endif;

add_action( 'customize_register', 'shopay_register_custom_controls' );

// Load theme upsell section
require_once get_template_directory() . '/inc/customizer/custom-controls/theme-upsell/class-theme-upsell-section.php';