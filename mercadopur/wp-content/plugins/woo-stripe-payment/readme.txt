=== Stripe For WooCommerce ===
Contributors: mr.clayton
Tags: stripe, ach, klarna, credit card, apple pay, google pay, ideal, sepa, sofort
Requires at least: 3.0.1
Tested up to: 5.4
Requires PHP: 5.4
Stable tag: 3.1.0
Copyright: Payment Plugins
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==
Accept Credit Cards, Google Pay, ApplePay, ACH, P24, Klarna, iDEAL and more all in one plugin for free!

= Official Stripe Partner = 
Payment Plugins is an official partner of Stripe. 

= Boost conversion by offering product and cart page checkout =
Stripe for WooCommerce is made to supercharge your conversion rate by decreasing payment friction for your customer.
Offer Google Pay, Apple Pay, and Stripe's Browser payment methods on product pages, cart pages, and at the top of your checkout page.

= Visit our demo site to see all the payment methods in action = 
[Demo Site](https://demos.paymentplugins.com/wc-stripe/product/pullover/)

To see Apple Pay, visit the site using an iOS device. Google Pay will display for supported browsers like Chrome.

= Features =
- Credit Cards
- Google Pay
- Apple Pay
- ACH Payments
- 3DS 2.0
- Local Payment Methods
- WooCommerce Subscriptions

== Frequently Asked Questions ==
= How do I test this plugin? = 
 You can enable the plugin's test mode, which allows you to simulate transactions.
 
= Does your plugin support WooCommerce Subscriptions? = 
Yes, the plugin supports all functionality related to WooCommerce Subscriptions.

= Where is your documentation? = 
https://docs.paymentplugins.com/wc-stripe/config/#/

= Why isn't the Payment Request button showing on my local machine? = 
If you're site is not loading over https, then Stripe won't render the Payment Request button. Make sure you are using https.

== Screenshots ==
1. Let customers pay directly from product pages
2. Apple pay on the cart page
3. Custom credit card forms
4. Klarna on checkout page
5. Local payment methods like iDEAL and P24
6. Configuration pages
7. Payment options at top of checkout page for easy one click checkout

== Changelog ==
= 3.1.0 = 
* Added - FPX payment method
* Added - Alipay payment method
* Updated - Stripe connect integration
* Updated - WeChat support for other countries besides CN
* Updated - CSS so prevent theme overrides
* Fixed - WeChat QR code
= 3.0.9 = 
* Added - Payment methods with payment sheets like Apple Pay now show order items on order pay page instead of just total.
* Fixed - Error if 100% off coupon is used on checkout page.
= 3.0.8 = 
* Updated - billing phone and email check added for card payment
* Updated - template checkout/payment-method.php name changed to checkout/stripe-payment-method.php
* Updated - cart checkout button styling
* Added - Connection test in admin API settings
* Misc - WC 3.9.1
= 3.0.7 = 
* Added - WPML support for gateway titles and descriptions
* Added - ACH fee option
* Added - Webhook registration option in Admin
* Updated - Cart one click checkout buttons
* Updated - WC 3.9
= 3.0.6 = 
* Added - ACH subscription support
* Updated - Top of checkout styling
* Updated =Positioning of cart buttons. They are now below cart checkout button
= 3.0.5 =
* Added - ACH payment support
* Added - New credit card form
* Fixed - Klarna error if item totals don't equal order total.
* Updated - API version to 2019-12-03
* Updated - Local payment logic.
= 3.0.4 =
* Added - Bootstrap form added
* Updated - WC 3.8.1
* Fixed - Check for customer object in Admin pages for local payment methods
= 3.0.3 = 
* Fixed - Check added to wc_stripe_order_status_completed function to ensure capture charge is only called when Stripe is the payment gateway for the order.
* Updated - Stripe API version to 2019-11-05
= 3.0.2 = 
* Added - Klarna payments now supported
* Added - Bancontact
* Updated - Local payments webhook
= 3.0.1 = 
* Updated - Google Pay paymentDataCallbacks in JavaScript
* Updated - Text domain to match plugin slug
* Added - Dynamic price option for Google Pay
* Added - Pre-orders support
= 3.0.0 = 
* First commit

== Upgrade Notice ==
= 3.0.0 = 