(function($, wc_stripe) {

    // Product page functionality
    if ($(document.body).is('.single-product')) {
        /**
         * [PaymentRequest description]
         */
        function PaymentRequest() {
            wc_stripe.BaseGateway.call(this, wc_stripe_payment_request_params);
            wc_stripe.ProductGateway.call(this);
            this.old_qty = this.get_quantity();
        }

        PaymentRequest.prototype = $.extend({}, wc_stripe.BaseGateway.prototype, wc_stripe.ProductGateway.prototype, wc_stripe.PaymentRequest.prototype);

        /**
         * [canMakePayment description]
         * @return {[type]} [description]
         */
        PaymentRequest.prototype.canMakePayment = function() {
            wc_stripe.PaymentRequest.prototype.canMakePayment.apply(this, arguments).then(function(result) {
                $(document.body).on('change', '[name="quantity"]', this.add_to_cart.bind(this));
                $(this.container).parent().parent().addClass('active');
                if (!this.is_variable_product()) {
                    this.cart_calculation().then(function() {
                        this.paymentRequest.update(this.get_payment_request_update({
                            total: {
                                pending: false
                            }
                        }));
                    }.bind(this)).catch(function() {

                    }.bind(this))
                } else {
                    if (this.$button.not('.disabled')) {
                        this.found_variation(null, this.get_product_data().variation);
                    }
                }
            }.bind(this))
        }

        PaymentRequest.prototype.create_button = function() {
            wc_stripe.PaymentRequest.prototype.create_button.apply(this, arguments);
            this.$button = $('#wc-stripe-payment-request-container');
        }

        PaymentRequest.prototype.button_click = function(e) {
            if (this.$button.is('.disabled')) {
                e.preventDefault();
            } else if (this.get_quantity() == 0) {
                e.preventDefault();
                this.submit_error(this.params.messages.invalid_amount);
            }
        }

        /**
         * [add_to_cart description]
         */
        PaymentRequest.prototype.add_to_cart = function(e) {
            if (this.$button.is('.disabled') && this.can_pay) {
                $('[name="quantity"]').val(this.old_qty);
                this.submit_error(this.params.messages.choose_product);
                return;
            }
            this.old_qty = this.get_quantity();
            this.adding_to_cart = true;
            wc_stripe.ProductGateway.prototype.add_to_cart.apply(this, arguments).then(function() {
                this.paymentRequest.update(this.get_payment_request_update({
                    total: {
                        pending: false
                    }
                }));
                this.adding_to_cart = false;
            }.bind(this))
        }

        PaymentRequest.prototype.found_variation = function(e, variation) {
            if (this.can_pay) {
                this.cart_calculation(variation.variation_id).then(function() {
                    wc_stripe.ProductGateway.prototype.found_variation.call(this, e, variation);
                    if (this.can_pay) {
                        this.paymentRequest.update(this.get_payment_request_update({
                            total: {
                                pending: false
                            }
                        }));
                    }
                }.bind(this)).catch(function() {
                    wc_stripe.ProductGateway.prototype.found_variation.call(this, e, variation);
                }.bind(this))
            }
        }

        /**
         * [block description]
         * @return {[type]} [description]
         */
        PaymentRequest.prototype.block = function() {
            $.blockUI({
                message: this.adding_to_cart ? this.params.messages.add_to_cart : null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });
        }

    }

    // Cart page functionality
    if ($(document.body).is('.woocommerce-cart')) {
        /**
         * [PaymentRequest description]
         */
        function PaymentRequest() {
            wc_stripe.BaseGateway.call(this, wc_stripe_payment_request_params);
            wc_stripe.CartGateway.call(this);
        }

        PaymentRequest.prototype = $.extend({}, wc_stripe.BaseGateway.prototype, wc_stripe.CartGateway.prototype, wc_stripe.PaymentRequest.prototype);

        PaymentRequest.prototype.canMakePayment = function() {
            wc_stripe.PaymentRequest.prototype.canMakePayment.apply(this, arguments).then(function() {
                $(this.container).addClass('active').parent().addClass('active');
            }.bind(this))
        }

        /**
         * New paymentRequest is needed since cart might have changed.
         * @return {[type]} [description]
         */
        PaymentRequest.prototype.init_payment_request = function() {
            this.paymentRequestButton.destroy();
        }

        /**
         * [updated_html description]
         * @return {[type]} [description]
         */
        PaymentRequest.prototype.updated_html = function() {
            if (this.can_pay) {
                this.paymentRequestButton.destroy();
                this.initialize();
            }
        }

        PaymentRequest.prototype.button_click = function(e) {
            this.paymentRequest.update(this.get_payment_request_update({
                total: {
                    pending: false
                }
            }));
        }
    }

    // Checkout page functionality
    if ($(document.body).is('.woocommerce-checkout')) {
        /**
         * [PaymentRequest description]
         */
        function PaymentRequest() {
            wc_stripe.BaseGateway.call(this, wc_stripe_payment_request_params);
            wc_stripe.CheckoutGateway.call(this);
        }

        PaymentRequest.prototype = $.extend({}, wc_stripe.BaseGateway.prototype, wc_stripe.CheckoutGateway.prototype, wc_stripe.PaymentRequest.prototype);

        /**
         * [canMakePayment description]
         * @return {[type]} [description]
         */
        PaymentRequest.prototype.canMakePayment = function() {
            wc_stripe.PaymentRequest.prototype.canMakePayment.apply(this, arguments).then(function(result) {
                if (this.banner_enabled()) {
                    $(this.banner_container).show().append('<div id="wc-stripe-payment-request-banner"></div>');
                    $(this.banner_container).show().parent().parent().addClass('active');
                    var elements = this.stripe.elements();
                    var button = elements.create("paymentRequestButton", {
                        paymentRequest: this.paymentRequest,
                        style: {
                            paymentRequestButton: {
                                type: this.params.button.type,
                                theme: this.params.button.theme,
                                height: this.params.button.height
                            }
                        }
                    });
                    button.on('click', this.banner_checkout.bind(this));
                    button.mount("#wc-stripe-payment-request-banner");
                }
            }.bind(this))
        }

        /**
         * [create_button description]
         * @return {[type]} [description]
         */
        PaymentRequest.prototype.create_button = function() {
            var $parent = $('#place_order').parent();
            if (this.$button) {
                this.$button.remove();
            }
            this.$button = $('<div id="wc-stripe-payment-request-container"></div>');
            $parent.append(this.$button);
            wc_stripe.PaymentRequest.prototype.create_button.call(this);
            this.trigger_payment_method_selected();
        }

        /**
         * [updated_checkout description]
         * @return {[type]} [description]
         */
        PaymentRequest.prototype.updated_checkout = function() {
            if (this.can_pay) {
                $(this.container).show();
                this.create_button();
            }
        }

        /**
         * [button_click description]
         * @param  {[type]} e [description]
         * @return {[type]}   [description]
         */
        PaymentRequest.prototype.banner_checkout = function(e) {
            this.set_payment_method(this.gateway_id);
            this.set_use_new_option(true);
        }

        PaymentRequest.prototype.on_token_received = function() {
            wc_stripe.CheckoutGateway.prototype.on_token_received.apply(this, arguments);
            if (this.checkout_fields_valid()) {
                this.get_form().submit();
            }
        }
    }

    new PaymentRequest();

}(jQuery, window.wc_stripe))