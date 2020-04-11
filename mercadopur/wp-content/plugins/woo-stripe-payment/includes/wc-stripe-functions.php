<?php

/**
 * @since 3.0.0
 * @package Stripe/Functions
 * Wrapper for wc_get_template that returns Stripe specfic templates.
 * @param string $template_name
 * @param array $args
 */
function wc_stripe_get_template($template_name, $args = array()) {
	wc_get_template ( $template_name, $args, wc_stripe ()->template_path (), wc_stripe ()->default_template_path () );
}

/**
 *
 *
 * Wrapper for wc_get_template_html that returns Stripe specififc templates in an html string.
 *
 * @package Stripe/Functions
 * @since 3.0.0
 * @param string $template_name        	
 * @param array $args        	
 * @return string
 */
function wc_stripe_get_template_html($template_name, $args = array()) {
	return wc_get_template_html ( $template_name, $args, wc_stripe ()->template_path (), wc_stripe ()->default_template_path () );
}

/**
 * Return true if WCS is active.
 *
 * @package Stripe/Functions
 * @return boolean
 */
function wcs_stripe_active() {
	return function_exists ( 'wcs_is_subscription' );
}

/**
 *
 * @package Stripe/Functions
 * @param WC_Payment_Gateway_Stripe $gateway        	
 */
function wc_stripe_token_field($gateway) {
	wc_stripe_hidden_field ( $gateway->token_key, 'wc-stripe-token-field' );
}

/**
 *
 * @package Stripe/Functions
 * @param WC_Payment_Gateway_Stripe $gateway        	
 */
function wc_stripe_payment_intent_field($gateway) {
	wc_stripe_hidden_field ( $gateway->payment_intent_key, 'wc-stripe-payment-intent-field' );
}

/**
 *
 * @package Stripe/Functions
 * @param string $id        	
 * @param string $class        	
 * @param string $value        	
 */
function wc_stripe_hidden_field($id, $class = '', $value = '') {
	printf ( '<input type="hidden" class="%1$s" id="%2$s" name="%2$s" value="%3$s"/>', $class, $id, $value );
}

/**
 * Return the mode for the plugin.
 *
 * @package Stripe/Functions
 * @return string
 */
function wc_stripe_mode() {
	return wc_stripe ()->api_settings->get_option ( 'mode' );
}

/**
 * Return the secret key for the provided mode.
 * If no mode given, the key for the active mode is returned.
 *
 * @package Stripe/Functions
 * @since 3.0.0
 * @param string $mode        	
 */
function wc_stripe_get_secret_key($mode = '') {
	$mode = empty ( $mode ) ? wc_stripe_mode () : $mode;
	return wc_stripe ()->api_settings->get_option ( "secret_key_{$mode}" );
}

/**
 * Return the publishable key for the provided mode.
 * If no mode given, the key for the active mode is returned.
 *
 * @package Stripe/Functions
 * @since 3.0.0
 * @param string $mode        	
 */
function wc_stripe_get_publishable_key($mode = '') {
	$mode = empty ( $mode ) ? wc_stripe_mode () : $mode;
	return wc_stripe ()->api_settings->get_option ( "publishable_key_{$mode}" );
}

/**
 * Return the stripe customer ID
 *
 * @package Stripe/Functions
 * @since 3.0.0
 * @param int $user_id        	
 * @param string $mode        	
 */
function wc_stripe_get_customer_id($user_id = '', $mode = '') {
	$mode = empty ( $mode ) ? wc_stripe_mode () : $mode;
	if ($user_id === 0) {
		return '';
	}
	if (empty ( $user_id )) {
		$user_id = get_current_user_id ();
	}
	return get_user_meta ( $user_id, "wc_stripe_customer_{$mode}", true );
}

/**
 *
 * @package Stripe/Functions
 * @param string $customer_id        	
 * @param int $user_id        	
 * @param string $mode        	
 */
function wc_stripe_save_customer($customer_id, $user_id, $mode = '') {
	$mode = empty ( $mode ) ? wc_stripe_mode () : $mode;
	$key = "wc_stripe_customer_{$mode}";
	update_user_meta ( $user_id, $key, $customer_id );
}

/**
 *
 * @since 3.0.0
 * @package Stripe/Functions
 * @param int $token_id        	
 * @param WC_Payment_Token $token        	
 */
