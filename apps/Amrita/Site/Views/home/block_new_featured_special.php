<div class="row">
    <div class="col-sm-12">
        <div class="homepage-products">
            <?php 
            $tag = $settings->{'site_home.row_1.tab_1.tag'};
            $limit = $settings->{'site_home.row_1.tab_1.images_count'} ? (int) $settings->{'site_home.row_1.tab_1.images_count'} : 12;
            if ($limit > 12) { $limit = 12; }
            if ($tag) {
                $this->__items = (new \Shop\Models\Products)
                ->setState( 'filter.tag', $tag )
                ->setParam( 'limit', $limit )
                ->setState( 'filter.published_today', true )
                ->setState( 'filter.publication_status', 'published' )
                ->getItemsRandom();
                
                if ($this->__items) { ?>
                    <div class="col-sm-12">
                        <div class="section-title">
                            <h1><?php echo $settings->{'site_home.row_1.tab_1.label'}; ?></h1>
                        </div>
                    </div>                
                    <div class="" id="<?php echo 'tab_1'; ?>">
                        <?php echo $this->renderView('Amrita/Site/Views::home/product_slider_4.php'); ?>
                    </div>
                	
                <?php }
            }
            ?>
            
            <?php 
            $tag = $settings->{'site_home.row_1.tab_2.tag'};
            $limit = $settings->{'site_home.row_1.tab_2.images_count'} ? (int) $settings->{'site_home.row_1.tab_2.images_count'} : 12;
            if ($limit > 12) { $limit = 12; }
            if ($tag) {
                $this->__items = (new \Shop\Models\Products)
                ->setState( 'filter.tag', $tag )
                ->setState( 'list.limit', $limit )
                ->setState( 'filter.published_today', true )
                ->setState( 'filter.publication_status', 'published' )
                ->getItemsRandom();
                
                if ($this->__items) { ?>
                    <div class="col-sm-12">
                        <div class="section-title">
                            <h1><?php echo $settings->{'site_home.row_1.tab_2.label'}; ?></h1>
                        </div>
                    </div>                
                    <div class="" id="<?php echo 'tab_2'; ?>">
                        <?php echo $this->renderView('Amrita/Site/Views::home/product_slider_4.php'); ?>
                    </div>
                	
                <?php }
            }
            ?>
            
            <?php 
            $tag = $settings->{'site_home.row_1.tab_3.tag'};
            $limit = $settings->{'site_home.row_1.tab_3.images_count'} ? (int) $settings->{'site_home.row_1.tab_3.images_count'} : 12;
            if ($limit > 12) { $limit = 12; }
            if ($tag) {
                $this->__items = (new \Shop\Models\Products)
                ->setState( 'filter.tag', $tag )
                ->setState( 'list.limit', $limit )
                ->setState( 'filter.published_today', true )
                ->setState( 'filter.publication_status', 'published' )
                ->getItemsRandom();
                
                if ($this->__items) { ?>
                    <div class="col-sm-12">
                        <div class="section-title">
                            <h1><?php echo $settings->{'site_home.row_1.tab_3.label'}; ?></h1>
                        </div>
                    </div>                
                    <div class="" id="<?php echo 'tab_3'; ?>">
                        <?php echo $this->renderView('Amrita/Site/Views::home/product_slider_4.php'); ?>
                    </div>
                	
                <?php }
            }
            ?>
        </div>

    </div>
</div>