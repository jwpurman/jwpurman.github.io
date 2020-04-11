<?php
if (! class_exists ( 'WC_Payment_Gateway_Stripe_Local_Payment' )) {
	return;
}
/**
 *
 * @package Stripe/Gateways
 * @author PaymentPlugins
 *        
 */
class WC_Payment_Gateway_Stripe_Alipay extends WC_Payment_Gateway_Stripe_Local_Payment {
	
	use WC_Stripe_Local_Payment_Charge_Trait;

	public function __construct() {
		$this->local_payment_type = 'alipay';
		$this->currencies = [ 'AUD', 'CAD', 'EUR', 
				'GBP', 'HKD', 'JPY', 'SGD', 'USD' 
		];
		// $this->countries = [ 'CN'
		// ];
		$this->id = 'stripe_alipay';
		$this->tab_title = __ ( 'Alipay', 'woo-stripe-payment' );
		$this->template_name = 'local-payment.php';
		$this->token_type = 'Stripe_Local';
		$this->method_title = __ ( 'Alipay', 'woo-stripe-payment' );
		$this->method_description = __ ( 'Alipay gateway that integrates with your Stripe account.', 'woo-stripe-payment' );
		$this->icon = wc_stripe ()->assets_url ( 'img/alipay.svg' );
		$this->order_button_text = __ ( 'Alipay', 'woo-stripe-payment' );
		parent::__construct ();
	}
}