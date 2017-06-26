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
        <div class="col-sm-12">
        	<div class="row">
	            <form method="get" action="./shop/collection/<?php echo $collection->slug; ?>">
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
            
            <?php echo $this->renderView('Shop/Site/Views::collection/grid-testing.php'); ?>
            
        </div>
    </div>
</div>