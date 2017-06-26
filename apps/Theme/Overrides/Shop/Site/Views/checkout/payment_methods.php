<?php if (!$cart->paymentRequired()) { ?>
	<div class="form-group has-error">
	   <label class="control-label">No payment necessary.</label>
	</div>
	<?php return; ?>
<?php } ?>


<div id="payment-methods-group" class="panel-group">
    
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
    
    <?php /* ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <label onclick="jQuery('#panel-creditcard').collapse('show');">
                <input type="radio" name="payment_method" value="amrita.credit_card" checked>
                Credit Card
            </label>                        
        </div>
        <div id="panel-creditcard" class="panel-collapse collapse in" data-toggle="false" data-parent="#payment-methods-group">
            <div class="panel-body">
                <div class="payment-method-formfields" data-method="amrita.credit_card" data-slug="creditcard">
                    <?php echo $this->renderView('Shop/Site/Views::checkout/payment_netsuite.php'); ?>
                </div>
            </div>
        </div>
    </div> 
    */ ?>    
    
</div>