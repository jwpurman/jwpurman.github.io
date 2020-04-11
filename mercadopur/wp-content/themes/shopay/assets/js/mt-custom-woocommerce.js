jQuery(document).ready(function($) {

    "use strict";  

	/**
    * Update wishlist on header
    *  
    */ 
   	$(document).on( 'added_to_wishlist removed_from_wishlist', function(){
		var counter = $('.shopay-whishlist .shopay-wl-counter');
		
		$.ajax({
			url: yith_wcwl_l10n.ajax_url,
			data: {
			action: 'yith_wcwl_update_wishlist_count'
			},
			dataType: 'json',
			success: function( data ){
				counter.html( data.count );
			},
			beforeSend: function(){
				counter.block();
			},
			complete: function(){
				counter.unblock();
			}
		})
	} );

	$( '.product .product-thumbnail-wrap .yith-wcqv-button' ).html( '<i class="fas fa-search"></i>' );
	$( '.single-product .related.products h2' ).addClass( 'widget-title' );
});	