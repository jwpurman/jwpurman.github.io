<?php
/**
 * WooCommerce Compatibility File
 *
 * @link https://woocommerce.com/
 *
 * @package Shopay
 */

/**
 * WooCommerce setup function.
 *
 * @link https://docs.woocommerce.com/document/third-party-custom-theme-compatibility/
 * @link https://github.com/woocommerce/woocommerce/wiki/Enabling-product-gallery-features-(zoom,-swipe,-lightbox)-in-3.0.0
 *
 * @return void
 */
function shopay_woocommerce_setup() {
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'shopay_woocommerce_setup' );

/**
 * WooCommerce specific scripts & stylesheets.
 *
 * @return void
 */
function shopay_woocommerce_scripts() {
	wp_enqueue_style( 'shopay-woocommerce-style', get_template_directory_uri() . '/inc/woocommerce/woocommerce.css' );

	$font_path   = WC()->plugin_url() . '/assets/fonts/';
	$inline_font = '@font-face {
			font-family: "star";
			src: url("' . $font_path . 'star.eot");
			src: url("' . $font_path . 'star.eot?#iefix") format("embedded-opentype"),
				url("' . $font_path . 'star.woff") format("woff"),
				url("' . $font_path . 'star.ttf") format("truetype"),
				url("' . $font_path . 'star.svg#star") format("svg");
			font-weight: normal;
			font-style: normal;
		}';

	wp_add_inline_style( 'shopay-woocommerce-style', $inline_font );
}
add_action( 'wp_enqueue_scripts', 'shopay_woocommerce_scripts' );

/**
 * Disable the default WooCommerce stylesheet.
 *
 * Removing the default WooCommerce stylesheet and enqueing your own will
 * protect you during WooCommerce core updates.
 *
 * @link https://docs.woocommerce.com/document/disable-the-default-stylesheet/
 */
//add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

/**
 * Add 'woocommerce-active' class to the body tag.
 *
 * @param  array $classes CSS classes applied to the body tag.
 * @return array $classes modified to include 'woocommerce-active' class.
 */
function shopay_woocommerce_active_body_class( $classes ) {
	$classes[] = 'woocommerce-active';

	return $classes;
}
add_filter( 'body_class', 'shopay_woocommerce_active_body_class' );

/**
 * Products per page.
 *
 * @return integer number of products.
 */
function shopay_woocommerce_products_per_page() {
	return 12;
}
add_filter( 'loop_shop_per_page', 'shopay_woocommerce_products_per_page' );

/**
 * Product gallery thumnbail columns.
 *
 * @return integer number of columns.
 */
function shopay_woocommerce_thumbnail_columns() {
	return 4;
}
add_filter( 'woocommerce_product_thumbnails_columns', 'shopay_woocommerce_thumbnail_columns' );

/**
 * Default loop columns on product archives.
 *
 * @return integer products per row.
 */
function shopay_woocommerce_loop_columns() {
	return 3;
}
add_filter( 'loop_shop_columns', 'shopay_woocommerce_loop_columns' );

/**
 * Related Products Args.
 *
 * @param array $args related products args.
 * @return array $args related products args.
 */
function shopay_woocommerce_related_products_args( $args ) {
	$defaults = array(
		'posts_per_page' => 3,
		'columns'        => 3,
	);

	$args = wp_parse_args( $defaults, $args );

	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'shopay_woocommerce_related_products_args' );

if ( ! function_exists( 'shopay_woocommerce_product_columns_wrapper' ) ) {
	
	/**
	 * Product columns wrapper.
	 *
	 * @return  void
	 */
	function shopay_woocommerce_product_columns_wrapper() {
		$columns = shopay_woocommerce_loop_columns();
		echo '<div class="columns-' . absint( $columns ) . '">';
	}

}
add_action( 'woocommerce_before_shop_loop', 'shopay_woocommerce_product_columns_wrapper', 40 );

if ( ! function_exists( 'shopay_woocommerce_product_columns_wrapper_close' ) ) {

	/**
	 * Product columns wrapper close.
	 *
	 * @return  void
	 */
	function shopay_woocommerce_product_columns_wrapper_close() {
		echo '</div>';
	}

}
add_action( 'woocommerce_after_shop_loop', 'shopay_woocommerce_product_columns_wrapper_close', 40 );

