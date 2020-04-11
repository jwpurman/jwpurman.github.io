<?php
/**
 * Widgets hooks
 * 
 * @package Shopay
 */

if ( !function_exists( 'shopay_product_cat_sec' ) ):
    /**
     * Front Page Categories Collection
     */
    function shopay_product_cat_sec( $shopay_product_cat_info ){
        $cat_name = $shopay_product_cat_info->name;
        $cat_slug = $shopay_product_cat_info->slug;
        $cat_id = $shopay_product_cat_info->term_id;
        $thumbnail_id = get_term_meta( $cat_id, 'thumbnail_id', true );
        if ( empty( $thumbnail_id ) ) {
            return;
        }
?>
        <div class="category-content  wow fadeInUp">
            <?php $cat_link = get_term_link( $cat_id, 'product_cat' ); ?>
            <figure class="category-thumb">
                <a href="<?php echo esc_url( $cat_link ); ?>">
                    <?php 
                        echo'<div class="category-image">';
                        echo wp_get_attachment_image( $thumbnail_id , 'thumbnail' );
                        echo'</div>';
                    ?> 
                </a>
            </figure>

            <h4 class="category-title small-font">
                <a href="<?php echo esc_url( $cat_link ); ?>"><?php echo esc_html( $cat_name ); ?></a>
            </h4>
        </div><!-- .category-content -->
<?php
    }
endif;
/*-----------------------------------------------------------------------------------------------------------------------------------------------------------------*/
if ( !function_exists( 'shopay_pfc_sec' ) ):
    /**
     * Front Page Product Filterby categories.
     */
    function shopay_pfc_sec(){
        $pfc_cat_args = array(
            'post_type'     => 'product',
            'posts_per_page' => -1
        );
        $pfc_cat_query = new WP_Query( $pfc_cat_args );
        if ( $pfc_cat_query -> have_posts() ) :
            while ( $pfc_cat_query -> have_posts() ) : $pfc_cat_query -> the_post();
                wc_get_template_part( 'content', 'product' );
            endwhile;
        endif;
        wp_reset_postdata();
    }
endif;