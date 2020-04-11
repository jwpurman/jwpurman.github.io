<?php
if (! class_exists ( 'WC_Payment_Gateway_Stripe' )) {
	return;
}
/**
 *
 * @author PaymentPlugins
 * @since 3.0.0
 * @package Stripe/Gateways
 *         
 */
class WC_Payment_Gateway_Stripe_GooglePay extends WC_Payment_Gateway_Stripe {
	
	use WC_Stripe_Payment_Charge_Trait;

	public function __construct() {
		$this->id = 'stripe_googlepay';
		$this->tab_title = __ ( 'Google Pay', 'woo-stripe-payment' );
		$this->template_name = 'googlepay.php';
		$this->token_type = 'Stripe_GooglePay';
		$this->method_title = __ ( 'Stripe Google Pay', 'woo-stripe-payment' );
		$this->method_description = __ ( 'Google Pay gateway that integrates with your Stripe account.', 'woo-stripe-payment' );
		parent::__construct ();
		$this->icon = wc_stripe ()->assets_url ( 'img/' . $this->get_option ( 'icon' ) . '.svg' );
	}

	public function init_supports() {
		parent::init_supports ();
		$this->supports[] = 'wc_stripe_cart_checkout';
		$this->supports[] = 'wc_stripe_product_checkout';
		$this->supports[] = 'wc_stripe_banner_checkout';
	}

	public function enqueue_checkout_scripts($scripts) {
		$scripts->enqueue_script ( 'googlepay-checkout', $scripts->assets_url ( 'js/frontend/googlepay-checkout.js' ), array( 
				$scripts->get_handle ( 'wc-stripe' ), 
				$scripts->get_handle ( 'gpay' ) 
		), wc_stripe ()->version (), true );
		$scripts->localize_script ( 'googlepay-checkout', $this->get_localized_params () );
	}

	public function enqueue_product_scripts($scripts) {
		$scripts->enqueue_script ( 'googlepay-product', $scripts->assets_url ( 'js/frontend/googlepay-product.js' ), array( 
				$scripts->get_handle ( 'wc-stripe' ), 
				$scripts->get_handle ( 'gpay' ) 
		), wc_stripe ()->version (), true );
		$scripts->localize_script ( 'googlepay-product', $this->get_localized_params () );
	}

	public function enqueue_cart_scripts($scripts) {
		$scripts->enqueue_script ( 'googlepay-cart', $scripts->assets_url ( 'js/frontend/googlepay-cart.js' ), array( 
				$scripts->get_handle ( 'wc-stripe' ), 
				$scripts->get_handle ( 'gpay' ) 
		), wc_stripe ()->version (), true );
		$scripts->localize_script ( 'googlepay-cart', $this->get_localized_params () );
	}

	public function enqueue_admin_scripts() {
		wp_register_script ( 'gpay-external', wc_stripe ()->scripts ()->global_scripts[ 'gpay' ], [], wc_stripe ()->version (), true );
		wp_enqueue_script ( 'wc-stripe-gpay-admin', wc_stripe ()->assets_url ( 'js/admin/googlepay.js' ), [ 
				'gpay-external', 'wc-stripe-admin-settings' 
		], wc_stripe ()->version (), true );
	}

	public function get_localized_params() {
		$data = array_merge_recursive ( parent::get_localized_params (), [ 
				'environment' => wc_stripe_mode () === 'test' ? 'TEST' : 'PRODUCTION', 
				'merchant_id' => wc_stripe_mode () === 'test' ? '' : $this->get_option ( 'merchant_id' ), 
				'merchant_name' => $this->get_option ( 'merchant_name' ), 
				'button_color' => $this->get_option ( 'button_color' ), 
				'button_style' => $this->get_option ( 'button_style' ), 
				'total_price_label' => __ ( 'Total', 'woo-stripe-payment' ), 
				'routes' => [ 
						'payment_data' => wc_stripe ()->rest_api->googlepay->rest_url ( 'shipping-data' ) 
				], 
				'messages' => [ 
						'invalid_amount' => __ ( 'Please update you product quantity before using Google Pay.', 'woo-stripe-payment' ) 
				], 
				'dynamic_price' => $this->is_active ( 'dynamic_price' ) 
		] );
		return $data;
	}

