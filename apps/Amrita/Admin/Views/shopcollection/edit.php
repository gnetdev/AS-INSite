<div class="row">
    <div class="col-md-2">

        <h3>Custom Fields</h3>
        <p>Collections can also filter on custom fields</p>
                
    </div>
    <!-- /.col-md-2 -->
                
    <div class="col-md-10">

        <div class="form-group">
            <label>Product Type</label>
            <select name="amrita[type][]" class="form-control select2" multiple>
                <option value="">None</option>
                <?php foreach (\Shop\Models\Products::collection()->distinct('amrita.type') as $__item) { ?>
                    <option value="<?php echo $__item; ?>" <?php if (in_array($__item, (array) $flash->old('amrita.type'))) { echo "selected='selected'"; } ?>><?php echo $__item; ?></option>                    
                <?php } ?> 
            </select>
                        
        </div>
        <!-- /.form-group -->
    
    </div>
    <!-- /.col-md-10 -->
    
</div>
<!-- /.row -->