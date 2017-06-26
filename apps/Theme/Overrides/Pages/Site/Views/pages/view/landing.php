<?php
$settings_admin = \Admin\Models\Settings::fetch();
$is_kissmetrics = $settings_admin->enabledIntegration( 'kissmetrics' );
?>

<article id="page-<?php echo $item->id; ?>" class="page-<?php echo $item->id; ?>">
    <div class="entry-header">
        <h2 class="entry-title">
            <?php echo $item->{'title'}; ?>
        </h2>
    </div>

    <div class="entry-description">
        <?php echo $item->{'copy'}; ?>
    </div>
    
    <?php if ($related_products = (array) $item->{'shop.products'}) { ?>
                <div class="margin-top widget-related-products-pages">
                <h3 style="text-align:center; clear:both;">Featured Items</h3>
                <?php 
                	$n=0; $count = count($related_products);
                	foreach ($related_products as $product_id) {
                    	$product = (new \Shop\Models\Products)->setState('filter.id', $product_id)->getItem();
                       	$image = (!empty($product->{'featured_image.slug'})) ? './asset/thumb/' . $product->{'featured_image.slug'} : null;
                       	$url = './shop/product/' . $product->slug;
                       	$js = '';
                       	if( $is_kissmetrics ){
							$js ="\" onclick=\"javascript:_kmq.push(['record', 'Landign Page Related Items', {'Product Name' : '".$product->title."', 'SKU' : '".$product->{'tracking.sku'}."' }])";
							
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
			<p><a href="/pages/category/press" target="_blank"><img class="img-responsive" src="/asset/2014-08-20-3imagesinsert-2-jpg" /></a></p>
</article>