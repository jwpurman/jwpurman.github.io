<?php
use Stripe\ApiOperations\Create;
use Stripe\ApiOperations\Retrieve;
use Stripe\ApiOperations\Update;
use Stripe\ApiOperations\All;

/**
 * Gateway class that abstracts all API calls to Stripe.
 *
 * @author Payment Plugins
 * @package Stripe/Classes
 *         
 */
class WC_Stripe_Gateway {

	/**
	 *
	 * @since 3.0.5
	 * @var Stripe mode (test, live)
	 */
	private $mode = null;

	/**
	 *
	 * @since 3.0.8
	 * @var unknown
	 */
	private $secret_key = null;

	public function __construct($mode = null, $secret_key = null) {
		if (null != $mode) {
			$this->mode = $mode;
		}
		if (null != $secret_key) {
			$this->secret_key = $secret_key;
		}
	}

	public static function init() {
		\Stripe\Stripe::setAppInfo ( 'Wordpress woo-stripe-payment', wc_stripe ()->version (), 'https://wordpress.org/plugins/woo-stripe-payment/', 'pp_partner_FdPtriN2Q7JLOe' );
		\Stripe\Stripe::setApiVersion ( '2019-12-03' );
	}

	/**
	 *
	 * @since 3.1.0
	 * @param string $mode        	
	 * @param string $secret_key        	
	 */
	public static function load($mode = null, $secret_key = null) {
		$class = apply_filters ( 'wc_stripe_gateway_class', 'WC_Stripe_Gateway' );
		return new $class ( $mode, $secret_key );
	}

	/**
	 *
	 * @since 3.1.0
	 * @param unknown $mode        	
	 */
	public function set_mode($mode) {
		$this->mode = $mode;
	}

	/**
	 * Create a customer within Stripe.
	 *
	 * @param array $args        	
	 * @return WP_Error|string
	 */
	public function create_customer($args, $mode = '') {
		try {
			$customer = \Stripe\Customer::create ( apply_filters ( 'wc_stripe_create_customer_args', $args ), $this->get_api_options () );
			return $customer->id;
		} catch ( \Stripe\Error\Base $e ) {
			$err = $e->getJsonBody ()[ 'error' ];
			return new WP_Error ( 'customer-error', $err[ 'message' ] );
		}
	}

	public function update_customer($id, $args, $mode = '') {
		try {
			return \Stripe\Customer::update ( $id, $args, $this->get_api_options ( $mode ) );
		} catch ( \Stripe\Error\Base $e ) {
			$err = $e->getJsonBody ()[ 'error' ];
			return new WP_Error ( 'customer-error', $err[ 'message' ] );
		}
	}

	public function charge($args, $mode = '') {
		try {
			return \Stripe\Charge::create ( $args, $this->get_api_options ( $mode ) );
		} catch ( \Stripe\Error\Base $e ) {
			$err = $e->getJsonBody ()[ 'error' ];
			return new WP_Error ( 'charge-error', $err[ 'message' ] );
		}
	}

	/**
	 *
	 * @param array $args        	
	 * @param string $mode        	
	 * @return WP_Error|\Stripe\PaymentIntent
	 */
	public function create_payment_intent($args, $mode = '') {
		try {
			return \Stripe\PaymentIntent::create ( $args, $this->get_api_options ( $mode ) );
		} catch ( \Stripe\Error\Base $e ) {
			$err = $e->getJsonBody ()[ 'error' ];
			return new WP_Error ( 'payment-intent-error', $err[ 'message' ] );
		}
	}

	public function create_setup_intent($args, $mode = '') {
		try {
			return \Stripe\SetupIntent::create ( $args, $this->get_api_options ( $mode ) );
		} catch ( \Stripe\Error\Base $e ) {
			$err = $e->getJsonBody ()[ 'error' ];
			return new WP_Error ( 'setup-intent-error', $err[ 'message' ] );
		}
	}

	/**
	 *
	 * @param \Stripe\PaymentIntent $intent        	
	 * @param array $args        	
	 * @param string $mode        	
	 */
	public function update_payment_intent($id, $args, $mode = '') {
		try {
			return \Stripe\PaymentIntent::update ( $id, $args, $this->get_api_options ( $mode ) );
		} catch ( \Stripe\Error\Base $e ) {
			$err = $e->getJsonBody ()[ 'error' ];
			return new WP_Error ( 'payment-intent-error', $err[ 'message' ] );
		}
	}

