<?php if (empty($cart->shippingMethods(true))) 
{
    if ($cart->validShippingAddress()) { ?>
    	<div class="form-group has-error">
    	   <label class="control-label">Unfortunately, we cannot ship to your address.</label>
    	   <input data-required="true" type="hidden" name="checkout[shipping_method]" value="" class="form-control" disabled />
    	</div>    
	<?php } else { ?>
    	<div class="form-group has-error">
    	   <label class="control-label">Please enter your address to view shipping methods.</label>
    	   <input data-required="true" type="hidden" name="checkout[shipping_method]" value="" class="form-control" disabled />
    	</div>	
	<?php
	}
}
else 
{
    ?>
    <div class="form-group">
    <?php
	$checked	=	false;
	foreach ($cart->shippingMethods() as $method_array) 
    {
        $method = new \Shop\Models\ShippingMethods( $method_array );
        ?>
		<div class="form-field">
			<label class="radio control-label">
				<input class="jq_shipping_methods" id="shipping_methods" data-required="true" type="radio" name="checkout[shipping_method]" value="<?php echo $method->{'id'}; ?>" <?php if (\Dsc\ArrayHelper::get( $cart, 'checkout.shipping_method' ) == $method->{'id'}) { $checked 	=	true; echo 'checked'; } ?> />
				<?php echo $method->{'name'}; ?> &mdash; <?php if (empty($method->total())) { echo "FREE"; } else { echo \Shop\Models\Currency::format( $method->total() ); } ?>
			</label>
		</div>        
        <?php		
	}
	?>
	<div class="error" id="jq_error_shipping_methods">&nbsp;</div>
	</div>
	<?php
}
?>
<script>
jQuery(document).ready(function(){
	jQuery('.jq_shipping_methods').on('change', function (){
					
					jQuery('#jq_cart_summary').css({ opacity: 0.5 });
					jQuery(this).closest('form').data('locked', true);
					
					var form_data = jQuery('#checkout-shipping-form').serialize();
					var request = jQuery.ajax({
						type: 'post', 
						url: './shop/checkout/update',
						data: form_data
					}); 
					
					request.done(function ()
					{
						var request1 = jQuery.ajax({
						type: 'POST', 
						url: './shop/cart/get_cart_content',
						data: form_data
						}).done(function(data){
							jQuery('#jq_cart_summary').html(data);
							jQuery('#jq_cart_summary').css({ opacity: '' });
							jQuery(this).closest('form').data('locked', false);
					
						
						});
					});
					
					
			});
			<?php 
			if (!$checked) { 
			?>
				
				jQuery('.jq_shipping_methods:first').prop('checked', true).trigger('change');
			<?php 
			} 
			?>
	
})
</script>