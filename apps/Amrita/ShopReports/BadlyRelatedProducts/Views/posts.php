<div class="row">
    <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
        <h1 class="page-title txt-color-blueDark">
            <i class="fa fa-table fa-fw "></i> Reports 
            <span> > Badly Related Products </span>
            <span> > to Posts </span>
        </h1>
    </div>
    <div class="col-xs-12 col-sm-5 col-md-5 col-lg-8">
        <ul id="sparks" class="list-actions list-unstyled list-inline">
            <li>
                <a class="btn btn-default" href="./admin/shop/reports/<?php echo $report->slug; ?>">Go to Pages</a>
            </li>        
            <li>
                <a class="btn btn-info" href="./admin/shop/reports/<?php echo $report->slug; ?>/posts/populate">Refresh</a>
            </li>        
            <li>
                <a class="btn btn-warning" href="./admin/shop/reports">Close Report</a>
            </li>
        </ul>
    </div>
</div>

<hr />

<form method="post" action="./admin/shop/reports/<?php echo $report->slug; ?>/posts">

    <div class="row">
        <div class="col-xs-12 col-sm-4">

        </div>
        
        <div class="col-xs-12 col-sm-2">

        </div>
        
        <div class="col-xs-12 col-sm-2">

        </div>
        
        <div class="col-xs-12 col-sm-4">
            <div class="pull-right">
            <ul class="list-filters list-unstyled list-inline">
                <li>

                </li>
                <li>
                    <a class="btn btn-danger" href="./admin/shop/reports/<?php echo $report->slug; ?>/posts/disassociate-all">Disassociate All</a>
                </li>                
            </ul>
            </div>
        </div>

    </div>

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
                        <div class="col-sm-3">
                            <b></b>
                        </div>
                        <div class="col-sm-3">
                            <b>Product</b>
                        </div> 
                        <div class="col-sm-3">
                            <b>Page</b>
                        </div>
                        <div class="col-sm-3">
                            <b>Reciprocal?</b>
                        </div>                                                                                               
                    </div>
                </li>

                <?php \Dsc\System::instance()->get( 'session' )->set( 'delete.redirect', '/admin/shop/reports/' . $report->slug ); ?>
                
                <?php foreach($paginated->items as $key=>$item) { ?>
                    <li class="list-group-item" data-id="<?php echo $item->id; ?>">
                    <div class="row">
                        <div class="col-sm-3">
                            <div>
                                <a class="btn btn-warning" href="./admin/shop/reports/<?php echo $report->slug; ?>/posts/disassociate/<?php echo $item->id; ?>">Disassociate</a>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div>
                                <?php echo $item['product_title']; ?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <p>
                                <b><?php echo $item['post_title']; ?></b>
                            </p>
                            <p>
                            Page ID: <?php echo $item['post_id']; ?>
                            </p>
                        </div>
                        <div class="col-sm-3">
                            <?php if ($item['reciprocal']) { ?>
                                <span class="label label-success">Yes</span>
                            <?php } else { ?>
                                <span class="label label-danger">No</span>
                            <?php } ?>
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