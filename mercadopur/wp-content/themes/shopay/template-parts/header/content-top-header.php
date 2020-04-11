<?php
/**
 * Template for top header part of theme
 * 
 * @package Shopay
 *
 */
$shopay_top_header_option = get_theme_mod( 'shopay_top_header_option', true );
if ( false == $shopay_top_header_option ) {
    return;
}
?>

<div id="site-top-header">
    <div class="mt-container">
        <div class="site-top header-wrap clearfix">
            <?php
                $shopay_top_header_description = get_theme_mod( 'shopay_top_header_description', __( 'Welcome To Worldwide Store', 'shopay' ) );
                if ( ! empty( $shopay_top_header_description ) ) {
            ?>
                    <div class="top-header-description small-font">
                        <?php echo wp_kses_post( $shopay_top_header_description ); ?>
                    </div><!-- .short-description -->
            <?php  } ?>

            <div class="top-header-elements-wrap clearfix">
                <?php
                    $shopay_top_header_location = get_theme_mod( 'shopay_top_header_location', __( 'Store Locator', 'shopay' ) );
                    if ( ! empty( $shopay_top_header_location ) ) {
                        echo '<div class="top-header-elements site-location small-font"><a><i class="fas fa-map-marked-alt"></i>'.esc_html( $shopay_top_header_location ).'</a></div>';
                    }

                    $shopay_top_header_service = get_theme_mod( 'shopay_top_header_service', __( 'Free Delivery', 'shopay' ) );
                    if ( ! empty( $shopay_top_header_service ) ) {
                        echo '<div class="top-header-elements site-service small-font"><a><i class="fas fa-truck"></i>'.esc_html( $shopay_top_header_service ).'</a></div>';
                    }
                ?>

                <nav class="top-header-elements site-top-navigation">
                    <?php
                        wp_nav_menu( array(
                            'theme_location' => 'menu-top',
                            'menu_id'        => 'top-menu',
                            'fallback_cb'    => false,
                            'depth'          => 1
                        ) );
                    ?>
                </nav><!-- .site-top-navigation -->
            </div><!-- .top-header-elements-wrap -->
        </div><!-- site-top header-wrap -->
    </div><!-- mt-container -->
</div><!-- .site-top-header -->