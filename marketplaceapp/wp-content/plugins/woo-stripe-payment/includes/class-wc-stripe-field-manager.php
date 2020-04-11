<?php
/**
 * @since 3.0.0
 * @package Stripe/Classes
 * @author Payment Plugins
 *
 */
class WC_Stripe_Field_Manager {

	private static $_cart_priority = 30;

	public static function init() {
		add_action ( 'woocommerce_checkout_before_customer_details', array( 
				__CLASS__, 'output_banner_checkout_fields' 
		) );
		add_action ( 'woocommerce_after_add_to_cart_button', array( 
				__CLASS__, 'output_product_checkout_fields' 
		) );
		add_action ( 'init', [ __CLASS__, 
				'init_action' 
		] );
		add_action ( 'woocommerce_review_order_after_order_total', [ 
				__CLASS__, 'output_checkout_fields' 
		] );
		add_action ( 'before_woocommerce_add_payment_method', array( 
				__CLASS__, 'add_payment_method_fields' 
		) );
		add_action ( 'before_woocommerce_pay', array( 
				__CLASS__, 'change_payment_request' 
		) );
		/*
		 * add_action ( 'woocommerce_quantity_input_args', array(
		 * __CLASS__, 'quantity_input_value'
		 * ) );
		 */
		add_action ( 'woocommerce_pay_order_after_submit', array( 
				__CLASS__, 'pay_order_fields' 
		) );
	}

	public static function init_action() {
		self::$_cart_priority = apply_filters ( 'wc_stripe_cart_buttons_order', 30 );
		add_action ( 'woocommerce_proceed_to_checkout', [ 
				__CLASS__, 'output_cart_fields' 
		], self::$_cart_priority );
	}

	public static function output_banner_checkout_fields() {
		$gateways = [];
		foreach ( WC ()->payment_gateways ()->get_available_payment_gateways () as $gateway ) {
			if ($gateway->supports ( 'wc_stripe_banner_checkout' ) && $gateway->banner_checkout_enabled ()) {
				$gateways[ $gateway->id ] = $gateway;
			}
		}
		if ($gateways) {
			wc_stripe_get_template ( 'checkout/checkout-banner.php', [ 
					'gateways' => $gateways 
			] );
		}
	}

	public static function output_checkout_fields() {
		// now that sources have been printed, clear them.
		WC ()->session->set ( 'wc_stripe_local_payment_sources', [] );
		
		self::output_required_fields ();
		
		wp_localize_script ( 'wc-checkout', 'wc_stripe_checkout_fields', [ 
				'billing' => WC ()->checkout ()->get_checkout_fields ( 'billing' ), 
				'shipping' => WC ()->checkout ()->get_checkout_fields ( 'shipping' ) 
		] );
		
		do_action ( 'wc_stripe_output_checkout_fields' );
	}

	public static function output_product_checkout_fields() {
		global $product;
		$gateways = [];
		foreach ( WC ()->payment_gateways ()->get_available_payment_gateways () as $id => $gateway ) {
			/**
			 *
			 * @var WC_Payment_Gateway_Stripe $gateway
			 */
			if ($gateway->supports ( 'wc_stripe_product_checkout' ) && $gateway->product_checkout_enabled ()) {
				$gateways[ $gateway->id ] = $gateway;
			}
		}
		
		if (count ( $gateways ) > 0) {
			
			self::output_required_fields ();
			printf ( '<input type="hidden" id="product_id", value="%s"/>', $product->get_id () );
			printf ( '<input type="hidden" id="wc_stripe_product_data" data-product="%s"/>', htmlspecialchars ( wp_json_encode ( [ 
					'product_id' => $product->get_id (), 
					'price' => $product->get_price (), 
					'needs_shipping' => $product->needs_shipping () 
			] ) ) );
			printf ( '<input type="hidden" id="wc_stripe_display_items" data-items="%s"/>', htmlspecialchars ( wp_json_encode ( [ 
					[ 
							'amount' => wc_stripe_add_number_precision ( $product->get_price () ), 
							'label' => esc_attr ( $product->get_name () ), 
							'pending' => true 
					] 
			] ) ) );
			printf ( '<input type="hidden" id="wc_stripe_shipping_options" data-items="%s"/>', htmlspecialchars ( wp_json_encode ( [] ) ) );
			printf ( '<input type="hidden" id="wc_stripe_order_total_cents" data-amount="%s"/>', wc_stripe_add_number_precision ( $product->get_price () ) );
			
			self::output_fields ( 'billing' );
			
			// don't always need shipping fields but doesn't hurt to output them anyway.
			self::output_fields ( 'shipping' );
			
			wc_stripe_get_template ( 'product/payment-methods.php', [ 
					'gateways' => $gateways 
			] );
		}
	}

