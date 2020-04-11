(function($, wc_stripe) {

    function ApplePay() {
        wc_stripe.BaseGateway.call(this, wc_stripe_applepay_product_params);
        wc_stripe.ProductGateway.call(this);
        this.old_qty = this.get_quantity();
    }

    /**
     * [prototype description]
     * @type {[type]}
     */
    ApplePay.prototype = $.extend({}, wc_stripe.BaseGateway.prototype, wc_stripe.ProductGateway.prototype, wc_stripe.ApplePay.prototype);

    /**
     * @return {[type]}
     */
    ApplePay.prototype.canMakePayment = function() {
        wc_stripe.ApplePay.prototype.canMakePayment.call(this).then(function() {
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
                    $('[name="quantity"]').val(0);
                }.bind(this))
            } else {
                if (this.do_payment_request_update) {
                    this.paymentRequest.update(this.get_payment_request_update({
                        total: {
                            pending: false
                        }
                    }));
                }
            }
        }.bind(this))
    }

    /**
     * @param  {[type]}
     * @return {[type]}
     */
    ApplePay.prototype.start = function(e) {
        if (this.get_quantity() == 0) {
            e.preventDefault();
            this.submit_error(this.params.messages.invalid_amount);
        } else {
            wc_stripe.ApplePay.prototype.start.apply(this, arguments);
        }
    }

    /**
     * @return {[type]}
     */
    ApplePay.prototype.append_button = function() {
        $('#wc-stripe-applepay-container').append(this.$button);
    }

    ApplePay.prototype.found_variation = function(e, variation) {
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
            }.bind(this))
        }
    }

    ApplePay.prototype.add_to_cart = function(e) {
        if (this.$button.is('.disabled') && this.can_pay) {
            $('[name="quantity"]').val(this.old_qty);
            this.submit_error(this.params.messages.choose_product);
            return;
        }
        this.old_qty = this.get_quantity();
        wc_stripe.ProductGateway.prototype.add_to_cart.apply(this, arguments);
    }

    new ApplePay();

}(jQuery, wc_stripe))