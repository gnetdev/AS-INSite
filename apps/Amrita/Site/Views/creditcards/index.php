<div class="container">
    <ol class="breadcrumb">
        <li>
            <a href="./shop/account">My Account</a>
        </li>
        <li class="active">Credit Cards</li>
    </ol>
    
    <hr/>    
    
    <div class="row">
        <div class="col-md-8">
        
            <legend>
                Existing Cards
            </legend>

            <?php if (empty($creditCards)) { ?>
                <p>No cards found.</p>
            <?php } else { ?>
                <?php $n=0; $count = count($creditCards); ?>
                
                <?php foreach ($creditCards as $creditCard) { ?>
                
                    <?php if ($n == 0 || ($n % 2 == 0)) { ?><div class="row"><?php } ?>
                    
                    <div class="col-xs-6 col-sm-6 col-md-6 category-article category-grid">
                
                        
                        <div class="panel panel-default">
                            <div class="panel-heading clearfix">
                                <a class="btn btn-xs btn-danger pull-right" data-bootbox="confirm" href="./user/credit-cards/delete/<?php echo $creditCard['internalId']; ?>">
                                    Delete <i class="fa fa-times"></i>
                                </a>                                        
                            </div>
                            <div class="panel-body">
                                <div><?php echo $creditCard['ccName']; ?></div>
                                <div><?php echo $creditCard['ccNumber']; ?> - expires <?php echo date('m/Y', strtotime( $creditCard['ccExpireDate'] ) ); ?></div>
                            </div>                
                        </div>
                        
                    </div>
                    
                    <?php $n++; if (($n % 2 == 0) || $n==$count) { ?></div><?php } ?>
                            
                    
                <?php } ?>
            <?php } ?>        
        
        </div>
        <div class="col-md-4">
            
            <legend>
                Add a New Card
            </legend>            
            
            <p class="help-block">During checkout, you will be asked to provide a billing address and security code for verification.</p>
            
            <div id="new-card" class="add-new-card">
                <input id="new-card-cid" type="hidden" name="card[cid]" value="<?php echo $this->auth->getIdentity()->{'netsuite.id'}; ?>">
                
                <div class="row">
                    <div class="form-group col-md-12">
                        <input id="new-card-name" type="text" class="form-control name" data-required="true" name="card[name]" value="" placeholder="Name on card" autocomplete="off">
                    </div>
                </div>                           
                
                <div class="row">
                    <div class="form-group col-md-12">
                        <input id="new-card-number" type="text" class="form-control number" data-required="true" name="card[number]" value="" placeholder="Card Number" autocomplete="off">
                    </div>
                </div>           
                <div class="row">         
                    <div class="form-group col-xs-6 col-sm-6 col-md-6">
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
                    <div class="form-group col-xs-6 col-sm-6 col-md-6">
                        <select class="form-control year" data-required="true" name="card[year]" id="new-card-year">
                        <?php for ($n=date('Y'); $n<date('Y')+25; $n++) { ?>
                        	<option value="<?php echo $n; ?>"><?php echo $n; ?></option>
                        <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <span id="submit-form" class="btn btn-primary" onclick="AmritaSubmitNewCard();">Submit</span>
                <div id="submit-working" class="working pull-left hidden">
                	<img src="./minify/Shop/Assets/images/working.gif" alt="Working" />
                	Working ... Please wait
                </div>
            </div>
            
        </div>        
    </div>

</div>

<script>
AmritaSubmitNewCard = function()
{
	jQuery('#addccerror').remove();
	
	window.submit_new_card = false;
	
	var ccnumber = jQuery('#new-card-number').val();
	var ccmonth = jQuery('#new-card-month').val();
	var ccyear = jQuery('#new-card-year').val();
	
	if (!ccnumber || !ccmonth || !ccyear) {
		return false;
	}
	
	// Appear to be working
	jQuery('#submit-form').addClass('hidden');
	jQuery('#submit-working').removeClass('hidden');
	
	//jQuery('#credit-cards').css({ opacity: 0.5 });
	//jQuery(this).closest('form').data('locked', true);
	
	var card_data = jQuery('#new-card').find('input,select').serializeJSON();
	var form_data = {};
	form_data.ccnumber = card_data.card.number;
	form_data.ccexpiredate = card_data.card.month+'/'+card_data.card.year;
	form_data.ccexpiremonth = card_data.card.month;
	form_data.ccexpireyear = card_data.card.year;
	form_data.ccname = jQuery('#new-card-name').val();
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
	        }).done(function(dd){
		        
		        window.submit_new_card = true;
		        
		        // Refresh the window
		        window.location = './user/credit-cards';
	        
		    });
		}
		
	    // handle errors from the add-new-card request
		else {
			
			jQuery('<p id="addccerror" class="margin-top alert alert-danger validation-errors">Could not add credit card to your account as provided.  Please confirm your entries and try again.</p>').insertBefore('#new-card');

			window.submit_new_card = false;
			
			jQuery('#submit-form').removeClass('hidden');
			jQuery('#submit-working').addClass('hidden');
						
		}
	});
	
	// Something went wrong.
	request.error(function (error, text)
	{
		window.submit_new_card = false;

		jQuery('<p id="addccerror" class="margin-top alert alert-danger validation-errors">There was an error adding your credit card to your account.  Please try again later.</p>').insertBefore('#new-card');

		jQuery('#submit-form').removeClass('hidden');
		jQuery('#submit-working').addClass('hidden');
				
	});
	
	return window.submit_new_card;
}
</script>