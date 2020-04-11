<?php
/**
 * 
 * @author PaymentPlugins
 * @package Stripe/Classes
 *
 */
class WC_Stripe_Redirect_Handler {

	public static function init() {
		add_action ( 'template_redirect', [ __CLASS__, 
				'local_payment_redirect' 
		] );
		add_action ( 'get_header', [ __CLASS__, 
				'maybe_restore_cart' 
		], 100 );
	}

	/**
	 * Check if this request is for a local payment redirect.
	 */
	public static function local_payment_redirect() {
		// resource created server side.
		if (isset ( $_GET[ 'wc-stripe-local-gateway' ], $_GET[ '_payment_nonce' ], $_GET[ 'order_id' ] ) && wp_verify_nonce ( $_GET[ '_payment_nonce' ], 'local-payment-' . $_GET[ 'order_id' ] )) {
			self::process_redirect ( wc_clean ( absint ( $_GET[ 'order_id' ] ) ) );
		}
		// resource created client side.
		if (isset ( $_GET[ 'wc-stripe-local-gateway' ], $_GET[ '_payment_nonce' ] ) && wp_verify_nonce ( $_GET[ '_payment_nonce' ], 'local-payment' )) {
			self::process_redirect ( WC ()->session->get ( 'wc_stripe_order_id', 0 ) );
		}
	}

	/**
	 */
	public static function process_redirect($order_id) {
		$order = wc_get_order ( $order_id );
		/**
		 *
		 * @var WC_Payment_Gateway_Stripe_Local_Payment $payment_method
		 */
		$payment_method = WC ()->payment_gateways ()->payment_gateways ()[ $order->get_payment_method () ];
		
		// first do some validations on the source
		$stripe_gateway = WC_Stripe_Gateway::load ();
		
		if (isset ( $_GET[ 'source' ] )) {
			$result = $stripe_gateway->fetch_payment_source ( wc_clean ( $_GET[ 'source' ] ) );
		} else {
			$result = $stripe_gateway->fetch_payment_intent ( wc_clean ( $_GET[ 'payment_intent' ] ) );
		}
		if (is_wp_error ( $result )) {
			wc_add_notice ( sprintf ( __ ( 'Error retrieving payment source. Reason: %s', 'woo-stripe-payment' ), $result->get_error_message () ), 'error' );
			return;
		} else {
			if ('failed' === $result->status || 'requires_payment_method' == $result->status) {
				wc_add_notice ( __ ( 'Payment authorization failed. Please select another payment method.', 'woo-stripe-payment' ), 'error' );
				$order->update_status ( 'failed', __ ( 'Payment authorization failed.', 'woo-stripe-payment' ) );
				if ($result instanceof \Stripe\PaymentIntent) {
					$order->update_meta_data ( WC_Stripe_Constants::PAYMENT_INTENT, $result->jsonSerialize () );
				}
				return;
			} elseif ('chargeable' === $result->status) {
				if ($payment_method->has_order_lock ( $order )) {
					wp_safe_redirect ( $order->get_checkout_order_received_url () );
					exit ();
				}
				// source can be charged synchronously
				$payment_method->set_new_source_token ( $result->id );
				$payment_method->processing_payment = true;
				$payment_method->set_order_lock ( $order );
				wc_stripe_log_info ( 'processing from redirect handler.' );
				$result = $payment_method->process_payment ( $order_id );
				if ($result[ 'result' ] === 'success') {
					wp_safe_redirect ( $result[ 'redirect' ] );
					die ();
				}
			} elseif ('succeeded' == $result->status) {
				if ($payment_method->has_order_lock ( $order )) {
					wp_safe_redirect ( $order->get_checkout_order_received_url () );
					exit ();
				}
				$payment_method->set_order_lock ( $order );
				$result = $payment_method->process_payment ( $order_id );
				if ($result[ 'result' ] === 'success') {
					wp_safe_redirect ( $result[ 'redirect' ] );
					die ();
				}
			}
		}
		wp_safe_redirect ( $order->get_checkout_order_received_url () );
		exit ();
	}

	public static function maybe_restore_cart() {
		global $wp;
		if (isset ( $wp->query_vars[ 'order-received' ] ) && isset ( $_GET[ 'wc_stripe_product_checkout' ] )) {
			add_action ( 'woocommerce_cart_emptied', 'wc_stripe_restore_cart_after_product_checkout' );
		}
	}
}
WC_Stripe_Redirect_Handler::init ();