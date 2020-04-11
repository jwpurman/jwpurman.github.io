<?php
if (! class_exists ( 'WC_Payment_Gateway' )) {
	return;
}

require_once ( WC_STRIPE_PLUGIN_FILE_PATH . 'includes/class-wc-stripe-payment-intent.php' );
require_once ( WC_STRIPE_PLUGIN_FILE_PATH . 'includes/class-wc-stripe-payment-charge.php' );

require_once ( WC_STRIPE_PLUGIN_FILE_PATH . 'includes/traits/wc-stripe-payment-traits.php' );

/**
 *
 * @since 3.0.0
 * @author PaymentPlugins
 * @package Stripe/Abstract
 *         
 */
abstract class WC_Payment_Gateway_Stripe extends WC_Payment_Gateway {
	
	use WC_Stripe_Settings_Trait;

	/**
	 *
	 * @var WC_Stripe_Payment
	 */
	public $payment_object;

	/**
	 *
	 * @var string
	 */
	public $token_key;

	/**
	 *
	 * @var string
	 */
	public $saved_method_key;

	/**
	 *
	 * @var string
	 */
	public $payment_type_key;

	/**
	 *
	 * @var string
	 */
	public $payment_intent_key;

	/**
	 *
	 * @var string
	 */
	public $save_source_key;

	/**
	 *
	 * @var string
	 */
	public $template_name;

	/**
	 *
	 * @var bool
	 */
	protected $checkout_error = false;

	/**
	 * Used to create an instance of a WC_Payment_Token
	 *
	 * @var string
	 */
	protected $token_type;

	/**
	 *
	 * @var WC_Stripe_Gateway
	 */
	protected $gateway;

	/**
	 *
	 * @var WP_Error
	 */
	protected $wp_error;

	/**
	 *
	 * @var string
	 */
	protected $payment_method_token = null;

	/**
	 *
	 * @var string
	 */
	protected $new_source_token = null;

	/**
	 * Is the payment method synchronous or asynchronous
	 *
	 * @var bool
	 */
	public $synchronous = true;

	/**
	 *
	 * @var array
	 */
	protected $post_payment_processes = [];

	/**
	 *
	 * @var bool
	 */
	public $processing_payment = false;

	public function __construct() {
		$this->token_key = $this->id . '_token_key';
		$this->saved_method_key = $this->id . '_saved_method_key';
		$this->save_source_key = $this->id . '_save_source_key';
		$this->payment_type_key = $this->id . '_payment_type_key';
		$this->payment_intent_key = $this->id . '_payment_intent_key';
		$this->has_fields = true;
		$this->init_form_fields ();
		$this->init_settings ();
		$this->title = $this->get_option ( 'title_text' );
		$this->description = $this->get_option ( 'description' );
		$this->hooks ();
		$this->init_supports ();
		$this->gateway = WC_Stripe_Gateway::load ();
		
		$this->payment_object = $this->get_payment_object ();
	}

	public function hooks() {
		add_filter ( 'wc_stripe_settings_nav_tabs', array( 
				$this, 'admin_nav_tab' 
		) );
		add_action ( 'woocommerce_settings_checkout_' . $this->id, [ 
				$this, 'enqueue_admin_scripts' 
		] );
		add_action ( 'woocommerce_update_options_payment_gateways_' . $this->id, [ 
				$this, 'process_admin_options' 
		] );
		add_filter ( 'woocommerce_payment_methods_list_item', [ 
				$this, 'payment_methods_list_item' 
		], 10, 2 );
		add_action ( 'wc_stripe_payment_token_deleted_' . $this->id, [ 
				$this, 'delete_payment_method' 
		], 10, 2 );
		add_filter ( 'woocommerce_subscription_payment_meta', [ 
				$this, 'subscription_payment_meta' 
		], 10, 2 );
		add_action ( 'woocommerce_scheduled_subscription_payment_' . $this->id, [ 
				$this, 'scheduled_subscription_payment' 
		], 10, 2 );
		if (! is_admin ()) {
			add_action ( 'woocommerce_subscriptions_pre_update_payment_method', [ 
					$this, 
					'subscriptions_pre_update_payment_method' 
			], 10, 3 );
			add_action ( 'woocommerce_subscription_payment_method_updated_to_' . $this->id, [ 
					$this, 
					'subscription_payment_method_updated' 
			] );
		}
		add_action ( 'wc_pre_orders_process_pre_order_completion_payment_' . $this->id, array( 
				$this, 'process_pre_order_payment' 
		) );
	}

	public function init_supports() {
		$this->supports = array( 'tokenization', 'products', 
				'subscriptions', 'add_payment_method', 
				'subscription_cancellation', 
				'multiple_subscriptions', 
				'subscription_amount_changes', 
				'subscription_date_changes', 
				'default_credit_card_form', 'refunds', 
				'pre-orders', 
				'subscription_payment_method_change_admin', 
				'subscription_reactivation', 
				'subscription_suspension', 
				'subscription_payment_method_change_customer' 
		);
	}

	public function init_form_fields() {
		$this->form_fields = include wc_stripe ()->plugin_path () . 'includes/gateways/settings/' . str_replace ( [ 
				'stripe_', '_' 
		], [ '', '-' 
		], $this->id ) . '-settings.php';
		$this->form_fields = apply_filters ( 'wc_stripe_form_fields_' . $this->id, $this->form_fields );
	}

