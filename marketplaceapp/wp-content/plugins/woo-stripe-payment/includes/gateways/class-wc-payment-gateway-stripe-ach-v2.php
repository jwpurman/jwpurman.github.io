<?php
/**
 * Gateway that processes ACH payments.
 * Only available for U.S. based merchants at this time.
 *
 * @since 3.0.5
 * @author Payment Plugins
 * @package Stripe/Gateways
 *         
 */
class WC_Payment_Gateway_Stripe_ACH_V2 extends WC_Payment_Gateway_Stripe {
	
	use WC_Stripe_Payment_Charge_Trait;

	/**
	 *
	 * @var object
	 */
	public $metadata_key = '';

	public function __construct() {
		$this->synchronous = false;
		$this->id = 'stripe_ach';
		$this->tab_title = __ ( 'ACH', 'woo-stripe-payment' );
		$this->template_name = 'ach.php';
		$this->token_type = 'Stripe_ACH';
		$this->method_title = __ ( 'Stripe ACH', 'woo-stripe-payment' );
		$this->method_description = __ ( 'ACH gateway that integrates with your Stripe account.', 'woo-stripe-payment' );
		$this->icon = wc_stripe ()->assets_url ( 'img/ach.svg' );
		$this->order_button_text = __ ( 'Bank Payment', 'woo-stripe-payment' );
		$this->metadata_key = $this->id . '_metadata';
		parent::__construct ();
		$this->settings[ 'charge_type' ] = 'capture';
	}

	public function get_payment_object() {
		return new WC_Stripe_Payment_Charge ( $this, new WC_Stripe_Gateway_ACH () );
	}

