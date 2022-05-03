<input type="hidden" id="thisViewTitle" value="<?php echo __('Edit Api Configuration'); ?>" />

<div class="text-center">
	<h4><span class="label label-success">
	NEW API CONNECTION CONFIG
</span></h4></div>
<?php echo $this->Form->create('ApiConfiguration', ['inputDefaults' => ['div' => 'row col-md-8']]); ?>
	<?php
		echo $this->Form->input('configuration_name', ['label' => "Config name",
			'before' => '<span class="pull-right text-danger">The connection config name must include the name of the external system it connects to. If this is for testing, type "test" at the end i.e.: salesforce test.</span>',
		 'placeholder' => 'Must be unique.']);
		echo $this->Element('/Layout/formFieldsApiConifigs');
		echo $this->Form->end(['class' => 'btn btn-success center-block']); ?>
</div>

