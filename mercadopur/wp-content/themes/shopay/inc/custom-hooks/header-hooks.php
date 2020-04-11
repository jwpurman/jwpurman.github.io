<?php
/**
 * Hooks for handling header.
 * 
 * @package Shopay
 */

if ( ! function_exists( 'shopay_preloader' ) ) :
    /**
     * Function for preloader.
     */
    function shopay_preloader() {
        $shopay_preloader_option = get_theme_mod( 'shopay_preloader_option', true );
        if ( true != $shopay_preloader_option ) {
            return;
        }
?>  
        <div id="preloader-background">
            <div class="preloader-wrapper">
                <div class="custom-preloader"><i class="fab fa-opencart"></i></div>
            </div><!-- .preloader-wrapper -->
        </div><!-- #preloader-background -->
<?php
    }
endif;
add_action( 'shopay_before_page', 'shopay_preloader' );

if ( ! function_exists( 'shopay_top_header' ) ) :
    /**
     * Function for loading top header part.
     *  
     */
    function shopay_top_header() {
        get_template_part( '/template-parts/header/content', 'top-header' );
    }
endif;

/*-------------------------------------------------------------------------------------------------- ---------------------------------------------------------------*/
if ( ! function_exists( 'shopay_main_header' ) ) :
    /**
     * Function for loading header part.
     * 
     */
    function shopay_main_header() {
        get_template_part( '/template-parts/header/content', 'header' );
    }
endif;

/*-----------------------------------------------------------------------------------------------------------------------------------------------------------------*/
add_action( 'shopay_header_section', 'shopay_top_header', 10 );
add_action( 'shopay_header_section', 'shopay_main_header', 20 );