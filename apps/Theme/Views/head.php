<base href="<?php echo $SCHEME . "://" . $HOST . $BASE . "/"; ?>" />

<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/flexslider/2.2.2/jquery.flexslider-min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery.imagesloaded/3.0.4/jquery.imagesloaded.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jcarousel/0.3.1/jquery.jcarousel.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-placeholder/2.0.7/jquery.placeholder.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/chosen/1.1.0/chosen.jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-hover-dropdown/2.0.2/bootstrap-hover-dropdown.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootbox.js/4.2.0/bootbox.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.2/modernizr.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="./minify/js?date=<?php echo date('Y-m-d'); ?>"></script>

<link href="//cdnjs.cloudflare.com/ajax/libs/flexslider/2.2.2/flexslider-min.css" type="text/css" rel="stylesheet">
<link href="//cdnjs.cloudflare.com/ajax/libs/chosen/1.1.0/chosen.min.css" type="text/css" rel="stylesheet">
<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" type="text/css" rel="stylesheet">
<link href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
<link href='//fonts.googleapis.com/css?family=Playfair+Display:400,400italic' rel='stylesheet' type='text/css'>
<link href="./minify/css?date=<?php echo date('Y-m-d'); ?>" type="text/css" rel="stylesheet">

<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<link rel="icon" type="image/ico" href="/site/images/favicon.ico" />

<?php $global_settings = \Dsc\Mongo\Collections\Settings::fetch('admin.settings'); ?>
<?php $title = trim( $this->app->get( 'meta.title' ) . ' ' . $global_settings->{'system.page_title_suffix'} ); ?>
<title><?php echo $title; ?></title>

<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<meta name="description" content="<?php echo $this->app->get('meta.description'); ?>" />
<meta name="generator" content="<?php echo $this->app->get('meta.generator'); ?>" />    

<meta property="og:title" content="<?php echo $this->app->get('og.title') ? $this->app->get('og.title') : 'Amrita Singh Jewelry'; ?>" />
<meta property="og:type" content="<?php echo $this->app->get('og.type'); ?>" />
<meta property="og:image" content="<?php echo $this->app->get('og.image'); ?>" />
<meta property="og:url" content="<?php echo $this->app->get('og.url') ? $this->app->get('og.url') : $SCHEME . "://" . $HOST . $BASE . "/"; ?>" />
<meta property="og:site_name" content="<?php echo $this->app->get('og.site_name') ? $this->app->get('og.site_name') : 'Amrita Singh Jewelry'; ?>" />
<meta property="og:description" content="<?php echo $this->app->get('og.description'); ?>" />
<meta property="fb:app_id" content="<?php echo $this->app->get('fb.appId'); ?>" />
<?php /* ?><meta name="google-site-verification" content="8gVkVz2P5sJLyNLG1Tb3cHfReO5m4osepg19EDpWu-U" /> <?php */ ?>
<meta name="google-site-verification" content="YtqHlROPCCit3dWdAusAJmbmxUr2U6P1vMfQYGlBrko" />

<?php 
$settings = \Admin\Models\Settings::fetch();
if( $settings->enabledIntegration( 'kissmetrics' ) ) { ?>
<script type="text/javascript">var _kmq = _kmq || [];
var _kmk = _kmk || '<?php echo $settings->{'integration.kissmetrics.key'};?>';
function _kms(u){
  setTimeout(function(){
    var d = document, f = d.getElementsByTagName('script')[0],
    s = d.createElement('script');
    s.type = 'text/javascript'; s.async = true; s.src = u;
    f.parentNode.insertBefore(s, f);
  }, 1);
}
_kms('//i.kissmetrics.com/i.js');
_kms('//doug1izaerwt3.cloudfront.net/' + _kmk + '.1.js');
</script>

<?php if (!empty($this->auth->getIdentity()->email)) { ?>
<script type="text/javascript">  _kmq.push(['identify', '<?php echo $this->auth->getIdentity()->email; ?>']);</script>
<?php } ?>
<?php } ?>

<?php /* ?>
<script type='text/javascript'>
 var _springMetq = _springMetq || [];
 _springMetq.push(['id', 'cc3e7571ed']);
 (
  function(){
   var s = document.createElement('script');
   s.type = 'text/javascript';
   s.async = true;
   s.src = ('https:' == document.location.protocol ? 'https://d3rmnwi2tssrfx.cloudfront.net/a.js' : 'http://static.springmetrics.com/a.js');
   var x = document.getElementsByTagName('script')[0];
   x.parentNode.insertBefore(s, x);
  }
 )();
</script>
*/ ?>

<script>(function() {
  var _fbq = window._fbq || (window._fbq = []);
  if (!_fbq.loaded) {
    var fbds = document.createElement('script');
    fbds.async = true;
    fbds.src = '//connect.facebook.net/en_US/fbds.js';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(fbds, s);
    _fbq.loaded = true;
  }
  _fbq.push(['addPixelId', '451882584947019']);
})();
window._fbq = window._fbq || [];
window._fbq.push(['track', 'PixelInitialized', {}]);
</script>
<noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?id=451882584947019&amp;ev=NoScript" /></noscript>
<?php echo $this->app->get('head.custom'); ?>
