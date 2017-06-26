<li>
<div class="table-responsive checkout-cart ">
	<div class="table_height">
		<form method="post">
		<table class="table">	
        
			<tbody>
			<?php foreach ($cart->items as $key=>$item) { ?>
            <tr>
                <td class="checkout-cart-image">
                    <figure>
                        <?php if (\Dsc\ArrayHelper::get($item, 'image')) { ?>
                        <img class="img-responsive" src="./asset/thumb/<?php echo \Dsc\ArrayHelper::get($item, 'image'); ?>" alt="" />
                        <?php } ?>
                    </figure>
                </td>
                <td class="checkout-cart-product">
                    <h4>
                        <?php echo \Dsc\ArrayHelper::get($item, 'product.title'); ?>
                        <?php if (\Dsc\ArrayHelper::get($item, 'attribute_title')) { ?>
                        <div>
                            <small><?php echo \Dsc\ArrayHelper::get($item, 'attribute_title'); ?></small>
                        </div>
                        <?php } ?>      
						<div>
						<small>Quantity:<?php echo \Dsc\ArrayHelper::get($item, 'quantity'); ?></small>
						</div>
                    </h4>
					
					<div class="clearfix"></div>
					<div class="details">

                    </div>
                    <div>
                        
					</div>
					<div>
                        <span class="price">
						
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
							?>
							<?php echo \Shop\Models\Currency::format( \Dsc\ArrayHelper::get($item, 'discount') ); ?><?php } ?>
							<?php if($discount_type!='flat-rate'){
							?>
								<?php echo \Dsc\ArrayHelper::get($item, 'discount_value'); ?>%
							<?php 
							}*/
							?>
							Off</div>
						</div>
						<?php } ?>
						
						</span> 
                    </div>
                    
                </td>
				
                   
                
            </tr>
			
			<?php } ?>
        
			</tbody>
		</table>
		
		</form>
	</div>
	<table>
	<?php 
	$discount 	=	0;
	if ($user_coupons_nonshipping = $cart->userCoupons(false)) { 
                    \Dsc\System::instance()->get( 'session' )->set( 'site.removecoupon.redirect', '/shop/checkout' ); ?>
                    <?php 
					foreach ($user_coupons_nonshipping as $coupon) {
								$discount	=	$discount+\Dsc\ArrayHelper::get($coupon, 'amount');
					?>
                        
                    <?php } ?>
    <?php } ?>
	<?php 
	if ($giftcards = $cart->giftcards) { \Dsc\System::instance()->get( 'session' )->set( 'site.removegiftcard.redirect', '/shop/checkout' ); ?>
		<?php 
		foreach ($giftcards as $giftcard) {
					$discount	=	$discount+\Dsc\ArrayHelper::get($giftcard, 'amount');
		?>  
		<?php } ?>
    <?php } ?>
		<?php if(!empty($discount)){ ?>
		<tr>
                    <td colspan="2" class="subtotle"><div class="strong">
                            Discount :
                        </div>
                    <div class="price">-<?php echo \Shop\Models\Currency::format( $discount ); ?></div></td>
        </tr>
		<?php } ?>
		<tr>
                    <td colspan="2" class="subtotle">
					<div class="strong">
                            Total<?php if (!$shippingMethod = $cart->shippingMethod()) { ?> <small>(est)</small> <?php } ?>:
                    </div>
                    <div class="price"><?php echo \Shop\Models\Currency::format( $cart->total() ); ?></div></td>
        </tr>
	</table>
	<div class="view_cart"><a href="./shop/cart">View Bag ( <span><?php echo (int) count($cart->items); ?></span> items)</a></div>
</div>
</li>