function wc_stripe_woocommerce_payment_token_deleted($token_id, $token) {
	if (! did_action ( 'woocommerce_payment_gateways' )) {
		WC_Payment_Gateways::instance ();
	}
	do_action ( 'wc_stripe_payment_token_deleted_' . $token->get_gateway_id (), $token_id, $token );
}

/**
 * Log the provided message in the WC logs directory.
 *
 * @since 3.0.0
 * @package Stripe/Functions
 * @param int $level        	
 * @param string $message        	
 */
function wc_stripe_log($level, $message) {
	if (wc_stripe ()->api_settings->is_active ( 'debug_log' )) {
		$log = wc_get_logger ();
		$log->log ( $level, $message, array( 
				'source' => 'wc-stripe' 
		) );
	}
}

/**
 *
 * @since 3.0.0
 * @package Stripe/Functions
 * @param string $message        	
 */
function wc_stripe_log_error($message) {
	wc_stripe_log ( WC_Log_Levels::ERROR, $message );
}

/**
 *
 * @since 3.0.0
 * @package Stripe/Functions
 * @param string $message        	
 */
function wc_stripe_log_info($message) {
	wc_stripe_log ( WC_Log_Levels::INFO, $message );
}

/**
 * Return the mode that the order was created in.
 * Values can be <strong>live</strong> or <strong>test</strong>
 *
 * @since 3.0.0
 * @package Stripe/Functions
 * @param WC_Order|int $order        	
 */
function wc_stripe_order_mode($order) {
	if (is_object ( $order )) {
		return $order->get_meta ( WC_Stripe_Constants::MODE, true );
	}
	return get_post_meta ( $order, WC_Stripe_Constants::MODE, true );
}

/**
 *
 * @since 3.0.0
 * @package Stripe\Functions
 * @param array $gateways        	
 */
function wc_stripe_payment_gateways($gateways) {
	return array_merge ( $gateways, wc_stripe ()->payment_gateways () );
}

/**
 * Cancel the Stripe charge
 *
 * @package Stripe/Functions
 * @param int $order_id        	
 * @param WC_Order $order        	
 */
function wc_stripe_order_cancelled($order_id, $order) {
	$gateways = WC ()->payment_gateways ()->payment_gateways ();
	/**
	 *
	 * @var WC_Payment_Gateway_Stripe $gateway
	 */
	$gateway = isset ( $gateways[ $order->get_payment_method () ] ) ? $gateways[ $order->get_payment_method () ] : null;
	
	if ($gateway && $gateway instanceof WC_Payment_Gateway_Stripe) {
		$gateway->void_charge ( $order );
	}
}

/**
 *
 * @since 3.0.0
 * @package Stripe/Functions
 * @param int $order_id        	
 * @param WC_Order $order        	
 */
function wc_stripe_order_status_completed($order_id, $order) {
	$gateways = WC ()->payment_gateways ()->payment_gateways ();
	/**
	 *
	 * @var WC_Payment_Gateway_Stripe $gateway
	 */
	$gateway = isset ( $gateways[ $order->get_payment_method () ] ) ? $gateways[ $order->get_payment_method () ] : null;
	// @since 3.0.3 check added to ensure this is a Stripe gateway.
	if ($gateway && $gateway instanceof WC_Payment_Gateway_Stripe && ! $gateway->processing_payment) {
		$gateway->capture_charge ( $order->get_total (), $order );
	}
}

/**
 *
 * @since 3.0.0
 * @package Stripe/Functions
 * @param [] $address        	
 * @throws Exception
 */
