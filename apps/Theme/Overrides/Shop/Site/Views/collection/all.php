<div class="container">
    <ol class="breadcrumb">
        <li>
            <a href="./shop">Shop</a>
        </li>
        <li class="active"><?php echo $collection->title; ?></li>
    </ol>
</div>
    
<div class="page-head">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <?php if ($collection->{'featured_image.slug'}) { ?>
                <h1 class="collection-title"><?php echo $collection->title; ?></h1>
                 <figure class="visible-lg">
                     <img src="./asset/<?php echo $collection->{'featured_image.slug'}; ?>" alt="" />
                
                    <figcaption>
                         <?php if ($collection->description) { ?>
                         <div class="collection-desc"><?php echo $collection->description; ?></div>
                         <?php } ?>
                    </figcaption>
                 </figure>
                 <?php } else { ?>
                     <h1><?php echo $collection->title; ?></h1>
                     <?php if ($collection->description) { ?>
                     <div><?php echo $collection->description; ?></div>
                     <?php } ?>
                <?php } ?>
                <div class="visible-xs visible-sm visible-md"><?php echo $collection->description; ?></div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-sm-9">
        	<div class="row">
	            <form method="get" action="./shop/collection/<?php echo $collection->slug; ?>/view-all">
	            <div class="filter-bar">
	                <div class="col-sm-6">
	                    <?php /* ?><button class="btn btn-default btn-sm custom-button pull-left">Compare</button> */ ?>
	                    <div class="sort-wrap pull-left">
	                        <label for="sort-by">Sort by: </label>
	                        <select name="sort_by" id="sort-by" class="chosen-select" onchange="this.form.submit();">
	                            <option value="collection-default" <?php if ($state->get('sort_by') == 'collection-default') { echo "selected"; } ?>>Default</option>
	                            <option value="title-asc" <?php if ($state->get('sort_by') == 'title-asc') { echo "selected"; } ?>>Name +</option>
	                            <option value="title-desc" <?php if ($state->get('sort_by') == 'title-desc') { echo "selected"; } ?>>Name -</option>
	                            <option value="price-asc" <?php if ($state->get('sort_by') == 'price-asc') { echo "selected"; } ?>>Price +</option>
	                            <option value="price-desc" <?php if ($state->get('sort_by') == 'price-desc') { echo "selected"; } ?>>Price -</option>
	                        </select>
	                    </div>
	                </div>
	                <div class="col-sm-6">
	                    <?php /* ?>
	                    <div class="range-wrap custom-range pull-left">
	                        <label for="range-price">Price filter: </label>
	                        <input id="range-price" type="text" class="col-sm-8 col-md-7 col-xs-6 range-slider" value="" data-slider-value="[0,150]" />
	                    </div>
	                    */ ?>
	                    <ul class="list-inline list-unstyled pull-right no-margin">
	                       <li>
	                       </li>
	                       <li>
	                       <button class="btn btn-default btn-sm custom-button" onclick="jQuery('#reset-tags').attr('name', 'filter[tags][]');">Clear Filters</button>
	                       </li>
	                    </ul>
	                    
	                    <input type="hidden" id="reset-tags" value="">                   
	                </div>
	            </div>
	            </form>
            </div>
            
            <?php if (!empty($paginated->items) && $paginated->total_items > $paginated->items_per_page) { ?>
            <script>
            jQuery(document).ready(function(){
            	function get_items(page) {
                    var request = jQuery.ajax({
                        type: 'get', 
                        url: './shop/collection/<?php echo $collection->slug; ?>/view-all/page/' + page
                    }).done(function(data){
                        var lr = jQuery.parseJSON( JSON.stringify(data), false);
                        if (lr.result) {
                            jQuery('.grid-container').append(lr.result);                
                        }
                        if (lr.next_page) {
                        	get_items(parseInt(lr.next_page));
                        }
                    });				
            	}
            
            	get_items(<?php echo $paginated->next_page; ?>);
            });
            </script>
            <?php } ?>
            
            <p class="margin-top">
                <a href="./shop/collection/<?php echo $collection->slug; ?>" class="btn btn-default btn-sm custom-button">View 30 at a time</a>
            </p>
            
            <div class="grid-container">                        
                <?php echo $this->renderView('Shop/Site/Views::collection/all_grid.php'); ?>
            </div>
            
        </div>
        <aside class="col-sm-3">
        
            <?php echo $this->renderView('Shop/Site/Views::categories/accordion.php'); ?>

            <?php if ($this->__colors = \Amrita\Models\ShopCollections::distinctColorTags($collection->id)) { ?>
                <form method="get" action="./shop/collection/<?php echo $collection->slug; ?>/view-all">
                <?php echo $this->renderView('Shop/Site/Views::filters/color.php'); ?>
                </form>
            <?php } ?>
            
            <?php if ($this->__sizes = \Amrita\Models\ShopCollections::distinctSizeTags($collection->id)) { ?>
                <form method="get" action="./shop/collection/<?php echo $collection->slug; ?>/view-all">
                <?php echo $this->renderView('Shop/Site/Views::filters/size.php'); ?>
                </form>
            <?php } ?>
            
            <?php // echo $this->renderView('Shop/Site/Views::widgets/featured.php'); ?>
            
            <?php // echo $this->renderView('Shop/Site/Views::widgets/product.php'); ?>

        </aside>
    </div>
</div>