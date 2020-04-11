<?php
/**
 * Singleton class that handles plugin functionality like class loading.
 * @since 3.0.0
 * @author PaymentPlugins
 * @package Stripe/Classes
 *
 */
class WC_Stripe_Manager {

	public static $_instance;

	public static function instance() {
		if (self::$_instance == null) {
			self::$_instance = new self ();
		}
		return self::$_instance;
	}

	/**
	 *
	 * @var string
	 */
	public $version = '3.1.0';

	/**
	 *
	 * @var WC_Stripe_Settings_API
	 */
	public $api_settings;

	/**
	 *
	 * @var WC_Stripe_Rest_API
	 */
	public $rest_api;

	/**
	 *
	 * @var string
	 */
	public $client_id = 'ca_Gp4vLOJiqHJLZGxakHW7JdbBlcgWK8Up';

	/**
	 * Test client id;
	 *
	 * @var string
	 */
	// public $client_id = 'ca_Gp4vL3V6FpTguYoZIehD5COPeI80rLpV';
	
	/**
	 *
	 * @var WC_Stripe_Frontend_Scripts
	 */
	private $scripts;

	/**
	 *
	 * @var array
	 */
	private $payment_gateways;

	public function __construct() {
		add_action ( 'plugins_loaded', array( $this, 
				'plugins_loaded' 
		), 10 );
		add_action ( 'init', array( $this, 'init' 
		) );
		add_action ( 'admin_init', array( $this, 
				'admin_init' 
		) );
		add_action ( 'woocommerce_init', array( $this, 
				'woocommerce_dependencies' 
		) );
		$this->includes ();
		// $this->version = rand(0, 12000);
	}

	/**
	 * Return the plugin version.
	 *
	 * @return string
	 */
	public function version() {
		return $this->version;
	}

	/**
	 * Return the url for the plugin assets.
	 *
	 * @return string
	 */
	public function assets_url($uri = '') {
		$url = WC_STRIPE_ASSETS . $uri;
		if (! preg_match ( '/(\.js)|(\.css)|(\.svg)|(\.png)/', $uri )) {
			return trailingslashit ( $url );
		}
		return $url;
	}

	/**
	 * Return the dir path for the plugin.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return WC_STRIPE_PLUGIN_FILE_PATH;
	}

	public function plugins_loaded() {
		load_plugin_textdomain ( 'woo-stripe-payment', false, dirname ( WC_STRIPE_PLUGIN_NAME ) . '/i18n/languages' );
	}

	/**
	 * Function that is hooked in to the Wordpress init action.
	 */
	public function init() {}

	public function includes() {
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/class-wc-stripe-install.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/class-wc-stripe-update.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/class-wc-stripe-rest-api.php';
		
		if (is_admin ()) {
			include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/admin/class-wc-stripe-admin-menus.php';
			include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/admin/class-wc-stripe-admin-assets.php';
			include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/admin/class-wc-stripe-admin-settings.php';
			include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/admin/meta-boxes/class-wc-stripe-admin-order-metaboxes.php';
			include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/admin/class-wc-stripe-admin-user-edit.php';
			include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/admin/class-wc-stripe-admin-notices.php';
		}
	}

	/**
	 * Function that is hooked in to the Wordpress admin_init action.
	 */
	public function admin_init() {}