function wc_stripe_update_customer_location($address) {
	// address validation for countries other than US is problematic when using responses from payment sources like Apple Pay.
	if ($address[ 'postcode' ] && $address[ 'country' ] === 'US' && ! WC_Validation::is_postcode ( $address[ 'postcode' ], $address[ 'country' ] )) {
		throw new Exception ( __ ( 'Please enter a valid postcode / ZIP.', 'woocommerce' ) );
	} elseif ($address[ 'postcode' ]) {
		$address[ 'postcode' ] = wc_format_postcode ( $address[ 'postcode' ], $address[ 'country' ] );
	}
	
	if ($address[ 'country' ]) {
		WC ()->customer->set_billing_location ( $address[ 'country' ], $address[ 'state' ], $address[ 'postcode' ], $address[ 'city' ] );
		WC ()->customer->set_shipping_location ( $address[ 'country' ], $address[ 'state' ], $address[ 'postcode' ], $address[ 'city' ] );
		// set the customer's address if it's in the $address array
		if (! empty ( $address[ 'address_1' ] )) {
			WC ()->customer->set_shipping_address_1 ( wc_clean ( $address[ 'address_1' ] ) );
		}
		if (! empty ( $address[ 'address_2' ] )) {
			WC ()->customer->set_shipping_address_2 ( wc_clean ( $address[ 'address_2' ] ) );
		}
		if (! empty ( $address[ 'first_name' ] )) {
			WC ()->customer->set_shipping_first_name ( $address[ 'first_name' ] );
		}
		if (! empty ( $address[ 'last_name' ] )) {
			WC ()->customer->set_shipping_last_name ( $address[ 'last_name' ] );
		}
	} else {
		WC ()->customer->set_billing_address_to_base ();
		WC ()->customer->set_shipping_address_to_base ();
	}
	
	WC ()->customer->set_calculated_shipping ( true );
	WC ()->customer->save ();
	
	do_action ( 'woocommerce_calculated_shipping' );
}

/**
 *
 * @since 3.0.0
 * @package Stripe/Functions
 * @param [] $methods        	
 */
function wc_stripe_update_shipping_methods($methods) {
	$chosen_shipping_methods = WC ()->session->get ( 'chosen_shipping_methods', [] );
	
	foreach ( $methods as $i => $method ) {
		$chosen_shipping_methods[ $i ] = $method;
	}
	
	WC ()->session->set ( 'chosen_shipping_methods', $chosen_shipping_methods );
}

/**
 * Return true if there are shipping packages that contain rates.
 *
 * @since 3.0.0
 * @package Stripe/Functions
 * @return boolean
 */
function wc_stripe_shipping_address_serviceable() {
	$packages = WC ()->shipping ()->get_packages ();
	if ($packages) {
		foreach ( $packages as $package ) {
			if (count ( $package[ 'rates' ] ) > 0) {
				return true;
			}
		}
	}
	return false;
}

/**
 *
 * @package Stripe/Functions
 * @param bool $encode        	
 * @param WC_Order $order        	
 * @since 3.0.0
 */
function wc_stripe_get_display_items($encode = false, $order = null) {
	$items = [];
	if (! $order) {
		$cart = WC ()->cart;
		foreach ( $cart->get_cart () as $cart_item ) {
			/**
			 *
			 * @var WC_Product $product
			 */
			$product = $cart_item[ 'data' ];
			$qty = $cart_item[ 'quantity' ];
			$items[] = [ 
					'label' => $qty > 1 ? sprintf ( '%s X %s', $product->get_name (), $qty ) : $product->get_name (), 
					'pending' => false, 
					'amount' => wc_stripe_add_number_precision ( $product->get_price () * $qty ) 
			];
		}
		if ($cart->needs_shipping ()) {
			$items[] = [ 
					'label' => __ ( 'Shipping', 'woo-stripe-payment' ), 
					'pending' => false, 
					'amount' => wc_stripe_add_number_precision ( $cart->shipping_total ) 
			];
		}
		
		// fees
		foreach ( $cart->get_fees () as $fee ) {
			$items[] = [ 'label' => $fee->name, 
					'pending' => false, 
					'amount' => wc_stripe_add_number_precision ( $fee->total ) 
			];
		}
		// coupons
		if ($cart->discount_cart != 0) {
			$items[] = [ 
					'label' => __ ( 'Discount', 'woo-stripe-payment' ), 
					'pending' => false, 
					'amount' => wc_stripe_add_number_precision ( - 1 * abs ( $cart->discount_cart ) ) 
			];
		}
		
		if (wc_tax_enabled ()) {
			$items[] = [ 
					'label' => __ ( 'Tax', 'woo-stripe-payment' ), 
					'pending' => false, 
					'amount' => wc_stripe_add_number_precision ( $cart->get_taxes_total () ) 
			];
		}
	} else {
		// add all order items
		foreach ( $order->get_items () as $item ) {
			/**
			 *
			 * @var WC_Order_Item_Product $item
			 */
			$qty = $item->get_quantity ();
			
			$items[] = [ 
					'label' => $qty > 1 ? sprintf ( '%s X %s', $item->get_name (), $qty ) : $item->get_name (), 
					'pending' => false, 
					'amount' => wc_stripe_add_number_precision ( $item->get_subtotal () ) 
			];
		}
		// shipping total
		if ($order->get_shipping_total ()) {
			$items[] = [ 
					'label' => __ ( 'Shipping', 'woo-stripe-payment' ), 
					'pending' => false, 
					'amount' => wc_stripe_add_number_precision ( $order->get_shipping_total () ) 
			];
		}
		// discount total
		if ($order->get_total_discount ()) {
			$items[] = [ 
					'label' => __ ( 'Discount', 'woo-stripe-payment' ), 
					'pending' => false, 
					'amount' => wc_stripe_add_number_precision ( $order->get_total_discount () ) 
			];
		}
		if ($order->get_fees ()) {
			$fee_total = 0;
			foreach ( $order->get_fees () as $fee ) {
				$fee_total += $fee->get_total ();
			}
			$items[] = [ 
					'label' => __ ( 'Fees', 'woo-stripe-payment' ), 
					'pending' => false, 
					'amount' => wc_stripe_add_number_precision ( $fee_total ) 
			];
		}
		// tax total
		if ($order->get_total_tax ()) {
			$items[] = [ 
					'label' => __ ( 'Tax', 'woocommerce' ), 
					'pending' => false, 
					'amount' => wc_stripe_add_number_precision ( $order->get_total_tax () ) 
			];
		}
	}
	$items = apply_filters ( 'wc_stripe_get_display_items', $items, $order );
	return $encode ? htmlspecialchars ( wp_json_encode ( $items ) ) : $items;
}

