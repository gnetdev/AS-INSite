<div class="container">
    <ol class="breadcrumb">
        <li>
            <a href="./shop">Shop</a>
        </li>
        <?php foreach ($category->ancestors() as $ancestor) { ?>
        <li>
            <a href="./shop/category<?php echo $ancestor->path; ?>"><?php echo $ancestor->title; ?></a>
        </li>        	
        <?php } ?>
        <li class="active"><?php echo $category->title; ?></li>
    </ol>
</div>

<div class="page-head hidden-xs">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <?php if ($category->{'featured_image.slug'}) { ?>
                <img class="img-responsive" src="./asset/<?php echo $category->{'featured_image.slug'}; ?>" alt="" />
                <?php } ?>
                
                <h1><?php echo $category->title; ?></h1>
                <?php if ($category->description) { ?>
                <div><?php echo $category->description; ?></div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-sm-9">
			<div class="row">
	            <form method="get" action="./shop/category<?php echo $category->path; ?>/view-all">
	            <div class="filter-bar">
	                <div class="col-sm-6">
	                    <?php /* ?><button class="btn btn-default btn-sm custom-button pull-left">Compare</button> */ ?>
	                    <div class="sort-wrap pull-left">
	                        <label for="sort-by">Sort by: </label>
	                        <select name="sort_by" id="sort-by" class="chosen-select" onchange="this.form.submit();">
	                            <option value="title-asc" <?php if ($state->get('sort_by') == 'title-asc') { echo "selected"; } ?>>Name +</option>
	                            <option value="title-desc" <?php if ($state->get('sort_by') == 'title-desc') { echo "selected"; } ?>>Name -</option>
	                            <option value="price-asc" <?php if ($state->get('sort_by') == 'price-asc') { echo "selected"; } ?>>Price +</option>
	                            <option value="price-desc" <?php if ($state->get('sort_by') == 'price-desc') { echo "selected"; } ?>>Price -</option>
	                        </select>
	                    </div>
	                </div>
	                <div class="col-sm-6">
	                    <?php /*?>
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
                        url: './shop/category<?php echo $category->path; ?>/view-all/page/' + page
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
                <a href="./shop/category<?php echo $category->path; ?>" class="btn btn-default btn-sm custom-button">View 30 at a time</a>
            </p>
            
            <div class="grid-container">
                <?php echo $this->renderView('Shop/Site/Views::category/all_grid.php'); ?>
            </div>
            
        </div>
        <aside class="col-sm-3">
            
            <?php echo $this->renderView('Shop/Site/Views::categories/accordion.php'); ?>
            
            <?php if ($this->__colors = \Amrita\Models\ShopCategories::distinctColorTags($category->id)) { ?>
                <form method="get" action="./shop/category<?php echo $category->path; ?>/view-all">
                <?php echo $this->renderView('Shop/Site/Views::filters/color.php'); ?>
                </form>
            <?php } ?>
            
            <?php if ($this->__sizes = \Amrita\Models\ShopCategories::distinctSizeTags($category->id)) { ?>
                <form method="get" action="./shop/category<?php echo $category->path; ?>/view-all">
                <?php echo $this->renderView('Shop/Site/Views::filters/size.php'); ?>
                </form>
            <?php } ?>
            
            <?php // echo $this->renderView('Shop/Site/Views::widgets/featured.php'); ?>
            
            <?php // echo $this->renderView('Shop/Site/Views::widgets/product.php'); ?>
        </aside>
    </div>
</div>