<?php
$user = $this->auth->getIdentity();

// only display this if the user isn't already subscribed
$MailChimp = new \Drewm\MailChimp('84c41f4f72b8c9d33a35abba9a7f1ca2-us2');
$result = $MailChimp->call('helper/lists-for-email', array(
    'email' => array('email'=>$user->email(true)),
));

if (!empty($result) && !empty($result->error))
{

}
elseif (!empty($result))
{
    $subscribed = false;
    //653929
    foreach ((array) $result as $subscription) 
    {
    	if (!empty($subscription['web_id']) && $subscription['web_id'] == '653929') 
    	{
    	    $subscribed = true;
    	}
    }

}

if (empty($subscribed)) {
?>
<div class="checkbox form-group">
    <label>
        <input type="checkbox" name="checkout[mailchimp_subscribe]" value="<?php echo '653929'; ?>" checked>
        Keep me updated
    </label>
</div>
<?php } ?>