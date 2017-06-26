<article id="post-<?php echo $item->id; ?>" class="post-<?php echo $item->id; ?>">

    <div class="entry-header">
        <?php if ($item->{'details.featured_image.slug'}) { ?>
        <img class="entry-featured img-responsive" width="100%" src="./asset/<?php echo $item->{'details.featured_image.slug'} ?>">
        <?php } ?>
    
        <?php if ($item->{'metadata.creator.image'}) { ?>
        <img class="entry-avatar" alt="<?php echo $item->{'metadata.creator.name'}; ?>" height="52" width="52" src="<?php echo $item->{'metadata.creator.image'}; ?>">
        <?php } ?>
        
        <h2 class="entry-title">
            <?php echo $item->{'metadata.title'}; ?>
        </h2>
        <div class="entry-meta lead">
            <?php echo date( 'F j, Y', $item->{'publication.start.time'} ); ?>
            / 
            <span class="byline">
                <span class="author vcard">
                    <a class="url fn n" href="./blog/author/<?php echo $item->{'metadata.creator.id'}; ?>" title="View all posts by <?php echo $item->{'metadata.creator.name'}; ?>" rel="author"><?php echo $item->{'metadata.creator.name'}; ?></a>
                </span>
            </span>
            /
            <span class="comments-link">
                0 comments
            </span>
            
            <?php if (!empty($item->{'metadata.tags'})) { ?>
            <p class="tag-links">
                <?php foreach ($item->{'metadata.tags'} as $tag) { ?>
                <a class="label label-info" href="./blog/tag/<?php echo $tag; ?>" rel="tag"><?php echo $tag; ?></a>
                <?php } ?>
            </p>
            <?php } ?>
        </div>
    </div>

    <div class="entry-description">
        <?php echo $item->{'details.copy'}; ?>
    </div>

    <div class="entry-meta">
        
        <?php if (!empty($item->{'metadata.categories'})) { ?>
        <p class="cat-links">
            <?php foreach ($item->{'metadata.categories'} as $category) { ?>
            <a class="label label-primary" href="./blog/category/<?php echo $category['slug']; ?>" title="View all posts in <?php echo $category['title']; ?>" rel="category tag"><?php echo $category['title']; ?></a>
            <?php } ?>
        </p>
        <?php } ?>

    </div>
    
    <hr />

    <div class="well">
        <h3>About the Author</h3>
        <p>Vestibulum molestie at augue eu bibendum. Maecenas tempus est purus, mollis vulputate urna tempor in. Integer lacinia diam at felis aliquam, vel gravida est molestie. Aliquam erat volutpat. Suspendisse ac laoreet turpis. Nunc tincidunt placerat ipsum vel pharetra. Maecenas a
            mauris massa.</p>
    </div>

    <hr />

    <div class="social-shares clearfix">
        <h3>Share</h3>

        <div class="">
            <ul class="list-unstyled list-inline">
                <li>
                    <a href="http://www.facebook.com/share.php?u=http://themes.goodlayers2.com/ideo/egestas-tellus-sit-dolor/" target="_blank">
                        <img class="no-preload" src="http://themes.goodlayers2.com/ideo/wp-content/themes/ideo-v1-10/images/icon/social-icon-m/facebook.png" width="32" height="32" style="opacity: 1;" data-scroll="1589">
                    </a>
                </li>
                <li>
                    <a href="http://twitter.com/share?url=http://themes.goodlayers2.com/ideo/egestas-tellus-sit-dolor/" target="_blank">
                        <img class="no-preload" src="http://themes.goodlayers2.com/ideo/wp-content/themes/ideo-v1-10/images/icon/social-icon-m/twitter.png" width="32" height="32" style="opacity: 1;" data-scroll="1589">
                    </a>
                </li>
                <li>
                    <a href="http://www.stumbleupon.com/submit?url=http://themes.goodlayers2.com/ideo/egestas-tellus-sit-dolor/&amp;title=Egestas%20Tellus%20Sit%20Dolor" target="_blank">
                        <img class="no-preload" src="http://themes.goodlayers2.com/ideo/wp-content/themes/ideo-v1-10/images/icon/social-icon-m/stumble-upon.png" width="32" height="32" style="opacity: 1;" data-scroll="1589">
                    </a>
                </li>
                <li>
                    <a href="http://www.myspace.com/Modules/PostTo/Pages/?u=http://themes.goodlayers2.com/ideo/egestas-tellus-sit-dolor/" target="_blank">
                        <img class="no-preload" src="http://themes.goodlayers2.com/ideo/wp-content/themes/ideo-v1-10/images/icon/social-icon-m/my-space.png" width="32" height="32" style="opacity: 1;" data-scroll="1589">
                    </a>
                </li>
                <li>
                    <a href="http://digg.com/submit?url=http://themes.goodlayers2.com/ideo/egestas-tellus-sit-dolor/&amp;title=Egestas%20Tellus%20Sit%20Dolor" target="_blank">
                        <img class="no-preload" src="http://themes.goodlayers2.com/ideo/wp-content/themes/ideo-v1-10/images/icon/social-icon-m/digg.png" width="32" height="32" style="opacity: 1;" data-scroll="1589">
                    </a>
                </li>
                <li>
                    <a href="http://reddit.com/submit?url=http://themes.goodlayers2.com/ideo/egestas-tellus-sit-dolor/&amp;title=Egestas%20Tellus%20Sit%20Dolor" target="_blank">
                        <img class="no-preload" src="http://themes.goodlayers2.com/ideo/wp-content/themes/ideo-v1-10/images/icon/social-icon-m/reddit.png" width="32" height="32" style="opacity: 1;" data-scroll="1589">
                    </a>
                </li>
                <li>
                    <a href="http://www.linkedin.com/shareArticle?mini=true&amp;url=http://themes.goodlayers2.com/ideo/egestas-tellus-sit-dolor/&amp;title=Egestas%20Tellus%20Sit%20Dolor" target="_blank">
                        <img class="no-preload" src="http://themes.goodlayers2.com/ideo/wp-content/themes/ideo-v1-10/images/icon/social-icon-m/linkedin.png" width="32" height="32" style="opacity: 1;" data-scroll="1589">
                    </a>
                </li>
                <li>
                    <a href="https://plus.google.com/share?url=http://themes.goodlayers2.com/ideo/egestas-tellus-sit-dolor/" onclick="javascript:window.open(this.href,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600');return false;">
                        <img class="no-preload" src="http://themes.goodlayers2.com/ideo/wp-content/themes/ideo-v1-10/images/icon/social-icon-m/google-plus.png" alt="google-share" width="32" height="32" style="opacity: 1;" data-scroll="1589">
                    </a>
                </li>
                <li>
                    <a href="http://pinterest.com/pin/create/button/?url=http://themes.goodlayers2.com/ideo/egestas-tellus-sit-dolor/&amp;media=http://themes.goodlayers2.com/ideo/wp-content/uploads/2013/07/photodune-4347985-choose-direction-m.jpg" class="pin-it-button"
                        count-layout="horizontal" onclick="javascript:window.open(this.href,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600');return false;">
                        <img class="no-preload" src="http://themes.goodlayers2.com/ideo/wp-content/themes/ideo-v1-10/images/icon/social-icon-m/pinterest.png" width="32" height="32" style="opacity: 1;" data-scroll="1589">
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <hr />

    <div class="comments">
        <h3>Comments</h3>
        <p>Disqus or Facebook</p>
    </div>

</article>