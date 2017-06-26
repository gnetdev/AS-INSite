<?php 
$settings = \Admin\Models\Settings::fetch();
$is_kissmetrics = $settings->enabledIntegration( 'kissmetrics' );
$term = \Base::instance()->get('q');
?>

<div class="main-bottom">
    <div class="half text-left">
        <div class="page-counter pull-left">
            <div class="type-selector">
                <span class="pagination">
                    <?php echo (!empty($paginated->total_pages)) ? $paginated->getResultsCounter() : null; ?>
                </span>
            </div>
        </div>
    </div>
    <div class="half text-right">
        <?php if (!empty($paginated->total_pages) && $paginated->total_pages > 1) { ?>
            <?php echo $paginated->serve(); ?>
        <?php } ?>
    </div>
</div>            

<?php $n=0; $count = count($paginated->items); ?>
<?php foreach ($paginated->items as $position=>$model_item) { ?>
    <?php if ($item = $model_item->toSearchItem()) { ?>
    <?php if ($n == 0 || ($n % 4 == 0)) { ?><div class="row"><?php } ?>
    <?php
    $onclick = null;
    if ( $is_kissmetrics )
    {
        $onclick = "onclick=\"javascript:_kmq.push(['record', 'Viewed Product via Search', {'Product Name' : '".$model_item->title."', 'SKU' : '". $model_item->{'tracking.sku'}."', 'Search Term' : '".$term."' }])\"";
    }
    ?>        
    <div class="col-xs-6 col-sm-3 col-md-3 category-article category-grid text-center">
        
        <?php if ($model_item->{'display.stickers'}) { foreach ($model_item->{'display.stickers'} as $sticker) { ?>
        <div class="corner-sign corner-<?php echo \Web::instance()->slug( $sticker ); ?>"><?php echo $sticker; ?></div>
        <?php } } ?>
        
        <div class="">
            <?php if ($item->image) { ?>
            <a href="<?php echo $item->url; ?>" <?php echo $onclick; ?>>
                <img src="<?php echo $item->image; ?>" class="img-responsive" />
            </a>
            <?php } ?>
        </div>
        <div class="">
            <a href="<?php echo $item->url; ?>" <?php echo $onclick; ?>>
                <h3><?php echo $item->title; ?></h3>
                <?php if (!empty($item->subtitle)) { ?>
                <h4><?php echo $item->subtitle; ?></h4>
                <?php } ?>
                <div><?php echo $item->summary; ?></div>
            </a>
        </div>

        <?php if ($model_item->{'policies.hide_price'}) { ?>
            <div class="price"><p>Call for price.</p></div>
        <?php } else { ?>
            <div class="price">        
                <?php if (((int) $item->get('prices.list') > 0) && $item->get('prices.list') != $item->price ) { ?>
                    <span class="list-price"><strike><?php echo \Shop\Models\Currency::format( $item->get('prices.list') ); ?></strike></span>
                <?php } ?>
                &nbsp;                
                <a href="<?php echo $item->url; ?>">
                    <span class="new-price"><?php echo \Shop\Models\Currency::format( $model_item->price() ); ?></span>
                </a>
            </div>  
        <?php } ?>

    </div>
         
    <?php $n++; if (($n % 4 == 0) || $n==$count) { ?></div> <hr/><?php } ?>         
    <?php }?>
<?php } ?>

<div class="main-bottom">
    <div class="half text-left">
        <div class="page-counter pull-left">
            <span class="pagination">
                <?php echo (!empty($paginated->total_pages)) ? $paginated->getResultsCounter() : null; ?>
            </span>             
        </div>
    </div>
    <div class="half text-right">
        <?php if (!empty($paginated->total_pages) && $paginated->total_pages > 1) { ?>
            <?php echo $paginated->serve(); ?>
        <?php } ?>
    </div>
</div>