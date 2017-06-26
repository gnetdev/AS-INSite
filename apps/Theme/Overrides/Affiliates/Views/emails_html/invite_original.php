<?php $link = $SCHEME . '://' . $HOST . $BASE . '/invite/' . $invite->id; ?>

<h3><?php echo $invite->sender_name; ?> has sent you an invitation.</h3>

<p><?php echo $invite->message; ?></p>
<p class="well well-sm"><a href="<?php echo $link; ?>"><?php echo $link; ?></a></p>