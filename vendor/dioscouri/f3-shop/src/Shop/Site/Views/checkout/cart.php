<h4 class="margin-top">Summary</h4>
<div class="total-box">
    <div class="table-responsive">
        <table class="table">
            <tbody>
                <tr>
                    <td><div class="strong">Subtotal:</div></td>
                    <td><div class="price"><?php echo \Shop\Models\Currency::format( $cart->subtotal() ); ?></div></td>
                </tr>
                <?php if ($user_coupons_nonshipping = $cart->userCoupons(false)) { 
					
                    \Dsc\System::instance()->get( 'session' )->set( 'site.removecoupon.redirect', '/shop/checkout' ); ?>
                    <?php foreach ($user_coupons_nonshipping as $coupon) { 
					
					?>
                        <tr class="coupon">
                            <td>
                                <div class="row">
                                    <div class="col-xs-8"><div class="strong">Coupon:<br/><?php echo $coupon['code']; ?>
									<br>
									
									</div></div>
                                    <div class="col-xs-4"><a href="./shop/cart/removeCoupon/<?php echo $coupon['code']; ?>" class="btn btn-default custom-button"><i class="glyphicon glyphicon-remove"></i></a></div>
									<div class="col-xs-12 strong">
									<span style="color:red;"><?php echo $coupon['title']; ?></span>
									</div>
                                </div>
                            </td>
                            <td class="col-xs-6">
                                <div class="price">-<?php echo \Shop\Models\Currency::format( \Dsc\ArrayHelper::get($coupon, 'amount') ); ?></div>
                            </td>                            
                        </tr>
                    <?php } ?>
                <?php } ?>
                
                <?php if ($autocoupons_nonshipping_discount = $cart->autoDiscountTotal(true)) { ?>
                    <tr class="auto_discount">
                        <td>
                            <div class="strong">Discount:</div>
                        </td>
                        <td class="col-xs-6">
                            <div class="price">-<?php echo \Shop\Models\Currency::format( $autocoupons_nonshipping_discount ); ?></div>
                        </td>                            
                    </tr>
                <?php } ?>                            
                                
                <tr>
                    <td><div class="strong">
                            Shipping:
                        </div></td>
                    <td><div class="price">
                        <?php if (!$shippingMethod = $cart->shippingMethod()) {
                            echo \Shop\Models\Currency::format( $cart->shippingEstimate() );
                            echo ' <small>(est)</small>';
                        } else {
                        	echo \Shop\Models\Currency::format( $shippingMethod->total() );
                        }
                        ?>
                    </div></td>
                </tr>
                
                <?php if ($user_coupons_shipping = $cart->userCoupons(true)) { 
                    \Dsc\System::instance()->get( 'session' )->set( 'site.removecoupon.redirect', '/shop/checkout' ); ?>
					<?php foreach ($user_coupons_shipping as $coupon) {
							
					?>
                        <tr class="coupon">
                            <td>
                                <div class="row">
                                    <div class="col-xs-8"><div class="strong">Shipping Coupon:<br/><?php echo $coupon['code']; ?></div></div>
                                    <div class="col-xs-4"><a href="./shop/cart/removeCoupon/<?php echo $coupon['code']; ?>" class="btn btn-default custom-button"><i class="glyphicon glyphicon-remove"></i></a></div>
                                </div>
                            </td>
                            <td class="col-xs-6">
                                <div class="price">-<?php echo \Shop\Models\Currency::format( \Dsc\ArrayHelper::get($coupon, 'amount') ); ?></div>
                            </td>                            
                        </tr>
                    <?php } ?>
                <?php } ?>
                
                <?php if ($autocoupons_shipping_discount = $cart->autoShippingDiscountTotal()) { ?>
                    <tr class="auto_discount">
                        <td>
                            <div class="strong">Shipping Discount:</div>
                        </td>
                        <td class="col-xs-6">
                            <div class="price">-<?php echo \Shop\Models\Currency::format( $autocoupons_shipping_discount ); ?></div>
                        </td>                            
                    </tr>
                <?php } ?>
                
                <?php if ($credit = $cart->creditTotal()) { ?>
                    <tr class="auto_discount">
                        <td>
                            <div class="strong">Store Credit:</div>
                        </td>
                        <td class="col-xs-6">
                            <div class="price">-<?php echo \Shop\Models\Currency::format( $credit ); ?></div>
                        </td>                            
                    </tr>
                <?php } ?>                
                
                <tr>
                    <td><div class="strong">
                            <span data-toggle="tooltip" data-placement="top" title="Taxable amount: <?php echo \Shop\Models\Currency::format( $cart->taxableTotal() ); ?>">
                            Tax:
                            </span>
                        </div></td>
                    <td><div class="price">
                        <span data-toggle="tooltip" data-placement="top" title="Taxable amount: <?php echo \Shop\Models\Currency::format( $cart->taxableTotal() ); ?>">
                        <?php if (!$shippingMethod = $cart->shippingMethod()) {
                            echo \Shop\Models\Currency::format( $cart->taxEstimate() );
                            echo ' <small>(est)</small>';
                        } else {
                        	echo \Shop\Models\Currency::format( $cart->taxTotal() );
                        }
                        ?>
                        </span>                    
                    </div></td>
                </tr>
                <?php if ($giftcards = $cart->giftcards) { \Dsc\System::instance()->get( 'session' )->set( 'site.removegiftcard.redirect', '/shop/checkout' ); ?>
                    <?php foreach ($giftcards as $giftcard) { ?>
                        <tr class="giftcard">
                            <td>
                                <div class="row">
                                    <div class="col-xs-8"><div class="strong">Gift Card</div></div>
                                    <div class="col-xs-4"><a href="./shop/cart/removeGiftCard/<?php echo $giftcard['code']; ?>" class="btn btn-default custom-button"><i class="glyphicon glyphicon-remove"></i></a></div>
                                </div>
                                <small><?php echo $giftcard['code']; ?></small>
                            </td>
                            <td class="col-xs-6">
                                <div class="price">-<?php echo \Shop\Models\Currency::format( \Dsc\ArrayHelper::get($giftcard, 'amount') ); ?></div>
                            </td>                            
                        </tr>
                    <?php } ?>
                <?php } ?>
                            
            </tbody>
            <tfoot>
                <tr>
                    <td><div class="strong">
                            Total<?php if (!$shippingMethod = $cart->shippingMethod()) { ?> <small>(est)</small> <?php } ?>:
                        </div></td>
                    <td><div class="price"><?php echo \Shop\Models\Currency::format( $cart->total() ); ?></div></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<?php if (empty($cart->userCoupons())) { \Dsc\System::instance()->get( 'session' )->set( 'site.addcoupon.redirect', '/shop/checkout' ); ?>
<div class="margin-top">
    <div class="row">
        <div class="col-md-12">
            <div id="coupon">
                <form class="form" role="form" action="./shop/cart/addCoupon" method="post">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" name="coupon_code" class="form-control" id="inputCouponCode" placeholder="Have a Coupon?">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="submit">Add</button>
                            </span>                  
                        </div>                          
                    </div>                                    
                </form>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<?php if (empty($giftcards)) { \Dsc\System::instance()->get( 'session' )->set( 'site.addgiftcard.redirect', '/shop/checkout' ); ?>
<div class="margin-top">
    <div class="row">
        <div class="col-md-12">
            <div id="coupon">
                <form class="form" role="form" action="./shop/cart/addGiftCard" method="post">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" name="giftcard_code" class="form-control" id="inputGiftCardCode" placeholder="Have a Gift Card?">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="submit">Add</button>
                            </span>                  
                        </div>                          
                    </div>                                    
                </form>
            </div>
        </div>
    </div>
</div>           
<?php } ?>

<div class="table-responsive checkout-cart margin-top">
    <form method="post" action="">
	<table class="table">
        <thead>
            <tr>
                <th colspan="4">Items</th>
            </tr>
			 <tr>
                        <th class="col-xs-5"><div class="title-wrap">Product</div></th>
                        <th class="col-xs-1"><div class="title-wrap"> Quantity </div></th>
                        <th class="col-xs-5"><div class="title-wrap">Subtotal</div></th>
                        <th class="col-xs-1"><div class="title-wrap"><i class="glyphicon glyphicon-remove"></i></div></th>
                    </tr>
        </thead>
        <tbody>
        <?php foreach ($cart->items as $key=>$item) { ?>
             <tr>
                        <td>
                            <div class="cart-product row">
                                <div class="col-md-3">
                                    <?php if (\Dsc\ArrayHelper::get($item, 'image')) { ?>
                                    <a href="./shop/product/<?php echo \Dsc\ArrayHelper::get($item, 'product.slug'); ?>">
                                        <img src="./asset/thumb/<?php echo \Dsc\ArrayHelper::get($item, 'image'); ?>" alt="" class="img-responsive" />
                                    </a>
                                    <?php } ?>
                                </div>
                                <div class="col-md-9">
                                    <h4>
                                        <a href="./shop/product/<?php echo \Dsc\ArrayHelper::get($item, 'product.slug'); ?>"><?php echo \Dsc\ArrayHelper::get($item, 'product.title'); ?></a>
                                        <?php if (\Dsc\ArrayHelper::get($item, 'attribute_title')) { ?>
                                        <div><small><?php echo \Dsc\ArrayHelper::get($item, 'attribute_title'); ?></small></div>
                                        <?php } ?>                                            
                                    </h4>
                                    <?php /* ?><div class="details">
                                        <?php if (\Dsc\ArrayHelper::get($item, 'sku')) { ?>
                                        <p class="detail-line">
                                            <label>SKU:</label> <?php echo \Dsc\ArrayHelper::get($item, 'sku'); ?>
                                        </p>
                                        <?php } ?><?php */ ?>
                                        <p class="detail-line">
                                            <?php echo \Shop\Models\Currency::format( \Dsc\ArrayHelper::get($item, 'price') ); ?> each
                                        </p>
                                    </div> 

                                </div>
                                
                            </div>
                            
                        </td>

                        <td>
                            <div class="price">
                                <input type="text" class="form-control" value="<?php echo \Dsc\ArrayHelper::get($item, 'quantity'); ?>" placeholder="Quantity" name="quantities[<?php echo \Dsc\ArrayHelper::get($item, 'hash'); ?>]" />
                            </div>
                        </td>
                        <td><div class="price rate">
						<?php 
							$discount_amount	=	\Dsc\ArrayHelper::get($item, 'discount'); 
							$discount_type	=	\Dsc\ArrayHelper::get($item, 'discount_type'); 
							$discount_class 	=	"";
							if(!empty($discount_amount)){
								$discount_class 	=	"old-price";
							}
						?>
						<div class="<?php echo $discount_class; ?>" ><?php echo \Shop\Models\Currency::format( $cart->calcItemSubtotal( $item ) ); ?></div>
						
						<?php if(!empty($discount_amount)){ ?>
						<div class="net-price">
						<?php
						$total_amount 	=	$cart->calcItemSubtotal( $item )-\Dsc\ArrayHelper::get($item, 'discount');
						?>
						<?php echo \Shop\Models\Currency::format( $total_amount); ?>
						</div>
						<div class="discount-div">
							
							
							
							<div class="discount-value">
							<?php 
								if($discount_type=='percentage'){
								?>
								<?php echo \Dsc\ArrayHelper::get($item, 'discount_value'); ?>%
								<?php	
								}else{
								?><?php echo \Shop\Models\Currency::format( \Dsc\ArrayHelper::get($item, 'discount') );  ?><?php	
								}
							?>
							
							<?php /*
								if($discount_type=='flat-rate'){
								?><?php echo \Shop\Models\Currency::format( \Dsc\ArrayHelper::get($item, 'discount') ); ?> <?php } ?>
							<?php if($discount_type!='flat-rate'){
							?>
								<?php echo \Dsc\ArrayHelper::get($item, 'discount_value'); ?>%
							<?php 
							}*/
							?>
							Off</div>
						</div>
						
						
						<?php } ?>
						
						
						
						
						</div></td>
                        <td class="text-center"><a href="./shop/cart/remove/<?php echo \Dsc\ArrayHelper::get($item, 'hash'); ?>" class="btn btn-default custom-button"><i class="glyphicon glyphicon-remove"></i></a></td>
                    </tr>
                  <?php } ?>
        
        </tbody>
    </table>
	<div class="cart-table-actions">
        	    <div class="pull-right">
            		<button type="submit" name="updateQuantities" onclick="this.form.action='./shop/cart/updateQuantities';" class="cart-table-update btn btn-default custom-button btn-block">
            			Update Quantities
                    </button>
                </div>
                <div class="clearfix"></div>
    </div>
	</form>
</div>

<script>
jQuery(document).ready(function(){
	jQuery('[data-toggle="tooltip"]').tooltip();
});
</script>