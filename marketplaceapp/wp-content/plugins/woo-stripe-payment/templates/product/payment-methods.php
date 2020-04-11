<?php
/**
 * @version 3.0.0
 */
?>
<div class="wc-stripe-product-checkout-container">
	<ul class="wc_stripe_product_payment_methods">
		<?php foreach($gateways as $gateway):?>
			<li class="payment_method_<?php echo esc_attr($gateway->id)?>">
				<div class="payment-box">
					<?php $gateway->product_fields()?>
				</div>
			</li>
		<?php endforeach;?>
	</ul>
</div>