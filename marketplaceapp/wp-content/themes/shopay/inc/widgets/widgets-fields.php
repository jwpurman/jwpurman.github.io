<?php
/**
 * Define custom fields for widgets
 * 
 * @package Shopay
 */

function shopay_widgets_show_widget_field( $instance = '', $widget_field = '', $shopay_widget_field_value = '' ) {
    
    extract( $widget_field );
    $shopay_widget_field_wrapper  = isset( $shopay_widget_field_wrapper ) ? $shopay_widget_field_wrapper : '';
    $shopay_widget_field_relation = isset( $shopay_widget_field_relation ) ? $shopay_widget_field_relation : array();
    $shopay_widget_relation_json  = wp_json_encode( $shopay_widget_field_relation );
    $shopay_widget_relation_class = ( $shopay_widget_field_relation ) ? 'shopay_widget_field_relation' : '';


    switch ( $shopay_widgets_field_type ){

        /**
         * heading
         */
        case 'heading':
        ?>
                <h4 class="field-heading mt-widget-field-wrapper <?php echo esc_attr( $shopay_widget_field_wrapper ); ?>"><span class="field-label"><strong><?php echo esc_html( $shopay_widgets_title ); ?></strong></span></h4>
        <?php
            break;

        /**
         * Group Start
         */
        case 'start_group':
        ?>
            <div class="mt-group-wrapper mt-widget-field-wrapper <?php echo esc_attr( $shopay_widget_field_wrapper ); ?>">
                <span class="group-title"><h4><?php echo esc_html( $shopay_widgets_title ); ?></h4></span>
                <div class="group-fields-wrapper">
        <?php
            break;

        /**
         * Group end
         */
        case 'end_group':
        ?>
                </div><!-- .group-fields-wrapper -->
            </div><!-- .mt-group-wrapper -->
        <?php
            break;

        /**
         * Info
         */
        case 'info':
        ?>
            <p>
                <div class="field-info-wrapper">
                    <span class="info-title"><strong><?php echo esc_html( $shopay_widgets_title ); ?></strong></span>
                    <span class="info-description"><?php echo wp_kses_post( $shopay_widgets_description ); ?></span>
                </div>
            </p>
        <?php
            break;

        /**
         * title field
         */
        case 'title' :
        ?>
            <p class="mt-widget-field-wrapper <?php echo esc_attr( $shopay_widget_field_wrapper ); ?>">
                <label for="<?php echo esc_attr( $instance->get_field_id( $shopay_widgets_name ) ); ?>"><?php echo esc_html( $shopay_widgets_title ); ?>:</label>
                <input class="widefat" id="<?php echo esc_attr( $instance->get_field_id( $shopay_widgets_name ) ); ?>" name="<?php echo esc_attr( $instance->get_field_name( $shopay_widgets_name ) ); ?>" type="text" value="<?php echo esc_html( $shopay_widget_field_value ); ?>" />

                <?php if ( isset( $shopay_widgets_description ) ) { ?>
                   <span class="field-description"><em><?php echo wp_kses_post( $shopay_widgets_description ); ?></em></span>
                <?php } ?>
            </p>
        <?php
            break;

        /**
         * text field
         */
        case 'text' :
        ?>
            <p class="mt-widget-field-wrapper <?php echo esc_attr( $shopay_widget_field_wrapper ); ?>">
                <label for="<?php echo esc_attr( $instance->get_field_id( $shopay_widgets_name ) ); ?>"><?php echo esc_html( $shopay_widgets_title ); ?>:</label>
                <input class="widefat" id="<?php echo esc_attr( $instance->get_field_id( $shopay_widgets_name ) ); ?>" name="<?php echo esc_attr( $instance->get_field_name( $shopay_widgets_name ) ); ?>" type="text" value="<?php echo esc_html( $shopay_widget_field_value ); ?>" />

                <?php if ( isset( $shopay_widgets_description ) ) { ?>
                   <span class="field-description"><em><?php echo wp_kses_post( $shopay_widgets_description ); ?></em></span>
                <?php } ?>
            </p>
        <?php
            break;

        /**
         * number field
         */
        case 'number' :
        ?>
            <p>
                <label for="<?php echo esc_attr( $instance->get_field_id( $shopay_widgets_name ) ); ?>"><?php echo esc_html( $shopay_widgets_title ); ?>:</label>
                <input name="<?php echo esc_attr( $instance->get_field_name( $shopay_widgets_name ) ); ?>" type="number" step="1" min="1" id="<?php echo esc_attr( $instance->get_field_id( $shopay_widgets_name ) ); ?>" value="<?php echo esc_html( $shopay_widget_field_value ); ?>" class="small-text" />

                <?php if ( isset( $shopay_widgets_description ) ) { ?>
                   <span class="field-description"><em><?php echo wp_kses_post( $shopay_widgets_description ); ?></em></span>
                <?php } ?>
            </p>
        <?php
            break;

        /**
         * url field
         */
        case 'url' :
        ?>
            <p>
                <label for="<?php echo esc_attr( $instance->get_field_id( $shopay_widgets_name ) ); ?>"><?php echo esc_html( $shopay_widgets_title ); ?>:</label>
                <input class="widefat" id="<?php echo esc_attr( $instance->get_field_id( $shopay_widgets_name ) ); ?>" name="<?php echo esc_attr( $instance->get_field_name( $shopay_widgets_name ) ); ?>" type="text" value="<?php echo esc_url( $shopay_widget_field_value ); ?>" />

                <?php if ( isset( $shopay_widgets_description ) ) { ?>
                   <span class="field-description"><em><?php echo wp_kses_post( $shopay_widgets_description ); ?></em></span>
                <?php } ?>
            </p>
        <?php
            break;

        /**
         * checkbox
         */
        case 'checkbox' :
        ?>
            <p class="mt-widget-field-wrapper <?php echo esc_attr( $shopay_widget_field_wrapper ); ?>">
                <input id="<?php echo esc_attr( $instance->get_field_id( $shopay_widgets_name ) ); ?>" name="<?php echo esc_attr( $instance->get_field_name( $shopay_widgets_name ) ); ?>" type="checkbox" value="1" class="widefat <?php echo esc_attr( $shopay_widget_relation_class ); ?>" data-relations="<?php echo esc_attr( $shopay_widget_relation_json ) ?>" <?php checked( '1', $shopay_widget_field_value ); ?>/>
                <label for="<?php echo esc_attr( $instance->get_field_id( $shopay_widgets_name ) ); ?>"><?php echo esc_html( $shopay_widgets_title ); ?></label>

                <?php if ( isset( $shopay_widgets_description ) ) { ?>
                   <span class="field-description"><em><?php echo wp_kses_post( $shopay_widgets_description ); ?></em></span>
                <?php } ?>
            </p>
        <?php
            break;

        /**
         * select field
         */
        case 'select' :
        ?>
            <p class="mt-widget-field-wrapper <?php echo esc_attr( $shopay_widget_field_wrapper ); ?>">
                <label for="<?php echo esc_attr( $instance->get_field_id( $shopay_widgets_name ) ); ?>"><?php echo esc_html( $shopay_widgets_title ); ?>:</label>
                <?php if ( isset( $shopay_widgets_description ) ) { ?>
                   <span class="field-description"><em><?php echo wp_kses_post( $shopay_widgets_description ); ?></em></span>
                <?php } ?>
                <select name="<?php echo esc_attr( $instance->get_field_name( $shopay_widgets_name ) ); ?>" id="<?php echo esc_attr( $instance->get_field_id( $shopay_widgets_name ) ); ?>" class="widefat <?php echo esc_attr( $shopay_widget_relation_class ); ?>" data-relations="<?php echo esc_attr( $shopay_widget_relation_json ) ?>">
                    <?php foreach ( $shopay_widgets_field_options as $select_option_name => $select_option_title ) { ?>
                        <option value="<?php echo esc_attr( $select_option_name ); ?>" id="<?php echo esc_attr( $instance->get_field_id( $select_option_name ) ); ?>" <?php selected( $select_option_name, $shopay_widget_field_value ); ?>><?php echo esc_html( $select_option_title ); ?></option>
                    <?php } ?>
                </select>
            </p>
        <?php 
            break;

        /**
         * category dropdown field
         */
        case 'category_dropdown' :
            $select_field = 'name="'. esc_attr( $instance->get_field_name( $shopay_widgets_name ) ) .'" id="'. esc_attr( $instance->get_field_id( $shopay_widgets_name ) ) .'" class="widefat"';
        ?>
                <p class="post-cat mt-widget-field-wrapper <?php echo esc_attr( $shopay_widget_field_wrapper ); ?>">
                    <label for="<?php echo esc_attr( $instance->get_field_id( $shopay_widgets_name ) ); ?>"><?php echo esc_html( $shopay_widgets_title ); ?>:</label>
                    <?php if ( isset( $shopay_widgets_description ) ) { ?>
                       <span class="field-description"><em><?php echo wp_kses_post( $shopay_widgets_description ); ?></em></span>
                    <?php }

                        $dropdown_args = wp_parse_args( array(
                            'taxonomy'          => 'category',
                            'show_option_none'  => __( '- - Select Category - -', 'shopay' ),
                            'selected'          => esc_attr( $shopay_widget_field_value ),
                            'show_option_all'   => '',
                            'orderby'           => 'id',
                            'order'             => 'ASC',
                            'show_count'        => 0,
                            'hide_empty'        => 1,
                            'child_of'          => 0,
                            'exclude'           => '',
                            'hierarchical'      => 1,
                            'depth'             => 0,
                            'tab_index'         => 0,
                            'hide_if_empty'     => false,
                            'option_none_value' => 0,
                            'value_field'       => 'slug',
                        ) );

                        $dropdown_args['echo'] = false;

                        $dropdown = wp_dropdown_categories( $dropdown_args );
                        $dropdown = str_replace( '<select', '<select ' . $select_field, $dropdown );
                        echo $dropdown;
                    ?>
                </p>
        <?php
            break;

        /**
         * woocommerce category dropdown field
         */
        case 'woo_category_dropdown' :
            if ( shopay_is_active_woocommerce() ) {
                $select_field = 'name="'. esc_attr( $instance->get_field_name( $shopay_widgets_name ) ) .'" id="'. esc_attr( $instance->get_field_id( $shopay_widgets_name ) ) .'" class="widefat"';
        ?>
                <p class="post-cat mt-widget-field-wrapper <?php echo esc_attr( $shopay_widget_field_wrapper ); ?>">
                    <label for="<?php echo esc_attr( $instance->get_field_id( $shopay_widgets_name ) ); ?>"><?php echo esc_html( $shopay_widgets_title ); ?>:</label>
                    <?php if ( isset( $shopay_widgets_description ) ) { ?>
                       <span class="field-description"><em><?php echo wp_kses_post( $shopay_widgets_description ); ?></em></span>
                    <?php }

                        $dropdown_args = wp_parse_args( array(
                            'taxonomy'          => 'product_cat',
                            'show_option_none'  => __( '- - Select Category - -', 'shopay' ),
                            'selected'          => esc_attr( $shopay_widget_field_value ),
                            'show_option_all'   => '',
                            'orderby'           => 'id',
                            'order'             => 'ASC',
                            'show_count'        => 0,
                            'hide_empty'        => 1,
                            'child_of'          => 0,
                            'exclude'           => '',
                            'hierarchical'      => 1,
                            'depth'             => 0,
                            'tab_index'         => 0,
                            'hide_if_empty'     => false,
                            'option_none_value' => 0,
                            'value_field'       => 'slug',
                        ) );

                        $dropdown_args['echo'] = false;

                        $dropdown = wp_dropdown_categories( $dropdown_args );
                        $dropdown = str_replace( '<select', '<select ' . $select_field, $dropdown );
                        echo $dropdown;
                    ?>
                </p>
        <?php
            }
            break;

        /**
         * upload widget field
         */
        case 'upload':
        $image = $image_class = "";
        if ( $shopay_widget_field_value ) { 
            $image          = '<img src="'.esc_url( $shopay_widget_field_value ).'" style="max-width:100%;"/>';
            $image_class    = ' hidden';
        }
        ?>
        <div class="attachment-media-view">

            <p>
                <span class="field-label"><label for="<?php echo esc_attr( $instance->get_field_id( $shopay_widgets_name ) ); ?>"><?php echo esc_html( $shopay_widgets_title ); ?>:</label></span>
            </p>
            
            <div class="placeholder<?php echo esc_attr( $image_class ); ?>">
                <?php esc_html_e( 'No image selected', 'shopay' ); ?>
            </div>
            <div class="thumbnail thumbnail-image">
                <?php echo $image; ?>
            </div>

            <div class="actions mt-clearfix">
                <button type="button" class="button mt-delete-button align-left"><?php esc_html_e( 'Remove', 'shopay' ); ?></button>
                <button type="button" class="button mt-upload-button alignright"><?php esc_html_e( 'Select Image', 'shopay' ); ?></button>
                
                <input name="<?php echo esc_attr( $instance->get_field_name( $shopay_widgets_name ) ); ?>" id="<?php echo esc_attr( $instance->get_field_id( $shopay_widgets_name ) ); ?>" class="upload-id" type="hidden" value="<?php echo esc_url( $shopay_widget_field_value ); ?>"/>
            </div>

            <?php if ( isset( $shopay_widgets_description ) ) { ?>
               <span class="field-description"><em><?php echo wp_kses_post( $shopay_widgets_description ); ?></em></span>
            <?php } ?>

        </div>
        <?php
            break;

        /**
         * multicheckboxes field
         */
        case 'multicheckboxes':
        ?>
            <label class="multicheckbox-label"><?php echo esc_html( $shopay_widgets_title ); ?>:</label>
            <ul class="mt-multiple-checkbox">

        <?php    
            foreach ( $shopay_widgets_field_options as $multi_option_name => $multi_option_title ) {
                if ( isset( $shopay_widget_field_value[$multi_option_name] ) ) {
                    $multi_option_status = 1;
                } else {
                    $multi_option_status = 0;
                }
        ?>
                <li>
                    <input id="<?php echo esc_attr( $instance->get_field_id( $multi_option_name ) ); ?>" name="<?php echo esc_attr( $instance->get_field_name( $shopay_widgets_name ).'['.$multi_option_name.']' ); ?>" type="checkbox" value="1" <?php checked( '1', $multi_option_status ); ?>/>
                    <label for="<?php echo esc_attr( $instance->get_field_id( $multi_option_name ) ); ?>"><?php echo esc_html( $multi_option_title ); ?></label>
                </li>
        <?php
            }
        ?>
            </ul>
            <?php if ( isset( $shopay_widgets_description ) ) { ?>
               <span class="field-description"><em><?php echo wp_kses_post( $shopay_widgets_description ); ?></em></span>
            <?php }
                break;

        case 'switch':
            if ( empty( $shopay_widget_field_value ) ) {
                $shopay_widget_field_value = $shopay_widgets_default;
            }
            $switch_class = ( 'on' == $shopay_widget_field_value ) ? 'switch-on' : '';
        ?>
            <p>
                <label for="<?php echo esc_attr( $instance->get_field_id( $shopay_widgets_name ) ); ?>"><?php echo esc_html( $shopay_widgets_title ); ?></label>
                <div class="mt-widget-switch mt-widget-field-wrapper <?php echo esc_attr( $switch_class ); ?>">
                    <div class="mt-switch-options">
                        <div class="mt-switch-active">
                            <span class="mt-switch-item"><?php esc_html_e( 'Yes', 'shopay' ); ?></span>
                        </div>
                        <div class="mt-switch-inactive">
                            <span class="mt-switch-item"><?php esc_html_e( 'No', 'shopay' ); ?></span>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="<?php echo esc_attr( $instance->get_field_id( $shopay_widgets_name ) ); ?>" class="widefat <?php echo esc_attr( $shopay_widget_relation_class ); ?>" data-relations="<?php echo esc_attr( $shopay_widget_relation_json ) ?>" name="<?php echo esc_attr( $instance->get_field_name( $shopay_widgets_name ) ); ?>" value="<?php echo esc_attr( $shopay_widget_field_value ); ?>" />
                <?php if ( isset( $shopay_widgets_description ) ) { ?>
                   <span class="field-description"><em><?php echo wp_kses_post( $shopay_widgets_description ); ?></em></span>
                <?php } ?>
            </p>
        <?php
            break;
    }
}

function shopay_widgets_updated_field_value( $widget_field, $new_field_value ) {

    extract( $widget_field );

    if ( $shopay_widgets_field_type == 'number') {
        return absint( $new_field_value );
    } elseif ( $shopay_widgets_field_type == 'textarea' || $shopay_widgets_field_type == 'title' ) {
        return wp_kses_post( $new_field_value );
    } elseif ( $shopay_widgets_field_type == 'url' ) {
        return esc_url_raw( $new_field_value );
    } elseif ( $shopay_widgets_field_type == 'multicheckboxes' ) {
        $multicheck_list = array();
        if ( is_array( $new_field_value ) ) {
            foreach ( $new_field_value as $key => $value ) {
                $multicheck_list[esc_attr( $key )] = esc_attr( $value );
            }
        }
        return $multicheck_list;
    } else {
        return sanitize_text_field( $new_field_value );
    }
}