/**
 *
 * @since 3.0.0
 * @package Stripe/Functions
 * @param bool $encode        	
 * @param WC_Order $order        	
 * @return mixed
 */
function wc_stripe_get_shipping_options($encode = false, $order = null) {
	$methods = [];
	if (! $order) {
		$ids = [];
		$chosen_shipping_methods = WC ()->session->get ( 'chosen_shipping_methods', [] );
		$packages = WC ()->shipping ()->get_packages ();
		foreach ( $packages as $i => $package ) {
			foreach ( $package[ 'rates' ] as $rate ) {
				/**
				 *
				 * @var WC_Shipping_Rate $rate
				 */
				$methods[] = [ 
						'id' => sprintf ( '%s:%s', $i, $rate->id ), 
						'label' => sprintf ( '%s', esc_attr ( $rate->get_label () ) ), 
						'detail' => '', 
						'amount' => wc_stripe_add_number_precision ( $rate->cost ) 
				];
				$ids[] = $rate->id;
			}
			// Stripe always shows the first shipping option as selected. Make sure the chosen method
			// is first in the array.
			if (isset ( $chosen_shipping_methods[ $i ] )) {
				$index = array_search ( $chosen_shipping_methods[ $i ], $ids );
				if ($index != 0) {
					$temp = $methods[ 0 ];
					$methods[ 0 ] = $methods[ $index ];
					$methods[ $index ] = $temp;
				}
			}
		}
		if (empty ( $methods )) {
			// GPay does not like empty shipping methods. Make a temporary one;
			$methods[] = [ 'id' => 'default', 
					'label' => __ ( 'Waiting...', 'woo-stripe-payment' ), 
					'detail' => __ ( 'loading shipping methods...', 'woo-stripe-payment' ), 
					'amount' => 0 
			];
		}
	}
	$methods = apply_filters ( 'wc_stripe_get_shipping_options', $methods, $order );
	return $encode ? htmlspecialchars ( wp_json_encode ( $methods ) ) : $methods;
}

/**
 *
 * @since 3.0.0
 * @package Stripe/Functions
 */
function wc_stripe_set_checkout_error() {
	add_action ( 'woocommerce_after_template_part', 'wc_stripe_output_checkout_error' );
}

/**
 *
 * @since 3.0.0
 * @package Stripe/Functions
 * @param string $template_name        	
 */
function wc_stripe_output_checkout_error($template_name) {
	if ($template_name === 'notices/error.php' && is_ajax ()) {
		echo '<input type="hidden" id="wc_stripe_checkout_error" value="true"/>';
		remove_action ( 'woocommerce_after_template_part', 'wc_braintree_output_checkout_error' );
		add_filter ( 'wp_kses_allowed_html', 'wc_stripe_add_allowed_html', 10, 2 );
	}
}