	/**
	 *
	 * @param \Stripe\PaymentIntent $intent        	
	 * @param array $args        	
	 * @param string $mode        	
	 */
	public function confirm_payment_intent($intent, $args = [], $mode = '') {
		try {
			/*
			 * $intent = $this->fetch_payment_intent ( $id, $mode );
			 * if (is_wp_error ( $intent )) {
			 * return $intent;
			 * }
			 */
			return $intent->confirm ( $args, $this->get_api_options ( $mode ) );
		} catch ( \Stripe\Error\Base $e ) {
			$err = $e->getJsonBody ()[ 'error' ];
			return new WP_Error ( 'payment-intent-error', $err[ 'message' ] );
		}
	}

	/**
	 *
	 * @param string $id        	
	 * @param string $mode        	
	 * @return WP_Error|\Stripe\PaymentIntent
	 */
	public function fetch_payment_intent($id, $mode = '') {
		try {
			return \Stripe\PaymentIntent::retrieve ( $id, $this->get_api_options ( $mode ) );
		} catch ( \Stripe\Error\Base $e ) {
			$err = $e->getJsonBody ()[ 'error' ];
			return new WP_Error ( 'payment-intent-error', $err[ 'message' ] );
		}
	}

	public function capture_payment_intent($id, $args = [], $mode = '') {
		try {
			$payment_intent = $this->fetch_payment_intent ( $id, $mode );
			return $payment_intent->capture ( $args, $this->get_api_options ( $mode ) );
		} catch ( \Stripe\Error\Base $e ) {
			$err = $e->getJsonBody ()[ 'error' ];
			return new WP_Error ( 'payment-intent-error', $err[ 'message' ] );
		}
	}

	/**
	 *
	 * @param \Stripe\PaymentIntent|string $id        	
	 * @param string $mode        	
	 */
	public function cancel_payment_intent($id, $mode = '') {
		try {
			if (! is_object ( $id )) {
				$payment_intent = $this->fetch_payment_intent ( $id, $mode );
			} else {
				$payment_intent = $id;
			}
			return $payment_intent->cancel ( [], $this->get_api_options ( $mode ) );
		} catch ( \Stripe\Error\Base $e ) {
			$err = $e->getJsonBody ()[ 'error' ];
			return new WP_Error ( 'payment-intent-error', $err[ 'message' ] );
		}
	}

	/**
	 *
	 * @param string $id        	
	 * @param string $mode        	
	 * @return WP_Error|\Stripe\SetupIntent
	 */
	public function fetch_setup_intent($id, $mode = '') {
		try {
			return \Stripe\SetupIntent::retrieve ( $id, $this->get_api_options ( $mode ) );
		} catch ( \Stripe\Error\Base $e ) {
			$err = $e->getJsonBody ()[ 'error' ];
			return new WP_Error ( $err[ 'code' ], $err[ 'message' ] );
		}
	}

	public function capture($id, $args, $mode = '') {
		$charge = $this->get_charge ( $id, $mode );
		if (! is_wp_error ( $charge )) {
			try {
				return $charge->capture ( $args, $this->get_api_options ( $mode ) );
			} catch ( \Stripe\Error\Base $e ) {
				return new WP_Error ( 'capture-error', sprintf ( __ ( 'Error capturing charge. Reason: %s', 'woo-stripe-payment' ), $e->getMessage () ) );
			}
		} else {
			return $charge;
		}
	}

	/**
	 *
	 * @param string $charge_id        	
	 * @param string $mode        	
	 * @return \Stripe\Charge|WP_Error
	 */
	public function get_charge($charge_id, $mode = '') {
		try {
			return \Stripe\Charge::retrieve ( $charge_id, $this->get_api_options ( $mode ) );
		} catch ( \Stripe\Error\Base $e ) {
			$err = $e->getJsonBody ()[ 'error' ];
			return new WP_Error ( 'payment-intent-error', $err[ 'message' ] );
		}
	}

	public function refund($args, $mode = '') {
		try {
			return \Stripe\Refund::create ( $args, $this->get_api_options ( $mode ) );
		} catch ( \Stripe\Error\Base $e ) {
			$err = $e->getJsonBody ()[ 'error' ];
			return new WP_Error ( 'refund', $err[ 'message' ] );
		}
	}

