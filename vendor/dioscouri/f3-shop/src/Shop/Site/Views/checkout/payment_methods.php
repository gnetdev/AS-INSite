<?php if (!$cart->paymentRequired()) { ?>
	<div class="form-group has-error">
	   <label class="control-label">No payment necessary.</label>
	</div>
	<?php return; ?>
<?php } ?>


<div id="payment-methods-group" class="panel-group">
    <?php // TODO Make this use $cart->paymentMethods() so that the enabled payment methods are filtered against the cart (so ones that are invalid for the cart aren't displayed) ?>
    <?php $payment_methods = (new \Shop\Models\PaymentMethods)->setState('filter.enabled', true)->setState('filter.configured', true)->getList(); ?>
    <?php foreach ($payment_methods as $payment_method) { ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <label onclick="jQuery('#panel-<?php echo $payment_method->slug; ?>').collapse('show');">
                <input type="radio" name="payment_method" value="<?php echo $payment_method->identifier; ?>">
                    <?php echo $payment_method->getClass()->displayName(); ?>
            </label>            
        </div>
        <div id="panel-<?php echo $payment_method->slug; ?>" class="panel-collapse collapse" data-toggle="false" data-parent="#payment-methods-group">
            <div class="panel-body">
                <div class="payment-method-formfields" data-method="<?php echo $payment_method->identifier; ?>" data-slug="<?php echo $payment_method->slug; ?>">
                    <?php echo $payment_method->addCart( $cart )->getClass()->displayForm(); ?>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
    
</div>

<?php /* if (empty($cart->paymentMethods(true))) 
{
	?>
	<div class="form-group has-error">
	   <label class="control-label">No valid payment methods could be found.</label>
	   <input data-required="true" type="hidden" name="checkout[payment_method]" value="" class="form-control" disabled />
	</div>	
	<?php
}
else 
{
    ?>
    <div class="form-group">
    <?php
	foreach ($cart->paymentMethods() as $method_array) 
    {
        $method = new \Shop\Models\PaymentMethods( $method_array );
        ?>
		<div class="form-field">
			<label class="radio control-label">
				<input data-required="true" type="radio" name="checkout[payment_method]" value="<?php echo $method->{'id'}; ?>" <?php if (\Dsc\ArrayHelper::get( $cart, 'checkout.payment_method' ) == $method->{'id'}) { echo 'checked'; } ?> />
				<?php echo $method->{'name'}; ?> &mdash; <?php if (empty($method->total())) { echo "FREE"; } else { echo '$' . $method->total(); } ?>
			</label>
		</div>        
        <?php		
	}
	?>
	</div>
	<?php
} */
?>