/**
 *
 * @since 3.0.0
 * @package Stripe/Functions
 */
function wc_stripe_add_allowed_html($tags, $context) {
	if ($context === 'post') {
		$tags[ 'input' ] = array( 'id' => true, 
				'type' => true, 'value' => true 
		);
	}
	return $tags;
}

/**
 * Save WCS meta data when it's changed in the admin section.
 * By default WCS saves the
 * payment method title as the gateway title. This method saves the payment method title in
 * a human readable format suitable for the frontend.
 *
 * @package Stripe/Functions
 * @param int $post_id        	
 * @param WP_Post $post        	
 */
function wc_stripe_process_shop_subscription_meta($post_id, $post) {
	$subscription = wcs_get_subscription ( $post_id );
	$gateway_id = $subscription->get_payment_method ();
	$gateways = WC ()->payment_gateways ()->payment_gateways ();
	if (isset ( $gateways[ $gateway_id ] )) {
		$gateway = $gateways[ $gateway_id ];
		if ($gateway instanceof WC_Payment_Gateway_Stripe) {
			$token = $gateway->get_token ( $subscription->get_meta ( WC_Stripe_Constants::PAYMENT_METHOD_TOKEN ), $subscription->get_customer_id () );
			if ($token) {
				$subscription->set_payment_method_title ( $token->get_payment_method_title () );
				$subscription->save ();
			}
		}
	}
}

/**
 * Filter the WC payment gateways based on criteria specific to Stripe functionality.
 *
 * <strong>Example:</strong> on add payment method page, only show the CC gateway for Stripe.
 *
 * @since 3.0.0
 * @package Stripe/Functions
 * @param WC_Payment_Gateway[] $gateways        	
 */
function wc_stripe_available_payment_gateways($gateways) {
	global $wp;
	if (is_add_payment_method_page () && ! isset ( $wp->query_vars[ 'payment-methods' ] )) {
		foreach ( $gateways as $gateway ) {
			if ($gateway instanceof WC_Payment_Gateway_Stripe) {
				if ('stripe_cc' !== $gateway->id) {
					unset ( $gateways[ $gateway->id ] );
				}
			}
		}
	}
	return $gateways;
}

/**
 *
 * @since 3.0.0
 * @package Stripe/Functions
 * @return array
 */
function wc_stripe_get_local_payment_params() {
	$data = [];
	$gateways = WC ()->payment_gateways ()->payment_gateways ();
	foreach ( $gateways as $gateway ) {
		if ($gateway instanceof WC_Payment_Gateway_Stripe_Local_Payment && $gateway->is_available ()) {
			$data[ 'gateways' ][ $gateway->id ] = $gateway->get_localized_params ();
		}
	}
	$data[ 'api_key' ] = wc_stripe_get_publishable_key ();
	return $data;
}

/**
 *
 * @package Stripe/Functions
 * @since 3.0.0
 * @param array $gateways        	
 * @return WC_Payment_Gateway[]
 */
function wc_stripe_get_available_local_gateways($gateways) {
	foreach ( $gateways as $gateway ) {
		if ($gateway instanceof WC_Payment_Gateway_Stripe_Local_Payment) {
			if (! $gateway->is_local_payment_available ()) {
				unset ( $gateways[ $gateway->id ] );
			}
		}
	}
	return $gateways;
}

/**
 *
 * @since 3.0.0
 * @package Stripe/Functions
 * @param string|int $key        	
 */
function wc_stripe_set_idempotency_key($key) {
	global $wc_stripe_idempotency_key;
	$wc_stripe_idempotency_key = $key;
}

/**
 *
 * @since 3.0.0
 * @package Stripe/Functions
 * @return mixed
 */
function wc_stripe_get_idempotency_key() {
	global $wc_stripe_idempotency_key;
	return $wc_stripe_idempotency_key;
}

/**
 *
 * @since 3.0.0
 * @package Stripe/Functions
 * @param array $options        	
 * @return array
 */
function wc_stripe_api_options($options) {
	$key = wc_stripe_get_idempotency_key ();
	if ($key) {
		$options[ 'idempotency_key' ] = $key;
	}
	return $options;
}

/**
 *
 * @since 3.0.0
 * @package Stripe/Functions
 * @param string $order_status        	
 * @param int $order_id        	
 * @param WC_Order $order        	
 */
