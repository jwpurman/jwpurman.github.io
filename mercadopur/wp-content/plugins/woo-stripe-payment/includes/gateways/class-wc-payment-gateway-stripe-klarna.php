<?php
if (! class_exists ( 'WC_Payment_Gateway_Stripe_Local_Payment' )) {
	return;
}
/**
 *
 * @package Stripe/Gateways
 * @author PaymentPlugins
 *        
 */
class WC_Payment_Gateway_Stripe_Klarna extends WC_Payment_Gateway_Stripe_Local_Payment {
	
	use WC_Stripe_Local_Payment_Charge_Trait;

	public function __construct() {
		$this->local_payment_type = 'klarna';
		$this->currencies = [ 'EUR', 'SEK', 'NOK', 
				'DKK', 'GBP', 'USD' 
		];
		$this->id = 'stripe_klarna';
		$this->tab_title = __ ( 'Klarna', 'woo-stripe-payment' );
		$this->template_name = 'local-payment.php';
		$this->token_type = 'Stripe_Local';
		$this->method_title = __ ( 'Klarna', 'woo-stripe-payment' );
		$this->method_description = __ ( 'Klarna gateway that integrates with your Stripe account.', 'woo-stripe-payment' );
		$this->icon = wc_stripe ()->assets_url ( 'img/klarna.svg' );
		$this->order_button_text = __ ( 'Klarna', 'woo-stripe-payment' );
		parent::__construct ();
		$this->template_name = 'klarna.php';
	}

	public function hooks() {
		parent::hooks ();
		add_action ( 'woocommerce_review_order_before_payment', array( 
				$this, 'enqueue_klarna' 
		) );
	}

	/**
	 * Enqueue Klarna based on whether it's available or not.
	 */
	public function enqueue_klarna() {
		if ($this->is_available ()) {
			wc_stripe ()->scripts ()->enqueue_script ( 'klarna', 'https://x.klarnacdn.net/kp/lib/v1/api.js', [], wc_stripe ()->version (), true );
		}
	}

	public function get_required_parameters() {
		return [ 'USD' => [ 'US' 
		], 'EUR' => [ 'AT', 'FI', 'DE', 'NL' 
		], 'DKK' => [ 'DK' 
		], 'NOK' => [ 'NO' 
		], 'SEK' => [ 'SE' 
		], 'GBP' => [ 'GB' 
		] 
		];
	}

