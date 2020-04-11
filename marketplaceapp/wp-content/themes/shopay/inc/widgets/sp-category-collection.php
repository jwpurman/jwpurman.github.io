<?php
/**
 * Widget for display slider section along with categories menu
 *
 * @package Shopay
 */

class Shopay_Category_Collection extends WP_Widget {

	/**
     * Register widget with WordPress.
     */
    public function __construct() {
        $widget_ops = array( 
            'classname'                     => 'shopay-widget shopay_category_collection',
            'description'                   => __( 'Display product categories from selected category as slider.', 'shopay' ),
            'customize_selective_refresh'   => true,
        );
        parent::__construct( 'shopay_category_collection', __( 'MT: Category Collection', 'shopay' ), $widget_ops );
    }

    /**
     * Helper function that holds widget fields
     * Array is used in update and form functions
     */
    private function widget_fields(){
        $shopay_categories_lists = shopay_categories_lists();
        $fields = array(
            'category_collection_title' => array(
                'shopay_widgets_name'         => 'category_collection_title',
                'shopay_widgets_title'        => __( 'Category Collection Title', 'shopay' ),
                'shopay_widgets_default'      => __( 'Top Categories', 'shopay' ),
                'shopay_widgets_field_type'   => 'text'
            ),

            'section_cat_slugs' => array(
                'shopay_widgets_name'         => 'section_cat_slugs',
                'shopay_widgets_title'        => __( 'Section Categories', 'shopay' ),
                'shopay_widgets_field_type'   => 'multicheckboxes',
                'shopay_widgets_field_options' => $shopay_categories_lists
            )
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

        $category_collection_title = empty( $instance['category_collection_title'] ) ? '' : $instance['category_collection_title'];
        $section_cat_slugs = empty( $instance['section_cat_slugs'] ) ? '' : $instance['section_cat_slugs'];

        echo $before_widget;
?>
<div class="mt-container">
    <div class="shopay-category-collection-section clearfix">
        <div class="section-title-control-wrapper">
            <?php
                if ( !empty( $category_collection_title ) ) {
                    echo '<h3 class="section-title">'.esc_html( $category_collection_title ).'</h3>';
                }
            ?>

            <span class="categorySlider-controls categorySlider-controls__prev"><i class="fas fa-chevron-left"></i></span>
            <span class="categorySlider-controls categorySlider-controls__next"><i class="fas fa-chevron-right"></i></span>
        </div><!--.section-title-control-wrapper -->
        <div class="category-collection-wrap">
            <div class="categorySlider">
                <?php
                    if ( !empty( $section_cat_slugs ) ) :
                        foreach ( $section_cat_slugs as $section_cat_slug => $value ) {
                            $shopay_product_cat_info = get_term_by( 'slug', $section_cat_slug , 'product_cat' );
                                shopay_product_cat_sec( $shopay_product_cat_info );
                        }   
                    endif; 
                ?>
            </div><!-- .categorySlider -->
        </div><!-- .category-collection-wrap -->
    </div><!-- .shopay-category-collection-section -->
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
                $shopay_widgets_field_value = $instance[$shopay_widgets_name] ;
            }
            shopay_widgets_show_widget_field( $this, $widget_field, $shopay_widgets_field_value );
        }
    }
}