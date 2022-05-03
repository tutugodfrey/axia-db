<?php
/* Drop breadcrumb */
$this->Html->addCrumb($this->name . ' ' . $this->action, '/' . $this->name . '/' . $this->action);
?>
<div class="reportTables">
	<?php
	echo $this->Form->create('LastDepositReport', array(
		'inputDefaults' => array(
			'div' => false,
			'label' => false,
			'wrapInput' => false,
			'class' => 'form-control'
		),
		'class' => 'well well-lg form-inline',
		'type' => 'file',
		'url' => array(
			'plugin' => null,
			'controller' => 'LastDepositReports',
			'action' => 'upload',
		),
		'id' => 'upload-form'
	));
	echo $this->Form->input('file', array(
		'label' => __('Select a CSV file:'),
		'class' => 'btn btn-info',
		'type' => 'file',
		'required' => true
	));
	echo $this->Form->button(
			$this->Html->tag('span', '', array('class' => 'glyphicon glyphicon-open')) . ' Upload', array(
		'type' => 'submit',
		'class' => 'btn btn-success'
	));
	echo $this->Form->end();
	?>
</div>
