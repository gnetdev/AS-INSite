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
                    <a href="#tab-general" data-toggle="tab"> Pricing Table </a>
                </li>
            </ul>
        </div>

        <div class="col-lg-10 col-md-9 col-sm-8">

            <div class="tab-content stacked-content">
                
                <div class="tab-pane fade in active" id="tab-general">

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Shipping cost for orders over Rs.1000</label>
                                <input type="text" name="india[shipping][over_1000]" class="form-control" value="<?php echo $flash->old('india.shipping.over_1000'); ?>" placeholder='e.g. 0' />
                            </div>
                            <!-- /.form-group -->
                        </div>                        
                        <div class="col-md-8">
                                                        
                        </div>
                    </div>                
                
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Shipping cost for orders between Rs.500-Rs.1000</label>
                                <input type="text" name="india[shipping][500_1000]" class="form-control" value="<?php echo $flash->old('india.shipping.500_1000'); ?>" placeholder='e.g. 99' />
                            </div>
                            <!-- /.form-group -->
                        </div>                        
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Shipping cost for orders under Rs.500</label>
                                <input type="text" name="india[shipping][under_500]" class="form-control" value="<?php echo $flash->old('india.shipping.under_500'); ?>" placeholder='e.g. 49' />
                            </div>
                            <!-- /.form-group -->
                        </div>                        
                        <div class="col-md-8">
                                                        
                        </div>
                    </div>
                    
                </div>                
                
            </div>

        </div>
    </div>

</form>

</div>