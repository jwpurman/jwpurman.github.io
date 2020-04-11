<?php
/**
 * Customizer callbacks functions. 
 * 
 * @package Shopay
 */

if ( ! function_exists( 'shopay_site_info_option_active_callback' ) ) :
    /**
	 * Check if header site information option is enabled.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Customize_Control $control WP_Customize_Control instance.
	 *
	 * @return bool Whether the control is active to the current preview.
	 */
    function shopay_site_info_option_active_callback( $control ) {
        if ( false !== $control->manager->get_setting( 'shopay_site_info_option' )->value() ) {
            return true;
        } else {
            return false;
        }
    }
    
endif;

if ( ! function_exists( 'shopay_yith_active_callback' ) ) :
    /**
     * Check if WooCommerce and yith wishlist plugin was activated.
     *
     * @since 1.0.0
     *
     * @param WP_Customize_Control $control WP_Customize_Control instance.
     *
     * @return bool Whether the control is active to the current preview.
     */
    function shopay_yith_active_callback( $control ) {
        if ( shopay_is_active_woocommerce() && shopay_is_active_wishlist() ) {
            return true;
        } else {
            return false;
        }
    }
    
endif;

if ( ! function_exists( 'shopay_cat_menu_title_active_callback' ) ) :
    /**
     * Check if cat menu option was enable with WooCommerce plugin was activated.
     *
     * @since 1.0.0
     *
     * @param WP_Customize_Control $control WP_Customize_Control instance.
     *
     * @return bool Whether the control is active to the current preview.
     */
    function shopay_cat_menu_title_active_callback( $control ) {
        if ( false !== $control->manager->get_setting( 'shopay_cat_menu_option' )->value() && shopay_is_active_woocommerce() ) {
            return true;
        } else {
            return false;
        }
    }
    
endif;

if ( ! function_exists( 'shopay_search_field_type_active_callback' ) ) :
    /**
     * Check if header Search Bar Section option and Search field option was enabled with WooCommerce plugin was activated.
     *
     * @since 1.0.0
     *
     * @param WP_Customize_Control $control WP_Customize_Control instance.
     *
     * @return bool Whether the control is active to the current preview.
     */
    function shopay_search_field_type_active_callback( $control ) {
        if ( false !== $control->manager->get_setting( 'shopay_search_bar_option' )->value() ) {
            return true;
        } else {
            return false;
        }
    }
    
endif;

if ( ! function_exists( 'shopay_is_woo_acitvated_active_callback' ) ) :
    /**
     * Check if WooCommerce plugin was activated.
     *
     * @since 1.0.0
     *
     * @param WP_Customize_Control $control WP_Customize_Control instance.
     *
     * @return bool Whether the control is active to the current preview.
     */
    function shopay_is_woo_acitvated_active_callback( $control ) {
        if ( shopay_is_active_woocommerce() ) {
            return true;
        } else {
            return false;
        }
    }
    
endif;

if ( ! function_exists( 'shopay_highlight_product_option_with_woo_active_callback' ) ) :
    /**
     * Check if highlight woo product option was enabled with WooCommerce plugin activated.
     *
     * @since 1.0.0
     *
     * @param WP_Customize_Control $control WP_Customize_Control instance.
     *
     * @return bool Whether the control is active to the current preview.
     */
    function shopay_highlight_product_option_with_woo_active_callback( $control ) {
        if ( false !== $control->manager->get_setting( 'shopay_highlight_products_option' )->value() && shopay_is_active_woocommerce() ) {
            return true;
        } else {
            return false;
        }
    }
    
endif;

if ( ! function_exists( 'shopay_footer_widget_area_option_active_callback' ) ) :
    /**
	 * Check if footer widget area option is enabled.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Customize_Control $control WP_Customize_Control instance.
	 *
	 * @return bool Whether the control is active to the current preview.
	 */
    function shopay_footer_widget_area_option_active_callback( $control ) {
        if ( false !== $control->manager->get_setting( 'shopay_footer_widget_area_option' )->value() ) {
            return true;
        } else {
            return false;
        }
    }
    
endif;

if ( ! function_exists( 'shopay_site_layout_bg_active_callback' ) ) :
    /**
     * Check if site layout is boxed layout or not.
     *
     * @since 1.0.0
     *
     * @param WP_Customize_Control $control WP_Customize_Control instance.
     *
     * @return bool Whether the control is active to the current preview.
     */
    function shopay_site_layout_bg_active_callback( $control ) {
        if ( 'full-width' != $control->manager->get_setting( 'shopay_site_layout' )->value() ) {
            return true;
        } else {
            return false;
        }
    }
    
endif;

if ( ! function_exists( 'shopay_header_sticky_sidebar_active_callback' ) ) :
    /**
     * Check if header sticky sidebar option is true or not.
     *
     * @since 1.0.0
     *
     * @param WP_Customize_Control $control WP_Customize_Control instance.
     *
     * @return bool Whether the control is active to the current preview.
     */
    function shopay_header_sticky_sidebar_active_callback( $control ) {
        if ( false !== $control->manager->get_setting( 'shopay_sticky_header_sidebar_option' )->value() ) {
            return true;
        } else {
            return false;
        }
    }
    
endif;