	public function get_payment_method($id, $mode = '') {
		try {
			return \Stripe\PaymentMethod::retrieve ( $id, $this->get_api_options ( $mode ) );
		} catch ( \Stripe\Error\Base $e ) {
			$err = $e->getJsonBody ()[ 'error' ];
			return new WP_Error ( 'payment-method', $err[ 'message' ] );
		}
	}

	/**
	 *
	 * @param \Stripe\PaymentMethod $payment_method        	
	 * @param array $args        	
	 * @param string $mode        	
	 */
	public function attach_payment_method($payment_method, $args = [], $mode = '') {
		try {
			return $payment_method->attach ( $args, $this->get_api_options ( $mode ) );
		} catch ( \Stripe\Error\Base $e ) {
			$err = $e->getJsonBody ()[ 'error' ];
			return new WP_Error ( 'attach-payment-error', $err[ 'message' ] );
		}
	}

	public function fetch_payment_method($id, $mode = '') {
		try {
			return \Stripe\PaymentMethod::retrieve ( $id, $this->get_api_options ( $mode ) );
		} catch ( \Stripe\Error\Base $e ) {
			$err = $e->getJsonBody ()[ 'error' ];
			return new WP_Error ( 'fetch-payment-error', $err[ 'message' ] );
		}
	}

	/**
	 *
	 * @param \Stripe\PaymentMethod $payment_method        	
	 * @param string $mode        	
	 */
	public function delete_payment_method($payment_method, $mode = '') {
		try {
			return $payment_method->detach ( [], $this->get_api_options ( $mode ) );
		} catch ( \Stripe\Error\Base $e ) {
			$err = $e->getJsonBody ()[ 'error' ];
			return new WP_Error ( 'delete-source-error', $err[ 'message' ] );
		}
	}

	/**
	 *
	 * @param string $id        	
	 * @param string $customer        	
	 * @param string $mode        	
	 */
	public function delete_card($id, $customer, $mode = '') {
		try {
			\Stripe\Customer::deleteSource ( $customer, $id, null, $this->get_api_options ( $mode ) );
		} catch ( \Stripe\Error\Base $e ) {
			$err = $e->getJsonBody ()[ 'error' ];
			return new WP_Error ( 'delete-source-error', $err[ 'message' ] );
		}
	}

	/**
	 *
	 * @param array $args        	
	 * @param string $mode        	
	 * @return WP_Error|\Stripe\PaymentMethod
	 */
	public function create_payment_method($args, $mode = '') {
		try {
			return \Stripe\PaymentMethod::create ( $args, $this->get_api_options ( $mode ) );
		} catch ( \Stripe\Error\Base $e ) {
			$err = $e->getJsonBody ()[ 'error' ];
			return new WP_Error ( 'delete-source-error', $err[ 'message' ] );
		}
	}

	/**
	 *
	 * @param string $id        	
	 * @param string $mode        	
	 * @return WP_Error|\Stripe\Source
	 *
	 */
	public function fetch_payment_source($id, $mode = '') {
		try {
			return \Stripe\Source::retrieve ( $id, $this->get_api_options ( $mode ) );
		} catch ( \Stripe\Error\Base $e ) {
			$err = $e->getJsonBody ()[ 'error' ];
			return new WP_Error ( 'source-error', $err[ 'message' ] );
		}
	}

	/**
	 *
	 * @param string $customer_id        	
	 * @param string $id        	
	 * @param string $mode        	
	 * @return WP_Error|\Stripe\Source
	 */
	public function create_customer_source($customer_id, $id, $mode = '') {
		try {
			return \Stripe\Customer::createSource ( $customer_id, [ 
					'source' => $id 
			], $this->get_api_options ( $mode ) );
		} catch ( \Stripe\Error\Base $e ) {
			$err = $e->getJsonBody ()[ 'error' ];
			return new WP_Error ( 'source-error', $err[ 'message' ] );
		}
	}

	/**
	 *
	 * @param array $args        	
	 * @param string $mode        	
	 * @return WP_Error|\Stripe\Source
	 */
	public function create_source($args, $mode = '') {
		try {
			return \Stripe\Source::create ( $args, $this->get_api_options ( $mode ) );
		} catch ( \Stripe\Error\Base $e ) {
			$err = $e->getJsonBody ()[ 'error' ];
			return new WP_Error ( 'source-error', $err[ 'message' ] );
		}
	}

