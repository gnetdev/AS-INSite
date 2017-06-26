<?php if ($list = \Shop\Models\Categories::find()) { ?>
<div class="widget">
    <div class="widget-title">
        <h2>Categories</h2>
    </div>
    <div class="widget-content">
        <div class="accordion">
            <div class="panel-group" id="category-accordion">
            
            <?php
            $path = \Base::instance()->hive()['PATH'];
            
            foreach ($list as $key => $item) 
            {
                $class = null;
                
                $active = (strpos( $path, $item->path ) !== false);                
            	
            	if ($item->getDepth() == 1) 
            	{
            	    echo '<div class="panel panel-default">';
            	        echo '<div class="panel-heading">';
            	            echo '<h4 class="panel-title clearfix">';
            	                echo '<div class="pull-left">';
                            	    echo '<a class="" href="./shop/category' . $item->{'path'} . '">';
                            	    echo $item->title;
                            	    echo '</a>';
                        	    echo '</div>';
                        	    
                        	    // any kids?
                        	    if (isset($list[$key+1]) && $list[$key+1]->getDepth() > $item->getDepth()) {
                        	    echo '<div class="pull-right">';
                        	        if ($active) {
                        	            echo '<a class="accordion-toggler pull-right" data-toggle="collapse" data-parent="#category-accordion" href="#' . $item->slug . '"></a>';
                        	        } else {
                        	            echo '<a class="accordion-toggler collapsed pull-right" data-toggle="collapse" data-parent="#category-accordion" href="#' . $item->slug . '"></a>';
                        	        }
                        	    echo '</div>';
                        	    }
                        	    
                    	    echo '</h4>';
                	    echo '</div>';
            	} 
            	else 
            	{
            	    echo '<div>';
            	    echo '<a href="./shop/category' . $item->{'path'} . '">';
            	    echo $item->title;
            	    echo '</a>';
            	    echo '</div>';
            	}            	
            
            	// The next item is deeper.
            	if (isset($list[$key+1]) && $list[$key+1]->getDepth() > $item->getDepth()) {
            	    
            	    // only do this if there are children of a level1 item
            	    if ($item->getDepth() == 1) 
            	    {
            	        if ($active) {
            	            echo '<div id="' . $item->slug . '" class="panel-collapse collapse in">';
            	        } else {
            	            echo '<div id="' . $item->slug . '" class="panel-collapse collapse">';
            	        }
            	        
            	        echo '<div class="panel-body">';            	    	
            	    }            	    
            	    
            	}
            	
            	// The next item is shallower.
            	elseif (isset($list[$key+1]) && $item->getDepth() > $list[$key+1]->getDepth()) 
            	{
            		if ($list[$key+1]->getDepth() == 1) 
            		{
            		    echo '</div>';
            		    echo '</div>';
            		    echo '</div>';
            		} else {
            			echo '</div>';
            		}
            	}
            	
            	// The next item is on the same level.
            	else 
            	{
                    if (isset($list[$key+1]) && $list[$key+1]->getDepth() == 1) 
                    {
                        echo '</div>';
                    } 
                    elseif (!isset($list[$key+1])) 
                    {
                        echo '</div>';                    	
                    }
            	}
            }
            ?>
            </div>        
        </div>
    </div>
</div>
<?php } ?>