	public function get_method_formats() {
		$class_name = 'WC_Payment_Token_' . $this->token_type;
		if (class_exists ( $class_name )) {
			/**
			 *
			 * @var WC_Payment_Token_Stripe
			 */
			$token = new $class_name ();
			return $token->get_formats ();
		}
		return array();
	}

	public function enqueue_admin_scripts() {}

	public function payment_fields() {
		$this->enqueue_frontend_scripts ();
		wc_stripe_token_field ( $this );
		wc_stripe_payment_intent_field ( $this );
		wc_stripe_get_template ( 'checkout/stripe-payment-method.php', array( 
				'gateway' => $this, 
				'tokens' => is_add_payment_method_page () ? null : $this->get_tokens () 
		) );
	}

	/**
	 * Output the product payment fields.
	 */
	public function product_fields() {
		global $product;
		$this->enqueue_frontend_scripts ();
		wc_stripe_token_field ( $this );
		wc_stripe_get_template ( 'product/' . $this->template_name, array( 
				'gateway' => $this, 'product' => $product 
		) );
	}

	public function cart_fields() {
		$this->enqueue_frontend_scripts ();
		wc_stripe_token_field ( $this );
		wc_stripe_get_template ( 'cart/' . $this->template_name, array( 
				'gateway' => $this 
		) );
	}

	/**
	 * Enqueue scripts needed by the gateway on the frontend of the WC shop.
	 *
	 * @param WC_Stripe_Frontend_Scripts $scripts        	
	 */
	public function enqueue_frontend_scripts() {
		global $wp;
		if (is_add_payment_method_page ()) {
			$this->enqueue_add_payment_method_scripts ( wc_stripe ()->scripts () );
		}
		if (is_checkout ()) {
			$this->enqueue_checkout_scripts ( wc_stripe ()->scripts () );
		}
		if (is_cart ()) {
			$this->enqueue_cart_scripts ( wc_stripe ()->scripts () );
		}
		if (is_product ()) {
			$this->enqueue_product_scripts ( wc_stripe ()->scripts () );
		}
		if (! empty ( wc_stripe ()->scripts ()->enqueued_scripts )) {
			wc_stripe ()->scripts ()->enqueue_script ( 'form-handler' );
			wp_enqueue_style ( wc_stripe ()->scripts ()->prefix . 'styles', wc_stripe ()->assets_url () . 'css/stripe.css', array(), wc_stripe ()->version () );
		}
	}

	/**
	 * Enqueue scripts needed by the gateway on the checkout page.
	 *
	 * @param WC_Stripe_Frontend_Scripts $scripts        	
	 */
	public function enqueue_checkout_scripts($scripts) {}

	/**
	 * Enqueue scripts needed by the gateway on the add payment method page.
	 *
	 * @param WC_Stripe_Frontend_Scripts $scripts        	
	 */
	public function enqueue_add_payment_method_scripts($scripts) {
		$this->enqueue_checkout_scripts ( $scripts );
	}

	/**
	 * Enqueue scripts needed by the gateway on the cart page.
	 *
	 * @param WC_Stripe_Frontend_Scripts $scripts        	
	 */
	public function enqueue_cart_scripts($scripts) {}

	/**
	 * Enqueue scripts needed by the gateway on the product page.
	 *
	 * @param WC_Stripe_Frontend_Scripts $scripts        	
	 */
	public function enqueue_product_scripts($scripts) {}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see WC_Payment_Gateway::process_payment()
	 */
	public function process_payment($order_id) {
		$order = wc_get_order ( $order_id );
		
		if ($this->is_change_payment_method_request ()) {
			return [ 'result' => 'success', 
					'redirect' => $order->get_view_order_url () 
			];
		}
		
		do_action ( 'wc_stripe_before_process_payment', $order, $this->id );
		
		if (wc_notice_count ( 'error' ) > 0) {
			return $this->get_order_error ();
		}
		$this->processing_payment = true;
		
		if ($this->order_contains_pre_order ( $order ) && $this->pre_order_requires_tokenization ( $order )) {
			return $this->process_pre_order ( $order );
		}
		
		// if order total is zero, then save meta but don't process payment.
		if ($order->get_total () == 0) {
			return $this->process_zero_total_order ( $order );
		}
		
		$result = $this->payment_object->process_payment ( $order, $this );
		
		if (is_wp_error ( $result )) {
			wc_add_notice ( $result->get_error_message (), 'error' );
			$order->update_status ( 'failed', sprintf ( __ ( 'Error processing payment. Reason: %s', 'woo-stripe-payment' ), $result->get_error_message () ) );
			return $this->get_order_error ();
		}
		
		if ($result->complete_payment) {
			$this->save_order_meta ( $order, $result->charge );
			if ('pending' === $result->charge->status) {
				$order->update_status ( apply_filters ( 'wc_stripe_pending_charge_status', 'on-hold', $order, $this ), sprintf ( __ ( 'Charge %s is pending. Payment Method: %s. Payment will be completed once charge.succeeded webhook received from Stripe.', 'woo-stripe-payment' ), $order->get_transaction_id (), $order->get_payment_method_title () ) );
			} else {
				if ($result->charge->captured) {
					$order->payment_complete ( $result->charge->id );
				} else {
					$order_status = $this->get_option ( 'order_status' );
					$order->update_status ( apply_filters ( 'wc_stripe_authorized_order_status', 'default' === $order_status ? 'on-hold' : $order_status, $order, $this ) );
				}
				$order->add_order_note ( sprintf ( __ ( 'Order %s successful in Stripe. Charge: %s. Payment Method: %s', 'woo-stripe-payment' ), $result->charge->captured ? __ ( 'charge', 'woo-stripe-payment' ) : __ ( 'authorization', 'woo-stripe-payment' ), $order->get_transaction_id (), $order->get_payment_method_title () ) );
			}
			
			$this->trigger_post_payment_processes ( $order, $this );
			
			return array( 'result' => 'success', 
					'redirect' => $order->get_checkout_order_received_url () 
			);
		} else {
			return [ 'result' => 'success', 
					'redirect' => $result->redirect 
			];
		}
	}

