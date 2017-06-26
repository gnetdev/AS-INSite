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
        <div class="col-sm-12 deal-of-the-day">
        
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
        	            
            <?php echo $this->renderView('Shop/Site/Views::collection/all_grid/deal_of_the_week.php'); ?>
            
        </div>
    </div>
</div>