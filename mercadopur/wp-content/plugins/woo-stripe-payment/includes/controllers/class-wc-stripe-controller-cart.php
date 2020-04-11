<?php
/**
 * Controller class that perfors cart operations for client side requests.
 * @author PaymentPlugins
 * @package Stripe/Controllers
 *
 */
class WC_Stripe_Controller_Cart extends WC_Stripe_Rest_Controller {
	
	use WC_Stripe_Controller_Cart_Trait;

	protected $namespace = 'cart/';

	public function register_routes() {
		register_rest_route ( $this->rest_uri (), 'shipping-method', [ 
				'methods' => WP_REST_Server::CREATABLE, 
				'callback' => [ $this, 
						'update_shipping_method' 
				], 
				'args' => [ 
						'shipping_methods' => [ 
								'required' => true 
						], 
						'payment_method' => [ 
								'required' => true 
						] 
				] 
		] );
		register_rest_route ( $this->rest_uri (), 'shipping-address', [ 
				'methods' => WP_REST_Server::CREATABLE, 
				'callback' => [ $this, 
						'update_shipping_address' 
				], 
				'args' => [ 
						'address' => [ 
								'required' => true 
						], 
						'payment_method' => [ 
								'required' => true 
						] 
				] 
		] );
		register_rest_route ( $this->rest_uri (), 'add-to-cart', [ 
				'methods' => WP_REST_Server::CREATABLE, 
				'callback' => [ $this, 'add_to_cart' 
				], 
				'args' => [ 
						'product_id' => [ 
								'required' => true 
						], 
						'qty' => [ 'required' => true, 
								'validate_callback' => [ 
										$this, 
										'validate_quantity' 
								] 
						], 
						'payment_method' => [ 
								'required' => true 
						] 
				] 
		] );
		/**
		 *
		 * @since 3.0.6
		 */
		register_rest_route ( $this->rest_uri (), 'cart-calculation', [ 
				'methods' => WP_REST_Server::CREATABLE, 
				'callback' => [ $this, 
						'cart_calculation' 
				], 
				'args' => [ 
						'product_id' => [ 
								'required' => true 
						], 
						'qty' => [ 'required' => true, 
								'validate_callback' => [ 
										$this, 
										'validate_quantity' 
								] 
						], 
						'payment_method' => [ 
								'required' => true 
						] 
				] 
		] );
	}

	/**
	 *
	 * @param int $qty        	
	 * @param WP_REST_Request $request        	
	 */
	public function validate_quantity($qty, $request) {
		if ($qty == 0) {
			return $this->add_validation_error ( new WP_Error ( 'cart-error', __ ( 'Quantity must be greater than zero.', 'woo-stripe-payment' ) ) );
		}
		return true;
	}

	/**
	 * Update the shipping method chosen by the customer.
	 *
	 * @param WP_REST_Request $request        	
	 */
	public function update_shipping_method($request) {
		wc_maybe_define_constant ( 'WOOCOMMERCE_CART', true );
		$shipping_methods = $request->get_param ( 'shipping_methods' );
		$payment_method = $request->get_param ( 'payment_method' );
		/**
		 *
		 * @var WC_Payment_Gateway_Stripe $gateway
		 */
		$gateway = WC ()->payment_gateways ()->payment_gateways ()[ $payment_method ];
		
		wc_stripe_update_shipping_methods ( $shipping_methods );
		
		$this->add_ready_to_calc_shipping ();
		
		// if this request is coming from product page, stash cart and use product cart
		if ('product' == $request->get_param ( 'page_id' )) {
			wc_stripe_stash_cart ( WC ()->cart );
		} else {
			WC ()->cart->calculate_totals ();
		}
		
		$response = rest_ensure_response ( apply_filters ( 'wc_stripe_update_shipping_method_response', [ 
				'data' => $gateway->get_update_shipping_method_response ( [ 
						'newData' => [ 
								'status' => 'success', 
								'total' => [ 
										'amount' => wc_stripe_add_number_precision ( WC ()->cart->total ), 
										'label' => __ ( 'Total', 'woo-stripe-payment' ), 
										'pending' => false 
								], 
								'displayItems' => wc_stripe_get_display_items (), 
								'shippingOptions' => wc_stripe_get_shipping_options () 
						], 
						'shipping_methods' => WC ()->session->get ( 'chosen_shipping_methods', [] ) 
				] ) 
		] ) );
		if ('product' == $request->get_param ( 'page_id' )) {
			wc_stripe_restore_cart ( WC ()->cart );
		}
		return $response;
	}

	/**
	 *
	 * @param WP_REST_Request $request        	
	 */
	public function update_shipping_address($request) {
		wc_maybe_define_constant ( 'WOOCOMMERCE_CART', true );
		$address = $request->get_param ( 'address' );
		$payment_method = $request->get_param ( 'payment_method' );
		/**
		 *
		 * @var WC_Payment_Gateway_Stripe $gateway
		 */
		$gateway = WC ()->payment_gateways ()->payment_gateways ()[ $payment_method ];
		try {
			wc_stripe_update_customer_location ( $address );
			
			$this->add_ready_to_calc_shipping ();
			
			if ('product' == $request->get_param ( 'page_id' )) {
				wc_stripe_stash_cart ( WC ()->cart );
			} else {
				WC ()->cart->calculate_totals ();
			}
			
			if (! $this->has_shipping_methods ( WC ()->shipping ()->get_packages () )) {
				throw new Exception ( 'No valid shipping methods.' );
			}
			
			$response = rest_ensure_response ( apply_filters ( 'wc_stripe_update_shipping_method_response', [ 
					'data' => $gateway->get_update_shipping_address_response ( [ 
							'newData' => [ 
									'status' => 'success', 
									'total' => [ 
											'amount' => wc_stripe_add_number_precision ( WC ()->cart->total ), 
											'label' => __ ( 'Total', 'woo-stripe-payment' ), 
											'pending' => false 
									], 
									'displayItems' => wc_stripe_get_display_items (), 
									'shippingOptions' => wc_stripe_get_shipping_options () 
							] 
					] ) 
			] ) );
		} catch ( Exception $e ) {
			$response = new WP_Error ( 'address-error', $e->getMessage (), [ 
					'status' => 200, 
					'newData' => [ 
							'status' => 'invalid_shipping_address' 
					] 
			] );
		}
		if ('product' == $request->get_param ( 'page_id' )) {
			wc_stripe_restore_cart ( WC ()->cart );
		}
		return $response;
	}

