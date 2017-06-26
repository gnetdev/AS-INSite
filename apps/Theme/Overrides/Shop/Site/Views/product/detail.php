<script src="./ThemeAssets/js/bootbox-4.3.0.min.js"></script>

<?php
	$item->url = './shop/product/' . $item->{'slug'};
	$item->urlwanelo = 'https://amritasingh.com/shop/product/' . $item->{'slug'} . '?km_source=wanelo&km_campaign=wanelo';
	$item->urlfull = 'https://amritasingh.com/shop/product/' . $item->{'slug'};
	$item->ogimageurl = 'https://amritasingh.com/asset/' . $item->{'featured_image.slug'};
	$images = $item->images();
	$variantsInStock = $item->variantsInStock();
	$variant_1 = current( $variantsInStock );
	$wishlist_state = \Shop\Models\Wishlists::hasAddedVariant($variant_1['id'], (string) $this->auth->getIdentity()->id) ? 'false' : 'true';
 
	$settings = \Admin\Models\Settings::fetch();
	$is_kissmetrics = $settings->enabledIntegration( 'kissmetrics' );
	
	$custom_head = $this->app->get('head.custom') . 
	"<meta property='wanelo:product:name' content='$item->title' />
	<meta property='wanelo:product:price' content='" . $item->price() . "' />
	<meta property='wanelo:product:price:currency' content='USD' />
	<meta property='wanelo:product:url' content='$item->urlwanelo' />
	<meta property='og:price:amount' content='" . $item->price() . "' />
	<meta property='og:price:currency' content='USD' />
	<meta property='og:brand' content='Amrita Singh Jewelry' />
	<meta property='twitter:card' content='product' />
	<meta property='twitter:site' content='@amritasjewelry' />		
	<meta property='twitter:image' content='$item->ogimageurl' />
	<meta property='twitter:title' content='$item->title' />
	<meta property='twitter:description' content='" .strip_tags( $item->getAbstract() ) . "' />		
	<meta property='twitter:data1' content='" . $item->price() . "' />
	<meta property='twitter:label1' content='PRICE' />
	<meta property='twitter:data2' content='Online' />
	<meta property='twitter:label2' content='LOCATION' />
	";
	
	$this->app->set('head.custom', $custom_head );
	$this->app->set( 'og.type', 'product' );
	$this->app->set( 'og.description', strip_tags( $item->getAbstract() ) );
	$this->app->set( 'og.url', $item->urlfull );
	$this->app->set( 'og.title', $item->title );
	$this->app->set( 'og.image', $item->ogimageurl );
?> 

<script>

Shop.toggleWishlist = function(state) {
	var new_html = '';
	if( state == true ){ // enable adding to wishilist
		new_html = "<a class='add-to-wishlist' href='javascript:void(0);'><i class='glyphicon glyphicon-heart'></i> Add to wishlist</a>";
	} else {
		new_html = "<a href='javascript:void(0);'><i class='glyphicon glyphicon-heart'></i> In your wishlist</a>";
	}
	jQuery( '.add-to-wishlist-container' ).html( new_html );
}

jQuery(document).ready(function(){
   jQuery('.product-details').on('click', '.add-to-wishlist', function(ev){
       ev.preventDefault();
       var el = jQuery(this);
       var variant_id = el.closest('form').find('.variant_id').val();
       if (variant_id) {
	        var request = jQuery.ajax({
	            type: 'get', 
	            url: './shop/wishlist/add?variant_id='+variant_id
	        }).done(function(data){
	            var response = jQuery.parseJSON( JSON.stringify(data), false);
	            if (response.result) {
					jQuery( 'select[name="variant_id"] option[value="'+variant_id+'"]' ).attr( 'data-wishlist', "0" );
	                el.replaceWith("<a href='javascript:void(0);'><i class='glyphicon glyphicon-heart'></i> In your wishlist</a>");
	            }
	        });
       } 
   });

   jQuery('select[name="variant_id"]').on('change', function(e) {
		   	wishlist_state = jQuery( e.target ).find("option:selected").attr('data-wishlist') == '1';
			Shop.toggleWishlist(wishlist_state);
	   });

   Shop.toggleWishlist(<?php echo $wishlist_state; ?>);

	var select = jQuery('select.select-variant');
	if (select.length) {
		var selected = select.find("option:selected");
        var variant = jQuery.parseJSON( selected.attr('data-variant') );
        if (variant.image) {
        	Shop.selectVariant(variant);
        }		
	}
   
});
</script>

