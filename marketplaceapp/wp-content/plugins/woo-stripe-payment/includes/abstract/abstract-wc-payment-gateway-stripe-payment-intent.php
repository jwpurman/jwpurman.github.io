<?php
if (! class_exists ( 'WC_Payment_Gateway_Stripe' )) {
	return;
}
/**
 * Gateways that use PaymentIntent API should extend this abstract class.
 *
 * @since 3.0.0
 * @package Stripe/Abstract
 * @author PaymentPlugins
 *        
 */
abstract class WC_Payment_Gateway_Stripe_Payment_Intent extends WC_Payment_Gateway_Stripe {

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
		
		// first check to see if a payment intent already exists
		if (( $intent_id = $order->get_meta ( '_payment_intent_id', true ) )) {
			if ($this->can_update_payment_intent ( $order )) {
				$intent = $this->gateway->update_payment_intent ( $intent_id, $this->get_payment_intent_args ( $order, false ) );
			} else {
				$intent = $this->gateway->fetch_payment_intent ( $intent_id );
			}
		} else {
			$intent = $this->gateway->create_payment_intent ( $this->get_payment_intent_args ( $order ) );
		}
		
		if (is_wp_error ( $intent )) {
			wc_add_notice ( $intent->get_error_message (), 'error' );
			$order->update_status ( 'failed', sprintf ( __ ( 'Error processing payment. Reason: %s', 'woo-stripe-payment' ), $intent->get_error_message () ) );
			return $this->order_error ();
		}
		
		// always update the order with the payment intent.
		$order->update_meta_data ( '_payment_intent_id', $intent->id );
		$order->update_meta_data ( '_payment_method_token', $intent->payment_method );
		// serialize the the intent and save to the order. The intent will be used to analyze if anything
		// has changed.
		$order->update_meta_data ( '_payment_intent', $intent->jsonSerialize () );
		$order->save ();
		
		if ($intent->status === 'requires_confirmation') {
			$intent = $this->gateway->confirm_payment_intent ( $intent );
			if (is_wp_error ( $intent )) {
				wc_add_notice ( $intent->get_error_message (), 'error' );
				$order->update_status ( 'failed' );
				// manually add note because if status is already failed then the note won't get included
				$order->add_order_note ( sprintf ( __ ( 'Error processing payment. Reason: %s', 'woo-stripe-payment' ), $intent->get_error_message () ) );
				return $this->order_error ();
			}
		}
		
