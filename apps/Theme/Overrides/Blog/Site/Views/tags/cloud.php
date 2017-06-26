<?php if ($tags = \Blog\Models\Posts::distinctTags(array(
	'publication.status' => 'published'
))) { ?>
<div class="widget widget-tags">
    <div class="widget-title">
        <h2>Tags</h2>
    </div>
	<div class="widget-content">
	
		<ul class='tag-cloud'>
            <?php foreach( $tags as $tag ) { ?>
                <li class="tag">
				    <a class="btn btn-default" href="./blog/tag/<?php echo $tag; ?>"><?php echo $tag;?></a>
				</li>
            <?php } ?>
		</ul>
		
	</div>
</div>
<?php } ?>