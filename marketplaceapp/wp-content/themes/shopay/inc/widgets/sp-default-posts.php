<?php
/**
 * Widget for display blog section
 *
 * @package Shopay
 */

class Shopay_Default_Posts extends WP_Widget {

	/**
     * Register widget with WordPress.
     */
    public function __construct() {
        $widget_ops = array( 
            'classname'                     => 'shopay-widget shopay_default_posts',
            'description'                   => __( 'Display default posts in grid view.', 'shopay' ),
            'customize_selective_refresh'   => true,
        );
        parent::__construct( 'shopay_default_posts', __( 'MT: Default Posts', 'shopay' ), $widget_ops );
    }

    /**
     * Helper function that holds widget fields
     * Array is used in update and form functions
     */
    private function widget_fields(){
        
        $fields = array(
            'section_title' => array(
                'shopay_widgets_name'         => 'section_title',
                'shopay_widgets_title'        => __( 'Section Title', 'shopay' ),
                'shopay_widgets_default'      => __( 'Recent Blogs', 'shopay' ),
                'shopay_widgets_field_type'   => 'text'
            ),

            'section_desc' => array(
                'shopay_widgets_name'         => 'section_desc',
                'shopay_widgets_title'        => __( 'Section Description', 'shopay' ),
                'shopay_widgets_default'      => __( 'Little stories', 'shopay' ),
                'shopay_widgets_field_type'   => 'text'
            ),

            'section_cat_slugs' => array(
                'shopay_widgets_name'         => 'section_cat_slugs',
                'shopay_widgets_title'        => __( 'Default Categories', 'shopay' ),
                'shopay_widgets_default'      => '',
                'shopay_widgets_field_type'   => 'category_dropdown'
            ),
            
            'section_view_more_txt' => array(
                'shopay_widgets_name'         => 'section_view_more_txt',
                'shopay_widgets_title'        => __( 'View More Text', 'shopay' ),
                'shopay_widgets_default'      => __( 'More Articles', 'shopay' ),
                'shopay_widgets_field_type'   => 'text'
            ),

            'section_view_more_link' => array(
                'shopay_widgets_name'         => 'section_view_more_link',
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

        $section_title          = empty( $instance['section_title'] ) ? '' : $instance['section_title'];
        $section_desc           = empty( $instance['section_desc'] ) ? '' : $instance['section_desc'];
        $section_cat_slugs      = empty( $instance['section_cat_slugs'] ) ? '' : $instance['section_cat_slugs'];
        $section_view_more_txt  = empty( $instance['section_view_more_txt'] ) ? '' : $instance['section_view_more_txt'];
        $section_view_more_link = empty( $instance['section_view_more_link'] ) ? '' : $instance['section_view_more_link'];

        echo $before_widget;
?>
<div class="mt-container">
    <div class="shopay-blog-section">
        <div class="section-title-wrapper">
            <?php
                if ( !empty( $section_title ) ) {
                    echo '<h3 class="section-title">'.esc_html( $section_title ).'</h3>';
                }
                if ( !empty( $section_desc ) ) {
                    echo '<p class="section-desc medium-font">'.esc_html( $section_desc ).'</p>';
                }
            ?>
        </div><!--section-title-wrapper -->

        <div class="blogs-wrap">
            <ul class="blog-posts-wrap clearfix">
                <?php
                    $dp_posts_args = array(
                        'post_type'     => 'post',
                        'category_name' => esc_html( $section_cat_slugs ),
                        'posts_per_page'=> 3,
                    );
                    $dp_posts_query = new WP_Query( $dp_posts_args );
                    if ( $dp_posts_query -> have_posts() ) :
                        while ( $dp_posts_query -> have_posts() ) : $dp_posts_query -> the_post();
                            get_template_part( '/template-parts/widgets/content', 'default-post' );
                        endwhile;
                    endif;
                    wp_reset_postdata();
                ?>
            </ul><!-- .blog-posts-wrap -->
            <?php
                if ( !empty( $section_view_more_link ) ) {
                    echo '<button><a href="'.esc_url( $section_view_more_link ).'">'.esc_html( $section_view_more_txt ).'</a></button>';
                }
            ?>
        </div><!-- .blogs-wrap -->
    </div><!-- .shopay-blog-section -->
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