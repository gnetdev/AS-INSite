<?php
$settings_admin = \Admin\Models\Settings::fetch();
$is_kissmetrics = $settings_admin->enabledIntegration( 'kissmetrics' );
?>

<div class="container">
    <ol class="breadcrumb">
        <li>
            <a href="./pages/category/press">Press</a>
        </li>
        <li class="active"><?php echo $item->title; ?></li>
    </ol>
</div>

<?php $images = $item->images(); ?>

<div class="container">
    <div class="row">
        <?php if (!empty($images)) { ?> 
        <div class="col-sm-6 col-md-6">
            <?php if ($item->{'featured_image.slug'}) { ?>
            <div class="product-image product-image-big hidden-xs">
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
                
                    <div id="product-image-carousel" class="owl-carousel" data-lazy-load="true" data-scroll-per-page="true">
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
        
        <div class="col-sm-6 col-md-6">
            <div class="">
                <h2><?php echo $item->{'title'}; ?></h2>
                
                <?php if ($item->{'copy'}) { ?>
                <div class="description">
                    <?php echo $item->{'copy'}; ?>
                </div>
                <?php } ?>
            </div>
            
            <hr/>
            
            <?php if ($related_products = (array) $item->{'shop.products'}) { ?>
                <div class="margin-top widget-related-products-pages">
                <h3>Related Products</h3>
                <?php 
                	$n=0; $count = count($related_products);
                	foreach ($related_products as $product_id) {
                    	$product = (new \Shop\Models\Products)->setState('filter.id', $product_id)->getItem();
                       	$image = (!empty($product->{'featured_image.slug'})) ? './asset/thumb/' . $product->{'featured_image.slug'} : null;
                       	$url = './shop/product/' . $product->slug;
                       	$js = '';
                       	if( $is_kissmetrics ){
							$js ="\" onclick=\"javascript:_kmq.push(['record', 'Page Related Items', {'Product Name' : '".$product->title."', 'SKU' : '".$product->{'tracking.sku'}."' }])";
							
							$url .= $js;
							$image .= $js;
						}

						if (empty($url) || !$product->isAvailable()) { continue; }
                    
                    	if ($n == 0 || ($n % 4 == 0)) { ?><div class="row"><?php } ?>
                    
                    <div class="col-xs-6 col-sm-3 col-md-3 category-article category-grid text-center related-product-pages">
                        
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
                                    <span class="list-price price"><strike><?php echo \Shop\Models\Currency::format( $product->{'prices.list'} ); ?></strike></span>
                                <?php } ?>
                                <div class="price">
                                    <?php echo \Shop\Models\Currency::format( $product->price() ); ?>
                                </div>
                            <?php } ?>
                        </div>                        
               
                    </div>
                    
                    <?php $n++; if (($n % 4 == 0) || $n==$count) { ?></div> <hr/><?php } ?>
                <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>