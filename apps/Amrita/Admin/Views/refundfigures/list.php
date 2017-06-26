<div class="row">
	<div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
		<h1 class="page-title txt-color-blueDark">
			<i class="fa fa-table fa-fw "></i> 
				Refund Figures 
			<span> > 
				List
			</span>
		</h1>
	</div>
	<div class="col-xs-12 col-sm-5 col-md-5 col-lg-8">
        <a class="btn btn-success pull-right" href="./admin/amrita/refundfigures/fetch-and-save">Fetch and Process Immediately</a>
	</div>
</div>

<form action="./admin/amrita/refundfigures" class="searchForm" method="post">

    <div class="no-padding">

        <div class="row">
            <div class="col-xs-12 col-sm-5 col-md-5 col-lg-8">

                <ul class="list-filters list-unstyled list-inline">
                    <li>
                        <select name="filter[recent_error]" class="form-control" onchange="this.form.submit();">
                            <option value="">-- Recent Export Error? --</option>
                            <option value="1" <?php if ($state->get('filter.recent_error')) { echo "selected='selected'"; } ?>>Yes</option>
                            <option value="0" <?php if ($state->get('filter.recent_error') == '0') { echo "selected='selected'"; } ?>>No</option>                            
                        </select>
                    </li>                                    
                    <li>
                        <select name="filter[has_ns_so_id]" class="form-control" onchange="this.form.submit();">
                            <option value="">-- Has a Netsuite Sales Order ID? --</option>
                            <option value="1" <?php if ($state->get('filter.has_ns_so_id') == '1') { echo "selected='selected'"; } ?>>Yes</option>
                            <option value="0" <?php if ($state->get('filter.has_ns_so_id') == '0') { echo "selected='selected'"; } ?>>No</option>                            
                        </select>
                    </li>                                                        
                </ul>    
        
            </div>
            <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
                <div class="form-group">
                    <div class="input-group">
                        <input class="form-control" type="text" name="filter[keyword]" placeholder="Search..." maxlength="200" value="<?php echo $state->get('filter.keyword'); ?>"> 
                        <span class="input-group-btn">
                            <input class="btn btn-primary" type="submit" onclick="this.form.submit();" value="Search" />
                            <button class="btn btn-danger" type="button" onclick="Dsc.resetFormFilters(this.form);">Reset</button>
                        </span>
                    </div>
                </div>
            </div>
        </div>    
    
        <div class="widget-body-toolbar">
    
            <div class="row">
                <div class="col-xs-12 col-sm-5 col-md-3 col-lg-3">
                    <span class="pagination"></span>
                </div>    
                <div class="col-xs-12 col-sm-7 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3">
                    <div class="row text-align-right">
                        <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10">
                            <?php if (!empty($paginated->total_pages) && $paginated->total_pages > 1) { ?>
                                <?php echo $paginated->serve(); ?>
                            <?php } ?>
                        </div>
                        <?php if (!empty($paginated->items)) { ?>
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <span class="pagination">
                            <?php echo $paginated->getLimitBox( $state->get('list.limit') ); ?>
                            </span>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        
        </div>
        <!-- /.widget-body-toolbar -->
        
        <input type="hidden" name="list[order]" value="<?php echo $state->get('list.order'); ?>" />
        <input type="hidden" name="list[direction]" value="<?php echo $state->get('list.direction'); ?>" />
    
        <div class="table-responsive datatable dt-wrapper dataTables_wrapper">
        
        <table class="table table-striped table-bordered table-hover table-highlight table-checkable">
    		<thead>
    			<tr>
    				<th class="col-md-1" data-sortable="ns_id">Netsuite ID</th>    			
    				<th class="col-md-1" data-sortable="Netsuite Sales Order Number.value">NS Sales Order</th>
    				<th class="col-md-2">Item and Amount</th>
    				<th class="col-md-2">Shop Order ID</th>
    				<th class="col-md-2" data-sortable="processed_time.time">Processed</th>
    				<th class="col-md-3">Processing Result</th>
    				<th class="col-md-1"></th>
    			</tr>
    		</thead>
    		<tbody>    
        
            <?php if (!empty($paginated->items)) { ?>
            
                <?php foreach($paginated->items as $item) { ?>
                <tr>
                    <td class="">
                        <a href="./admin/amrita/refundfigures/view/<?php echo $item->{'id'}; ?>">
                        <?php echo $item->{'ns_id'}; ?>
                        </a>
                        <div>
                            Line #<?php echo $item->{'line_id.value'}; ?>
                        </div>                        
                    </td>
                
                    <td class="">
                        <a href="./admin/amrita/refundfigures/view/<?php echo $item->{'id'}; ?>">
                            <?php if (empty($item->{'Netsuite Sales Order Number.value'})) {
                                $item = $item->findAndSetSalesOrderNumber();
                            } ?>
                            <?php echo $item->{'Netsuite Sales Order Number.value'}; ?>
                        </a>                        
                    </td>
                    
                    <td>
                        <div>
                            <?php echo $item->{'Item.column_value.name'}; ?>
                        </div>
                        <div>
                            <?php echo $amount = 0 + $item->{'associated Refund amount.value'}; ?>
                        </div>     
                        <?php switch($item->{'Status.value'}) {
                            case "cancelled":
                                $class="label-danger";
                                break;
                            case "pendingReceipt":
                                $class="label-warning";
                                break;
                            case "closed":
                                $class="label-info";
                                break;
                            case "refunded":
                                $class="label-success";
                                break;
                            default:
                                $class="label-default";
                                break;
                        } ?>
                        <div class="label <?php echo $class; ?>">
                            <?php echo $item->{'Status.value'}; ?>
                        </div>               
                    </td>
    
                    <td class="">
                        <?php
                        if ($order = $item->shopOrder()) {
                            echo "<a href='./admin/shop/order/edit/". $order->id ."'>" . $order->id . "</a>";
                        }
                        else {
                            echo $item->__shopOrderError;
                        }
                        ?>
                    </td>
                    
                    <td>
                        <?php echo $item->{'processed'} ? date('Y-m-d g:ia', $item->{'processed_time.time'}) : 'Not Processed'; ?>
                    </td>
                    
                    <td>
                        <?php echo $item->{'processed'} ? $item->processing_result : null; ?>
                    </td>
                    
                    <td>
                        <a href="./admin/amrita/refundfigures/export/<?php echo $item->{'id'}; ?>">
                        Process Now
                        </a>
                    </td>
                    
                </tr>
            <?php } ?>
            
            <?php } else { ?>
                <tr>
                <td colspan="100">
                    <div class="">No items found.</div>
                </td>
                </tr>
            <?php } ?>
    
            </tbody>
        </table>
        
        </div>
        <!-- /.table-responsive .datatable .dt-wrapper -->
        
        <div class="dt-row dt-bottom-row">
            <div class="row">
                <div class="col-sm-10">
                    <?php if (!empty($paginated->total_pages) && $paginated->total_pages > 1) { ?>
                        <?php echo $paginated->serve(); ?>
                    <?php } ?>
                </div>
                <div class="col-sm-2">
                    <div class="datatable-results-count pull-right">
                        <span class="pagination">
                            <?php echo (!empty($paginated->total_pages)) ? $paginated->getResultsCounter() : null; ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- /.no-padding -->
    
</form>