	/**
	 *
	 * @return array
	 */
	public function get_localized_params() {
		return array( 'gateway_id' => $this->id, 
				'api_key' => wc_stripe_get_publishable_key (), 
				'saved_method_selector' => '[name="' . $this->saved_method_key . '"]', 
				'token_selector' => '[name="' . $this->token_key . '"]', 
				'payment_intent_selector' => '[name="' . $this->payment_intent_key . '"]', 
				'messages' => array( 
						'terms' => __ ( 'Please read and accept the terms and conditions to proceed with your order.', 'woocommerce' ) 
				), 
				'routes' => array( 
						'setup_intent' => wc_stripe ()->rest_api->payment_intent->rest_url ( 'setup-intent' ), 
						'sync_intent' => wc_stripe ()->rest_api->payment_intent->rest_url ( 'sync-payment-intent' ), 
						'add_to_cart' => wc_stripe ()->rest_api->cart->rest_url ( 'add-to-cart' ), 
						'cart_calculation' => wc_stripe ()->rest_api->cart->rest_url ( 'cart-calculation' ), 
						'shipping_method' => wc_stripe ()->rest_api->cart->rest_url ( 'shipping-method' ), 
						'shipping_address' => wc_stripe ()->rest_api->cart->rest_url ( 'shipping-address' ), 
						'checkout' => wc_stripe ()->rest_api->checkout->rest_url ( 'checkout' ) 
				), 
				'rest_nonce' => wp_create_nonce ( 'wp_rest' ), 
				'needs_shipping' => $this->get_needs_shipping (), 
				'banner_enabled' => $this->banner_checkout_enabled (), 
				'currency' => get_woocommerce_currency (), 
				'total_label' => __ ( 'Total', 'woo-stripe-payment' ), 
				'country_code' => wc_get_base_location ()[ 'country' ] 
		);
	}

	/**
	 * Save the Stripe data to the order.
	 *
	 * @param WC_Order $order        	
	 * @param \Stripe\Charge $charge        	
	 */
	public function save_order_meta($order, $charge) {
		/**
		 *
		 * @var WC_Payment_Token_Stripe $token
		 */
		$token = $this->get_payment_token ( $this->get_payment_method_from_charge ( $charge ), $charge->payment_method_details );
		$order->set_transaction_id ( $charge->id );
		$order->set_payment_method_title ( $token->get_payment_method_title () );
		$order->update_meta_data ( WC_Stripe_Constants::MODE, wc_stripe_mode () );
		$order->update_meta_data ( WC_Stripe_Constants::CHARGE_STATUS, $charge->status );
		$order->update_meta_data ( WC_Stripe_Constants::PAYMENT_METHOD_TOKEN, $token->get_token () );
		$order->save ();
		
		// if WCS is active and there are subscriptions in the order, save meta data
		if (wcs_stripe_active () && wcs_order_contains_subscription ( $order )) {
			foreach ( wcs_get_subscriptions_for_order ( $order ) as $subscription ) {
				$subscription->set_transaction_id ( $charge->id );
				$subscription->set_payment_method_title ( $token->get_payment_method_title () );
				$subscription->update_meta_data ( WC_Stripe_Constants::MODE, wc_stripe_mode () );
				$subscription->update_meta_data ( WC_Stripe_Constants::CHARGE_STATUS, $charge->status );
				$subscription->update_meta_data ( WC_Stripe_Constants::PAYMENT_METHOD_TOKEN, $token->get_token () );
				$subscription->update_meta_data ( WC_Stripe_Constants::CUSTOMER_ID, wc_stripe_get_customer_id ( $order->get_user_id () ) );
				$subscription->save ();
			}
		}
	}

