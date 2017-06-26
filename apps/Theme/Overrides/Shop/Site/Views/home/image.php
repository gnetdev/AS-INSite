<?php if (empty($this->key)) {
	return;
} ?>

<?php if ($this->settings->{'shop_home.'.$this->key.'.url'}) { ?>
<figure>
<?php } else { ?> 
<figure class="no-pointer">
<?php } ?>

    <?php if ($this->settings->{'shop_home.'.$this->key.'.url'}) { ?>
    <a href="<?php echo $this->settings->{'shop_home.'.$this->key.'.url'}; ?>">
    <?php } ?>

    <?php if ($this->settings->{'shop_home.'.$this->key.'.slug'}) { ?>
    <img src="./asset/<?php echo $this->settings->{'shop_home.'.$this->key.'.slug'}; ?>" class="img-responsive" />
    <?php } ?>
    
    <div class="figure-overlay">
        
        <?php if ($this->settings->{'shop_home.'.$this->key.'.header'}) { ?>
        <h3><?php echo $this->settings->{'shop_home.'.$this->key.'.header'}; ?></h3>
        <?php } ?>
        
        <?php if ($this->settings->{'shop_home.'.$this->key.'.copy'}) { ?>
        <div class="excerpt">
            <?php echo $this->settings->{'shop_home.'.$this->key.'.copy'}; ?>
        </div>
        <?php } ?>
        
        <?php if ($this->settings->{'shop_home.'.$this->key.'.label'}) { ?>
        <button class="btn btn-default custom-button"><?php echo $this->settings->{'shop_home.'.$this->key.'.label'}; ?></button>
        <?php } ?>
                
    </div>
    
    <?php if ($this->settings->{'shop_home.'.$this->key.'.url'}) { ?>
    </a>
    <?php } ?>    

</figure>