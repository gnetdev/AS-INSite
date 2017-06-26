<div class="alert alert-info">
    <p><b>Note:</b> Coupons will only be applied if <u>all</u> of the conditions below are fulfilled.  For example, if you have check the "wholesale" user group, add "t-shirt" to the list of Products, and set a $90 order minimum, the coupon will only be applied if the user is a wholesaler <i>AND</i> their order is above $90 <i>AND</i> they have a t-shirt in their cart.</p>
</div>

<div class="row">
    <div class="col-md-2">
        
        <h3>Required Products</h3>
        <p class="help-block">This coupon will only be applied if the shopper has one of these products in their cart.</p>
                
    </div>
    <!-- /.col-md-2 -->
                
    <div class="col-md-10">
    
        <div class="form-group">
            <label>Search</label>
            <div class="input-group">
                <input id="required_products" name="required_products" value="<?php echo implode(",", (array) $flash->old('required_products') ); ?>" type="text" class="form-control" /> 
            </div>
            <!-- /.form-group -->        
            
        </div>
        <!-- /.form-group -->
        
    </div>
    <!-- /.col-md-10 -->
    
</div>
<!-- /.row -->

<hr />

<div class="row">
    <div class="col-md-2">
        
        <h3>Required Collection</h3>
        <p class="help-block">This coupon will only be applied if the shopper has a product from this collection in their cart. Also, the coupon will be applied only on products from these collections.</p>
                
    </div>
    <!-- /.col-md-2 -->
                
    <div class="col-md-10">
    
        <div class="form-group">
            <label>Search</label>
            <div class="input-group">
                <input id="required_collections" name="required_collections" value="<?php echo implode(",", (array) $flash->old('required_collections') ); ?>" type="text" class="form-control" /> 
            </div>
            <!-- /.form-group -->        
            
        </div>
        <!-- /.form-group -->
        
    </div>
    <!-- /.col-md-10 -->
    
</div>
<!-- /.row -->

<hr />

<div class="row">
    <div class="col-md-2">
        
        <h3>Required Coupons</h3>
        <p class="help-block">This coupon will only be applied if the shopper has one of these OTHER coupons already in their cart.</p>
                
    </div>
    <!-- /.col-md-2 -->
                
    <div class="col-md-10">
    
        <div class="form-group">
            <label>Search</label>
            <div class="input-group">
                <input id="required_coupons" name="required_coupons" value="<?php echo implode(",", (array) $flash->old('required_coupons') ); ?>" type="text" class="form-control" /> 
            </div>
            <!-- /.form-group -->        
            
        </div>
        <!-- /.form-group -->
        
    </div>
    <!-- /.col-md-10 -->
    
</div>
<!-- /.row -->

<hr />

<div class="row">
    <div class="col-md-2">
        
        <h3>Minimums</h3>
        <p class="help-block">The coupon will only be applied if these minimums are met.</p>
                
    </div>
    <!-- /.col-md-2 -->
                
    <div class="col-md-10">
    
        <div class="form-group">
            <label>Subtotal (before discounts, gift cards, store credits, taxes, and shipping totals are applied)</label>
            <div class="row">
                <div class="col-md-6">
                    <input type="text" name="min_subtotal_amount" placeholder="0.00" value="<?php echo $flash->old('min_subtotal_amount'); ?>" class="form-control" />
                </div>
                <div class="col-md-6">
                    <input type="text" name="min_subtotal_amount_currency" placeholder="USD" value="<?php echo $flash->old('min_subtotal_amount_currency'); ?>" class="form-control" />
                </div>                
            </div>
        </div>
        <!-- /.form-group -->    
    
        <div class="form-group">
            <label>Total (after gift cards, store credits, and all other discounts are applied, but before taxes and shipping totals are applied)</label>
            <div class="row">
                <div class="col-md-6">
                    <input type="text" name="min_order_amount" placeholder="0.00" value="<?php echo $flash->old('min_order_amount'); ?>" class="form-control" />
                </div>
                <div class="col-md-6">
                    <input type="text" name="min_order_amount_currency" placeholder="USD" value="<?php echo $flash->old('min_order_amount_currency'); ?>" class="form-control" />
                </div>                
            </div>
        </div>
        <!-- /.form-group -->
        
    </div>
    <!-- /.col-md-10 -->
    
</div>
<!-- /.row -->

<hr />

<div class="row">
    <div class="col-md-2">
        
        <h3>Geolocation</h3>
        <p class="help-block">The coupon will only be applied if the specified order address is in the selected regions.</p>
                
    </div>
    <!-- /.col-md-2 -->
                
    <div class="col-md-10">
        <div class="form-group">
            <label>Address Type</label>
            <select name="geo_address_type" class="form-control">
                <option value="shipping" <?php echo ($flash->old('geo_address_type') == 'shipping') ? "selected='selected'" : null; ?>>Shipping</option>
                <option value="billing" <?php echo ($flash->old('geo_address_type') == 'billing') ? "selected='selected'" : null; ?>>Billing</option>
            </select>            
        </div>
        <!-- /.form-group -->    
    
        <div class="form-group">
            <label>Countries</label>
            <input id="geo_countries" name="geo_countries" value="<?php echo implode(",", (array) $flash->old('geo_countries') ); ?>" type="text" class="form-control" />
        </div>
        <!-- /.form-group -->
        
        <div class="form-group">
            <label>Regions</label>
            <input id="geo_regions" name="geo_regions" value="<?php echo implode(",", (array) $flash->old('geo_regions') ); ?>" type="text" class="form-control" />
        </div>
        <!-- /.form-group -->
        
    </div>
    <!-- /.col-md-10 -->
    
