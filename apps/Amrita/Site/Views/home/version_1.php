<?php
$promo1 = \Modules\Factory::render( 'homepage1-promo1', \Base::instance()->get('PARAMS.0') );
$promo2 = \Modules\Factory::render( 'homepage1-promo2', \Base::instance()->get('PARAMS.0') ); 
if ($promo1 || $promo2) { ?>
<div class="container">
    <div class="row">
        <div class="presentation-boxes">
            <div class="col-sm-7">
                <div class="combined-boxes">
                    <?php echo $promo1; ?>
                </div>
            </div>                            
            <div class="col-sm-5">
                <div class="combined-boxes">
                    <?php echo $promo2; ?>
                </div>                                        
            </div>
        </div>
    </div>
</div>
<?php } ?>

<div class="container">
    <tmpl type="modules" name="homepage1-shipping" />
</div>

<section id='main'>
    <div class="container">

        <div class="row">
            <div class="presentation-boxes">
                <div class="col-sm-4">
                    <div class="combined-boxes">
                        <tmpl type="modules" name="homepage1-featured1" />
                    </div>
                </div>                            
                <div class="col-sm-4">
                    <div class="combined-boxes">
                        <tmpl type="modules" name="homepage1-featured2" />
                    </div>                                        
                </div>
                <div class="col-sm-4">
                    <div class="combined-boxes">
                        <tmpl type="modules" name="homepage1-featured3" />
                    </div>
                </div>
            </div>
        </div>    
    
        <?php echo $this->renderView('Amrita\Site\Views::home/block_new_featured_special.php'); ?>
        
        <?php echo $this->renderView('Amrita\Site\Views::home/block_bestsellers.php'); ?>
        
    </div>
</section>