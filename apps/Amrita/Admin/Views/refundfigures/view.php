<div class="well">

    <form method="post">
    
    <div class="clearfix">

        <div class="pull-right">
            <a class="btn btn-default" href="./admin/amrita/refundfigures">Close</a>
        </div>

    </div>
    
    <hr />
    
    <ul class="nav nav-tabs">
        <li class="active">
            <a href="#tab-basics" data-toggle="tab"> Basics </a>
        </li>
        <li>
            <a href="#tab-dump" data-toggle="tab"> Data Dump </a>
        </li>
    </ul>
    
    <div class="tab-content">

        <div class="tab-pane active" id="tab-basics">
        
            <div class="row">
                <div class="col-md-2">
                    
                    <h3>Basics</h3>
                            
                </div>
                <!-- /.col-md-2 -->
                            
                <div class="col-md-10">
                
                    <div class="list-group-item">
                        <div class="row">
                            <div class="col-md-3">
                                <label>Processed?</label>
                            </div>
                            <div class="col-md-9">
                                <?php echo $item->{'processed'} ? 'Yes' : 'No'; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="list-group-item">
                        <div class="row">
                            <div class="col-md-3">
                                <label>Processing result:</label>
                            </div>
                            <div class="col-md-9">
                                <?php if ($item->processed) {
                                    echo $item->processing_result;
                                } else {
                                    echo "n/a";
                                } ?>
                            </div>
                        </div>
                    </div>                    
                                        
                </div>
                <!-- /.col-md-10 -->
                
            </div>
            <!-- /.row -->
            
            <hr />
            
            <div class="row">
                <div class="col-md-2">
                    
                    <h3>Export Data</h3>
                    <p class="help-block">as seen by the Exporter</p>
                            
                </div>
                <!-- /.col-md-2 -->
                            
                <div class="col-md-10">
                
                    <div class="list-group-item">
                        <div class="row">
                            <div class="col-md-3">
                                <label>Netsuite ID</label>
                            </div>
                            <div class="col-md-9">
                                <?php echo $item->{'ns_id'}; ?>                                    
                            </div>
                        </div>
                    </div>
                
                    <div class="list-group-item">
                        <div class="row">
                            <div class="col-md-3">
                                <label>Tienda Order</label>
                            </div>
                            <div class="col-md-9">
                                <?php
                                if ($order = $item->shopOrder()) {
                                    echo "<a href='./admin/shop/order/edit/". $order->id ."'>" . $order->id . "</a>";
                                }
                                else {
                                    echo $item->__shopOrderError;
                                }
                                ?>                                    
                            </div>
                        </div>
                    </div>
                    
                    <div class="list-group-item">
                        <div class="row">
                            <div class="col-md-3">
                                <label>Item</label>
                            </div>
                            <div class="col-md-9">
                                <?php echo $item->{'Item.column_value.name'}; ?>
                            </div>
                        </div>
                    </div>                    
                    
                    <div class="list-group-item">
                        <div class="row">
                            <div class="col-md-3">
                                <label>Amount</label>
                            </div>
                            <div class="col-md-9">
                                <?php echo $amount = 0 + $item->{'associated Refund amount.value'}; ?>
                            </div>
                        </div>
                    </div>   
                    
                    <div class="list-group-item">
                        <div class="row">
                            <div class="col-md-3">
                                <label>Status</label>
                            </div>
                            <div class="col-md-9">
                                <?php echo $item->{'Status.value'}; ?>
                            </div>
                        </div>
                    </div>                                                         
                    
                </div>
            </div>        
        
        
        </div>
        <!-- /.tab-pane -->
        
        <div class="tab-pane" id="tab-dump">

            <?php if (!empty($item)) { ?>
            <div>
                <?php echo \Dsc\Debug::dump( $item->cast() ); ?>
            </div>
            <?php } ?>
                                
        </div>
        <!-- /.tab-pane -->
        
    </div>
    
    </form>
    
</div>