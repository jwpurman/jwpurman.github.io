<?php
/**
 * Widget for displaying latest products.
 *
 * @package Shopay
 */

class Shopay_Latest_Products extends WP_Widget{

	/**
     * Register widget with WordPress.
     */
    public function __construct() {
        $widget_ops = array( 
            'classname'                     => 'shopay-widget shopay_latest_product',
            'description'                   => __( 'Display latest products.', 'shopay' ),
            'customize_selective_refresh'   => true,
        );
        parent::__construct( 'shopay_latest_product', __( 'MT: Latest Products', 'shopay' ), $widget_ops );
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
                'shopay_widgets_default'      => __( 'Latest Products', 'shopay' ),
                'shopay_widgets_field_type'   => 'text'
            ),

            'widget_desc' => array(
                'shopay_widgets_name'         => 'widget_desc',
                'shopay_widgets_title'        => __( 'Widget Description', 'shopay' ),
                'shopay_widgets_field_type'   => 'text'
            ),

            'widget_bg_image' => array(
                'shopay_widgets_name'       => 'widget_bg_image',
                'shopay_widgets_title'      => __( 'Background Image', 'shopay' ),
                'shopay_widgets_default'      => '',
                'shopay_widgets_field_type' => 'upload',
            ),

            'widget_image_title' => array(
                'shopay_widgets_name'         => 'widget_image_title',
                'shopay_widgets_title'        => __( 'Image Title', 'shopay' ),
                'shopay_widgets_default'      => __( 'Great Quality', 'shopay' ),
                'shopay_widgets_field_type'   => 'text'
            ),

            'widget_image_btn_txt' => array(
                'shopay_widgets_name'         => 'widget_image_btn_txt',
                'shopay_widgets_title'        => __( 'Button Text', 'shopay' ),
                'shopay_widgets_default'      => __( 'View Collection', 'shopay' ),
                'shopay_widgets_field_type'   => 'text'
            ),

            'widget_image_btn_link' => array(
                'shopay_widgets_name'         => 'widget_image_btn_link',
                'shopay_widgets_title'        => __( 'Button Link', 'shopay' ),
                'shopay_widgets_field_type'   => 'url'
            ),

            'widget_product_type' => array(
                'shopay_widgets_name'         => 'widget_product_type',
                'shopay_widgets_title'        => __( 'Product Type', 'shopay' ),
                'shopay_widgets_default'      => 'latest-product',
                'shopay_widgets_description'  => __( 'Choose the product type options.', 'shopay' ),
                'shopay_widgets_field_type'   => 'select',
                'shopay_widgets_field_options' => array(
                    'latest-product'    =>  __( 'Latest Products', 'shopay' ),
                    'featured-product'  =>  __( 'Featured Products', 'shopay' ),
                ),
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

        $widget_title           = empty( $instance['widget_title'] ) ? '' : $instance['widget_title'];
        $widget_desc            = empty( $instance['widget_desc'] ) ? '' : $instance['widget_desc'];
        $widget_product_type    = empty( $instance['widget_product_type'] ) ? '' : $instance['widget_product_type'];
        $widget_bg_image        = empty( $instance['widget_bg_image'] ) ? '' : $instance['widget_bg_image'];
        $widget_image_title     = empty( $instance['widget_image_title'] ) ? '' : $instance['widget_image_title'];
        $widget_image_btn_txt   = empty( $instance['widget_image_btn_txt'] ) ? '' : $instance['widget_image_btn_txt'];
        $widget_image_btn_link  = empty( $instance['widget_image_btn_link'] ) ? '' : $instance['widget_image_btn_link'];

        echo $before_widget;
?>
<div class="mt-container">
    <div class="shopay-latest-products-section">
        <div class="section-title-wrapper">
            <?php
                if ( !empty( $widget_title ) ) {
                    echo '<h3 class="section-title">'.esc_html( $widget_title ).'</h3>';
                }

                if ( !empty( $widget_desc ) ) {
                    echo '<p class="section-desc">'.esc_html( $widget_desc ).'</p>';
                }
            ?>
        </div><!--.section-title-wrapper -->

        <div class="shopay-latest-product-wrapper clearfix">
            <div class="shopay-image-figure-wrapper">
                <?php if ( !empty( $widget_bg_image ) ) { ?>
                        <figure class="shopay-bg-image cover-image" style="background-image:url( <?php echo esc_url( $widget_bg_image ); ?> )">
                        </figure>
                <?php
                    }
                    
                    if ( !empty( $widget_image_title ) || !empty( $widget_image_btn_link ) ) {
                        echo '<div class="image-title-btn-wrap">';
                            if ( !empty( $widget_image_title ) ) {
                                echo '<h2 class="thumb-title">'.esc_html( $widget_image_title ).'</h2>';
                            }

                            if ( !empty( $widget_image_btn_link ) ) {
                                echo '<button><a href="'.esc_url( $widget_image_btn_link ).'">'.esc_html( $widget_image_btn_txt ).'</a></button>';
                            }
                        echo '</div><!-- .image-title-btn-wrap -->';
                    }
                ?>
            </div><!-- shopay-image-figure-wrapper --> 
            <div class="latest-products-wrap clearfix">
                <?php 
                    $product_cat_args = array(
                        'post_type'     => 'product',
                        'posts_per_page'=> 4,
                    );

                    if ( $widget_product_type === 'featured-product' ) {
                        $product_cat_args[ 'tax_query' ] = array(
                            array(
                                'taxonomy' => 'product_visibility',
                                'field'    => 'name',
                                'post_status'   => 'publish',
                                'terms'    => 'featured',
                            ),
                        );
                    }
                    $product_cat_query = new WP_Query( $product_cat_args );
                    if ( $product_cat_query -> have_posts() ) :
                        while ( $product_cat_query -> have_posts() ) : $product_cat_query -> the_post();
                            wc_get_template_part( 'content', 'product' );
                        endwhile;
                    endif;
                    wp_reset_postdata();
                ?>
            </div><!-- .latest-products-wrap -->
        </div><!-- .shopay-latest-product-wrapper -->
    </div><!-- .shopay-latest-products-section -->
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