	/**
	 *
	 * @since 3.0.2
	 * @param string $source_id        	
	 * @param array $args        	
	 * @param string $mode        	
	 */
	public function update_source($source_id, $args, $mode = '') {
		try {
			return \Stripe\Source::update ( $source_id, $args, $this->get_api_options ( $mode ) );
		} catch ( \Stripe\Error\Base $e ) {
			$err = $e->getJsonBody ()[ 'error' ];
			return new WP_Error ( 'source-error', $err[ 'message' ] );
		}
	}

	public function fetch_customer($customer_id, $mode = '') {
		try {
			return \Stripe\Customer::retrieve ( $customer_id, $this->get_api_options ( $mode ) );
		} catch ( \Stripe\Error\Base $e ) {
			$err = $e->getJsonBody ()[ 'error' ];
			return new WP_Error ( 'customer-error', $err[ 'message' ] );
		}
	}

	public function fetch_customers($mode = '') {
		try {
			return \Stripe\Customer::all ( [ 
					'limit' => 1 
			], $this->get_api_options ( $mode ) );
		} catch ( \Stripe\Error\Base $e ) {
			$err = $e->getJsonBody ()[ 'error' ];
			return new WP_Error ( 'customer-error', $err[ 'message' ] );
		}
	}

	public function fetch_payment_methods($customer_id, $mode = '', $type = 'card') {
		try {
			return \Stripe\PaymentMethod::all ( [ 
					'customer' => $customer_id, 
					'type' => $type 
			], $this->get_api_options ( $mode ) );
		} catch ( \Stripe\Error\Base $e ) {
			$err = $e->getJsonBody ()[ 'error' ];
			return new WP_Error ( 'source-error', $err[ 'message' ] );
		}
	}

	public function register_domain($domain, $mode = '') {
		try {
			\Stripe\ApplePayDomain::create ( [ 
					'domain_name' => $domain 
			], $this->get_api_options ( $mode ) );
		} catch ( \Stripe\Error\Base $e ) {
			$err = $e->getJsonBody ()[ 'error' ];
			return new WP_Error ( 'domain-error', $err[ 'message' ] );
		}
	}

	public function webhooks($mode = '') {
		try {
			return \Stripe\WebhookEndpoint::all ( [ 
					'limit' => 100 
			], $this->get_api_options ( $mode ) );
		} catch ( \Stripe\Error\Base $e ) {
			$err = $e->getJsonBody ()[ 'error' ];
			return new WP_Error ( 'webhook-error', $err[ 'message' ] );
		}
	}

	public function create_webhook($url, $events, $mode = '') {
		try {
			return \Stripe\WebhookEndpoint::create ( [ 
					'url' => $url, 
					'enabled_events' => $events 
			], $this->get_api_options ( $mode ) );
		} catch ( \Stripe\Error\Base $e ) {
			$err = $e->getJsonBody ()[ 'error' ];
			return new WP_Error ( 'webhook-error', $err[ 'message' ] );
		}
	}

	public function update_webhook($id, $params, $mode = '') {
		try {
			return \Stripe\WebhookEndpoint::update ( $id, $params, $this->get_api_options ( $mode ) );
		} catch ( \Stripe\Error\Base $e ) {
			$err = $e->getJsonBody ()[ 'error' ];
			return new WP_Error ( 'webhook-error', $err[ 'message' ] );
		}
	}

	public function fetch_webhook($id, $mode = '') {
		try {
			return \Stripe\WebhookEndpoint::retrieve ( $id, $this->get_api_options ( $mode ) );
		} catch ( \Stripe\Error\Base $e ) {
			$err = $e->getJsonBody ()[ 'error' ];
			return new WP_Error ( 'webhook-error', $err[ 'message' ] );
		}
	}

	private function get_api_options($mode = '') {
		if (empty ( $mode ) && $this->mode != null) {
			$mode = $this->mode;
		}
		$args = [ 
				'api_key' => $this->secret_key ? $this->secret_key : wc_stripe_get_secret_key ( $mode ) 
		];
		return apply_filters ( 'wc_stripe_api_options', $args );
	}
}
WC_Stripe_Gateway::init ();