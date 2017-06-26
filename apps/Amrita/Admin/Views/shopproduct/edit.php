<div class="row">
    <div class="col-md-2">
        
        <h3>Custom Fields</h3>
                
    </div>
    <!-- /.col-md-2 -->
                
    <div class="col-md-10">
        
        <div class="form-group">
            <label>Measurements (amrita_measurements)</label>
            <input type="text" name="amrita_measurements" value="<?php echo htmlspecialchars( $item->exists('amrita_measurements') ? $flash->old('amrita_measurements') : $flash->old( 'amrita.measurements' ) ); ?>" class="form-control" />
        </div>
        <!-- /.form-group -->
    
    </div>
    <!-- /.col-md-10 -->
    
</div>
<!-- /.row -->

<hr/>

<div class="row">
    <div class="col-md-2">
        
        <h3>Prices Data Dump</h3>
        <p>These values have been imported from Netsuite</p>
                
    </div>
    <!-- /.col-md-2 -->
                
    <div class="col-md-10">
        
        <div class="form-group">
            <?php echo \Dsc\Debug::dump( $flash->old('prices') ); ?>
        </div>
        <!-- /.form-group -->
    
    </div>
    <!-- /.col-md-10 -->
    
</div>
<!-- /.row -->

<hr/>

<div class="row">
    <div class="col-md-2">
        
        <h3>Data Dump</h3>
        <p>These custom values have been imported from Netsuite</p>
                
    </div>
    <!-- /.col-md-2 -->
                
    <div class="col-md-10">
        
        <div class="form-group">
            <?php echo \Dsc\Debug::dump( $flash->old('amrita') ); ?>
        </div>
        <!-- /.form-group -->
    
    </div>
    <!-- /.col-md-10 -->
    
</div>
<!-- /.row -->