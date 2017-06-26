<?php $link = $SCHEME . '://' . $HOST . $BASE . '/shop/giftcard/' . $giftcard->id . '/' . $giftcard->token; ?>

<p>Just a reminder that you have an unused AmritaSingh.com gift card!</p>
<p><strong>Your gift card has a balance of <?php echo \Shop\Models\Currency::format( $giftcard->balance() ) ?>.</strong></p>
<p>Please note that this is a new gift card code. We recently switched platforms for our website and are re-issuing your gift card. 
You must use this NEW gift card code to checkout. The old code is no longer valid.</p>
<p>To use your gift card, enter the code in the Gift Card box at checkout and click "add".</p>
<p>To view your gift card, please click here: <a href="<?php echo $link; ?>"><?php echo $link; ?></a></p>
<p>Happy Shopping!</p>