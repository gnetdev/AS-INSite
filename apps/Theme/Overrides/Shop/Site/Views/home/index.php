<?php $this->settings = \Amrita\Models\Settings::fetch(); ?>

<div class="shop-home">

    <div class="container grid">
        <div class="outer-row">
            <div class="row">
                <div class="category-article category-grid col-md-6">
                    <?php $this->key = 'row_1.featured'; echo $this->renderView('Shop/Site/Views::home/image.php'); ?>
                </div>
                <div class="category-article category-grid col-md-6">
                    <div class="outer-row">
                        <div class="row">
                            <div class="category-article category-grid col-md-6">
                                <?php $this->key = 'row_1.image_1'; echo $this->renderView('Shop/Site/Views::home/image.php'); ?>
                            </div>
                            <div class="category-article category-grid col-md-6">
                                <?php $this->key = 'row_1.image_2'; echo $this->renderView('Shop/Site/Views::home/image.php'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="category-article category-grid col-md-6">
                            <?php $this->key = 'row_1.image_3'; echo $this->renderView('Shop/Site/Views::home/image.php'); ?>
                        </div>
                        <div class="category-article category-grid col-md-6">
                            <?php $this->key = 'row_1.image_4'; echo $this->renderView('Shop/Site/Views::home/image.php'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="outer-row">
            <div class="row">
                <div class="category-article category-grid col-md-6">
                    <div class="outer-row">
                        <div class="row">
                            <div class="category-article category-grid col-md-6">
                                <?php $this->key = 'row_2.image_1'; echo $this->renderView('Shop/Site/Views::home/image.php'); ?>
                            </div>
                            <div class="category-article category-grid col-md-6">
                                <?php $this->key = 'row_2.image_2'; echo $this->renderView('Shop/Site/Views::home/image.php'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="category-article category-grid col-md-6">
                            <?php $this->key = 'row_2.image_3'; echo $this->renderView('Shop/Site/Views::home/image.php'); ?>
                        </div>
                        <div class="category-article category-grid col-md-6">
                            <?php $this->key = 'row_2.image_4'; echo $this->renderView('Shop/Site/Views::home/image.php'); ?>
                        </div>
                    </div>
                </div>
                <div class="category-article category-grid col-md-6">
                    <?php $this->key = 'row_2.featured'; echo $this->renderView('Shop/Site/Views::home/image.php'); ?>
                </div>
            </div>
        </div>

        <div class="outer-row">
            <div class="row">
                <div class="category-article category-grid col-md-3">
                    <?php $this->key = 'row_3.image_1'; echo $this->renderView('Shop/Site/Views::home/image.php'); ?>
                </div>
                <div class="category-article category-grid col-md-3">
                    <?php $this->key = 'row_3.image_2'; echo $this->renderView('Shop/Site/Views::home/image.php'); ?>
                </div>
                <div class="category-article category-grid col-md-3">
                    <?php $this->key = 'row_3.image_3'; echo $this->renderView('Shop/Site/Views::home/image.php'); ?>
                </div>
                <div class="category-article category-grid col-md-3">
                    <?php $this->key = 'row_3.image_4'; echo $this->renderView('Shop/Site/Views::home/image.php'); ?>
                </div>
            </div>
        </div>

    </div>

</div>