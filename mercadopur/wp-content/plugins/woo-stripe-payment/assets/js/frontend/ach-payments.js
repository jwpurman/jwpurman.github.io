(function($, wc_stripe) {

	function ACH() {
		wc_stripe.BaseGateway.call(this, wc_stripe_ach_params);
		wc_stripe.CheckoutGateway.call(this);
		this.metadata_key = this.params.metadata_key;

		$(document.body).on('payment_method_selected', this.payment_method_selected.bind(this));
	}

	ACH.prototype = $.extend({}, wc_stripe.BaseGateway.prototype, wc_stripe.CheckoutGateway.prototype);

	ACH.prototype.initialize = function() {
		$(document.body).on('click', '#place_order', this.place_order.bind(this));
		this.init_plaid();
	}

	ACH.prototype.init_plaid = function() {
		this.linkHandler = Plaid.create({
			env: this.params.env,
			clientName: this.params.client_name,
			key: this.params.public_key,
			product: ['auth'],
			selectAccount: true,
			countryCodes: ['US'],
			onSuccess: function(public_token, metadata) {
				// serialize metadata and submit form
				this.payment_token_received = true;
				this.set_nonce(public_token);
				this.set_metadata(metadata);
				$(this.container).closest('form').submit();
			}.bind(this),
			onExit: function(err, metadata) {
				if (err != null) {
					this.submit_error(err.error_message);
				}
			}.bind(this)
		});
	}

	ACH.prototype.place_order = function(e) {
		if (this.is_gateway_selected()) {
			if (!this.payment_token_received && !this.is_saved_method_selected()) {
				e.preventDefault();
				this.linkHandler.open();
			}
		}
	}

	ACH.prototype.hide_place_order = function() {

	}

	ACH.prototype.show_payment_button = function() {
		wc_stripe.CheckoutGateway.prototype.show_place_order.apply(this, arguments);
	}

	ACH.prototype.set_metadata = function(metadata) {
		$(this.metadata_key).val(JSON.stringify(metadata));
	}

	ACH.prototype.fees_enabled = function() {
		return this.params.fees_enabled == "1";
	}

	ACH.prototype.payment_method_selected = function() {
		if (this.fees_enabled()) {
			$(document.body).trigger('update_checkout');
		}
	}

	new ACH();

}(jQuery, window.wc_stripe))