<div class="container">
    <ol class="breadcrumb">
        <li>
            <a href="./shop">Shop</a>
        </li>
        <?php if (!empty($surrounding)) { foreach (array_reverse( $this->session->lastUrls() ) as $lastUrl) { ?>
        <li>
            <a href=".<?php echo $lastUrl['url']; ?>"><?php echo $lastUrl['title']; ?></a>
        </li>        
        <?php } } ?>
        <li class="active"><?php echo $item->title; ?></li>
    </ol>
</div>

<?php /* Only do this if we have the Listing URL, prev, or next */ ?>
<?php if (!empty($surrounding['prev']) || !empty($surrounding['next'])) { ?>
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <div class="pull-right">
            	<?php if (!empty($surrounding['prev'])) { ?>
                <a class="btn btn-link" href="./shop/product/<?php echo $surrounding['prev']->slug; ?>"><i class="fa fa-chevron-left"></i> Prev</a>
                <?php } ?>
                <?php if (!empty($surrounding['next'])) { ?>
                <a class="btn btn-link" href="./shop/product/<?php echo $surrounding['next']->slug; ?>">Next <i class="fa fa-chevron-right"></i></a>
                <?php } ?>
            </div>
        </div>        
    </div>
</div>
<?php } ?>

<div class="container" itemscope itemtype="http://schema.org/Product">
    <div class="row">
        <?php if (!empty($images)) { ?> 
        <div class="col-sm-6">
            <?php if ($item->{'featured_image.slug'}) { ?>
            <div class="product-image product-image-big">
   	            <img itemprop="image" id="product-image" alt="Close up of the <?php echo $item->{'title'}; ?>" class="imagezoom img-responsive" src="./asset/<?php echo $item->{'featured_image.slug'}; ?>" title="<?php echo htmlspecialchars_decode( $item->title ); ?>" data-big-image-src="./asset/<?php echo $item->{'featured_image.slug'}; ?>" />
            </div>
            <?php } ?>

            <?php if (count($images) > 1) { ?>
                <div class="product-image-container">
                    <div class="owl-nav-container hidden-xs">
                        <a href="javascript:void(0);" class="btn btn-link custom-button owl-nav prev" data-target="#product-image-carousel">
                            <i class="glyphicon glyphicon-chevron-left"></i>
                        </a>
                        <a href="javascript:void(0);" class="btn btn-link custom-button owl-nav next" data-target="#product-image-carousel">
                             <i class="glyphicon glyphicon-chevron-right"></i>
                        </a>
                    </div>
                    <script>
                	ShopAfterAction = function(){
                    	if (this.owl.owlItems.length <= this.owl.visibleItems.length) {
                        	jQuery('.owl-nav').hide();
                        } else {
                        	jQuery('.owl-nav').show();
                        }
                    }
                    </script>                
                    <div id="product-image-carousel" class="owl-carousel" data-lazy-load="true" data-scroll-per-page="true" data-items-tablet="[600,3]" data-items-mobile="false" data-after-action="ShopAfterAction">
                        <?php foreach ($images as $key=>$image) { ?>
                        <div id="<?php echo $image; ?>" class="slide">
                            <a href="javascript:void(0);" class="imagezoom-thumb btn btn-link" data-target="#product-image">
                                 <img itemprop="image" class="img-responsive lazyOwl" data-src="./asset/<?php echo $image; ?>" src="./asset/<?php echo $image; ?>" title="<?php echo htmlspecialchars_decode( $item->title ); ?>" data-big-image-src="./asset/<?php echo $image; ?>">
                            </a>                        
                        </div>
                        <?php } ?>
                    </div>

                </div>
            <?php } ?>
            
        </div>
        <?php } ?>
        
        <div class="col-sm-6">
            <div class="product-details">
                <h1 itemprop="name"><?php echo $item->{'title'}; ?></h1>
				<hr />
                <?php if ($item->{'tracking.sku'}) { ?>
                <div class="details">
                    <span class="detail-line"><strong>Product Code:</strong> <?php echo $item->{'tracking.sku'}; ?></span>
                </div>
                <?php } ?>
                
                <?php if ($item->{'copy'}) { ?>
                <div class="description">
                    <span itemprop="description"><?php echo $item->{'copy'}; ?></span>
                </div>
                <?php } ?>
                
                <?php // TODO Push this into a function?
                $measurements = $item->exists('amrita_measurements') ? $item->{'amrita_measurements'} : $item->{'amrita.measurements'}; 
                if ($measurements) { ?>
                <div class="amrita-measurements details">
                    <span class="detail-line"><strong>Measurements:</strong> <?php echo $measurements; ?></span>
                </div>
                <?php } ?>
                
                <?php if (in_array('finalsale', $item->tags)) { ?>
                <tmpl type="modules" name="product-finalsale" />
                <?php } ?>
                
                <?php if (in_array('featuredsale', $item->tags)) { ?>
                <tmpl type="modules" name="product-featuredsale" /> 
                <?php } ?>
                
                <?php if (in_array('bargain', $item->tags)) { ?>
                <tmpl type="modules" name="product-bargain" /> 
                <?php } ?>
                
                <tmpl type="modules" name="product-livingsocial" />
                
                <?php if ($item->{'policies.hide_price'}) { ?>
                    <div class="price"><p>Call for price.</p></div>
                <?php } else { ?>
                    
                    <form action="./shop/cart/add" method="post" class="add-to-cart-form">
                        <div id="validation-cart-add" class="validation-message"></div>
                        
                        <div class="buttons">
                            <div class="row">
                                <?php if (!empty($item->variantsInStock()) && count($item->variantsInStock()) > 1) { ?>
                                <div class="col-sm-8">
                                    
                                    <select name="variant_id" class="chosen-select select-variant variant_id" data-callback="Shop.selectVariant">
                                        <?php foreach ($variantsInStock as $key=>$variant) {
                                        	$wishlist_state = \Shop\Models\Wishlists::hasAddedVariant($variant['id'], (string) $this->auth->getIdentity()->id) ? '0' : '1';
                                        	?>
                                            <option value="<?php echo $variant['id']; ?>" data-variant='<?php echo htmlspecialchars( json_encode( array(
                                                'id' => $variant['id'],
                                                'key' => $variant['key'],
                                            	'image' => $variant['image'],
                                                'quantity' => $variant['quantity'],
                                                'price' => \Shop\Models\Currency::format( $item->price( $variant['id'] ) ),
                                            ) ) ); ?>'
                                            	data-wishlist="<?php echo $wishlist_state; ?>"><?php echo $variant['attribute_title'] ? $variant['attribute_title'] : $item->title; ?> </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <?php } elseif (count($item->variantsInStock()) == 1) { ?>
                                    <div class="col-sm-8">
                                        <?php $variant = $item->variantsInStock()[0]; ?>
                                        <div class="input-group">
                                            <span class="form-control"><?php echo $variant['attribute_title'] ? $variant['attribute_title'] : $item->title; ?></span>
                                            <span class="input-group-addon">
                                                <i class="fa fa-chevron-right"></i>
                                            </span>
                                        </div>
                                        <input type="hidden" id="variant-id" name="variant_id" value="<?php echo $variant['id']; ?>" class="variant_id" data-variant='<?php echo htmlspecialchars( json_encode( array(
                                                    'id' => $variant['id'],
                                                    'key' => $variant['key'],
                                                	'image' => $variant['image'],
                                                    'quantity' => $variant['quantity'],
                                                    'price' => \Shop\Models\Currency::format( $item->price( $variant['id'] ) ),
                                                ) ) ); ?>' />
                                                
                                        <script>
                                        jQuery(document).ready(function(){
                                    		var selected = jQuery("#variant-id");
                                            var variant = jQuery.parseJSON( selected.attr('data-variant') );
                                            if (variant.image) {
                                            	Shop.selectVariant(variant);
                                            }
                                        });
                                        </script>             
                                    </div>                               
                                <?php } ?> 
                            
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="1" placeholder="Quantity" name="quantity" id="quantity" />
                                        <span class="input-group-btn">
                                            <button onclick="jQuery('#quantity').val(parseInt(jQuery('#quantity').val())+1);" class="btn btn-default" type="button"><i class="glyphicon glyphicon-plus"></i></button>
                                        </span>                                    
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="price-line">
                            <?php if (((int) $item->get('prices.list') > 0) && (float) $item->get('prices.list') != (float) $item->price() ) { ?>
                                <span class="list-price"><strike><?php echo \Shop\Models\Currency::format( $item->{'prices.list'} ); ?></strike></span>
                            <?php } ?>
                            &nbsp;
                            <div itemprop="offers" itemscope itemtype="http://schema.org/Offer" class="price product-price">
                                <span itemprop="price"><?php echo \Shop\Models\Currency::format( $item->price() ); ?></span>
                            </div>
                            <button class="btn btn-default custom-button custom-button-inverted" data-button="add-to-bag">Add to bag</button>
                            <?php \Dsc\System::instance()->get('session')->set('shop.add_to_cart.product.redirect', '/shop/product/' . $item->slug ); ?>
                            
                            <div class="small-buttons">
                                <div class="add-to-wishlist-container">
                                </div>
                            </div>
    
                        </div>
                    </form>
                    
                <?php } ?>
                
                <hr />
                
                <div class="social-share">
                
                    <span>
                        <a href="//www.pinterest.com/pin/create/button/" data-pin-do="buttonBookmark" ><img src="//assets.pinterest.com/images/pidgets/pinit_fg_en_rect_gray_20.png" /></a>
                    </span>
                    
    				<span>
    				    <div class="fb-like" data-layout="button_count" data-action="like" data-show-faces="false" data-share="true"></div>
    				</span>
    				
    				<span>
    				    <a class="wanelo-save-button"
        				  href="//wanelo.com/"
        				  data-url=""
        				  data-title=""
        				  data-image=""
        				  data-price=""></a>
    				</span>
    				  
                    <?php /* TODO Make this use https ?><script src="http://platform.tumblr.com/v1/share.js"></script> */ ?>
                    <?php /* ?>
                    <span>				  
                    <a href="http://www.tumblr.com/share" title="Share on Tumblr" style="display:inline-block; text-indent:-9999px; overflow:hidden; width:61px; height:20px; background:url('http://platform.tumblr.com/v1/share_2.png') top left no-repeat transparent;">Share on Tumblr</a>
                    </span>
                    */ ?>
    				  
                    <span>
    				    <a href="https://twitter.com/share" class="twitter-share-button" data-via="amritasjewelry" data-hashtags="amritasingh">Tweet</a>
                        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
    				</span>
				  
				</div>
				
				<hr/>
				
            </div>
        </div>
    </div>
    
    <?php if ($related_products = $item->relatedProducts()) { ?>
    <hr />
    <div>
    <h3 class="related-header">You might also like</h3>
    <?php $n=0; $count = count($related_products); ?>
    
    <?php 
    	foreach ($related_products as $product ) {
			$image = (!empty($product->{'featured_image.slug'})) ? './asset/thumb/' . $product->{'featured_image.slug'} : null;
			$url = './shop/product/' . $product->slug;
			$js = '';
			if( $is_kissmetrics ){
				$js ="\" onclick=\"javascript:_kmq.push(['record', 'Product Related Items', {'Product Name' : '".$product->title."', 'SKU' : '".$product->{'tracking.sku'}."' }])";
			
				$url .= $js;
				$image .= $js;
			}
        
        	if ($n == 0 || ($n % 6 == 0)) { ?><div class="row"><?php } ?>
        
        <div class="col-xs-6 col-sm-2 col-md-2 category-article category-grid text-center related-product-shop" >
            
            <div class="related-product-body" itemprop="isRelatedTo" itemscope itemtype="http://schema.org/Product">
                <?php if ($image) { ?>
                <a itemprop="url" href="<?php echo $url; ?>">
                    <img itemprop="image" class="img-responsive" src="<?php echo $image ?>">
                </a>
                <?php } ?>
                <div class="title-line">
                	<h4 itemprop="name"><?php echo $product->title; ?></h4>                
                </div>
            </div>
            <div class="price-line">
                <?php if ($product->{'policies.hide_price'}) { ?>
                    <div class="price"><p>Call for price.</p></div>
                <?php } else { ?>                                                
                    <?php if (((int) $product->get('prices.list') > 0) && $product->get('prices.list') != $product->price() ) { ?>
                        <span class="list-price"><strike><?php echo \Shop\Models\Currency::format( $product->{'prices.list'} ); ?></strike></span>
                    <?php } ?>
                    <div class="product-price" >
                        <?php echo \Shop\Models\Currency::format( $product->price() ); ?>
                    </div>
                <?php } ?>
            </div>                        
   
        </div>
        
        <?php $n++; if (($n % 6 == 0) || $n==$count) { ?></div> <?php if ($n!=$count) { ?><hr/><?php } ?><?php } ?>
    <?php } ?>
    </div>
    <?php } ?>

    <?php if ($related_pages = $item->relatedPages()) { ?>
    <hr />
    <div>
    <h3 class="related-header">Press and Customer Images</h3>
    <?php $n=0; $count = count($related_pages); ?>
    
    <?php 
    	foreach ($related_pages as $page) { ?>
        
        <?php if ($n == 0 || ($n % 6 == 0)) { ?><div class="row"><?php } ?>
        
        <div class="col-xs-6 col-sm-2 col-md-2 category-article category-grid text-center">
            
            <div>
                <a href="./pages/<?php echo $page->slug; ?>">
            		
            		<?php if ($page->{'featured_image.slug'} ) { ?>
                    	<img class="img-responsive" alt="<?php echo $page->title; ?>" src="./asset/thumb/<?php echo $page->{'featured_image.slug'}; ?>" />
                	<?php } ?>
                </a>
            </div>
        </div>
        
        <?php $n++; if (($n % 6 == 0) || $n==$count) { ?></div> <?php if ($n!=$count) { ?><hr/><?php } ?><?php } ?>
    <?php } ?>
    </div>
    <?php } ?>

    <?php if ($related_posts = $item->relatedPosts()) { ?>
    <hr />
    <div>
    <h3 class="related-header">Related Blog Posts</h3>
    <?php $n=0; $count = count($related_posts); ?>
    
    <?php 
    	foreach ($related_posts as $post) { ?>
        
        <?php if ($n == 0 || ($n % 6 == 0)) { ?><div class="row"><?php } ?>
        
        <div class="col-xs-6 col-sm-2 col-md-2 category-article category-grid text-center">
            
            <div>
                <a href="./blog/post/<?php echo $post->slug; ?>">
            		<h4><?php echo $post->title; ?></h4>
            		<?php if ($post->{'featured_image.slug'} ) { ?>
                    	<img class="img-responsive" alt="<?php echo $post->title; ?>" src="./asset/thumb/<?php echo $post->{'featured_image.slug'}; ?>" />
                	<?php } ?>
                </a>
            </div>
        </div>
        
        <?php $n++; if (($n % 6 == 0) || $n==$count) { ?></div> <?php if ($n!=$count) { ?><hr/><?php } ?><?php } ?>
    <?php } ?>
    </div>
    <?php } ?>    
    
	<!-- Please call pinit.js only once per page -->
	<script type="text/javascript" async src="//assets.pinterest.com/js/pinit.js"></script>
	<script async="true" type="text/javascript" src="//cdn-saveit.wanelo.com/bookmarklet/3/save.js"></script>
	<script>(function(d, s, id) {
		  var js, fjs = d.getElementsByTagName(s)[0];
		  if (d.getElementById(id)) return;
		  js = d.createElement(s); js.id = id;
		  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&appId=248248578710949&version=v2.0";
		  fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));</script>
					
    <?php $settings = \Shop\Models\Settings::fetch(); ?>
    <?php if ($settings->{'reviews.enabled'}) { ?>
        <?php echo $this->renderView('Shop/Site/Views::product/fragment_reviews.php'); ?>
    <?php } ?>
    					