function wc_stripe_payment_complete_order_status($order_status, $order_id, $order) {
	if (is_checkout () && $order->get_payment_method ()) {
		$gateway = WC ()->payment_gateways ()->payment_gateways ()[ $order->get_payment_method () ];
		if ($gateway instanceof WC_Payment_Gateway_Stripe && 'default' !== $gateway->get_option ( 'order_status' )) {
			$order_status = $gateway->get_option ( 'order_status' );
		}
	}
	return $order_status;
}

/**
 * Converts the amount to cents.
 * Stripe processes all requests in cents.
 *
 * @since 3.0.0
 * @package Stripe/Functions
 * @param float $value        	
 * @param string $round        	
 * @return number
 */
function wc_stripe_add_number_precision($value, $round = true) {
	$cent_precision = pow ( 10, 2 );
	$value = $value * $cent_precision;
	$value = $round ? round ( $value, wc_get_rounding_precision () - wc_get_price_decimals () ) : $value;
	
	if (is_numeric ( $value ) && floor ( $value ) != $value) {
		// there are some decimal points that need to be removed.
		$value = round ( $value );
	}
	return $value;
}

/**
 * Return an array of credit card forms.
 *
 * @since 3.0.0
 * @package Stripe/Functions
 * @return mixed
 */
function wc_stripe_get_custom_forms() {
	return apply_filters ( 'wc_stripe_get_custom_forms', [ 
			'bootstrap' => [ 
					'template' => 'cc-forms/bootstrap.php', 
					'label' => __ ( 'Bootstrap form', 'woo-stripe-payment' ), 
					'cardBrand' => wc_stripe ()->assets_url ( 'img/card_brand2.svg' ), 
					'elementStyles' => [ 
							'base' => [ 
									'color' => '#495057', 
									'fontWeight' => 300, 
									'fontFamily' => 'Roboto, sans-serif, Source Code Pro, Consolas, Menlo, monospace', 
									'fontSize' => '16px', 
									'fontSmoothing' => 'antialiased', 
									'::placeholder' => [ 
											'color' => '#fff', 
											'fontSize' => '0px' 
									], 
									':-webkit-autofill' => [ 
											'color' => '#e39f48' 
									] 
							], 
							'invalid' => [ 
									'color' => '#E25950', 
									'::placeholder' => [ 
											'color' => '#757575' 
									] 
							] 
					], 
					'elementOptions' => [ 
							'fonts' => [ 
									[ 
											'cssSrc' => 'https://fonts.googleapis.com/css?family=Source+Code+Pro' 
									] 
							] 
					] 
			], 
			'simple' => [ 
					'template' => 'cc-forms/simple.php', 
					'label' => __ ( 'Simple form', 'woo-stripe-payment' ), 
					'cardBrand' => wc_stripe ()->assets_url ( 'img/card_brand2.svg' ), 
					'elementStyles' => [ 
							'base' => [ 
									'color' => '#32325D', 
									'fontWeight' => 500, 
									'fontFamily' => 'Source Code Pro, Consolas, Menlo, monospace', 
									'fontSize' => '16px', 
									'fontSmoothing' => 'antialiased', 
									'::placeholder' => [ 
											'color' => '#CFD7DF' 
									], 
									':-webkit-autofill' => [ 
											'color' => '#e39f48' 
									] 
							], 
							'invalid' => [ 
									'color' => '#E25950', 
									'::placeholder' => [ 
											'color' => '#FFCCA5' 
									] 
							] 
					], 
					'elementOptions' => [ 
							'fonts' => [ 
									[ 
											'cssSrc' => 'https://fonts.googleapis.com/css?family=Source+Code+Pro' 
									] 
							] 
					] 
			], 
			'minimalist' => [ 
					'template' => 'cc-forms/minimalist.php', 
					'label' => __ ( 'Minimalist form', 'woo-stripe-payment' ), 
					'cardBrand' => wc_stripe ()->assets_url ( 'img/card_brand2.svg' ), 
					'elementStyles' => [ 
							'base' => [ 
									'color' => '#495057', 
									'fontWeight' => 300, 
									'fontFamily' => 'Roboto, sans-serif, Source Code Pro, Consolas, Menlo, monospace', 
									'fontSize' => '30px', 
									'fontSmoothing' => 'antialiased', 
									'::placeholder' => [ 
											'color' => '#fff', 
											'fontSize' => '0px' 
									], 
									':-webkit-autofill' => [ 
											'color' => '#e39f48' 
									] 
							], 
							'invalid' => [ 
									'color' => '#495057', 
									'::placeholder' => [ 
											'color' => '#495057' 
									] 
							] 
					], 
					'elementOptions' => [ 
							'fonts' => [ 
									[ 
											'cssSrc' => 'https://fonts.googleapis.com/css?family=Source+Code+Pro' 
									] 
							] 
					] 
			], 
			'inline' => [ 
					'template' => 'cc-forms/inline.php', 
					'label' => __ ( 'Inline Form', 'woo-stripe-payment' ), 
					'cardBrand' => wc_stripe ()->assets_url ( 'img/card_brand.svg' ), 
					'elementStyles' => [ 
							'base' => [ 
									'color' => '#819efc', 
									'fontWeight' => 600, 
									'fontFamily' => 'Roboto, Open Sans, Segoe UI, sans-serif', 
									'fontSize' => '16px', 
									'fontSmoothing' => 'antialiased', 
									':focus' => [ 
											'color' => '#819efc' 
									], 
									'::placeholder' => [ 
											'color' => '#87BBFD' 
									], 
									':focus::placeholder' => [ 
											'color' => '#CFD7DF' 
									], 
									':-webkit-autofill' => [ 
											'color' => '#fce883' 
									] 
							], 
							'invalid' => [ 
									'color' => '#f99393' 
							] 
					], 
					'elementOptions' => [ 
							'fonts' => [ 
									[ 
											'cssSrc' => 'https://fonts.googleapis.com/css?family=Roboto' 
									] 
							] 
					] 
			], 
			'rounded' => [ 
					'template' => 'cc-forms/round.php', 
					'label' => __ ( 'Rounded Form', 'woo-stripe-payment' ), 
					'cardBrand' => wc_stripe ()->assets_url ( 'img/card_brand.svg' ), 
					'elementStyles' => [ 
							'base' => [ 
									'color' => '#fff', 
									'fontWeight' => 600, 
									'fontFamily' => 'Quicksand, Open Sans, Segoe UI, sans-serif', 
									'fontSize' => '16px', 
									'fontSmoothing' => 'antialiased', 
									':focus' => [ 
											'color' => '#424770' 
									], 
									'::placeholder' => [ 
											'color' => '#9BACC8' 
									], 
									':focus::placeholder' => [ 
											'color' => '#CFD7DF' 
									], 
									':-webkit-autofill' => [ 
											'color' => '#e39f48' 
									] 
							], 
							'invalid' => [ 
									'color' => '#fff', 
									':focus' => [ 
											'color' => '#FA755A' 
									], 
									'::placeholder' => [ 
											'color' => '#FFCCA5' 
									] 
							] 
					], 
					'elementOptions' => [ 
							'fonts' => [ 
									[ 
											'cssSrc' => 'https://fonts.googleapis.com/css?family=Quicksand' 
									] 
							] 
					] 
			] 
	] );
}

