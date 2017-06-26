/*
*************************************************
jQuery Carousel Plugin

@created Dioscouri Design
@copyright 2011 http://www.dioscouri.com
*************************************************
*/

(function () {
    var defaults = {
        transition: 'fade',
        start: 0,
        scroll: 1,
        autoPlay: 0,
        autoPlayInterval: 6000,
        autoPlayStopOnClick: 1,
        hideControls: 0,
        sizeToBrowser: 0,
        sizeToContainer: 0,
        slideWidth: 960,
        slideHeight: 450,
        containerWidth: 960,
        containerHeight: 450,
        resizeImages: 1,
        setSlidesCSS: 1,
        loop: 1,
        easing: 'swing',
        duration: 1000,
        insertControls: 1,
        fadeDetailsWithSlides: 1,
        onCompleteTransitionFunction: null,
        onCompleteResizeImagesFunction: null
    };
    
    jQuery.fn.customCarousel = function(o) {
        return this.each(function() {
            jQuery(this).data('customCarousel', new $c(this, o));
        });
    };
    
    jQuery.customCarousel = function ( element, options ) {
        this.e          = jQuery( element );
        this.options    = jQuery.extend( {}, defaults, this.e.data(), options || {} );
        this.timer      = null;
        this.go();
    };
    
    var $c = jQuery.customCarousel;
        $c.fn = $c.prototype = {
        customCarousel: '0.1.0'
    };
    
    $c.fn.extend = $c.extend = jQuery.extend;
    
    $c.fn.extend({
        
        go: function() {
            this.currentPosition = this.options.start;
            this.container = this.e.find('.slides-container');
            this.slidesWrapper = this.e.find('.slides');
            this.slides = this.e.find('.slide');
            this.slidesImages = this.slides.find('img');
            this.numberOfSlides = this.slides.length;
            this.details = this.e.find('.detail');
            this.options.slideImageHeight = this.options.slideHeight;
            this.options.slideImageWidth = this.options.slideWidth;
            
            /* during a transition, holds the selected click action.  Possible values: null/prev/next/slide */
            this.clicked = null; 
            
            this.prepareDOM();
            
            if (this.options.start != 0)
            {
                this.goToSlide();
            }

            if (this.options.hideControls != 1)
            {
                if (this.options.insertControls == 1) 
                {
                	this.e.append('<div class="arrow-control control prev">Prev</div><div class="arrow-control control next">Next</div>');
                }
                this.e.find('.control').css({
                    'cursor': 'pointer'
                });
    
                this.manageControls();
    
                this.e.find('.prev').bind('click', jQuery.proxy(this.prevClick, this) );
                this.e.find('.next').bind('click', jQuery.proxy(this.nextClick, this) );
                if (this.options.autoPlayStopOnClick) 
                {
                    this.e.find('.prev').bind('click', jQuery.proxy(this.stopAuto, this) );
                    this.e.find('.next').bind('click', jQuery.proxy(this.stopAuto, this) );
                    this.details.bind('click', jQuery.proxy(this.stopAuto, this) );
                }
            }

            if (this.options.slidesVisibleAtOnce >= this.numberOfSlides) {
            	this.e.find('.controls').hide();
            }
            
            if (this.options.autoPlay == 1 && this.numberOfSlides > 1)
            {
                this.startAuto();
            }
        },
        
        setImageSizesToBrowser: function() {
        	var self = this;
        	
        	/* store the original values */
        	if (!('containerWidthOriginal' in this.options)) {
        		this.options.containerWidthOriginal = this.options.containerWidth; 
        	}
        	if (!('containerHeightOriginal' in this.options)) {
        		this.options.containerHeightOriginal = this.options.containerHeight; 
        	}
        	if (!('slideWidthOriginal' in this.options)) {
        		this.options.slideWidthOriginal = this.options.slideWidth; 
        	}
        	if (!('slideHeightOriginal' in this.options)) {
        		this.options.slideHeightOriginal = this.options.slideHeight; 
        	}
        	
        	/* newImageWidth: the width of the browser window or the width of the original image file, whichever is larger */
        	var newImageWidth = Math.max(this.options.containerWidthOriginal, jQuery(window).width());
        	
        	/* Set the min_display_width to .page or window, whichever is wider). */
        	var minDisplayWidth = Math.max(this.options.containerWidthOriginal, jQuery(window).width());

            var t = new Image();
            t.src = this.slidesImages[0].src;
            newContainerHeight = Math.floor( (t.height / t.width) * newImageWidth );
        	
        	this.options.containerWidth = newImageWidth;
        	this.options.containerHeight = newContainerHeight;
        	this.options.slideHeight = newContainerHeight;
        	this.options.slideWidth = newImageWidth;
        	this.options.slideImageWidth = newImageWidth;
        	this.options.slideImageHeight = 'auto';
        },
        
        setImageSizesToContainer: function() {
            var self = this;
            
            /* store the original values */
            if (!('containerWidthOriginal' in this.options)) {
                this.options.containerWidthOriginal = this.options.containerWidth; 
            }
            if (!('containerHeightOriginal' in this.options)) {
                this.options.containerHeightOriginal = this.options.containerHeight; 
            }
            if (!('slideWidthOriginal' in this.options)) {
                this.options.slideWidthOriginal = this.options.slideWidth; 
            }
            if (!('slideHeightOriginal' in this.options)) {
                this.options.slideHeightOriginal = this.options.slideHeight; 
            }
            
            var newContainerWidth = this.e.width();
            var newSlideWidth = this.e.width() / this.options.scroll;
            var newImageWidth = this.e.width() / this.options.scroll;

            var t = new Image();
            t.src = this.slidesImages[0].src;
            newContainerHeight = Math.floor( (t.height / t.width) * newImageWidth );
            
            this.options.containerWidth = newContainerWidth;
            this.options.containerHeight = newContainerHeight;
            this.options.slideHeight = newContainerHeight;
            this.options.slideWidth = newSlideWidth;
            this.options.slideImageWidth = newImageWidth;
            this.options.slideImageHeight = 'auto';
        },        
        
        resizeImages: function() {
        	var self = this;
        	
            this.container.css({
                'overflow': 'hidden',
                'position': 'relative',
                'width': this.options.containerWidth,
                'height': this.options.containerHeight
            });
            
            if (this.options.setSlidesCSS == 1) {
                this.slides.css({
                    'overflow': 'hidden',
                    'position': 'relative'
                });
            }
            
            switch(this.options.transition)
            {
                case "down":
                    this.options.slidesVisibleAtOnce = Math.round( this.options.containerHeight / this.options.slideHeight );
                    this.slidesWrapper.css({
                        'margin-top' : this.options.slideHeight * (-this.currentPosition)
                      });
                    if (this.options.loop == 2) {
                        self.slidesWrapper.css({'margin-top': (0 - self.options.slideHeight)});
                    }
                    this.slides.css({
                        'width' : this.options.slideWidth,
                        'height' : this.options.slideHeight
                      });
                    if (this.options.resizeImages == 1) {
                        this.slidesImages.css({
                            'width' : this.options.slideImageWidth,
                            'height' : this.options.slideImageHeight
                        });                        
                    }
                    break;
                case "fade":
                    this.options.slidesVisibleAtOnce = Math.round( this.options.containerWidth / this.options.slideWidth );
                    this.slides.css({
                        'position' : 'absolute',
                        'width' : this.options.slideWidth,
                        'height' : this.options.slideHeight
                      }).each(function(){
                          n = jQuery(this).attr('data-position');
                          if (n != self.currentPosition) { jQuery(this).hide(); }
                      });
                    if (this.options.resizeImages == 1) {
                        this.slidesImages.css({
                            'width' : this.options.slideImageWidth,
                            'height' : this.options.slideImageHeight
                          });
                    }
                    break;
                case "left":
                    this.options.slidesVisibleAtOnce = Math.round( this.options.containerWidth / this.options.slideWidth );
                    this.slidesWrapper.css({
                      'float' : 'left',
                      'width' : this.options.slideWidth * this.numberOfSlides,
                      'height' : this.options.slideHeight,
                      'margin-left' : this.options.slideWidth * (-this.currentPosition)
                    });
                    if (this.options.loop == 2 && this.numberOfSlides > 1) {
                        self.slidesWrapper.css({'margin-left': (0 - self.options.slideWidth)});
                    }
                    this.slides.css({
                        'float' : 'left',
                        'width' : this.options.slideWidth,
                        'height' : this.options.slideHeight
                      });
                    if (this.options.resizeImages == 1) {
                        this.slidesImages.css({
                            'width' : this.options.slideImageWidth,
                            'height' : this.options.slideImageHeight
                          });
                    }
                    break;
            }
            
            if (typeof self.options.onCompleteResizeImagesFunction == 'function') {
                self.options.onCompleteResizeImagesFunction(self);
            }            
        },
        
        prepareDOM: function() {
            var self = this;
            
            this.slides.each(function(i){
                jQuery(this).attr('data-position', i);
            });
            
            this.details.each(function(i){
                jQuery(this).attr('data-position', i).css({
                    'cursor': 'pointer'
                });
                jQuery(this).click(function(){
                    n = parseInt( jQuery(this).attr('data-position') );
                    self.slideClick(n); 
                });
            });
            
            /*
            this.e.css({
                'position': 'relative'
            });
            */

            jQuery(this.slides[this.currentPosition]).addClass('active');
            jQuery(this.details[this.currentPosition]).addClass('active');
            
            if (this.options.sizeToBrowser == 1) 
            {
            	self.setImageSizesToBrowser();
            	
                jQuery(window).resize(function () {
                    self.setImageSizesToBrowser();
                    self.resizeImages();
                });
            }
            
            if (this.options.sizeToContainer == 1) 
            {
                self.setImageSizesToContainer();
                
                jQuery(window).resize(function () {
                    self.setImageSizesToContainer();
                    self.resizeImages();
                });
            }            
            
            self.resizeImages();
            
            if (this.options.loop == 2 && this.numberOfSlides > 1) {
                self.e.find('.slide:first').before( self.e.find('.slide:last') );
            }
        },
        
        doSlideTransition: function() {
            var self = this;
            
            /*next = this.e.find('[data-position="'+this.currentPosition+'"]');*/

            switch(this.options.transition)
            {
                case "down":
                    switch(this.options.loop) {
                        case 2:
                            switch(this.clicked) {
                                case 'prev':
                                    var new_margin = parseInt(this.slidesWrapper.css('margin-top')) + this.options.slideHeight * this.options.scroll;
                                    this.slidesWrapper.animate({ 'marginTop' : new_margin }, this.options.duration, this.options.easing, function(){
                                        self.e.find('.slide:first').before( self.e.find('.slide:last') );
                                        self.slidesWrapper.css({'margin-top': (0 - self.options.slideHeight * self.options.scroll)});
                                    });
                                    break;
                                case 'next':
                                    var new_margin = parseInt(this.slidesWrapper.css('margin-top')) - this.options.slideHeight * this.options.scroll;
                                    this.slidesWrapper.animate({ 'marginTop' : new_margin }, this.options.duration, this.options.easing, function(){
                                        self.e.find('.slide:last').after( self.e.find('.slide:first') );
                                        self.slidesWrapper.css({'margin-top': (0 - self.options.slideHeight * self.options.scroll)});                                        
                                    });
                                    break;
                                case null:
                                default:
                                    this.slidesWrapper.animate({ 'marginTop' : this.options.slideHeight * (-this.currentPosition) }, this.options.duration, this.options.easing);
                                    break;
                            }
                            break;
                        case 1:
                        case 0:
                        default:
                            this.slidesWrapper.animate({ 'marginTop' : this.options.slideHeight * (-this.currentPosition) }, this.options.duration, this.options.easing);
                            break;
                    }
                    break;
                case "fade":
                    this.slides.each(function(){
                        n = jQuery(this).attr('data-position');
                        if (n != self.currentPosition) { jQuery(this).fadeOut(self.options.duration); }
                    });
                    jQuery(this.slides[this.currentPosition]).fadeIn(this.options.duration);
                    if (this.options.fadeDetailsWithSlides == '1') {
                        this.details.each(function(){
                            n = jQuery(this).attr('data-position');
                            if (n != self.currentPosition) { jQuery(this).fadeOut(self.options.duration); }
                        });                    
                        jQuery(this.details[this.currentPosition]).fadeIn(this.options.duration);                    	
                    }

                    break;
                case "left":
                    switch(this.options.loop) {
                        case 2:
                            switch(this.clicked) {
                                case 'prev':
                                    var new_margin = parseInt(this.slidesWrapper.css('margin-left')) + this.options.slideWidth * this.options.scroll;
                                    this.slidesWrapper.animate({ 'marginLeft' : new_margin }, this.options.duration, this.options.easing, function(){
                                        self.e.find('.slide:first').before( self.e.find('.slide:last') );
                                        self.slidesWrapper.css({'margin-left': (0 - self.options.slideWidth * self.options.scroll)});
                                    });
                                    break;
                                case 'next':
                                    var new_margin = parseInt(this.slidesWrapper.css('margin-left')) - this.options.slideWidth * this.options.scroll;
                                    this.slidesWrapper.animate({ 'marginLeft' : new_margin }, this.options.duration, this.options.easing, function(){
                                        self.e.find('.slide:last').after( self.e.find('.slide:first') );
                                        self.slidesWrapper.css({'margin-left': (0 - self.options.slideWidth * self.options.scroll)});                                        
                                    });
                                    break;
                                case null:
                                default:
                                    this.slidesWrapper.animate({ 'marginLeft' : this.options.slideWidth * (-this.currentPosition) }, this.options.duration, this.options.easing);
                                    break;
                            }
                            break;
                        case 1:
                        case 0:
                        default:
                            this.slidesWrapper.animate({ 'marginLeft' : this.options.slideWidth * (-this.currentPosition) }, this.options.duration, this.options.easing);
                            break;
                    }
                    break;
            }
            
            this.slides.removeClass('active');
            jQuery(this.slides[this.currentPosition]).addClass('active');
            
            this.details.removeClass('active');
            jQuery(this.details[this.currentPosition]).addClass('active');
            
            if (typeof this.options.onCompleteTransitionFunction == 'function') {
                this.options.onCompleteTransitionFunction(this);
            }
        },
        
        prevClick: function() {
            this.clicked = 'prev';
            n = this.currentPosition - this.options.scroll;
            if (this.isValidPosition(n))
            {
                this.currentPosition = n;
            } else if (this.options.loop >= 1)
            {
                this.currentPosition = (this.numberOfSlides-1);
            }
            this.manageControls();
            this.pauseAuto();
            this.goToSlide();
            this.clicked = null;
        },
        
        nextClick: function() {
            this.clicked = 'next';
            n = parseInt(this.currentPosition + this.options.scroll);
            if (this.isValidPosition(n))
            {
                this.currentPosition = n;
            } else if (this.options.loop >= 1)
            {
                this.currentPosition = 0;
            }
            this.manageControls();
            this.pauseAuto();
            this.goToSlide();
            this.clicked = null;
        },
        
        slideClick: function(n) {
            this.clicked = 'slide';
            n = parseInt(n);
            if (this.isValidPosition(n))
            {
                this.currentPosition = n;
            } else if (this.options.loop >= 1)
            {
                this.currentPosition = 0;
            }
            this.manageControls();
            this.pauseAuto();
            this.goToSlide();
            this.clicked = null;
        },
        
        goToSlide: function() {
            if (this.currentPosition >= (this.numberOfSlides) && this.options.loop >= 1) {
                this.currentPosition = 0;
                this.manageControls();
            }
            if (this.currentPosition < 0 && this.options.loop >= 1) {
                this.currentPosition = (this.numberOfSlides-1);
                this.manageControls();
            }
            
            if (this.isValidPosition())
            {
                this.doSlideTransition();
            }
            this.continueAuto();
        },
        
        isValidPosition: function(n) {
            if (n !== undefined) {
                if (n < 0 || n >= this.numberOfSlides)
                {
                    return false;
                }
                if (this.allSlidesVisibleFrom(n)) {
                    return false;
                }
                return true;
            }
            
            if (this.currentPosition < 0 || this.currentPosition >= this.numberOfSlides)
            {
                return false;
            }

            if (this.allSlidesVisibleFrom(this.currentPosition)) {
                return false;
            }
            return true;
        },
        
        allSlidesVisibleFrom: function(n) {
            if(this.options.loop > 0) {
                return false;
            }

            var allVisible = false;
            switch(this.options.transition)
            {
                case "down":
                    if ((n + this.options.slidesVisibleAtOnce - 1) > this.numberOfSlides) {
                        allVisible = true;
                    }
                    break;
                case "fade":
                    allVisible = false;
                    break;
                case "left":
                    if ((n + this.options.slidesVisibleAtOnce) > this.numberOfSlides) {
                        allVisible = true;
                    }
                    break;
            }
            return allVisible;
        },
        
        /**
         * Starts autoscrolling.
         *
         * @method auto
         * @return undefined
         * @param s {Number} Seconds to periodically autoscroll the content.
         */
        startAuto: function(s) {
            if (s !== undefined) {
                this.options.autoPlayInterval = s;
            }

            if (this.options.autoPlay == 0) {
                return this.stopAuto();
            }

            if (this.timer !== null) {
                return;
            }

            this.autoStopped = false;

            var self = this;
            this.timer = window.setTimeout(function() { self.nextClick(); }, this.options.autoPlayInterval);
        },

        /**
         * Stops autoscrolling.
         *
         * @method stopAuto
         * @return undefined
         */
        stopAuto: function() {
            this.pauseAuto();
            this.autoStopped = true;
        },

        /**
         * Pauses autoscrolling.
         *
         * @method pauseAuto
         * @return undefined
         */
        pauseAuto: function() {
            if (this.timer === null) {
                return;
            }

            window.clearTimeout(this.timer);
            this.timer = null;
        },
        
        /**
         * Continues autoscrolling.
         *
         * @method pauseAuto
         * @return undefined
         */
        continueAuto: function() {
            if (this.options.autoPlay == 0) {
                return this.stopAuto();
            }

            if (this.autoStopped === true) {
                return;
            }

            var self = this;
            this.timer = window.setTimeout(function() { self.nextClick(); }, this.options.autoPlayInterval);
        },
        
        /**
         * manageControls: Hides and shows controls depending on currentPosition
         */
        manageControls: function ()
        {
          if (this.options.loop < 1)
          {
              // Hide left arrow if position is first slide
              if (this.currentPosition <= 0) { 
                  this.e.find('.prev').addClass( "inactive" ); 
              } else {
                  this.e.find('.prev').removeClass( "inactive" ); 
              }
              
              // Hide right arrow if position is last slide
              if (this.currentPosition >= (this.numberOfSlides-1)) {
                  this.e.find('.next').addClass( "inactive" );
              } else {
                  this.e.find('.next').removeClass( "inactive" ); 
              }
          }
        }
    });
    
})(jQuery);