<?php
/**
 * 
 * @author PaymentPlugins
 * @package Stripe/Classes
 * @propertyWC_Stripe_Rest_Controller $order_actions
 * @property WC_Stripe_Rest_Controller $cart
 * @property WC_Stripe_Rest_Controller $checkout
 * @propertyWC_Stripe_Rest_Controller $payment_intent
 * @property WC_Stripe_Rest_Controller $googlepay
 * @property WC_Stripe_Rest_Controller $settings
 * @property WC_Stripe_Rest_Controller $webhook
 */
class WC_Stripe_Rest_API {

	/**
	 *
	 * @var array
	 */
	private $controllers = array();

	public function __construct() {
		$this->include_classes ();
		add_action ( 'rest_api_init', array( $this, 
				'register_routes' 
		) );
	}

	public static function init() {
		add_filter ( 'woocommerce_is_rest_api_request', array( 
				__CLASS__, 'is_rest_api_request' 
		) );
	}

	/**
	 *
	 * @param WC_Braintree_Rest_Controller $key        	
	 */
	public function __get($key) {
		$controller = isset ( $this->controllers[ $key ] ) ? $this->controllers[ $key ] : '';
		if (empty ( $controller )) {
			wc_doing_it_wrong ( __FUNCTION__, sprintf ( __ ( '%1$s is an invalid controller name.', 'woo-stripe-payment' ), $key ), wc_stripe ()->version );
		}
		return $controller;
	}

	public function __set($key, $value) {
		$this->controllers[ $key ] = $value;
	}

	private function include_classes() {
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/abstract/abstract-wc-stripe-rest-controller.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/controllers/class-wc-stripe-controller-order-actions.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/controllers/class-wc-stripe-controller-payment-intent.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/controllers/class-wc-stripe-controller-cart.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/controllers/class-wc-stripe-controller-checkout.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/controllers/class-wc-stripe-controller-googlepay.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/controllers/class-wc-stripe-controller-payment-method.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/controllers/class-wc-stripe-controller-gateway-settings.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/controllers/class-wc-stripe-controller-webhook.php';
		
		foreach ( $this->get_controllers () as $key => $class_name ) {
			if (class_exists ( $class_name )) {
				$this->{$key} = new $class_name ();
			}
		}
	}

	public function register_routes() {
		WC ()->payment_gateways ();
		foreach ( $this->controllers as $key => $controller ) {
			if (is_callable ( array( $controller, 
					'register_routes' 
			) )) {
				$controller->{ 'register_routes' } ();
			}
		}
	}

	public function get_controllers() {
		$controllers = array( 
				'order_actions' => 'WC_Stripe_Controller_Order_Actions', 
				'checkout' => 'WC_Stripe_Controller_Checkout', 
				'cart' => 'WC_Stripe_Controller_Cart', 
				'payment_intent' => 'WC_Stripe_Controller_Payment_Intent', 
				'googlepay' => 'WC_Stripe_Controller_GooglePay', 
				'payment_method' => 'WC_Stripe_Controller_Payment_Method', 
				'settings' => 'WC_Stripe_Controller_Gateway_Settings', 
				'webhook' => 'WC_Stripe_Controller_Webhook' 
		);
		return apply_filters ( 'wc_stripe_api_controllers', $controllers );
	}

	public function rest_url() {
		return wc_stripe ()->rest_url ();
	}

	public function rest_uri() {
		return wc_stripe ()->rest_uri ();
	}

	/**
	 * Added after WC 3.6 so WC_STRIPE_PLUGIN_FILE_PATH, and Session are loaded for Stripe rest requests.
	 *
	 * @param bool $bool        	
	 */
	public static function is_rest_api_request($bool) {
		if (! empty ( $_SERVER[ 'REQUEST_URI' ] ) && strpos ( $_SERVER[ 'REQUEST_URI' ], wc_stripe ()->rest_uri () ) !== - 1) {
			$bool = false;
		}
		return $bool;
	}
}
WC_Stripe_Rest_API::init ();