<?php
/**
 * Function for handling widgets files. 
 * 
 * @package Shopay
 */

 /**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function shopay_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'shopay' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'shopay' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	if ( shopay_is_active_woocommerce() ) {
		/**
		 * Register Shop Sidebar
		 */
		register_sidebar( array(
			'name'          => esc_html__( 'Woocommerce Sidebar', 'shopay' ),
			'id'            => 'sidebar-woocommerce',
			'description'   => esc_html__( 'Add widgets here.', 'shopay' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );
	}
		
	/**
	 * Register Sticky Header Sidebar
	 */
	register_sidebar( array(
		'name'          => esc_html__( 'Sticky Header Sidebar', 'shopay' ),
		'id'            => 'sticky-header-sidebar',
		'description'   => esc_html__( 'Add widgets here.', 'shopay' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	
	/**
	 * Register Homepage Section
	 */
	register_sidebar( array(
		'name'          => esc_html__( 'Homepage Section', 'shopay' ),
		'id'            => 'homepage-section',
		'description'   => esc_html__( 'Add widgets here.', 'shopay' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
		
	/**
	 * Register Footer Sidebars.
	 */
	register_sidebars( 5, array(
		'name'          => esc_html__( 'Footer Sidebar %d', 'shopay' ),
		'id'            => 'footer-sidebar',
		'description'   => esc_html__( 'Add widgets here.', 'shopay' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	/**
	 * Register widgets
	 */
	register_widget( 'Shopay_Slider' );
	register_widget( 'Shopay_Default_Posts' );
	register_widget( 'Shopay_Sponsors' );
	register_widget( 'Shopay_Services' );
	if ( shopay_is_active_woocommerce() ) {
		require get_template_directory().'/inc/widgets/sp-category-collection.php';
		require get_template_directory().'/inc/widgets/sp-product-filterby-category.php';
		require get_template_directory().'/inc/widgets/sp-latest-products.php';
		require get_template_directory().'/inc/widgets/sp-category-products.php';
		
		register_widget( 'Shopay_Category_Collection' );
		register_widget( 'Shopay_Product_Filterby_Category' );
		register_widget( 'Shopay_Latest_Products' );
		register_widget( 'Shopay_Category_Products' );
	}
}
add_action( 'widgets_init', 'shopay_widgets_init' );

/**
 * Load widgets file
 * 
 */
require get_template_directory().'/inc/widgets/widgets-fields.php';
require get_template_directory().'/inc/widgets/widgets-hooks.php';
require get_template_directory().'/inc/widgets/sp-slider-widget.php';

require get_template_directory().'/inc/widgets/sp-default-posts.php';
require get_template_directory().'/inc/widgets/sp-sponsors.php';
require get_template_directory().'/inc/widgets/sp-services.php';