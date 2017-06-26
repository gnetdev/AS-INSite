<!-- Google Code for New Website Conversions Conversion Page -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 1037204903;
var google_conversion_language = "en";
var google_conversion_format = "3";
var google_conversion_color = "ffffff";
var google_conversion_label = "UBuRCIfW4AkQp_vJ7gM";
var google_conversion_value = <?php echo (float) $this->order->grand_total; ?>;
var google_remarketing_only = false;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/1037204903/?value=<?php echo (float) $this->order->grand_total; ?>&amp;label=UBuRCIfW4AkQp_vJ7gM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>

<?php 
$total_minus_shipping_and_taxes = $this->order->grand_total - $this->order->shipping_total - $this->order->tax_total;
if ($total_minus_shipping_and_taxes < 0) {
    $total_minus_shipping_and_taxes = 0;
} 
?>

<!-- Pepperjam -->
<iframe src="https://t.pepperjamnetwork.com/track?PID=5843&AMOUNT=<?php echo (float) $total_minus_shipping_and_taxes; ?>&TYPE=1&OID=<?php echo $this->order->number; ?>" height="1" width="1" frameborder="0"></iframe>

<!-- Springmetrics -->
<script type="text/javascript">
    _springMetq.push(["setdata", {revenue: "<?php echo (float) $this->order->grand_total; ?>"}]);
    _springMetq.push(["setdata", { "orderId": "<?php echo $this->order->number; ?>" }]);
    _springMetq.push(["convert", "sale" ]);
</script>

<!-- Facebook Conversion Code for __FBMail -->
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
})();
window._fbq = window._fbq || [];
window._fbq.push(['track', '6015248789436', {'value':'<?php echo (float) $this->order->grand_total; ?>','currency':'USD'}]);
</script>