	/**
	 *
	 * @param WP_REST_Request $request        	
	 */
	public function add_to_cart($request) {
		// WC 3.0.0 requires WOOCOMMERCE_CART to be defined.
		wc_maybe_define_constant ( 'WOOCOMMERCE_CART', true );
		$payment_method = $request->get_param ( 'payment_method' );
		/**
		 *
		 * @var WC_Payment_Gateway_Stripe $gateway
		 */
		$gateway = WC ()->payment_gateways ()->payment_gateways ()[ $payment_method ];
		
		$product_id = $request->get_param ( 'product_id' );
		$qty = $request->get_param ( 'qty' );
		$variation_id = $request->get_param ( 'variation_id' );
		
		// stash cart so clean calculation can be performed.
		wc_stripe_stash_cart ( WC ()->cart, false );
		
		if (WC ()->cart->add_to_cart ( $product_id, $qty, $variation_id ) == false) {
			return new WP_Error ( 'cart-error', $this->get_error_messages (), [ 
					'status' => 200 
			] );
		} else {
			// add to cart was successful. Send a new X-WP-Nonce since it will be different now that a WC session exists.
			rest_get_server ()->send_header ( 'X-WP-Nonce', wp_create_nonce ( 'wp_rest' ) );
			$response = rest_ensure_response ( apply_filters ( 'wc_stripe_add_to_cart_response', [ 
					'data' => $gateway->add_to_cart_response ( [ 
							'total' => WC ()->cart->total, 
							'subtotal' => WC ()->cart->subtotal, 
							'totalCents' => wc_stripe_add_number_precision ( WC ()->cart->total ), 
							'displayItems' => wc_stripe_get_display_items (), 
							'shippingOptions' => wc_stripe_get_shipping_options () 
					] ) 
			], $gateway, $request ) );
			// save the product cart so it can be used for shipping calculations etc.
			wc_stripe_stash_product_cart ( WC ()->cart );
			// put cart contents back to how they were before.
			wc_stripe_restore_cart ( WC ()->cart );
			return $response;
		}
	}

	/**
	 * Performs a cart calculation
	 *
	 * @param WP_REST_Request $request        	
	 */
	public function cart_calculation($request) {
		// WC 3.0.0 requires WOOCOMMERCE_CART to be defined.
		wc_maybe_define_constant ( 'WOOCOMMERCE_CART', true );
		$product_id = $request->get_param ( 'product_id' );
		$qty = $request->get_param ( 'qty' );
		$variation_id = $request->get_param ( 'variation_id' ) == null ? 0 : $request->get_param ( 'variation_id' );
		
		wc_stripe_stash_cart ( WC ()->cart, false );
		
		if (WC ()->cart->add_to_cart ( $product_id, $qty, $variation_id )) {
			$payment_method = $request->get_param ( 'payment_method' );
			/**
			 *
			 * @var WC_Payment_Gateway_Stripe $gateway
			 */
			$gateway = WC ()->payment_gateways ()->payment_gateways ()[ $payment_method ];
			$response = rest_ensure_response ( apply_filters ( 'wc_stripe_add_to_cart_response', [ 
					'data' => $gateway->add_to_cart_response ( [ 
							'total' => WC ()->cart->total, 
							'subtotal' => WC ()->cart->subtotal, 
							'totalCents' => wc_stripe_add_number_precision ( WC ()->cart->total ), 
							'displayItems' => wc_stripe_get_display_items (), 
							'shippingOptions' => wc_stripe_get_shipping_options () 
					] ) 
			], $gateway, $request ) );
		} else {
			$response = new WP_Error ( 'cart-error', $this->get_error_messages (), [ 
					'status' => 200 
			] );
		}
		wc_stripe_stash_product_cart ( WC ()->cart );
		wc_stripe_restore_cart ( WC ()->cart );
		wc_clear_notices ();
		// add to cart was successful. Send a new X-WP-Nonce since it will be different now that a WC session exists.
		rest_get_server ()->send_header ( 'X-WP-Nonce', wp_create_nonce ( 'wp_rest' ) );
		return $response;
	}

	protected function get_error_messages() {
		return $this->get_messages ( 'error' );
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see WC_Stripe_Rest_Controller::get_messages()
	 */
	protected function get_messages($types = 'all') {
		$notices = wc_get_notices ();
		$message = '';
		if ($types !== 'all') {
			$types = ( array ) $types;
			foreach ( $notices as $type => $notice ) {
				if (! in_array ( $type, $types )) {
					unset ( $notices[ $type ] );
				}
			}
		}
		foreach ( $notices as $notice ) {
			$message .= sprintf ( ' %s', $notice );
		}
		
		return trim ( $message );
	}

	/**
	 * Return true if the provided packages have shipping methods.
	 *
	 * @param array $packages        	
	 */
	private function has_shipping_methods($packages) {
		foreach ( $packages as $i => $package ) {
			if (! empty ( $package[ 'rates' ] )) {
				return true;
			}
		}
		return false;
	}
}