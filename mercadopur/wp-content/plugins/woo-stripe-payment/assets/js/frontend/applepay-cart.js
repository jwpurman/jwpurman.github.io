(function($, wc_stripe) {

    /**
     * @constructor
     */
    function ApplePay() {
        wc_stripe.BaseGateway.call(this, wc_stripe_applepay_cart_params);
        wc_stripe.CartGateway.call(this);
        this.canMakePayment().then(function() {
            $(this.container).addClass('active').parent().addClass('active');
            this.add_cart_totals_class();
        }.bind(this));

    }

    /**
     * [prototype description]
     * @type {[type]}
     */
    ApplePay.prototype = $.extend({}, wc_stripe.BaseGateway.prototype, wc_stripe.CartGateway.prototype, wc_stripe.ApplePay.prototype);

    /**
     * @return {[type]}
     */
    ApplePay.prototype.append_button = function() {
        $('#wc-stripe-applepay-container').append(this.$button);
    }

    /**
     * @return {[type]}
     */
    ApplePay.prototype.updated_html = function() {
        if (this.can_pay) {
            this.create_button();
            $(this.container).show().addClass('active').parent().addClass('active');
            this.add_cart_totals_class();
        }
    }

    new ApplePay();

}(jQuery, window.wc_stripe))