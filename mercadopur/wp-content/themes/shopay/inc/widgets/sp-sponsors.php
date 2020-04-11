<?php
/**
 * Widget for display sponsors section
 *
 * @package Shopay
 */

class Shopay_sponsors extends WP_Widget {

	/**
     * Register widget with WordPress.
     */
    public function __construct() {
        $widget_ops = array( 
            'classname'                     => 'shopay-widget shopay_sponsors',
            'description'                   => __( 'Display sponsors images.', 'shopay' ),
            'customize_selective_refresh'   => true,
        );
        parent::__construct( 'shopay_sponsors', __( 'MT: Sponsors', 'shopay' ), $widget_ops );
    }

    /**
     * Helper function that holds widget fields
     * Array is used in update and form functions
     */
    private function widget_fields() {
        $fields = array(
            'section_title' => array(
                'shopay_widgets_name'         => 'section_title',
                'shopay_widgets_title'        => __( 'Section Title', 'shopay' ),
                'shopay_widgets_default'      => __( 'Sponsors', 'shopay' ),
                'shopay_widgets_field_type'   => 'text'
            ),

            'section_info' => array(
                'shopay_widgets_name'         => 'section_info',
                'shopay_widgets_title'        => __( 'Info', 'shopay' ),
                'shopay_widgets_description'  => sprintf( __( 'Sponsors items are managed from %1$s customizer panel %2$s.', 'shopay' ), '<a href="'. esc_url( admin_url( '/customize.php?autofocus[section]=shopay_section_home_sponsors' ) ) .'">', '</a>' ),
                'shopay_widgets_field_type'   => 'info'
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

        $section_title = empty( $instance['section_title'] ) ? '' : $instance['section_title'];

        $get_shopay_sponsors = get_theme_mod( 'shopay_sponsors_items', '' );
        $get_decode_shopay_sponsors = json_decode( $get_shopay_sponsors );
        if ( empty( $get_decode_shopay_sponsors ) ) {
            return;
        }

        echo $before_widget;
?>
<div class="mt-container">
    <div class="shopay-sponsors-section">
        <div class="section-title-wrapper">        
            <?php
                if ( !empty( $section_title ) ){
                    echo '<h3 class="section-title">'.esc_html( $section_title ).'</h3>';
                }
            ?>
        </div><!-- .section-title-wrapper -->

        <div class="sponsors-wrap clearfix">
            <?php 
                foreach ( $get_decode_shopay_sponsors as $get_decode_shopay_sponser ) {
                    $sponser_image = $get_decode_shopay_sponser->mt_item_upload;
            ?>
                <figure class="sponser wow fadeInUp">
                    <img src="<?php echo esc_url( $sponser_image ); ?>" />
                </figure>
            <?php
                }
            ?>
        </div><!-- .sponsors-wrap -->
    </div><!-- .shopay-sponsors-section -->
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