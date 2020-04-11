(function($, wc_stripe) {

    var place_order_width = $('#place_order').css('width');

    // this will ensure the place order's width does not change when the 
    // text for the local payment method is added
    $(document.body).on('updated_checkout', function() {
        $('#place_order').css('min-width', place_order_width)
    })

    /**
     * [LocalPayment description]
     */
    function LocalPayment(params) {
        wc_stripe.BaseGateway.call(this, params);
        wc_stripe.CheckoutGateway.call(this);

        $(document.body).on('click', '#place_order', this.place_order.bind(this));
    }

    LocalPayment.prototype = $.extend({}, wc_stripe.BaseGateway.prototype, wc_stripe.CheckoutGateway.prototype);


    LocalPayment.prototype.initialize = function() {
        this.mount_button();
    }

    LocalPayment.prototype.elementType = null;

    /**
     * [createSource description]
     * @return {[type]} [description]
     */
    LocalPayment.prototype.createSource = function() {
        return new Promise(function(resolve, reject) {
            var handler = function(result) {
                if (result.error) {
                    this.submit_error(result.error.message);
                } else {
                    this.payment_token_received = true;
                    $(this.token_selector).val(result.source.id);
                    $(this.container).closest('form').submit();
                }
                resolve();
            }.bind(this);
            if (this.elementType == null) {
                this.payment_token_received = true;
                $(this.container).closest('form').submit();
            } else {
                this.stripe.createSource(this.element, this.getSourceArgs()).then(handler).catch(function(e) {
                    this.submit_error(e.message);
                }.bind(this))
            }
        }.bind(this));
    }

    LocalPayment.prototype.place_order = function(e) {
        if (this.is_gateway_selected()) {
            if (!this.payment_token_received) {
                e.preventDefault();
                this.createSource();
            }
        }
    }

    LocalPayment.prototype.updated_checkout = function() {
        if (this.payment_token_received) {
            this.show_place_order();
        }
    }

    LocalPayment.prototype.show_payment_button = function() {
        this.show_place_order();
    }

    /**
     * [Leave empty so that the place order button is not hidden]
     * @return {[type]} [description]
     */
    LocalPayment.prototype.hide_place_order = function() {

    }

    LocalPayment.prototype.show_place_order = function() {
        wc_stripe.CheckoutGateway.prototype.show_place_order.apply(this, arguments);
        if (this.payment_token_received) {
            $('#place_order').text($('#place_order').data('value'));
        }
    }

    LocalPayment.prototype.getSourceArgs = function() {
        return {
            type: this.params.local_payment_type,
            amount: this.get_total_price_cents(),
            currency: this.get_currency(),
            owner: {
                name: this.get_customer_name('#billing'),
                email: $('#billing_email').val()
            },
            redirect: {
                return_url: this.params.return_url
            }
        }
    }

    LocalPayment.prototype.updated_checkout = function() {
        this.mount_button()
    }

    LocalPayment.prototype.mount_button = function() {
        var id = '#wc_stripe_local_payment_' + this.gateway_id;
        if ($(id).length && this.elementType != null) {
            $(id).empty();
            if (!this.element) {
                this.element = this.elements.create(this.elementType, this.params.element_params);
            }
            this.element.mount(id);
        }

    }

    LocalPayment.prototype.load_external_script = function(url) {
        var script = document.createElement('script');
        script.type = "text/javascript";
        script.src = url;
        script.onload = function() {
            this.script_loaded = true;
        }.bind(this);
        document.body.appendChild(script);
    }

    LocalPayment.prototype.hashChange = function(e) {
        if (this.is_gateway_selected()) {
            var match = e.newURL.match(/response=(.*)/);
            if (match) {
                var obj = JSON.parse(window.atob(match[1]));
                this.stripe[this.confirmation_method](obj.client_secret, this.get_confirmation_args(obj)).then(function(result) {
                    if (result.error) {
                        this.submit_error(result.error.message);
                    }
                }.bind(this))
            }
        }
    }

    LocalPayment.prototype.get_confirmation_args = function(obj) {
        var args = {
            payment_method: {
                billing_details: {
                    name: this.get_first_name('billing') + ' ' + this.get_last_name('billing')
                }
            },
            return_url: obj.return_url
        }
        args['payment_method'][this.params.local_payment_type] = this.element;
        return args;
    }

    /*********** iDEAL ***********/
    function IDEAL(params) {
        this.elementType = 'idealBank';
        this.confirmation_method = 'confirmIdealPayment';
        LocalPayment.call(this, params);
        window.addEventListener('hashchange', this.hashChange.bind(this));
    }

    /******* Sepa *******/
    function Sepa(params) {
        this.elementType = 'iban';
        LocalPayment.call(this, params);
    }

    /****** Klarna ******/
    function Klarna(params) {
        LocalPayment.call(this, params);
        $(document.body).on('change', '.wc-stripe-klarna-category', this.category_change.bind(this));
        window.addEventListener('hashchange', this.hashChange.bind(this));
    }

    function FPX(params) {
        this.elementType = 'fpxBank';
        this.confirmation_method = 'confirmFpxPayment';
        LocalPayment.call(this, params);
        window.addEventListener('hashchange', this.hashChange.bind(this));
    }

    function WeChat(params) {
        LocalPayment.call(this, params);
        window.addEventListener('hashchange', this.hashChange.bind(this));
    }

    IDEAL.prototype.createSource = function() {
        this.payment_token_received = true;
        this.get_form().submit();
    }

    Klarna.prototype.category_change = function(e) {
        $('[id^="klarna-instance-"]').hide();
        var category = $('[name="klarna_category"]:checked').val();
        $('#klarna-instance-' + category).show();
    }

    Klarna.prototype.hashChange = function(e) {
        if (this.is_gateway_selected()) {
            var matches = e.newURL.match(/(local_payment=klarna).+redirect=(.+).+encoded_source=(\w+)/);
            if (matches) {
                e.preventDefault();
                var $form = $(this.token_selector).closest('form');
                $form.unblock().removeClass('processing');
                this.checkout_redirect = matches[2];
                // get the source
                var source = JSON.parse(window.atob(matches[3]));
                window.Klarna.Payments.init({
                    client_token: source.klarna.client_token
                }, function(response) {

                }.bind(this));
                this.payment_categories = source.klarna.payment_method_categories.split(",");
                this.render_ui();
            }
        }
    }

    Klarna.prototype.render_ui = function() {
        if (this.payment_categories.length > 0) {
            $('#wc_stripe_local_payment_stripe_klarna').show();
            for (var i = 0; i < this.payment_categories.length; i++) {
                var container = '#klarna-instance-' + this.payment_categories[i];
                if ($('#klarna-category-' + this.payment_categories[i]).length) {
                    $('#klarna-category-' + this.payment_categories[i]).show();
                    try {
                        window.Klarna.Payments.load({
                            container: container,
                            payment_method_category: this.payment_categories[i], //source.klarna.payment_method_categories
                            instance_id: 'klarna-instance-' + this.payment_categories[i]
                        }, function(response) {

                        }.bind(this));
                    } catch (e) {
                        window.alert(e);
                    }
                }
            }
            $('[name="klarna_category"]').first().prop('checked', true).trigger('change');
        }
    }

    Klarna.prototype.place_order = function(e) {
        if (this.is_gateway_selected()) {
            e.preventDefault();
            this.payment_token_received = true;
            if (this.payment_categories) {
                window.Klarna.Payments.authorize({
                    instance_id: 'klarna-instance-' + $('[name="klarna_category"]:checked').val()
                }, function(res) {
                    if (res.approved) {
                        this.block();
                        this.payment_token_received = true;
                        window.location = this.checkout_redirect;
                    } else {
                        if (res.error) {
                            this.submit_error(res.error);
                        } else {
                            this.submit_error('Klarna error');
                        }
                    }
                }.bind(this));
            } else {
                // let checkout process so we can get back client secret
                $(this.container).closest('form').submit();
            }
        }
    }

    Klarna.prototype.initialize = function() {

    }

    Klarna.prototype.createSource = function() {
        this.block();
    }

    Klarna.prototype.updated_checkout = function() {
        if (this.payment_categories) {
            this.render_ui();
        }
    }

    WeChat.prototype.updated_checkout = function() {
        if (!this.script_loaded && $(this.container).length) {
            this.load_external_script(this.params.qr_script);
        }
        LocalPayment.prototype.updated_checkout.apply(this, arguments);
    }

    WeChat.prototype.hashChange = function(e) {
        if (this.is_gateway_selected()) {
            var match = e.newURL.match(/qrcode=(.*)/);
            if (match) {
                this.qrcode = JSON.parse(window.atob(match[1]));
                this.get_form().unblock().removeClass('processing').addClass('wechat');
                const qrCode = new QRCode('wc_stripe_local_payment_stripe_wechat', {
                    text: this.qrcode.code,
                    width: 128,
                    height: 128,
                    colorDark: '#424770',
                    colorLight: '#f8fbfd',
                    correctLevel: QRCode.CorrectLevel.H,
                });
                $('#wc_stripe_local_payment_stripe_wechat').append('<p class="qrcode-message">' + this.params.qr_message + '</p>');
                this.payment_token_received = true;
                this.show_place_order();
            }
        }
    }

    WeChat.prototype.place_order = function() {
        if (this.get_form().is('.wechat')) {
            window.location = this.qrcode.redirect;
        } else {
            LocalPayment.prototype.place_order.apply(this, arguments);
        }
    }

    FPX.prototype.createSource = function() {
        this.payment_token_received = true;
        this.get_form().submit();
    }

    IDEAL.prototype = $.extend({}, LocalPayment.prototype, IDEAL.prototype);

    Sepa.prototype = $.extend({}, LocalPayment.prototype, Sepa.prototype);

    Klarna.prototype = $.extend({}, LocalPayment.prototype, Klarna.prototype);

    FPX.prototype = $.extend({}, LocalPayment.prototype, FPX.prototype);

    WeChat.prototype = $.extend({}, LocalPayment.prototype, WeChat.prototype);

    /**
     * Local payment types that require JS integration
     * @type {Object}
     */
    const types = {
        'ideal': IDEAL,
        'sepa_debit': Sepa,
        'klarna': Klarna,
        'fpx': FPX,
        'wechat': WeChat
    }

    for (var i in wc_stripe_local_payment_params.gateways) {
        var params = wc_stripe_local_payment_params.gateways[i];
        if (types[params.local_payment_type]) {
            new types[params.local_payment_type](params);
        } else {
            new LocalPayment(params);
        }
    }

}(jQuery, window.wc_stripe))