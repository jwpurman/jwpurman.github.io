<?php
if (! class_exists ( 'WC_Stripe_Rest_Controller' )) {
	return;
}
/**
 *
 * @author PaymentPlugins
 * @since 3.0.0
 * @package Stripe/Controllers
 *         
 */
class WC_Stripe_Controller_Checkout extends WC_Stripe_Rest_Controller {

	protected $namespace = '';

	private $order_review = false;

	/**
	 *
	 * @var WC_Payment_Gateway_Stripe
	 */
	private $gateway = null;

	public function register_routes() {
		register_rest_route ( $this->rest_uri (), 'checkout', [ 
				'methods' => WP_REST_Server::CREATABLE, 
				'callback' => [ $this, 
						'process_checkout' 
				] 
		] );
	}

	/**
	 * Process the WC Order
	 *
	 * @param WP_REST_Request $request        	
	 */
	public function process_checkout($request) {
		wc_maybe_define_constant ( 'DOING_AJAX', true );
		$this->actions ();
		$checkout = WC ()->checkout ();
		$payment_method = $request->get_param ( 'payment_method' );
		/**
		 *
		 * @var WC_Payment_Gateway_Stripe $gateway
		 */
		$this->gateway = $gateway = WC ()->payment_gateways ()->payment_gateways ()[ $payment_method ];
		
		$this->required_post_data ();
		
		$this->order_review = ! is_null ( $request->get_param ( 'order_review' ) ) && 'true' === $request->get_param ( 'order_review' );
		
		try {
			do_action ( 'wc_stripe_rest_process_checkout', $request, $gateway );
			if (! is_user_logged_in ()) {
				$this->create_customer ( $request );
			}
			// set the checkout nonce so no exceptions are thrown.
			$_REQUEST[ '_wpnonce' ] = $_POST[ '_wpnonce' ] = wp_create_nonce ( 'woocommerce-process_checkout' );
			
			if ('product' == $request->get_param ( 'page_id' )) {
				wc_stripe_stash_cart ( WC ()->cart );
				$gateway->set_post_payment_process ( [ 
						$this, 'post_payment_processes' 
				] );
				add_filter ( 'woocommerce_get_checkout_order_received_url', function ($url, $order) {
					return add_query_arg ( 'wc_stripe_product_checkout', $order->get_payment_method (), $url );
				}, 10, 2 );
			}
			$checkout->process_checkout ();
		} catch ( Exception $e ) {
			wc_add_notice ( $e->getMessage (), 'error' );
		}
		if (wc_notice_count ( 'error' ) > 0) {
			return $this->send_response ( false );
		}
		return $this->send_response ( true );
	}

	/**
	 *
	 * @param WP_REST_Request $request        	
	 */
	private function create_customer($request) {
		$create = WC ()->checkout ()->is_registration_required ();
		// create an account for the user if it's required for things like subscriptions.
		if (wcs_stripe_active () && WC_Subscriptions_Cart::cart_contains_subscription ()) {
			$create = true;
		}
		if ($create) {
			$password = wp_generate_password ();
			$username = $email = $request->get_param ( 'billing_email' );
			$result = wc_create_new_customer ( $email, $username, $password );
			if ($result instanceof WP_Error) {
				// for email exists errors you want customer to either login or use a different email address.
				throw new Exception ( $result->get_error_message () );
			}
			$this->customer_id = $result;
			
			// log the customer in
			wp_set_current_user ( $this->customer_id );
			wc_set_customer_auth_cookie ( $this->customer_id );
			
			// As we are now logged in, cart will need to refresh to receive updated nonces
			WC ()->session->set ( 'reload_checkout', true );
		}
	}

	private function send_response($success) {
		$reload = WC ()->session->get ( 'reload_checkout', false );
		$data = array( 
				'result' => $success ? 'success' : 'failure', 
				'messages' => $reload ? null : $this->get_error_messages (), 
				'reload' => $reload 
		);
		unset ( WC ()->session->reload_checkout );
		return rest_ensure_response ( $data );
	}

	public function validate_payment_method($payment_method) {
		$gateways = WC ()->payment_gateways ()->payment_gateways ();
		return isset ( $gateways[ $payment_method ] ) ? true : new WP_Error ( 'validation-error', 'Please choose a valid payment method.' );
	}

	private function actions() {
		add_action ( 'woocommerce_after_checkout_validation', array( 
				$this, 'after_checkout_validation' 
		), 10, 2 );
		add_action ( 'woocommerce_checkout_order_processed', array( 
				$this, 'checkout_order_processed' 
		), 10, 3 );
	}

	/**
	 *
	 * @param WC_Order $order        	
	 * @param WC_Payment_Gateway_Stripe $gateway        	
	 */
	public function set_stashed_cart($order, $gateway) {
		wc_stripe_restore_cart ( WC ()->cart );
	}

	/**
	 *
	 * @param array $data        	
	 * @param WP_Error $errors        	
	 */
	public function after_checkout_validation($data, $errors) {
		if ($errors->get_error_message ( 'required-field' )) {
			wc_add_notice ( apply_filters ( 'wc_stripe_after_checkout_validation_notice', sprintf ( __ ( 'Some required fields were missing. Please click %shere%s to complete your payment.', 'woo-stripe-payment' ), '<a href="' . $this->get_order_review_url () . '">', '</a>' ), $data, $errors ), 'notice' );
			wp_send_json ( array( 'result' => 'failure', 
					'messages' => $this->get_messages ( 'notice' ), 
					'reload' => false 
			), 200 );
		}
	}

	private function required_post_data() {
		if (WC ()->cart->needs_shipping ()) {
			$_POST[ 'ship_to_different_address' ] = true;
		}
		if (wc_get_page_id ( 'terms' ) > 0) {
			$_POST[ 'terms' ] = 1;
		}
	}

	private function get_order_review_url() {
		return add_query_arg ( array( 
				'order_review' => 'true', 
				'payment_method' => $this->gateway->id, 
				'payment_nonce' => $this->gateway->get_payment_source () 
		), wc_get_checkout_url () );
	}

	/**
	 *
	 * @param int $order_id        	
	 * @param array $posted_data        	
	 * @param WC_Order $order        	
	 */
	public function checkout_order_processed($order_id, $posted_data, $order) {
		if ($this->order_review) {
			wc_add_notice ( __ ( 'Please review your order details then click Place Order.', 'woo-stripe-payment' ), 'notice' );
			wp_send_json ( array( 'result' => 'success', 
					'redirect' => $this->get_order_review_url () 
			), 200 );
		}
	}

	public function post_payment_processes($order, $gateway) {
		wc_stripe_restore_cart ( WC ()->cart );
		$data = WC ()->session->get ( 'wc_stripe_cart', [] );
		unset ( $data[ 'product_cart' ] );
		WC ()->session->set ( 'wc_stripe_cart', $data );
	}
}