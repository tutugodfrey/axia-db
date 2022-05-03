<?php
echo $this->element('Layout/generalizedViewTitle');
echo $this->extend('/BaseViews/base');
echo $this->Form->create('WebAchRepCost');
$gridInputOptions = array(
	'label' => false,
	'type' => 'number',
	"step" => ".001",
);
$tableRows = array();
	$tableRow = $this->Form->hidden("WebAchRepCost.id");
	$tableRow .= $this->Form->hidden("WebAchRepCost.user_compensation_profile_id");
	$tableRow .= $this->Html->tableCells(array(
		"Rep Cost %",
		$this->Form->input("WebAchRepCost.rep_rate_pct", $gridInputOptions),
	));
	$tableRow .= $this->Html->tableCells(array(
		"Rep Per Item Cost",
		$this->Form->input("WebAchRepCost.rep_per_item", $gridInputOptions),
	));
	$tableRow .= $this->Html->tableCells(array(
		"Rep Per Item Cost",
		$this->Form->input("WebAchRepCost.rep_monthly_cost", $gridInputOptions),
	));
	$tableRows[] = $tableRow;

$tableBody = $this->Html->tag('tbody', implode('', $tableRows));
echo $this->Html->tag('table',$tableBody, array('class' => 'table'));
$cancelRedirect = ['controller' => 'Users', 'action' => 'view', $userId];
echo $this->Form->defaultButtons(null, $cancelRedirect);
echo $this->Form->end();
