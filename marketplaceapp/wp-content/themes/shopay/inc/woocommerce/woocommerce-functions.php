<?php
/**
 * Required functions for woocommerce or its extensions.
 * 
 * @package Shopay
 */

if ( ! function_exists( 'shopay_get_advanced_product_search' ) ) :
	/**
     * Woocommerce Product search 
     * 
     */
	function shopay_get_advanced_product_search() {

		$args = array(
                'number'     => '',
                'orderby'    => 'name',
                'order'      => 'ASC',
                'hide_empty' => true
        );
        $product_categories = get_terms( 'product_cat', $args ); 
        $categories_show = '<option value="">'.esc_html__( 'All Categories', 'shopay' ).'</option>';
        $check = '';
        if ( is_search() ) {
            if ( isset( $_GET['term'] ) && $_GET['term'] != '' ) {
                $check = isset( $_GET['term'] ) ? sanitize_text_field( wp_unslash( $_GET['term'] ) ) : '';
            }
        }
        $checked = '';
        $categories_show .= '<optgroup class="sm-advance-search" label="'.esc_html__( 'All Categories', 'shopay' ).'">';
        foreach( $product_categories as $category ) {
            if ( isset ( $category->slug ) ) {
                if ( trim( $category->slug ) == trim( $check ) ) {
                    $checked = 'selected="selected"';
                }
                $categories_show  .= '<option '.$checked.' value="'.esc_attr( $category->slug ).'">'.esc_html( $category->name ).'</option>';
                $checked = '';
            }
        }
        $categories_show .= '</optgroup>';
        echo $form = '<div class="mt-woo-product-search-wrapper"><form role="search" method="get" class="woocommerce-product-search" id="searchform"  action="' . esc_url( home_url( '/'  ) ) . '">
        			<div class = "search-wrap">
                         <div class="sm_search_wrap">
                            <select class="mt-select-products false" name="term">'.$categories_show.'
                            </select>
                         </div>
                         <div class="sm_search_form">
                             <input type="text" value="' . get_search_query() . '" name="s" id="s"  class="search-field" placeholder="'.esc_html__( 'Search products', 'shopay' ).'" autocomplete="off"/>
                             <button type="submit" id="searchsubmit">
                             <i class="fa fa-search"></i></button>
                             <input type="hidden" name="post_type" value="product" />
                             <input type="hidden" name="taxonomy" value="product_cat" />
                         </div>
                         <div class="search-content"></div>
                    </div>
                     
                </form><!-- .woocommerce-product-search --></div><!-- .mt-woo-product-search-wrapper -->';        
		return $form;
	}
endif;
/*-------------------------------------------------------------------------------------------------------------------------------------------*/

/**
 * Wishlist update
 * 
 */
if ( defined( 'YITH_WCWL' ) && ! function_exists( 'shopay_yith_wcwl_ajax_update_count' ) ) {
	function shopay_yith_wcwl_ajax_update_count() {
		wp_send_json( array(
			'count' => yith_wcwl_count_all_products()
		) );
	}

    add_action( 'wp_ajax_yith_wcwl_update_wishlist_count', 'shopay_yith_wcwl_ajax_update_count' );
    add_action( 'wp_ajax_nopriv_yith_wcwl_update_wishlist_count', 'shopay_yith_wcwl_ajax_update_count' );
}

if ( ! function_exists( 'shopay_header_wishlist_link' ) ) :
    /** 
     *  Header Wishlist Link function
     *
     */
    function shopay_header_wishlist_link() {
    	
    	$shopay_wishlist_link_option = get_theme_mod( 'shopay_wishlist_link_option', false );
    	if ( !shopay_is_active_wishlist() || ( $shopay_wishlist_link_option ) == false  ) { 
    		return;
    	}

    		$whishlist_url = YITH_WCWL()->get_wishlist_url();
    ?>
    		<div class="shopay-whishlist">
    			<a href="<?php echo esc_url( $whishlist_url ); ?>">
    				<i class="fas fa-heart"></i>
    				<span class="shopay-wl-counter">
    					<?php printf( esc_html( '%s', 'shopay' ), esc_attr( yith_wcwl_count_products() ) ); ?>
    				</span> 
    			</a>
    		</div>
    <?php
    }
endif;

/*----------------------------------------------------------------------------------------------------------------------------------------*/
/**
 * categories list
 *
 * @return array();
 */
if ( !function_exists( 'shopay_categories_lists' ) ) :
    function shopay_categories_lists() {
		if ( !shopay_is_active_woocommerce() ) {
			return;
		}

        $shopay_cat_args = array(
			'taxonomy'    => 'product_cat',
            'orderby'     => 'name',
            'order'       => 'ASC',
            'hide_empty'  => 1,
        );
        $shopay_categories = get_terms( $shopay_cat_args );
        $shopay_categories_lists = array();
        foreach( $shopay_categories as $category ) {
            $shopay_categories_lists[esc_attr( $category->slug )] = esc_html( $category->name );
        }
        return $shopay_categories_lists;
    }
endif;