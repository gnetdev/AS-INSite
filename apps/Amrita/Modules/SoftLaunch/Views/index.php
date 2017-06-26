<div class="slide-out-div hidden">
    <a class="handle" href="./pages/were-upgrading">Welcome</a>
    <img class="img-responsive slide-out-background" src="./img/soft_launch_bg.jpg" />
    <div class="slide-out-copy">
        <?php echo $module->model->copy; ?>
    </div>
</div>

<script>
jQuery(window).load(function(){
	var el = jQuery('.slide-out-div');
	if (jQuery.fn.tabSlideOut) {
		el.removeClass('hidden');
	    el.tabSlideOut({
	        tabHandle: '.handle',                     //class of the element that will become your tab
	        pathToTabImage: './img/welcome.jpg', //path to the image for the tab //Optionally can be set using css
	        imageHeight: '155px',                     //height of tab image           //Optionally can be set using css
	        imageWidth: '40px',                       //width of tab image            //Optionally can be set using css
	        tabLocation: 'left',                      //side of screen where tab lives, top, right, bottom, or left
	        speed: 300,                               //speed of animation
	        action: 'click',                          //options: 'click' or 'hover', action to trigger animation
	        topPos: '200px',                          //position from the top/ use if tabLocation is left or right
	        leftPos: '20px',                          //position from left/ use if tabLocation is bottom or top
	        fixedPosition: false                      //options: true makes it stick(fixed position) on scroll
	    });	    
	}
});
</script>
