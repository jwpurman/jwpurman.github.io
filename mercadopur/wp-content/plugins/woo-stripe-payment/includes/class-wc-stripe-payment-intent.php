<?php
require_once ( WC_STRIPE_PLUGIN_FILE_PATH . 'includes/abstract/abstract-wc-stripe-payment.php' );

/**
 *
 * @author Payment Plugins
 * @since 3.1.0
 *       
 */
class WC_Stripe_Payment_Intent extends WC_Stripe_Payment {

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see WC_Stripe_Payment::process_payment()
	 */
	public function process_payment($order) {
		// first check to see if a payment intent already exists
		if (( $intent = $order->get_meta ( WC_Stripe_Constants::PAYMENT_INTENT ) ) && $intent[ 'confirmation_method' ] == $this->payment_method->get_confirmation_method ( $order )) {
			$intent_id = $intent[ 'id' ];
			if ($this->can_update_payment_intent ( $order )) {
				$intent = $this->gateway->update_payment_intent ( $intent_id, $this->get_payment_intent_args ( $order, false ) );
			} else {
				$intent = $this->gateway->fetch_payment_intent ( $intent_id );
			}
		} else {
			$intent = $this->gateway->create_payment_intent ( $this->get_payment_intent_args ( $order ) );
		}
		
		if (is_wp_error ( $intent )) {
			return $intent;
		}
		
		// always update the order with the payment intent.
		$order->update_meta_data ( WC_Stripe_Constants::PAYMENT_INTENT_ID, $intent->id );
		$order->update_meta_data ( WC_Stripe_Constants::PAYMENT_METHOD_TOKEN, $intent->payment_method );
		// serialize the the intent and save to the order. The intent will be used to analyze if anything
		// has changed.
		$order->update_meta_data ( WC_Stripe_Constants::PAYMENT_INTENT, $intent->jsonSerialize () );
		$order->save ();
		
		if ($intent->status === 'requires_confirmation') {
			$intent = $this->gateway->confirm_payment_intent ( $intent );
			if (is_wp_error ( $intent )) {
				return $intent;
			}
		}
		
		// the intent was processed.
		if ($intent->status === 'succeeded' || $intent->status === 'requires_capture') {
			
			if ($this->payment_method->should_save_payment_method ( $order )) {
				$result = $this->payment_method->save_payment_method ( $this->payment_method->get_new_source_token (), $order );
				if (is_wp_error ( $result )) {
					$this->payment_method->set_payment_save_error ( $order, $result );
				}
			}
			
			// payment has been processed.
			$charges = $intent->charges;
			if (count ( $charges->data ) > 0) {
				$charge = $charges->data[ 0 ];
				return ( object ) [ 
						'complete_payment' => true, 
						'charge' => $charge 
				];
			}
		}
		if ($intent->status === 'requires_source_action' || $intent->status === 'requires_action') {
			// 3DS actions are required. Need to have customer complete action.
			return ( object ) [ 
					'complete_payment' => false, 
					'redirect' => $this->payment_method->get_payment_intent_checkout_url ( $intent, $order ) 
			];
		}
		if ($intent->status === 'requires_source' || $intent->status === 'requires_payment_method') {
			// return new WP_Error ( 'payment-intent-error', __ ( 'A new payment method is required.', 'woo-stripe-payment' ) );
			return ( object ) [ 
					'complete_payment' => false, 
					'redirect' => $this->payment_method->get_payment_intent_checkout_url ( $intent, $order ) 
			];
		}
	}

