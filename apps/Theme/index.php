<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->

<head>

 <?php echo $this->renderView('Theme/Views::head.php'); ?>
    <tmpl type="modules" name="theme-head" />

<!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);
t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','//connect.facebook.net/en_US/fbevents.js');
 
fbq('init', '1448238942173174');
fbq('track', 'PageView');
</script>

<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=1448238942173174&ev=PageView&noscript=1"/>
</noscript>
<!-- End Facebook Pixel Code -->	

</head>

<body class="dsc-wrap <?php echo !empty($body_class) ? $body_class : 'default-body'; ?>">
	<!-- Google Code for Remarketing Tag -->
<script type="text/javascript">
var google_tag_params = {
//ecomm_prodid: '101802639',
//ecomm_pagetype: 'home'
};
</script>
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 944232948;
var google_remarketing_only = true;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/944232948/?value=0&amp;guid=ON&amp;script=0"/>
</div>
</noscript>

    <?php if (!$this->app->get('DEBUG')) { ?>

    
<!-- Google Tag Manager -->
	<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-KM8T8M"
	height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-KM8T8M');</script>
<!-- End Google Tag Manager -->
            
	<?php if (!empty($this->auth->getIdentity()->id)) { ?>
            <script>
            dataLayer.push({'uid': '<?php echo $this->auth->getIdentity()->id; ?>'});
            </script>
            <?php } ?>
    <?php } ?>

    <?php echo $this->renderView('Theme/Views::nav/top.php'); ?>
    
    <tmpl type="modules" name="theme-below-header" />
	
	<div id="content" class="dsc-wrap">		

    	<div id="content-container" class="dsc-wrap container">
    	
    	   <?php /* PUT DEBUGGING STUFF HERE ?> */ /* ?>
            <div class="container margin-top">
                <h5>Debugging Data</h5>
                <?php $primaryGroup = \Shop\Models\Customer::primaryGroup( $this->auth->getIdentity() ); ?>
                <?php echo \Dsc\Debug::dump( 'primaryGroup: ' . $primaryGroup->title ); ?>
            </div>
            <?php /**/ ?>
    	
            <?php if (\Dsc\System::instance()->getMessages(false)) { ?>
            <div class="container margin-top">
                <tmpl type="system.messages" />
            </div>
            <?php } ?>
    
            <section id="main">
            
                <tmpl type="modules" name="theme-above-content" />
                
                <?php $theme_left_content = null; $theme_right_content = null; ?>
                <?php if (class_exists('\Modules\Factory')) { ?>
                    <?php $theme_left_content = \Modules\Factory::render( 'theme-left-content', \Base::instance()->get('PARAMS.0') ); ?>
                    <?php $theme_right_content = \Modules\Factory::render( 'theme-right-content', \Base::instance()->get('PARAMS.0') ); ?>
                <?php } ?>
                                
                <?php if ($theme_left_content && $theme_right_content) {
                	$left_width = 3;
                	$right_width = 3;
                	$main_width = 6; ?>
                	
                	<div class="row">
                	   <div class="col-md-<?php echo $left_width; ?>">
                	       <?php echo $theme_left_content; ?>
                	   </div>
                	   <div class="col-md-<?php echo $main_width; ?>">
                	       <tmpl type="view" />
                	   </div>                	   
                	   <div class="col-md-<?php echo $right_width; ?>">
                	       <?php echo $theme_right_content; ?>
                	   </div>                	   
                	</div>
                	
                <?php } elseif ($theme_left_content && !$theme_right_content) { 
                    $left_width = 3;
                    $right_width = 0;
                    $main_width = 9; ?>
                    
                	<div class="row">
                	   <div class="col-md-<?php echo $left_width; ?>">
                	       <?php echo $theme_left_content; ?>
                	   </div>
                	   <div class="col-md-<?php echo $main_width; ?>">
                	       <tmpl type="view" />
                	   </div>                	                   	   
                	</div>
                                    	
                <?php } elseif (!$theme_left_content && $theme_right_content) { 
                    $left_width = 0;
                    $right_width = 3;
                    $main_width = 9; ?>
                    
                	<div class="row">
                	   <div class="col-md-<?php echo $main_width; ?>">
                	       <tmpl type="view" />
                	   </div>                	   
                	   <div class="col-md-<?php echo $right_width; ?>">
                	       <?php echo $theme_right_content; ?>
                	   </div>                	   
                	</div>
                                    	
                <?php } else { ?>
                    <tmpl type="view" />
                <?php } ?>
                
                <tmpl type="modules" name="theme-below-content" />
                
            </section>
        
    	</div> <!-- /#content-container -->
    
    </div> <!-- #content -->
    
    <tmpl type="modules" name="theme-above-footer" />
    
    <?php echo $this->renderView('Theme/Views::footer.php'); ?>
    
    <tmpl type="modules" name="theme-below-footer" />
    
    <?php $debug = false; if ($this->app->get('DEBUG') && $debug) { ?>
    <div class="clearfix">
        <div class="stats list-group">
            <h4>Stats</h4>
            <div class="list-group-item">
                <?php echo \Base::instance()->format('Page rendered in {0} msecs / Memory usage {1} KB',round(1e3*(microtime(TRUE)-$TIME),2),round(memory_get_usage(TRUE)/1e3,1)); ?>
            </div>
        </div>
        
        <tmpl type="system.loaded_views" />
        
    </div>
    <?php } ?>

    <script type="text/javascript">
        setTimeout(function(){var a=document.createElement("script");
        var b=document.getElementsByTagName("script")[0];
        a.src=document.location.protocol+"//dnn506yrbagrg.cloudfront.net/pages/scripts/0021/7258.js?"+Math.floor(new Date().getTime()/3600000);
        a.async=true;a.type="text/javascript";b.parentNode.insertBefore(a,b)}, 1);
    </script>


<?php /* ?>
<script>

 * Asynchronous asset loader
 * Usage:
 * AL('css', 'http://somewhe.re/css/glam.css');
 * AL('js', '/js/something.js', function(e) { alert('WOOP!'); }); 
 
AL = function(type, url, callback) {
    var el, doc = document;

    switch (type) {
    case 'js':
        el = doc.createElement('script');
        el.src = url;
        break;
    case 'css':
        el = doc.createElement('link');
        el.href = url;
        el.rel = 'stylesheet';
        break;
    default:
        return;
    }

    if (callback) el.addEventListener('load', function(e) {
        callback(e);
    }, false);

    doc.getElementsByTagName('head')[0].appendChild(el);
}

AL('css', "//cdnjs.cloudflare.com/ajax/libs/flexslider/2.2.2/flexslider-min.css");
AL('css', "//cdnjs.cloudflare.com/ajax/libs/chosen/1.1.0/chosen.min.css");
AL('css', "//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css");
AL('css', "//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css");
AL('css', "//fonts.googleapis.com/css?family=Playfair+Display:400,400italic");
AL('css', "./minify/css");
</script>
*/ ?>

<?php if ($sync_customer = \Dsc\System::instance()->get('session')->get('amrita.sync.customer')) { ?>
<script>
jQuery(document).ready(function() {
    var request = jQuery.ajax({
        type: 'get', 
        url: './amrita/customer/sync'
    });
});
</script>
<?php } ?>




</body>

</html>
