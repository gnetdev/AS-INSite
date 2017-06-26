<?php
	$item->url = './shop/product/' . $item->{'slug'};
	$images = $item->images();
	$variantsInStock = $item->variantsInStock();
	$variant_1 = current( $variantsInStock );
	$wishlist_state = \Shop\Models\Wishlists::hasAddedVariant($variant_1['id'], (string) $this->auth->getIdentity()->id) ? 'false' : 'true';
 
	$settings = \Admin\Models\Settings::fetch();
	$is_kissmetrics = $settings->enabledIntegration( 'kissmetrics' );
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

<div class="container">
    <div class="row">
        <?php if (!empty($images)) { ?> 
        <div class="col-sm-6">
            <?php if ($item->{'featured_image.slug'}) { ?>
            <div class="product-image product-image-big">
   	            <img id="product-image" class="imagezoom img-responsive" src="./asset/<?php echo $item->{'featured_image.slug'}; ?>" title="<?php echo htmlspecialchars_decode( $item->title ); ?>" data-big-image-src="./asset/<?php echo $item->{'featured_image.slug'}; ?>" />
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
                                 <img class="img-responsive lazyOwl" data-src="./asset/<?php echo $image; ?>" src="./asset/<?php echo $image; ?>" title="<?php echo htmlspecialchars_decode( $item->title ); ?>" data-big-image-src="./asset/<?php echo $image; ?>">
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
                <h1><?php echo $item->{'title'}; ?></h1>

                <hr />
                <?php if ($item->{'tracking.sku'}) { ?>
                <div class="details">
                    <span class="detail-line"><strong>Product Code:</strong> <?php echo $item->{'tracking.sku'}; ?></span>
                </div>
                <?php } ?>
                
                <?php if ($item->{'copy'}) { ?>
                <div class="description">
                    <?php echo $item->{'copy'}; ?>
                </div>
                <?php } ?>
                
                <?php if ($item->{'policies.hide_price'}) { ?>
                    <div class="price"><p>Call for price.</p></div>
                <?php } else { ?>
                                
                    <form action="./shop/cart/add" method="post">
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
                                    <input type="hidden" name="variant_id" value="<?php echo $item->variantsInStock()[0]['id']; ?>" class="variant_id" />
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
                            <div class="price product-price">
                                <?php echo \Shop\Models\Currency::format( $item->price() ); ?>
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
            <?php if ($related_products = $item->relatedProducts()) { ?>
                <div class="margin-top">
                <h3>Related Products</h3>
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
                    
                    	if ($n == 0 || ($n % 4 == 0)) { ?><div class="row"><?php } ?>
                    
                    <div class="col-xs-6 col-sm-3 col-md-3 category-article category-grid text-center related-product-shop">
                        
                        <div class="related-product-body">
                            <?php if ($image) { ?>
                            <a href="<?php echo $url; ?>">
                                <img class="img-responsive" src="<?php echo $image ?>">
                            </a>
                            <?php } ?>
                            <div class="title-line">
                            	<h4><?php echo $product->title; ?></h4>                
                            </div>
                        </div>
                        <div class="price-line">
                            <?php if ($product->{'policies.hide_price'}) { ?>
                                <div class="price"><p>Call for price.</p></div>
                            <?php } else { ?>                        
                                <?php if (((int) $product->get('prices.list') > 0) && $product->get('prices.list') != $product->price() ) { ?>
                                    <span class="list-price"><strike><?php echo \Shop\Models\Currency::format( $product->{'prices.list'} ); ?></strike></span>
                                <?php } ?>
                                <div class="product-price">
                                    <?php echo \Shop\Models\Currency::format( $product->price() ); ?>
                                </div>
                            <?php } ?>
                        </div>                        
               
                    </div>
                    
                    <?php $n++; if (($n % 4 == 0) || $n==$count) { ?></div> <hr/><?php } ?>
                <?php } ?>
                </div>
            <?php } ?>
            
            <?php if ($related_pages = $item->relatedPages()) { ?>
                <div class="margin-top">
                <h3>Related Pages</h3>
                <?php $n=0; $count = count($related_pages); ?>
                
                <?php 
                	foreach ($related_pages as $page) { ?>
                    
                    <?php if ($n == 0 || ($n % 4 == 0)) { ?><div class="row"><?php } ?>
                    
                    <div class="col-xs-6 col-sm-3 col-md-3 category-article category-grid text-center">
                        
                        <div>
                            <a href="./pages/<?php echo $page->slug; ?>">
                        		<h4><?php echo $page->title; ?></h4>
                        		<?php if ($page->{'featured_image.slug'} ) { ?>
                                	<img class="img-responsive" alt="<?php echo $page->title; ?>" src="./asset/thumb/<?php echo $page->{'featured_image.slug'}; ?>" />
                            	<?php } ?>
                            </a>
                        </div>
                    </div>
                    
                    <?php $n++; if (($n % 4 == 0) || $n==$count) { ?></div> <hr/><?php } ?>
                <?php } ?>
                </div>
            <?php } ?>
            
            <?php if ($related_posts = $item->relatedPosts()) { ?>
                <div class="margin-top">
                <h3>Related Blog Posts</h3>
                <?php $n=0; $count = count($related_posts); ?>
                
                <?php 
                	foreach ($related_posts as $post) { ?>
                    
                    <?php if ($n == 0 || ($n % 4 == 0)) { ?><div class="row"><?php } ?>
                    
                    <div class="col-xs-6 col-sm-3 col-md-3 category-article category-grid text-center">
                        
                        <div>
                            <a href="./blog/post/<?php echo $post->slug; ?>">
                        		<h4><?php echo $post->title; ?></h4>
                        		<?php if ($post->{'featured_image.slug'} ) { ?>
                                	<img class="img-responsive" alt="<?php echo $post->title; ?>" src="./asset/thumb/<?php echo $post->{'featured_image.slug'}; ?>" />
                            	<?php } ?>
                            </a>
                        </div>
                    </div>
                    
                    <?php $n++; if (($n % 4 == 0) || $n==$count) { ?></div> <hr/><?php } ?>
                <?php } ?>
                </div>
            <?php } ?>
            
            </div>
        </div>
    </div>

    <?php $settings = \Shop\Models\Settings::fetch(); ?>
    <?php if ($settings->{'reviews.enabled'}) { ?>
        <?php echo $this->renderView('Shop/Site/Views::product/fragment_reviews.php'); ?>
    <?php } ?> 
    
</div>
