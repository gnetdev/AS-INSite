<form id="settings-form" role="form" method="post" class="clearfix">

    <div class="clearfix">
        <div class="pull-right">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            &nbsp;
            <a class="btn btn-default" href="./admin/shop/payment-methods">Close</a>
        </div>        
    </div>

    <h2>Paypal Express Settings</h2>
    
    <div class="panel panel-default">
        <div class="panel-body">
        
            <div class="row">
                <div class="col-md-2">
                    
                    <h3>Enabled</h3>
                            
                </div>
                <!-- /.col-md-2 -->
                            
                <div class="col-md-10">
                
                    <div class="form-group">
                        <label>Enable this payment method?</label>
                        <select name="enabled" class="form-control">
                            <option value="0" <?php echo !$model->{'enabled'} ? 'selected' : null; ?>>No</option>
                            <option value="1" <?php echo $model->{'enabled'} ? 'selected' : null; ?>>Yes</option>
                        </select> 
                    </div>
                    <!-- /.form-group -->
        
                </div>
                <!-- /.col-md-10 -->
                
            </div>
            <!-- /.row -->        
            
            <hr />                
        
            <div class="row">
                <div class="col-md-2">
                    
                    <h3>Mode</h3>
                            
                </div>
                <!-- /.col-md-2 -->
                            
                <div class="col-md-10">
                
                    <div class="form-group">
                        <label>Live or Test?</label>
                        <select name="settings[mode]" class="form-control">
                            <option value="test" <?php echo ($model->{'settings.mode'} == 'test') ? 'selected' : null; ?>>Test</option>
                            <option value="live" <?php echo ($model->{'settings.mode'} == 'live') ? 'selected' : null; ?>>Live</option>
                        </select> 
                    </div>
                    <!-- /.form-group -->
        
                </div>
                <!-- /.col-md-10 -->
                
            </div>
            <!-- /.row -->        
            
            <hr />        
            
            <p class="alert alert-info">
                See this page for instructions on finding your API credentials: <a href="https://developer.paypal.com/webapps/developer/docs/classic/api/apiCredentials/" target="_blank">https://developer.paypal.com/webapps/developer/docs/classic/api/apiCredentials/</a>
            </p>
        
            <div class="row">
                <div class="col-md-2">
                    
                    <h3>Test Credentials</h3>
                            
                </div>
                <!-- /.col-md-2 -->
                            
                <div class="col-md-10">
                
                    <div class="form-group">
                        <label>API Username</label>
                        <input name="settings[test][username]" value="<?php echo $model->{'settings.test.username'}; ?>" type="text" class="form-control" /> 
                    </div>
                    <!-- /.form-group -->
                    
                    <div class="form-group">
                        <label>API Password</label>
                        <input name="settings[test][password]" value="<?php echo $model->{'settings.test.password'}; ?>" type="text" class="form-control" /> 
                    </div>
                    <!-- /.form-group -->
                    
                    <div class="form-group">
                        <label>Signature</label>
                        <input name="settings[test][signature]" value="<?php echo $model->{'settings.test.signature'}; ?>" type="text" class="form-control" /> 
                    </div>
                    <!-- /.form-group -->
        
                </div>
                <!-- /.col-md-10 -->
                
            </div>
            <!-- /.row -->        
            
            <hr />
        
            <div class="row">
                <div class="col-md-2">
                    
                    <h3>Live Credentials</h3>
                            
                </div>
                <!-- /.col-md-2 -->
                            
                <div class="col-md-10">
                
                    <div class="form-group">
                        <label>API Username</label>
                        <input name="settings[live][username]" value="<?php echo $model->{'settings.live.username'}; ?>" type="text" class="form-control" /> 
                    </div>
                    <!-- /.form-group -->
                    
                    <div class="form-group">
                        <label>API Password</label>
                        <input name="settings[live][password]" value="<?php echo $model->{'settings.live.password'}; ?>" type="text" class="form-control" /> 
                    </div>
                    <!-- /.form-group -->
                    
                    <div class="form-group">
                        <label>Signature</label>
                        <input name="settings[live][signature]" value="<?php echo $model->{'settings.live.signature'}; ?>" type="text" class="form-control" /> 
                    </div>
                    <!-- /.form-group -->
        
                </div>
                <!-- /.col-md-10 -->
                
            </div>
            <!-- /.row -->
                    
        </div>
    </div>
        
</form>