/*---------------------------------------------------------------------------------------------------------------*/

/**
 * Customizer selective refresh
 *
 * @since 1.0.0
 */
if ( isset( $wp_customize->selective_refresh ) ) {

    // Site Identity > Site Title
    $wp_customize->selective_refresh->add_partial( 'blogname',
        array(
            'selector'        => '.site-title a',
            'render_callback' => 'shopay_customize_partial_blogname',
        )
    );

    // Site Identity > Tagline
    $wp_customize->selective_refresh->add_partial( 'blogdescription',
        array(
            'selector'        => '.site-description',
            'render_callback' => 'shopay_customize_partial_blogdescription',
        )
    );

    // Theme Options > Header > Top Bar > Short Description
    $wp_customize->selective_refresh->add_partial( 'shopay_top_header_description',
        array(
            'selector'        => '.top-header-description',
            'render_callback' => 'shopay_customize_partial_top_header_description',
        )
    );

    // Theme Options > Header > Top Bar > Site Location
    $wp_customize->selective_refresh->add_partial( 'shopay_top_header_location',
        array(
            'selector'        => '.top-header-elements.site-location a',
            'render_callback' => 'shopay_customize_partial_top_header_location',
        )
    );

    // Theme Options > Header > Top Bar > Site Service
    $wp_customize->selective_refresh->add_partial( 'shopay_top_header_service',
        array(
            'selector'        => '.top-header-elements.site-service a',
            'render_callback' => 'shopay_customize_partial_top_header_service',
        )
    );

    // Theme Options > Header > Main Area > Contact Info
    $wp_customize->selective_refresh->add_partial( 'shopay_header_site_contact_info',
        array(
            'selector'        => '.site-info-contact',
            'render_callback' => 'shopay_customize_partial_header_site_contact_info',
        )
    );

    // Theme Options > Header > Main Area > Email Info
    $wp_customize->selective_refresh->add_partial( 'shopay_header_site_email_info',
        array(
            'selector'        => '.site-info-email',
            'render_callback' => 'shopay_customize_partial_header_site_email_info',
        )
    );

    // Theme Options > Header > Search Bar > Category Menu Title
    $wp_customize->selective_refresh->add_partial( 'shopay_cat_menu_title',
        array(
            'selector'        => '.main-category-list-title',
            'render_callback' => 'shopay_customize_partial_cat_menu_title',
        )
    );

    // Theme Options > Header > Woo Products > Site Title
    $wp_customize->selective_refresh->add_partial( 'shopay_highlight_products_title',
        array(
            'selector'        => '.highlight-title',
            'render_callback' => 'shopay_customize_partial_highlight_products_title',
        )
    );

    // Theme Options > Footer > Main Area
    $wp_customize->selective_refresh->add_partial( 'shopay_footer_description',
        array(
            'selector'        => '.footer-description',
            'render_callback' => 'shopay_customize_partial_footer_description',
        )
    );

    // Theme Options > Footer > Bottom Area
    $wp_customize->selective_refresh->add_partial( 'shopay_copyright_text',
        array(
            'selector'        => '.site-bottom-footer .mt-container .site-info',
            'render_callback' => 'shopay_customize_partial_copyright_text',
        )
    );

}
/*--------------------------------------------------------------------------------------------------------------*/

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function shopay_customize_partial_blogname() {
    bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function shopay_customize_partial_blogdescription() {
    bloginfo( 'description' );
}

/**
 * Render the top header description for the selective refresh partial.
 *
 * @return void
 */
function shopay_customize_partial_top_header_description() {
    return get_theme_mod( 'shopay_top_header_description' );
}

/**
 * Render the top header location for the selective refresh partial.
 *
 * @return void
 */
function shopay_customize_partial_top_header_location() {
    return get_theme_mod( 'shopay_top_header_location' );
}

/**
 * Render the top header service for the selective refresh partial.
 *
 * @return void
 */
function shopay_customize_partial_top_header_service() {
    return get_theme_mod( 'shopay_top_header_service' );
}

/**
 * Render the header site contact info for the selective refresh partial.
 *
 * @return void
 */
function shopay_customize_partial_header_site_contact_info() {
    return get_theme_mod( 'shopay_header_site_contact_info' );
}

/**
 * Render the header site email info for the selective refresh partial.
 *
 * @return void
 */
function shopay_customize_partial_header_site_email_info() {
    return get_theme_mod( 'shopay_header_site_email_info' );
}

/**
 * Render the search bar category menu title for the selective refresh partial.
 *
 * @return void
 */
function shopay_customize_partial_cat_menu_title() {
    return get_theme_mod( 'shopay_cat_menu_title' );
}

/**
 * Render the woo products section title for the selective refresh partial.
 *
 * @return void
 */
function shopay_customize_partial_highlight_products_title() {
    return get_theme_mod( 'shopay_highlight_products_title' );
}

/**
 * Render the footer short description for the selective refresh partial.
 *
 * @return void
 */
function shopay_customize_partial_footer_description() {
    return get_theme_mod( 'shopay_footer_description' );
}

/**
 * Render the footer copyright text for the selective refresh partial.
 *
 * @return void
 */
function shopay_customize_partial_copyright_text() {
    return get_theme_mod( 'shopay_copyright_text' );
}