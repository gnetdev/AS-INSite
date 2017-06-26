
<div><p style="text-align: center; border:1px solid gray; padding: 3px;">FREE Shipping on Orders $75+ (Continental U.S. Only) </p></div>

    <table border="0" cellpadding="0" cellspacing="0" width="750" id="templateHeader" style="background-color: #FFFFFF;border-bottom: 0;">
        <tr width="750">
            <td>
                <a href="https://amritasingh.com/?kmi=*|URL:EMAIL|*&km_source=abandonedcartemail" target="_blank"><img src="https://gallery.mailchimp.com/e7a8c83d29dfb2e19f87936d2/images/newsletter_header_01.jpg" alt="Amrita Singh Jewelry" border="0" style="border-style: none;margin: 0;padding: 0;max-width: 374px;border: none;font-size: 14px;font-weight: bold;height: auto;line-height: 100%;outline: none;text-decoration: none;text-transform: capitalize;"></a>
            </td>
            <td>
                <a href="https://amritasingh.com/shop/collection/new-arrivals?kmi=*|URL:EMAIL|*&km_source=abandonedcartemail" target="_blank"><img src="https://gallery.mailchimp.com/e7a8c83d29dfb2e19f87936d2/images/newsletter_header_02.jpg" alt="New Arrivals | Amrita Singh" border="0" style="border-style: none;margin: 0;padding: 0;max-width: 114px;border: none;font-size: 14px;font-weight: bold;height: auto;line-height: 100%;outline: none;text-decoration: none;text-transform: capitalize;"></a>
            </td>
            <td>
                <a href="https://amritasingh.com/shop/collection/amrita-exclusives?kmi=*|URL:EMAIL|*&km_source=abandonedcartemail" target="_blank"><img src="https://gallery.mailchimp.com/e7a8c83d29dfb2e19f87936d2/images/newsletter_header_03.jpg" alt="Exclusives | Amrita Singh" border="0" style="border-style: none;margin: 0;padding: 0;max-width: 99px;border: none;font-size: 14px;font-weight: bold;height: auto;line-height: 100%;outline: none;text-decoration: none;text-transform: capitalize;"></a>
            </td>
            <td>
                <a href="https://amritasingh.com/shop/category/fine-jewelry?kmi=*|URL:EMAIL|*&km_source=abandonedcartemail" target="_blank"><img src="https://gallery.mailchimp.com/e7a8c83d29dfb2e19f87936d2/images/newsletter_header_04.jpg" alt="Maharaja Collection | Amrita Singh" border="0" style="border-style: none;margin: 0;padding: 0;max-width: 113px;border: none;font-size: 14px;font-weight: bold;height: auto;line-height: 100%;outline: none;text-decoration: none;text-transform: capitalize;"></a>
            </td>
            <td>
                <a href="https://amritasingh.com/shop/collection/bargains?kmi=*|URL:EMAIL|*&km_source=abandonedcartemail" target="_blank"><img src="https://gallery.mailchimp.com/e7a8c83d29dfb2e19f87936d2/images/newsletter_header_05.jpg" alt="40% Off Winter Clearance Sale | Amrita Singh" border="0" style="border-style: none;margin: 0;padding: 0;max-width: 50px;border: none;font-size: 14px;font-weight: bold;height: auto;line-height: 100%;outline: none;text-decoration: none;text-transform: capitalize;"></a>
            </td>
        </tr>
    </table>
<div>

<?php $view_link = $SCHEME . '://' . $HOST . $BASE . '/shop/cart/'.(string)$cart->id.'?email=1&user_id='.(string)$user->id.'&idx='.$idx.'&auto_login_token='.$token; ?>
<p><?php echo trim( 'Hi ' . $user->fullName() ); ?>,</p>


<?php echo $notification['text']['html']; ?>
</div>

<p>Here's what you left behind:</p>
<table style="width: 100%;">
    <?php foreach ($cart->items as $item) { ?>
    <tr>
        <td style="width: 75px;">
            <?php if (\Dsc\ArrayHelper::get($item, 'image')) { ?>
            <img style="width: 100%;" src="<?php echo $SCHEME . '://' . $HOST . $BASE; ?>/asset/thumb/<?php echo \Dsc\ArrayHelper::get($item, 'image'); ?>" alt="" />
            <?php } ?>
        </td>
        <td style="vertical-align: top;">
            <h4>
                <?php echo \Dsc\ArrayHelper::get($item, 'product.title'); ?>
                <?php if (\Dsc\ArrayHelper::get($item, 'attribute_title')) { ?>
                <div>
                    <small><?php echo \Dsc\ArrayHelper::get($item, 'attribute_title'); ?></small>
                </div>
                <?php } ?>
                <div>
                    <small>
                    <span class="quantity"><?php echo $quantity = \Dsc\ArrayHelper::get($item, 'quantity'); ?></span>
                    x
                    <span class="price"><?php echo \Shop\Models\Currency::format( $price = \Dsc\ArrayHelper::get($item, 'price') ); ?></span>
                    </small> 
                </div>
            </h4>
        </td>
        <td style="vertical-align: top; text-align: right;">
            <h4>
                <?php echo \Shop\Models\Currency::format( $quantity * $price ); ?>
            </h4>
        </td>
    </tr>        
    <?php } ?>
</table>

<p>It's not too late, to complete your purchase <a href="<?php echo $view_link; ?>">Go to Your Shopping Bag</a>.</p>

<p>If you have any questions, we're here to help. Give us a call at 1-855-426-7482 or <a href="https://amritasingh.com/support/case">Email us</a>.
<p></p>
<a href="https://amritasingh.com/pages/referral-program"><img src="https://gallery.mailchimp.com/e7a8c83d29dfb2e19f87936d2/images/2db57fef-00b5-4217-af5b-d16e3d0bbe80.png"/></a>