<div class="row">
    <div class="col-md-2">
        
        <h3>Products</h3>
        <p class="help-block">Manually add/remove products.</p>
                
    </div>
    <!-- /.col-md-2 -->
                
    <div class="col-md-10">
    
        <div class="form-group">
            <label>Search</label>
            <div class="input-group">
                <input id="products" name="__products" value="<?php echo implode(",", (array) \Shop\Models\Categories::productIds( $flash->old('_id') ) ); ?>" type="text" class="form-control" /> 
            </div>
            <!-- /.form-group -->        
            
        </div>
        <!-- /.form-group -->
        
    </div>
    <!-- /.col-md-10 -->
    
</div>
<!-- /.row -->

<script>
jQuery(document).ready(function() {
    
    jQuery("#products").select2({
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
        <?php if ($flash->old('_id')) { ?>
        , initSelection : function (element, callback) {
            var data = <?php echo json_encode( \Shop\Models\Products::forSelection( array('_id'=>array('$in'=>array_map( function($input){ return new \MongoId($input); }, (array) \Shop\Models\Categories::productIds( $flash->old('_id') ) ) ) ) ) ); ?>;
            callback(data);            
        }
        <?php } ?>
    });

});
</script>