<?php
/**
 * 
 * @author Payment Plugins
 * @since 3.1.0
 * @package Stripe/Trait
 */
trait WC_Stripe_Payment_Intent_Trait{

	public function get_payment_object() {
		return new WC_Stripe_Payment_Intent ( $this, WC_Stripe_Gateway::load () );
	}

	public function get_payment_method_type() {
		return $this->payment_method_type;
	}

	/**
	 *
	 * @param WC_Order $order        	
	 */
	public function get_confirmation_method($order) {
		return 'manual';
	}

	/**
	 *
	 * @param \Stripe\PaymentIntent $secret        	
	 * @param WC_Order $order        	
	 */
	public function get_payment_intent_checkout_url($intent, $order) {
		// rand is used to generate some random entropy so that window hash events are triggered.
		return sprintf ( '#response=%s', base64_encode ( wp_json_encode ( [ 
				'client_secret' => $intent->client_secret, 
				'order_id' => $order->get_id (), 
				'time' => rand ( 0, 999999 ) 
		] ) ) );
	}
}
/**
 *
 * @author Payment Plugins
 * @since 3.1.0
 * @package Stripe/Trait
 */
trait WC_Stripe_Payment_Charge_Trait{

	public function get_payment_object() {
		return new WC_Stripe_Payment_Charge ( $this, WC_Stripe_Gateway::load () );
	}
}

/**
 *
 * @author Payment Plugins
 * @since 3.1.0
 * @package Stripe/Trait
 */
trait WC_Stripe_Local_Payment_Charge_Trait{
	use WC_Stripe_Payment_Charge_Trait;

	/**
	 *
	 * @param int $order_id        	
	 */
	public function process_payment($order_id) {
		$order = wc_get_order ( $order_id );
		if (! $this->processing_payment) {
			$source_id = $this->get_new_source_token ();
			if (! empty ( $source_id )) {
				// source was created client side.
				$source = $this->gateway->fetch_payment_source ( $source_id );
				
				// save the order ID to the session. Stripe doesn't allow the source object's
				// redirect url to be updated so we can't pass order_id in url params.
				WC ()->session->set ( 'wc_stripe_order_id', $order_id );
			} else {
				// create the source
				$source = $this->gateway->create_source ( $this->get_source_args ( $order ) );
			}
			
			if (is_wp_error ( $source )) {
				wc_add_notice ( sprintf ( __ ( 'Error creating payment source. Reason: %s', 'woo-stripe-payment' ), $source->get_error_message () ), 'error' );
				$order->update_status ( 'failed', sprintf ( __ ( 'Error creating payment source. Reason: %s', 'woo-stripe-payment' ), $source->get_error_message () ) );
				return $this->get_order_error ();
			}
			
			$order->update_meta_data ( '_stripe_source_id', $source->id );
			$order->update_meta_data ( WC_Stripe_Constants::MODE, wc_stripe_mode () );
			
			$order->save ();
			
			return [ 'result' => 'success', 
					'redirect' => $this->get_source_redirect_url ( $source, $order ) 
			];
		} else {
			return parent::process_payment ( $order_id );
		}
	}
}

/**
 *
 * @author Payment Plugins
 * @since 3.1.0
 * @package Stripe/Trait
 *         
 */
trait WC_Stripe_Local_Payment_Intent_Trait {
	
	use WC_Stripe_Payment_Intent_Trait;

	/**
	 *
	 * @param \Stripe\PaymentIntent $secret        	
	 * @param WC_Order $order        	
	 */
	public function get_payment_intent_checkout_url($intent, $order) {
		// rand is used to generate some random entropy so that window hash events are triggered.
		return sprintf ( '#response=%s', base64_encode ( wp_json_encode ( [ 
				'client_secret' => $intent->client_secret, 
				'return_url' => $this->get_local_payment_return_url ( $order ), 
				'time' => rand ( 0, 999999 ) 
		] ) ) );
	}

	/**
	 *
	 * @param WC_Order $order        	
	 */
	public function get_confirmation_method($order) {
		return 'automatic';
	}
}