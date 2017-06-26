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
                    <a href="#tab-general" data-toggle="tab"> General </a>
                </li>
            </ul>
        </div>

        <div class="col-lg-10 col-md-9 col-sm-8">

            <div class="tab-content stacked-content">
                
                <div class="tab-pane fade in active" id="tab-general">

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>API Key</label>
                                <input type="text" name="mailchimp[api_key]" class="form-control" value="<?php echo $flash->old('mailchimp.api_key'); ?>" placeholder='e.g. 5324543452134554345654345' />
                                <p class="help-block">Required for the Mailchimp integration to work.</p>
                            </div>
                            <!-- /.form-group -->
                        </div>                        
                        <div class="col-md-8">
                                                        
                        </div>
                    </div>                
                
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Newsletter Unique ID</label>
                                <input type="text" name="mailchimp[main_list_id]" class="form-control" value="<?php echo $flash->old('mailchimp.main_list_id'); ?>" placeholder='e.g. ajasdh19723' />
                                <p class="help-block">You can find the Unique ID of your mailing lists in the Mailchimp interface by going to the list's Settings page.</p>
                            </div>
                            <!-- /.form-group -->
                        </div>                        
                        <div class="col-md-8">
                            <p>Customers will be subscribed to this newsletter after registration and checkout.</p>                            
                        </div>
                    </div>
                                    
                </div>                
                
            </div>

        </div>
    </div>

</form>

</div>