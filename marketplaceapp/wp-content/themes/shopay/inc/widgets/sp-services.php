<?php
/**
 * Widget for display services section
 *
 * @package Shopay
 */

class Shopay_Services extends WP_Widget {

	/**
     * Register widget with WordPress.
     */
    public function __construct() {
        $widget_ops = array( 
            'classname'                     => 'shopay-widget shopay_services',
            'description'                   => __( 'Display services images.', 'shopay' ),
            'customize_selective_refresh'   => true,
        );
        parent::__construct( 'shopay_services', __( 'MT: Services', 'shopay' ), $widget_ops );
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
                'shopay_widgets_default'      => __( 'Services', 'shopay' ),
                'shopay_widgets_field_type'   => 'text'
            ),

            'section_info' => array(
                'shopay_widgets_name'         => 'section_info',
                'shopay_widgets_title'        => __( 'Info', 'shopay' ),
                'shopay_widgets_description'  => sprintf( __( 'Services items are managed from %1$s customizer panel %2$s.', 'shopay' ), '<a href="'. esc_url( admin_url( '/customize.php?autofocus[section]=shopay_section_home_services' ) ) .'">', '</a>' ),
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

        $section_title          = empty( $instance['section_title'] ) ? '' : $instance['section_title'];
        $get_shopay_services    = get_theme_mod( 'shopay_services_items', '' );
        $get_decode_shopay_services = json_decode( $get_shopay_services );

        if ( empty( $get_decode_shopay_services ) ) {
            return;
        }

        echo $before_widget;
?>
<div class="mt-container">
    <div class="shopay-services-section">
        <div class="section-title-wrapper">
            <?php
                if ( !empty( $section_title ) ) {
                    echo '<h3 class="section-title">'.esc_html( $section_title ).'</h3>';
                }
            ?>     
        </div><!-- .section-title-wrapper -->
        <div class="services-wrapper">
            <ul class="services-wrap clearfix">
                <?php 
                    foreach ( $get_decode_shopay_services as $get_decode_shopay_service ) {
                        $service_icon = $get_decode_shopay_service->mt_item_icon;
                        $service_desc = $get_decode_shopay_service->mt_item_desc;
                ?>
                        <li class="services-item clearfix wow fadeInUp">
                            <i class="<?php echo esc_attr( $service_icon ); ?>"></i>
                            <span><?php echo esc_html( $service_desc ); ?></span>
                        </li>
                <?php
                    }
                ?>
            </ul><!-- .services-wrap -->
        </div><!-- services-wrapper -->
    </div><!-- .shopay-services-section -->
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