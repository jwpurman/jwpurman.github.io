<?php
/**
 * Template for header part of theme
 * 
 * @package Shopay
 *
 */
?>

<header id="masthead" class="site-header">
    <div class="mt-container">
        <div class="main-header-wrapper clearfix">
            <div class="site-branding-toggle-wrapper">
                <div class="site-branding">
                    <?php
                    the_custom_logo();
                    if ( is_front_page() && is_home() ) :
                    ?>
                        <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
                    <?php
                    else :
                    ?>
                        <p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
                    <?php
                    endif;
                    $shopay_description = get_bloginfo( 'description', 'display' );
                    if ( $shopay_description || is_customize_preview() ) :
                    ?>
                        <p class="site-description small-font"><a><?php echo $shopay_description; /* WPCS: xss ok. */ ?></a></p>
                    <?php endif; ?>
                </div><!-- .site-branding -->

                <?php 
                    $shopay_sticky_header_sidebar_option = get_theme_mod( 'shopay_sticky_header_sidebar_option', false );
                    if ( true == $shopay_sticky_header_sidebar_option && is_active_sidebar( 'sticky-header-sidebar' ) ) {
                ?>
                        <div class="sticky-header-sidebar-section">
                            <div class="sticky-sidebar-content-wrapper">
                                <div class="sticky-sidebar-close"><a href="javascript:void(0);"><i class="far fa-window-close"></i></a></div>
                                <div class="sticky-sidebar-content">
                                    <?php dynamic_sidebar( 'sticky-header-sidebar' ); ?>
                                </div>
                            </div><!-- sticky-sidebar-content-wrapper -->
                            <div class="sticky-sidebar-icon"><a href="javascript:void(0);"><i class="fas fa-minus"></i></a></div>
                        </div>
                <?php
                    }
                ?>
            </div><!-- site-branding-toggle-wrapper -->
            <nav id="site-navigation" class="main-navigation clearfix">
                <div class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><a href="javascript:void(0);"><i class="fas fa-ellipsis-v"></i><?php esc_html_e( 'MENU', 'shopay' ); ?></a></div>
                <div class="primary-menu-wrap">
                    <?php
                        wp_nav_menu( array(
                            'theme_location' => 'menu-primary',
                            'menu_id'        => 'primary-menu'
                        ) );
                    ?>
                    <div class="main-menu-close hide"><a href="javascript:void(0);"><i class="far fa-window-close"></i></a></div>
                </div><!-- .primary-menu-wrap -->
            </nav><!-- #site-navigation -->

            <?php
                $shopay_site_info_option = get_theme_mod( 'shopay_site_info_option', true );
                if ( true == $shopay_site_info_option ) {
                    $shopay_header_site_contact_info = get_theme_mod( 'shopay_header_site_contact_info', __( 'Support ( +123-465465464)', 'shopay' ) );
                    $shopay_header_site_email_info = get_theme_mod( 'shopay_header_site_email_info', __( 'Email: noreply@example.com', 'shopay' ) );
            ?>
                    <div class="header-site-info-wrap clearfix">
                        <i class="fas fa-headset"></i>
                        <div class="site-info-content-wrap">
                            <?php
                                if ( !empty( $shopay_header_site_contact_info ) ) {
                            ?>
                                <div class="site-info-contact">
                                    <?php echo wp_kses_post( $shopay_header_site_contact_info ); ?>
                                </div>
                            <?php
                                }
                                if ( !empty( $shopay_header_site_email_info ) ) {
                            ?>
                                <div class="site-info-email">
                                    <?php echo wp_kses_post( $shopay_header_site_email_info ); ?>
                                </div>
                            <?php
                                }
                            ?>
                        </div><!-- .site-info-content -->
                    </div><!-- .header-site-info-wrap -->
            <?php
                }
            ?>
        </div><!-- .main-header-wrapper -->
    </div><!-- mt-container -->
</header><!-- #masthead -->

