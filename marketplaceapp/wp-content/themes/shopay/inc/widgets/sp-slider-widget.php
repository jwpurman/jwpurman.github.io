<?php
/**
 * Widget for display slider section along with categories menu
 *
 * @package Shopay
 */

class Shopay_Slider extends WP_Widget {

	/**
     * Register widget with WordPress.
     */
    public function __construct() {
        $widget_ops = array( 
            'classname'                     => 'shopay-widget shopay_slider',
            'description'                   => __( 'Display posts from selected category as slider.', 'shopay' ),
            'customize_selective_refresh'   => true,
        );
        parent::__construct( 'shopay_slider', __( 'MT: Slider', 'shopay' ), $widget_ops );
    }

    /**
     * Helper function that holds widget fields
     * Array is used in update and form functions
     */
    private function widget_fields() {
        
        if ( shopay_is_active_woocommerce() ) {
            $slider_cat_type_arg = array(
                'default-cat'      => __( 'Default Category', 'shopay' ),
                'product-cat'      => __( 'Product Category', 'shopay' ),
            );
        } else {
            $slider_cat_type_arg = array(
                'default-cat'      => __( 'Default Category', 'shopay' ),
            );
        }

        $fields = array(
            'featured_products_sec_heading' => array(
                'shopay_widgets_name'         => 'featured_products_sec_heading',
                'shopay_widgets_title'        => __( 'Featured Products Section', 'shopay' ),
                'shopay_widgets_field_type'   => 'heading'
            ),

            'slider_featured_products_option'   => array(
                'shopay_widgets_name'           => 'slider_featured_products_option',
                'shopay_widgets_title'          => __( 'Show Featured Products', 'shopay' ),
                'shopay_widgets_description'    => __( 'Two latest featured products are listed.', 'shopay' ),
                'shopay_widgets_default'        => 'on',
                'shopay_widgets_field_type'     => 'switch',
                'shopay_widget_field_relation'  => array(
                    'values' => array(
                        'on' => array(
                            'show_fields'   => array(
                                'is-featured-title',
                            )
                        ),
                        'off' => array(
                            'hide_fields'   => array(
                                'is-featured-title',
                            )
                        )
                    )
                )
            ),

            'featured_sec_title' => array(
                'shopay_widgets_name'         => 'featured_sec_title',
                'shopay_widget_field_wrapper' => 'is-featured-title',
                'shopay_widgets_title'        => __( 'Title', 'shopay' ),
                'shopay_widgets_default'      => __( 'Featured Products', 'shopay' ),
                'shopay_widgets_field_type'   => 'text'
            ),

            'slider_heading' => array(
                'shopay_widgets_name'         => 'slider_heading',
                'shopay_widgets_title'        => __( 'Slider Section', 'shopay' ),
                'shopay_widgets_field_type'   => 'heading'
            ),

            'slider_cat_type' => array(
                'shopay_widgets_name'         => 'slider_cat_type',
                'shopay_widgets_title'        => __( 'Category type', 'shopay' ),
                'shopay_widgets_default'      => 'default-cat',
                'shopay_widgets_description'  => __( 'Choose the category type to display on slider.', 'shopay' ),
                'shopay_widgets_field_type'   => 'select',
                'shopay_widgets_field_options' => $slider_cat_type_arg,
                'shopay_widget_field_relation'  => array(
                    'values' => array(
                        'default-cat' => array(
                            'show_fields'   => array(
                                'slider-default-cat',
                            ),
                            'hide_fields'   => array(
                                'slider-product-cat',
                            ),
                        ),
                        'product-cat' => array(
                            'show_fields'   => array(
                                'slider-product-cat',
                            ),
                            'hide_fields'   => array(
                                'slider-default-cat',
                            ),
                        )
                    )
                ),
            ),

            'slider_default_cat_slug' => array(
                'shopay_widgets_name'         => 'slider_default_cat_slug',
                'shopay_widgets_title'        => __( 'Default Categories', 'shopay' ),
                'shopay_widgets_default'      => '',
                'shopay_widgets_field_type'   => 'category_dropdown',
                'shopay_widget_field_wrapper' => 'slider-default-cat'
            ),

            'slider_product_cat_slug' => array(
                'shopay_widgets_name'         => 'slider_product_cat_slug',
                'shopay_widgets_title'        => __( 'Product Categories', 'shopay' ),
                'shopay_widgets_default'      => '',
                'shopay_widgets_field_type'   => 'woo_category_dropdown',
                'shopay_widget_field_wrapper' => 'slider-product-cat'
            ),

            'slider_post_count' => array(
                'shopay_widgets_name'         => 'slider_post_count',
                'shopay_widgets_title'        => __( 'Post Count', 'shopay' ),
                'shopay_widgets_default'      => 2,
                'shopay_widgets_field_type'   => 'number'
            ),

            'slide_btn_text' => array(
                'shopay_widgets_name'         => 'slide_btn_text',
                'shopay_widgets_title'        => __( 'Slide button text', 'shopay' ),
                'shopay_widgets_default'      => __( 'Add to cart', 'shopay' ),
                'shopay_widgets_field_type'   => 'text'
            ),

            'slider_featured_heading' => array(
                'shopay_widgets_name'         => 'slider_featured_heading',
                'shopay_widgets_title'        => __( 'Featured Section', 'shopay' ),
                'shopay_widgets_field_type'   => 'heading'
            ),

            'slider_featured_section_option' => array(
                'shopay_widgets_name'         => 'slider_featured_section_option',
                'shopay_widgets_title'        => __( 'Show Featured Section', 'shopay' ),
                'shopay_widgets_description'  => __( 'Checked to show featured section under slider.', 'shopay' ),
                'shopay_widgets_default'      => 'on',
                'shopay_widgets_field_type'   => 'switch',
                'shopay_widget_field_relation'  => array(
                    'values' => array(
                        'on' => array(
                            'show_fields'   => array(
                                'is-featured-group',
                            ),
                        ),
                        'off' => array(
                            'hide_fields'   => array(
                                'is-featured-group',
                            ),
                        )
                    )
                )
            ),
        );
        for ( $i = 0; $i <= 1; $i++ ) {
            switch ( $i ) {
                case 1:
                    $field_id = 'featured_image_two';
                    $group_title = __( 'Featured Item 2', 'shopay' );
                    break;
                
                default:
                    $field_id = 'featured_image_one';
                    $group_title = __( 'Featured Item 1', 'shopay' );
                    break;
            }
            
            $fields[$field_id.'_group_start'] = array(
                'shopay_widgets_name'           => $field_id.'_group_start',
                'shopay_widget_field_wrapper'   => 'is-featured-group',
                'shopay_widgets_title'          => $group_title,
                'shopay_widgets_field_type'     => 'start_group'
            );
            $fields[$field_id] = array(
                'shopay_widgets_name'           => $field_id,
                'shopay_widgets_title'          => __( 'Featured Image', 'shopay' ),
                'shopay_widgets_default'        => '',
                'shopay_widgets_field_type'     => 'upload',
            );
            $fields[$field_id.'_link'] = array(
                'shopay_widgets_name'         => $field_id.'_link',
                'shopay_widgets_title'        => __( 'Image Link', 'shopay' ),
                'shopay_widgets_field_type'   => 'url'
            );
            $fields[$field_id.'_title'] = array(
                'shopay_widgets_name'         => $field_id.'_title',
                'shopay_widgets_title'        => __( 'Title', 'shopay' ),
                'shopay_widgets_default'      => __( 'Trending', 'shopay' ),
                'shopay_widgets_field_type'   => 'title'
            );
            $fields[$field_id.'_btn_txt'] = array(
                'shopay_widgets_name'         => $field_id.'_btn_txt',
                'shopay_widgets_title'        => __( 'Button Label', 'shopay' ),
                'shopay_widgets_default'      => '',
                'shopay_widgets_field_type'   => 'title'
            );
            $fields[$field_id.'_btn_link'] = array(
                'shopay_widgets_name'         => $field_id.'_btn_link',
                'shopay_widgets_title'        => __( 'Button Link', 'shopay' ),
                'shopay_widgets_field_type'   => 'url'
            );
            $fields[$field_id.'_group_end'] = array(
                'shopay_widgets_name'         => $field_id.'_group_end',
                'shopay_widgets_field_type'   => 'end_group'
            );
        }
        
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
        
        $slider_featured_products_option  = empty( $instance['slider_featured_products_option'] ) ? 'on' : $instance['slider_featured_products_option'];
        $featured_sec_title = empty( $instance['featured_sec_title'] ) ? '' : $instance['featured_sec_title'];

        $slider_cat_type    = empty( $instance['slider_cat_type'] ) ? '' : $instance['slider_cat_type'];
        $slider_default_cat_slug   = empty( $instance['slider_default_cat_slug'] ) ? '' : $instance['slider_default_cat_slug'];
        $slider_product_cat_slug   = empty( $instance['slider_product_cat_slug'] ) ? '' : $instance['slider_product_cat_slug'];
        $slider_post_count = empty( $instance['slider_post_count'] ) ? 2 : $instance['slider_post_count'];
        $slide_btn_text = empty( $instance['slide_btn_text'] ) ? '' : $instance['slide_btn_text'];

        $slider_featured_section_option  = empty( $instance['slider_featured_section_option'] ) ? 'on' : $instance['slider_featured_section_option'];
        $featured_image_one_link = empty( $instance['featured_image_one_link'] ) ? '' : $instance['featured_image_one_link'];
        $featured_image_one_title = empty( $instance['featured_image_one_title'] ) ? '' : $instance['featured_image_one_title'];
        $featured_image_one_btn_txt = empty( $instance['featured_image_one_btn_txt'] ) ? '' : $instance['featured_image_one_btn_txt'];
        $featured_image_one_btn_link = empty( $instance['featured_image_one_btn_link'] ) ? '' : $instance['featured_image_one_btn_link'];
        $featured_image_two = empty( $instance['featured_image_two'] ) ? '' : $instance['featured_image_two'];
        $featured_image_two_link = empty( $instance['featured_image_two_link'] ) ? '' : $instance['featured_image_two_link'];
        $featured_image_two_title = empty( $instance['featured_image_two_title'] ) ? '' : $instance['featured_image_two_title'];
        $featured_image_two_btn_txt = empty( $instance['featured_image_two_btn_txt'] ) ? '' : $instance['featured_image_two_btn_txt'];
        $featured_image_two_btn_link = empty( $instance['featured_image_two_btn_link'] ) ? '' : $instance['featured_image_two_btn_link'];

        $slider_class = '';
        if ( 'off' == $slider_featured_products_option ) {
            $slider_class .= 'no-featured-section';
        }

        if ( 'off' == $slider_featured_section_option ) {
            $slider_class .= ' no-featured-section';
        }
        echo $before_widget;
?>
<div class="mt-container">
    <div class="shopay-slider-section clearfix <?php echo esc_attr( $slider_class ); ?>">
    <?php if ( 'on' == $slider_featured_products_option && shopay_is_active_woocommerce() ) { ?>
            <div class="slider-featured-section slider-featured-left">
                <?php
                    if ( !empty( $featured_sec_title ) ) {
                        echo '<h3 class="section-title">'.esc_html( $featured_sec_title ).'</h3>';
                    }
                    $product_cat_args[ 'tax_query' ] = array(
                        array(
                            'taxonomy'      => 'product_visibility',
                            'field'         => 'name',
                            'post_status'   => 'publish',
                            'terms'         => 'featured',
                        ),
                    );
                    $product_cat_args[ 'posts_per_page' ] = (int)2;
                    $product_cat_query = new WP_Query( $product_cat_args );
                    if ( $product_cat_query -> have_posts() ) :
                        while ( $product_cat_query -> have_posts() ) : $product_cat_query -> the_post();
                            wc_get_template_part( 'content', 'product' );
                        endwhile;
                    endif;
                ?>
            </div><!-- .slider-featured-section -->
    <?php } ?>
        <div class="main-slider-section">
			<?php
                if ( 'product-cat' === $slider_cat_type ) {
                    $shopay_slider_args = array(
                        'post_type'      => 'product',
                        'product_cat'    => esc_attr( $slider_product_cat_slug ),
                        'posts_per_page' => absint( $slider_post_count )
                    );
                } else {
                    $shopay_slider_args = array(
                        'category_name'  => esc_attr( $slider_default_cat_slug ),
                        'posts_per_page' => absint( $slider_post_count )
                    );
                }

				$shopay_slider_query = new WP_Query( $shopay_slider_args );
				if ( $shopay_slider_query->have_posts() ) {
					echo '<ul class="mainSlider cS-hidden">';
					while ( $shopay_slider_query->have_posts() ) {
						$shopay_slider_query->the_post();
						if ( has_post_thumbnail() ) {
			?>
							<div class="single-slide shopay-bg-image cover-image" style="background-image:url( <?php echo esc_url( get_the_post_thumbnail_url() ); ?> )">
                                <div class="slide-content-wrap">
                                    <div class="slider-content">
                                        <h2 class="product-title"><?php the_title(); ?></h2>
                                        <?php if ( 'product-cat' != $slider_cat_type ) { ?>
    									    <div class="product-content"><?php the_excerpt(); ?></div>
                                        <?php } ?>
                                        <div class="product-btn"><a href="<?php the_permalink(); ?>"><?php echo esc_html( $slide_btn_text ); ?></a></div>
                                    </div><!-- slider-content -->
                                </div><!-- slider-content-wrap -->
							</div><!-- .single-slide -->
			<?php
						}
					}
					echo '</ul><!-- .mainSldier -->';
				}
                wp_reset_postdata();
                    
                if ( 'on' == $slider_featured_section_option ) {
			?>
                    <div class="slider-featured-section clearfix">
                        <?php
                            for ( $i = 0; $i <= 1; $i++ ) {
                                $field_id_prefix = ( $i == 0 ) ? 'featured_image_one' : 'featured_image_two';
                                $featured_image_url  = empty( $instance[$field_id_prefix] ) ? '' : $instance[$field_id_prefix];
                                $featured_image_link = empty( $instance[$field_id_prefix.'_link'] ) ? '' : $instance[$field_id_prefix.'_link'];
                                $featured_image_title = empty( $instance[$field_id_prefix.'_title'] ) ? '' : $instance[$field_id_prefix.'_title'];
                                $featured_image_btn_txt = empty( $instance[$field_id_prefix.'_btn_txt'] ) ? '' : $instance[$field_id_prefix.'_btn_txt'];
                                $featured_image_btn_link = empty( $instance[$field_id_prefix.'_btn_link'] ) ? '' : $instance[$field_id_prefix.'_btn_link'];

                                if ( !empty( $featured_image_url ) ) {
                                    echo '<div class="featured-section wow fadeInUp">';
                        ?>
                                    <a href="<?php echo esc_url( $featured_image_link ); ?>">
                                        <figure class="shopay-bg-image medium-image" style="background-image:url(<?php echo esc_url( $featured_image_url ); ?>)">
                                        </figure>
                                    </a>
                                    <div class="featured-image-content ">
                                        <div class="featured-image-content-one">
                                            <?php
                                                if ( !empty( $featured_image_title ) ) {
                                                    echo '<h2 class="post-title">'.wp_kses_post( $featured_image_title ).'</h2>';
                                                }

                                                if ( !empty( $featured_image_btn_link ) ) {
                                                    echo '<button><a href="'.esc_url( $featured_image_btn_link ).'">'.wp_kses_post( $featured_image_btn_txt ).'</a></button>';
                                                }
                                            ?>
                                        </div>
                                    </div>
                        <?php
                                    echo '</div><!-- .featured-section -->';
                                }
                            }

                        ?>
                    </div><!-- .slider-featured-section -->
            <?php
                }
            ?>
        </div><!-- .main-slider-section -->
    </div><!-- .shopay-slider-section -->
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