		// the intent was processed.
		if ($intent->status === 'succeeded' || $intent->status === 'requires_capture') {
			// payment has been processed.
			$charges = $intent->charges;
			if (count ( $charges->data ) > 0) {
				$charge = $charges->data[ 0 ];
				if ($charge->captured) {
					$order->payment_complete ( $charge->id );
				} else {
					// update the order status since the intent has not been captured.
					$order_status = $this->get_option ( 'order_status' );
					$order->update_status ( apply_filters ( 'wc_stripe_authorized_order_status', 'default' === $order_status ? 'processing' : $order_status, $order, $this ) );
				}
				$this->save_order_meta ( $order, $charge );
				$order->add_order_note ( sprintf ( __ ( 'Order %s successful in Stripe. Charge: %s. Payment Method: %s', 'woo-stripe-payment' ), $charge->captured ? __ ( 'charge', 'woo-stripe-payment' ) : __ ( 'authorization', 'woo-stripe-payment' ), $order->get_transaction_id (), $order->get_payment_method_title () ) );
			}
			
			/**
			 * Save the payment method if appropriate.
			 * We wait until the end of order processing because a failed payment method save
			 * should not halt the order process. Customer's can always take action after a failed save,
			 * like going to the Add Payment method page.
			 */
			if ($this->should_save_payment_method ( $order )) {
				if (is_wp_error ( $this->save_payment_method ( $intent->payment_method, $order ) )) {
					$this->set_payment_save_error ( $order, $this->wp_error );
				}
			}
			$this->trigger_post_payment_processes ( $order, $this );
			
			return array( 'result' => 'success', 
					'redirect' => $order->get_checkout_order_received_url () 
			);
		}
		if ($intent->status === 'requires_source_action' || $intent->status === 'requires_action') {
			// 3DS actions are required. Need to have customer complete action.
			$url = $this->get_payment_intent_checkout_url ( $intent->client_secret );
			return array( 'result' => 'success', 
					'redirect' => $url 
			);
		}
		if ($intent->status === 'requires_source' || $intent->status === 'requires_payment_method') {
			wc_add_notice ( __ ( 'A new payment method is required.', 'woo-stripe-payment' ), 'error' );
			return $this->order_error ();
		}
	}

	/**
	 *
	 * @param WC_Order $order        	
	 */
	public function get_payment_intent_args($order, $new = true) {
		$args = [ 
				'amount' => wc_stripe_add_number_precision ( $order->get_total () ), 
				'currency' => $order->get_currency (), 
				'metadata' => $this->get_order_meta_data ( $order ), 
				'description' => $this->get_order_description ( $order ), 
				'shipping' => wc_stripe_order_has_shipping_address ( $order ) ? [ 
						'address' => [ 
								'city' => $order->get_shipping_city (), 
								'country' => $order->get_shipping_country (), 
								'line1' => $order->get_shipping_address_1 (), 
								'line2' => $order->get_shipping_address_2 (), 
								'postal_code' => $order->get_shipping_postcode (), 
								'state' => $order->get_shipping_state () 
						], 
						'name' => $this->get_name_from_order ( $order, 'shipping' ) 
				] : [] 
		];
		if ($new) {
			$args = array_merge ( $args, [ 
					'confirmation_method' => 'manual', 
					'capture_method' => $this->get_option ( 'charge_type' ) === 'capture' ? 'automatic' : 'manual', 
					'confirm' => false 
			] );
		}
		if ($order->get_meta ( '_payment_method_token', true ) !== $this->get_payment_method_from_request ()) {
			$args[ 'payment_method' ] = $this->get_payment_method_from_request ();
		}
		if (( $customer_id = wc_stripe_get_customer_id ( $order->get_customer_id () ) )) {
			$args[ 'customer' ] = $customer_id;
		}
		if ($this->should_save_payment_method ( $order )) {
			$args[ 'setup_future_usage' ] = 'off_session';
		}
		return apply_filters ( 'wc_stripe_payment_intent_args', $args, $order, $this );
	}

	protected function get_payment_intent_checkout_url($secret) {
		// rand is used to generate some random entropy so that window hash events are triggered.
		return sprintf ( '#payment-intent=%s:%s', $secret, rand ( 0, 999999 ) );
	}

	public function create_payment_method($id, $customer_id) {
		// fetch the payment method from Stripe.
		$payment_method = $this->gateway->fetch_payment_method ( $id );
		
		if (is_wp_error ( $payment_method )) {
			return $payment_method;
		}
		
		$payment_method = $this->gateway->attach_payment_method ( $payment_method, [ 
				'customer' => $customer_id 
		] );
		
		if (is_wp_error ( $payment_method )) {
			return $payment_method;
		}
		
		return $this->get_payment_token ( $id, $payment_method );
	}

	public function capture_charge($amount, $order) {
		$amount_in_cents = wc_stripe_add_number_precision ( $amount );
		$payment_intent = $order->get_meta ( '_payment_intent_id', true );
		// if the intent was not saved before, then fetch it from the charge and save it to the order.
		if (empty ( $payment_intent )) {
			$charge = $this->retrieve_charge ( $order->get_transaction_id (), wc_stripe_order_mode ( $order ) );
			$payment_intent = $charge->payment_intent;
			$order->update_meta_data ( '_payment_intent_id', $payment_intent );
			$order->save ();
		}
		$result = $this->gateway->capture_payment_intent ( $payment_intent, array( 
				'amount_to_capture' => $amount_in_cents 
		), wc_stripe_order_mode ( $order ) );
		if (! is_wp_error ( $result )) {
			$order->payment_complete ();
			$order->add_order_note ( sprintf ( __ ( 'Order amount captured in Stripe. Amount: %s', 'woo-stripe-payment' ), wc_price ( $amount, array( 
					'currency' => $order->get_currency () 
			) ) ) );
		} else {
			$order->add_order_note ( sprintf ( __ ( 'Error capturing charge in Stripe. Reason: %s', 'woo-stripe-payment' ), $result->get_error_message () ) );
		}
		return $result;
	}

	public function void_charge($order) {
		// fetch the intent and check its status
		$payment_intent = $this->gateway->fetch_payment_intent ( $order->get_meta ( '_payment_intent_id', true ), wc_stripe_order_mode ( $order ) );
		if (is_wp_error ( $payment_intent )) {
			return;
		}
		$statuses = array( 'requires_payment_method', 
				'requires_capture', 'requires_confirmation', 
				'requires_action' 
		);
		if ('canceled' !== $payment_intent->status) {
			if (in_array ( $payment_intent->status, $statuses )) {
				$result = $this->gateway->cancel_payment_intent ( $payment_intent, wc_stripe_order_mode ( $order ) );
				if (is_wp_error ( $result )) {
					$order->add_order_note ( sprintf ( __ ( 'Error voiding charge. Reason: %s', 'woo-stripe-payment' ), $result->get_error_message () ) );
				} else {
					$order->add_order_note ( __ ( 'Charge voided in Stripe.', 'woo-stripe-payment' ) );
				}
			} elseif ('succeeded' === $payment_intent->status) {
				$this->process_refund ( $order->get_id (), $order->get_total () );
			}
		}
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see WC_Payment_Gateway_Stripe::scheduled_subscription_payment()
	 */
	public function scheduled_subscription_payment($amount, $order) {
		$this->processing_payment = true;
		
		$args = array_merge ( $this->get_payment_intent_args ( $order ), [ 
				'amount' => wc_stripe_add_number_precision ( $amount ), 
				'payment_method' => $order->get_meta ( '_payment_method_token', true ), 
				'customer' => $order->get_meta ( '_wc_stripe_customer', true ), 
				'confirm' => true, 'off_session' => true 
		] );
		if (empty ( $args[ 'customer' ] )) {
			unset ( $args[ 'customer' ] );
		}
		$intent = $this->gateway->create_payment_intent ( $args, wc_stripe_order_mode ( $order ) );
		
		if (is_wp_error ( $intent )) {
			// payment intent failed
			$order->update_status ( 'failed', sprintf ( __ ( 'Recurring payment for order failed. Reason: %s', 'woo-stripe-payment' ), $intent->get_error_message () ) );
		} else {
			if ($intent->status === 'succeeded' || $intent->status === 'requires_capture') {
				$charges = $intent->charges->data;
				if (isset ( $charges[ 0 ] )) {
					$charge = $charges[ 0 ];
					
					$order->update_meta_data ( '_payment_intent_id', $intent->id );
					$order->save ();
					$this->save_order_meta ( $order, $charge );
					
					if ($charge->captured) {
						$order->payment_complete ( $charge->id );
						$order->add_order_note ( sprintf ( __ ( 'Recurring payment captured in Stripe. Payment method: %s', 'woo-stripe-payment' ), $order->get_payment_method_title () ) );
					} else {
						$order->update_status ( apply_filters ( 'wc_stripe_authorized_renewal_order_status', 'processing', $order, $this ), sprintf ( __ ( 'Recurring payment authorized in Stripe. Payment method: %s', 'woo-stripe-payment' ), $order->get_payment_method_title () ) );
					}
				}
			} else {
				$order->update_status ( 'pending', sprintf ( __ ( 'Customer must manually complete payment for payment method %s', 'woo-stripe-payment' ), $order->get_payment_method_title () ) );
			}
		}
	}

	/**
	 * Compares the order's saved intent to the order's updates attributes.
	 * If there is a delta, then the payment intent can be updated.
	 *
	 * @param WC_Order $order        	
	 */
	public function can_update_payment_intent($order) {
		$intent = $order->get_meta ( '_payment_intent', true );
		if ($intent) {
			$order_hash = sprintf ( '%s_%s_%s', wc_stripe_add_number_precision ( $order->get_total () ), wc_stripe_get_customer_id ( $order->get_user_id () ), $this->get_payment_method_from_request () );
			$intent_hash = sprintf ( '%s_%s_%s', $intent[ 'amount' ], $intent[ 'customer' ], $intent[ 'payment_method' ] );
			return $order_hash !== $intent_hash;
		}
		return false;
	}

	public function delete_payment_method($token_id, $token) {
		$mode = $token->get_environment ();
		try {
			$customer_id = wc_stripe_get_customer_id ( $token->get_user_id (), $mode );
			$payment_method = $this->gateway->fetch_payment_method ( $token->get_token () );
			$this->gateway->delete_payment_method ( $payment_method );
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
		
		$args = array_merge ( $this->get_payment_intent_args ( $order ), [ 
				'amount' => wc_stripe_add_number_precision ( $order->get_total () ), 
				'payment_method' => $order->get_meta ( '_payment_method_token', true ), 
				'confirm' => true, 'off_session' => true 
		] );
		if (empty ( $args[ 'customer' ] )) {
			unset ( $args[ 'customer' ] );
		}
		$intent = $this->gateway->create_payment_intent ( $args, wc_stripe_order_mode ( $order ) );
		
		if (is_wp_error ( $intent )) {
			// payment intent failed
			$order->update_status ( 'failed', sprintf ( __ ( 'Pre-order payment for order failed. Reason: %s', 'woo-stripe-payment' ), $intent->get_error_message () ) );
		} else {
			if ($intent->status === 'succeeded' || $intent->status === 'requires_capture') {
				$charges = $intent->charges->data;
				if (isset ( $charges[ 0 ] )) {
					$charge = $charges[ 0 ];
					
					$order->update_meta_data ( '_payment_intent_id', $intent->id );
					$order->save ();
					$this->save_order_meta ( $order, $charge );
					
					if ($charge->captured) {
						$order->payment_complete ( $charge->id );
						$order->add_order_note ( sprintf ( __ ( 'Pre-order payment captured in Stripe. Payment method: %s', 'woo-stripe-payment' ), $order->get_payment_method_title () ) );
					} else {
						$order->update_status ( apply_filters ( 'wc_stripe_authorized_renewal_order_status', 'processing', $order, $this ), sprintf ( __ ( 'Recurring payment authorized in Stripe. Payment method: %s', 'woo-stripe-payment' ), $order->get_payment_method_title () ) );
					}
				}
			} else {
				$order->update_status ( 'pending', sprintf ( __ ( 'Customer must manually complete payment for payment method %s', 'woo-stripe-payment' ), $order->get_payment_method_title () ) );
			}
		}
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see WC_Payment_Gateway_Stripe::get_payment_method_from_charge()
	 */
	public function get_payment_method_from_charge($charge) {
		return $charge->payment_method;
	}
}