<div id="search-bar-section">
    <div class="mt-container">
        <div class="search-bar-section-wrapper clearfix">
            <?php 
                $shopay_cat_menu_option = get_theme_mod( 'shopay_cat_menu_option', true );
                if ( true == $shopay_cat_menu_option && shopay_is_active_woocommerce() ) {
                    echo '<div class="shopay-cat-menu-wrapper">';
                        $shopay_cat_menu_title = get_theme_mod( 'shopay_cat_menu_title', __( 'Main Categories', 'shopay' ) );
                        if ( ! empty( $shopay_cat_menu_title ) ) {
            ?>
                            <h2 class="main-category-list-title cover-font">
                                <?php echo esc_html( $shopay_cat_menu_title ); ?>
                                <a href="javascript:void(0);"><i class="fas fa-bars"></i></a>
                            </h2>
            <?php
                        }
                        echo '<div class="shopay-cat-menu deactivate-menu">';
                            shopay_category_menu_sec();
                        echo '</div><!-- .shopay-cat-menu -->';
                    echo '</div><!-- .shopay-cat-menu-wrapper -->';
                }
                $shopay_search_bar_option = get_theme_mod( 'shopay_search_bar_option', true );
                if ( true == $shopay_search_bar_option ) {
                    if ( shopay_is_active_woocommerce() ) {
                        $shopay_search_bar_type = get_theme_mod( 'shopay_search_bar_type', 'default-search' );
                    } else {
                        $shopay_search_bar_type = apply_filters( 'shopay_search_bar_type', 'default-search' );
                    }
            ?>
                    <div class="header-search-form-wrap">
                        <?php 
                            if ( 'default-search' === $shopay_search_bar_type ) {
                                get_search_form();
                            } elseif ( 'product-search' == $shopay_search_bar_type ) { 
                                shopay_get_advanced_product_search();
                            }
                        ?>
                    </div>
            <?php
                }
            ?>

            <div class="header-woo-links-wrap">
                <?php
                    if ( shopay_is_active_woocommerce() ) {
                        shopay_header_wishlist_link();
                        shopay_woocommerce_header_cart();
                    }
                ?>
            </div><!-- .header-woo-links-wrap -->
        </div><!-- .search-bar-section-wrapper -->
    </div><!-- mt-container -->
</div><!-- #search-bar-section --> 
<?php
$shopay_highlight_products_option = get_theme_mod( 'shopay_highlight_products_option', false );
if ( true == $shopay_highlight_products_option && shopay_is_active_woocommerce() ) {
    $shopay_highlight_products_title = get_theme_mod( 'shopay_highlight_products_title', __( 'Trending Products', 'shopay' ) )
?>
    <div id="highlight-products-section">
        <div class="highlight-products-section">
            <?php if ( ! empty( $shopay_highlight_products_title ) ) { ?>
                    <h2 class="highlight-title">
                        <div class="mt-container">
                            <?php echo esc_html( $shopay_highlight_products_title ); ?>
                        </div><!-- mt-container -->
                    </h2><!-- .section-title -->
            <?php } ?>
            <div class="highlight-products-section-content">
                <?php
                    $highlight_products_args = array(
                        'post_type'  =>  'product',
                    );
                    $highlight_products_query = new WP_Query( $highlight_products_args );
                    if ( $highlight_products_query -> have_posts() ) :
                        while ( $highlight_products_query -> have_posts() ) : $highlight_products_query -> the_post();
                ?>
                            <div class="highlight-product-wrap">
                                <div class="highlight-image">
                                    <?php
                                        woocommerce_template_loop_product_link_open(); 
                                        echo woocommerce_get_product_thumbnail( 'thumbnail' );
                                        woocommerce_template_loop_product_link_close();
                                    ?>
                                </div>
                                <div class="highlight-content-wrap">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    <?php woocommerce_template_loop_price(); ?>
                                </div>
                            </div><!-- highlight-product-wrap -->
                <?php
                        endwhile;
                    endif;
                    wp_reset_postdata();
                ?>
            </div><!-- .highlight-products-section-content -->
        </div><!-- highlight-products-section -->
    </div><!-- #highlight-products-section  -->
<?php
}
