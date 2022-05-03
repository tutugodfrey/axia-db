<?php
echo $this->Form->create(Inflector::singularize($this->name), array(
	'inputDefaults' => array(
		'div' => 'form-group',
		'wrapInput' => true,
		'class' => 'form-control'
	),
	'class' => 'form-inline col-xs-7 col-md-7'
));

echo $this->Form->input('year', array('label' => 'Year', 'options' => $years, 'default' => date('Y')));
echo $this->Form->input('month', array('label' => 'Month', 'options' => $months));
echo '&nbsp;';
echo $this->Form->end(array('label' => 'Search', 'class' => 'btn btn-default btn-sm', 'div' => array('class' => 'form-group')));
?>