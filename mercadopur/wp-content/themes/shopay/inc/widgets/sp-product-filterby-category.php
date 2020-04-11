<?php
/**
 * Widget for display product filter section.
 *
 * @package Shopay
 */

class Shopay_Product_Filterby_Category extends WP_Widget {

	/**
     * Register widget with WordPress.
     */
    public function __construct() {
        $widget_ops = array( 
            'classname'                     => 'shopay-widget shopay_product_filterby_category',
            'description'                   => __( 'Display products with categories that can be filtered.', 'shopay' ),
            'customize_selective_refresh'   => true,
        );
        parent::__construct( 'shopay_product_filterby_category', __( 'MT: Product FilterBy Category', 'shopay' ), $widget_ops );
    }

    /**
     * Helper function that holds widget fields
     * Array is used in update and form functions
     */
    private function widget_fields(){
        $shopay_categories_lists = shopay_categories_lists();
        $fields = array(
            'section_title' => array(
                'shopay_widgets_name'         => 'section_title',
                'shopay_widgets_title'        => __( 'Section Title', 'shopay' ),
                'shopay_widgets_default'      => __( 'Discover More Products', 'shopay' ),
                'shopay_widgets_field_type'   => 'text'
            ),

            'section_desc' => array(
                'shopay_widgets_name'         => 'section_desc',
                'shopay_widgets_title'        => __( 'Section Description', 'shopay' ),
                'shopay_widgets_default'      => __( 'Includes digital, womens and mens products', 'shopay' ),
                'shopay_widgets_field_type'   => 'text'
            ),

            'section_cat_slugs' => array(
                'shopay_widgets_name'         => 'section_cat_slugs',
                'shopay_widgets_title'        => __( 'Section Categories', 'shopay' ),
                'shopay_widgets_field_type'   => 'multicheckboxes',
                'shopay_widgets_field_options' => $shopay_categories_lists
            ),
            
            'pfc_view_more_txt' => array(
                'shopay_widgets_name'         => 'pfc_view_more_txt',
                'shopay_widgets_title'        => __( 'View More Text', 'shopay' ),
                'shopay_widgets_default'      => __( 'More Products', 'shopay' ),
                'shopay_widgets_field_type'   => 'text'
            ),

            'pfc_view_more_link' => array(
                'shopay_widgets_name'         => 'pfc_view_more_link',
                'shopay_widgets_title'        => __( 'View More Link', 'shopay' ),
                'shopay_widgets_field_type'   => 'text'
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

        $section_title      = empty( $instance['section_title'] ) ? '' : $instance['section_title'];
        $section_desc       = empty( $instance['section_desc'] ) ? '' : $instance['section_desc'];
        $section_cat_slugs  = empty( $instance['section_cat_slugs'] ) ? '' : $instance['section_cat_slugs'];
        $pfc_view_more_txt  = empty( $instance['pfc_view_more_txt'] ) ? '' : $instance['pfc_view_more_txt'];
        $pfc_view_more_link = empty( $instance['pfc_view_more_link'] ) ? '' : $instance['pfc_view_more_link'];

        echo $before_widget;
?>
<div class="mt-container">
    <div class="shopay-pfc-section">
        <div class="section-title-wrapper ">
            <?php
                if ( !empty( $section_title ) ) {
                    echo '<h3 class="section-title cover-font">'.esc_html( $section_title ).'</h3>';
                }
                if ( !empty( $section_desc ) ) {
                    echo '<p class="section-desc medium-font">'.esc_html( $section_desc ).'</p>';
                }
            ?>
        </div><!-- .section-title-wrapper -->
        <?php
        if ( !empty( $section_cat_slugs ) ) {
        ?>
        <div class="pfc-wrap">
        <?php
        echo '<ul class="category-titles-wrap">';
            foreach ( $section_cat_slugs as $section_cat_slug_title => $cat_value ) {
        ?>
                <li class="cat-title" data-filter=".product_cat-<?php echo esc_html( $section_cat_slug_title ); ?>"><h3 class="medium-font"><a><?php echo esc_html( $section_cat_slug_title ); ?></a></h3></li>
        <?php
            }
        echo'</ul><!-- .category-titles-wrap --> ';  
        ?>
            <ul class="pfc-products-wrap">
                <?php shopay_pfc_sec(); ?>
            </ul><!-- .pfc-products-wrap -->
            <?php
                if ( !empty( $pfc_view_more_link ) ) {
                    echo '<button><a href="'.esc_url( $pfc_view_more_link ).'">'.esc_html( $pfc_view_more_txt ).'</a></button>';
                }
            ?>
        </div><!-- .pfc-wrap -->
    <?php
        }
    ?>
    </div><!-- .shopay-pfc-section -->
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