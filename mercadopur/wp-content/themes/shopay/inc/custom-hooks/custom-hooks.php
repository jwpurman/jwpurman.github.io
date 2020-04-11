<?php
/**
 * Hooks for handling homepage section.
 * 
 * @package Shopay
 */

if ( ! function_exists( 'shopay_homepage_section' ) ) :
    /**
     * Function for loading homepage sections.
     *  
     */
    function shopay_homepage_section() {
        if ( !is_active_sidebar( 'homepage-section' )  || !is_front_page() ) {
            return;
        }

        dynamic_sidebar( 'homepage-section' );
    }
endif;

add_action( 'shopay_before_content', 'shopay_homepage_section', 10 );