	/**
	 * Given a charge object, return the ID of the payment method used for the charge.
	 *
	 * @since 3.0.6
	 * @param \Stripe\Charge $charge        	
	 */
	public function get_payment_method_from_charge($charge) {
		return $this->payment_object->get_payment_method_from_charge ( $charge );
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see WC_Payment_Gateway::add_payment_method()
	 */
	public function add_payment_method() {
		$user_id = get_current_user_id ();
		try {
			if (! is_user_logged_in ()) {
				throw new Exception ( __ ( 'User must be logged in.', 'woo-stripe-payment' ) );
			}
			
			$customer_id = wc_stripe_get_customer_id ( $user_id );
			
			if (empty ( $customer_id )) {
				$customer_id = $this->create_customer ( $user_id );
			}
			
			$result = $this->create_payment_method ( $this->get_new_source_token (), $customer_id );
			
			if (is_wp_error ( $result )) {
				wc_add_notice ( sprintf ( __ ( 'Error saving your payment method. Reason: %s', 'woo-stripe-payment' ), $result->get_error_message () ), 'error' );
				return array( 'result' => 'error' 
				);
			}
			$result->set_user_id ( $user_id );
			$result->save ();
			WC_Payment_Tokens::set_users_default ( $user_id, $result->get_id () );
			
			do_action ( 'wc_stripe_add_payment_method_success', $result );
			
			return array( 'result' => 'success', 
					'redirect' => wc_get_account_endpoint_url ( 'payment-methods' ) 
			);
		} catch ( \Stripe\Error\Base $e ) {
			wc_add_notice ( sprintf ( __ ( 'Error saving payment method. Reason: %s', 'woo-stripe-payment' ), $e->getMessage () ), 'error' );
			return array( 'result' => 'error' 
			);
		}
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see WC_Payment_Gateway::process_refund()
	 */
	public function process_refund($order_id, $amount = null, $reason = '') {
		$order = wc_get_order ( $order_id );
		$result = $this->payment_object->process_refund ( $order, $amount, $reason );
		
		if (! is_wp_error ( $result )) {
			$order->add_order_note ( sprintf ( __ ( 'Order refunded in Stripe. Amount: %s', 'woo-stripe-payment' ), wc_price ( $amount, array( 
					'currency' => $order->get_currency () 
			) ) ) );
		}
		return $result;
	}

	/**
	 * Captures the charge for the Stripe order.
	 *
	 * @param float $amount        	
	 * @param WC_Order $order        	
	 */
	public function capture_charge($amount, $order) {
		$result = $this->retrieve_charge ( $order->get_transaction_id (), wc_stripe_order_mode ( $order ) );
		
		if (is_wp_error ( $result )) {
			return;
		} else {
			if (! $result->captured) {
				$result = $this->payment_object->capture_charge ( $amount, $order );
				
				if (! is_wp_error ( $result )) {
					remove_action ( 'woocommerce_order_status_completed', 'wc_stripe_order_status_completed' );
					$order->payment_complete ();
					if ($amount != $order->get_total ()) {
						$order->set_total ( $amount );
						$order->save ();
					}
					$order->add_order_note ( sprintf ( __ ( 'Order amount captured in Stripe. Amount: %s', 'woo-stripe-payment' ), wc_price ( $amount, array( 
							'currency' => $order->get_currency () 
					) ) ) );
				} else {
					$order->add_order_note ( sprintf ( __ ( 'Error capturing charge in Stripe. Reason: %s', 'woo-stripe-payment' ), $result->get_error_message () ) );
				}
			}
		}
		return $result;
	}

	/**
	 * Void the Stripe charge.
	 *
	 * @param WC_Order $order        	
	 */
	public function void_charge($order) {
		$result = $this->payment_object->void_charge ( $order );
		
		if (is_wp_error ( $result )) {
			$order->add_order_note ( sprintf ( __ ( 'Error voiding charge. Reason: %s', 'woo-stripe-payment' ), $result->get_error_message () ) );
		} else {
			$order->add_order_note ( __ ( 'Charge voided in Stripe.', 'woo-stripe-payment' ) );
		}
	}

	/**
	 * Return the \Stripe\Charge object
	 *
	 * @param String $charge_id        	
	 * @param String $mode        	
	 * @return WP_Error|\Stripe\Charge
	 */
	public function retrieve_charge($charge_id, $mode = '') {
		return $this->gateway->get_charge ( $charge_id, $mode );
	}

	/**
	 *
	 * @param string $method_id        	
	 * @param \Stripe\Card|array $method_details        	
	 */
	public function get_payment_token($method_id, $method_details = null) {
		$class_name = 'WC_Payment_Token_' . $this->token_type;
		if (class_exists ( $class_name )) {
			/**
			 *
			 * @var WC_Payment_Token_Stripe $token
			 */
			$token = new $class_name ( '', $method_details );
			$token->set_token ( $method_id );
			$token->set_gateway_id ( $this->id );
			$token->set_format ( $this->get_option ( 'method_format' ) );
			$token->set_environment ( wc_stripe_mode () );
			if ($method_details) {
				$token->details_to_props ( $method_details );
			}
			return $token;
		}
	}

	/**
	 * Return a failed order response.
	 *
	 * @return array
	 */
	public function get_order_error() {
		wc_stripe_set_checkout_error ();
		return array( 'result' => 'failure' 
		);
	}

	/**
	 * Return the payment source the customer has chosen to use.
	 * This can be a saved source
	 * or a one time use source.
	 */
	public function get_payment_source() {
		if ($this->use_saved_source ()) {
			return $this->get_saved_source_id ();
		} else {
			if ($this->payment_method_token) {
				return $this->payment_method_token;
			}
			return $this->get_new_source_token ();
		}
	}

	/**
	 * Returns the payment method the customer wants to use.
	 * This can be a saved payment method
	 * or a new payment method.
	 */
	public function get_payment_method_from_request() {
		return $this->get_payment_source ();
	}

	public function get_payment_intent_id() {
		return ! empty ( $_POST[ $this->payment_intent_key ] ) ? $_POST[ $this->payment_intent_key ] : '';
	}

	/**
	 * Return true of the customer is using a saved payment method.
	 */
	public function use_saved_source() {
		return ( ! empty ( $_POST[ $this->payment_type_key ] ) && $_POST[ $this->payment_type_key ] === 'saved' ) || $this->payment_method_token;
	}

	/**
	 *
	 * @deprecated
	 *
	 */
	public function get_new_source_id() {
		return $this->get_new_source_token ();
	}

	public function get_new_source_token() {
		return null != $this->new_source_token ? $this->new_source_token : ( ! empty ( $_POST[ $this->token_key ] ) ? $_POST[ $this->token_key ] : '' );
	}

	public function get_saved_source_id() {
		return ! empty ( $_POST[ $this->saved_method_key ] ) ? $_POST[ $this->saved_method_key ] : $this->payment_method_token;
	}

	/**
	 * Create a customer in the stripe gateway.
	 *
	 * @param int $user_id        	
	 */
	public function create_customer($user_id) {
		$customer = WC ()->customer;
		$customer_id = $this->gateway->create_customer ( array( 
				'email' => $customer->get_email (), 
				'name' => sprintf ( '%s %s', $customer->get_first_name (), $customer->get_last_name () ), 
				'phone' => $customer->get_billing_phone () 
		) );
		if (! is_wp_error ( $customer )) {
			wc_stripe_save_customer ( $customer_id, $user_id );
			return $customer->id;
		} else {
			return $customer;
		}
	}

	/**
	 * Creates a payment method in Stripe.
	 *
	 * @param string $id
	 *        	payment method id
	 * @param string $customer_id
	 *        	WC Stripe customer ID
	 * @return WC_Payment_Token_Stripe|WP_Error
	 */
	public function create_payment_method($id, $customer_id) {
		$token = $this->get_payment_token ( $id );
		$token->set_customer_id ( $customer_id );
		
		$result = $token->save_payment_method ();
		
		if (is_wp_error ( $result )) {
			return $result;
		} else {
			$token->set_token ( $result->id );
			$token->details_to_props ( $result );
			return $token;
		}
	}

	/**
	 *
	 * @param array $item        	
	 * @param WC_Payment_Token_Stripe_CC $payment_token        	
	 */
	public function payment_methods_list_item($item, $payment_token) {
		if ($payment_token->get_type () === $this->token_type) {
			$item[ 'method' ][ 'last4' ] = $payment_token->get_last4 ();
			$item[ 'method' ][ 'brand' ] = ucfirst ( $payment_token->get_brand () );
			if ($payment_token->has_expiration ()) {
				$item[ 'expires' ] = sprintf ( '%s / %s', $payment_token->get_exp_month (), $payment_token->get_exp_year () );
			} else {
				$item[ 'expires' ] = __ ( 'n/a', 'woo-stripe-payment' );
			}
			$item[ 'wc_stripe_method' ] = true;
		}
		return $item;
	}

	/**
	 *
	 * @param string $token_id        	
	 * @param WC_Payment_Token_Stripe $token        	
	 */
	public function delete_payment_method($token_id, $token) {
		$token->delete_from_stripe ();
	}

	public function saved_payment_methods($tokens = array()) {
		wc_stripe_get_template ( 'payment-methods.php', array( 
				'tokens' => $tokens, 'gateway' => $this 
		) );
	}

	public function get_new_method_label() {
		return __ ( 'New Card', 'woo-stripe-payment' );
	}

	public function get_saved_methods_label() {
		return __ ( 'Saved Cards', 'woo-stripe-payment' );
	}

	/**
	 * Return true if shipping is needed.
	 * Shipping is based on things like if the cart or product needs shipping.
	 *
	 * @return bool
	 */
	public function get_needs_shipping() {
		if (is_checkout () || is_cart ()) {
			global $wp;
			if (wcs_stripe_active () && WC_Subscriptions_Change_Payment_Gateway::$is_request_to_change_payment) {
				return false;
			}
			// return false if this is the order pay page. Gateways that have payment sheets don't need
			// to make any changes to the order.
			if (! empty ( $wp->query_vars[ 'order-pay' ] )) {
				return false;
			}
			return WC ()->cart->needs_shipping ();
		}
		if (is_product ()) {
			global $product;
			return is_a ( $product, 'WC_Product' ) && $product->needs_shipping ();
		}
	}

	/**
	 * Return true of the payment method should be saved.
	 *
	 * @param WC_Order $order        	
	 * @return bool
	 */
	public function should_save_payment_method($order) {
		if (! $this->use_saved_source ()) {
			if (wcs_stripe_active ()) {
				if (wcs_order_contains_subscription ( $order )) {
					return true;
				}
				if (wcs_cart_contains_renewal ()) {
					return true;
				}
			}
			if (! empty ( $_POST[ $this->save_source_key ] )) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Returns true if the save payment method checkbox can be displayed.
	 *
	 * @return boolean
	 */
	public function show_save_source() {
		global $wp;
		if (wcs_stripe_active ()) {
			if (WC_Subscriptions_Cart::cart_contains_subscription ()) {
				return false;
			}
			if (WC_Subscriptions_Change_Payment_Gateway::$is_request_to_change_payment) {
				return false;
			}
			if (wcs_cart_contains_renewal ()) {
				return false;
			}
		}
		return is_user_logged_in () && is_checkout () && $this->is_active ( 'save_card_enabled' );
	}

	/**
	 * Returns a formatted array of items for display in the payment gateway's payment sheet.
	 *
	 * @param bool $encode        	
	 * @return []
	 */
	public function get_display_items($encode = false) {}

	/**
	 * Returns a formatted array of shipping methods for display in the payment gateway's
	 * payment sheet.
	 *
	 * @param bool $encode        	
	 * @return []
	 */
	public function get_shipping_methods($encode = false) {}

	/**
	 * Return true if product page checkout is enabled for this gateway
	 *
	 * @return bool
	 */
	public function product_checkout_enabled() {
		return in_array ( 'product', $this->get_option ( 'payment_sections', [] ) );
	}

	/**
	 * Return true if cart page checkout is enabled for this gateway
	 *
	 * @return bool
	 */
	public function cart_checkout_enabled() {
		return in_array ( 'cart', $this->get_option ( 'payment_sections', [] ) );
	}

	/**
	 * Return true if checkout page banner is enabled for this gateway
	 *
	 * @return bool
	 */
	public function banner_checkout_enabled() {
		global $wp;
		return empty ( $wp->query_vars[ 'order-pay' ] ) && $this->supports ( 'wc_stripe_banner_checkout' ) && in_array ( 'checkout_banner', $this->get_option ( 'payment_sections', [] ) );
	}

	/**
	 * Decorate the response with data specific to the gateway.
	 *
	 * @param [] $data        	
	 */
	public function add_to_cart_response($data) {
		return $data;
	}

	/**
	 * Decorate the update shipping method reponse with data.
	 *
	 * @param [] $data        	
	 */
	public function get_update_shipping_method_response($data) {
		return $data;
	}

	/**
	 * Decorate the update shipping address respond with data.
	 *
	 * @param [] $data        	
	 */
	public function get_update_shipping_address_response($data) {
		return apply_filters ( 'wc_stripe_update_shipping_address_response', $data );
	}

	/**
	 * Save the customer's payment method.
	 * If the payment method has already been saved to the customer
	 * then simply return true.
	 *
	 * @param string $id        	
	 * @param WC_Order $order        	
	 * @return WP_Error|bool
	 */
	public function save_payment_method($id, $order) {
		$token = $this->create_payment_method ( $id, wc_stripe_get_customer_id ( $order->get_user_id () ) );
		if (is_wp_error ( $token )) {
			$this->wp_error = $token;
			$order->add_order_note ( sprintf ( __ ( 'Attemp to save payment method failed. Reason: %s', 'woo-stripe-payment' ), $token->get_error_message () ) );
			return $token;
		}
		$token->set_user_id ( $order->get_user_id () );
		$token->save ();
		
		// set token value so it can be used for other processes.
		$this->payment_method_token = $token->get_token ();
		return true;
	}

	/**
	 * Set an error on the order.
	 * This error is used on the frontend to alert customer's to a failed payment method save.
	 *
	 * @param WC_Order $order        	
	 * @param WP_Error $error        	
	 */
	public function set_payment_save_error($order, $error) {
		if (wcs_stripe_active () && wcs_order_contains_subscription ( $order )) {
			$message = __ ( 'We were not able to save your payment method. To prevent billing issues with your subscription, please add a payment method to the subscription.', 'woo-stripe-payment' );
		} else {
			$message = sprintf ( __ ( 'We were not able to save your payment method. Reason: %s', 'woo-stripe-payment' ), $error->get_error_message () );
		}
		$order->update_meta_data ( '_wc_stripe_order_error', $message );
		$order->save ();
	}

	/**
	 *
	 * @param string $token_id        	
	 * @param int $user_id        	
	 * @return null|WC_Payment_Token_Stripe_CC
	 */
	public function get_token($token_id, $user_id) {
		$tokens = WC_Payment_Tokens::get_customer_tokens ( $user_id, $this->id );
		foreach ( $tokens as $token ) {
			if ($token_id === $token->get_token ()) {
				return $token;
			}
		}
		return null;
	}

	/**
	 *
	 * @param array $payment_meta        	
	 * @param WC_Subscription $subscription        	
	 */
	public function subscription_payment_meta($payment_meta, $subscription) {
		$payment_meta[ $this->id ] = array( 
				'post_meta' => array( 
						WC_Stripe_Constants::PAYMENT_METHOD_TOKEN => array( 
								'value' => $this->get_order_meta_data ( WC_Stripe_Constants::PAYMENT_METHOD_TOKEN, $subscription ), 
								'label' => __ ( 'Payment Method Token', 'woo-stripe-payment' ) 
						) 
				) 
		);
		return $payment_meta;
	}

	/**
	 *
	 * @param float $amount        	
	 * @param WC_Order $order        	
	 */
	public function scheduled_subscription_payment($amount, $order) {
		$this->processing_payment = true;
		
		$result = $this->payment_object->scheduled_subscription_payment ( $amount, $order );
		
		if (is_wp_error ( $result )) {
			$order->update_status ( 'failed' );
			$order->add_order_note ( sprintf ( __ ( 'Recurring payment for order failed. Reason: %s', 'woo-stripe-payment' ), $result->get_error_message () ) );
			return;
		}
		
		$this->save_order_meta ( $order, $result->charge );
		
		if ($result->complete_payment) {
			if ($result->charge->captured) {
				$order->payment_complete ( $result->charge->id );
				$order->add_order_note ( sprintf ( __ ( 'Recurring payment captured in Stripe. Payment method: %s', 'woo-stripe-payment' ), $order->get_payment_method_title () ) );
			} else {
				$order->update_status ( apply_filters ( 'wc_stripe_authorized_renewal_order_status', 'on-hold', $order, $this ), sprintf ( __ ( 'Recurring payment authorized in Stripe. Payment method: %s', 'woo-stripe-payment' ), $order->get_payment_method_title () ) );
			}
		} else {
			$order->update_status ( 'pending', sprintf ( __ ( 'Customer must manually complete payment for payment method %s', 'woo-stripe-payment' ), $order->get_payment_method_title () ) );
		}
	}

	/**
	 *
	 * @param WC_Subscription $subscription        	
	 * @param string $new_payment_method        	
	 * @param string $old_payment_method        	
	 */
	public function subscriptions_pre_update_payment_method($subscription, $new_payment_method, $old_payment_method) {
		if ($new_payment_method === $this->id) {
			// customer is using a new payment method so we need to save it.
			if (! $this->use_saved_source ()) {
				$result = $this->save_payment_method ( $this->get_new_source_token (), $subscription );
				if (is_wp_error ( $result )) {
					wc_add_notice ( sprintf ( __ ( 'Error saving payment method for subscription. Reason: %s', 'woo-stripe-payment' ), $result->get_error_message () ), 'error' );
					// redirect to change payment method page.
					wp_safe_redirect ( $subscription->get_view_order_url () );
				}
			} else {
				// set the payment method token so it can be used downstream.
				$this->payment_method_token = $this->get_payment_source ();
			}
		}
	}

	/**
	 * Update the subscription with the new meta data for the gateway.
	 * Save any new method the customer is trying to use.
	 *
	 * @param WC_Subscription $subscription        	
	 */
	public function subscription_payment_method_updated($subscription) {
		$token = $this->get_token ( $this->payment_method_token, $subscription->get_user_id () );
		
		// update the meta data needed by the gateway to process a subscription payment.
		$subscription->update_meta_data ( WC_Stripe_Constants::PAYMENT_METHOD_TOKEN, $this->payment_method_token );
		$subscription->set_payment_method_title ( $token->get_payment_method_title () );
		$subscription->save ();
	}

	/**
	 * Return true if this request is to change the payment method of a WC Subscription.
	 *
	 * @return bool
	 */
	public function is_change_payment_method_request() {
		return wcs_stripe_active () && did_action ( 'woocommerce_subscriptions_pre_update_payment_method' );
	}

	/**
	 * Sets the ID of a payment token.
	 *
	 * @param string $id        	
	 */
	public function set_payment_method_token($id) {
		$this->payment_method_token = $id;
	}

	public function set_new_source_token($token) {
		$this->new_source_token = $token;
	}

	/**
	 *
	 * @deprecated
	 *
	 * @param WC_Order $order        	
	 */
	public function get_order_description($order) {
		return sprintf ( __ ( 'Order %s from %s', 'woo-stripe-payment' ), $order->get_order_number (), get_bloginfo ( 'name' ) );
	}

	/**
	 *
	 * @param WC_Order $order        	
	 */
	public function process_zero_total_order($order) {
		// save payment method if necessary
		if ($this->should_save_payment_method ( $order )) {
			$result = $this->save_payment_method ( $this->get_new_source_token (), $order );
			if (is_wp_error ( $result )) {
				wc_add_notice ( $result->get_error_message (), 'error' );
				return $this->get_order_error ();
			}
		} else {
			$this->payment_method_token = $this->get_saved_source_id ();
		}
		$this->save_zero_total_meta ( $order );
		$order_status = $this->get_option ( 'order_status' );
		if ('capture' === $this->get_option ( 'charge_type' )) {
			$order->payment_complete ();
		} else {
			$order_status = $this->get_option ( 'order_status' );
			$order->update_status ( apply_filters ( 'wc_stripe_authorized_order_status', 'default' === $order_status ? 'on-hold' : $order_status, $order, $this ) );
		}
		WC ()->cart->empty_cart ();
		return array( 'result' => 'success', 
				'redirect' => $order->get_checkout_order_received_url () 
		);
	}

	public function process_pre_order($order) {
		// maybe save payment method
		if (! $this->use_saved_source ()) {
			$result = $this->save_payment_method ( $this->get_new_source_token (), $order );
			if (is_wp_error ( $result )) {
				wc_add_notice ( $result->get_error_message (), 'error' );
				return $this->get_order_error ();
			}
		} else {
			$this->payment_method_token = $this->get_saved_source_id ();
		}
		WC_Pre_Orders_Order::mark_order_as_pre_ordered ( $order );
		$this->save_zero_total_meta ( $order );
		
		return array( 'result' => 'success', 
				'redirect' => $order->get_checkout_order_received_url () 
		);
	}

	/**
	 *
	 * @param WC_Order $order        	
	 */
	protected function save_zero_total_meta($order) {
		$token = $this->get_token ( $this->payment_method_token, $order->get_user_id () );
		$order->set_payment_method_title ( $token->get_payment_method_title () );
		$order->update_meta_data ( WC_Stripe_Constants::MODE, wc_stripe_mode () );
		$order->update_meta_data ( WC_Stripe_Constants::PAYMENT_METHOD_TOKEN, $token->get_token () );
		$order->update_meta_data ( WC_Stripe_Constants::CUSTOMER_ID, wc_stripe_get_customer_id ( $order->get_user_id () ) );
		$order->save ();
		
		if (wcs_stripe_active () && wcs_order_contains_subscription ( $order )) {
			foreach ( wcs_get_subscriptions_for_order ( $order ) as $subscription ) {
				/**
				 *
				 * @var WC_Subscription $subscription
				 */
				$subscription->set_payment_method_title ( $token->get_payment_method_title () );
				$subscription->update_meta_data ( WC_Stripe_Constants::MODE, wc_stripe_mode () );
				$subscription->update_meta_data ( WC_Stripe_Constants::PAYMENT_METHOD_TOKEN, $token->get_token () );
				$subscription->update_meta_data ( WC_Stripe_Constants::CUSTOMER_ID, wc_stripe_get_customer_id ( $order->get_user_id () ) );
				$subscription->save ();
			}
		}
	}

	/**
	 * Pre orders can't be mixed with regular products.
	 *
	 * @param WC_Order $order        	
	 */
	protected function order_contains_pre_order($order) {
		return wc_stripe_pre_orders_active () && WC_Pre_Orders_Order::order_contains_pre_order ( $order );
	}

	/**
	 *
	 * @param WC_Order $order        	
	 * @return boolean
	 */
	protected function pre_order_requires_tokenization($order) {
		return WC_Pre_Orders_Order::order_requires_payment_tokenization ( $order );
	}

	/**
	 * Sets a lock on the order.
	 * Default behavior is a 2 minute lock.
	 *
	 * @param WC_Order|int $order        	
	 */
	public function set_order_lock($order) {
		set_transient ( '_stripe_lock_order_' . ( is_object ( $order ) ? $order->get_id () : $order ), apply_filters ( 'wc_stripe_set_order_lock', time () + 2 * MINUTE_IN_SECONDS ) );
	}

	/**
	 * Removes the lock on the order
	 *
	 * @param WC_Order|int $order        	
	 */
	public function release_order_lock($order) {
		delete_transient ( '_stripe_lock_order_' . ( is_object ( $order ) ? $order->get_id () : $order ) );
	}

	/**
	 * Returns true of the order has been locked.
	 * If the lock exists and is greater than current time
	 * method returns true;
	 *
	 * @param WC_Order|int $order        	
	 */
	public function has_order_lock($order) {
		$lock = get_transient ( '_stripe_lock_order_' . ( is_object ( $order ) ? $order->get_id () : $order ) );
		return $lock != false;
	}

	public function set_post_payment_process($callback) {
		$this->post_payment_processes[] = $callback;
	}

	/**
	 *
	 * @param WC_Order $order        	
	 * @param WC_Payment_Gateway_Stripe $gateway        	
	 */
	public function trigger_post_payment_processes($order, $gateway) {
		foreach ( $this->post_payment_processes as $callback ) {
			call_user_func_array ( $callback, func_get_args () );
		}
	}

	public function validate_payment_sections_field($key, $value) {
		if (empty ( $value )) {
			$value = [];
		}
		return $value;
	}

	/**
	 *
	 * @param WC_Order $order        	
	 */
	public function process_pre_order_payment($order) {
		$this->processing_payment = true;
		
		$result = $this->payment_object->process_pre_order_payment ( $order );
		
		if (is_wp_error ( $result )) {
			$order->update_status ( 'failed' );
			$order->add_order_note ( sprintf ( __ ( 'Pre-order payment for order failed. Reason: %s', 'woo-stripe-payment' ), $result->get_error_message () ) );
		} else {
			if ($result->complete_payment) {
				
				$this->save_order_meta ( $order, $result->charge );
				
				if ($result->charge->captured) {
					$order->payment_complete ( $result->charge->id );
					$order->add_order_note ( sprintf ( __ ( 'Pre-order payment captured in Stripe. Payment method: %s', 'woo-stripe-payment' ), $order->get_payment_method_title () ) );
				} else {
					$order->update_status ( apply_filters ( 'wc_stripe_authorized_preorder_order_status', 'on-hold', $order, $this ), sprintf ( __ ( 'Pre-order payment authorized in Stripe. Payment method: %s', 'woo-stripe-payment' ), $order->get_payment_method_title () ) );
				}
			} else {
				$order->update_status ( 'pending', sprintf ( __ ( 'Customer must manually complete payment for payment method %s', 'woo-stripe-payment' ), $order->get_payment_method_title () ) );
			}
		}
	}

	/**
	 * Given a meta key, see if there is a value for that key in another plugin.
	 * This acts as a lazy conversion
	 * method for merchants that have switched to our plugin from other plugins.
	 *
	 * @since 3.1.0
	 * @param string $meta_key        	
	 * @param WC_Order $order        	
	 * @param string $context        	
	 */
	public function get_order_meta_data($meta_key, $order, $context = 'view') {
		$value = $order->get_meta ( $meta_key, true, $context );
		// value is empty so check metadata from other plugins
		if (empty ( $value )) {
			$keys = [];
			switch ($meta_key) {
				case WC_Stripe_Constants::PAYMENT_METHOD_TOKEN :
					$keys = [ '_stripe_source_id' 
					];
					break;
				case WC_Stripe_Constants::CUSTOMER_ID :
					$keys = [ '_stripe_customer_id' 
					];
					break;
				case WC_Stripe_Constants::PAYMENT_INTENT_ID :
					$keys = [ '_stripe_intent_id' 
					];
			}
			if ($keys) {
				$meta_data = $order->get_meta_data ();
				if ($meta_data) {
					$keys = array_intersect ( wp_list_pluck ( $meta_data, 'key' ), $keys );
					$array_keys = array_keys ( $keys );
					if (! empty ( $array_keys )) {
						$value = $meta_data[ current ( $array_keys ) ]->value;
						update_post_meta ( $order->get_id (), $meta_key, $value );
					}
				}
			}
		}
		
		return $value;
	}
}