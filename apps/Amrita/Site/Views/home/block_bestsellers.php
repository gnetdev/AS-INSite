<?php 
$tag = $settings->{'site_home.3_products.group_1.tag'};
$limit = $settings->{'site_home.3_products.group_1.images_count'} ? $settings->{'site_home.3_products.group_1.images_count'} : 12;
if ($tag) {
    $this->__items = (new \Shop\Models\Products)
    ->setState( 'filter.tag', $tag )
    ->setState( 'list.limit', $limit )
    ->setState( 'filter.published_today', true )
    ->setState( 'filter.publication_status', 'published' )
    ->getItemsRandom();
    
    if ($this->__items) { ?>
        <div class="col-sm-12">
            <div class="section-title">
                <h1><?php echo $settings->{'site_home.3_products.group_1.label'}; ?></h1>
            </div>
        </div>                
        <div class="" id="<?php echo 'tab_3'; ?>">
            <?php echo $this->renderView('Amrita/Site/Views::home/product_slider_3.php'); ?>
        </div>
    	
    <?php }
}
?>