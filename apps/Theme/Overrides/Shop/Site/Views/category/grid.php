<?php if (!empty($paginated->items)) { ?>
    <?php 
    $tags = array();
    if ($state = \Base::instance()->get( 'state' )) {
        $tags = (array) $state->get( 'filter.vtags' );
    }
    ?>
    <div class="main-bottom">
        <div class="half text-left">
            <div class="page-counter pull-left">
                <div class="type-selector">
                    <span class="pagination">
                        <?php echo (!empty($paginated->total_pages)) ? $paginated->getResultsCounter() : null; ?>
                        <a href="./shop/category<?php echo $category->path; ?>/view-all" class="btn btn-default btn-sm custom-button">View all</a>
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

    <?php foreach ($paginated->items as $position=>$item) { ?>
        <?php $item->url = './shop/product/' . $item->{'slug'}; ?>
        
        <?php if ($n == 0 || ($n % 3 == 0)) { ?><div class="row"><?php } ?>
        
        <article itemscope itemtype="http://schema.org/Product" class="category-article category-grid col-sm-4">
            <figure>
                <?php if ($item->{'display.stickers'}) { foreach ($item->{'display.stickers'} as $sticker) { ?>
                <div class="corner-sign corner-<?php echo \Web::instance()->slug( $sticker ); ?>"><?php echo $sticker; ?></div>
                <?php } } ?>
                
                <?php if ($image = $item->image($tags)) { ?>
                <img itemprop="image" id="img-<?php echo $item->id; ?>" src="./asset/thumb/<?php echo $image; ?>" alt="" class="img-responsive" />
                <?php } ?>                
                
                <a itemprop="url" href="<?php echo $item->url; ?>">
                <div class="figure-overlay">
                    <p>&nbsp;</p>
                    <?php // if rating enabled and rating exists, display 
                    /*
                    <div class="rating-line">
                        <div class="stars-white" data-number="5" data-score="4"></div>
                    </div>
                    */ ?>
                    <?php if ($item->{'description'}) { ?>
                    <div class="excerpt">
                        <?php echo $item->{'description'}; ?>
                    </div>
                    <?php } ?>
                    
                    <button class="btn btn-default custom-button">
                        View
                    </button>
                    
                    <?php /* ?>
                    <a href="#"><span class="wrap-icon"><i class='glyphicon glyphicon-heart'></i></span></a>
                    <a href="#"><span class="wrap-icon"><i class='glyphicon glyphicon-ok'></i></span></a>
                    */ ?>
                </div>
                </a>
            </figure>
            <div class="text">
                <div class="sku"><a href="<?php echo $item->url; ?>"><?php echo $item->{'tracking.sku'}; ?></a></div>
                
                <h2 itemprop="name"><a href="<?php echo $item->url; ?>"><?php echo $item->title; ?></a></h2>
                
                <div itemprop="offers" itemscope itemtype="http://schema.org/Offer" class="price">
                    <?php if ($item->{'policies.hide_price'}) { ?>
                        <p>Call for price.</p>
                    <?php } else { ?>
                        <?php if (((int) $item->get('prices.list') > 0) && (float) $item->get('prices.list') != (float) $item->price() ) { ?>
                            <span class="list-price"><strike><?php echo \Shop\Models\Currency::format( $item->{'prices.list'} ); ?></strike></span>
                            &nbsp;
                        <?php } ?>
                        <a href="<?php echo $item->url; ?>">
                            <span itemprop="price" class="new-price"><?php echo \Shop\Models\Currency::format( $item->price() ); ?></span>
                        </a>
                    <?php } ?>
                </div>
            </div>
            <?php if (count($item->variantsInStockWithImages(true)) > 1) { ?>
                <ul class="list-unstyled list-inline">
                <?php foreach ($item->variantsInStockWithImages(true) as $variant) { ?>
                    <li class="col-xs-2 col-sm-3 col-md-2">
                        <a href="javascript:void(0);" onclick="AmritaSingh.imageSwap('./asset/thumb/<?php echo $variant['image']; ?>', '#img-<?php echo $item->id; ?>');" data-target="img-<?php echo $item->id; ?>">
                            <img itemprop="image" src="./asset/thumb/<?php echo $variant['image']; ?>" alt="" class="img-responsive" />
                        </a>
                    </li>
                <?php } ?>
                </ul>
            <?php } ?>
        </article>
        
        <?php $n++; if (($n % 3 == 0) || $n==$count) { ?></div><?php } ?>
    
    <?php } // end foreach ?>
    
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

<?php } else { ?>
    
    <p>No items found.</p>
    
<?php } ?>
