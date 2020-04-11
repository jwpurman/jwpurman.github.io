<?php
/**
 * Dynamic style for site theme color.
 *
 * @package Shopay
 *
 */

add_action( 'wp_enqueue_scripts', 'shopay_dynamic_styles' );

if ( ! function_exists( 'shopay_dynamic_styles' ) ) :
    function shopay_dynamic_styles() { 

        $shopay_primary_color = get_theme_mod( 'shopay_primary_color', '#ed6d23' );
        $shopay_primary_hover_color = shopay_hover_color( $shopay_primary_color, '+20' );

        $output_css = '';

        $output_css .= ".sticky-sidebar-icon:hover::after, .sticky-sidebar-icon:hover::before{ background: ". esc_attr( $shopay_primary_hover_color ) ."}\n";

        $output_css .= ".woocommerce .woocommerce-notices-wrapper a.button:hover, .woocommerce .yith-wcwl-add-button a.add_to_wishlist:after, .product-btns-wrap a.wishlist-button:after, .woocommerce ul.product_list_widget li a:hover, .woocommerce-breadcrumbs-wrapper a:hover, .breadcrumb-trail .trail-items li a:hover,.header-site-info-wrap i,#top-menu li a:hover,.sticky-sidebar-icon:hover i,#site-navigation ul li:hover > a, #site-navigation ul li.current-menu-item > a, #site-navigation ul li.current_page_ancestor > a, #site-navigation ul li.current_page_item > a, #site-navigation ul li.current-menu-ancestor > a,.main-slider-section .slick-controls,.shopay_default_posts .post-meta,#footer-site-navigation ul li:hover > a, #footer-site-navigation ul li.current-menu-item > a, #footer-site-navigation ul li.current_page_ancestor > a, #footer-site-navigation ul li.current_page_item > a, #footer-site-navigation ul li.current-menu-ancestor > a,.site-bottom-footer a:hover,#shopay-scroll-to-top:hover,.entry-footer a:hover::before, .entry-footer a:hover,li.product .star-rating span::before,.custom-preloader i,.sticky-sidebar-close i:hover, p.stars.selected a:not(.active)::before,.services-item i{ color: ". esc_attr( $shopay_primary_color ) ."}\n";

        $output_css .= ".error404 .page-content .search-submit, .search-no-results .page-content .search-submit,.footer-social-media-section .follow-us-icon:hover,.reply .comment-reply-link,                                                                                                         .woocommerce #payment #place_order:hover, .woocommerce-page #payment #place_order:hover, .woocommerce div.product form.cart .button:hover:after, .woocommerce div.product form.cart .button:hover, .woocommerce div.product .woocommerce-tabs ul.tabs li.active, .woocommerce nav.woocommerce-pagination ul li a:focus, .woocommerce nav.woocommerce-pagination ul li a:hover, .woocommerce nav.woocommerce-pagination ul li span.current, .navigation .nav-links a:hover, .bttn:hover, button, input[type='button']:hover, input[type='reset']:hover, input[type='submit']:hover, #search-bar-section,.product-btn a,.main-slider-section .slick-dots li button:hover::after, .main-slider-section .slick-dots li.slick-active button::after,.shopay_default_posts .posted-on::after,.widget-title::after,.woocommerce ul.products li.product .onsale, .woocommerce span.onsale,.shopay-image-figure-wrapper .image-title-btn-wrap button,.shopay-image-figure-wrapper .image-title-btn-wrap button:hover,.custom-preloader::after, .custom-preloader::before,.shopay-image-figure-wrapper .image-title-btn-wrap button:hover,#site-navigation ul li a .menu-item-description,.woocommerce #payment #place_order, .woocommerce-page #payment #place_order{ background: ". esc_attr( $shopay_primary_color ) ."}\n";

        $output_css .= ".shopay-slider-section .product-categories,#shopay-scroll-to-top:hover,.shopay-image-figure-wrapper .image-title-btn-wrap button:hover,#colophon,.shopay-cat-menu.deactivate-menu{ border-color: ". esc_attr( $shopay_primary_color ) ."}\n";

        $output_css .= "#site-navigation ul li a .menu-item-description:after{ border-top-color: ". esc_attr( $shopay_primary_color ) ."}\n";

        $output_css .= ".woocommerce .woocommerce-message a.button:hover:after,.active a,a:hover,a:focus,a:active,.entry-cat .cat-links a:hover,.entry-cat a:hover,.entry-footer a:hover,.comment-author .fn .url:hover,.commentmetadata .comment-edit-link, #cancel-comment-reply-link, #cancel-comment-reply-link:before, .logged-in-as a,.widget a:hover, .widget a:hover::before, .widget li:hover::before,#site-navigation ul li a:hover,.cat-links a:hover,.navigation.pagination .nav-links .page-numbers.current, .navigation.pagination .nav-links a.page-numbers:hover, #footer-menu li a:hover, .entry-meta a:hover, .post-info-wrap .entry-meta a:hover, .breadcrumbs .trail-items li a:hover,  .entry-title a:hover, .widget_tag_cloud .tagcloud a:hover,.woocommerce ul.products li.product .price, .woocommerce div.product p.price, .woocommerce div.product span.price,.woocommerce-loop-product__title:hover,.woocommerce .star-rating span::before,.woocommerce p.stars:hover a::before, .woocommerce a:hover{ color: ". esc_attr( $shopay_primary_color ) ."}\n";

        $output_css .= ".page .wc-block-grid .add_to_cart_button, .page .wc-block-grid .added_to_cart, .page .wc-block-grid .wc-block-grid__product .wc-block-grid__product-onsale,.page .wc-block-featured-product .wc-block-featured-product__link .wp-block-button__link,.reply .comment-reply-link,.widget_search .search-submit,.lSSlideOuter .lSPager.lSpg > li.active a, .lSSlideOuter .lSPager.lSpg > li:hover a, .navigation .nav-links a, .bttn, input[type=button], input[type=reset], input[type=submit], .navigation .nav-links a:hover, .bttn:hover,input[type=button]:hover, input[type=reset]:hover, input[type=submit]:hover,.woocommerce #respond input#submit:hover, .woocommerce a.button:hover, .woocommerce button.button:hover, .woocommerce input.button:hover, .woocommerce #respond input#submit.alt:hover, .woocommerce a.button.alt:hover, .woocommerce button.button.alt:hover, .woocommerce input.button.alt:hover,.woocommerce #respond input#submit, .woocommerce input.button, .woocommerce #respond input#submit.alt, .woocommerce button.button.alt, .woocommerce input.button.alt, .added_to_cart.wc-forward,.product-btns-wrap a.button,.onsale,.woocommerce .widget_price_filter .ui-slider .ui-slider-range, .woocommerce .widget_price_filter .ui-slider .ui-slider-handle,.woocommerce .widget_price_filter .price_slider_amount .button:hover, .woocommerce #respond input#submit.alt.disabled, .woocommerce #respond input#submit.alt.disabled:hover, .woocommerce #respond input#submit.alt:disabled, .woocommerce #respond input#submit.alt:disabled:hover, .woocommerce #respond input#submit.alt[disabled]:disabled, .woocommerce #respond input#submit.alt[disabled]:disabled:hover, .woocommerce a.button.alt.disabled, .woocommerce a.button.alt.disabled:hover, .woocommerce a.button.alt:disabled, .woocommerce a.button.alt:disabled:hover, .woocommerce a.button.alt[disabled]:disabled, .woocommerce a.button.alt[disabled]:disabled:hover, .woocommerce button.button.alt.disabled, .woocommerce button.button.alt.disabled:hover, .woocommerce button.button.alt:disabled, .woocommerce button.button.alt:disabled:hover, .woocommerce button.button.alt[disabled]:disabled, .woocommerce button.button.alt[disabled]:disabled:hover, .woocommerce input.button.alt.disabled, .woocommerce input.button.alt.disabled:hover, .woocommerce input.button.alt:disabled, .woocommerce input.button.alt:disabled:hover, .woocommerce input.button.alt[disabled]:disabled, .woocommerce input.button.alt[disabled]:disabled:hover{ background: ". esc_attr( $shopay_primary_color ) ."}\n";

        $output_css .= ".footer-social-media-section .follow-us-icon:hover, .woocommerce div.product .woocommerce-tabs ul.tabs::before, .woocommerce div.product .woocommerce-tabs ul.tabs li.active,.navigation .nav-links a, .btn, button, input[type=button], input[type=reset], input[type=submit],.widget_search .search-submit{ border-color: ". esc_attr( $shopay_primary_color ) ."}\n";

        $shopay_display_header_text = get_theme_mod( 'shopay_display_header_text', true );
        $shopay_header_textcolor = get_theme_mod( 'shopay_header_textcolor', '#000000' );
        if ( true !== $shopay_display_header_text ) {
            $output_css .=".site-title, .site-description { position: absolute; clip: rect(1px, 1px, 1px, 1px); }\n";
        } else {
            $output_css .=".site-title a, .site-description { color: ". esc_attr( $shopay_header_textcolor ) ."; }\n";
        }


        $refine_output_css = shopay_css_strip_whitespace( $output_css );
        wp_add_inline_style( 'shopay-style', $refine_output_css );
    }
endif;