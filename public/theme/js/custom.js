/*
 * 
 */
if (typeof(AmritaSingh) === 'undefined') {
    var AmritaSingh = {};
}

AmritaSingh.setSizesOnZoomImages = function() {
	var width = document.documentElement.clientWidth;
	
	if (width<1140) {} else {
		jQuery('.imagezoom img').each(function(){
	        bgImg = jQuery(this);
	        var parentHeight = bgImg.parent().height;
	        var theImage = new Image();
	        theImage.src = bgImg.attr("src");

	        //var newImageWidth = 400;
	        //var newImageHeight = (theImage.height / theImage.width) * newImageWidth; 
	        //var newWidthCss = newImageWidth + 'px'; 
	        //var newHeightCss = newImageHeight + 'px';

	        // Make adjustments based on image ratio
	        if (theImage.height < parentHeight) {
	            var newImageHeight = parentHeight;
	            var newImageWidth = (theImage.width / theImage.height) * newImageHeight;
	            var newWidthCss = newImageWidth + 'px'; 
	            var newHeightCss = newImageHeight + 'px';
	        }
	        
	        bgImg.css({
	            width     : newWidthCss,
	            height    : newHeightCss
	        });        
	    });
	}
    
}

jQuery(document).ready(function() {
	jQuery( window ).resize(function() {
		//AmritaSingh.setSizesOnZoomImages();
	});
	
    //AmritaSingh.setSizesOnZoomImages();
    
    var jqzoom_options = {  
            zoomType: 'innerzoom',  
            lens: true,  
            preloadImages: true,  
            alwaysOn: false,  
            zoomWidth: 500,  
            zoomHeight: 500,  
            position:'right'
    };
    //jQuery('.zoom').jqzoom(jqzoom_options);
    
    jQuery('.imagezoom').each(function(){
    	var el = jQuery(this);
    	el.ImageZoom( el.data() );
    });
    
    jQuery('.imagezoom-thumb').each(function(){
    	var el = jQuery(this);
    	var target = el.attr('data-target');
    	if (target) {
    		var imagezoom = jQuery(target).data('imagezoom');
    		if (imagezoom) {
    			el.on('click', function(){
    				imagezoom.changeImage(el.find('img').attr('src'), el.find('img').data('big-image-src') );
    			});    			
    		}
    	}
    });
    
    jQuery('.owl-nav').each(function(){
    	var el = jQuery(this);
    	var target = el.attr('data-target');
    	if (target) {
    		var owl = jQuery(target);
    		if (owl) {
    			if (el.hasClass('prev')) {
        			el.on('click', function(){
        				owl.trigger('owl.prev');
        			});    			
    			} else if (el.hasClass('next')) {
        			el.on('click', function(){
        				owl.trigger('owl.next');
        			});    			
    			} 
    		}
    	}
    });
    
    jQuery('.owl-carousel').each(function(){
    	var el = jQuery(this);
    	var options = el.data();
        var final_options = jQuery.extend({}, options);
        el.owlCarousel(final_options);
        
        var the_typeof = typeof(el.data("owlCarousel").options.afterAction); 
        if (the_typeof === "string") 
        {
        	var functionName = el.data("owlCarousel").options.afterAction;
        	var context = window;
            var namespaces = functionName.split(".");
            var func = namespaces.pop();
            for (var i = 0; i < namespaces.length; i++) {
                context = context[namespaces[i]];
            }
            if (typeof context[func] == 'function') {
            	el.data("owlCarousel").options.afterAction = context[func];
            	el.data("owlCarousel").options.afterAction.apply(el.data("owlCarousel"), el);
            }        	
        }
    });

    
    
});

jQuery(window).load(function(){
    
});

AmritaSingh.imageSwap = function(src, target) {
	var t = jQuery(target);
	if (!t) {
		return;
	}
	
	t.attr('src', src);
}