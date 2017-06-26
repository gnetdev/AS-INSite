RmmResize = function() {
	var width = document.documentElement.clientWidth;
	var height = document.documentElement.clientHeight;
	var container = jQuery('.navbar .container');

	if (width<1140) {
		jQuery('.mega-menu ul.dropdown-menu').css({
			/*'width' : container.outerWidth(),*/
			'margin-left' : parseInt( container.css('marginLeft') ) + parseInt( container.css('paddingLeft') ),
		});		
	} else {
		jQuery('.mega-menu ul.dropdown-menu').css({
			'margin-left' : 'inherit',
		});		
	}
	
}

jQuery(document).ready(function() {

	jQuery( window ).resize(function() {
		RmmResize();
	});

	RmmResize();

});