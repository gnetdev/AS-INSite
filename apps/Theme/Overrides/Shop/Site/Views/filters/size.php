<?php if (!empty($this->__sizes)) { ?>
    <?php
    $tags = array();
    if ($state = \Base::instance()->get('state')) {
        $tags = (array) $state->get('filter.vtags');
    } ?>
<div class="widget">
	<div class="widget-title">
		<h3 class="clearfix">
		Size Filter
		<button class="btn btn-default btn-sm custom-button pull-right" onclick="AmritaResetSizes(this.form);">Reset</button>
		</h3>		
	</div>

	<div class="widget-content">
		<div class="panel-body">
			<?php foreach ($this->__sizes as $size) { ?>
			<div class="custom-checkbox half-size">
			<input id="<?php echo \Web::instance()->slug( $size['value'] ); ?>" type="checkbox" name="filter[vtags][]" value="<?php echo $size['value']; ?>" onchange="this.form.submit();" <?php if (in_array($size['value'], $tags)) { echo "checked"; } ?>>
			<label for="<?php echo \Web::instance()->slug( $size['value'] ); ?>"><?php echo ucwords( $size['text'] ); ?></label>
			</div>
			<?php } ?>
		<input type="hidden" name="filter[vtags][]" value="">
		</div>
	</div>
</div>

<script>
AmritaResetSizes = function(form)
{
    // loop through form elements
    var str = new Array();
    for(i=0; i<form.elements.length; i++)
    {
        var string = form.elements[i].name;
        if (string.substring(0,6) == 'filter')
        {
            form.elements[i].value = '';
        }
        if (string.substring(0,4) == 'list')
        {
            form.elements[i].value = '';
        }        
    }
    form.submit();
}
</script>
<?php } ?>