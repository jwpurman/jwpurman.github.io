<?php
/**
 * The sidebar containing the widget areas
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Shopay
 */
$shopay_footer_widget_area_option = get_theme_mod( 'shopay_footer_widget_area_option', true );
if ( false == $shopay_footer_widget_area_option ) {
    return;
}
if ( ! is_active_sidebar( 'footer-sidebar' ) && ! is_active_sidebar( 'footer-sidebar-2' ) && ! is_active_sidebar( 'footer-sidebar-3' ) && ! is_active_sidebar( 'footer-sidebar-4' ) && ! is_active_sidebar( 'footer-sidebar-5' ) ) {
	return;
}

$shopay_footer_widget_layout = get_theme_mod( 'shopay_footer_widget_layout', 'five-column' );
?>

<div id="footer-widget-area" class="widget-area <?php echo 'footer-widget--'.esc_attr( $shopay_footer_widget_layout ); ?>">
    <div class="mt-container">
        <div class="footer-widget-wrapper clearfix">
            <?php
                if ( is_active_sidebar( 'footer-sidebar' ) ) {
                    echo '<div class="footer-widget">';
                        dynamic_sidebar( 'footer-sidebar' );
                    echo '</div><!-- .footer-widget -->';
                }

                
                if ( is_active_sidebar( 'footer-sidebar-2' ) ) {
                    if ( 'one-column' != $shopay_footer_widget_layout ) {
                        echo '<div class="footer-widget">';
                            dynamic_sidebar( 'footer-sidebar-2' );
                        echo '</div><!-- .footer-widget -->';
                    }
                }

                if ( is_active_sidebar( 'footer-sidebar-3' ) ) {
                    if ( 'one-column' != $shopay_footer_widget_layout && 'two-column' != $shopay_footer_widget_layout ) {
                        echo '<div class="footer-widget">';
                            dynamic_sidebar( 'footer-sidebar-3' );
                        echo '</div><!-- .footer-widget -->';
                    }
                }

                if ( is_active_sidebar( 'footer-sidebar-4' ) ) {
                    if ( 'four-column' == $shopay_footer_widget_layout || 'five-column' == $shopay_footer_widget_layout ) {
                        echo '<div class="footer-widget">';
                            dynamic_sidebar( 'footer-sidebar-4' );
                        echo '</div><!-- .footer-widget -->';
                    }
                }

                if ( is_active_sidebar( 'footer-sidebar-5' ) ) {
                    if ( 'five-column' == $shopay_footer_widget_layout ) {
                        echo '<div class="footer-widget">';
                            dynamic_sidebar( 'footer-sidebar-5' );
                        echo '</div><!-- .footer-widget -->';
                    }
                }
            ?>
        </div><!-- footer-widget-wrapper -->
    </div><!-- mt-container -->
</div><!-- #footer-widget-area -->