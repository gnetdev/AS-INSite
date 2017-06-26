<?php if ($cats = \Blog\Models\Categories::find()) { ?>
<div class="widget widget-categories">
    <div class="widget-title">
        <h2>Categories</h2>
    </div>
	<div class="widget-content">	
        <div class="accordion">
            <div class="panel-group" id="category-accordion">
            	
                <?php foreach ($cats as $cat) {
                    // TODO Set the classes based on anything in the $cat object and also on the active URL and also on $this->selected 
                    $classes = null; 
                    ?>
            		<div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title clearfix">
                                <div class="pull-left">
                                <a href="./blog/category<?php echo $cat->{"path"}; ?>" class="<?php echo $classes; ?>"><?php echo $cat->{'title'}; ?></a>
                                </div>
                            </h4>
                        </div>
                		
            		</div>
                <?php } ?>
                	
	       </div>
	   </div>
	   
	</div>
</div>
<?php } ?>