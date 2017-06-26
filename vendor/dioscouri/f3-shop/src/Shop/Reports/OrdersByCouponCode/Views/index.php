<div class="row">
    <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
        <h1 class="page-title txt-color-blueDark">
            <i class="fa fa-table fa-fw "></i> Reports 
            <span> > Orders by Coupon Code </span>
        </h1>
    </div>
    <div class="col-xs-12 col-sm-5 col-md-5 col-lg-8">
        <ul id="sparks" class="list-actions list-unstyled list-inline">
            <li>
                <a class="btn btn-warning" href="./admin/shop/reports">Close Report</a>
            </li>
        </ul>
    </div>
</div>

<hr />

<form method="post" action="./admin/shop/reports/<?php echo $report->slug; ?>">

    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <div class="form-group">
                <div class="input-group">
                    <input class="form-control" type="text" name="filter[keyword]" placeholder="Search..." maxlength="200" value="<?php echo $state->get('filter.keyword'); ?>"> 
                    <span class="input-group-btn">
                        <button class="btn btn-primary" type="button" onclick="this.form.submit();">Search</button>
                    </span>
                </div>
            </div>
        </div>
    
        <div class="col-xs-12 col-sm-6">
            <div class="pull-right">
            <ul class="list-filters list-unstyled list-inline">
                <li>
                    <?php /* ?><a class="btn btn-link" href="javascript:void(0);" onclick="ShopToggleAdvancedFilters();">Advanced Filters</a> */ ?>
                </li>
                <li>
                    <button class="btn btn-sm btn-danger" type="button" onclick="Dsc.resetFormFilters(this.form);">Reset Filters</button>
                </li>                
            </ul>
            </div>
        </div>

    </div>
    
    <div id="advanced-filters" class="panel panel-default" 
    <?php 
    if (!$state->get('filter.created_after')
        && !$state->get('filter.created_before')            
    ) { ?>
        style="display: none;"
    <?php } ?>
    >
        <div class="panel-body">
            <div class="row">
                <div class="col-md-10">
                    <div class="row">
                        <div class="col-md-2">
                            <h4>Created</h4>
                        </div>
                        <div class="col-md-10">
                            <div class="input-daterange input-group" id="datepicker">
                                <input type="text" name="filter[created_after]" value="<?php echo $state->get('filter.created_after'); ?>" class="input-sm ui-datepicker form-control" data-date-format="yyyy-mm-dd" data-date-today-highlight="true" data-date-today-btn="true" />
                                <span class="input-group-addon">to</span>
                                <input type="text" name="filter[created_before]" value="<?php echo $state->get('filter.created_before'); ?>" class="input-sm ui-datepicker form-control" data-date-format="yyyy-mm-dd" data-date-today-highlight="true" data-date-today-btn="true" />
                            </div>
                        </div>                
                    </div>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary pull-right">Go</button>
                </div>
            </div>   
        </div> 
    </div>
    
    <script>
    ShopToggleAdvancedFilters = function(el) {
        var filters = jQuery('#advanced-filters');
        if (filters.is(':hidden')) {
            filters.slideDown();        
        } else {
        	filters.slideUp();
        }
    }
    </script>    

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-lg-3">
                    <span class="pagination">
                        <?php if (!empty($paginated->items)) { ?>
                            <?php echo $paginated->getLimitBox( $state->get('list.limit') ); ?>
                        <?php } ?>    
                    </span>
                </div>
                <div class="col-xs-12 col-sm-6 col-lg-6 col-lg-offset-3">
                    <div class="text-align-right">
                        <?php if (!empty($paginated->total_pages) && $paginated->total_pages > 1) { ?>
                            <?php echo $paginated->serve(); ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-body">

            <?php if (!empty($paginated->items)) { ?>
                <ul class="list-group">
                <li class="list-group-item">
                    <div class="row">
                        <div class="col-sm-6">
                            <b>Coupon</b>
                        </div>
                        <div class="col-sm-2">
                            <b>Uses</b>
                        </div>
                        <div class="col-sm-3">
                            <b>Totals</b>
                        </div>
                    </div>
                </li>

                <?php \Dsc\System::instance()->get( 'session' )->set( 'delete.redirect', '/admin/shop/reports/' . $report->slug ); ?>
                
                <?php foreach($paginated->items as $key=>$item) { ?>
                    <li class="list-group-item" data-id="<?php echo $item->id; ?>">
                    <div class="row">
                        <div class="col-sm-6">
                            <a href="./admin/shop/coupon/edit/<?php echo $item->id; ?>">
                            <?php echo $item->code; ?>
                            </a>
                        </div>
                        <div class="col-sm-2">
                            <?php echo (int) $item->countSales(true); ?>
                        </div>
                        <div class="col-sm-3">
                            <?php echo \Shop\Models\Currency::format( $item->totalSales(true) ); ?>
                        </div>
                    </div>
                </li>
                <?php } ?>
                </ul>
            
            <?php } else { ?>
                <p>No items found.</p>
            <?php } ?>
        
            </div>

        <div class="panel-footer">
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

</form>