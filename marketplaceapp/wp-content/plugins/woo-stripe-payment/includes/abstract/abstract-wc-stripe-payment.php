<?php
/**
 * 
 * @author PaymentPlugins
 * @since 3.1.0
 *
 */
abstract class WC_Stripe_Payment {

	/**
	 *
	 * @var WC_Payment_Gateway_Stripe
	 */
	protected $payment_method;

	/**
	 *
	 * @var WC_Stripe_Gateway
	 */
	protected $gateway;

	/**
	 *
	 * @param WC_Payment_Gateway_Stripe $payment_method        	
	 * @param WC_Stripe_Gateway $gateway        	
	 */
	public function __construct($payment_method, $gateway) {
		$this->payment_method = $payment_method;
		$this->gateway = $gateway;
	}

	public function get_gateway() {
		return $this->gateway;
	}

	/**
	 * Process the payment for the order.
	 *
	 * @param WC_Order $order        	
	 * @param WC_Payment_Gateway_Stripe $payment_method        	
	 */
	public abstract function process_payment($order);

	/**
	 *
	 * @param float $amount        	
	 * @param WC_Order $order        	
	 */
	public abstract function capture_charge($amount, $order);

	/**
	 *
	 * @param WC_Order $order        	
	 */
	public abstract function void_charge($order);

	/**
	 *
	 * @param \Stripe\Charge $charge        	
	 */
	public abstract function get_payment_method_from_charge($charge);

	/**
	 *
	 * @param array $args        	
	 * @param WC_Order $order        	
	 */
	public abstract function add_order_payment_method(&$args, $order);

	/**
	 *
	 * @param float $amount        	
	 * @param WC_Order $order        	
	 */
	public abstract function scheduled_subscription_payment($amount, $order);

	/**
	 *
	 * @param WC_Order $order        	
	 */
	public abstract function process_pre_order_payment($order);

	/**
	 *
	 * @param WC_Order $order        	
	 * @param float $amount        	
	 * @param string $reason        	
	 * @throws Exception
	 */
	public function process_refund($order, $amount = null, $reason = '') {
		$amount_in_cents = wc_stripe_add_number_precision ( $amount );
		$charge = $order->get_transaction_id ();
		try {
			if (empty ( $charge )) {
				throw new Exception ( __ ( 'Transaction Id cannot be empty.', 'woo-stripe-payment' ) );
			}
			$result = $this->gateway->refund ( array( 
					'charge' => $charge, 
					'amount' => $amount_in_cents 
			), wc_stripe_order_mode ( $order ) );
			if (! is_wp_error ( $result )) {
				return true;
			}
			return $result;
		} catch ( Exception $e ) {
			return new WP_Error ( 'refund-error', $e->getMessage () );
		}
	}

	/**
	 * Return a failed order response.
	 *
	 * @return array
	 */
	public function order_error() {
		wc_stripe_set_checkout_error ();
		return [ 'result' => 'failure' 
		];
	}

	/**
	 *
	 * @param array $args        	
	 * @param WC_Order $order        	
	 */
	public function add_general_order_args(&$args, $order) {
		$this->add_order_amount ( $args, $order );
		$this->add_order_currency ( $args, $order );
		$this->add_order_description ( $args, $order );
		$this->add_order_shipping_address ( $args, $order );
		$this->add_order_metadata ( $args, $order );
		$this->add_order_payment_method ( $args, $order );
	}

	/**
	 *
	 * @param array $args        	
	 * @param WC_Order $order        	
	 */
	public function add_order_metadata(&$args, $order) {
		$meta_data = [ 
				'gateway_id' => $this->payment_method->id, 
				'order_id' => $order->get_order_number (), 
				'user_id' => $order->get_user_id (), 
				'customer_id' => wc_stripe_get_customer_id ( $order->get_user_id () ), 
				'ip_address' => $order->get_customer_ip_address (), 
				'user_agent' => isset ( $_SERVER[ 'HTTP_USER_AGENT' ] ) ? $_SERVER[ 'HTTP_USER_AGENT' ] : 'unavailable', 
				'partner' => 'PaymentPlugins' 
		];
		foreach ( $order->get_items ( 'line_item' ) as $item ) {
			/**
			 *
			 * @var WC_Order_Item_Product $item
			 */
			$meta_data[ 'product_' . $item->get_product_id () ] = sprintf ( '%s x %s', $item->get_name (), $item->get_quantity () );
		}
		$args[ 'metadata' ] = apply_filters ( 'wc_stripe_order_meta_data', $meta_data, $order );
	}

	/**
	 *
	 * @param array $args        	
	 * @param WC_Order $order        	
	 */
	public function add_order_description(&$args, $order) {
		$args[ 'description' ] = sprintf ( __ ( 'Order %s from %s', 'woo-stripe-payment' ), $order->get_order_number (), get_bloginfo ( 'name' ) );
	}

	/**
	 *
	 * @param array $args        	
	 * @param WC_Order $order        	
	 * @param float $amount        	
	 */
	public function add_order_amount(&$args, $order, $amount = null) {
		$args[ 'amount' ] = wc_stripe_add_number_precision ( $amount ? $amount : $order->get_total () );
	}

	/**
	 *
	 * @param array $args        	
	 * @param WC_Order $order        	
	 */
	public function add_order_currency(&$args, $order) {
		$args[ 'currency' ] = $order->get_currency ();
	}

	/**
	 *
	 * @param array $args        	
	 * @param WC_Order $order        	
	 */
	public function add_order_shipping_address(&$args, $order) {
		if (wc_stripe_order_has_shipping_address ( $order )) {
			$args[ 'shipping' ] = [ 
					'address' => [ 
							'city' => $order->get_shipping_city (), 
							'country' => $order->get_shipping_country (), 
							'line1' => $order->get_shipping_address_1 (), 
							'line2' => $order->get_shipping_address_2 (), 
							'postal_code' => $order->get_shipping_postcode (), 
							'state' => $order->get_shipping_state () 
					], 
					'name' => $this->get_name_from_order ( $order, 'shipping' ) 
			];
		} else {
			$args[ 'shipping' ] = [];
		}
	}

	/**
	 *
	 * @param WC_Order $order        	
	 */
	public function get_name_from_order($order, $type) {
		if ($type === 'billing') {
			return sprintf ( '%s %s', $order->get_billing_first_name (), $order->get_billing_last_name () );
		} else {
			return sprintf ( '%s %s', $order->get_shipping_first_name (), $order->get_shipping_last_name () );
		}
	}
}