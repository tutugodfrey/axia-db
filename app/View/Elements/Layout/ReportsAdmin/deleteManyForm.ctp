<?php
echo $this->Form->create(Inflector::singularize($this->name), array(
	'url' => ['action' => 'deleteMany'],
	'inputDefaults' => array(
		'div' => 'form-group',
		'wrapInput' => true,
		'class' => 'form-control'
	),
	'id' => 'deleteManyForm',
	'class' => 'form-inline'
));
echo $this->Form->button('Delete Selected', [
	'type' => 'button',
	'id' => 'submitDeleteManyBtn',
	'class' => 'btn btn-sm btn-default pull-right',
	'disabled' => 'disabled',
	'onClick' => 'confirmDeleteMany()'
]);
//A JSON string of data to be deleted is assigned via JS on before form submit
echo $this->Form->hidden('json_delete_data');
echo $this->Form->end();
echo $this->AssetCompress->script('report-admin-functions', array(
	'raw' => (bool)Configure::read('debug')
));