</div>

<style type="text/css">
.add-to-cart-dialog .cart-lightbox-body {
max-height: 300px;
overflow-y: scroll;
overflow-x: hidden;
}

.add-to-cart-dialog .cart-item {
height: 100px;
border-bottom: 1px solid #eee;
padding-top: 15px;
}

.add-to-cart-dialog .cart-item:last-child {
border-bottom: none;
}

.add-to-cart-dialog .cart-item-image {
padding-left: 20px;
}

.add-to-cart-dialog .cart-lightbox-footer {
padding: 5px 20px;
border-top: 1px solid #eee;
}

.add-to-cart-dialog .modal-footer {
margin-top: 0px;
}

.add-to-cart-dialog .modal-body {
padding: 0px;
}

@media (max-width: 480px) {
    .add-to-cart-dialog .cart-lightbox-body {
    max-height: 200px;
    }
    
    .add-to-cart-dialog .cart-item-image {
    padding-left: 0px;
    }    
}
</style>

<script>
jQuery(document).ready(function(){
    jQuery('.add-to-cart-form').on('submit', function(ev){
        ev.preventDefault();
        var form = jQuery(this);
        var message_container = form.attr('data-message_container');

        if (message_container) {
            message_container.html('');
        }            
        
        var form_data = new Array();
        $.merge( form_data, form.serializeArray() );
        var url = form.attr('action');

        var request = $.ajax({
            type: 'post', 
            url: url,
            data: form_data
        }).done(function(data){
			
            var r = $.parseJSON( JSON.stringify(data), false);
            if (r.message) {
                message_container.html(r.message);
            }
            if (r.redirect) {
                window.location = r.redirect;
            }
			var request1 = jQuery.ajax({
				type: 'POST', 
				url: './shop/cart/get_mini_cart_content'
				}).done(function(data){
					
					jQuery("#mini_cart_content").html(data);
					
					var request2 = jQuery.ajax({
						type: 'POST', 
						url: './shop/cart/get_mini_cart_count'
						}).done(function(data){
							jQuery("#jq_item_count").html(data);
						});
					
					if (!r.error) {
						if (form.attr('data-callback')) {
							callback = form.attr('data-callback');
							Dsc.executeFunctionByName(callback, window, r);
						}

						bootbox.dialog({
							title: 'Item added to cart',
							className: 'add-to-cart-dialog',
							message: r.html,
							buttons: {
								success: {
									label: 'Continue Shopping',
									className: 'btn-default btn-sm',                            
								},
								main: {
									label: 'Checkout Now',
									className: 'btn-primary',
									callback: function() {
										window.location = './shop/checkout'
									}
								}
							}
						});                
					}
				});
			
			
			
			
            
            
        }).fail(function(data){

        }).always(function(data){

        });
    });
});
</script>