/**
 * Remove default WooCommerce wrapper.
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

if ( ! function_exists( 'shopay_woocommerce_wrapper_before' ) ) {

	/**
	 * Before Content.
	 *
	 * Wraps all WooCommerce content in wrappers which match the theme markup.
	 *
	 * @return void
	 */
	function shopay_woocommerce_wrapper_before() {
?>
		<div id="primary" class="content-area">
			<main id="main" class="site-main" role="main">
<?php
	}

}
add_action( 'woocommerce_before_main_content', 'shopay_woocommerce_wrapper_before' );

if ( ! function_exists( 'shopay_woocommerce_wrapper_after' ) ) {

	/**
	 * After Content.
	 *
	 * Closes the wrapping divs.
	 *
	 * @return void
	 */
	function shopay_woocommerce_wrapper_after() {
			?>
			</main><!-- #main -->
		</div><!-- #primary -->
		<?php
	}

}
add_action( 'woocommerce_after_main_content', 'shopay_woocommerce_wrapper_after' );

if ( ! function_exists( 'shopay_woocommerce_cart_link_fragment' ) ) {
	/**
	 * Cart Fragments.
	 *
	 * Ensure cart contents update when products are added to the cart via AJAX.
	 *
	 * @param array $fragments Fragments to refresh via AJAX.
	 * @return array Fragments to refresh via AJAX.
	 */
	function shopay_woocommerce_cart_link_fragment( $fragments ) {
		ob_start();
		shopay_woocommerce_cart_link();
		$fragments['a.cart-contents'] = ob_get_clean();

		return $fragments;
	}
}
add_filter( 'woocommerce_add_to_cart_fragments', 'shopay_woocommerce_cart_link_fragment' );

if ( ! function_exists( 'shopay_woocommerce_cart_link' ) ) {

	/**
	 * Cart Link.
	 *
	 * Displayed a link to the cart including the number of items present and the cart total.
	 *
	 * @return void
	 */
	function shopay_woocommerce_cart_link() {
		$shopay_cart_icon_title = apply_filters( 'shopay_cart_icon_title', __( 'View your shopping cart', 'shopay' ) );
?>
		<a class="cart-contents" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php echo esc_attr( $shopay_cart_icon_title ); ?>">
			<i class="fas fa-shopping-basket"></i>
			<?php $item_count_text = WC()->cart->get_cart_contents_count(); ?>
			<span class="count"><?php echo esc_html( $item_count_text ); ?></span>
			<span class="amount"><?php echo wp_kses_data( WC()->cart->get_cart_subtotal() ); ?></span>
		</a>
<?php
	}

}

if ( ! function_exists( 'shopay_woocommerce_header_cart' ) ) {

	/**
	 * Display Header Cart.
	 *
	 * @return void
	 */
	function shopay_woocommerce_header_cart() {
		$shopay_cart_link_option = get_theme_mod( 'shopay_cart_link_option', false );
		if ( false == $shopay_cart_link_option ) {
			return;
		}
		if ( is_cart() ) {
			$class = 'current-menu-item';
		} else {
			$class = '';
		}
?>
		<ul id="site-header-cart" class="site-header-cart">
			<li class="<?php echo esc_attr( $class ); ?>">
				<?php shopay_woocommerce_cart_link(); ?>
			</li>
			<li>
				<?php
					$instance = array(
						'title' => '',
					);

					the_widget( 'WC_Widget_Cart', $instance );
				?>
			</li>
		</ul>
<?php
	}

}

/*------------------------------------------------------------------------------------------------------------------------------------------*/
/**
 * Wrap the #primary and #secondary with <div class="shopay-all-content-wrapper"> div.
 * 
 */
if ( ! function_exists( 'shopay_all_content_wrapper_opens' ) ) :
	
	/**
	 * Shopay All Content Wrapper Opens
	 */
	function shopay_all_content_wrapper_opens() {
		echo '<div class="shopay-all-content-wrapper">';
	}

endif;

if ( ! function_exists( 'shopay_all_content_wrapper_closes' ) ) :
	
	/**
	 * Shopay All Content Wrapper Closes
	 */
	function shopay_all_content_wrapper_closes() {
		echo '</div><!-- .shopay-all-content-wrapper -->';
	}

endif;
add_action( 'woocommerce_before_main_content', 'shopay_all_content_wrapper_opens', 5 );
add_action( 'woocommerce_sidebar', 'shopay_all_content_wrapper_closes', 20 );
/*------------------------------------------------------------------------------------------------------------------------------------------*/
/**
 * Adds Add to wishlist button Product List
 *
 */
