<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->

<head>
    <title>Error, Page Not Found</title>
    <?php echo $this->renderView('Theme/Views::head.php'); ?>
</head>

<body class="dsc-wrap <?php echo !empty($body_class) ? $body_class : 'default-body'; ?>">

	<!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-TNPHDT"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-TNPHDT');</script>
<!-- End Google Tag Manager -->

<?php echo $this->renderView('Theme/Views::nav/top.php'); ?>

<tmpl type="modules" name="theme-below-header" />
	
	<div id="content" class="dsc-wrap">		

    	<div id="content-container" class="dsc-wrap container">
    	
            <?php
            // $messages contains an array of contextual messages, such as "Invalid Product" or "Invalid Blog Post" or "Invalid Page"
            $messages = (array) \Dsc\System::instance()->getMessages();
            ?>
    
            <section id="main" class="margin-top">
                                
                <?php echo \Modules\Factory::render( '404-above-content', $this->app->get('PARAMS.0') ); ?>
                	
                	<div class="row">
                	   <div class="col-md-3">
                	       <?php echo \Modules\Factory::render( '404-left-content', $this->app->get('PARAMS.0') ); ?>
                	   </div>
                	   <div class="col-md-6">
                            <div class="well text-center">
                                <h1>BUMMER</h1><h3>we can't find the page you are looking for!</h3><span>404 / Page Not Found</span>
                            </div>
                	   </div>                	   
                	   <div class="col-md-3">
                	       <?php echo \Modules\Factory::render( '404-right-content', $this->app->get('PARAMS.0') ); ?>
                	   </div>                	   
                	</div>
                
                <?php echo \Modules\Factory::render( '404-below-content', $this->app->get('PARAMS.0') ); ?>
                
            </section>
        
    	</div> <!-- /#content-container -->
    
    </div> <!-- #content -->


<tmpl type="modules" name="theme-above-footer" />
    
<?php echo $this->renderView('Theme/Views::footer.php'); ?>
    
<tmpl type="modules" name="theme-below-footer" />
</body>
</html>