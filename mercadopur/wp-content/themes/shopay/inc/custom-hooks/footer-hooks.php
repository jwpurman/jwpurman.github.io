<?php
/**
 * Hooks for handling footer.
 * 
 * @package Shopay
 */

if ( ! function_exists( 'shopay_footer_widget_area' ) ) :
    /**
     * Function for loading footer widget area.
     *  
     */
    function shopay_footer_widget_area() {
        get_sidebar('footer');
    }
endif;

if ( ! function_exists( 'shopay_footer' ) ) :
    /**
     * Function for loading footer.
     *  
     */
    function shopay_footer() {
        get_template_part( '/template-parts/footer/content', 'footer' );
    }
endif;
/*-----------------------------------------------------------------------------------------------------------------------------------------------------------------*/
add_action( 'shopay_footer_section', 'shopay_footer_widget_area', 5 );
add_action( 'shopay_footer_section', 'shopay_footer', 10 );

/*-----------------------------------------------------------------------------------------------------------------------------------------------------------------*/
if ( ! function_exists( 'shopay_scroll_to_top' ) ) :
    /**
     * Function for scroll to top.
     */
    function shopay_scroll_to_top() {
        $shopay_scroll_to_top = get_theme_mod( 'shopay_scroll_to_top', true );
        if ( true != $shopay_scroll_to_top ) {
            return;
        }
    ?>
        <div id="shopay-scroll-to-top">
            <i class="fas fa-arrow-up"></i>
        </div><!-- #shopay-scroll-to-top -->
<?php
    }
endif;
add_action( 'shopay_after_footer', 'shopay_scroll_to_top', 5 );