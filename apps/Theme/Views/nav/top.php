<?php 
$cart = \Shop\Models\Carts::fetch();
$cart_qt = (int) count($cart->items);
\Base::instance()->set( 'cart', $cart );

?>
                              
<div id="top-menu" class="navbar navbar-inverse navbar-fixed-top navbar-custom">
    <div class="container">
            
                <div class="navbar-header">
                    <div class="row visible-xs">
                        <div class="col-xs-5">
                            <a class="navbar-brand" href="./">
                                <img src="./site/images/logo.png" class="img-responsive" />
                            </a>
                                
                        </div>
                        <div class="col-xs-7">
                            <button type="button" class="navbar-toggle" onclick="jQuery('.mobile-collapse-search').collapse('hide'); jQuery('.mobile-collapse').collapse('toggle');">
                                <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span>
                            </button>
                            
                            <button id="mobile-search-toggle" class="navbar-toggle" onclick="jQuery('.mobile-collapse').collapse('hide'); jQuery('.mobile-collapse-search').collapse('toggle');">
                                <i class="fa fa-search"></i>
                            </button>                
                            
                            <a id="mobile-cart" class="navbar-toggle" href="./shop/cart">
                                <span>
                                <?php 
                                if ($cart_qt) {
                                    echo (int) $cart_qt;
                                }
                                ?>
                                </span>                    
                            </a>
                                
                        </div>                        
                    </div>
                    
                    <a class="hidden-xs navbar-brand" href="./">
                        <img src="./site/images/logo.png" class="img-responsive" />
                    </a>
                    
                </div>
                
                <div id="nav-primary-items" class="hidden-xs megamenu">
                    <?php
                    if (class_exists('\Modules\Factory')) {
                        if ($list = (new \Admin\Models\Navigation)->emptyState()
                            ->setState('filter.root', false)
                            ->setState('filter.published', true)
                            ->setState('filter.tree', '52b9bfdbf02e257c2eb5ecac')
                            ->setState('order_clause', array(
                            'tree' => 1,
                            'lft' => 1
                        ))
                            ->getList())
                        {
                            echo (new \Modules\Modules\Megamenu\Module(array(
                                'list' => $list,
                                'class_suffix' => 'nav navbar-nav'
                            )))->html();
                        }
                    }
                    ?>
        
                    <div class="nav-search navbar-left visible-lg">
                        <div class="inner-wrap">
                            <form class="navbar-form" method="GET" action="/search" role="search">
                                <label for="q"><i class="fa fa-search"></i></label>                    
                                <input name="q" type="text" class="form-control">
                            </form>
                        </div>
                    </div>
                    
                    <?php if (!empty($this->auth->getIdentity()->id)) { ?>
                
                    <ul class="nav navbar-nav user-menu">
                    	<li class="livechat-img" style="padding-top:5px;padding-bottom:0px;">
                    		<a style="padding-right:5px;" href="/support/live-chat">
                    			<img class="livechat-img" src="/asset/2014-09-05-chat-icon-gif"/>
                    		</a>
                    	</li>
                        <?php if ($cart_qt) { ?>
                        <li>
                            <a href="./shop/checkout">
                                Checkout
                            </a>
                        </li>                 
                        <?php } ?>                       
                        <li id="jq_cart_content" class="dropdown mega-menu-1">
                            <a href="./shop/cart">
                               <img src="./site/images/bag.png" class="img-responsive" /> Bag <span id="jq_item_count">
                                <?php 
                                if ($cart_qt) {
                                    echo "(" . (int) $cart_qt . ")";
                                }
                                ?>
								</span>
								<b class="caret"></b>
                            </a>
							<ul class="dropdown-menu" id="mini_cart_content">
                               <?php echo $this->renderView('Shop/Site/Views::checkout/mini_cart.php'); ?>
                            </ul>
                        </li> 
					  <li class="dropdown mega-menu-1">
                            <a href="./shop/account" data-target="#dm-my-account" class="dropdown-toggle disabled nav-title-normal " data-toggle="dropdown" data-hover="dropdown">
                                My Account <b class="caret"></b>
                            </a>
                            <ul id="dm-my-account" class="dropdown-menu">
                                <?php if ($loyalty_level = \Amrita\Models\Customers::loyaltyLevel($this->auth->getIdentity()->reload()) ) { ?>
                                <li class="one-column my-account store-credit border-bottom">
                                    <a href="./shop/account">
                                    <?php echo $loyalty_level['title']; ?>
                                    </a>
                                </li>                                
                                <?php } ?>
                                                                                            
                                <?php if ($balance = $this->auth->getIdentity()->reload()->{'shop.credits.balance'}) { ?>
                                <li class="one-column my-account store-credit border-bottom">
                                    <a href="./shop/account">
                                    Store Credit: <span class="store-credit-balance"><?php echo \Shop\Models\Currency::format( $balance ); ?></span>
                                    </a>
                                </li>
                                <?php } ?>
                                <li class="one-column my-account">
                                    <a href="./shop/orders"><i class="fa fa-inbox"></i> My Orders</a>
                                </li>                            
                                <li class="one-column my-account">
                                    <a href="./shop/wishlist"><i class="fa fa-heart"></i> My Wishlist</a>
                                </li>
                                <li class="one-column my-account">
                                    <a href="./shop/account"><i class="fa fa-cog"></i> Account Settings</a>
                                </li>
                                <li class="one-column my-account logout border-top">
                                    <a href="./logout"><i class="fa fa-undo"></i> Sign Out</a>
                                </li>                                
                            </ul>
                        </li>                                
                        <li class="visible-xs">
                            <a href="./logout">Logout</a>
                        </li>
                    </ul>
                    
                    <?php } else { ?>
                    
                    <ul class="nav navbar-nav user-menu">
                    	<li class="livechat-img" style="padding-top:5px;padding-bottom:0px;">
                    		<a style="padding-right:5px;" href="/support/live-chat">
                    			<img class="livechat-img" src="/asset/2014-09-05-chat-icon-gif"/>
                    		</a>
                    	</li>
                        <?php if ($cart_qt) { ?>
                        <li>
                            <a href="./shop/checkout">
                                Checkout
                            </a>
                        </li>                 
                        <?php } ?>                    
                        <li class="dropdown mega-menu-1" id="jq_cart_content">
                            <a href="./shop/cart" data-target="#dm-my-account" class="dropdown-toggle disabled nav-title-normal " data-toggle="dropdown" data-hover="dropdown">
                              <img src="./site/images/bag.png" class="img-responsive" /> Bag <span id="jq_item_count">
                                <?php 
                                if ($cart_qt) {
                                    echo "(" . (int) $cart_qt . ")";
                                }
                                ?>  
								</span>
                                <b class="caret"></b>
                            </a>
                            <ul class="dropdown-menu" id="mini_cart_content">
                               <?php echo $this->renderView('Shop/Site/Views::checkout/mini_cart.php'); ?>
                            </ul>
                        </li>                                                    
                        <li>
                            <a href="./sign-in">Sign In</a>
                        </li>
                        <li>
                            <a href="./register">Register</a>
                        </li>                       
                    </ul>
                                 
                    <?php }?>
        
                </div>
                <!--/.navbar-collapse -->
                
                <div id="mobile-items" class="navbar-inverse">
                    <div id="mobile-nav-primary-items" class="collapse mobile-collapse mobile-nav-primary-items" data-toggle="false">
                        <?php
                        if (class_exists('\Modules\Factory')) {
                            if ($list = (new \Dsc\Mongo\Collections\Navigation)
                                ->setState('filter.root', false)
                                ->setState('filter.published', true)
                                ->setState('filter.tree_slug', 'mobile-navigation')
                                ->setState('order_clause', array(
                                'tree' => 1,
                                'lft' => 1
                            ))
                                ->getList())
                            {
                                echo (new \Modules\Modules\Menu\Module(array(
                                    'list' => $list,
                                    'class_suffix' => 'nav navbar-nav'
                                )))->html();
                            }
                        }
                        ?>
                    </div>
                    
                    <div id="mobile-nav-search" class="collapse mobile-collapse-search nav-search" data-toggle="false">
                        <form class="navbar-form" method="GET" action="/search" role="search">
                            <div class="row">
                                <div class="col-xs-2">
                                    <label for="q"><i class="fa fa-search"></i></label>
                                </div>
                                <div class="col-xs-10">
                                    <input name="q" type="text" class="form-control">
                                </div>
                            </div>
                        </form>                    
                    </div>   
                </div>         

    </div>
