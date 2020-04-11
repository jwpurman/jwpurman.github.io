<?php
/**
 * @since 3.0.0
 * @package Stripe/Tokens
 * @author PaymentPlugins
 *
 */
class WC_Payment_Token_Stripe_Local extends WC_Payment_Token_Stripe {

	protected $type = 'Stripe_Local';

	protected $stripe_data = [ 'gateway_title' => '' 
	];

	public function details_to_props($details) {}

	public function set_gateway_title($value) {
		$this->set_prop ( 'gateway_title', $value );
	}

	public function get_gateway_title($context = 'view') {
		return $this->get_prop ( 'gateway_title', $context );
	}

	public function get_formats() {
		return [ 
				'gateway_title' => [ 
						'label' => __ ( 'Gateway Title', 'woo-stripe-payment' ), 
						'example' => 'P24', 
						'format' => '{gateway_title}' 
				] 
		];
	}
}