<?php
if (! class_exists ( 'WC_Payment_Gateway_Stripe' )) {
	return;
}
/**
 * Gateways that use Charge API should extend this abstract class.
 *
 * @package Stripe/Abstract
 * @since 3.0.0
 * @author User
 *        
 */
abstract class WC_Payment_Gateway_Stripe_Charge extends WC_Payment_Gateway_Stripe {

	public function process_payment($order_id) {
		$order = wc_get_order ( $order_id );
		
		if ($this->is_change_payment_method_request ()) {
			return [ 'result' => 'success', 
					'redirect' => $order->get_view_order_url () 
			];
		}
		
		do_action ( 'wc_stripe_before_process_payment', $order, $this->id );
		
		if (wc_notice_count ( 'error' ) > 0) {
			return $this->order_error ();
		}
		$this->processing_payment = true;
		
		if ($this->order_contains_pre_order ( $order ) && $this->pre_order_requires_tokenization ( $order )) {
			return $this->process_pre_order ( $order );
		}
		
		// if order total is zero, then save meta but don't process payment.
		if ($order->get_total () == 0) {
			return $this->process_zero_total_order ( $order );
		}
		
		/**
		 * Try to save the payment token.
		 * A failure should not affect the order processing. Tokens have to be saved before a charge since they
		 * can only be used once. Save the token then it can be used.
		 */
		if ($this->should_save_payment_method ( $order )) {
			if (is_wp_error ( $this->save_payment_method ( $this->get_new_source_token (), $order ) )) {
				$this->set_payment_save_error ( $order, $this->wp_error );
			}
		}
		
		/**
		 * Set a lock on the order so webhooks don't cause issues with payment processing.
		 */
		$this->set_order_lock ( $order );
		
		$args = $this->get_order_charge_args ( $order );
		
		$customer_id = wc_stripe_get_customer_id ( $order->get_user_id () );
		
		// only add customer ID if user is paying with a saved payment method
		if ($customer_id && $this->use_saved_source ()) {
			$args[ 'customer' ] = $customer_id;
		}
		
		$charge = $this->gateway->charge ( $args, wc_stripe_order_mode ( $order ) );
		
		wc_stripe_log_info ( 'Stripe charge: ' . print_r ( $charge, true ) );
		if (is_wp_error ( $charge )) {
			wc_add_notice ( sprintf ( __ ( 'Error processing payment. Reason: %s', 'woo-stripe-payment' ), $charge->get_error_message () ), 'error' );
			$order->update_status ( 'failed' );
			$order->add_order_note ( sprintf ( __ ( 'Error processing payment. Reason: %s. Code: %s', 'woo-stripe-payment' ), $charge->get_error_message (), $charge->get_error_code () ) );
			return $this->order_error ();
		}
		
		$this->save_order_meta ( $order, $charge );
		
		// pending status is for asynchronous payment methods
		if ('pending' === $charge->status) {
			$order->update_status ( apply_filters ( 'wc_stripe_pending_charge_status', 'on-hold', $order, $this ), sprintf ( __ ( 'Charge %s is pending. Payment Method: %s. Payment will be completed once charge.succeeded webhook received from Stripe.', 'woo-stripe-payment' ), $order->get_transaction_id (), $order->get_payment_method_title () ) );
		} else {
			if ($charge->captured) {
				$order->payment_complete ( $charge->id );
			} else {
				$order_status = $this->get_option ( 'order_status' );
				$order->update_status ( apply_filters ( 'wc_stripe_authorized_order_status', 'default' === $order_status ? 'processing' : $order_status, $order, $this ) );
			}
			$order->add_order_note ( sprintf ( __ ( 'Order charge successful in Stripe. Charge: %s. Payment Method: %s', 'woo-stripe-payment' ), $order->get_transaction_id (), $order->get_payment_method_title () ) );
		}
		
		$this->trigger_post_payment_processes ( $order, $this );
		
		return array( 'result' => 'success', 
				'redirect' => $order->get_checkout_order_received_url () 
		);
	}

	private function get_order_charge_args($order, $args = []) {
		$args = array_merge ( [ 
				'metadata' => $this->get_order_meta_data ( $order ), 
				'currency' => $order->get_currency (), 
				'amount' => wc_stripe_add_number_precision ( $order->get_total () ), 
				'shipping' => array( 
						'address' => array( 
								'city' => $order->get_shipping_city (), 
								'country' => $order->get_shipping_country (), 
								'line1' => $order->get_shipping_address_1 (), 
								'line2' => $order->get_shipping_address_2 (), 
								'postal_code' => $order->get_shipping_postcode (), 
								'state' => $order->get_shipping_state () 
						), 
						'name' => $this->get_name_from_order ( $order, 'shipping' ) 
				), 
				'capture' => $this->get_option ( 'charge_type' ) === 'capture', 
				'source' => $this->get_payment_source (), 
				'receipt_email' => $order->get_billing_email () 
		], $args );
		return apply_filters ( 'wc_stripe_charge_order_args', $args, $order, $this->id );
	}