/**
 *
 * @package Stripe/Functions
 * @since 3.0.0
 * @param WC_Order $order        	
 */
function wc_stripe_order_has_shipping_address($order) {
	if (method_exists ( $order, 'has_shipping_address' )) {
		return $order->has_shipping_address ();
	} else {
		return $order->get_shipping_address_1 () || $order->get_shipping_address_2 ();
	}
}

/**
 *
 * @since 3.0.0
 * @package Stripe/Functions
 */
function wc_stripe_display_prices_including_tax() {
	$cart = WC ()->cart;
	if (method_exists ( $cart, 'display_prices_including_tax' )) {
		return $cart->display_prices_including_tax ();
	} else {
		$customer = WC ()->customer;
		$customer_exempt = $customer && $customer->get_is_vat_exempt ();
		'incl' === $cart->tax_display_cart && ! $customer_exempt;
	}
}

/**
 * Return true if the WC pre-orders plugin is active
 *
 * @since 3.0.1
 * @package Stripe/Functions
 */
function wc_stripe_pre_orders_active() {
	return class_exists ( 'WC_Pre_Orders' );
}

/**
 *
 * @since 3.0.5
 * @param string $source_id        	
 */
function wc_stripe_get_order_from_source_id($source_id) {
	global $wpdb;
	$order_id = $wpdb->get_var ( $wpdb->prepare ( "SELECT ID FROM {$wpdb->posts} AS posts LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id WHERE meta.meta_key = %s AND meta.meta_value = %s LIMIT 1", '_stripe_source_id', $source_id ) );
	return wc_get_order ( $order_id );
}

