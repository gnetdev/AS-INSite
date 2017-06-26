<?php $creditCards = \Netsuite\Models\Customer::creditCardsForUser( $this->auth->getIdentity() ); ?>
<div id="credit-cards">
    <div id="credit-cards-container">
        <div id="existing-cards" class="row">
            <div class="form-group col-xs-12 col-sm-12 col-md-5">
                <select id="select-token" name="card[token]" class="form-control" data-required="true">
                <?php foreach ($creditCards as $creditCard) { ?>
                    <option value="<?php echo $creditCard['internalId']; ?>"><?php echo $creditCard['ccNumber']; ?> - exp. <?php echo date('m/Y', strtotime( $creditCard['ccExpireDate'] ) ); ?></option>
                <?php } ?>
                <option value="--" class="add-new-card">- Add a new card -</option>
                </select>
            </div>
            <div class="form-group col-md-3">
                <?php if (!empty($creditCards)) { ?>
                <input id="token-csc" type="text" class="form-control cvv" data-numeric="true" data-required="true" name="card[csc]" value="" placeholder="Security Code" autocomplete="off">
                <?php } ?>
            </div>
        </div>
        
        <div id="new-card" class="add-new-card">
            <input id="new-card-cid" type="hidden" name="card[cid]" value="<?php echo $this->auth->getIdentity()->{'netsuite.id'}; ?>">
            
            <div class="row">
                <div class="form-group col-xs-12 col-sm-12 col-md-5">
                    <input id="new-card-number" type="text" class="form-control number" data-numeric="true" data-required="true" name="card[number]" value="" placeholder="Card Number" autocomplete="off">
                </div>
                <div class="form-group col-xs-12 col-sm-12 col-md-4">
                    <select class="form-control month" data-required="true" name="card[month]" id="new-card-month">
                    <?php 
                    for ($i=1; $i<=12; $i++) {
                        $month_num = str_pad( $i, 2, 0, STR_PAD_LEFT );
                        $month_name = date('F', strtotime( date('Y') . '-' . $month_num ) );
                        ?>
                        <option value="<?php echo $month_num; ?>"><?php echo $month_num . ' - ' . $month_name; ?></option>
                    <?php } ?>
                    </select>        
                </div>
                <div class="form-group col-xs-12 col-sm-12 col-md-3">
                    <select class="form-control year" data-required="true" name="card[year]" id="new-card-year">
                    <?php for ($n=date('Y'); $n<date('Y')+25; $n++) { ?>
                    	<option value="<?php echo $n; ?>"><?php echo $n; ?></option>
                    <?php } ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-xs-12 col-sm-12 col-md-4">
                    <input id="new-card-csc" type="text" class="form-control cvv" data-numeric="true" data-required="true" name="card[csc]" value="" placeholder="Security Code" autocomplete="off">
                </div>
                <div class="form-group col-xs-12 col-sm-12 col-md-8">
                    <img src="./minify/Shop/Assets/images/cvv_mc_visa.gif" />
                    <img src="./minify/Shop/Assets/images/cvv_amex.gif" />
                </div>
            </div>
        </div>
    </div>
</div>

