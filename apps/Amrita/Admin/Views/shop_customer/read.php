
<div class="panel panel-default">
    <div class="panel-body">
    
        <div class="row">
            <div class="col-md-2">
                
                <h4>Netsuite Record</h4>
                        
            </div>
            <!-- /.col-md-2 -->
            
            <div class="col-md-10">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-2">
                            ID: <?php echo $item->{'netsuite.id'}; ?>
                        </div>
                        <div class="col-md-10">
                            <a class="btn btn-default" target="_blank" href="https://system.netsuite.com/app/common/entity/custjob.nl?id=<?php echo $item->{'netsuite.id'}; ?>">View in Netsuite</a>                            
                        </div>                        
                    </div>
                </div>
                <!-- /.form-group -->
            </div>
            <!-- /.col-md-10 -->                        
            
        </div>
        <!-- /.row -->    

        <div class="row">
            <div class="col-md-2">
                
                <h4>Last Order Sync</h4>
                        
            </div>
            <!-- /.col-md-2 -->
                        
            <div class="col-md-10">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-2">
                            <?php echo $flash->old('netsuite.last_order_sync_datetime'); ?>
                        </div>
                        <div class="col-md-10">
                            <a class="btn btn-default" href="./admin/amrita/customer/sync/<?php echo $item->id; ?>">Sync Now</a>
                            <a class="btn btn-warning" href="./admin/amrita/customer/downloadOrderHistorySince/<?php echo $item->id; ?>">Download Entire Order History</a>
                            
                        </div>                        
                    </div>
                </div>
                <!-- /.form-group -->
            
            </div>
            <!-- /.col-md-10 -->
            
        </div>
        <!-- /.row -->

    </div>
</div>