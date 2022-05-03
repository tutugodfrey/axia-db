<?php
echo $this->element('Layout/generalizedViewTitle');
echo $this->extend('/BaseViews/base');
echo $this->Form->create('AddlAmexRepCost');
$gridInputOptions = array(
	'label' => false,
	'type' => 'number',
	"step" => ".001",
);
$tableRows = array();
	$tableRow = $this->Form->hidden("AddlAmexRepCost.id");
	$tableRow .= $this->Form->hidden("AddlAmexRepCost.user_compensation_profile_id");
	$tableRow .= $this->Html->tableCells(array(
		"Conversion Fee",
		$this->Form->input("AddlAmexRepCost.conversion_fee", $gridInputOptions),
	));
	$tableRow .= $this->Html->tableCells(array(
		"System Processing Fee",
		$this->Form->input("AddlAmexRepCost.sys_processing_fee", $gridInputOptions),
	));
	$tableRows[] = $tableRow;

$tableBody = $this->Html->tag('tbody', implode('', $tableRows));
echo $this->Html->tag('table',$tableBody, array('class' => 'table'));
$cancelRedirect = ['controller' => 'Users', 'action' => 'view', $userId];
echo $this->Form->defaultButtons(null, $cancelRedirect);
echo $this->Form->end();