	/**
	 *
	 * @param float $amount        	
	 * @param WC_Order $order        	
	 */
	public function capture_charge($amount, $order) {
		$amount_in_cents = wc_stripe_add_number_precision ( $amount );
		$result = $this->gateway->capture ( $order->get_transaction_id (), array( 
				'amount' => $amount_in_cents 
		), wc_stripe_order_mode ( $order ) );
		if (! is_wp_error ( $result )) {
			$order->payment_complete ();
			$this->save_order_meta ( $order, $result );
			$order->add_order_note ( sprintf ( __ ( 'Order amount captured in Stripe. Amount: %s', 'woo-stripe-payment' ), wc_price ( $amount, array( 
					'currency' => $order->get_currency () 
			) ) ) );
		} else {
			$order->add_order_note ( sprintf ( __ ( 'Error capturing charge in Stripe. Reason: %s', 'woo-stripe-payment' ), $result->get_error_message () ) );
		}
		return $result;
	}

	public function void_charge($order) {
		$result = $this->gateway->refund ( array( 
				'charge' => $order->get_transaction_id () 
		), wc_stripe_order_mode ( $order ) );
		if (is_wp_error ( $result )) {
			$order->add_order_note ( sprintf ( __ ( 'Error voiding charge. Reason: %s', 'woo-stripe-payment' ), $result->get_error_message () ) );
		} else {
			$order->add_order_note ( __ ( 'Charge voided in Stripe.', 'woo-stripe-payment' ) );
		}
		return $result;
	}

	public function create_payment_method($id, $customer_id) {
		$result = $this->gateway->create_customer_source ( $customer_id, $id );
		
		if (is_wp_error ( $result )) {
			return $result;
		}
		
		return $this->get_payment_token ( $result->id, $result );
	}

	public function scheduled_subscription_payment($amount, $order) {
		$this->processing_payment = true;
		
		$args = $this->get_order_charge_args ( $order, [ 
				'customer' => $order->get_meta ( '_wc_stripe_customer', true ), 
				'source' => $order->get_meta ( '_payment_method_token', true ), 
				'amount' => wc_stripe_add_number_precision ( $amount ) 
		] );
		if (empty ( $args[ 'customer' ] )) {
			unset ( $args[ 'customer' ] );
		}
		$charge = $this->gateway->charge ( $args, wc_stripe_order_mode ( $order ) );
		
		if (is_wp_error ( $charge )) {
			$order->update_status ( 'failed', sprintf ( __ ( 'Recurring payment for subscription failed. Reason: %s. Payment method: %s', 'woo-stripe-payment' ), $charge->get_error_message (), $order->get_payment_method_title () ) );
		} else {
			if ($charge->captured) {
				$order->payment_complete ( $charge->id );
				$order->add_order_note ( sprintf ( __ ( 'Recurring payment captured in Stripe. Payment method: %s', 'woo-stripe-payment' ), $order->get_payment_method_title () ) );
			} else {
				$order->update_status ( apply_filters ( 'wc_stripe_authorized_renewal_order_status', 'processing', $order, $this ), sprintf ( __ ( 'Recurring payment authorized in Stripe. Payment method: %s', 'woo-stripe-payment' ), $order->get_payment_method_title () ) );
			}
			$this->save_order_meta ( $order, $charge );
		}
	}

	public function delete_payment_method($token_id, $token) {
		$mode = $token->get_environment ();
		try {
			$customer_id = wc_stripe_get_customer_id ( $token->get_user_id (), $mode );
			$this->gateway->delete_card ( $token->get_token (), $customer_id, $mode );
		} catch ( \Stripe\Error\Base $e ) {
			wc_stripe_log_error ( sprintf ( __ ( 'Error deleting Stripe card. Token Id: %s', 'woo-stripe-payment' ), $token->get_token () ) );
		}
	}

	/**
	 *
	 * @param WC_Order $order        	
	 */
	public function process_pre_order_payment($order) {
		$this->processing_payment = true;
		
		$args = $this->get_order_charge_args ( $order, [ 
				'customer' => wc_stripe_get_customer_id ( $order->get_customer_id (), wc_stripe_order_mode ( $order ) ), 
				'source' => $order->get_meta ( '_payment_method_token', true ), 
				'amount' => wc_stripe_add_number_precision ( $order->get_total () ) 
		] );
		if (empty ( $args[ 'customer' ] )) {
			unset ( $args[ 'customer' ] );
		}
		$charge = $this->gateway->charge ( $args, wc_stripe_order_mode ( $order ) );
		
		if (is_wp_error ( $charge )) {
			$order->update_status ( 'failed', sprintf ( __ ( 'Pre-order payment for subscription failed. Reason: %s. Payment method: %s', 'woo-stripe-payment' ), $charge->get_error_message (), $order->get_payment_method_title () ) );
		} else {
			if ($charge->captured) {
				$order->payment_complete ( $charge->id );
				$order->add_order_note ( sprintf ( __ ( 'Pre-order payment captured in Stripe. Payment method: %s', 'woo-stripe-payment' ), $order->get_payment_method_title () ) );
			} else {
				$order->update_status ( apply_filters ( 'wc_stripe_authorized_renewal_order_status', 'processing', $order, $this ), sprintf ( __ ( 'Recurring payment authorized in Stripe. Payment method: %s', 'woo-stripe-payment' ), $order->get_payment_method_title () ) );
			}
			$this->save_order_meta ( $order, $charge );
		}
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see WC_Payment_Gateway_Stripe::get_payment_method_from_charge()
	 */
	public function get_payment_method_from_charge($charge) {
		return $charge->source->id;
	}
}