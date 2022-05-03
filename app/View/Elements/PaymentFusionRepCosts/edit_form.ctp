<?php
echo $this->element('Layout/generalizedViewTitle');
// echo $this->extend('/BaseViews/base');
echo $this->Form->create('PaymentFusionRepCost');
$gridInputOptions = array(
	'label' => false,
	'type' => 'number',
	"step" => ".001",
);
$tableRows = array();
	$tableRow = $this->Form->hidden("PaymentFusionRepCost.id");
	$tableRow .= $this->Form->hidden("PaymentFusionRepCost.user_compensation_profile_id");
	$tableRow .= $this->Html->tableCells(array(
		"Rep Per Item Cost",
		$this->Form->input("PaymentFusionRepCost.rep_per_item", $gridInputOptions),
	));
	$tableRow .= $this->Html->tableCells(array(
		"Rep Monthly Cost",
		$this->Form->input("PaymentFusionRepCost.rep_monthly_cost", $gridInputOptions),
	));
	$tableRow .= $this->Html->tableCells(array(
		"Standard Device Cost",
		$this->Form->input("PaymentFusionRepCost.standard_device_cost", $gridInputOptions),
	));
	$tableRow .= $this->Html->tableCells(array(
		"VP2PE Device Cost",
		$this->Form->input("PaymentFusionRepCost.vp2pe_device_cost", $gridInputOptions),
	));
	$tableRow .= $this->Html->tableCells(array(
		"PFCC Device Cost",
		$this->Form->input("PaymentFusionRepCost.pfcc_device_cost", $gridInputOptions),
	));
	$tableRow .= $this->Html->tableCells(array(
		"VP2PE & PFCC Device Cost",
		$this->Form->input("PaymentFusionRepCost.vp2pe_pfcc_device_cost", $gridInputOptions),
	));
	$tableRows[] = $tableRow;

$tableBody = $this->Html->tag('tbody', implode('', $tableRows));
echo $this->Html->tag('table',$tableBody, array('class' => 'table'));
$cancelRedirect = ['controller' => 'Users', 'action' => 'view', $userId];
echo $this->Form->defaultButtons(null, $cancelRedirect);
echo $this->Form->end();
