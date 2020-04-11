(function(window, $) {
    window.wc_stripe = {};

    try {
        /**
         * [Initiate Stripe]
         * @type {[type]}
         */
        var stripe = Stripe(wc_stripe_params_v3.api_key);
    } catch (error) {
        window.alert(error);
        console.log(error);
        return;
    }

    /**
     * @consructor
     */
    wc_stripe.BaseGateway = function(params) {
        this.params = params;
        this.gateway_id = this.params.gateway_id;
        this.token_selector = this.params.token_selector;
        this.saved_method_selector = this.params.saved_method_selector;
        this.payment_intent_selector = this.params.payment_intent_selector;
        this.payment_token_received = false;
        this.stripe = stripe;
        this.elements = stripe.elements($.extend({}, { locale: 'auto' }, this.get_element_options()));
        this.initialize();
    }

    wc_stripe.BaseGateway.prototype.get_page = function() {
        return wc_stripe_params_v3.page;
    }

    wc_stripe.BaseGateway.prototype.set_nonce = function(value) {
        $(this.token_selector).val(value);
    }

    /**
     * [get_element_options description]
     * @return {[type]} [description]
     */
    wc_stripe.BaseGateway.prototype.get_element_options = function() {
        return {};
    }

    wc_stripe.BaseGateway.prototype.initialize = function() {};

    /**
     * @return {[type]}
     */
    wc_stripe.BaseGateway.prototype.create_button = function() {}

    /**
     * @returns {Boolean}
     */
    wc_stripe.BaseGateway.prototype.is_gateway_selected = function() {
        return $('[name="payment_method"]:checked').val() === this.gateway_id;
    }

    /**
     * @returns {Boolean}
     */
    wc_stripe.BaseGateway.prototype.is_saved_method_selected = function() {
        return this.is_gateway_selected() &&
            $(
                '[name="' + this.gateway_id +
                '_payment_type_key"]:checked').val() === 'saved';
    }

    /**
     * @return {Boolean}
     */
    wc_stripe.BaseGateway.prototype.has_checkout_error = function() {
        return $('#wc_stripe_checkout_error').length > 0 && this.is_gateway_selected();
    }

    /**
     * @param  {[type]}
     * @return {[type]}
     */
    wc_stripe.BaseGateway.prototype.submit_error = function(message) {
        if (message.indexOf('</ul>') == -1) {
            message = '<div class="woocommerce-error">' + message + '</div>';
        }
        this.submit_message(message);
    }

    wc_stripe.BaseGateway.prototype.submit_error_code = function(code) {

    }

    /**
     * @param  {[type]}
     * @return {[type]}
     */
    wc_stripe.BaseGateway.prototype.submit_message = function(message) {
        $('.woocommerce-error, .woocommerce-message, .woocommerce-info')
            .remove();
        var $container = $(this.message_container);
        if ($container.closest('form').length) {
            $container = $container.closest('form');
        }
        $container.prepend(message);
        $container.removeClass('processing').unblock();
        $container.find('.input-text, select, input:checkbox').blur();
        $('html, body').animate({
            scrollTop: ($container.offset().top - 100)
        }, 1000);
    }

    wc_stripe.BaseGateway.prototype.get_first_name = function(prefix) {
        return $('#' + prefix + '_first_name').val();
    }

    wc_stripe.BaseGateway.prototype.get_last_name = function(prefix) {
        return $('#' + prefix + '_last_name').val();
    }

    /**
     * Return true if the source should be saved.
     * 
     * @returns {Boolean}
     */
    wc_stripe.BaseGateway.prototype.should_save_method = function() {
        return $('#' + this.gateway_id + '_save_source_key').is(':checked');
    }

    wc_stripe.BaseGateway.prototype.is_add_payment_method_page = function() {
        return $(document.body).hasClass('woocommerce-add-payment-method');
    }

    wc_stripe.BaseGateway.prototype.get_selected_payment_method = function() {
        return $(this.saved_method_selector).val();
    }

    wc_stripe.BaseGateway.prototype.needs_shipping = function() {
        return this.params.needs_shipping === "1";
    }

    wc_stripe.BaseGateway.prototype.get_currency = function() {
        return $('#wc_stripe_currency').val();
    }

    wc_stripe.BaseGateway.prototype.get_country = function() {
        return $('#wc_stripe_country').val();
    }

    /**
     * [get_customer_name description]
     * @return {[type]} [description]
     */
    wc_stripe.BaseGateway.prototype.get_customer_name = function(prefix) {
        return $(prefix + '_first_name').val() + ' ' + $(prefix + '_last_name').val();
    }

    /**
     * [get_customer_email description]
     * @return {[type]} [description]
     */
    wc_stripe.BaseGateway.prototype.get_customer_email = function() {
        return $('#billing_email').val();
    }

    /**
     * Returns a string representation of an address.
     * @param  {[type]}
     * @return {[type]}
     */
    wc_stripe.BaseGateway.prototype.get_address_field_hash = function(prefix) {
        var params = ['_first_name', '_last_name', '_address_1', '_address_2', '_postcode', '_city', '_state', '_country', ];
        var hash = "";
        for (var i = 0; i < params.length; i++) {
            hash += $(prefix + params[i]).val() + '_';
        }
        return hash;
    }

    /**
     * @return {[type]}
     */
    wc_stripe.BaseGateway.prototype.block = function() {
        $.blockUI({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });
    }

    /**
     * @return {[type]}
     */
    wc_stripe.BaseGateway.prototype.unblock = function() {
        $.unblockUI();
    }

    /**
     * @return {[type]}
     */
    wc_stripe.BaseGateway.prototype.get_form = function() {
        return $(this.token_selector).closest('form');
    }

    /**
     * @return {[type]}
     */
    wc_stripe.BaseGateway.prototype.get_total_price = function() {
        return $('#wc_stripe_order_total').data('amount');
    }

    wc_stripe.BaseGateway.prototype.get_total_price_cents = function() {
        return $('#wc_stripe_order_total_cents').data('amount');
    }

    /**
     * @return {[type]}
     */
    wc_stripe.BaseGateway.prototype.set_total_price = function(total) {
        $('#wc_stripe_order_total').data('amount', total);
    }

    /**
     * @return {[type]}
     */
    wc_stripe.BaseGateway.prototype.set_total_price_cents = function(total) {
        $('#wc_stripe_order_total_cents').data('amount', total);
    }

    /**
     * [set_payment_method description]
     * @param {[type]} payment_method [description]
     */
    wc_stripe.BaseGateway.prototype.set_payment_method = function(payment_method) {
        $('[name="payment_method"][value="' + payment_method + '"]').prop("checked", true).trigger('click');
    }

    /**
     * [set_shipping_methods description]
     */
    wc_stripe.BaseGateway.prototype.set_selected_shipping_methods = function(shipping_methods) {
        if (shipping_methods && $('[name^="shipping_method"]').length) {
            for (var i in shipping_methods) {
                var method = shipping_methods[i];
                $('[name="shipping_method[' + i + ']"][value="' + method + '"]').prop("checked", true).trigger('change');
            }
        }
    }

    /**
     * @param  {[type]}
     * @return {[type]}
     */
    wc_stripe.BaseGateway.prototype.on_token_received = function(paymentMethod) {
        this.payment_token_received = true;
        $(this.token_selector).val(paymentMethod.id);
        this.process_checkout();
    }

    wc_stripe.BaseGateway.prototype.createPaymentRequest = function() {
        this.paymentRequest = stripe.paymentRequest(this.get_payment_request_options());

        // events
        if (this.needs_shipping()) {
            this.paymentRequest.on('shippingaddresschange', this.update_shipping_address.bind(this));
            this.paymentRequest.on('shippingoptionchange', this.update_shipping_method.bind(this));
        }
        this.paymentRequest.on('paymentmethod', this.on_payment_method_received.bind(this));
    }

    /**
     * @return {[Object]}
     */
    wc_stripe.BaseGateway.prototype.get_payment_request_options = function() {
        var options = {
            country: this.params.country_code,
            currency: this.get_currency().toLowerCase(),
            total: {
                amount: this.get_total_price_cents(),
                label: this.params.total_label,
                pending: true
            },
            requestPayerName: true,
            requestPayerEmail: true,
            requestPayerPhone: true,
            requestShipping: this.needs_shipping()
        }
        var displayItems = this.get_display_items(),
            shippingOptions = this.get_shipping_options();
        if (displayItems) {
            options.displayItems = displayItems;
        }
        if (this.needs_shipping() && shippingOptions) {
            options.shippingOptions = shippingOptions;
        }
        return options;
    }

    /**
     * @return {[Object]}
     */
    wc_stripe.BaseGateway.prototype.get_payment_request_update = function(data) {
        var options = {
            currency: this.get_currency().toLowerCase(),
            total: {
                amount: parseInt(this.get_total_price_cents()),
                label: this.params.total_label,
                pending: true
            }
        }
        var displayItems = this.get_display_items(),
            shippingOptions = this.get_shipping_options();
        if (displayItems) {
            options.displayItems = displayItems;
        }
        if (this.needs_shipping() && shippingOptions) {
            options.shippingOptions = shippingOptions;
        }
        if (data) {
            options = $.extend(true, {}, options, data);
        }
        return options;
    }

    /**
     * @return {[type]}
     */
    wc_stripe.BaseGateway.prototype.get_display_items = function() {
        return $('#wc_stripe_display_items').data('items');
    }

    /**
     * @return {[type]}
     */
    wc_stripe.BaseGateway.prototype.set_display_items = function(items) {
        $('#wc_stripe_display_items').data('items', items);
    }

    /**
     * Return an array of shipping options for display in the Google payment sheet
     * @return {[type]}
     */
    wc_stripe.BaseGateway.prototype.get_shipping_options = function() {
        return $('#wc_stripe_shipping_options').data('items');
    }

    /**
     * Update the shipping options.
     * @param {[type]}
     */
    wc_stripe.BaseGateway.prototype.set_shipping_options = function(items) {
        $('#wc_stripe_shipping_options').data('items', items);
    }

    /**
     * Maps an address from the Browser address format to WC format.
     * @param  {[type]}
     * @return {[type]}
     */
    wc_stripe.BaseGateway.prototype.map_address = function(address) {
        return {
            city: address.city,
            postcode: address.postalCode,
            state: address.region,
            country: address.country
        }
    }

    /**
     * @param  {[type]}
     * @return {[type]}
     */
    wc_stripe.BaseGateway.prototype.on_payment_method_received = function(paymentResponse) {
        try {
            this.payment_response = paymentResponse;
            this.populate_checkout_fields(paymentResponse);
            paymentResponse.complete("success");
            this.on_token_received(paymentResponse.paymentMethod);
        } catch (err) {
            window.alert(err);
        }
    }

    /**
     * @return {[type]}
     */
    wc_stripe.BaseGateway.prototype.populate_checkout_fields = function(data) {
        $(this.token_selector).val(data.paymentMethod.id);
        this.populate_address_fields(data);
    }

    /**
     * @param  {[type]}
     * @return {[type]}
     */
    wc_stripe.BaseGateway.prototype.populate_address_fields = function(data) {
        var mappings = this.address_mappings();
        if (data.payerName) {
            mappings.payerName.set(data.payerName);
        }
        if (data.payerEmail) {
            mappings.payerEmail.set(data.payerEmail);
        }
        if (data.payerPhone) {
            mappings.payerPhone.set(data.payerPhone);
        }
        if (data.shippingAddress) {
            var address = data.shippingAddress;
            for (var k in address) {
                if (mappings[k]) {
                    mappings[k].set.call(this, address[k], '#shipping');
                }
            }
        }
        if (data.paymentMethod.billing_details.address) {
            var address = data.paymentMethod.billing_details.address;
            for (var k in address) {
                if (mappings[k]) {
                    mappings[k].set.call(this, address[k], '#billing');
                }
            }
        }
        this.maybe_set_ship_to_different();
        $('[name="billing_country"]').trigger('change');
    }

    /**
     * @return {[type]}
     */
    wc_stripe.BaseGateway.prototype.address_mappings = function() {
        return {
            payerName: {
                set: function(v, prefix) {
                    var name = v.split(" ");
                    if (name.length > 0) {
                        $('#billing_first_name').val(name[0]);
                    }
                    if (name.length > 1) {
                        $('#billing_last_name').val(name[1]);
                    }
                },
                get: function(prefix) {
                    return $('#billing_first_name').val() + ' ' + $('#billing_last_name').val()
                }
            },
            payerEmail: {
                set: function(v) {
                    $('#billing_email').val(v);
                },
                get: function() {
                    return $('#billing_email').val();
                }
            },
            payerPhone: {
                set: function(v) {
                    $('#billing_phone').val(v);
                },
                get: function() {
                    return $('#billing_phone').val();
                }
            },
            recipient: {
                set: function(v, prefix) {
                    var name = v.split(" ");
                    if (name.length > 0) {
                        $(prefix + '_first_name').val(name[0]);
                    }
                    if (name.length > 1) {
                        $(prefix + '_last_name').val(name[1]);
                    }
                },
                get: function(prefix) {
                    return $(prefix + '_first_name').val() + ' ' + $(prefix + '_last_name').val()
                }
            },
            country: {
                set: function(v, prefix) {
                    $(prefix + '_country').val(v);
                },
                get: function(prefix) {
                    return $(prefix + '_country').val();
                }
            },
            addressLine: {
                set: function(v, prefix) {
                    if (v.length > 0) {
                        $(prefix + '_address_1').val(v[0]);
                    }
                    if (v.length > 1) {
                        $(prefix + '_address_2').val(v[1]);
                    }
                },
                get: function(prefix) {
                    return [
                        $(prefix + '_address_1').val(),
                        $(prefix + '_address_2').val(),
                    ]
                }
            },
            line1: {
                set: function(v, prefix) {
                    $(prefix + '_address_1').val(v);
                },
                get: function(prefix) {
                    return $(prefix + '_address_1').val();
                }
            },
            line2: {
                set: function(v, prefix) {
                    $(prefix + '_address_2').val(v);
                },
                get: function(prefix) {
                    return $(prefix + '_address_2').val();
                }
            },
            region: {
                set: function(v, prefix) {
                    $(prefix + '_state').val(v);
                },
                get: function(prefix) {
                    $(prefix + '_state').val();
                }
            },
            state: {
                set: function(v, prefix) {
                    $(prefix + '_state').val(v);
                },
                get: function(prefix) {
                    $(prefix + '_state').val();
                }
            },
            city: {
                set: function(v, prefix) {
                    $(prefix + '_city').val(v);
                },
                get: function(prefix) {
                    $(prefix + '_city').val();
                }
            },
            postalCode: {
                set: function(v, prefix) {
                    $(prefix + '_postcode').val(v);
                },
                get: function(prefix) {
                    $(prefix + '_postcode').val();
                }
            },
            postal_code: {
                set: function(v, prefix) {
                    $(prefix + '_postcode').val(v);
                },
                get: function(prefix) {
                    $(prefix + '_postcode').val();
                }
            }
        }
    }

    /**
     * @return {[type]}
     */
    wc_stripe.BaseGateway.prototype.process_checkout = function() {
        return new Promise(function(resolve, reject) {
            this.block();
            $.ajax({
                url: this.params.routes.checkout,
                method: 'POST',
                dataType: 'json',
                data: $.extend({}, this.serialize_form(this.get_form()), { payment_method: this.gateway_id, page_id: this.get_page() }),
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', this.params.rest_nonce);
                }.bind(this)
            }).done(function(result) {
                if (result.reload) {
                    window.location.reload();
                    return;
                }
                if (result.result === 'success') {
                    window.location = result.redirect;
                } else {
                    if (result.messages) {
                        this.submit_error(result.messages);
                    }
                    this.unblock();
                }
            }.bind(this)).fail(function(xhr, textStatus, errorThrown) {
                this.unblock();
                this.submit_error(errorThrown);
            }.bind(this))
        }.bind(this))
    }

    /**
     * @return {[type]}
     */
    wc_stripe.BaseGateway.prototype.serialize_form = function($form) {
        var formData = $form.find('input').filter(function(i, e) {
                if ($(e).is('[name^="add-to-cart"]')) {
                    return false;
                }
                return true;
            }.bind(this)).serializeArray(),
            data = {};

        for (var i in formData) {
            var obj = formData[i];
            data[obj.name] = obj.value;
        }
        data.payment_method = this.gateway_id;
        return data;
    }

    /**
     * @param  {[type]}
     * @return {[type]}
     */
    wc_stripe.BaseGateway.prototype.map_shipping_methods = function(shippingData) {
        var methods = {};
        if (shippingData !== "default") {
            var matches = shippingData.match(/^(\d):(.+)$/);
            if (matches.length > 1) {
                methods[matches[1]] = matches[2];
            }
        }
        return methods;
    }

    /**
     * [maybe_set_ship_to_different description]
     * @return {[type]} [description]
     */
    wc_stripe.BaseGateway.prototype.maybe_set_ship_to_different = function() {
        // if shipping and billing address are different, 
        // set the ship to different address option.
        if ($('[name="ship_to_different_address"]').length) {
            $('[name="ship_to_different_address"]').prop('checked', this.get_address_field_hash("#billing") !== this.get_address_field_hash("#shipping")).trigger('change');
        }
    }

    /**
     * @return {[@event]}
     */
    wc_stripe.BaseGateway.prototype.update_shipping_address = function(ev) {
        return new Promise(function(resolve, reject) {
            $.ajax({
                url: this.params.routes.shipping_address,
                method: 'POST',
                dataType: 'json',
                data: { address: this.map_address(ev.shippingAddress), payment_method: this.gateway_id, page_id: this.get_page() },
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', this.params.rest_nonce);
                }.bind(this)
            }).done(function(response) {
                if (response.code) {
                    ev.updateWith(response.data.newData);
                    reject(response.data);
                } else {
                    ev.updateWith(response.data.newData);
                    resolve(response.data);
                }
            }.bind(this)).fail(function(xhr, textStatus, errorThrown) {

            }.bind(this))
        }.bind(this))
    }

    /**
     * @return {[@event]}
     */
    wc_stripe.BaseGateway.prototype.update_shipping_method = function(ev) {
        return new Promise(function(resolve, reject) {
            $.ajax({
                url: this.params.routes.shipping_method,
                method: 'POST',
                dataType: 'json',
                data: { shipping_methods: this.map_shipping_methods(ev.shippingOption.id), payment_method: this.gateway_id, page_id: this.get_page() },
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', this.params.rest_nonce);
                }.bind(this)
            }).done(function(response) {
                if (response.code) {
                    ev.updateWith(response.data.newData);
                    reject(response.data);
                } else {
                    this.set_selected_shipping_methods(response.data.shipping_methods);
                    ev.updateWith(response.data.newData);
                    resolve(response.data);
                }
            }.bind(this)).fail(function(xhr, textStatus, errorThrown) {
                this.submit_error(errorThrown);
            }.bind(this))
        }.bind(this))
    }


    /********** Checkout Gateway ********/

    /**
     * @constructor
     */
    wc_stripe.CheckoutGateway = function() {
        this.container = this.message_container = 'li.payment_method_' + this.gateway_id;
        this.banner_container = 'li.banner_payment_method_' + this.gateway_id;
        $(document.body).on('update_checkout', this.update_checkout.bind(this));
        $(document.body).on('updated_checkout', this.updated_checkout.bind(this));
        $(document.body).on('checkout_error', this.checkout_error.bind(this));
        $(this.token_selector).closest('form').on('checkout_place_order_' + this.gateway_id, this.checkout_place_order.bind(this));

        // events for showing gateway payment buttons
        $(document.body).on('wc_stripe_new_method_' + this.gateway_id, this.on_show_new_methods.bind(this));
        $(document.body).on('wc_stripe_saved_method_' + this.gateway_id, this.on_show_saved_methods.bind(this));
        $(document.body).on('wc_stripe_payment_method_selected', this.on_payment_method_selected.bind(this));

        if (this.banner_enabled()) {
            if ($('.woocommerce-billing-fields').length) {
                $('.wc-stripe-banner-checkout').css('max-width', $('.woocommerce-billing-fields').outerWidth(true));
            }
        }

        this.order_review();
    }

    wc_stripe.CheckoutGateway.prototype.order_review = function() {
        var url = window.location.href;
        var matches = url.match(/order_review.+payment_method=([\w]+).+payment_nonce=(.+)/);
        if (matches && matches.length > 1) {
            var payment_method = matches[1],
                nonce = matches[2];
            if (this.gateway_id === payment_method) {
                this.payment_token_received = true;
                this.set_nonce(nonce);
                this.set_use_new_option(true);
            }
        }
    }

    /**
     * Called on the WC updated_checkout event
     */
    wc_stripe.CheckoutGateway.prototype.updated_checkout = function() {}

    /**
     * Called on the WC update_checkout event
     */
    wc_stripe.CheckoutGateway.prototype.update_checkout = function() {}

    /**
     * Called on the WC checkout_error event
     */
    wc_stripe.CheckoutGateway.prototype.checkout_error = function() {
        if (this.has_checkout_error()) {
            this.payment_token_received = false;
            this.payment_response = null;
            this.show_payment_button();
            this.hide_place_order();
        }
    }

    /**
     * 
     */
    wc_stripe.CheckoutGateway.prototype.is_valid_checkout = function() {
        if ($('[name="terms"]').length) {
            if (!$('[name="terms"]').is(':checked')) {
                return false;
            }
        }
        return true;
    }

    /**
     * Returns the selected payment gateway's id.
     * 
     * @returns {String}
     */
    wc_stripe.CheckoutGateway.prototype.get_payment_method = function() {
        return $('[name="payment_method"]:checked').val();
    }

    wc_stripe.CheckoutGateway.prototype.set_use_new_option = function(bool) {
        $('#' + this.gateway_id + '_use_new').prop("checked", bool).trigger('change');
    }

    /**
     * Called on the WC checkout_place_order_{$gateway_id} event
     */
    wc_stripe.CheckoutGateway.prototype.checkout_place_order = function() {
        if (!this.is_valid_checkout()) {
            this.submit_error(this.params.messages.terms);
            return false;
        } else if (this.is_saved_method_selected()) {
            return true;
        }
        return this.payment_token_received;
    }

    /**
     * @param  {[type]}
     * @return {[type]}
     */
    wc_stripe.CheckoutGateway.prototype.on_token_received = function(paymentMethod) {
        this.payment_token_received = true;
        $(this.token_selector).val(paymentMethod.id);
        this.hide_payment_button();
        this.show_place_order();
    }

    /**
     * @return {[type]}
     */
    wc_stripe.CheckoutGateway.prototype.block = function() {
        $('form.checkout').block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });
    }

    /**
     * @return {[type]}
     */
    wc_stripe.CheckoutGateway.prototype.unblock = function() {
        $('form.checkout').unblock();
    }

    wc_stripe.CheckoutGateway.prototype.hide_place_order = function() {
        $('#place_order').addClass('wc-stripe-hide');
    }

    /**
     * @return {[type]}
     */
    wc_stripe.CheckoutGateway.prototype.show_place_order = function() {
        $('#place_order').removeClass('wc-stripe-hide');
    }

    /**
     * Method that should perform actions when the show new methods contain is made visible.
     * @param  {[@event]}
     * @param  {[String]}
     * @return {[type]}
     */
    wc_stripe.CheckoutGateway.prototype.on_show_new_methods = function() {
        if (this.payment_token_received) {
            this.show_place_order();
            this.hide_payment_button();
        } else {
            this.hide_place_order();
            this.show_payment_button();
        }
    }

    /**
     * Method that performs actions when the saved methods contains is visible.
     * @param  {[type]}
     * @param  {[type]}
     * @return {[type]}
     */
    wc_stripe.CheckoutGateway.prototype.on_show_saved_methods = function() {
        this.hide_payment_button();
        this.show_place_order();
    }

    /**
     * @return {[type]}
     */
    wc_stripe.CheckoutGateway.prototype.show_payment_button = function() {
        if (this.$button) {
            this.$button.show();
        }
    }

    /**
     * @return {[type]}
     */
    wc_stripe.CheckoutGateway.prototype.hide_payment_button = function() {
        if (this.$button) {
            this.$button.hide();
        }
    }

    /**
     * Wrapper for on_payment_method_selected that is safe to call since it won't trigger
     * any DOM events.
     * @return {[type]}
     */
    wc_stripe.CheckoutGateway.prototype.trigger_payment_method_selected = function() {
        this.on_payment_method_selected(null, $('[name="payment_method"]:checked').val());
    }

    /**
     * @param  {[type]}
     * @param  {[type]}
     * @return {[type]}
     */
    wc_stripe.CheckoutGateway.prototype.on_payment_method_selected = function(e, payment_method) {
        if (payment_method === this.gateway_id) {
            if (this.payment_token_received || this.is_saved_method_selected()) {
                this.hide_payment_button();
                this.show_place_order();
            } else {
                this.show_payment_button();
                this.hide_place_order();
            }
        } else {
            this.hide_payment_button();
            if (payment_method.indexOf('stripe_') < 0) {
                this.show_place_order();
            }
        }
    }

    /**
     * [Return true if the banner option has been enabled for the gateway.]
     * @return {[type]} [description]
     */
    wc_stripe.CheckoutGateway.prototype.banner_enabled = function() {
        return this.params.banner_enabled === "1";
    }

    wc_stripe.CheckoutGateway.prototype.checkout_fields_valid = function() {
        if (typeof wc_stripe_checkout_fields == 'undefined') {
            return true;
        }
        var billing = Object.keys(wc_stripe_checkout_fields.billing),
            shipping = Object.keys(wc_stripe_checkout_fields.shipping);
        valid = true;

        function validateFields(keys, fields) {
            for (var i = 0; i < keys.length; i++) {
                var field = fields[keys[i]];
                if (field.required) {
                    var val = $('#' + keys[i]).val();
                    if ((typeof val == 'undefined' || val.length == 0)) {
                        valid = false;
                        return
                    }
                }
            }
        }

        validateFields(billing, wc_stripe_checkout_fields.billing);

        if (this.needs_shipping() && $('#ship-to-different-address-checkbox').is(':checked')) {
            validateFields(shipping, wc_stripe_checkout_fields.shipping);
        }
        valid = this.is_valid_checkout();
        return valid;
    }

    /************** Product Gateway ***************/

    wc_stripe.ProductGateway = function() {
        this.container = 'li.payment_method_' + this.gateway_id;
        this.message_container = 'div.product';

        // events
        $(document.body).on('wc_stripe_updated_rest_nonce', this.set_rest_nonce.bind(this));
        $('form.cart').on('found_variation', this.found_variation.bind(this));
        $('form.cart').on('reset_data', this.reset_variation_data.bind(this));

        this.buttonWidth = $('div.quantity').outerWidth(true) + $('.single_add_to_cart_button').outerWidth();
        $(this.container).css('max-width', this.buttonWidth + 'px');
    }

    /**
     * @return {[@int]}
     */
    wc_stripe.ProductGateway.prototype.get_quantity = function() {
        return parseInt($('[name="quantity"]').val());
    }

    /**
     * @param {[type]}
     * @param {[type]}
     */
    wc_stripe.ProductGateway.prototype.set_rest_nonce = function(e, nonce) {
        this.params.rest_nonce = nonce;
    }

    /**
     * @param  {[type]}
     * @param  {[type]}
     * @return {[type]}
     */
    wc_stripe.ProductGateway.prototype.found_variation = function(e, variation) {
        var data = this.get_product_data();
        data.price = variation.display_price;
        data.needs_shipping = !variation.is_virtual;
        data.variation = variation;
        this.set_product_data(data);
        this.enable_payment_button();
    }

    /**
     * @return {[type]}
     */
    wc_stripe.ProductGateway.prototype.reset_variation_data = function() {
        this.disable_payment_button();
    }

    /**
     * @return {[type]}
     */
    wc_stripe.ProductGateway.prototype.disable_payment_button = function() {
        if (this.$button) {
            this.get_button().prop('disabled', true).addClass('disabled');
        }
    }

    /**
     * @return {[type]}
     */
    wc_stripe.ProductGateway.prototype.enable_payment_button = function() {
        if (this.$button) {
            this.get_button().prop('disabled', false).removeClass('disabled');
        }
    }

    /**
     * @return {[type]}
     */
    wc_stripe.ProductGateway.prototype.get_button = function() {
        return this.$button;
    }

    /**
     * @return {Boolean}
     */
    wc_stripe.ProductGateway.prototype.is_variable_product = function() {
        return $('[name="variation_id"]').length > 0;
    }

    /**
     * @return {[type]}
     */
    wc_stripe.ProductGateway.prototype.needs_shipping = function() {
        return this.get_product_data().needs_shipping;
    }

    /**
     * @return {[type]}
     */
    wc_stripe.ProductGateway.prototype.get_product_data = function() {
        return $('#wc_stripe_product_data').data('product');
    }

    /**
     * @return {[type]}
     */
    wc_stripe.ProductGateway.prototype.set_product_data = function(data) {
        $('#wc_stripe_product_data').data('product', data);
    }

    /**
     * Add a product to the WC shopping cart
     */
    wc_stripe.ProductGateway.prototype.add_to_cart = function() {
        return new Promise(function(resolve, reject) {
            this.block();
            $.ajax({
                url: this.params.routes.add_to_cart,
                method: 'POST',
                dataType: 'json',
                data: {
                    product_id: $('#product_id').val(),
                    variation_id: this.is_variable_product() ? $('[name="variation_id"]').val() : 0,
                    qty: $('[name="quantity"]').val(),
                    payment_method: this.gateway_id,
                    page_id: this.get_page()
                },
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', this.params.rest_nonce);
                }.bind(this)
            }).done(function(response, status, xhr) {
                this.unblock();
                $(document.body).triggerHandler('wc_stripe_updated_rest_nonce', xhr.getResponseHeader('X-WP-Nonce'));
                if (response.code) {
                    this.submit_error(response.message);
                    reject(response);
                } else {
                    this.set_total_price(response.data.total);
                    this.set_total_price_cents(response.data.totalCents);
                    this.set_display_items(response.data.displayItems);
                    resolve(response.data);
                }
            }.bind(this)).fail(function(xhr, textStatus, errorThrown) {
                this.unblock();
                this.submit_error(errorThrown);
            }.bind(this))
        }.bind(this))
    }

    wc_stripe.ProductGateway.prototype.cart_calculation = function(variation_id) {
        return new Promise(function(resolve, reject) {
            $.ajax({
                url: this.params.routes.cart_calculation,
                method: 'POST',
                dataType: 'json',
                data: {
                    product_id: $('#product_id').val(),
                    variation_id: this.is_variable_product() && variation_id ? variation_id : 0,
                    qty: $('[name="quantity"]').val(),
                    payment_method: this.gateway_id
                },
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', this.params.rest_nonce);
                }.bind(this)
            }).done(function(response, status, xhr) {
                $(document.body).triggerHandler('wc_stripe_updated_rest_nonce', xhr.getResponseHeader('X-WP-Nonce'));
                if (response.code) {
                    this.cart_calculation_error = true;
                } else {
                    this.set_total_price(response.data.total);
                    this.set_total_price_cents(response.data.totalCents);
                    this.set_display_items(response.data.displayItems);
                    resolve(response.data);
                }
            }.bind(this)).fail(function(xhr, textStatus, errorThrown) {

            }.bind(this))
        }.bind(this))
    }

    /************* Cart Gateway *************/

    /**
     * @constructor
     */
    wc_stripe.CartGateway = function() {
        this.container = 'li.payment_method_' + this.gateway_id;
        this.message_container = 'div.woocommerce';

        // cart events
        $(document.body).on('updated_wc_div', this.updated_html.bind(this));
        $(document.body).on('updated_cart_totals', this.updated_html.bind(this));
    }

    wc_stripe.CartGateway.prototype.needs_shipping = function() {
        return $('#wc_stripe_needs_shipping').data('value') === 1;
    }

    /**
     * @param  {[type]}
     * @return {[type]}
     */
    wc_stripe.CartGateway.prototype.submit_error = function(message) {
        this.submit_message(message);
    }

    /**
     * @param  {[@event]}
     * @return {[null]}
     */
    wc_stripe.CartGateway.prototype.updated_html = function(e) {

    }

    wc_stripe.CartGateway.prototype.add_cart_totals_class = function() {
        $('.cart_totals').addClass('stripe_cart_gateway_active');
    }

    /************* Google Pay Mixins **************/

    wc_stripe.GooglePay = function() {}

    const googlePayBaseRequest = {
        apiVersion: 2,
        apiVersionMinor: 0
    }

    const allowedCardNetworks = ["AMEX", "DISCOVER", "INTERAC", "JCB", "MASTERCARD", "VISA"];

    const allowedCardAuthMethods = ["PAN_ONLY"];

    const baseCardPaymentMethod = {
        type: 'CARD',
        parameters: {
            allowedAuthMethods: allowedCardAuthMethods,
            allowedCardNetworks: allowedCardNetworks
        }
    }

    /**
     * Retrun an object of address mappings.
     * @param  {[type]}
     * @return {[type]}
     */
    wc_stripe.GooglePay.prototype.address_mappings = function(prefix) {
        return {
            name: {
                set: function(v, prefix) {
                    var name = v.split(" ");
                    $(prefix + '_first_name').val(name[0]);
                    $(prefix + '_last_name').val(name[1]);
                },
                get: function(prefix) {
                    return $(prefix + '_first_name').val() + $(prefix + '_last_name').val()
                }
            },
            postalCode: {
                set: function(v, prefix) {
                    $(prefix + '_postcode').val(v);
                },
                get: function(prefix) {
                    return $(prefix + '_postcode').val();
                }
            },
            countryCode: {
                set: function(v, prefix) {
                    $(prefix + '_country').val(v);
                },
                get: function(prefix) {
                    return $(prefix + '_country').val();
                }
            },
            phoneNumber: {
                set: function(v, prefix) {
                    $('#billing_phone').val(v);
                },
                get: function() {
                    return ('#billing_phone').val();
                }
            },
            address1: {
                set: function(v, prefix) {
                    $(prefix + '_address_1').val(v);
                },
                get: function(prefix) {
                    return $(prefix + '_address_1').val();
                }
            },
            address2: {
                set: function(v, prefix) {
                    $(prefix + '_address_2').val(v);
                },
                get: function(prefix) {
                    return $(prefix + '_address_2').val();
                }
            },
            locality: {
                set: function(v, prefix) {
                    $(prefix + '_city').val(v);
                },
                get: function(prefix) {
                    return $(prefix + '_city').val();
                }
            },
            administrativeArea: {
                set: function(v, prefix) {
                    $(prefix + '_state').val(v);
                },
                get: function(prefix) {
                    return $(prefix + '_state').val();
                }
            }
        }
    }

    wc_stripe.GooglePay.prototype.serialize_form = function($form) {
        return $.extend({}, wc_stripe.BaseGateway.prototype.serialize_form.apply(this, arguments), {
            order_review: !this.dynamic_price_enabled()
        });
    }

    /**
     * Populate the WC checkout fields.
     * @param  {[type]}
     * @return {[type]}
     */
    wc_stripe.GooglePay.prototype.populate_address_fields = function(paymentData) {
        var billingAddress = paymentData.paymentMethodData.info.billingAddress,
            addressMappings = this.address_mappings();
        for (var k in billingAddress) {
            if (addressMappings[k]) {
                addressMappings[k].set.call(this, billingAddress[k], "#billing");
            }
        }
        if (paymentData.shippingAddress) {
            for (var k in paymentData.shippingAddress) {
                if (addressMappings[k]) {
                    addressMappings[k].set.call(this, paymentData.shippingAddress[k], "#shipping");
                }
            }
        }
        if (paymentData.email) {
            $('#billing_email').val(paymentData.email);
        }
        this.maybe_set_ship_to_different();
        $('[name="billing_country"]').trigger('change');
    }

    /**
     * @param  {[type]}
     * @return {[type]}
     */
    wc_stripe.GooglePay.prototype.map_address = function(address) {
        return {
            city: address.locality,
            postcode: address.postalCode,
            state: address.administrativeArea,
            country: address.countryCode
        }
    }

    /**
     * @param  {[type]}
     * @return {[type]}
     */
    wc_stripe.GooglePay.prototype.update_payment_data = function(data) {
        return new Promise(function(resolve, reject) {
            $.when($.ajax({
                url: this.params.routes.payment_data,
                dataType: 'json',
                method: 'POST',
                data: {
                    shipping_address: this.map_address(data.shippingAddress),
                    shipping_methods: this.map_shipping_methods(data.shippingOptionData.id),
                    shipping_method_id: data.shippingOptionData.id,
                    page_id: this.get_page()
                },
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', this.params.rest_nonce)
                }.bind(this)
            })).done(function(response) {
                if (response.code) {
                    reject(response.data.data);
                } else {
                    resolve(response.data);
                }
            }.bind(this)).fail(function() {
                reject();
            }.bind(this))
        }.bind(this))
    }

    /**
     * @param  {[type]}
     * @return {[type]}
     */
    wc_stripe.GooglePay.prototype.on_payment_data_changed = function(address) {
        return new Promise(function(resolve, reject) {
            this.update_payment_data(address).then(function(response) {
                resolve(response.paymentRequestUpdate);
                this.set_selected_shipping_methods(response.shipping_methods);
                this.payment_data_updated(response, address);
            }.bind(this)).catch(function(data) {
                resolve(data);
            }.bind(this))
        }.bind(this))
    }

    /**
     * Convenience method so that gateway can perform actions after the payment data
     * has been updated.
     * @param  {[type]}
     * @return {[type]}
     */
    wc_stripe.GooglePay.prototype.payment_data_updated = function(response) {

    }

    /**
     * Return an array of line items for display in the Google payment sheet
     * @return {[type]}
     */
    wc_stripe.GooglePay.prototype.get_googlepay_display_items = function() {
        return $('#googlepay_display_items').data('items');
    }

    /**
     * Set the display items in the DOM as a data attribute.
     * @param {[type]}
     */
    wc_stripe.GooglePay.prototype.set_googlepay_display_items = function(displayItems) {
        $('#googlepay_display_items').data('items', displayItems);
    }

    /**
     * Return an array of shipping options for display in the Google payment sheet
     * @return {[type]}
     */
    wc_stripe.GooglePay.prototype.get_shipping_options = function() {
        return $('#googlepay_shipping_options').data('items');
    }

    /**
     * @return {[type]}
     */
    wc_stripe.GooglePay.prototype.get_merchant_info = function() {
        var options = {
            merchantId: this.params.merchant_id,
            merchantName: this.params.merchant_name
        }
        if (this.params.environment === 'TEST') {
            delete options.merchantId;
        }
        return options;
    }

    /**
     * Return true if dynamic pricing is enabled.
     * @return {[type]} [description]
     */
    wc_stripe.GooglePay.prototype.dynamic_price_enabled = function() {
        return this.params.dynamic_price === "1";
    }

    /**
     * @return {[type]}
     */
    wc_stripe.GooglePay.prototype.get_payment_options = function() {
        var options = {
            environment: this.params.environment,
            merchantInfo: this.get_merchant_info()
        }
        if (this.dynamic_price_enabled()) {
            if (this.needs_shipping() && (this.get_total_price_cents() > 0)) {
                options.paymentDataCallbacks = {
                    onPaymentDataChanged: this.on_payment_data_changed.bind(this),
                    onPaymentAuthorized: function(data) {
                        return new Promise(function(resolve, reject) {
                            resolve({ transactionState: "SUCCESS" })
                        }.bind(this))
                    }.bind(this)
                }
            } else {
                options.paymentDataCallbacks = {
                    onPaymentAuthorized: function(data) {
                        return new Promise(function(resolve, reject) {
                            resolve({ transactionState: "SUCCESS" })
                        }.bind(this))
                    }
                }
            }
        }
        return options;
    }

    /**
     * @return {[type]}
     */
    wc_stripe.GooglePay.prototype.build_payment_request = function() {
        var request = $.extend({}, googlePayBaseRequest, {
            emailRequired: true,
            merchantInfo: this.get_merchant_info(),
            allowedPaymentMethods: [$.extend({
                type: "CARD",
                tokenizationSpecification: {
                    type: "PAYMENT_GATEWAY",
                    parameters: {
                        gateway: 'stripe',
                        "stripe:version": "2018-10-31",
                        "stripe:publishableKey": this.params.api_key
                    }
                }
            }, baseCardPaymentMethod)],
            shippingAddressRequired: this.needs_shipping() && this.get_total_price_cents() > 0,
            transactionInfo: {
                currencyCode: this.get_currency(),
                totalPriceStatus: "ESTIMATED",
                totalPrice: this.get_total_price().toString(),
                displayItems: this.get_googlepay_display_items(),
                totalPriceLabel: this.params.total_price_label
            }
        })
        request.allowedPaymentMethods[0].parameters['billingAddressRequired'] = true;
        request.allowedPaymentMethods[0].parameters['billingAddressParameters'] = {
            format: "FULL",
            phoneNumberRequired: $('#billing_phone').length > 0
        }
        if (this.dynamic_price_enabled()) {
            if (this.needs_shipping() && (this.get_total_price_cents() > 0)) {
                request['shippingAddressParameters'] = {};
                request['shippingOptionRequired'] = true;
                request['shippingOptionParameters'] = {
                    shippingOptions: this.get_shipping_options(),
                };
                request['callbackIntents'] = ["SHIPPING_ADDRESS", "SHIPPING_OPTION", "PAYMENT_AUTHORIZATION"];
            } else {
                request['callbackIntents'] = ["PAYMENT_AUTHORIZATION"];
            }
        }
        return request;
    }

    /**
     * @return {[type]}
     */
    wc_stripe.GooglePay.prototype.createPaymentsClient = function() {
        this.paymentsClient = new google.payments.api.PaymentsClient(this.get_payment_options());
    }

    /**
     * @return {Promise}
     */
    wc_stripe.GooglePay.prototype.isReadyToPay = function() {
        return new Promise(function(resolve) {
            var isReadyToPayRequest = $.extend({}, googlePayBaseRequest);
            isReadyToPayRequest.allowedPaymentMethods = [baseCardPaymentMethod];
            this.paymentsClient.isReadyToPay(isReadyToPayRequest).then(function() {
                this.can_pay = true;
                this.create_button();
                resolve();
            }.bind(this)).catch(function(err) {
                this.submit_error(err);
            }.bind(this))
        }.bind(this))
    }

    wc_stripe.GooglePay.prototype.create_button = function() {
        if (this.$button) {
            this.$button.remove();
        }
        this.$button = $(this.paymentsClient.createButton({
            onClick: this.start.bind(this),
            buttonColor: this.params.button_color,
            buttonType: this.params.button_style
        }));
        this.$button.addClass('gpay-button-container');
    }

    /**
     * @return {[type]}
     */
    wc_stripe.GooglePay.prototype.start = function() {
        // always recreate the paymentClient to ensure latest data is used.
        this.createPaymentsClient();
        this.paymentsClient.loadPaymentData(this.build_payment_request()).then(function(paymentData) {
            var data = JSON.parse(paymentData.paymentMethodData.tokenizationData.token);
            this.populate_address_fields(paymentData);
            this.on_token_received(data);
        }.bind(this)).catch(function(err) {
            if (err.statusCode === "CANCELED") {
                return;
            }
            if (err.statusMessage && err.statusMessage.indexOf("paymentDataRequest.callbackIntent") > -1) {
                this.submit_error_code("DEVELOPER_ERROR_WHITELIST");
            } else {
                this.submit_error(err.statusMessage);
            }
        }.bind(this))
    }

    /************* Apple Pay ************/

    /**
     * @constructor
     */
    wc_stripe.ApplePay = function() {

    }

    /**
     * @return {[type]}
     */
    wc_stripe.ApplePay.prototype.initialize = function() {
        $(document.body).on('click', '.apple-pay-button', this.start.bind(this));
        this.createPaymentRequest();
        this.canMakePayment();
    }

    wc_stripe.ApplePay.prototype.create_button = function() {
        if (this.$button) {
            this.$button.remove();
        }
        this.$button = $(this.params.button);
        this.append_button();
    }

    /**
     * @return {[type]}
     */
    wc_stripe.ApplePay.prototype.canMakePayment = function() {
        return new Promise(function(resolve, reject) {
            this.paymentRequest.canMakePayment().then(function(result) {
                if (result && result.applePay) {
                    this.can_pay = true;
                    this.create_button();
                    $(this.container).show();
                    resolve(result);
                }
            }.bind(this))
        }.bind(this))
    }

    /**
     * @return {[type]}
     */
    wc_stripe.ApplePay.prototype.start = function(e) {
        e.preventDefault();
        this.paymentRequest.update(this.get_payment_request_update({
            total: {
                pending: false
            }
        }));
        this.paymentRequest.show();
    }

    /*********** PaymentRequest *********/

    wc_stripe.PaymentRequest = function() {

    }

    /**
     * [initialize description]
     * @return {[type]} [description]
     */
    wc_stripe.PaymentRequest.prototype.initialize = function() {
        this.createPaymentRequest();
        this.canMakePayment();
        this.paymentRequestButton = this.createPaymentRequestButton();
        this.paymentRequestButton.on('click', this.button_click.bind(this));
    }

    /**
     * [button_click description]
     * @param  {[type]} event [description]
     * @return {[type]}       [description]
     */
    wc_stripe.PaymentRequest.prototype.button_click = function(event) {}

    /**
     * [createPaymentRequestButton description]
     * @return {[type]} [description]
     */
    wc_stripe.PaymentRequest.prototype.createPaymentRequestButton = function() {
        return this.elements.create("paymentRequestButton", {
            paymentRequest: this.paymentRequest,
            style: {
                paymentRequestButton: {
                    type: this.params.button.type,
                    theme: this.params.button.theme,
                    height: this.params.button.height
                }
            }
        })
    }

    /**
     * [canMakePayment description]
     * @return {[type]} [description]
     */
    wc_stripe.PaymentRequest.prototype.canMakePayment = function() {
        return new Promise(function(resolve, reject) {
            this.paymentRequest.canMakePayment().then(function(result) {
                if (result && !result.applePay) {
                    this.can_pay = true;
                    this.create_button();
                    $(this.container).show();
                    resolve(result);
                }
            }.bind(this))
        }.bind(this))
    }

    /**
     * [create_button description]
     * @return {[type]} [description]
     */
    wc_stripe.PaymentRequest.prototype.create_button = function() {
        this.paymentRequestButton.mount('#wc-stripe-payment-request-container');
    }

}(window, jQuery))