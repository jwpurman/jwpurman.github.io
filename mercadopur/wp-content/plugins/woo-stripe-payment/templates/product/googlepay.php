<?php
/**
 * @version 3.0.9
 * 
 * @var WC_Payment_Gateway_Stripe_GooglePay $gateway
 */
?>
<div id="wc-stripe-googlepay-container">
	<input type="hidden" id="googlepay_display_items"
		data-items="<?php echo $gateway->get_display_items(true)?>" /> <input
		type="hidden" id="googlepay_shipping_options"
		data-items="<?php echo $gateway->get_shipping_methods(true)?>" />
</div>