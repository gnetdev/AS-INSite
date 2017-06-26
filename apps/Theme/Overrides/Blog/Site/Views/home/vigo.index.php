<div class="blog-page">
    <div class="container">
        <div class="row">
            <div class="col-sm-8">
            
                <?php if (!empty($paginated->items)) { ?>
            
                <?php foreach($paginated->items as $item) { 
                    $item->_url = './blog/post/' . $item->slug; 
                ?>            
                
                    <article id="post-<?php echo $item->id; ?>" class="blog-article">
                    
                        <h2><a href="<?php echo $item->_url; ?>"><?php echo $item->{'title'}; ?></a></h2>
                        
                        <?php if ($item->{'featured_image.slug'}) { ?>
                        <a href="<?php echo $item->_url; ?>">
                        <figure class="flexslider photo-gallery">
                            <ul class="slides">
                                <li>
                                    <img class="entry-featured img-responsive" width="100%" src="./asset/thumb/<?php echo $item->{'featured_image.slug'} ?>">
                                </li>
                            </ul>
                        </figure>
                        </a>
                        <?php } ?>
                        
                        <div class="text">
                            <div class="left-info">
                                <span class="bold-text"><?php echo date( 'd F Y', $item->{'publication.start.time'} ); ?></span>
                                <?php /*?><span class="bold-text"><a href="#">7 Comment(s)</a></span>*/ ?>
                                <div class="info-separator">
                                    <div class="separator-icon photo"></div>
                                </div>
                                <?php /* ?>
                                <span class="small-text">by <a href="./blog/author/<?php echo $item->{'metadata.creator.id'}; ?>"><?php echo $item->{'metadata.creator.name'}; ?></a></span>
                                */ ?>
                                <?php if (!empty($item->{'tags'})) { ?>
                                <span class="small-text">
                                    tags:
                                    <?php foreach ($item->{'tags'} as $key=>$tag) { if ($key>0){ echo ", "; }?><a class="tag" href="./blog/tag/<?php echo $tag; ?>" rel="tag"><?php echo $tag; ?></a><?php } ?>
                                </span>
                                <?php } ?>                                
                                
                            </div>
                            <div class="right">
                                <div class="excerpt">
                                    <?php 
                                    $parts = explode( '</p>', $item->{'copy'} );
                                    if (!empty($parts[0])) {
                                    	echo $parts[0] . "</p>";
                                    } 
                                    ?>
                                </div>
                                <div class="bottom-line">
                                    <a href="<?php echo $item->_url; ?>" class="read-more">Read More &gt;</a>
                                    <?php /* ?>
                                    <div class="blog-stats">
                                        <span><i class="glyphicon glyphicon-eye-open"></i> 151 </span>
                                        <span><i class="glyphicon glyphicon-heart"></i> 87 </span>
                                    </div>
                                    */ ?>
                                </div>
                            </div>
                        </div>
                    </article>
                    
                <?php } ?>
                
                <?php } else { ?>
                    
                        <div class="">No items found.</div>
                    
                <?php } ?>
                
                <div class="main-bottom">
                
                    <div class="dt-row dt-bottom-row">
                        <div class="row">
                            <div class="col-sm-10">
                                <?php if (!empty($paginated->total_pages) && $paginated->total_pages > 1) { ?>
                                    <?php echo $paginated->serve(); ?>
                                <?php } ?>
                            </div>
                            <div class="col-sm-2">
                                <div class="datatable-results-count pull-right">
                                    <span class="pagination">
                                        <?php echo (!empty($paginated->total_pages)) ? $paginated->getResultsCounter() : null; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
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