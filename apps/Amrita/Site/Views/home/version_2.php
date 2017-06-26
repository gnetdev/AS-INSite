<div class="container">
    <tmpl type="modules" name="homepage2-shipping" />
</div>

<tmpl type="modules" name="homepage2-slider" />

<section id='main'>
    <div class="container">
    
        <div class="row">
            <div class="presentation-boxes">
                <div class="col-sm-4">
                    <div class="combined-boxes">
                        <tmpl type="modules" name="homepage2-featured1" />
                    </div>                                        
                </div>
                <div class="col-sm-4">
                    <div class="combined-boxes">
                        <tmpl type="modules" name="homepage2-featured2" />
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="combined-boxes">
                        <tmpl type="modules" name="homepage2-featured3" />
                    </div>
                </div>
            </div>
        </div>

        <?php echo $this->renderView('Amrita\Site\Views::home/block_new_featured_special.php'); ?>
        
        <?php echo $this->renderView('Amrita\Site\Views::home/block_bestsellers.php'); ?>

    </div>
</section>