<div class="row">
    <div class="col-md-2">
        
        <h3>Publication and Visibility</h3>
        
                
    </div>
    <!-- /.col-md-2 -->
                
    <div class="col-md-10">
    
        <div class="portlet">

            <div class="portlet-header">
    
                <h3>Publication</h3>
    
            </div>
            <!-- /.portlet-header -->
    
            <div class="portlet-content">
            
                <div class="form-group">
                    <label>Status:</label>
    
                    <select name="publication[status]" class="form-control">
                        <option value="published" <?php if ($flash->old('publication.status') == 'published') { echo "selected='selected'"; } ?>>Published</option>
                        <option value="unpublished" <?php if ($flash->old('publication.status') == 'unpublished') { echo "selected='selected'"; } ?>>Unpublished</option>
                        <option value="inactive" <?php if ($flash->old('publication.status') == 'inactive') { echo "selected='selected'"; } ?>>Inactive</option>
                    </select>
                
                </div>
                <div class="form-group">
                    <label>Start:</label>
                    <div class="row">
                        <div class="col-md-6">
                            <input name="publication[start_date]" value="<?php echo $flash->old('publication.start_date', date('Y-m-d') ); ?>" class="ui-datepicker form-control" type="text" data-date-format="yyyy-mm-dd" data-date-today-highlight="true" data-date-today-btn="true">
                        </div>
                        <div class="input-group col-md-6">
                            <input name="publication[start_time]" value="<?php echo $flash->old('publication.start_time' ); ?>" type="text" class="ui-timepicker form-control" data-show-meridian="false" data-show-inputs="false">
                            <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Finish:</label>
                    <div class="row">
                        <div class="col-md-6">
                            <input name="publication[end_date]" value="<?php echo $flash->old('publication.end_date' ); ?>" class="ui-datepicker form-control" type="text" data-date-format="yyyy-mm-dd" data-date-today-highlight="true" data-date-today-btn="true">
                        </div>
                        <div class="input-group col-md-6">
                            <input name="publication[end_time]" value="<?php echo $flash->old('publication.end_time' ); ?>" type="text" class="ui-timepicker form-control" data-default-time="false" data-show-meridian="false" data-show-inputs="false">
                            <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                        </div>
                    </div>
                    <span class="help-text">Leave these blank to never unpublish.</span>
                </div>
    
            </div>
            <!-- /.portlet-content -->
    
        </div>
        <!-- /.portlet -->
        
        <hr />
        
    </div>
    <!-- /.col-md-10 -->
    
</div>
<!-- /.row -->