function shopay_archive_product_wishlist_btn() {
	if ( ! shopay_is_active_wishlist() ) {
	    return;
	}
	global $product;
	$product_id 		= yit_get_product_id( $product );
	$current_product 	= wc_get_product( $product_id );
	$product_type 		= $current_product->get_type();
	$whishlist_url 		= YITH_WCWL()->get_wishlist_url();
	$shopay_wishlist_btn_label = apply_filters( 'shopay_wishlist_btn_label', __( 'Add To Wishlist', 'shopay' ) );
?>	
		<a class="wishlist-button add_to_wishlist" href="<?php echo esc_url( add_query_arg( 'add_to_wishlist', intval( $product_id ) ) )?>" rel="nofollow" data-product-id="<?php echo esc_attr( $product_id ); ?>" data-product-type="<?php echo esc_attr( $product_type ); ?>">
			<?php echo esc_html( $shopay_wishlist_btn_label ); ?>
		</a> <!-- .whishlist-buttom -->
<?php
}
add_action( 'woocommerce_after_shop_loop_item', 'shopay_archive_product_wishlist_btn', 15 );

if ( shopay_is_active_quick_view() ) {
	/**
	 * Hooks quick view button inside product thumbnail wrap
	 */
	$shopay_quick_view = new YITH_WCQV_Frontend();
	remove_action( 'woocommerce_after_shop_loop_item', array( $shopay_quick_view, 'yith_add_quick_view_button' ), 15 );
	add_action( 'woocommerce_before_shop_loop_item_title', array( $shopay_quick_view, 'yith_add_quick_view_button' ), 15 );
}
/*------------------------------------------------------------------------------------------------------------------------------------------*/
if ( ! function_exists( 'shopay_get_shop_sidebar' ) ) :

	/**
	 * Function for shop sidebar
	 */
	function shopay_get_shop_sidebar() {
		if ( !is_active_sidebar( 'sidebar-woocommerce' ) ) {
			return;
		}
		echo '<aside id="secondary" class="widget-area">';
			dynamic_sidebar( 'sidebar-woocommerce' );
		echo '</aside><!-- #secondary -->';
	}

endif;

// Remove WooCommerce sidebar
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
add_action( 'woocommerce_sidebar', 'shopay_get_shop_sidebar', 15 );

/*----------------------------------------------------------------------------------------------------------------------------------------*/
/**
 * Removed breadcrumb 
 *
 * @since 1.0.0
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

/*----------------------------------------------------------------------------------------------------------------------------------------*/
/**
 * Wrap the sale and thumbnail in product list - archive.
 * 
 */
if ( ! function_exists( 'shopay_product_archive_thumbwrap_open' ) ) :

	/**
	 * Wrapper Open
	 */
	function shopay_product_archive_thumbwrap_open() {
		echo '<div class="product-thumbnail-wrap">';
	}

endif;

if ( ! function_exists( 'shopay_product_archive_thumbwrap_close' ) ) :

	/**
	 * Wrapper Close
	 */
	function shopay_product_archive_thumbwrap_close() {
		echo '</div><!-- .product-thumbnail-wrap -->';
	}

endif;
add_action( 'woocommerce_before_shop_loop_item_title', 'shopay_product_archive_thumbwrap_open', 5 );
add_action( 'woocommerce_before_shop_loop_item_title', 'shopay_product_archive_thumbwrap_close', 20 );
/*----------------------------------------------------------------------------------------------------------------------------------------*/
/**
 * Wrap the woocommerce buttons.
 * 
 */
if ( ! function_exists( 'shopay_product_archive_btnwrap_open' ) ) :

	/**
	 * Buttons Wrapper Open
	 */
	function shopay_product_archive_btnwrap_open() {
		echo '<div class="product-btns-wrap">';
	}

endif;

if ( ! function_exists( 'shopay_product_archive_btnwrap_close' ) ) :
	
	/**
	 * Buttons Wrapper Close
	 */
	function shopay_product_archive_btnwrap_close() {
		echo '</div><!-- .product-btns-wrap -->';
	}

endif;

add_action( 'woocommerce_after_shop_loop_item', 'shopay_product_archive_btnwrap_open', 9 );
add_action( 'woocommerce_after_shop_loop_item', 'shopay_product_archive_btnwrap_close', 20 );

/**
 * managed loop product link functions
 */
remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );

add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_link_open', 5 );
add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_link_close', 15 );