	public function scheduled_subscription_payment($amount, $order) {
		$args = $this->get_payment_intent_args ( $order );
		
		$args[ 'confirm' ] = true;
		$args[ 'off_session' ] = true;
		$args[ 'payment_method' ] = $this->payment_method->get_order_meta_data ( WC_Stripe_Constants::PAYMENT_METHOD_TOKEN, $order );
		
		if (( $customer = $this->payment_method->get_order_meta_data ( WC_Stripe_Constants::CUSTOMER_ID, $order ) )) {
			$args[ 'customer' ] = $customer;
		}
		
		$intent = $this->gateway->create_payment_intent ( $args, wc_stripe_order_mode ( $order ) );
		
		if (is_wp_error ( $intent )) {
			return $intent;
		} else {
			$order->update_meta_data ( WC_Stripe_Constants::PAYMENT_INTENT_ID, $intent->id );
			
			$charge = $intent->charges->data[ 0 ];
			
			if ($intent->status === 'succeeded' || $intent->status === 'requires_capture') {
				
				return ( object ) [ 
						'complete_payment' => true, 
						'charge' => $charge 
				];
			} else {
				return ( object ) [ 
						'complete_payment' => false, 
						'charge' => $charge 
				];
			}
		}
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see WC_Stripe_Payment::process_pre_order_payment()
	 */
	public function process_pre_order_payment($order) {
		$args = $this->get_payment_intent_args ( $order );
		
		$args[ 'confirm' ] = true;
		$args[ 'off_session' ] = true;
		$args[ 'payment_method' ] = $this->payment_method->get_order_meta_data ( WC_Stripe_Constants::PAYMENT_METHOD_TOKEN, $order );
		
		if (( $customer = wc_stripe_get_customer_id ( $order->get_customer_id (), wc_stripe_order_mode ( $order ) ) )) {
			$args[ 'customer' ] = $customer;
		}
		
		$intent = $this->gateway->create_payment_intent ( $args, wc_stripe_order_mode ( $order ) );
		
		if (is_wp_error ( $intent )) {
			return $intent;
		} else {
			$order->update_meta_data ( '_payment_intent_id', $intent->id );
			
			$charge = $intent->charges->data[ 0 ];
			
			if ($intent->status === 'succeeded' || $intent->status === 'requires_capture') {
				return ( object ) [ 
						'complete_payment' => true, 
						'charge' => $charge 
				];
			} else {
				return ( object ) [ 
						'complete_payment' => false, 
						'charge' => $charge 
				];
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
		$intent = $order->get_meta ( WC_Stripe_Constants::PAYMENT_INTENT );
		if ($intent) {
			$order_hash = implode ( '_', [ 
					wc_stripe_add_number_precision ( $order->get_total () ), 
					wc_stripe_get_customer_id ( $order->get_user_id () ), 
					$this->payment_method->get_payment_method_from_request (), 
					$this->payment_method->get_payment_method_type () 
			] );
			$intent_hash = implode ( '_', [ 
					$intent[ 'amount' ], 
					$intent[ 'customer' ], 
					$intent[ 'payment_method' ], 
					isset ( $intent[ 'payment_method_types' ] ) ? $intent[ 'payment_method_types' ][ 0 ] : '' 
			] );
			return $order_hash !== $intent_hash;
		}
		return false;
	}

	/**
	 *
	 * @param WC_Order $order        	
	 */
	public function get_payment_intent_args($order, $new = true) {
		$this->add_general_order_args ( $args, $order );
		
		if ($new) {
			$args[ 'confirmation_method' ] = $this->payment_method->get_confirmation_method ( $order );
			$args[ 'capture_method' ] = $this->payment_method->get_option ( 'charge_type' ) === 'capture' ? 'automatic' : 'manual';
			$args[ 'confirm' ] = false;
		}
		if (( $customer_id = wc_stripe_get_customer_id ( $order->get_customer_id () ) )) {
			$args[ 'customer' ] = $customer_id;
		}
		if ($this->payment_method->should_save_payment_method ( $order )) {
			$args[ 'setup_future_usage' ] = 'off_session';
		}
		
		$args[ 'payment_method_types' ][] = $this->payment_method->get_payment_method_type ();
		
		return apply_filters ( 'wc_stripe_payment_intent_args', $args, $order, $this );
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see WC_Stripe_Payment::capture_charge()
	 */
	public function capture_charge($amount, $order) {
		$payment_intent = $this->payment_method->get_order_meta_data ( WC_Stripe_Constants::PAYMENT_INTENT_ID, $order );
		if (empty ( $payment_intent )) {
			$charge = $this->gateway->get_charge ( $order->get_transaction_id (), wc_stripe_order_mode ( $order ) );
			$payment_intent = $charge->payment_intent;
			$order->update_meta_data ( WC_Stripe_Constants::PAYMENT_INTENT_ID, $payment_intent );
			$order->save ();
		}
		return $this->gateway->capture_payment_intent ( $payment_intent, array( 
				'amount_to_capture' => wc_stripe_add_number_precision ( $amount ) 
		), wc_stripe_order_mode ( $order ) );
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see WC_Stripe_Payment::void_charge()
	 */
	public function void_charge($order) {
		// fetch the intent and check its status
		$payment_intent = $this->gateway->fetch_payment_intent ( $order->get_meta ( '_payment_intent_id', true ), wc_stripe_order_mode ( $order ) );
		if (is_wp_error ( $payment_intent )) {
			return $payment_intent;
		}
		$statuses = array( 'requires_payment_method', 
				'requires_capture', 'requires_confirmation', 
				'requires_action' 
		);
		if ('canceled' !== $payment_intent->status) {
			if (in_array ( $payment_intent->status, $statuses )) {
				return $this->gateway->cancel_payment_intent ( $payment_intent, wc_stripe_order_mode ( $order ) );
			} elseif ('succeeded' === $payment_intent->status) {
				return $this->process_refund ( $order->get_id (), $order->get_total () );
			}
		}
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see WC_Stripe_Payment::get_payment_method_from_charge()
	 */
	public function get_payment_method_from_charge($charge) {
		return $charge->payment_method;
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see WC_Stripe_Payment::add_order_payment_method()
	 */
	public function add_order_payment_method(&$args, $order) {
		// if ($order->get_meta ( WC_Stripe_Constants::PAYMENT_METHOD_TOKEN, true ) !== $this->payment_method->get_payment_method_from_request ()) {
		$args[ 'payment_method' ] = $this->payment_method->get_payment_method_from_request ();
		if (empty ( $args[ 'payment_method' ] )) {
			unset ( $args[ 'payment_method' ] );
		}
		// }
	}
}