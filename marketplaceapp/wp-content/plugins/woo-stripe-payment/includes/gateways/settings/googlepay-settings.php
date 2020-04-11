<?php
return array( 
		'desc1' => array( 'type' => 'description', 
				'description' => '<p><a target="_blank" href="https://services.google.com/fb/forms/googlepayAPIenable/">' . __ ( 'Google Pay Request', 'woo-stripe-payment' ) . '</a>. ' . __ ( 'When you submit your request for Google Pay, request to be whitelisted for callbackintents. This ensures that the order items are displayed on the Google Payment sheet.', 'woo-stripe-payment' ) . '</p>' . __ ( 'To have the Google API team approve your integration you can enable test mode and Google Pay. When test mode is enabled, Google Pay will work, allowing you to capture the necessary screenshots the Google API team needs to approve your Merchant ID request.', 'woo-stripe-payment' ) 
		), 
		'desc2' => array( 'type' => 'description', 
				'description' => sprintf ( '<p>%s</p>', sprintf ( __ ( 'If you don\'t want to request a Google Merchant ID, you can use the %sPayment Request Gateway%s which has a Google Pay integration through Stripe via the Chrome browser.', 'woo-stripe-payment' ), '<a target="_blank" href="' . admin_url ( 'admin.php?page=wc-settings&tab=checkout&section=stripe_payment_request' ) . '">', '</a>' ) ) 
		), 
		'enabled' => array( 
				'title' => __ ( 'Enabled', 'woo-stripe-payment' ), 
				'type' => 'checkbox', 'default' => 'no', 
				'value' => 'yes', 'desc_tip' => true, 
				'description' => __ ( 'If enabled, your site can accept Google Pay payments through Stripe.', 'woo-stripe-payment' ) 
		), 
		'general_settings' => array( 'type' => 'title', 
				'title' => __ ( 'General Settings', 'woo-stripe-payment' ) 
		), 
		'dynamic_price' => array( 
				'title' => __ ( 'Dynamic Price', 'woo-stripe-payment' ), 
				'type' => 'checkbox', 'default' => 'yes', 
				'desc_tip' => true, 
				'description' => __ ( 'If enabled, the Google Payment sheet will show the order line items. You must have Google whitelist you for callback intents.', 'woo-stripe-payment' ) 
		), 
		'merchant_id' => array( 'type' => 'text', 
				'title' => __ ( 'Merchant ID', 'woo-stripe-payment' ), 
				'default' => '', 
				'description' => __ ( 'Your Google Merchant ID is given to you by the Google API team once you register for Google Pay. While testing in TEST mode you can leave this value blank and Google Pay will work.', 'woo-stripe-payment' ) 
		), 
		'title_text' => array( 'type' => 'text', 
				'title' => __ ( 'Title', 'woo-stripe-payment' ), 
				'default' => __ ( 'Google Pay', 'woo-stripe-payment' ), 
				'desc_tip' => true, 
				'description' => __ ( 'Title of the credit card gateway' ) 
		), 
		'description' => array( 
				'title' => __ ( 'Description', 'woo-stripe-payment' ), 
				'type' => 'text', 'default' => '', 
				'description' => __ ( 'Leave blank if you don\'t want a description to show for the gateway.', 'woo-stripe-payment' ), 
				'desc_tip' => true 
		), 
		'method_format' => array( 
				'title' => __ ( 'Credit Card Display', 'woo-stripe-payment' ), 
				'type' => 'select', 
				'class' => 'wc-enhanced-select', 
				'options' => wp_list_pluck ( $this->get_method_formats (), 'example' ), 
				'value' => '', 'default' => 'type_ending_in', 
				'desc_tip' => true, 
				'description' => __ ( 'This option allows you to customize how the credit card will display for your customers on orders, subscriptions, etc.' ) 
		), 
		'charge_type' => array( 'type' => 'select', 
				'title' => __ ( 'Charge Type', 'woo-stripe-payment' ), 
				'default' => 'capture', 
				'class' => 'wc-enhanced-select', 
				'options' => array( 
						'capture' => __ ( 'Capture', 'woo-stripe-payment' ), 
						'authorize' => __ ( 'Authorize', 'woo-stripe-payment' ) 
				), 'desc_tip' => true, 
				'description' => __ ( 'This option determines whether the customer\'s funds are capture immediately or authorized and can be captured at a later date.', 'woo-stripe-payment' ) 
		), 
		'payment_sections' => array( 
				'type' => 'multiselect', 
				'title' => __ ( 'Payment Sections', 'woo-stripe-payment' ), 
				'class' => 'wc-enhanced-select', 
				'options' => [ 
						'product' => __ ( 'Product Page', 'woo-stripe-payment' ), 
						'cart' => __ ( 'Cart Page', 'woo-stripe-payment' ), 
						'checkout_banner' => __ ( 'Top of Checkout', 'woo-stripe-payment' ) 
				], 'default' => [ 'product', 'cart' 
				], 
				'description' => __ ( 'Increase your conversion rate by offering Google Pay on your Product and Cart pages, or at the top of the checkout page.', 'woo-stripe-payment' ) 
		), 
		'order_status' => array( 'type' => 'select', 
				'title' => __ ( 'Order Status', 'woo-stripe-payment' ), 
				'default' => 'default', 
				'class' => 'wc-enhanced-select', 
				'options' => array_merge ( array( 
						'default' => __ ( 'Default', 'woo-stripe-payment' ) 
				), wc_get_order_statuses () ), 
				'tool_tip' => true, 
				'description' => __ ( 'This is the status of the order once payment is complete. If <b>Default</b> is selected, then WooCommerce will set the order status automatically based on internal logic which states if a product is virtual and downloadable then status is set to complete. Products that require shipping are set to Processing. Default is the recommended setting as it allows standard WooCommerce code to process the order status.', 'woo-stripe-payment' ) 
		), 
		'merchant_name' => [ 'type' => 'text', 
				'title' => __ ( 'Merchant Name', 'woo-stripe-payment' ), 
				'default' => get_bloginfo ( 'name' ), 
				'description' => __ ( 'The name of your business as it appears on the Google Pay payment sheet.', 'woo-stripe-payment' ), 
				'desc_tip' => true 
		], 
		'icon' => array( 
				'title' => __ ( 'Icon', 'woo-stripe-payment' ), 
				'type' => 'select', 
				'options' => array( 
						'googlepay_outline' => __ ( 'With Outline', 'woo-stripe-payment' ), 
						'googlepay_standard' => __ ( 'Standard', 'woo-stripe-payment' ) 
				), 'default' => 'googlepay_outline', 
				'desc_tip' => true, 
				'description' => __ ( 'This is the icon style that appears next to the gateway on the checkout page. Google\'s API team typically requires the With Outline option on the checkout page for branding purposes.', 'woo-stripe-payment' ) 
		), 
		'button_section' => array( 'type' => 'title', 
				'title' => __ ( 'Button Options', 'woo-stripe-payment' ) 
		), 
		'button_color' => array( 
				'title' => __ ( 'Button Color', 'woo-stripe-payment' ), 
				'type' => 'select', 
				'class' => 'gpay-button-option button-color', 
				'options' => array( 
						'black' => __ ( 'Black', 'woo-stripe-payment' ), 
						'white' => __ ( 'White', 'woo-stripe-payment' ) 
				), 'default' => 'black', 
				'description' => __ ( 'The button color of the GPay button.', 'woo-stripe-payment' ) 
		), 
		'button_style' => array( 
				'title' => __ ( 'Button Style', 'woo-stripe-payment' ), 
				'type' => 'select', 
				'class' => 'gpay-button-option button-style', 
				'options' => array( 
						'long' => __ ( 'Long', 'woo-stripe-payment' ), 
						'short' => __ ( 'Short', 'woo-stripe-payment' ) 
				), 'default' => 'long', 
				'description' => __ ( 'The button style of the GPay button.', 'woo-stripe-payment' ) 
		), 
		'button_render' => [ 'type' => 'button_demo', 
				'title' => __ ( 'Button Design', 'woo-stripe-payment' ), 
				'id' => 'gpay-button', 
				'description' => __ ( 'If you can\'t see the Google Pay button, try switching to a Chrome browser.', 'woo-stripe-payment' ) 
		] 
);