	public function woocommerce_dependencies() {
		// load functions
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/wc-stripe-functions.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/wc-stripe-webhook-functions.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/wc-stripe-hooks.php';
		
		// constants
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/class-wc-stripe-constants.php';
		
		// traits
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/traits/wc-stripe-settings-trait.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/traits/wc-stripe-controller-cart-trait.php';
		
		// load gateways
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/abstract/abstract-wc-payment-gateway-stripe.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/abstract/abstract-wc-payment-gateway-stripe-local-payment.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/gateways/class-wc-payment-gateway-stripe-cc.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/gateways/class-wc-payment-gateway-stripe-applepay.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/gateways/class-wc-payment-gateway-stripe-googlepay.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/gateways/class-wc-payment-gateway-stripe-ach.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/gateways/class-wc-payment-gateway-stripe-payment-request.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/gateways/class-wc-payment-gateway-stripe-ideal.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/gateways/class-wc-payment-gateway-stripe-p24.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/gateways/class-wc-payment-gateway-stripe-klarna.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/gateways/class-wc-payment-gateway-stripe-giropay.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/gateways/class-wc-payment-gateway-stripe-eps.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/gateways/class-wc-payment-gateway-stripe-multibanco.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/gateways/class-wc-payment-gateway-stripe-sepa.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/gateways/class-wc-payment-gateway-stripe-sofort.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/gateways/class-wc-payment-gateway-stripe-wechat.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/gateways/class-wc-payment-gateway-stripe-bancontact.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/gateways/class-wc-payment-gateway-stripe-fpx.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/gateways/class-wc-payment-gateway-stripe-alipay.php';
		
		// tokens
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/abstract/abstract-wc-payment-token-stripe.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/tokens/class-wc-payment-token-stripe-cc.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/tokens/class-wc-payment-token-stripe-applepay.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/tokens/class-wc-payment-token-stripe-googlepay.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/tokens/class-wc-payment-token-stripe-local-payment.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/tokens/class-wc-payment-token-stripe-ach.php';
		
		// main classes
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/class-wc-stripe-frontend-scripts.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/class-wc-stripe-field-manager.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/class-wc-stripe-rest-api.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/class-wc-stripe-gateway.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/class-wc-stripe-gateway-ach.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/class-wc-stripe-customer-manager.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/class-wc-stripe-gateway-conversions.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/class-wc-stripe-frontend-notices.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/class-wc-stripe-redirect-handler.php';
		
		// settings
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/abstract/abstract-wc-stripe-settings.php';
		include_once WC_STRIPE_PLUGIN_FILE_PATH . 'includes/admin/settings/class-wc-stripe-api-settings.php';
		
		$this->payment_gateways = apply_filters ( 'wc_stripe_payment_gateways', array( 
				'WC_Payment_Gateway_Stripe_CC', 
				'WC_Payment_Gateway_Stripe_ApplePay', 
				'WC_Payment_Gateway_Stripe_GooglePay', 
				'WC_Payment_Gateway_Stripe_Payment_Request', 
				'WC_Payment_Gateway_Stripe_ACH', 
				'WC_Payment_Gateway_Stripe_Ideal', 
				'WC_Payment_Gateway_Stripe_P24', 
				'WC_Payment_Gateway_Stripe_Klarna', 
				'WC_Payment_Gateway_Stripe_Bancontact', 
				'WC_Payment_Gateway_Stripe_Giropay', 
				'WC_Payment_Gateway_Stripe_EPS', 
				'WC_Payment_Gateway_Stripe_Multibanco', 
				'WC_Payment_Gateway_Stripe_Sepa', 
				'WC_Payment_Gateway_Stripe_Sofort', 
				'WC_Payment_Gateway_Stripe_WeChat', 
				'WC_Payment_Gateway_Stripe_FPX', 
				'WC_Payment_Gateway_Stripe_Alipay' 
		) );
		
		$api_class = apply_filters ( 'wc_stripe_rest_api_class', 'WC_Stripe_Rest_API' );
		$this->rest_api = new $api_class ();
		$this->scripts = new WC_Stripe_Frontend_Scripts ();
		
		// allow other plugins to provide their own settings classes.
		$setting_classes = apply_filters ( 'wc_stripe_setting_classes', array( 
				'api_settings' => 'WC_Stripe_API_Settings' 
		) );
		foreach ( $setting_classes as $id => $class_name ) {
			if (class_exists ( $class_name )) {
				$this->{$id} = new $class_name ();
			}
		}
	}

	/**
	 * Return the plugin template path.
	 */
	public function template_path() {
		return 'woo-stripe-payment';
	}

	/**
	 * Return the plguins default directory path for template files.
	 */
	public function default_template_path() {
		return WC_STRIPE_PLUGIN_FILE_PATH . 'templates/';
	}

	/**
	 *
	 * @return string
	 */
	public function rest_uri() {
		return 'wc-stripe/v1/';
	}

	/**
	 *
	 * @return string
	 */
	public function rest_url() {
		return get_rest_url ( null, $this->rest_uri () );
	}

	/**
	 *
	 * @return WC_Stripe_Frontend_Scripts
	 */
	public function scripts() {
		return $this->scripts;
	}

	public function payment_gateways() {
		return $this->payment_gateways;
	}
}

/**
 * Returns the global instance of the WC_Stripe_Manager.
 *
 * @package Stripe/Functions
 * @return WC_Stripe_Manager
 */
function wc_stripe() {
	return WC_Stripe_Manager::instance ();
}

// load singleton
wc_stripe ();