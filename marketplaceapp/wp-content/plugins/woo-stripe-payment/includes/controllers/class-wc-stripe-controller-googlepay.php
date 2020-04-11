<?php
if (! class_exists ( 'WC_Stripe_Rest_Controller' )) {
	return;
}
/**
 *
 * @author PaymentPlugins
 * @package Stripe/Controllers
 */
class WC_Stripe_Controller_GooglePay extends WC_Stripe_Rest_Controller {
	
	use WC_Stripe_Controller_Cart_Trait;

	protected $namespace = 'googlepay/';

	/**
	 *
	 * @var WC_Payment_Gateway_Stripe
	 */
	private $gateway;

	/**
	 *
	 * @var string
	 */
	private $shipping_method_id;

	/**
	 *
	 * @var array
	 */
	private $shipping_methods = [];

	/**
	 *
	 * @var string
	 */
	private $reason_code;

	public function register_routes() {
		register_rest_route ( $this->rest_uri (), 'shipping-data', [ 
				'methods' => WP_REST_Server::CREATABLE, 
				'callback' => [ $this, 
						'update_shipping_data' 
				], 
				'args' => [ 
						'shipping_address' => [ 
								'required' => true 
						], 
						'shipping_methods' => [ 
								'required' => false 
						], 
						'shipping_method_id' => [ 
								'required' => true 
						] 
				] 
		] );
	}

	/**
	 * Update the WC shipping data from the Google payment sheet.
	 *
	 * @param WP_REST_Request $request        	
	 */
	public function update_shipping_data($request) {
		wc_maybe_define_constant ( 'WOOCOMMERCE_CART', true );
		$address = $request->get_param ( 'shipping_address' );
		$this->shipping_methods = $request->get_param ( 'shipping_methods' );
		$this->shipping_methods = null == $this->shipping_methods ? [] : $this->shipping_methods;
		$this->shipping_method_id = $request->get_param ( 'shipping_method_id' );
		
		$this->gateway = WC ()->payment_gateways ()->payment_gateways ()[ 'stripe_googlepay' ];
		
		$this->add_ready_to_calc_shipping ();
		
		try {
			if ('product' == $request->get_param ( 'page_id' )) {
				wc_stripe_stash_cart ( WC ()->cart );
			}
			wc_stripe_update_customer_location ( $address );
			
			wc_stripe_update_shipping_methods ( $this->shipping_methods );
			
			// update the WC cart with the new shipping options
			WC ()->cart->calculate_totals ();
			
			// if shipping address is not serviceable, throw an error.
			if (! wc_stripe_shipping_address_serviceable ()) {
				$this->reason_code = 'SHIPPING_ADDRESS_UNSERVICEABLE';
				throw new Exception ( __ ( 'Your shipping address is not serviceable.', 'woo-stripe-payment' ) );
			}
			
			$response = rest_ensure_response ( apply_filters ( 'wc_stripe_googlepay_paymentdata_response', [ 
					'data' => [ 
							'shipping_methods' => $this->get_shipping_methods (), 
							'paymentRequestUpdate' => $this->get_payment_response_data () 
					] 
			] ) );
			if ('product' == $request->get_param ( 'page_id' )) {
				wc_stripe_restore_cart ( WC ()->cart );
			}
			return $response;
		} catch ( Exception $e ) {
			return new WP_Error ( 'payment-data-error', $e->getMessage (), [ 
					'status' => 200, 
					'data' => [ 
							'error' => [ 
									'reason' => $this->reason_code, 
									'message' => $e->getMessage (), 
									'intent' => 'SHIPPING_ADDRESS' 
							] 
					] 
			] );
		}
	}

	/**
	 * Return a formatted array of response data required by the Google payment sheet.
	 */
	public function get_payment_response_data() {
		return [ 
				'newTransactionInfo' => [ 
						'currencyCode' => get_woocommerce_currency (), 
						'totalPriceStatus' => 'FINAL', 
						'totalPrice' => strval ( WC ()->cart->total ), 
						'displayItems' => $this->gateway->get_display_items (), 
						'totalPriceLabel' => __ ( 'Total', 'woo-stripe-payment' ) 
				], 
				'newShippingOptionParameters' => [ 
						'shippingOptions' => $this->gateway->get_shipping_methods (), 
						'defaultSelectedOptionId' => $this->get_default_shipping_method () 
				] 
		];
	}

	private function get_shipping_methods() {
		return WC ()->session->get ( 'chosen_shipping_methods', [] );
	}

	/**
	 * Returns a default shipping method based on the chosen shipping methods.
	 *
	 * @return string
	 */
	private function get_default_shipping_method() {
		$chosen_shipping_methods = WC ()->session->get ( 'chosen_shipping_methods', [] );
		if (! empty ( $chosen_shipping_methods )) {
			return sprintf ( '%s:%s', 0, current ( $chosen_shipping_methods ) );
		}
		return $this->shipping_method_id;
	}
}