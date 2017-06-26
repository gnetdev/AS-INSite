;var VigoShop = {
    init : function() {
    "use strict";
    jQuery('.footer-form #send_message').click(function(e){
    
        e.preventDefault();
        var error = false;
        var $this = jQuery(this);
        var name = $this.parent().find('#footer-name').val();
        var email = $this.parent().find('#footer-email').val();
        var message = $this.parent().find('#footer-message').val();
        
            if(name.length == 0){
                error = true;
                jQuery('.footer-form #footer-name').css('border','1px solid red');
            }
            else
            {
                jQuery('.footer-form #footer-name').css('border','1px solid #444444');
            }
                
            if(email.length == 0 || email.indexOf('@') == '-1'){
                error = true;
                jQuery('.footer-form #footer-email').css('border','1px solid red');
            }
            else
            {  
                
                jQuery('.footer-form #footer-email').css('border','1px solid #444444');
            }
            
            if(message.length == 0){
                error = true;
                jQuery('.footer-form #footer-message').css('border','1px solid red');
            }
            else
            {                   
                jQuery('.footer-form #footer-message').css('border','1px solid #444444');
            }

            if(error == true)
            {
                jQuery('.footer-success').hide();
                jQuery('.footer-error').hide();
            }
            
            
            if(error == false){
                jQuery('.footer-form #send_message').attr({'disabled' : 'true'});

                jQuery.post("process.php", { bbsubmit: "1", bbname: name, bbemail: email, bbmessage: message }, function(result){
                    if(result == 'sent')
                    {
                        jQuery('.footer-success').fadeIn(500);
                        jQuery('.footer-form').hide();
                    }
                    else
                    {
                        jQuery('.footer-error').fadeIn(500);
                        jQuery('.footer-form #send_message').removeAttr('disabled');
                    }
                });
            }
        });

        jQuery('.contact-form #contact-send').click(function(e){
    
        e.preventDefault();
        var error = false;
        var $this = jQuery(this);
        var name = jQuery('.contact-form #contact-name').val();
        var email = jQuery('.contact-form #contact-email').val();
        var message = jQuery('.contact-form #contact-message').val();
        var subject = jQuery('.contact-form #contact-subject').val();
        
            if(name.length == 0){
                error = true;
                jQuery('.contact-form #contact-name').css('border','1px solid red');
            }
            else
            {
                jQuery('.contact-form #contact-name').css('border','1px solid #CCCCCC');
            }
                
            if(email.length == 0 || email.indexOf('@') == '-1'){
                error = true;
                jQuery('.contact-form #contact-email').css('border','1px solid red');
            }
            else
            {  
                
                jQuery('.contact-form #contact-email').css('border','1px solid #CCCCCC');
            }
            
            if(message.length == 0){
                error = true;
                jQuery('.contact-form #contact-message').css('border','1px solid red');
            }
            else
            {                   
                jQuery('.contact-form #contact-message').css('border','1px solid #CCCCCC');
            }

            if(error == true)
            {
                jQuery('.footer-success').hide();
                jQuery('.footer-error').hide();
            }
            
            
            if(error == false){
                jQuery('.contact-form #send_message').attr({'disabled' : 'true'});

                jQuery.post("process.php", { bbsubmit: "1", bbname: name, bbemail: email, bbmessage: message, bbsubject: subject }, function(result){
                    if(result == 'sent')
                    {
                        jQuery('.footer-success').fadeIn(500);
                        jQuery('.contact-form').hide();
                    }
                    else
                    {
                        jQuery('.footer-error').fadeIn(500);
                        jQuery('.contact-form #send_message').removeAttr('disabled');
                    }
                });
            }
        });
       this.initFlexsliders();
        jQuery('.presentation-boxes figcaption .content').each(VigoShop.centerBox);
        jQuery('input, textarea').placeholder();
        jQuery(".main-nav").tinyNav({
            active: 'active',
            header: 'Navigation'
        });
        jQuery('.l_tinynav1').addClass('hidden-xs');
        jQuery('#tinynav1').addClass('visible-xs');
        jQuery('.stars').raty({
            path : 'theme/img',
            half : false,
            score: function() {
                return jQuery(this).attr('data-score');
            },
            number: function() {
                return jQuery(this).attr('data-number');
            }
        });
        jQuery('.stars-white').raty({
            path : 'theme/img',
            starOff : 'star-off-white.png',
            starOn : 'star-on-white.png',
            half : false,
            size     : 19,
            score: function() {
                return jQuery(this).attr('data-score');
            },
            number: function() {
                return jQuery(this).attr('data-number');
            }
        });
        setTimeout(function() {
            jQuery('.promo-slider figcaption').each(function() {
                var sH = jQuery('.promo-slider').height();
                var fH = jQuery(this).height();
                jQuery(this).css({
                    "top" : (sH/2 - fH/2)+"px"
                });
            });
        }, 200);
        jQuery(".chosen-select").chosen({
            disable_search_threshold: 10
        });
        var index = 0;
        jQuery('.range-slider').slider({
            selection : "before",
            orientation : "horizontal",
            min : 0,
            max : 500,
            step : 10,
            tooltip : "hide",
            handle : "square",
            formater : function(val) {
                var value = jQuery('<b></b>').text("$"+val+".00");
                if(index == 0) {
                    jQuery('.slider-handle').first().html(value);
                    index++;
                } else {
                    jQuery('.slider-handle').last().html(value);
                }
            }
        }).on('slide', function(ev) {
                jQuery('.slider-handle').each(function(index) {
                    var value = jQuery('<b></b>').text("$"+ev.value[index]+".00");
                    jQuery(this).html(value);
                });
        });

//        $('textarea').each(VigoShop.textareaPlacehodler);

        jQuery('.scroll-top').on('click', VigoShop.scrollTop);

    },
    scrollTop : function() {
      jQuery('body, html').animate({
          scrollTop : 0
      }, 500);
    },
    textareaPlacehodler : function() {
      jQuery(this).on('keyup', function() {
         if(jQuery(this).val()) {
             jQuery(this).addClass('active');
         } else {
             jQuery(this).removeClass('active');
         }
      });
    },
    centerBox : function() {
      var el = jQuery(this);
        var pH = el.parent().height();
        var eH = el.height();
        el.css({
            marginTop : (pH/2 - eH/2)+"px"
        });
    },
    initFlexsliders : function() {
        jQuery('#mycarousel').jcarousel({
            vertical: true,
            scroll: 1,
            buttonNextHTML: "<div><i class='glyphicon glyphicon-chevron-down'></i></div>",
            buttonPrevHTML: "<div><i class='glyphicon glyphicon-chevron-up'></i></div>",
            initCallback : function() {
                jQuery('#mycarousel a').on('click', function() {
                    var imgLink = jQuery(this).attr('rel');
                    jQuery('.product-image-big').fadeOut('fast', function() {
                        jQuery('.product-image-big').css({
                            "background-image" : "url('"+imgLink+"')"
                        }).fadeIn('fast');
                    });
                    return false;
                });
            }
        });

        jQuery('.latest-post-slider').flexslider({
            'controlNav': false,
            'directionNav' : false,
            "touch": true,
            "animation": "slide",
            "animationLoop": true,
            "slideshow" : false
        });
        VigoShop.createSliderButton('.latest-post-slider', '.latest-post-controls .next', 'next');
        VigoShop.createSliderButton('.latest-post-slider', '.latest-post-controls .prev', 'prev');

        jQuery('.footer-most-favorite').flexslider({
            'controlNav': false,
            'directionNav' : false,
            "touch": true,
            "animation": "slide",
            "animationLoop": true,
            "slideshow" : false
        });
        VigoShop.createSliderButton('.footer-most-favorite', '.most-favorite-controls .next', 'next');
        VigoShop.createSliderButton('.footer-most-favorite', '.most-favorite-controls .prev', 'prev');

        jQuery('.featured-slider').flexslider({
            'controlNav': false,
            'directionNav' : false,
            "touch": true,
            "animation": "slide",
            "animationLoop": true,
            "slideshow" : false
        });
        VigoShop.createSliderButton('.featured-slider', '.featured-slider-controls .next', 'next');
        VigoShop.createSliderButton('.featured-slider', '.featured-slider-controls .prev', 'prev');

        jQuery('.comments-slider').flexslider({
            'controlNav': false,
            'directionNav' : false,
            "touch": true,
            "animation": "slide",
            "animationLoop": true,
            "slideshow" : false
        });
        VigoShop.createSliderButton('.comments-slider', '.latest-comments-controls .next', 'next');
        VigoShop.createSliderButton('.comments-slider', '.latest-comments-controls .prev', 'prev');

        jQuery('.related-posts-slider').flexslider({
            'controlNav': false,
            'directionNav' : false,
            "touch": true,
            "animation": "slide",
            "animationLoop": true,
            "slideshow" : false
        });

        VigoShop.createSliderButton('.related-posts-slider', '.related-post-controls .next', 'next');
        VigoShop.createSliderButton('.related-posts-slider', '.related-post-controls .prev', 'prev');

        jQuery('.photo-gallery').flexslider({
            'controlNav': false,
            'directionNav' : false,
            "touch": true,
            "animation": "slide",
            "animationLoop": true,
            "slideshow" : false
        });

        VigoShop.createSliderButton('.photo-gallery', '.photo-gallery .next', 'next');
        VigoShop.createSliderButton('.photo-gallery', '.photo-gallery .prev', 'prev');

        jQuery('.sliderTypeOne').flexslider({
            'controlNav': false,
            'directionNav' : false,
            "touch": true,
            "animation": "slide",
            "animationLoop": true,
            "slideshow" : false
        });

        VigoShop.createSliderButton('.sliderTypeOne', '.sliderTypeOne-controls .next', 'next');
        VigoShop.createSliderButton('.sliderTypeOne', '.sliderTypeOne-controls .prev', 'prev');

        jQuery('.sliderTypeTwo').flexslider({
            'controlNav': false,
            'directionNav' : false,
            "touch": true,
            "animation": "slide",
            "animationLoop": true,
            "slideshow" : false
        });

        VigoShop.createSliderButton('.sliderTypeTwo', '.sliderTypeTwo-controls .next', 'next');
        VigoShop.createSliderButton('.sliderTypeTwo', '.sliderTypeTwo-controls .prev', 'prev');

        jQuery('.promo-slider').flexslider({
            'controlNav': true,
            'directionNav' : false,
            "touch": true,
            "animation": "slide",
            "animationLoop": true,
            "slideshow" : false
        });

        jQuery('.new-arrivals-slider').flexslider({
            'controlNav': false,
            'directionNav' : false,
            "touch": true,
            "animation": "slide",
            "animationLoop": true,
            "slideshow" : false
        });

        VigoShop.createSliderButton('.new-arrivals-slider', '.new-arrivals-controls .next', 'next');
        VigoShop.createSliderButton('.new-arrivals-slider', '.new-arrivals-controls .prev', 'prev');

        jQuery('.article-slider').flexslider({
            'controlNav': false,
            'directionNav' : false,
            "touch": true,
            "animation": "slide",
            "animationLoop": true,
            "slideshow" : false
        });

        VigoShop.createSliderButton('.article-slider', '.article-slider-controls .next', 'next');
        VigoShop.createSliderButton('.article-slider', '.article-slider-controls .prev', 'prev');

        jQuery('.testimonials-slider').flexslider({
            'controlNav': false,
            'directionNav' : false,
            "touch": true,
            "animation": "slide",
            "animationLoop": true,
            "slideshow" : false
        });

        VigoShop.createSliderButton('.testimonials-slider', '.testimonials-slider-controls .next', 'next');
        VigoShop.createSliderButton('.testimonials-slider', '.testimonials-slider-controls .prev', 'prev');

        jQuery('.logo-slider').flexslider({
            'controlNav': false,
            'directionNav' : false,
            "touch": true,
            "animation": "slide",
            "animationLoop": true,
            "slideshow" : false
        });

        VigoShop.createSliderButton('.logo-slider', '.logo-slider-controls .next', 'next');
        VigoShop.createSliderButton('.logo-slider', '.logo-slider-controls .prev', 'prev');

        jQuery('.team-slider').flexslider({
            'controlNav': false,
            'directionNav' : false,
            "touch": true,
            "animation": "slide",
            "animationLoop": true,
            "slideshow" : false
        });

        VigoShop.createSliderButton('.team-slider', '.team-slider-controls .next', 'next');
        VigoShop.createSliderButton('.team-slider', '.team-slider-controls .prev', 'prev');
    },
    createSliderButton : function(slider, button, action) {
        jQuery(button).click(function() {
           jQuery(slider).flexslider(action);
        });
    }
};

$(document).ready(function() {
    VigoShop.init();
});