	public static function init() {
		add_action ( 'woocommerce_checkout_update_order_review', [ 
				__CLASS__, 'update_order_review' 
		] );
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see WC_Payment_Gateway::is_available()
	 */
	public function is_available() {
		$is_available = parent::is_available ();
		return $is_available && get_woocommerce_currency () == 'USD';
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see WC_Payment_Gateway_Stripe::init_supports()
	 */
	public function init_supports() {
		/*
		 * $this->supports = [ 'tokenization', 'products',
		 * 'refunds'
		 * ];
		 */
		parent::init_supports ();
		unset ( $this->supports[ 'add_payment_method' ] );
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see WC_Payment_Gateway_Stripe::enqueue_checkout_scripts()
	 */
	public function enqueue_checkout_scripts($scripts) {
		$scripts->enqueue_script ( 'ach', $scripts->assets_url ( 'js/frontend/ach-payments.js' ), array( 
				$scripts->get_handle ( 'external' ), 
				$scripts->get_handle ( 'plaid' ) 
		) );
		$scripts->localize_script ( 'ach', $this->get_localized_params () );
	}

	public function get_localized_params() {
		return array_merge_recursive ( parent::get_localized_params (), array( 
				'env' => $this->get_plaid_environment (), 
				'client_name' => $this->get_option ( 'client_name' ), 
				'public_key' => '3a5433ed5ff7229aef78bb4d87a63a', 
				'metadata_key' => '[name="' . $this->metadata_key . '"]', 
				'fees_enabled' => $this->fees_enabled () 
		) );
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see WC_Payment_Gateway_Stripe_Charge::process_payment()
	 */
	public function process_payment($order_id) {
		$this->payment_object->get_gateway ()->set_plaid_environment ( $this->get_option ( 'environment' ) );
		$this->payment_object->get_gateway ()->set_public_token ( $this->get_public_token () );
		$this->payment_object->get_gateway ()->set_account_id ( $this->get_metadata ()[ 'account_id' ] );
		
		return parent::process_payment ( $order_id );
	}

	public function get_plaid_environment() {
		return $this->get_option ( 'environment' );
	}

	private function get_metadata() {
		return isset ( $_POST[ $this->metadata_key ] ) ? json_decode ( stripslashes ( $_POST[ $this->metadata_key ] ), true ) : null;
	}

	private function get_public_token() {
		return $this->get_new_source_token ();
	}

	public function get_saved_methods_label() {
		return __ ( 'Saved Banks', 'woo-stripe-payment' );
	}

	public function get_new_method_label() {
		return __ ( 'New Bank', 'woo-stripe-payment' );
	}

	public function generate_ach_fee_html($key, $data) {
		$field_key = $this->get_field_key ( $key );
		$defaults = array( 'title' => '', 
				'disabled' => false, 'class' => '', 
				'css' => 'max-width: 150px; min-width: 150px;', 
				'placeholder' => '', 'type' => 'text', 
				'desc_tip' => false, 'description' => '', 
				'custom_attributes' => array(), 
				'options' => array() 
		);
		$data = wp_parse_args ( $data, $defaults );
		ob_start ();
		include wc_stripe ()->plugin_path () . 'includes/admin/views/html-ach-fee.php';
		return ob_get_clean ();
	}

	/**
	 *
	 * @param string $key        	
	 * @param array $value        	
	 */
	public function validate_ach_fee_field($key, $value) {
		$value = empty ( $value ) ? [ 'type' => 'none', 
				'taxable' => 'no', 'value' => '0' 
		] : $value;
		if (! isset ( $value[ 'taxable' ] )) {
			$value[ 'taxable' ] = 'no';
		}
		return $value;
	}

	/**
	 *
	 * @param string $key        	
	 * @param string $value        	
	 */
	public function validate_environment_field($key, $value) {
		if ('test' == wc_stripe_mode () && 'development' == $value) {
			WC_Admin_Settings::add_error ( __ ( 'You must set the API mode to live in order to enable the Plaid development environment.', 'woo-stripe-payment' ) );
		}
		return $value;
	}

	public function fees_enabled() {
		$fee = $this->get_option ( 'fee', [ 
				'type' => 'none', 'value' => '0' 
		] );
		return ! empty ( $fee ) && $fee[ 'type' ] != 'none';
	}

	/**
	 *
	 * @param WC_Cart $cart        	
	 */
	public function after_calculate_totals($cart) {
		remove_action ( 'woocommerce_after_calculate_totals', [ 
				$this, 'after_calculate_totals' 
		] );
		
		add_action ( 'woocommerce_cart_calculate_fees', array( 
				$this, 'calculate_fees' 
		) );
		
		WC ()->session->set ( 'wc_stripe_cart_total', $cart->total );
		$cart->calculate_totals ();
	}

	/**
	 *
	 * @param WC_Cart $cart        	
	 */
	public function calculate_fees($cart) {
		remove_action ( 'woocommerce_cart_calculate_fees', array( 
				$this, 'calculate_fees' 
		) );
		$fee = $this->get_option ( 'fee' );
		$taxable = $fee[ 'taxable' ] == 'yes';
		switch ($fee[ 'type' ]) {
			case 'amount' :
				$cart->add_fee ( __ ( 'ACH Fee' ), $fee[ 'value' ], $taxable );
				break;
			case 'percent' :
				$cart->add_fee ( __ ( 'ACH Fee' ), $fee[ 'value' ] * WC ()->session->get ( 'wc_stripe_cart_total', 0 ), $taxable );
				break;
		}
		unset ( WC ()->session->wc_stripe_cart_total );
	}

	public static function update_order_review() {
		if (! empty ( $_POST[ 'payment_method' ] ) && $_POST[ 'payment_method' ] == 'stripe_ach') {
			$payment_method = new WC_Payment_Gateway_Stripe_ACH ();
			if ($payment_method->fees_enabled ()) {
				add_action ( 'woocommerce_after_calculate_totals', [ 
						$payment_method, 
						'after_calculate_totals' 
				] );
			}
		}
	}
}
WC_Payment_Gateway_Stripe_ACH::init ();