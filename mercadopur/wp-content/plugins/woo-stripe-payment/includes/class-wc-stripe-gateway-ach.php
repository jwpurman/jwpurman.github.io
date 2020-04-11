<?php
/**
 * @since 3.1.0
 * @author Payment Plugins
 *
 */
class WC_Stripe_Gateway_ACH extends WC_Stripe_Gateway {

	private $public_token = '';

	private $account_id = '';

	private $environment = '';

	public function set_public_token($public_token) {
		$this->public_token = $public_token;
	}

	public function set_account_id($account_id) {
		$this->account_id = $account_id;
	}

	public function set_plaid_environment($environment) {
		$this->environment = $environment;
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see WC_Stripe_Gateway::charge()
	 */
	public function charge($args, $mode = '') {
		$mode = empty ( $mode ) ? wc_stripe_mode () : $mode;
		$args = [ 'charge' => $args, 'mode' => $mode, 
				'account_id' => wc_stripe ()->api_settings->get_option ( 'account_id' ), 
				'plaid' => [ 
						'public_token' => $this->public_token, 
						'account_id' => $this->account_id, 
						'environment' => $this->environment 
				] 
		];
		try {
			// validate env and mode to prevent conflicts
			if ($mode == 'live' && in_array ( $this->environment, [ 
					'sandbox', 'development' 
			] )) {
				throw new Exception ( __ ( 'Please update your ACH environment to Production to match your Stripe API Mode.', 'woo-stripe-payment' ) );
			}
			$response = $this->do_request ( 'POST', '', $args, $mode );
			return $response;
		} catch ( Exception $e ) {
			return new WP_Error ( 'charge-error', $e->getMessage () );
		}
	}

	private function do_request($method, $uri, $args, $mode) {
		$response = wp_safe_remote_post ( $this->base_url ( $uri, $mode ), [ 
				'headers' => [ 
						'Content-Type' => 'application/json' 
				], /**
				 * 20 second timeout
				 */
				'timeout' => 20, 
				'body' => wp_json_encode ( $args ) 
		] );
		if (is_wp_error ( $response )) {
			throw new Exception ( $response->get_error_message () );
		} else {
			$body = json_decode ( $response[ 'body' ], true );
			if ($response[ 'response' ][ 'code' ] > 299) {
				throw new Exception ( $body[ 'message' ] );
			} else {
				$obj = \Stripe\Util\Util::convertToStripeObject ( $body, null );
				$obj->setLastResponse ( $response );
				return $obj;
			}
		}
	}

	/**
	 *
	 * @param string $uri        	
	 */
	private function base_url($uri, $mode) {
		$url = '';
		switch ($mode) {
			case 'live' :
			case 'test' :
				$url = 'https://api.plaid.paymentplugins.com/v1/stripe/';
				// $url = 'http://localhost:8080/v1/stripe/';
				break;
		}
		return $url . $uri;
	}
}