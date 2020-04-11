<?php
/**
 * @package Stripe/Admin
 * @author User
 *
 */
class WC_Stripe_Admin_Settings {

	public static function init() {
		add_action ( 'woocommerce_settings_checkout', array( 
				__CLASS__, 'output' 
		) );
		add_action ( 'woocommerce_settings_checkout_stripe_advanced', array( 
				__CLASS__, 'output_advanced_settings' 
		) );
		add_action ( 'woocommerce_settings_checkout_stripe_local_gateways', array( 
				__CLASS__, 'output_local_gateways' 
		) );
		add_action ( 'woocommerce_update_options_checkout_stripe_local_gateways', array( 
				__CLASS__, 'save_local_gateway' 
		) );
		add_filter ( 'wc_stripe_settings_nav_tabs', array( 
				__CLASS__, 'admin_settings_tabs' 
		), 20 );
		add_action ( 'wc_stripe_settings_before_options_stripe_advanced', array( 
				__CLASS__, 'before_options' 
		) );
		add_action ( 'wc_stripe_settings_before_options_stripe_local_gateways', array( 
				__CLASS__, 'before_options' 
		) );
		add_action ( 'woocommerce_update_options_checkout', array( 
				__CLASS__, 'deprecated_save' 
		) );
	}

	public static function output() {
		global $current_section;
		do_action ( 'woocommerce_settings_checkout_' . $current_section );
	}

	public static function output_advanced_settings() {
		self::output_custom_section ( '' );
	}

	public static function output_local_gateways() {
		self::output_custom_section ( 'stripe_ideal' );
	}

	public static function output_custom_section($sub_section = '') {
		global $current_section, $wc_stripe_subsection;
		$wc_stripe_subsection = isset ( $_GET[ 'stripe_sub_section' ] ) ? sanitize_title ( $_GET[ 'stripe_sub_section' ] ) : $sub_section;
		do_action ( 'woocommerce_settings_checkout_' . $current_section . '_' . $wc_stripe_subsection );
	}

	public static function save_local_gateway() {
		self::save_custom_section ( 'stripe_ideal' );
	}

	public static function save_custom_section($sub_section = '') {
		global $current_section, $wc_stripe_subsection;
		$wc_stripe_subsection = isset ( $_GET[ 'stripe_sub_section' ] ) ? sanitize_title ( $_GET[ 'stripe_sub_section' ] ) : $sub_section;
		do_action ( 'woocommerce_update_options_checkout_' . $current_section . '_' . $wc_stripe_subsection );
	}

	public static function deprecated_save() {
		global $current_section;
		if ($current_section && ! did_action ( 'woocommerce_update_options_checkout_' . $current_section )) {
			do_action ( 'woocommerce_update_options_checkout_' . $current_section );
		}
	}

	public static function admin_settings_tabs($tabs) {
		$tabs[ 'stripe_local_gateways' ] = __ ( 'Local Gateways', 'woo-stripe-payment' );
		return $tabs;
	}

	public static function before_options() {
		global $current_section, $wc_stripe_subsection;
		do_action ( 'wc_stripe_settings_before_options_' . $current_section . '_' . $wc_stripe_subsection );
	}
}
WC_Stripe_Admin_Settings::init ();