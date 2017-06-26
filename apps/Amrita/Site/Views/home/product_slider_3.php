<div class="clearfix slider-controls-container">
    <div class="slider-controls new-arrivals-controls hidden-xs">
        <button class="prev">
            <i class="glyphicon glyphicon-chevron-left"></i>
        </button>
        <button class="next">
            <i class="glyphicon glyphicon-chevron-right"></i>
        </button>
    </div>
</div>

<div class="new-arrivals-slider slides-container">
    <ul class="slides">
        <?php $n=0; foreach ($this->__items as $item) { ?>
            <?php if ($n == 3) { $n = 0; ?>
                    </div>
                </li>                       
            <?php } ?>
            <?php if ($n == 0) { ?>
                <li>
                    <div class="row">            
            <?php } ?>            

                <?php $item->url = './shop/product/' . $item->{'slug'}; ?>
                
                <article class="category-article category-grid col-sm-4">
                    <figure>                    
                        <?php if ($item->{'featured_image.slug'}) { ?>
                        <img src="./asset/thumb/<?php echo $item->{'featured_image.slug'}; ?>" title="<?php echo $item->{'title'}; ?>" alt="<?php echo $item->{'title'}; ?>">
                        <?php } ?>
                        <a href="<?php echo $item->url; ?>">
                        <div class="figure-overlay">
                            <?php if ($item->{'description'}) { ?>
                            <div class="excerpt">
                                <button class="btn btn-default custom-button">
                                <?php echo $item->{'description'}; ?>
                                </button>
                            </div>
                            <?php } ?>
                            
                            <button class="btn btn-default custom-button">
                            View
                            </button>
                            <?php /* ?>
                            <a href="#">
                                <span class="wrap-icon"><i class='glyphicon glyphicon-heart'></i></span>
                            </a>
                            <a href="#">
                                <span class="wrap-icon"><i class='glyphicon glyphicon-ok'></i></span>
                            </a>
                            */ ?>
                        </div>
                        </a>
                    </figure>
                    <div class="text">
                        <h2>
                            <a href="<?php echo $item->url; ?>"> <?php echo $item->{'title'}; ?> </a>
                        </h2>
                        <div class="price">
                            <span class="new-price"><?php echo \Shop\Models\Currency::format( $item->price() ); ?></span>
                        </div>
                    </div>
                </article>
                
        <?php $n++; } ?>
    </ul>
</div>