<div id="billing-address">
    <legend><small>Billing Address</small></legend>
    <?php if ($cart->shippingRequired()) { ?>
    <div class="form-group">
        <div class="checkbox">
            <label>
              <input type="checkbox" id="same-as-shipping" name="checkout[billing_address][same_as_shipping]" <?php if ($cart->billingSameAsShipping()) { echo 'checked'; } ?>> Same as shipping address
            </label>
        </div>
    </div>
    <?php } ?>
    
    <?php if ($existing_addresses = \Shop\Models\CustomerAddresses::fetch()) { ?>
    <div class="form-group" id="existing-address">
        <label>Use an existing address or provide a new one below.</label>
        <select name="checkout[billing_address][id]" class="form-control" id="select-address">
            <option id="new-address" value="">-- New Address --</option>
        <?php foreach ($existing_addresses as $address) { ?>
            <option <?php if ($cart->{'checkout.billing_address.id'} == (string) $address->id) { echo "selected"; } ?>
                value="<?php echo $address->id; ?>" 
                data-name="<?php echo htmlspecialchars( $address->name ); ?>"
                data-line_1="<?php echo htmlspecialchars( $address->line_1 ); ?>"
                data-line_2="<?php echo htmlspecialchars( $address->line_2 ); ?>"
                data-city="<?php echo htmlspecialchars( $address->city ); ?>"
                data-region="<?php echo htmlspecialchars( $address->region ); ?>"
                data-country="<?php echo htmlspecialchars( $address->country ); ?>"
                data-postal_code="<?php echo htmlspecialchars( $address->postal_code ); ?>"
                data-phone_number="<?php echo htmlspecialchars( $address->phone_number ); ?>"
            >
                <?php echo $address->asString(', '); ?>
            </option>
        <?php } ?>
        </select>
        <hr/>
    </div>
    <?php } ?>
    
    
    <div class="form-group">
        <input id="name" type="text" class="form-control name" data-required="true" data-shipping="<?php echo $cart->{'checkout.shipping_address.name'}; ?>" name="checkout[billing_address][name]" value="<?php echo $cart->billingName( $cart->{'checkout.shipping_address.name'} ); ?>" placeholder="Full Name" autocomplete="name" <?php if ($cart->billingSameAsShipping()) { echo 'disabled'; } ?>>
    </div>
    <div class="form-group">
        <input id="line_1" type="text" class="form-control address" data-required="true" data-shipping="<?php echo $cart->{'checkout.shipping_address.line_1'}; ?>" name="checkout[billing_address][line_1]" value="<?php echo $cart->billingLine1( $cart->{'checkout.shipping_address.line_1'} ); ?>" placeholder="Address Line 1" autocomplete="address-line1" <?php if ($cart->billingSameAsShipping()) { echo 'disabled'; } ?>>
    </div>
    <div class="form-group">
        <input id="line_2" type="text" class="form-control address" data-shipping="<?php echo $cart->{'checkout.shipping_address.line_2'}; ?>" name="checkout[billing_address][line_2]" value="<?php echo $cart->billingLine2( $cart->{'checkout.shipping_address.line_2'} ); ?>" placeholder="Address Line 2" autocomplete="address-line2" <?php if ($cart->billingSameAsShipping()) { echo 'disabled'; } ?>>
    </div>
    <div class="form-group">
        <input id="city" type="text" class="form-control city" data-required="true" data-shipping="<?php echo $cart->{'checkout.shipping_address.city'}; ?>" name="checkout[billing_address][city]" value="<?php echo $cart->billingCity( $cart->{'checkout.shipping_address.city'} ); ?>" placeholder="City" autocomplete="locality" <?php if ($cart->billingSameAsShipping()) { echo 'disabled'; } ?>>
    </div>
    <div class="row">
        <div class="form-group col-xs-12 col-sm-12 col-md-6">
            <select class="form-control region" data-required="true" data-shipping="<?php echo $cart->{'checkout.shipping_address.region'}; ?>" name="checkout[billing_address][region]" id="billing-region" autocomplete="region" <?php if ($cart->billingSameAsShipping()) { echo 'disabled'; } ?>>
            <?php foreach (\Shop\Models\Regions::byCountry( $cart->billingCountry( $cart->shippingCountry() ) ) as $region) { ?>
                <option value="<?php echo $region->code; ?>" <?php if ($cart->billingRegion( $cart->{'checkout.shipping_address.region'} ) == $region->code) { echo "selected"; } ?>><?php echo $region->name; ?></option>
            <?php } ?>
            </select>                        
        </div>
        <div class="form-group col-xs-12 col-sm-12 col-md-6">
            <select class="form-control country" data-required="true" data-shipping="<?php echo $cart->shippingCountry(); ?>" name="checkout[billing_address][country]" id="billing-country" autocomplete="country" <?php if ($cart->billingSameAsShipping()) { echo 'disabled'; } ?>>
            <?php foreach (\Shop\Models\Countries::defaultList() as $country) { ?>
                <option data-requires_postal_code="<?php echo $country->requires_postal_code; ?>" value="<?php echo $country->isocode_2; ?>" <?php if ($cart->billingCountry( $cart->shippingCountry() ) == $country->isocode_2) { echo "selected"; } ?>><?php echo $country->name; ?></option>
            <?php } ?>
            </select>
        </div>            
    </div>            
    <div class="row">
        <div class="form-group col-xs-12 col-sm-12 col-md-4">
            <input id="postal_code" type="text" class="form-control postal-code" data-required="<?php echo \Shop\Models\Countries::fromCode( $cart->billingCountry( $cart->shippingCountry() ) )->requires_postal_code ? 'true' : 'false'; ?>" data-shipping="<?php echo $cart->{'checkout.shipping_address.postal_code'}; ?>" name="checkout[billing_address][postal_code]" value="<?php echo $cart->billingPostalCode( $cart->{'checkout.shipping_address.postal_code'} ); ?>" placeholder="Postal Code" autocomplete="postal-code" <?php if ($cart->billingSameAsShipping()) { echo 'disabled'; } ?>>
        </div>
        <div class="form-group col-xs-12 col-sm-12 col-md-8">
            <input id="phone_number" type="text" class="form-control phone" data-required="true" data-shipping="<?php echo $cart->{'checkout.shipping_address.phone_number'}; ?>" name="checkout[billing_address][phone_number]" value="<?php echo $cart->billingPhone( $cart->{'checkout.shipping_address.phone_number'} ); ?>" placeholder="Phone Number" autocomplete="tel" <?php if ($cart->billingSameAsShipping()) { echo 'disabled'; } ?>>
        </div>            
    </div>
