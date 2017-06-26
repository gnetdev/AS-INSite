<script src="./ckeditor/ckeditor.js"></script>
<script>
jQuery(document).ready(function(){
    CKEDITOR.replaceAll( 'wysiwyg' );    
});
</script>

<div class="well">

<form id="settings-form" role="form" method="post" class="clearfix">

    <div class="clearfix">
        <button type="submit" class="btn btn-primary pull-right">Save Changes</button>
    </div>
    
    <hr/>

    <div class="row">
        <div class="col-md-3 col-sm-4">
            <ul class="nav nav-pills nav-stacked">
                <li class="active">
                    <a href="#tab-general" data-toggle="tab"> General Settings </a>
                </li>            
                <li>
                    <a href="#tab-home" data-toggle="tab"> Home View </a>
                </li>
                <li>
                    <a href="#tab-users" data-toggle="tab"> User Settings </a>
                </li>
                <li>
                    <a href="#tab-checkout" data-toggle="tab"> Checkout Settings </a>
                </li>
                <li>
                    <a href="#tab-orders" data-toggle="tab"> Order Settings </a>
                </li>
                <li>
                    <a href="#tab-order-confirmation" data-toggle="tab"> Order Confirmation </a>
                </li>
                <li>
                    <a href="#tab-cart-abandonment-emails" data-toggle="tab"> Cart Abandonment Emails </a>
                </li>
                <li>
                    <a href="#tab-reviews" data-toggle="tab"> Product Review Settings </a>
                </li>                                                
                <?php if (!empty($this->event)) { foreach ((array) $this->event->getArgument('tabs') as $key => $title ) { ?>
                <li>
                    <a href="#tab-<?php echo $key; ?>" data-toggle="tab"> <?php echo $title; ?> </a>
                </li>
                <?php } } ?>                
            </ul>
        </div>

        <div class="col-md-9 col-sm-8">

            <div class="tab-content stacked-content">
            
                <div class="tab-pane fade in active" id="tab-general">
                
                    <?php echo $this->renderLayout('Shop/Admin/Views::settings/general.php'); ?>

                </div>
            
                <div class="tab-pane fade in" id="tab-home">
                
                    <?php echo $this->renderLayout('Shop/Admin/Views::settings/home.php'); ?>

                </div>
                
                <div class="tab-pane fade in" id="tab-users">
                
                    <?php echo $this->renderLayout('Shop/Admin/Views::settings/users.php'); ?>

                </div>
                
                <div class="tab-pane fade in" id="tab-checkout">
                
                    <?php echo $this->renderLayout('Shop/Admin/Views::settings/checkout.php'); ?>

                </div>
                
                <div class="tab-pane fade in" id="tab-orders">
                
                    <?php echo $this->renderLayout('Shop/Admin/Views::settings/orders.php'); ?>

                </div>
                
                <div class="tab-pane fade in" id="tab-order-confirmation">
                
                    <?php echo $this->renderLayout('Shop/Admin/Views::settings/order_confirmation.php'); ?>

                </div>
                
                <div class="tab-pane fade in" id="tab-cart-abandonment-emails">
                
                    <?php echo $this->renderLayout('Shop/Admin/Views::settings/cart_abandonment_emails.php'); ?>

                </div>
                
                <div class="tab-pane fade in" id="tab-reviews">                
                    <?php echo $this->renderLayout('Shop/Admin/Views::settings/reviews.php'); ?>
                </div>

                <?php if (!empty($this->event)) { foreach ((array) $this->event->getArgument('content') as $key => $content ) { ?>
                <div class="tab-pane fade in" id="tab-<?php echo $key; ?>">
                    <?php echo $content; ?>
                </div>
                <?php } } ?>

            </div>

        </div>
    </div>

</form>

</div>