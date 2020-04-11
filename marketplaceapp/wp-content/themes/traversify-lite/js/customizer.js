/**
 * File customizer.js.
 *
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $ ) {

	// Site title and description.
	wp.customize( 'blogname', function( value ) {
		value.bind( function( to ) {
			$( '.site-title a' ).text( to );
		} );
	} );
	wp.customize( 'blogdescription', function( value ) {
		value.bind( function( to ) {
			$( '.site-description' ).text( to );
		} );
	} );

	// Header text color.
	wp.customize( 'header_textcolor', function( value ) {
		value.bind( function( to ) {
			if ( 'blank' === to ) {
				$( '.site-title, .site-description' ).css( {
					'clip': 'rect(1px, 1px, 1px, 1px)',
					'position': 'absolute'
				} );
			} else {
				$( '.site-title, .site-description' ).css( {
					'clip': 'auto',
					'position': 'relative'
				} );
				$( '.site-title a, .site-description' ).css( {
					'color': to
				} );
			}
		} );
	} );
} )( jQuery );

jQuery( document ).ready(function() {
	 jQuery(document).on('click', '#customize-control-traversify_lite_theme_options-traversify_lite_product_cat_lists select>option', function(e) {

            if(jQuery(this).hasClass('cat_selected')){
                jQuery(this).removeClass('cat_selected');
            }
            else {
                jQuery(this).addClass('cat_selected');
            }
            var last_valid_selection = null;
            jQuery('#customize-control-traversify_lite_theme_options-traversify_lite_product_cat_lists select').change(function(event) {
                if (jQuery(this).val().length > 4) {
                    alert('Please select up to four categories only');
                    jQuery(this).val(last_valid_selection);
                    jQuery(this).find('option').removeAttr('selected');
                } else {
                    last_valid_selection = jQuery(this).val();
                }
            });
        });

});


// added for pro upsell
wp.customize.sectionConstructor['traversify-customize-pro'] = wp.customize.Section.extend( {

    // No events for this type of section.
    attachEvents: function () {},

    // Always make the section active.
    isContextuallyActive: function () {
        return true;
    }
} );