	public function is_local_payment_available() {
		$currency = get_woocommerce_currency ();
		$country = WC ()->customer ? WC ()->customer->get_billing_country () : null;
		if ($country) {
			$params = $this->get_required_parameters ();
			return isset ( $params[ $currency ] ) && array_search ( $country, $params[ $currency ] ) !== false;
		}
		return false;
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see WC_Payment_Gateway_Stripe::payment_fields()
	 */
	public function payment_fields() {
		// this might be an update checkout request. If so, update the source if it exists
		if (is_ajax () && ( $order_id = absint ( WC ()->session->get ( 'order_awaiting_payment' ) ) )) {
			$order = wc_get_order ( $order_id );
			$source_id = $order->get_meta ( '_stripe_source_id', true );
			$this->gateway->update_source ( $source_id, $this->get_update_source_args ( $order ) );
		}
		parent::payment_fields ();
	}

	private function get_update_source_args($order) {
		$args = $this->get_source_args ( $order );
		unset ( $args[ 'type' ], $args[ 'currency' ], $args[ 'statement_descriptor' ], $args[ 'redirect' ], $args[ 'klarna' ][ 'product' ] );
		return $args;
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see WC_Payment_Gateway_Stripe_Local_Payment::get_source_args()
	 */
	public function get_source_args($order) {
		$args = array_merge_recursive ( parent::get_source_args ( $order ), [ 
				'klarna' => [ 'product' => 'payment', 
						'purchase_country' => $order->get_billing_country (), 
						'first_name' => $order->get_billing_first_name (), 
						'last_name' => $order->get_billing_last_name () 
				], 
				'owner' => [ 
						'address' => [ 
								'city' => $order->get_billing_city (), 
								'country' => $order->get_billing_country (), 
								'line1' => $order->get_billing_address_1 (), 
								'line2' => $order->get_billing_address_2 (), 
								'postal_code' => $order->get_billing_postcode (), 
								'state' => $order->get_billing_state () 
						] 
				] 
		] );
		$args[ 'source_order' ] = [];
		
		if ($order->get_shipping_address_1 ()) {
			$args[ 'klarna' ][ 'shipping_first_name' ] = $order->get_shipping_first_name ();
			$args[ 'klarna' ][ 'shipping_last_name' ] = $order->get_shipping_last_name ();
			$args[ 'source_order' ][ 'shipping' ][ 'address' ] = [ 
					'city' => $order->get_billing_city (), 
					'country' => $order->get_shipping_country (), 
					'line1' => $order->get_shipping_address_1 (), 
					'line2' => $order->get_shipping_address_2 (), 
					'postal_code' => $order->get_shipping_postcode (), 
					'state' => $order->get_shipping_state () 
			];
		}
		
		foreach ( $order->get_items ( 'line_item' ) as $item ) {
			/**
			 *
			 * @var WC_Order_Item_Product $item
			 */
			$args[ 'source_order' ][ 'items' ][] = [ 
					'type' => 'sku', 
					'amount' => wc_stripe_add_number_precision ( $item->get_subtotal () ), 
					'currency' => $order->get_currency (), 
					'quantity' => $item->get_quantity (), 
					'description' => $item->get_name () 
			];
		}
		// shipping
		if ($order->get_shipping_total ()) {
			$args[ 'source_order' ][ 'items' ][] = [ 
					'type' => 'shipping', 
					'amount' => wc_stripe_add_number_precision ( $order->get_shipping_total () ), 
					'currency' => $order->get_currency (), 
					'quantity' => 1, 
					'description' => __ ( 'Shipping', 'woo-stripe-payment' ) 
			];
		}
		// discount
		if ($order->get_discount_total ()) {
			$args[ 'source_order' ][ 'items' ][] = [ 
					'type' => 'discount', 
					'amount' => - 1 * wc_stripe_add_number_precision ( $order->get_discount_total () ), 
					'currency' => $order->get_currency (), 
					'quantity' => 1, 
					'description' => __ ( 'Discount', 'woo-stripe-payment' ) 
			];
		}
		// fees
		if ($order->get_fees ()) {
			$fee_total = 0;
			foreach ( $order->get_fees () as $fee ) {
				$fee_total += wc_stripe_add_number_precision ( $fee->get_total () );
			}
			$args[ 'source_order' ][ 'items' ][] = [ 
					'type' => 'sku', 'amount' => $fee_total, 
					'currency' => $order->get_currency (), 
					'quantity' => 1, 
					'description' => __ ( 'Fee', 'woo-stripe-payment' ) 
			];
		}
		// tax
		if ($order->get_total_tax ()) {
			$args[ 'source_order' ][ 'items' ][] = [ 
					'type' => 'tax', 
					'amount' => wc_stripe_add_number_precision ( $order->get_total_tax () ), 
					'description' => __ ( 'Tax', 'woo-stripe-payment' ), 
					'quantity' => 1, 
					'currency' => $order->get_currency () 
			];
		}
		return $args;
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see WC_Payment_Gateway_Stripe_Local_Payment::get_source_redirect_url()
	 */
	protected function get_source_redirect_url($source, $order) {
		return '#local_payment=klarna&redirect=' . $this->get_local_payment_return_url ( $order ) . '&encoded_source=' . base64_encode ( wp_json_encode ( $source ) );
	}

	protected function get_local_payment_return_url($order) {
		return add_query_arg ( 'source', $order->get_meta ( '_stripe_source_id', true ), parent::get_local_payment_return_url ( $order ) );
	}

	/**
	 *
	 * @return mixed
	 */
	public function get_payment_categories() {
		return apply_filters ( 'wc_stripe_klarna_payment_categries', [ 
				'pay_now' => __ ( 'Pay Now', 'woo-stripe-payment' ), 
				'pay_later' => __ ( 'Pay Later', 'woo-stripe-payment' ), 
				'pay_over_time' => __ ( 'Pay Over Time', 'woo-stripe-payment' ) 
		] );
	}
}