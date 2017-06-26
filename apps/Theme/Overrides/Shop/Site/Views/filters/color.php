<?php if (!empty($this->__colors)) { ?>
    <?php
	$tags = array ();
	if ($state = \Base::instance ()->get ( 'state' )) {
		$tags = ( array ) $state->get ( 'filter.vtags' );
	}
	?>
<div class="widget">
	<div class="widget-title">
		<h3 class="clearfix">
		Color Filter
		<button class="btn btn-default btn-sm custom-button pull-right" onclick="AmritaResetColors(this.form);">Reset</button>
		</h3>
	</div>

	<div class="widget-content">
		<div class="panel-body">
		<?php foreach ($this->__colors as $color) { ?>
		<div class="custom-checkbox half-size">
					<input id="<?php echo \Web::instance()->slug( $color['value'] ); ?>"
						type="checkbox" name="filter[vtags][]"
						value="<?php echo $color['value']; ?>"
						onchange="this.form.submit();"
						<?php if (in_array($color['value'], $tags)) { echo "checked"; } ?>>
					<label for="<?php echo \Web::instance()->slug( $color['value'] ); ?>"><?php echo ucwords( $color['text'] ); ?></label>
				</div>
		<?php } ?>
		<input type="hidden" name="filter[vtags][]" value="">
		</div>
	</div>
</div>

<script>
AmritaResetColors = function(form)
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