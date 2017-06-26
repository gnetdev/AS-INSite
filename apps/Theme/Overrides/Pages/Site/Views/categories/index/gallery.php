<?php $aside = false;
// // is a module published in the pages-category-aside position? 
$module_content = \Modules\Factory::render( 'pages-category-aside', \Base::instance()->get('PARAMS.0') );
if (!empty($module_content)) {
	$aside = true;
}
?>

<div id="pages-category" class="pages-pages">
    <div class="container">
        <?php if ($category->{'featured_image.slug'}) { ?>
        <div class="category-header-image">
            <img class="img-responsive" src="./asset/<?php echo $category->{'featured_image.slug'}; ?>" title="<?php echo $category->{'title'}; ?>" alt="<?php echo $category->{'title'}; ?>">
        </div>
        <?php } ?>
                
        <div class="row">
            <div class="col-sm-<?php echo !empty($aside) ? '9' : '12'; ?>">    
            
            <?php $this->paginated = $paginated; ?>
            
            <?php
            	if (!empty($this->paginated->items)) { ?>
            	
            	<?php $n=0; $count = count($paginated->items); ?>    
                <?php foreach ($paginated->items as $position=>$item) { ?>
                    <?php $item->url = './pages/' . $item->slug; ?>
                    <?php if ($n == 0 || ($n % 4 == 0)) { ?><div class="row"><?php } ?>   
                        
                    <div class="col-xs-6 col-sm-3 col-md-3 category-article category-grid text-center">
                        
                        <div class="">
                            <?php if ($item->{'featured_image.slug'}) { ?>
                            <a href="<?php echo $item->url; ?>">
                                <img class="img-responsive" src="./asset/thumb/<?php echo $item->{'featured_image.slug'} ?>">
                            </a>
                            <?php } ?>                
                        </div>
               
                    </div>
                         
                    <?php $n++; if (($n % 4 == 0) || $n==$count) { ?></div> <hr/><?php } ?>
                            
                <?php } ?>
                
                <div class="pagination-wrapper">
                    <div class="row">
                        <div class="col-sm-10">
                            <?php if (!empty($this->paginated->total_pages) && $this->paginated->total_pages > 1) { ?>
                                <?php echo $this->paginated->serve(); ?>
                            <?php } ?>
                        </div>
                        <div class="col-sm-2">
                            <div class="pagination-count pull-right">
                                <span class="pagination">
                                    <?php echo (!empty($this->paginated->total_pages)) ? $this->paginated->getResultsCounter() : null; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>        
            
            <?php } else { ?>
                
                    <p>No items found.</p>
                
            <?php } ?>
            
            
            </div>
            
            <?php if (!empty($aside)) { ?>
            <aside class="col-sm-3">
            	<?php 
                    echo $module_content;
            	?>
            </aside>
            <?php } ?>
            
        </div>    
    </div>
</div>