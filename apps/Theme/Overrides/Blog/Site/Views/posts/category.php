<div class="blog-page">
    <div class="container">
        <div class="row">
            <div class="col-sm-8">
            
                <?php if (!empty($paginated->items)) { ?>
            
                <?php foreach ($paginated->items as $item) { 
                    $item->_url = './blog/post/' . $item->{'metadata.slug'}; ?>            
                
                    <article id="post-<?php echo $item->id; ?>" class="blog-article">
                    
                        <h2><a href="<?php echo $item->_url; ?>"><?php echo $item->{'metadata.title'}; ?></a></h2>
                        
                        <?php if ($item->{'details.featured_image.slug'}) { ?>
                        <figure class="flexslider photo-gallery">
                            <ul class="slides">
                                <li>
                                    <img class="entry-featured img-responsive" width="100%" src="./asset/thumb/<?php echo $item->{'details.featured_image.slug'} ?>">
                                </li>
                            </ul>
                        </figure>
                        <?php } ?>
                        
                        <div class="text">
                            <div class="left-info">
                                <span class="bold-text">5 May 2013</span>
                                <span class="bold-text"><a href="#">7 Comment(s)</a></span>
                                <div class="info-separator">
                                    <div class="separator-icon photo"></div>
                                </div>
                                <span class="small-text">by <a href="#">Martin Doe</a></span>
                                <span class="small-text">tags: <a href="category-grid.html">Fashion</a>, <a href="category-list.html">Clothes</a></span>
                            </div>
                            <div class="right">
                                <div class="excerpt">
                                    <p>
                                        Mauris urna est, mattis vel dolor vel, posuere venenatis mi. Pellentesque eu massa a libero iaculis consequat eu lacinia lacus. Phasellus vel suscipit est.Donec gravida mi vitae sodales sollicitudin. Nam imperdiet sollicitudin odio, id
                                        fringilla diam suscipit sit amet Ñ�ras sollicitudin. Praesent eleifend iaculis
                                        bibendum. Praesent semper sapien in metus.  Etiam egestas ipsum orci, quis
                                        blandit tortor eleifend et.
                                    </p>
                                </div>
                                <div class="bottom-line">
                                    <a href="blog-article.html" class="read-more">Read More &gt;</a>
                                    <div class="blog-stats">
                                        <span><i class="glyphicon glyphicon-eye-open"></i> 151 </span>
                                        <span><i class="glyphicon glyphicon-heart"></i> 87 </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                    
                <?php } ?>
                
                <?php } else { ?>
                    
                        <div class="">No items found.</div>
                    
                <?php } ?>
                
                <div class="main-bottom">
                
                    <div class="row datatable-footer">
                        <div class="col-sm-2">
                            <div class="page-counter">
                            <?php echo (!empty($paginated->total_pages)) ? $paginated->getResultsCounter() : null; ?>
                            </div>
                        </div>
                    
                        <?php if (!empty($paginated->total_pages) && $paginated->total_pages > 1) { ?>
                        <div class="col-sm-10">
                            <div class="pull-right">
                            <?php echo $paginated->serve(); ?>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                                    
                </div>
                
            </div>
            <aside class="col-sm-4">
                <div class="widget">
                    <div class="widget-title">
                        <h2>Categories</h2>
                    </div>
                    <div class="widget-content">
                        <div class="accordion">
                            <div class="panel-group" id="accordion">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                                                make-up & beauty
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="collapseOne" class="panel-collapse collapse in">
                                        <div class="panel-body">
                                            <ul>
                                                <li><a href="category-list.html">day make-up</a></li>
                                                <li><a href="category-grid.html">night make-up</a></li>
                                                <li><a href="category-list.html">every day make-up</a></li>
                                                <li><a href="category-grid.html">beauty skin</a></li>
                                                <li><a href="category-list.html">manicure</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
                                                Accessories
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="collapseTwo" class="panel-collapse collapse">
                                        <div class="panel-body">
                                            <ul>
                                                <li><a href="category-list.html">day make-up</a></li>
                                                <li><a href="category-grid.html">night make-up</a></li>
                                                <li><a href="category-list.html">every day make-up</a></li>
                                                <li><a href="category-grid.html">beauty skin</a></li>
                                                <li><a href="category-list.html">manicure</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
                                                fashion trends
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="collapseThree" class="panel-collapse collapse">
                                        <div class="panel-body">
                                            <ul>
                                                <li><a href="category-grid.html">day make-up</a></li>
                                                <li><a href="category-list.html">night make-up</a></li>
                                                <li><a href="category-grid.html">every day make-up</a></li>
                                                <li><a href="category-list.html">beauty skin</a></li>
                                                <li><a href="category-grid.html">manicure</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse4">
                                                all about clothing
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="collapse4" class="panel-collapse collapse">
                                        <div class="panel-body">
                                            <ul>
                                                <li><a href="category-list.html">day make-up</a></li>
                                                <li><a href="category-grid.html">night make-up</a></li>
                                                <li><a href="category-list.html">every day make-up</a></li>
                                                <li><a href="category-grid.html">beauty skin</a></li>
                                                <li><a href="category-list.html">manicure</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="widget">
                    <div class="widget-title">
                        <h2>Tag Cloud</h2>
                    </div>
                    <div class="widget-content">
                        <ul class='tag-cloud'>
                            <li><a href="#" class="btn btn-default">Photography</a></li>
                            <li><a href="#" class="btn btn-default">dresses</a></li>
                            <li><a href="#" class="btn btn-default">Fashion</a></li>
                            <li><a href="#" class="btn btn-default">Clothing</a></li>
                            <li><a href="#" class="btn btn-default">Trends</a></li>
                            <li><a href="#" class="btn btn-default">Collection</a></li>
                            <li><a href="#" class="btn btn-default">Accessories</a></li>
                            <li><a href="#" class="btn btn-default">Beauty</a></li>
                            <li><a href="#" class="btn btn-default">Make-up</a></li>
                            <li><a href="#" class="btn btn-default">Design</a></li>
                            <li><a href="#" class="btn btn-default">Colors</a></li>
                            <li><a href="#" class="btn btn-default">Shoes</a></li>
                        </ul>
                    </div>
                </div>

            </aside>
        </div>
    </div>
</div>