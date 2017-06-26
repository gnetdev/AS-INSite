<?php $link = $SCHEME . '://' . $HOST . $BASE . '/invite/' . $invite->id; ?>

<?php echo $invite->sender_name; ?> has sent you an invitation. 

<?php echo $invite->message; ?> 

Visit by opening this link in your browser: <?php echo $link; ?> 