	public function get_display_items($encode = false) {
		$items = [];
		global $wp;
		if (wcs_stripe_active () && WC_Subscriptions_Change_Payment_Gateway::$is_request_to_change_payment) {
			$subscription = wcs_get_subscription ( absint ( $_GET[ 'change_payment_method' ] ) );
			$items[] = [ 
					'label' => __ ( 'Subscription', 'woo-stripe-payment' ), 
					'type' => 'SUBTOTAL', 
					'price' => strval ( $subscription->get_total () ) 
			];
		} elseif (is_product ()) {
			global $product;
			$items[] = [ 
					'label' => esc_attr ( $product->get_name () ), 
					'type' => 'LINE_ITEM', 
					'price' => strval ( $product->get_price () ) 
			];
		} elseif (is_checkout () && isset ( $wp->query_vars[ 'order-pay' ] )) {
			$order = wc_get_order ( absint ( $wp->query_vars[ 'order-pay' ] ) );
			// add all order items
			foreach ( $order->get_items () as $item ) {
				/**
				 *
				 * @var WC_Order_Item_Product $item
				 */
				$qty = $item->get_quantity ();
				
				$items[] = [ 
						'label' => $qty > 1 ? sprintf ( '%s X %s', $item->get_name (), $qty ) : $item->get_name (), 
						'type' => 'LINE_ITEM', 
						'price' => strval ( $item->get_subtotal () ) 
				];
			}
			// shipping total
			if ($order->get_shipping_total ()) {
				$items[] = [ 
						'label' => __ ( 'Shipping', 'woo-stripe-payment' ), 
						'type' => 'LINE_ITEM', 
						'price' => strval ( $order->get_shipping_total () ) 
				];
			}
			// discount total
			if ($order->get_total_discount ()) {
				$items[] = [ 
						'label' => __ ( 'Discount', 'woo-stripe-payment' ), 
						'type' => 'LINE_ITEM', 
						'price' => strval ( $order->get_total_discount () ) 
				];
			}
			if ($order->get_fees ()) {
				$fee_total = 0;
				foreach ( $order->get_fees () as $fee ) {
					$fee_total += $fee->get_total ();
				}
				$items[] = [ 
						'label' => __ ( 'Fees', 'woo-stripe-payment' ), 
						'type' => 'LINE_ITEM', 
						'price' => strval ( $fee_total ) 
				];
			}
			// tax total
			if ($order->get_total_tax ()) {
				$items[] = [ 
						'label' => __ ( 'Tax', 'woocommerce' ), 
						'type' => 'TAX', 
						'price' => strval ( $order->get_total_tax () ) 
				];
			}
		} else {
			foreach ( WC ()->cart->get_cart () as $cart_item ) {
				/**
				 *
				 * @var WC_Product $product
				 */
				$product = $cart_item[ 'data' ];
				$qty = $cart_item[ 'quantity' ];
				$items[] = [ 
						'label' => $qty > 1 ? sprintf ( '%s X %s', $product->get_name (), $qty ) : $product->get_name (), 
						'type' => 'LINE_ITEM', 
						'price' => strval ( round ( $product->get_price () * $qty, 2, PHP_ROUND_HALF_UP ) ) 
				];
			}
			if (WC ()->cart->needs_shipping ()) {
				$items[] = [ 
						'label' => __ ( 'Shipping', 'woo-stripe-payment' ), 
						'type' => 'LINE_ITEM', 
						'price' => strval ( round ( WC ()->cart->shipping_total, wc_get_price_decimals () ) ) 
				];
			}
			
			// fees
			foreach ( WC ()->cart->get_fees () as $fee_item ) {
				/**
				 *
				 * @var WC_Order_I
				 */
				$items[] = [ 'label' => $fee_item->name, 
						'type' => 'LINE_ITEM', 
						'price' => strval ( $fee_item->total ) 
				];
			}
			
			// coupons
			if (WC ()->cart->discount_cart != 0) {
				$items[] = [ 
						'label' => __ ( 'Discount', 'woo-stripe-payment' ), 
						'type' => 'LINE_ITEM', 
						'price' => strval ( - 1 * abs ( WC ()->cart->discount_cart ) ) 
				];
			}
			
			if (wc_tax_enabled ()) {
				$items[] = [ 
						'label' => __ ( 'Tax', 'woo-stripe-payment' ), 
						'type' => 'TAX', 
						'price' => strval ( WC ()->cart->get_taxes_total () ) 
				];
			}
		}
		return $encode ? htmlspecialchars ( json_encode ( $items ) ) : $items;
	}

	public function get_shipping_methods($encode = false) {
		if (wcs_stripe_active () && WC_Subscriptions_Change_Payment_Gateway::$is_request_to_change_payment) {
			$methods = [];
		} else {
			$methods = [];
			$packages = WC ()->shipping ()->get_packages ();
			foreach ( $packages as $i => $package ) {
				foreach ( $package[ 'rates' ] as $rate ) {
					/**
					 *
					 * @var WC_Shipping_Rate $rate
					 */
					$methods[] = [ 
							'id' => sprintf ( '%s:%s', $i, $rate->id ), 
							'label' => $this->get_shipping_method_label ( $rate ), 
							'description' => '' 
					];
				}
			}
			if (empty ( $methods )) {
				// GPay does not like empty shipping methods. Make a temporary one;
				$methods[] = [ 'id' => 'default', 
						'label' => __ ( 'Waiting...', 'woo-stripe-payment' ), 
						'description' => __ ( 'loading shipping methods...', 'woo-stripe-payment' ) 
				];
			}
		}
		return $encode ? htmlspecialchars ( json_encode ( $methods ) ) : $methods;
	}

	/**
	 * Return a formatted shipping method label.
	 * <strong>Example</strong>&nbsp;5 Day shipping: 5 USD
	 *
	 * @param WC_Shipping_Rate $rate        	
	 * @return
	 *
	 */
	public function get_shipping_method_label($rate) {
		if (wc_stripe_display_prices_including_tax ()) {
			$total = $rate->cost + $rate->get_shipping_tax ();
		} else {
			$total = $rate->cost;
		}
		return sprintf ( '%s: %s %s', $rate->get_label (), $total, get_woocommerce_currency () );
	}

	public function add_to_cart_response($data) {
		$data[ 'googlepay' ][ 'displayItems' ] = $this->get_display_items ();
		return $data;
	}
}