	public static function output_cart_fields() {
		$gateways = [];
		foreach ( WC ()->payment_gateways ()->get_available_payment_gateways () as $id => $gateway ) {
			/**
			 *
			 * @var WC_Payment_Gateway_Stripe $gateway
			 */
			if ($gateway->supports ( 'wc_stripe_cart_checkout' ) && $gateway->cart_checkout_enabled ()) {
				$gateways[ $gateway->id ] = $gateway;
			}
		}
		if (! empty ( $gateways )) {
			echo '<form id="wc-stripe-cart-form">';
			self::output_required_fields ();
			
			self::output_fields ( 'billing' );
			
			if (WC ()->cart->needs_shipping ()) {
				self::output_needs_shipping ( true );
				self::output_fields ( 'shipping' );
			} else {
				self::output_needs_shipping ( false );
			}
			
			wc_stripe_get_template ( 'cart/payment-methods.php', [ 
					'gateways' => $gateways, 
					'after' => self::$_cart_priority > 20, 
					'cart_total' => WC ()->cart->total 
			] );
			echo '</form>';
		}
	}

	public static function change_payment_request() {
		if (wcs_stripe_active () && WC_Subscriptions_Change_Payment_Gateway::$is_request_to_change_payment) {
			$subscription = wcs_get_subscription ( absint ( $_GET[ 'change_payment_method' ] ) );
			self::output_required_fields ( $subscription );
		}
	}

	public static function add_payment_method_fields() {
		wc_stripe_hidden_field ( 'billing_first_name', '', WC ()->customer->get_first_name () );
		wc_stripe_hidden_field ( 'billing_last_name', '', WC ()->customer->get_last_name () );
	}

	public static function pay_order_fields() {
		global $wp;
		$order = wc_get_order ( absint ( $wp->query_vars[ 'order-pay' ] ) );
		self::output_required_fields ( $order );
	}

	/**
	 *
	 * @param WC_Order $order        	
	 */
	public static function output_required_fields($order = null) {
		printf ( '<input type="hidden" id="%1$s" value="%2$s"/>', 'wc_stripe_currency', get_woocommerce_currency () );
		printf ( '<input type="hidden" id="%1$s" data-amount="%2$s"/>', 'wc_stripe_order_total', $order ? $order->get_total () : WC ()->cart->total );
		if (is_cart () || is_checkout ()) {
			printf ( '<input type="hidden" id="wc_stripe_display_items" data-items="%s"/>', wc_stripe_get_display_items ( true, $order ) );
			printf ( '<input type="hidden" id="wc_stripe_shipping_options" data-items="%s"/>', wc_stripe_get_shipping_options ( true, $order ) );
			printf ( '<input type="hidden" id="wc_stripe_order_total_cents" data-amount="%s"/>', wc_stripe_add_number_precision ( $order ? $order->get_total () : WC ()->cart->total ) );
		}
	}

	public static function output_fields($prefix) {
		$fields = WC ()->checkout ()->get_checkout_fields ( $prefix );
		foreach ( $fields as $key => $field ) {
			printf ( '<input type="hidden" id="%1$s" name="%1$s" value="%2$s"/>', $key, WC ()->checkout ()->get_value ( $key ) );
		}
	}

	public static function output_needs_shipping($needs_shipping) {
		printf ( '<input type="hidden" id="wc_stripe_needs_shipping" data-value="%s" />', $needs_shipping );
	}

	/**
	 *
	 * @deprecated
	 *
	 * @param array $args        	
	 */
	public static function quantity_input_value($args) {
		if (is_product ()) {
			foreach ( WC ()->payment_gateways ()->get_available_payment_gateways () as $id => $gateway ) {
				if ($gateway->supports ( 'wc_stripe_product_checkout' )) {
					$args[ 'min_value' ] = 0;
					break;
				}
			}
		}
		return $args;
	}
}
if (! is_admin ()) {
	WC_Stripe_Field_Manager::init ();
}