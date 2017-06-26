<?php 
$settings = \Admin\Models\Settings::fetch();
if( $settings->enabledIntegration( 'kissmetrics' ) ) { ?>
<script type="text/javascript">
	jQuery( function(){
		jQuery("footer #mc-embedded-subscribe").on('click', function(){
				_kmq.push(['record', 'Subscribed to Newsletter', {"Newsletter Name" : "Main", "E-mail" : jQuery("footer #mce-email").val() }]);
		});
	});
</script>
<?php } ?>

<footer>
    <div class="container">
        <div class="row">
            <div class="col-sm-3">
                <div class="footer-widget">
                    <div class="widget-title">
                        <h2>My Account</h2>
                    </div>
                    <div class="widget-content">
                    <?php
                    if (class_exists('\Modules\Factory')) { 
                        if ($list = (new \Admin\Models\Navigation)->emptyState()->setState('filter.root', false)->setState('filter.published', true)->setState('filter.tree', '534759a2f02e253a3d96ce4e')->setState('order_clause', array( 'tree'=> 1, 'lft' => 1 ))->getList()) 
                        { 
                            echo (new \Modules\Modules\Menu\Module( array('list'=>$list, 'class_suffix'=>'links' ) ))->html();
                        } 
                    }
                    ?>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="footer-widget">
                    <div class="widget-title">
                        <h2>Customer Care</h2>
                    </div>
                    <div class="widget-content">
                    <?php 
                    if (class_exists('\Modules\Factory')) {
                        if ($list = (new \Admin\Models\Navigation)->emptyState()->setState('filter.root', false)->setState('filter.published', true)->setState('filter.tree', '53474a27f02e25740896ce4c')->setState('order_clause', array( 'tree'=> 1, 'lft' => 1 ))->getList()) 
                        { 
                            echo (new \Modules\Modules\Menu\Module( array('list'=>$list, 'class_suffix'=>'links' ) ))->html();
                        } 
                    }
                    ?>
                    </div>
                </div>
            </div>            
            <div class="col-sm-3">
                <div class="footer-widget">
                    <div class="widget-title">
                        <h2>About Us</h2>
                    </div>
                    <div class="widget-content">
                    <?php
                    if (class_exists('\Modules\Factory')) { 
                        if ($list = (new \Admin\Models\Navigation)->emptyState()->setState('filter.root', false)->setState('filter.published', true)->setState('filter.tree', '53475bf0f02e253a3d96ce50')->setState('order_clause', array( 'tree'=> 1, 'lft' => 1 ))->getList()) 
                        { 
                            echo (new \Modules\Modules\Menu\Module( array('list'=>$list, 'class_suffix'=>'links' ) ))->html();
                        } 
                    }
                    ?>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="footer-widget">
                    <div class="widget-title">
                        <h2>Contact Info</h2>
                    </div>
                    <div class="widget-content">
                        <tmpl type="modules" name="footer-address" />
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-sm-12">
                <div class="separator footer-separator">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-4 col-sm-offset-4">
                <!-- Begin MailChimp Signup Form -->
                <div id="mc_embed_signup">
                    <form action="//amritasingh.us2.list-manage.com/subscribe/post?u=e7a8c83d29dfb2e19f87936d2&amp;id=faa3d29326" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
                        <div style="position: absolute; left: -5000px;"><input type="text" name="b_e7a8c83d29dfb2e19f87936d2_faa3d29326" tabindex="-1" value=""></div>
                        <div class="input-group">
                        	<input type="email" value="" name="EMAIL" class="form-control email" id="mce-email" placeholder="Sign Up for Email Updates" required>
                        	<span class="input-group-btn">
                                <input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="btn btn-default custom-button">
                            </span>
                        </div>
                    </form>
                </div>
                
                <!--End mc_embed_signup-->
            </div>
        </div>        
        
        <div class="row">
            <div class="col-sm-12">
                <div class="separator footer-separator">
                    <button class='scroll-top'>Scroll top</button>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="social-links">
                    <ul>
                        <li><a href="https://www.facebook.com/AmritaSinghJewelryIndia" target="_blank" class="facebook">facebook</a></li>
                        <li><a href="https://twitter.com/amritasjewelry" target="_blank" class="twitter">twitter</a></li>
                        <li><a href="http://instagram.com/banglebangle" target="_blank" class="instagram">instagram</a></li>
                        <li><a href="http://www.pinterest.com/amritasjewelry/" target="_blank" class="pinterest">pinterest</a></li>
                        <li><a href="https://www.google.com/+amritasinghjewelry" target="_blank" class="googleplus">googleplus</a></li>
                        <!--<li><a href="#" class="flickr">flickr</a></li>
                        <li><a href="#" class="skype">skype</a></li>
                        <li><a href="#" class="email">email</a></li>-->
                    </ul>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="copyright">
                    <p>&copy; 2014 Amrita Singh Jewelry. All Rights Reserved. <a href="/pages/terms-and-conditions">Terms of Use</a> | <a href="/pages/privacy-policy">Privacy Policy</a></p>
                </div>
                
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                    <div class="copyright">
                    <p><a class="tf_upfront_badge" href="http://www.thefind.com/store/about-amritasingh" title="TheFind Upfront"><img  border="0" src="//upfront.thefind.com/images/badges/r/69/5f/695f9c8080f7b7a151de4c3ccbdfee58.png" alt="Amrita Singh Jewelry is an Upfront Merchant on TheFind. Click for info."/></a>
						  <script type="text/javascript">
						    (function() {
						      var upfront = document.createElement('SCRIPT'); upfront.type = "text/javascript"; upfront.async = true;
						      upfront.src = document.location.protocol + "//upfront.thefind.com/scripts/main/utils-init-ajaxlib/upfront-badgeinit.js";
						      upfront.text = "thefind.upfront.init('tf_upfront_badge', '695f9c8080f7b7a151de4c3ccbdfee58')";
						      document.getElementsByTagName('HEAD')[0].appendChild(upfront);
						    })();
						  </script>
                    </p>
                </div>
                
            </div>
        </div>
    </div>
	
</footer>