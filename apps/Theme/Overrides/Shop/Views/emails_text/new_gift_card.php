<?php $link = $SCHEME . '://' . $HOST . $BASE . '/shop/giftcard/' . $giftcard->id . '/' . $giftcard->token; ?>

Just a reminder that you have an unused AmritaSingh.com gift card! 

Your gift card has a balance of <?php echo \Shop\Models\Currency::format( $giftcard->balance() ) ?>. 

Please note that this is a new gift card code. We recently switched platforms for our website and are re-issuing your gift card.  
You must use this NEW gift card code to checkout. The old code is no longer valid. 

To use your gift card, enter the code in the Gift Card box at checkout and click "add". 

To view, print, or share your gift card, please open this URL in your browser: <?php echo $link; ?> 

Thanks again! 