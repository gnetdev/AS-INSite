<div class="row">
    <div class="col-md-2">
        
        <h3>Tags</h3>
        
                
    </div>
    <!-- /.col-md-2 -->
                
    <div class="col-md-10">

        <div class="portlet">

            <div class="portlet-header">

                <h3>Tags</h3>

            </div>
            <!-- /.portlet-header -->

            <div class="portlet-content">
            
                <div class="input-group">
                    <input name="tags" data-tags='<?php echo json_encode( $all_tags ); ?>' value="<?php echo implode(",", (array) $flash->old('tags') ); ?>" type="text" class="form-control ui-select2-tags" /> 
                </div>
                <!-- /.form-group -->

            </div>
            <!-- /.portlet-content -->

        </div>
        <!-- /.portlet -->

    </div>
    <!-- /.col-md-10 -->
    
</div>
<!-- /.row -->