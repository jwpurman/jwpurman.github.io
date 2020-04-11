<?php

/**
 * Plugin Name: Stripe For WooCommerce
 * Plugin URI: https://docs.paymentplugins.com/wc-stripe/config/
 * Description: Accept credit cards, Google Pay, & Apple Pay, ACH, Klarna and more using Stripe.
 * Version: 3.1.0
 * Author: Payment Plugins, support@paymentplugins.com
 * Text Domain: woo-stripe-payment
 * Domain Path: /i18n/languages/
 * Tested up to: 5.4
 * WC requires at least: 3.0.0
 * WC tested up to: 4.0.1
 */
function wc_stripe_php_version_notice() {
	$message = sprintf ( __ ( 'Your PHP version is %s but Stripe requires version 5.4+.', 'woo-stripe-payment' ), PHP_VERSION );
	echo '<div class="notice notice-error"><p style="font-size: 16px">' . $message . '</p></div>';
}

if (version_compare ( PHP_VERSION, '5.4', '<' )) {
	add_action ( 'admin_init', 'wc_stripe_php_version_notice' );
	return;
}

define ( 'WC_STRIPE_PLUGIN_FILE_PATH', plugin_dir_path ( __FILE__ ) );
define ( 'WC_STRIPE_ASSETS', plugin_dir_url ( __FILE__ ) . 'assets/' );
define ( 'WC_STRIPE_PLUGIN_NAME', plugin_basename ( __FILE__ ) );
define ( 'WC_STRIPE_CLASS_VERSION', '6.40.0' );
// include main plugin file.
require_once ( WC_STRIPE_PLUGIN_FILE_PATH . 'includes/class-stripe.php' );
// load Stripe classes
require_once ( WC_STRIPE_PLUGIN_FILE_PATH . 'stripe-php-' . WC_STRIPE_CLASS_VERSION . '/init.php' );