<?php

/**
 * Processes the charge via webhooks for local payment methods like iDEAL, EPS, etc.
 * @since 3.0.0
 * @package Stripe/Functions
 * 
 * @param \Stripe\Source $source
 * @param WP_REST_Request $request
 */
function wc_stripe_process_source_chargeable($source, $request) {
	// first retrieve the order_id using the source ID.
	global $wpdb;
	
	$order = wc_stripe_get_order_from_source_id ( $source->id );
	if (! $order) {
		wc_stripe_log_error ( sprintf ( 'Could not create a charge for source %s. No order ID was found in your Wordpress database.', $source->id ) );
		return;
	}
	
	$payment_method = $order->get_payment_method ();
	
	/**
	 *
	 * @var WC_Payment_Gateway_Stripe $gateway
	 */
	$gateway = WC ()->payment_gateways ()->payment_gateways ()[ $payment_method ];
	
	// if the order has a transaction ID, then a charge has already been created.
	if ($gateway->has_order_lock ( $order ) || ( $transaction_id = $order->get_transaction_id () )) {
		wc_stripe_log_info ( sprintf ( 'source.chargeable event received. Charge has already been created for order %s. Event exited.', $order->get_id () ) );
		return;
	}
	$gateway->set_new_source_token ( $source->id );
	$gateway->processing_payment = true;
	$gateway->set_order_lock ( $order );
	wc_stripe_log_info ( 'processing from webhook.' );
	$gateway->process_payment ( $order->get_id () );
}

/**
 * When the charge has succeeded, the order should be completed.
 *
 * @since 3.0.5
 * @package Stripe/Functions
 *         
 * @param \Stripe\Charge $source        	
 * @param WP_REST_Request $request        	
 */
function wc_stripe_process_charge_succeeded($charge, $request) {
	$order = wc_stripe_get_order_from_transaction ( $charge->id );
	if (! $order) {
		wc_stripe_log_error ( sprintf ( 'Could not complete payment for charge %s. No order ID was found in your Wordpress database.', $charge->id ) );
		return;
	}
	$payment_method = WC ()->payment_gateways ()->payment_gateways ()[ $order->get_payment_method () ];
	
	// only process payment_complete for asynchronous methods.
	if (! $payment_method->synchronous) {
		// want to prevent plugin from processing capture_charge since charge has already been captured.
		remove_action ( 'woocommerce_order_status_completed', 'wc_stripe_order_status_completed' );
		// call payment complete so shipping, emails, etc are triggered.
		$order->payment_complete ();
		$order->add_order_note ( __ ( 'Charge.succeeded webhook recieved. Payment has been completed.', 'woo-stripe-payment' ) );
		
		if (wcs_stripe_active () && wcs_order_contains_subscription ( $order )) {
			/**
			 *
			 * @var WC_Payment_Gateway_Stripe $gateway
			 */
			$gateway = WC ()->payment_gateways ()->payment_gateways ()[ $order->get_payment_method () ];
			$gateway->save_order_meta ( $order, $charge );
		}
	}
}

/**
 *
 * @since 3.1.0
 * @package Stripe/Functions
 *         
 * @param \Stripe\PaymentIntent $intent        	
 * @param WP_REST_Request $request        	
 */
function wc_stripe_process_payment_intent_succeeded($intent, $request) {
	$order = wc_get_order ( $intent->metadata[ 'order_id' ] );
	if (! $order) {
		wc_stripe_log_error ( sprintf ( 'Could not complete payment for paymentintent %s. No order ID was found in your Wordpress database.', $intent->id ) );
		return;
	}
	$payment_method = WC ()->payment_gateways ()->payment_gateways ()[ $order->get_payment_method () ];
	
	if ($payment_method instanceof WC_Payment_Gateway_Stripe_Local_Payment) {
		// this webhook is executed immediately by Stripe so add some delay so the redirect
		// can potentially be used first.
		sleep ( 1 );
		
		if ($payment_method->has_order_lock ( $order ) || ( $transaction_id = $order->get_transaction_id () )) {
			wc_stripe_log_info ( sprintf ( 'payment_intent.succeeded event received. Intent has been completed and been created for order %s. Event exited.', $order->get_id () ) );
			return;
		}
		
		$payment_method->set_order_lock ( $order );
		
		// want to prevent plugin from processing capture_charge since charge has already been captured.
		remove_action ( 'woocommerce_order_status_completed', 'wc_stripe_order_status_completed' );
		
		$payment_method->save_order_meta ( $order, $intent->charges->data[ 0 ] );
		
		// call payment complete so shipping, emails, etc are triggered.
		$order->payment_complete ( $intent->charges->data[ 0 ]->id );
		
		$order->add_order_note ( __ ( 'payment_intent.succeeded webhook recieved. Payment has been completed.', 'woo-stripe-payment' ) );
	}
}