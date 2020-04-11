<?php
/**
 * Widget for displaying category products.
 *
 * @package Shopay
 */

class Shopay_Category_Products extends WP_Widget{

	/**
     * Register widget with WordPress.
     */
    public function __construct() {
        $widget_ops = array( 
            'classname'                     => 'shopay-widget shopay_category_products',
            'description'                   => __( 'Display products from selected category.', 'shopay' ),
            'customize_selective_refresh'   => true,
        );
        parent::__construct( 'shopay_category_products', __( 'MT: Category Products', 'shopay' ), $widget_ops );
    }

    /**
     * Helper function that holds widget fields
     * Array is used in update and form functions
     */
    private function widget_fields(){

        $fields = array(
            'widget_title' => array(
                'shopay_widgets_name'         => 'widget_title',
                'shopay_widgets_title'        => __( 'Widget Title', 'shopay' ),
                'shopay_widgets_default'      => __( 'Category Products', 'shopay' ),
                'shopay_widgets_field_type'   => 'text'
            ),

            'category_product_cat_slug' => array(
                'shopay_widgets_name'         => 'category_product_cat_slug',
                'shopay_widgets_title'        => __( 'Product Categories', 'shopay' ),
                'shopay_widgets_default'      => '',
                'shopay_widgets_field_type'   => 'woo_category_dropdown'
            ),

            'category_product_slider_post_count' => array(
                'shopay_widgets_name'         => 'category_product_slider_post_count',
                'shopay_widgets_title'        => __( 'Post Count', 'shopay' ),
                'shopay_widgets_default'      => 6,
                'shopay_widgets_field_type'   => 'number'
            ),
        );
        return $fields;
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {
        extract( $args );
        if ( empty( $instance ) ) {
            return;
        }

        $widget_title               = empty( $instance['widget_title'] ) ? '' : $instance['widget_title'];
        $category_product_cat_slug  = empty( $instance['category_product_cat_slug'] ) ? '' : $instance['category_product_cat_slug'];
        $category_product_slider_post_count = empty( $instance['category_product_slider_post_count'] ) ? '' : $instance['category_product_slider_post_count'];

        echo $before_widget;
?>
<div class="mt-container">
    <div class="shopay-category-products-section">
        <?php
            if ( !empty( $widget_title ) ) {
                echo '<div class="section-title-wrapper"><h3 class="section-title">'.esc_html( $widget_title ).'</h3></div><!--.section-title-wrapper -->';
            }
        ?>
        <div class="category-products-wrap clearfix">
            <?php 
                $category_product_cat_args = array(
                    'post_type'     => 'product',
                    'product_cat'   => esc_html( $category_product_cat_slug ),
                    'posts_per_page'=> absint( $category_product_slider_post_count ),
                );

                $category_product_cat_query = new WP_Query( $category_product_cat_args );
                if ( $category_product_cat_query -> have_posts() ) :
                    while ( $category_product_cat_query -> have_posts() ) : $category_product_cat_query -> the_post();
                        wc_get_template_part( 'content', 'product' );
                    endwhile;
                endif;
                wp_reset_postdata();
            ?>
        </div><!-- .category-products-wrap -->
    </div><!-- .shopay-category-products-section -->
</div><!-- mt-container -->
<?php
    	echo $after_widget;
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param   array   $new_instance   Values just sent to be saved.
     * @param   array   $old_instance   Previously saved values from database.
     *
     * @uses    shopay_widgets_updated_field_value()      defined in widget-fields.php
     *
     * @return  array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        $widget_fields = $this->widget_fields();

        // Loop through fields
        foreach ( $widget_fields as $widget_field ) {

            extract( $widget_field );

            // Use helper function to get updated field values
            $instance[$shopay_widgets_name] = shopay_widgets_updated_field_value( $widget_field, $new_instance[$shopay_widgets_name] );
        }

        return $instance;
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param   array $instance Previously saved values from database.
     *
     * @uses    shopay_widgets_show_widget_field()        defined in widget-fields.php
     */
    public function form( $instance ) {

        $widget_fields = $this->widget_fields();

        // Loop through fields
        foreach ( $widget_fields as $widget_field ) {

            // Make array elements available as variables
            extract( $widget_field );

            if ( empty( $instance ) && isset( $shopay_widgets_default ) ) {
                $shopay_widgets_field_value = $shopay_widgets_default;
            } elseif ( empty( $instance ) ) {
                $shopay_widgets_field_value = '';
            } else {
                $shopay_widgets_field_value = wp_kses_post( $instance[$shopay_widgets_name] );
            }
            shopay_widgets_show_widget_field( $this, $widget_field, $shopay_widgets_field_value );
        }
    }
}