
<input type="hidden" id="thisViewTitle" value="<?php echo __('Edit Api Configuration'); ?>" />

<div class="text-center">
	<h4><span class="label label-success">
	<?php echo strtoupper(h($this->request->data('ApiConfiguration.configuration_name')))?> API CONNECTION CONFIG
</span></h4></div>
<?php echo $this->Form->create('ApiConfiguration', ['inputDefaults' => ['div' => 'row col-md-8']]); ?>
	<?php
		echo $this->Form->hidden('id');
		echo $this->Element('/Layout/formFieldsApiConifigs');
		echo $this->Form->end(['class' => 'btn btn-success center-block']); ?>
</div>

