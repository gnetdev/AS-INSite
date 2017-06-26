<div class="well">

<form id="settings-form" role="form" method="post" class="form-horizontal clearfix">

    <div class="clearfix">
        <button type="submit" class="btn btn-primary pull-right">Save Changes</button>
    </div>

    <hr />

    <div class="row">
        <div class="col-lg-2 col-md-3 col-sm-4">
            <ul class="nav nav-pills nav-stacked">
                <li class="active">
                    <a href="#tab-product-feeds" data-toggle="tab"> Product Feeds </a>
                </li>
                <li>
                    <a href="#tab-seo" data-toggle="tab"> SEO </a>
                </li>                
            </ul>
        </div>

        <div class="col-lg-10 col-md-9 col-sm-8">

            <div class="tab-content stacked-content">

                <div class="tab-pane fade in active" id="tab-product-feeds">
                    <h4>Product Feeds</h4>
                    
                    <hr />
                    
                    <?php $product_tags = \Shop\Models\Products::distinctTags(); ?>
                    <div class="form-group">
                        
                        <label>4 Product Slider - Group 1</label>
                        <div class="row">
                            <div class="col-md-4">
                                <label>Label</label>
                                <input type="text" name="site_home[row_1][tab_1][label]" class="form-control input-sm" value="<?php echo $flash->old('site_home.row_1.tab_1.label'); ?>" />
                            </div>                        
                            <div class="col-md-4">
                                <label>Tag to Use</label>
                                <input name="site_home[row_1][tab_1][tag]" data-maximum="1" data-tags='<?php echo json_encode( $product_tags ); ?>' value="<?php echo $flash->old('site_home.row_1.tab_1.tag'); ?>" type="text" class="form-control ui-select2-tags" />
                            </div>                        
                            <div class="col-md-4">
                                <label>Number of Images</label>
                                <input type="text" name="site_home[row_1][tab_1][images_count]" class="form-control input-sm" value="<?php echo $flash->old('site_home.row_1.tab_1.images_count'); ?>" />
                            </div>
                        </div>                        
                    </div>
                    <!-- /.form-group -->
                    
                    <div class="form-group">
                        
                        <label>4 Product Slider - Group 2</label>
                        <div class="row">
                            <div class="col-md-4">
                                <label>Label</label>
                                <input type="text" name="site_home[row_1][tab_2][label]" class="form-control input-sm" value="<?php echo $flash->old('site_home.row_1.tab_2.label'); ?>" />
                            </div>                        
                            <div class="col-md-4">
                                <label>Tag to Use</label>
                                <input name="site_home[row_1][tab_2][tag]" data-maximum="1" data-tags='<?php echo json_encode( $product_tags ); ?>' value="<?php echo $flash->old('site_home.row_1.tab_2.tag'); ?>" type="text" class="form-control ui-select2-tags" />
                            </div>                        
                            <div class="col-md-4">
                                <label>Number of Images</label>
                                <input type="text" name="site_home[row_1][tab_2][images_count]" class="form-control input-sm" value="<?php echo $flash->old('site_home.row_1.tab_2.images_count'); ?>" />
                            </div>
                        </div>                        
                    </div>
                    <!-- /.form-group -->
                    
                    <div class="form-group">
                        
                        <label>4 Product Slider - Group 3</label>
                        <div class="row">
                            <div class="col-md-4">
                                <label>Label</label>
                                <input type="text" name="site_home[row_1][tab_3][label]" class="form-control input-sm" value="<?php echo $flash->old('site_home.row_1.tab_3.label'); ?>" />
                            </div>                        
                            <div class="col-md-4">
                                <label>Tag to Use</label>
                                <input name="site_home[row_1][tab_3][tag]" data-maximum="1" data-tags='<?php echo json_encode( $product_tags ); ?>' value="<?php echo $flash->old('site_home.row_1.tab_3.tag'); ?>" type="text" class="form-control ui-select2-tags" />
                            </div>                        
                            <div class="col-md-4">
                                <label>Number of Images</label>
                                <input type="text" name="site_home[row_1][tab_3][images_count]" class="form-control input-sm" value="<?php echo $flash->old('site_home.row_1.tab_3.images_count'); ?>" />
                            </div>
                        </div>                        
                    </div>
                    <!-- /.form-group -->
                    
                    <div class="form-group">
                        
                        <label>3 Product Slider - Group 1</label>
                        <div class="row">
                            <div class="col-md-4">
                                <label>Label</label>
                                <input type="text" name="site_home[3_products][group_1][label]" class="form-control input-sm" value="<?php echo $flash->old('site_home.3_products.group_1.label'); ?>" />
                            </div>                        
                            <div class="col-md-4">
                                <label>Tag to Use</label>
                                <input name="site_home[3_products][group_1][tag]" data-maximum="1" data-tags='<?php echo json_encode( $product_tags ); ?>' value="<?php echo $flash->old('site_home.3_products.group_1.tag'); ?>" type="text" class="form-control ui-select2-tags" />
                            </div>                        
                            <div class="col-md-4">
                                <label>Number of Images</label>
                                <input type="text" name="site_home[3_products][group_1][images_count]" class="form-control input-sm" value="<?php echo $flash->old('site_home.3_products.group_1.images_count'); ?>" />
                            </div>
                        </div>                        
                    </div>
                    <!-- /.form-group -->
                    
                </div>
                
                <div class="tab-pane fade in" id="tab-seo">
                
                    <div class="form-group">
                        
                        <label>Home Page Title</label>
                        <input type="text" name="site_home[page_title]" class="form-control" value="<?php echo $flash->old('site_home.page_title'); ?>" placeholder='e.g. Designer Indian Jewelry and Fashion Accessories' />
                                                
                    </div>
                    <!-- /.form-group -->
                    
                    <div class="form-group">
                        
                        <label>Home Page Meta Description</label>
                        <textarea name="site_home[page_description]" class="form-control"><?php echo $flash->old('site_home.page_description'); ?></textarea>
                                                
                    </div>
                    <!-- /.form-group -->                    
                                    
                </div>                
                
            </div>

        </div>
    </div>

</form>

</div>