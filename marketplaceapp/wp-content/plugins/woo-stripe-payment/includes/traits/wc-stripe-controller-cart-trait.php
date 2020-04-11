<?php
/**
 * @since 3.1.0
 * @author Payment Plugins
 *
 */
trait WC_Stripe_Controller_Cart_Trait{

	/**
	 * Method that hooks in to the woocommerce_cart_ready_to_calc_shipping filter.
	 * Purpose is to ensure
	 * true is returned so shipping packages are calculated. Some 3rd party plugins and themes return false
	 * if the current page is the cart because they don't want to display the shipping calculator.
	 *
	 * @since 3.1.0
	 */
	public function add_ready_to_calc_shipping() {
		add_filter ( 'woocommerce_cart_ready_to_calc_shipping', function ($show_shipping) {
			return true;
		}, 1000 );
	}
}