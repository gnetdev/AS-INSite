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
                    <a href="#tab-row-1" data-toggle="tab"> Row 1 </a>
                </li>
                <li>
                    <a href="#tab-row-2" data-toggle="tab"> Row 2 </a>
                </li>                
                <li>
                    <a href="#tab-row-3" data-toggle="tab"> Row 3 </a>
                </li>
            </ul>
        </div>

        <div class="col-lg-10 col-md-9 col-sm-8">

            <div class="tab-content stacked-content">

                <div class="tab-pane fade in active" id="tab-row-1">
                    <h4>Row 1</h4>
                    
                    <hr />
                    
                    <div class="form-group">
                        <div class="alert alert-info"><p>Ideal image dimensions: 720 x 720 </p></div>
                        
                        <label>Featured Image</label>
                        <div class="row">
                            <div class="col-md-5">
                                <label>Image</label>
                                <?php echo \Assets\Admin\Controllers\Assets::instance()->fetchElementImage( 'shop_home_row_1_featured_slug', $flash->old('shop_home.row_1.featured.slug'), array('field'=>'shop_home[row_1][featured][slug]') ); ?>
                            </div>                        
                            <div class="col-md-7">
                                <label>URL</label>
                                <input type="text" name="shop_home[row_1][featured][url]" class="form-control input-sm" value="<?php echo $flash->old('shop_home.row_1.featured.url'); ?>" />
                                <label>Header</label>
                                <input type="text" name="shop_home[row_1][featured][header]" class="form-control input-sm" value="<?php echo $flash->old('shop_home.row_1.featured.header'); ?>" />
                                <label>Button Label</label>
                                <input type="text" name="shop_home[row_1][featured][label]" class="form-control input-sm" value="<?php echo $flash->old('shop_home.row_1.featured.label'); ?>" />
                                <label>Copy</label>
                                <textarea name="shop_home[row_1][featured][copy]" class="form-control"><?php echo $flash->old('shop_home.row_1.featured.copy'); ?></textarea>
                            </div>
                        </div>                        
                    </div>
                    <!-- /.form-group -->
                    
                    <div class="form-group">
                        <div class="alert alert-info"><p>Ideal image dimensions: 360 x 360 </p></div>
                        
                        <label>Image 1</label>
                        <div class="row">
                            <div class="col-md-5">
                                <label>Image</label>
                                <?php echo \Assets\Admin\Controllers\Assets::instance()->fetchElementImage( 'shop_home_row_1_slug_image_1', $flash->old('shop_home.row_1.image_1.slug'), array('field'=>'shop_home[row_1][image_1][slug]') ); ?>
                            </div>                        
                            <div class="col-md-7">
                                <label>URL</label>
                                <input type="text" name="shop_home[row_1][image_1][url]" class="form-control input-sm" value="<?php echo $flash->old('shop_home.row_1.image_1.url'); ?>" />
                                <label>Header</label>
                                <input type="text" name="shop_home[row_1][image_1][header]" class="form-control input-sm" value="<?php echo $flash->old('shop_home.row_1.image_1.header'); ?>" />
                                <label>Button Label</label>
                                <input type="text" name="shop_home[row_1][image_1][label]" class="form-control input-sm" value="<?php echo $flash->old('shop_home.row_1.image_1.label'); ?>" />
                                <label>Copy</label>
                                <textarea name="shop_home[row_1][image_1][copy]" class="form-control"><?php echo $flash->old('shop_home.row_1.image_1.copy'); ?></textarea>
                            </div>
                        </div>                        
                    </div>
                    <!-- /.form-group -->
                    
                    <div class="form-group">
                        <div class="alert alert-info"><p>Ideal image dimensions: 360 x 360 </p></div>
                        
                        <label>Image 2</label>
                        <div class="row">
                            <div class="col-md-5">
                                <label>Image</label>
                                <?php echo \Assets\Admin\Controllers\Assets::instance()->fetchElementImage( 'shop_home_row_1_slug_image_2', $flash->old('shop_home.row_1.image_2.slug'), array('field'=>'shop_home[row_1][image_2][slug]') ); ?>
                            </div>                        
                            <div class="col-md-7">
                                <label>URL</label>
                                <input type="text" name="shop_home[row_1][image_2][url]" class="form-control input-sm" value="<?php echo $flash->old('shop_home.row_1.image_2.url'); ?>" />
                                <label>Header</label>
                                <input type="text" name="shop_home[row_1][image_2][header]" class="form-control input-sm" value="<?php echo $flash->old('shop_home.row_1.image_2.header'); ?>" />
                                <label>Button Label</label>
                                <input type="text" name="shop_home[row_1][image_2][label]" class="form-control input-sm" value="<?php echo $flash->old('shop_home.row_1.image_2.label'); ?>" />
                                <label>Copy</label>
                                <textarea name="shop_home[row_1][image_2][copy]" class="form-control"><?php echo $flash->old('shop_home.row_1.image_2.copy'); ?></textarea>
                            </div>
                        </div>                        
                    </div>
                    <!-- /.form-group -->
                    
                    <div class="form-group">
                        <div class="alert alert-info"><p>Ideal image dimensions: 360 x 360 </p></div>
                        
                        <label>Image 3</label>
                        <div class="row">
                            <div class="col-md-5">
                                <label>Image</label>
                                <?php echo \Assets\Admin\Controllers\Assets::instance()->fetchElementImage( 'shop_home_row_1_slug_image_3', $flash->old('shop_home.row_1.image_3.slug'), array('field'=>'shop_home[row_1][image_3][slug]') ); ?>
                            </div>                        
                            <div class="col-md-7">
                                <label>URL</label>
                                <input type="text" name="shop_home[row_1][image_3][url]" class="form-control input-sm" value="<?php echo $flash->old('shop_home.row_1.image_3.url'); ?>" />
                                <label>Header</label>
                                <input type="text" name="shop_home[row_1][image_3][header]" class="form-control input-sm" value="<?php echo $flash->old('shop_home.row_1.image_3.header'); ?>" />
                                <label>Button Label</label>
                                <input type="text" name="shop_home[row_1][image_3][label]" class="form-control input-sm" value="<?php echo $flash->old('shop_home.row_1.image_3.label'); ?>" />
                                <label>Copy</label>
                                <textarea name="shop_home[row_1][image_3][copy]" class="form-control"><?php echo $flash->old('shop_home.row_1.image_3.copy'); ?></textarea>
                            </div>
                        </div>                        
                    </div>
                    <!-- /.form-group -->
                    
                    <div class="form-group">
                        <div class="alert alert-info"><p>Ideal image dimensions: 360 x 360 </p></div>
                        
                        <label>Image 4</label>
                        <div class="row">
                            <div class="col-md-5">
                                <label>Image</label>
                                <?php echo \Assets\Admin\Controllers\Assets::instance()->fetchElementImage( 'shop_home_row_1_slug_image_4', $flash->old('shop_home.row_1.image_4.slug'), array('field'=>'shop_home[row_1][image_4][slug]') ); ?>
                            </div>                        
                            <div class="col-md-7">
                                <label>URL</label>
                                <input type="text" name="shop_home[row_1][image_4][url]" class="form-control input-sm" value="<?php echo $flash->old('shop_home.row_1.image_4.url'); ?>" />
                                <label>Header</label>
                                <input type="text" name="shop_home[row_1][image_4][header]" class="form-control input-sm" value="<?php echo $flash->old('shop_home.row_1.image_4.header'); ?>" />
                                <label>Button Label</label>
                                <input type="text" name="shop_home[row_1][image_4][label]" class="form-control input-sm" value="<?php echo $flash->old('shop_home.row_1.image_4.label'); ?>" />
                                <label>Copy</label>
                                <textarea name="shop_home[row_1][image_4][copy]" class="form-control"><?php echo $flash->old('shop_home.row_1.image_4.copy'); ?></textarea>
                            </div>
                        </div>                        
                    </div>
                    <!-- /.form-group -->
                    
                </div>
                
                <div class="tab-pane fade" id="tab-row-2">
                    <h4>Row 2</h4>
                    
                    <hr />
                    
                    <div class="form-group">
                        <div class="alert alert-info"><p>Ideal image dimensions: 720 x 720 </p></div>
                        
                        <label>Featured Image</label>
                        <div class="row">
                            <div class="col-md-5">
                                <label>Image</label>
                                <?php echo \Assets\Admin\Controllers\Assets::instance()->fetchElementImage( 'shop_home_row_2_featured_slug', $flash->old('shop_home.row_2.featured.slug'), array('field'=>'shop_home[row_2][featured][slug]') ); ?>
                            </div>                        
                            <div class="col-md-7">
                                <label>URL</label>
                                <input type="text" name="shop_home[row_2][featured][url]" class="form-control input-sm" value="<?php echo $flash->old('shop_home.row_2.featured.url'); ?>" />
                                <label>Header</label>
                                <input type="text" name="shop_home[row_2][featured][header]" class="form-control input-sm" value="<?php echo $flash->old('shop_home.row_2.featured.header'); ?>" />
                                <label>Button Label</label>
                                <input type="text" name="shop_home[row_2][featured][label]" class="form-control input-sm" value="<?php echo $flash->old('shop_home.row_2.featured.label'); ?>" />
                                <label>Copy</label>
                                <textarea name="shop_home[row_2][featured][copy]" class="form-control"><?php echo $flash->old('shop_home.row_2.featured.copy'); ?></textarea>
                            </div>
                        </div>                        
                    </div>
                    <!-- /.form-group -->
                    
                    <div class="form-group">
                        <div class="alert alert-info"><p>Ideal image dimensions: 360 x 360 </p></div>
                        
                        <label>Image 1</label>
                        <div class="row">
                            <div class="col-md-5">
                                <label>Image</label>
                                <?php echo \Assets\Admin\Controllers\Assets::instance()->fetchElementImage( 'shop_home_row_2_slug_image_1', $flash->old('shop_home.row_2.image_1.slug'), array('field'=>'shop_home[row_2][image_1][slug]') ); ?>
                            </div>                        
                            <div class="col-md-7">
                                <label>URL</label>
                                <input type="text" name="shop_home[row_2][image_1][url]" class="form-control input-sm" value="<?php echo $flash->old('shop_home.row_2.image_1.url'); ?>" />
                                <label>Header</label>
                                <input type="text" name="shop_home[row_2][image_1][header]" class="form-control input-sm" value="<?php echo $flash->old('shop_home.row_2.image_1.header'); ?>" />
                                <label>Button Label</label>
                                <input type="text" name="shop_home[row_2][image_1][label]" class="form-control input-sm" value="<?php echo $flash->old('shop_home.row_2.image_1.label'); ?>" />
                                <label>Copy</label>
                                <textarea name="shop_home[row_2][image_1][copy]" class="form-control"><?php echo $flash->old('shop_home.row_2.image_1.copy'); ?></textarea>
                            </div>
                        </div>                        
                    </div>
                    <!-- /.form-group -->
                    
                    <div class="form-group">
                        <div class="alert alert-info"><p>Ideal image dimensions: 360 x 360 </p></div>
                        
                        <label>Image 2</label>
                        <div class="row">
                            <div class="col-md-5">
                                <label>Image</label>
                                <?php echo \Assets\Admin\Controllers\Assets::instance()->fetchElementImage( 'shop_home_row_2_slug_image_2', $flash->old('shop_home.row_2.image_2.slug'), array('field'=>'shop_home[row_2][image_2][slug]') ); ?>
                            </div>                        
                            <div class="col-md-7">
                                <label>URL</label>
                                <input type="text" name="shop_home[row_2][image_2][url]" class="form-control input-sm" value="<?php echo $flash->old('shop_home.row_2.image_2.url'); ?>" />
                                <label>Header</label>
                                <input type="text" name="shop_home[row_2][image_2][header]" class="form-control input-sm" value="<?php echo $flash->old('shop_home.row_2.image_2.header'); ?>" />
                                <label>Button Label</label>
                                <input type="text" name="shop_home[row_2][image_2][label]" class="form-control input-sm" value="<?php echo $flash->old('shop_home.row_2.image_2.label'); ?>" />
                                <label>Copy</label>
                                <textarea name="shop_home[row_2][image_2][copy]" class="form-control"><?php echo $flash->old('shop_home.row_2.image_2.copy'); ?></textarea>
                            </div>
                        </div>                        
                    </div>
                    <!-- /.form-group -->
                    
                    <div class="form-group">
                        <div class="alert alert-info"><p>Ideal image dimensions: 360 x 360 </p></div>
                        
                        <label>Image 3</label>
                        <div class="row">
                            <div class="col-md-5">
                                <label>Image</label>
                                <?php echo \Assets\Admin\Controllers\Assets::instance()->fetchElementImage( 'shop_home_row_2_slug_image_3', $flash->old('shop_home.row_2.image_3.slug'), array('field'=>'shop_home[row_2][image_3][slug]') ); ?>
                            </div>                        
                            <div class="col-md-7">
                                <label>URL</label>
                                <input type="text" name="shop_home[row_2][image_3][url]" class="form-control input-sm" value="<?php echo $flash->old('shop_home.row_2.image_3.url'); ?>" />
                                <label>Header</label>
                                <input type="text" name="shop_home[row_2][image_3][header]" class="form-control input-sm" value="<?php echo $flash->old('shop_home.row_2.image_3.header'); ?>" />
                                <label>Button Label</label>
                                <input type="text" name="shop_home[row_2][image_3][label]" class="form-control input-sm" value="<?php echo $flash->old('shop_home.row_2.image_3.label'); ?>" />
                                <label>Copy</label>
                                <textarea name="shop_home[row_2][image_3][copy]" class="form-control"><?php echo $flash->old('shop_home.row_2.image_3.copy'); ?></textarea>
                            </div>
                        </div>                        
                    </div>
                    <!-- /.form-group -->
                    
                    <div class="form-group">
                        <div class="alert alert-info"><p>Ideal image dimensions: 360 x 360 </p></div>
                        
                        <label>Image 4</label>
                        <div class="row">
                            <div class="col-md-5">
                                <label>Image</label>
                                <?php echo \Assets\Admin\Controllers\Assets::instance()->fetchElementImage( 'shop_home_row_2_slug_image_4', $flash->old('shop_home.row_2.image_4.slug'), array('field'=>'shop_home[row_2][image_4][slug]') ); ?>
                            </div>                        
                            <div class="col-md-7">
                                <label>URL</label>
                                <input type="text" name="shop_home[row_2][image_4][url]" class="form-control input-sm" value="<?php echo $flash->old('shop_home.row_2.image_4.url'); ?>" />
                                <label>Header</label>
                                <input type="text" name="shop_home[row_2][image_4][header]" class="form-control input-sm" value="<?php echo $flash->old('shop_home.row_2.image_4.header'); ?>" />
                                <label>Button Label</label>
                                <input type="text" name="shop_home[row_2][image_4][label]" class="form-control input-sm" value="<?php echo $flash->old('shop_home.row_2.image_4.label'); ?>" />
                                <label>Copy</label>
                                <textarea name="shop_home[row_2][image_4][copy]" class="form-control"><?php echo $flash->old('shop_home.row_2.image_4.copy'); ?></textarea>
                            </div>
                        </div>                        
                    </div>
                    <!-- /.form-group -->
                    
                </div>
                
                <div class="tab-pane fade" id="tab-row-3">
                    <h4>Row 3</h4>
                    
                    <hr />
                    
                    <div class="form-group">
                        <div class="alert alert-info"><p>Ideal image dimensions: 360 x 360 </p></div>
                        
                        <label>Image 1</label>
                        <div class="row">
                            <div class="col-md-5">
                                <label>Image</label>
                                <?php echo \Assets\Admin\Controllers\Assets::instance()->fetchElementImage( 'shop_home_row_3_slug_image_1', $flash->old('shop_home.row_3.image_1.slug'), array('field'=>'shop_home[row_3][image_1][slug]') ); ?>
                            </div>                        
                            <div class="col-md-7">
                                <label>URL</label>
                                <input type="text" name="shop_home[row_3][image_1][url]" class="form-control input-sm" value="<?php echo $flash->old('shop_home.row_3.image_1.url'); ?>" />
                                <label>Header</label>
                                <input type="text" name="shop_home[row_3][image_1][header]" class="form-control input-sm" value="<?php echo $flash->old('shop_home.row_3.image_1.header'); ?>" />
                                <label>Button Label</label>
                                <input type="text" name="shop_home[row_3][image_1][label]" class="form-control input-sm" value="<?php echo $flash->old('shop_home.row_3.image_1.label'); ?>" />
                                <label>Copy</label>
                                <textarea name="shop_home[row_3][image_1][copy]" class="form-control"><?php echo $flash->old('shop_home.row_3.image_1.copy'); ?></textarea>
                            </div>
                        </div>                        
                    </div>
                    <!-- /.form-group -->
                    
                    <div class="form-group">
                        <div class="alert alert-info"><p>Ideal image dimensions: 360 x 360 </p></div>
                        
                        <label>Image 2</label>
                        <div class="row">
                            <div class="col-md-5">
                                <label>Image</label>
                                <?php echo \Assets\Admin\Controllers\Assets::instance()->fetchElementImage( 'shop_home_row_3_slug_image_2', $flash->old('shop_home.row_3.image_2.slug'), array('field'=>'shop_home[row_3][image_2][slug]') ); ?>
                            </div>                        
                            <div class="col-md-7">
                                <label>URL</label>
                                <input type="text" name="shop_home[row_3][image_2][url]" class="form-control input-sm" value="<?php echo $flash->old('shop_home.row_3.image_2.url'); ?>" />
                                <label>Header</label>
                                <input type="text" name="shop_home[row_3][image_2][header]" class="form-control input-sm" value="<?php echo $flash->old('shop_home.row_3.image_2.header'); ?>" />
                                <label>Button Label</label>
                                <input type="text" name="shop_home[row_3][image_2][label]" class="form-control input-sm" value="<?php echo $flash->old('shop_home.row_3.image_2.label'); ?>" />
                                <label>Copy</label>
                                <textarea name="shop_home[row_3][image_2][copy]" class="form-control"><?php echo $flash->old('shop_home.row_3.image_2.copy'); ?></textarea>
                            </div>
                        </div>                        
                    </div>
                    <!-- /.form-group -->
                    
                    <div class="form-group">
                        <div class="alert alert-info"><p>Ideal image dimensions: 360 x 360 </p></div>
                        
                        <label>Image 3</label>
                        <div class="row">
                            <div class="col-md-5">
                                <label>Image</label>
                                <?php echo \Assets\Admin\Controllers\Assets::instance()->fetchElementImage( 'shop_home_row_3_slug_image_3', $flash->old('shop_home.row_3.image_3.slug'), array('field'=>'shop_home[row_3][image_3][slug]') ); ?>
                            </div>                        
                            <div class="col-md-7">
                                <label>URL</label>
                                <input type="text" name="shop_home[row_3][image_3][url]" class="form-control input-sm" value="<?php echo $flash->old('shop_home.row_3.image_3.url'); ?>" />
                                <label>Header</label>
                                <input type="text" name="shop_home[row_3][image_3][header]" class="form-control input-sm" value="<?php echo $flash->old('shop_home.row_3.image_3.header'); ?>" />
                                <label>Button Label</label>
                                <input type="text" name="shop_home[row_3][image_3][label]" class="form-control input-sm" value="<?php echo $flash->old('shop_home.row_3.image_3.label'); ?>" />
                                <label>Copy</label>
                                <textarea name="shop_home[row_3][image_3][copy]" class="form-control"><?php echo $flash->old('shop_home.row_3.image_3.copy'); ?></textarea>
                            </div>
                        </div>                        
                    </div>
                    <!-- /.form-group -->
                    
                    <div class="form-group">
                        <div class="alert alert-info"><p>Ideal image dimensions: 360 x 360 </p></div>
                        
                        <label>Image 4</label>
                        <div class="row">
                            <div class="col-md-5">
                                <label>Image</label>
                                <?php echo \Assets\Admin\Controllers\Assets::instance()->fetchElementImage( 'shop_home_row_3_slug_image_4', $flash->old('shop_home.row_3.image_4.slug'), array('field'=>'shop_home[row_3][image_4][slug]') ); ?>
                            </div>                        
                            <div class="col-md-7">
                                <label>URL</label>
                                <input type="text" name="shop_home[row_3][image_4][url]" class="form-control input-sm" value="<?php echo $flash->old('shop_home.row_3.image_4.url'); ?>" />
                                <label>Header</label>
                                <input type="text" name="shop_home[row_3][image_4][header]" class="form-control input-sm" value="<?php echo $flash->old('shop_home.row_3.image_4.header'); ?>" />
                                <label>Button Label</label>
                                <input type="text" name="shop_home[row_3][image_4][label]" class="form-control input-sm" value="<?php echo $flash->old('shop_home.row_3.image_4.label'); ?>" />
                                <label>Copy</label>
                                <textarea name="shop_home[row_3][image_4][copy]" class="form-control"><?php echo $flash->old('shop_home.row_3.image_4.copy'); ?></textarea>
                            </div>
                        </div>                        
                    </div>
                    <!-- /.form-group -->
                    
                </div>                
                
            </div>

        </div>
    </div>

</form>

</div>