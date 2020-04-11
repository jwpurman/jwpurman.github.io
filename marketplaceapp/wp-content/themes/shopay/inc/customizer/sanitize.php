<?php
/**
 * Shopay Theme Customizer Sanitize functions
 *
 * @package Shopay
 */

if ( ! function_exists( 'shopay_sanitize_checkbox' ) ) :

    /**
	 * Sanitize checkbox.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $checked Whether the checkbox is checked.
	 * @return bool Whether the checkbox is checked.
	 */
	function shopay_sanitize_checkbox( $checked ) {

		return ( ( isset( $checked ) && true === $checked ) ? true : false );

	}

endif;


if ( ! function_exists( 'shopay_sanitize_select' ) ) :
	/**
	 * Sanitize select.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed                $input The value to sanitize.
	 * @param WP_Customize_Setting $setting WP_Customize_Setting instance.
	 * @return mixed Sanitized value.
	 */
	function shopay_sanitize_select( $input, $setting ) {
		// Ensure input is a slug.
		$input = sanitize_key( $input );

		// Get list of choices from the control associated with the setting.
		$choices = $setting->manager->get_control( $setting->id )->choices;

		// If the input is a valid key, return it; otherwise, return the default.
		return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
	}
endif;

if ( ! function_exists( 'shopay_sanitize_repeater' ) ) :
    /**
     * Sanitize repeater value
     *
     * @since 1.0.0
     */
    function shopay_sanitize_repeater( $input, $setting ) {
		$input_decoded = json_decode( $input, true );
            
        if ( !empty( $input_decoded ) ) {
            foreach ( $input_decoded as $boxes => $box ) {
                foreach ( $box as $key => $value ) {
                    if ( $key == 'mt_select_pages' || $key == 'mt_select_field' ) {
                        $input_decoded[$boxes][$key] = sanitize_key( $value );
                    } elseif ( $key == 'url' || $key == 'mt_item_upload' || $key == 'mt_item_link' ) {
                        $input_decoded[$boxes][$key] = esc_url_raw( $value );
                    } else {
                        $input_decoded[$boxes][$key] = wp_kses_post( $value );
                    }
                }
            }
            return json_encode( $input_decoded );
        }
        
        return $input;
    }
endif;

if ( ! function_exists( 'shopay_sanitize_textarea_field' ) ) :

    /**
	 * Sanitize textarea field with simple html tag.
	 *
	 * @since 1.0.0
	 *
	 * @param string          $input The plain text or combination of html to sanitize.
	 * @return string/html
	 */
	function shopay_sanitize_textarea_field( $input ) {

		$input = wp_kses_post( $input );

		return $input;

	}

endif;