</div>

<script>
ShopGetBillingRegions = function(callback_function) {
    var el = jQuery('#billing-country');
    var regions = jQuery('#billing-region');
    var val = el.val();
    var request = jQuery.ajax({
        type: 'get', 
        url: './shop/address/regions/'+val
    }).done(function(data){
        var response = jQuery.parseJSON( JSON.stringify(data), false);
        if (response.result) {
            regions.find('option').remove();
            var count = response.result.length;
            var n = 0;            
            jQuery.each(response.result, function(index,value){
                regions.append(jQuery("<option></option>").text(jQuery('<span>').html(value.name).text()).val(value.code));
                n++;
                if (n == count) {
                    if ( typeof callback_function === 'function') {
                        callback_function( response );
                    }
                }                
            });
        }
    });

    var selected = el.find('option:selected');
    var requires_postal_code = selected.attr('data-requires_postal_code');
    var postal_code = jQuery('#postal_code');
    if (requires_postal_code == 0) {            
        postal_code.attr('data-required', false);
    } else {
    	postal_code.attr('data-required', true);
    }
}

AmritaSubmitNewCard = function()
{
	jQuery('#addccerror').remove();
	
	window.submit_new_card = false;
	
	var ccnumber = jQuery('#new-card-number').val();
	var csc = jQuery('#new-card-csc').val();
	var ccmonth = jQuery('#new-card-month').val();
	var ccyear = jQuery('#new-card-year').val();
	
	if (!ccnumber || !csc || !ccmonth || !ccyear) {
		return false;
	}

	jQuery('.validation-errors').remove();
	
	// Appear to be loading.
	jQuery('#submit-order').addClass('hidden');
	jQuery('#submit-working').removeClass('hidden');
	jQuery('#submit-working-modal').popup('show');
	
	jQuery('#credit-cards').css({ opacity: 0.5 });
	jQuery(this).closest('form').data('locked', true);
	
	var card_data = jQuery('#new-card').find('input,select').serializeJSON();
	var form_data = {};
	form_data.ccnumber = card_data.card.number;
	form_data.ccexpiredate = card_data.card.month+'/'+card_data.card.year;
	form_data.ccexpiremonth = card_data.card.month;
	form_data.ccexpireyear = card_data.card.year;
	form_data.ccname = jQuery('#billing-address .name').val();
	form_data.ccname = form_data.ccname.replace(/ /g,"_");	
	form_data.customer_id = card_data.card.cid;
	form_data.paymentmethod = "7"; // TODO Set this from a config
	
	url_data = jQuery.param( form_data ); 
	
    var request = jQuery.ajax({
        type: 'get', 
        url: 'https://forms.netsuite.com/app/site/hosting/scriptlet.nl?script=105&deploy=1&compid=838520&h=c116260c83b653f5d02a&script_id=104&'+url_data,
        dataType: 'jsonp'
    });
    
	// Success.
	request.done(function (data)
	{
		var add_card_response = $.parseJSON( JSON.stringify(data), false);

		if (add_card_response.creditcard_id && add_card_response.ccnumber) {
		
	        var request_sync_customer = jQuery.ajax({
	            type: 'get', 
	            url: './amrita/customer/sync/force'
	        });

			// Ensure that the correct card is selected	        
			jQuery('#select-token').prepend('<option value="' + add_card_response.creditcard_id + '">' + add_card_response.ccnumber + '</option>').val(add_card_response.creditcard_id).trigger('change');
			jQuery('#existing-cards').prepend('<input type="hidden" name="card[csc]" value="'+ csc +'">');
			jQuery('#token-csc').remove();
			
			// reset the new card form and hide it
			jQuery('#new-card').find('input,select').val('').remove();
			
			jQuery(this).closest('form').data('locked', false);
			jQuery('#credit-cards').css({ opacity: '' });

			window.submit_new_card = true;

			jQuery('#checkout-payment-form').submit();
		}
		
	    // handle errors from the add-new-card request
		else {
			jQuery(this).closest('form').data('locked', false);
			jQuery('#credit-cards').css({ opacity: '' });
			
			jQuery('<p id="addccerror" class="margin-top alert alert-danger validation-errors">The billing information that you entered does not match your credit card information. Please confirm your billing information or use a different form of payment.</p>').insertBefore('#checkout-payment-methods');
			
			window.submit_new_card = false;
			jQuery('body').scrollTo('body', 1000);

			jQuery('#submit-order').trigger('reset');			
		}
	});
	
	// Something went wrong.
	request.error(function (error, text)
	{
		jQuery(this).closest('form').data('locked', false);
		jQuery('#credit-cards').css({ opacity: '' });
		
		window.submit_new_card = false;
	});
	
	return window.submit_new_card;
}

