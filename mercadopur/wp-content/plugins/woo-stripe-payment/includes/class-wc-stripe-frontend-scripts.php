<?php
/**
 * Handles scrip enqueuement and output of params needed by the plugin.
 * @package Stripe/Classes
 * @author PaymentPlugins
 *
 */
class WC_Stripe_Frontend_Scripts {

	public $prefix = 'wc-stripe-';

	public $registered_scripts = array();

	public $enqueued_scripts = array();

	public $localized_scripts = array();

	public $global_scripts = array( 
			'external' => 'https://js.stripe.com/v3/', 
			'gpay' => 'https://pay.google.com/gp/p/js/pay.js', 
			'plaid' => 'https://cdn.plaid.com/link/v2/stable/link-initialize.js' 
	);

	public function __construct() {
		add_action ( 'wp_enqueue_scripts', array( $this, 
				'enqueue_scripts' 
		) );
		add_action ( 'woocommerce_review_order_before_payment', array( 
				$this, 'enqueue_checkout_scripts' 
		) );
	}

	/**
	 * Enqueue all frontend scripts needed by the plugin
	 */
	public function enqueue_scripts() {
		// register global scripts
		foreach ( $this->global_scripts as $handle => $src ) {
			$this->register_script ( $handle, $src );
		}
		
		// register scripts that aren't part of gateways
		$this->register_script ( 'wc-stripe', $this->assets_url ( 'js/frontend/wc-stripe' . $this->get_min () . '.js' ), [ 
				'jquery', $this->get_handle ( 'external' ) 
		] );
		
		$this->register_script ( 'form-handler', $this->assets_url ( 'js/frontend/form-handler.js' ), [ 
				'selectWoo' 
		] );
		
		wp_localize_script ( $this->get_handle ( 'wc-stripe' ), 'wc_stripe_params_v3', [ 
				'api_key' => wc_stripe_get_publishable_key (), 
				'page' => $this->get_page_id () 
		] );
		
		wp_localize_script ( $this->get_handle ( 'form-handler' ), 'wc_stripe_form_handler_params', [ 
				'no_results' => __ ( 'No matches found', 'woo-stripe-payment' ) 
		] );
	}

	public function enqueue_checkout_scripts() {
		$data = wc_stripe_get_local_payment_params ();
		// only enqueue local payment script if there are local payment gateways that have been enabled.
		if (! empty ( $data[ 'gateways' ] )) {
			$this->enqueue_script ( 'local-payment', $this->assets_url ( 'js/frontend/local-payment.js' ), [ 
					$this->get_handle ( 'external' ), 
					$this->get_handle ( 'wc-stripe' ) 
			] );
			$this->localize_script ( 'local-payment', $data );
		}
	}

	public function register_script($handle, $src, $deps = [], $version = '', $footer = true) {
		$version = empty ( $version ) ? wc_stripe ()->version () : $version;
		$this->registered_scripts[] = $this->get_handle ( $handle );
		wp_register_script ( $this->get_handle ( $handle ), $src, $deps, $version, $footer );
	}

	public function enqueue_script($handle, $src = '', $deps = [], $version = '', $footer = true) {
		$handle = $this->get_handle ( $handle );
		$version = empty ( $version ) ? wc_stripe ()->version () : $version;
		if (! in_array ( $handle, $this->registered_scripts )) {
			$this->register_script ( $handle, $src, $deps, $version, $footer );
		}
		$this->enqueued_scripts[] = $handle;
		wp_enqueue_script ( $handle );
	}

	/**
	 *
	 * @param string $handle        	
	 * @param array $data        	
	 */
	public function localize_script($handle, $data) {
		$handle = $this->get_handle ( $handle );
		if (wp_script_is ( $handle ) && ! in_array ( $handle, $this->localized_scripts )) {
			$name = str_replace ( $this->prefix, '', $handle );
			$data = apply_filters ( 'wc_stripe_localize_script_' . $name, $data, $name );
			if ($data) {
				$this->localized_scripts[] = $handle;
				$object_name = str_replace ( '-', '_', $handle ) . '_params';
				wp_localize_script ( $handle, $object_name, $data );
			}
		}
	}

	public function get_handle($handle) {
		return strpos ( $handle, $this->prefix ) === false ? $this->prefix . $handle : $handle;
	}

	/**
	 *
	 * @param string $uri        	
	 */
	public function assets_url($uri = '') {
		return untrailingslashit ( wc_stripe ()->assets_url ( $uri ) );
	}

	public function get_min() {
		return $suffix = SCRIPT_DEBUG ? '' : '.min';
	}

	private function get_page_id() {
		if (is_product ()) {
			return 'product';
		}
		if (is_cart ()) {
			return 'cart';
		}
		if (is_checkout ()) {
			return 'checkout';
		}
		if (is_add_payment_method_page ()) {
			return 'add_payment_method';
		}
		return '';
	}
}