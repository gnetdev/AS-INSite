<?php if (!empty($paginated->items)) { ?>
    <?php 
    $tags = array();
    if ($state = \Base::instance()->get( 'state' )) {
        $tags = (array) $state->get( 'filter.vtags' );
    }
    ?>
    <?php $n=0; $count = count($paginated->items); ?>

    <?php foreach ($paginated->items as $position=>$item) { ?>
        <?php $item->url = './shop/product/' . $item->{'slug'}; ?>
        
        <?php if ($n == 0 || ($n % 3 == 0)) { ?><div class="row"><?php } ?>
        
        <article class="category-article category-grid col-sm-4">
            <figure>
                <?php if ($item->{'display.stickers'}) { foreach ($item->{'display.stickers'} as $sticker) { ?>
                <div class="corner-sign corner-<?php echo \Web::instance()->slug( $sticker ); ?>"><?php echo $sticker; ?></div>
                <?php } } ?>          
                  
                <?php if ($image = $item->image($tags)) { ?>
                <img id="img-<?php echo $item->id; ?>" src="./asset/thumb/<?php echo $image; ?>" alt="" class="img-responsive" />
                <?php } ?>
                
                <a href="<?php echo $item->url; ?>">
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
                            
                <h2><a href="<?php echo $item->url; ?>"><?php echo $item->title; ?></a></h2>
                
                <div class="price">
                    <?php if ($item->{'policies.hide_price'}) { ?>
                        <p>Call for price.</p>
                    <?php } else { ?>
                        <?php if (((int) $item->get('prices.list') > 0) && (float) $item->get('prices.list') != (float) $item->price() ) { ?>
                            <span class="list-price"><strike><?php echo \Shop\Models\Currency::format( $item->{'prices.list'} ); ?></strike></span>
                            &nbsp;
                        <?php } ?>                                        
                        <a href="<?php echo $item->url; ?>">
                            <span class="new-price"><?php echo \Shop\Models\Currency::format( $item->price() ); ?></span>
                        </a>
                    <?php } ?>
                </div>
            </div>
            <?php if (count($item->variantsInStockWithImages(true)) > 1) { ?>
                <ul class="list-unstyled list-inline">
                <?php foreach ($item->variantsInStockWithImages(true) as $variant) { ?>
                    <li class="col-xs-2 col-sm-3 col-md-2">
                        <a href="javascript:void(0);" onclick="AmritaSingh.imageSwap('./asset/thumb/<?php echo $variant['image']; ?>', '#img-<?php echo $item->id; ?>');" data-target="img-<?php echo $item->id; ?>">
                            <img src="./asset/thumb/<?php echo $variant['image']; ?>" alt="" class="img-responsive" />
                        </a>
                    </li>
                <?php } ?>
                </ul>
            <?php } ?>            
        </article>
        
        <?php $n++; if (($n % 3 == 0) || $n==$count) { ?></div><?php } ?>
    
    <?php } // end foreach ?>
    
<?php } else { ?>
    
<?php } ?>