</div>
<!-- /.row -->

<script>
jQuery(document).ready(function() {
    
    jQuery("#required_products").select2({
        allowClear: true, 
        placeholder: "Search...",
        multiple: true,
        minimumInputLength: 3,
        ajax: {
            url: "./admin/shop/products/forSelection",
            dataType: 'json',
            data: function (term, page) {
                return {
                    q: term
                };
            },
            results: function (data, page) {
                return {results: data.results};
            }
        }
        <?php if ($flash->old('required_products')) { ?>
        , initSelection : function (element, callback) {
            var data = <?php echo json_encode( \Shop\Models\Products::forSelection( array('_id'=>array('$in'=>array_map( function($input){ return new \MongoId($input); }, (array) $flash->old('required_products') ) ) ) ) ); ?>;
            callback(data);            
        }
        <?php } ?>
    });

    jQuery("#required_coupons").select2({
        allowClear: true, 
        placeholder: "Search...",
        multiple: true,
        minimumInputLength: 3,
        ajax: {
            url: "./admin/shop/coupons/forSelection",
            dataType: 'json',
            data: function (term, page) {
                return {
                    q: term
                };
            },
            results: function (data, page) {
                return {results: data.results};
            }
        }
        <?php if ($flash->old('required_coupons')) { ?>
        , initSelection : function (element, callback) {
            var data = <?php echo json_encode( \Shop\Models\Coupons::forSelection( array('_id'=>array('$in'=>array_map( function($input){ return new \MongoId($input); }, (array) $flash->old('required_coupons') ) ) ) ) ); ?>;
            callback(data);            
        }
        <?php } ?>
    });

    jQuery("#required_collections").select2({
        allowClear: true, 
        placeholder: "Search...",
        multiple: true,
        minimumInputLength: 3,
        ajax: {
            url: "./admin/shop/collections/forSelection",
            dataType: 'json',
            data: function (term, page) {
                return {
                    q: term
                };
            },
            results: function (data, page) {
                return {results: data.results};
            }
        }
        <?php if ($flash->old('required_collections')) { ?>
        , initSelection : function (element, callback) {
            var data = <?php echo json_encode( \Shop\Models\Collections::forSelection( array('_id'=>array('$in'=>array_map( function($input){ return new \MongoId($input); }, (array) $flash->old('required_collections') ) ) ) ) ); ?>;
            callback(data);            
        }
        <?php } ?>
    });
    
        
    jQuery("#geo_countries").select2({
        allowClear: true, 
        placeholder: "Search...",
        multiple: true,
        minimumInputLength: 3,
        ajax: {
            url: "./admin/shop/countries/forSelection",
            dataType: 'json',
            data: function (term, page) {
                return {
                    q: term
                };
            },
            results: function (data, page) {
                return {results: data.results};
            }
        }
        <?php if ($flash->old('geo_countries')) { ?>
        , initSelection : function (element, callback) {
            var data = <?php echo json_encode( \Shop\Models\Countries::forSelection( array('isocode_2'=>array('$in'=>$flash->old('geo_countries') ) ) ) ); ?>;
            callback(data);            
        }
        <?php } ?>    
    });
        
    jQuery("#geo_regions").select2({
        allowClear: true, 
        placeholder: "Search...",
        multiple: true,
        minimumInputLength: 3,
        ajax: {
            url: "./admin/shop/regions/forSelection",
            dataType: 'json',
            data: function (term, page) {
                return {
                    q: term
                };
            },
            results: function (data, page) {
                return {results: data.results};
            }
        }
        <?php if ($flash->old('geo_regions')) { ?>
        , initSelection : function (element, callback) {
            var data = <?php echo json_encode( \Shop\Models\Regions::initSelection( $flash->old('geo_regions') ) ); ?>;
            callback(data);            
        }
        <?php } ?>    
    });
});
</script>

<hr />

<div class="row">
    <div class="col-md-2">
        
        <h3>Shopper Groups</h3>
                        
    </div>
    <!-- /.col-md-2 -->
                
    <div class="col-md-10">
    
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <?php if ((array) $groups = \Users\Models\Groups::find() ) { ?>
                    <div class="max-height-200 list-group-item">
                        <?php foreach ($groups as $one) { ?>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="groups[]" class="icheck-input" value="<?php echo $one->_id; ?>" <?php if (in_array($one->_id, (array) $flash->old('groups'))) { echo "checked='checked'"; } ?>>
                                <?php echo $one->title;  ?>
                            </label>
                        </div>
                        <?php } ?> 
                        
                    </div>
                    <?php } ?>
                    <input type="hidden" name="groups[]" value="" />                        
                </div>
                <!-- /.form-group -->
            </div>
            <div class="col-md-6">
                <label>Matching Method</label>
                <select name="groups_method" class="form-control">
                    <option value="one" <?php if ($flash->old('groups_method') == "one") { echo "selected='selected'"; } ?>>At least one</option>
                    <option value="all" <?php if ($flash->old('groups_method') == "all") { echo "selected='selected'"; } ?>>Must be in all</option>
                    <option value="none" <?php if ($flash->old('groups_method') == "none") { echo "selected='selected'"; } ?>>Cannot be in any</option>
                </select>

            </div>            
        </div>
        
    </div>
    <!-- /.col-md-10 -->
    
</div>
<!-- /.row -->