jQuery(document).ready(function(){
    jQuery('[data-numeric]').payment('restrictNumeric');
    
    jQuery('#billing-country').on('change', function(event, callback){
        ShopGetBillingRegions(callback);
    });
        
    <?php if ($cart->shippingRequired()) { ?>
    jQuery('#same-as-shipping').on('change', function(){
        el = jQuery('#same-as-shipping');
        isChecked = el.is(':checked');
        if (isChecked) {
        	jQuery('#select-address').val('');
            jQuery('#existing-address').slideUp();
                        
            e = jQuery('#billing-country');
            if (e.length) {
                if (e.val() != e.attr('data-shipping')) {
                    e.val( e.attr('data-shipping') );
                    ShopGetBillingRegions(function(){
                        r = jQuery('#billing-region')
                        r.val( r.attr('data-shipping') );
                    });
                }                
            }
            jQuery('[data-shipping]').each(function() {
                e = jQuery(this); 
                e.val( e.attr('data-shipping') ).prop('disabled', true);
            });
        }
        else {
        	jQuery('#existing-address').slideDown();
        	
            jQuery('[data-shipping]').each(function() {
                jQuery(this).prop('disabled', false); 
            });            
        }
    });

    jQuery('#same-as-shipping').trigger('change');

    jQuery('#select-address').on('change', function(){
        
    	var el = jQuery(this);
        var val = el.val();
        var selected = el.children(":selected");

        if (selected.attr('id') == 'new-address') {
        	jQuery('#billing-country').val( '<?php echo $cart->shippingCountry(); ?>' );
        } else {
        	jQuery('#billing-country').val( selected.attr('data-country') );
        }
                
    	var el = jQuery(this);
        var val = el.val();

        jQuery('#billing-country').trigger('change', [ function(){
        
            if (selected.attr('id') == 'new-address') {
                // empty all fields
                jQuery('#name').val( '' );
                jQuery('#line_1').val( '' );
                jQuery('#line_2').val( '' );
                jQuery('#city').val( '' );
                jQuery('#postal_code').val( '' );
                jQuery('#phone_number').val( '' );
                jQuery('#billing-region').val( '' );
                
            } else {
                // populate all fields
                jQuery('#name').val( selected.attr('data-name') );
                jQuery('#line_1').val( selected.attr('data-line_1') );
                jQuery('#line_2').val( selected.attr('data-line_2') );
                jQuery('#city').val( selected.attr('data-city') );
                jQuery('#postal_code').val( selected.attr('data-postal_code') );
                jQuery('#phone_number').val( selected.attr('data-phone_number') );
                jQuery('#billing-region').val( selected.attr('data-region') );
            }

        } ] );
                
    });    
    <?php } ?>

    jQuery('#checkout-payment-form').on('submit.amrita_shipping', function() {
        if (jQuery(this).data('validated')) {
            jQuery(this).find('[data-shipping]').prop('disabled', false);
        }        
    });

    jQuery('#checkout-payment-form').on('submit.amrita', function(ev){
        var el = jQuery(this);

        selected_payment_method = el.find('input[name=payment_method]:checked').val();
        if (selected_payment_method != 'amrita.credit_card') {
            return true;
        }        
        
        val = jQuery('#select-token').val();
        if (val=='--') {
            var result = AmritaSubmitNewCard();

            if (result) {
    			// Submit the form
    			el.submit();
                return true;
            }
        
        } else if (!el.data('validated')) {
            
        } else {
            // Submit the form
            return true;
        }

    	ev.preventDefault();
        return false;
    });

    jQuery('#select-token').on('change', function(){
        el = jQuery(this);
        val = el.val();
        if (val=='--') {
            jQuery('#new-card').show();
            jQuery('#new-card').find('input,select').attr('data-required', true).data('required', true).attr('required', 'required');
            jQuery('#token-csc').hide().attr('data-required', false).data('required', false).removeAttr('name').removeAttr('required');
            jQuery('#new-card-csc').show().attr('data-required', true).data('required', true).attr('name', 'card[csc]').attr('required', 'required');

        } else {
        	jQuery('#new-card').hide();
        	jQuery('#new-card').find('input,select').attr('data-required', false).data('required', false).removeAttr('required');
        	jQuery('#token-csc').show().attr('data-required', true).data('required', true).attr('name', 'card[csc]').attr('required', 'required');
        	jQuery('#new-card-csc').hide().attr('data-required', false).data('required', false).removeAttr('name').removeAttr('required');
        }
    });

    jQuery('#select-token').trigger('change');
	    
});
</script>