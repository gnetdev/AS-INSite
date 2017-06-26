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
        	            
            <?php echo $this->renderView('Shop/Site/Views::collection/grid.php'); ?>
            
        </div>
    </div>
</div>