/**
 *
 * @since 3.0.5
 * @param string $transaction_id        	
 * @return WC_Order|WC_Refund|boolean|WC_Order_Refund
 */
function wc_stripe_get_order_from_transaction($transaction_id) {
	global $wpdb;
	$order_id = $wpdb->get_var ( $wpdb->prepare ( "SELECT ID FROM {$wpdb->posts} AS posts LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id WHERE meta.meta_key = %s AND meta.meta_value = %s LIMIT 1", '_transaction_id', $transaction_id ) );
	return wc_get_order ( $order_id );
}

/**
 * Stash the WC cart contents in the session and empty it's contents.
 * If $product_cart is true, add the stashed product(s)
 * to the cart.
 *
 * @since 3.0.6
 * @todo Maybe empty cart silently so actions are not triggered that cause session data to be removed
 *       from 3rd party plugins.
 *      
 * @param WC_Cart $cart        	
 */
function wc_stripe_stash_cart($cart, $product_cart = true) {
	$data = WC ()->session->get ( 'wc_stripe_cart', [] );
	$data[ 'cart' ] = $cart->get_cart_for_session ();
	WC ()->session->set ( 'wc_stripe_cart', $data );
	$cart->empty_cart ( false );
	if ($product_cart && isset ( $data[ 'product_cart' ] )) {
		foreach ( $data[ 'product_cart' ] as $cart_item ) {
			$cart->add_to_cart ( $cart_item[ 'product_id' ], $cart_item[ 'quantity' ], $cart_item[ 'variation_id' ] );
		}
	}
}

/**
 *
 * @since 3.0.6
 * @param number $product_id        	
 * @param number $qty        	
 * @param number $variation_id        	
 */
function wc_stripe_stash_product_cart($cart) {
	$data = WC ()->session->get ( 'wc_stripe_cart', [] );
	$data[ 'product_cart' ] = $cart->get_cart_for_session ();
	WC ()->session->set ( 'wc_stripe_cart', $data );
	WC ()->cart->set_session ();
}

/**
 *
 * @since 3.0.6
 * @param WC_Cart $cart        	
 */
function wc_stripe_restore_cart($cart) {
	$data = WC ()->session->get ( 'wc_stripe_cart', [ 
			'cart' => [] 
	] );
	$cart->cart_contents = $data[ 'cart' ];
	$cart->set_session ();
}

/**
 *
 * @since 3.0.6
 */
function wc_stripe_restore_cart_after_product_checkout() {
	wc_stripe_restore_cart ( WC ()->cart );
	$cart_contents = [];
	foreach ( WC ()->cart->get_cart () as $key => $cart_item ) {
		$cart_item[ 'data' ] = wc_get_product ( $cart_item[ 'variation_id' ] ? $cart_item[ 'variation_id' ] : $cart_item[ 'product_id' ] );
		$cart_contents[ $key ] = $cart_item;
	}
	WC ()->cart->cart_contents = $cart_contents;
	WC ()->cart->calculate_totals ();
}

/**
 *
 * @since 3.1.0
 * @param WC_Payment_Token[] $tokens        	
 * @param int $user_id        	
 * @param string $gateway_id        	
 * @return WC_Payment_Token[]
 */
function wc_stripe_get_customer_payment_tokens($tokens, $user_id, $gateway_id) {
	foreach ( $tokens as $idx => $token ) {
		if ($token instanceof WC_Payment_Token_Stripe) {
			$mode = wc_stripe_mode ();
			if ($token->get_environment () != $mode) {
				unset ( $tokens[ $idx ] );
			}
		}
	}
	return $tokens;
}

/**
 *
 * @since 3.1.0
 * @param array $labels        	
 * @return string
 */
function wc_stripe_credit_card_labels($labels) {
	if (! isset ( $labels[ 'amex' ] )) {
		$labels[ 'amex' ] = __ ( 'Amex', 'woocommerce' );
	}
	return $labels;
}