</div>      
<div style="clear:both;"></div>
<script>

jQuery(document).ready(function(){
    /*
    var el = jQuery("#mobile-nav-primary-items").clone().attr('id', 'clone').removeClass('collapse').removeClass('mobile-collapse');
    jQuery('body').append(el);
    el.css({
        'position': 'absolute',
        'left': '-1000px'
    });
    el.css({
        'visibility': 'visible',
        'display': 'block'
    });
    console.log( el );    
    var height = el.height() + 50;
    console.log( height );
    //jQuery(".mobile-collapse").css({ maxHeight: height + "px" });
    el.remove();
    */
    //jQuery(".mobile-collapse").css({ maxHeight: $(window).height() - $(".navbar-header").height() + "px" });
	
	jQuery("#jq_cart_content, #mini_cart_content").mouseover(function(){
		jQuery("#jq_cart_content").addClass("open");
	});
	jQuery("#jq_cart_content, #mini_cart_content").mouseout(function(){
		jQuery("#jq_cart_content").removeClass("open");
	});
	var request1 = jQuery.ajax({
		type: 'POST', 
		url: './shop/cart/get_mini_cart_content'
		}).done(function(data){
			jQuery("#mini_cart_content").html(data);
		});
	var request2 = jQuery.ajax({
		type: 'POST', 
		url: './shop/cart/get_mini_cart_count'
		}).done(function(data){
			jQuery("#jq_item_count").html(data);
		});
	
	
});

AmritaMobileScrollTop = function() {
    jQuery('body,html').animate({
        scrollTop: 0
    }, 800);
    return false;    
}



</script>