<?php
echo $this->element('Layout/generalizedViewTitle');
echo $this->extend('/BaseViews/base');
echo $this->Form->create('AchRepCost');
$gridInputOptions = array(
	'label' => false,
	'type' => 'number',
	"step" => ".001",
	'max' => 100,
);
$dataCount = count(Hash::extract($this->request->data, '{n}.AchRepCost'));
$inputIndex = null;
$tableHeader = $this->element('AchRepCosts/grid_table_headers');
$tableRows = array();
foreach ($achProviers as $id => $name) {
	$tableRow = ''; //reset table Html
	foreach ($this->request->data as $index => $achRepCost) {
		if ($achRepCost['AchRepCost']['ach_provider_id'] === $id) {
			$inputIndex = $index;
			break;
		}
	}
	if (is_null($inputIndex)) {
		$inputIndex = $dataCount;
		$dataCount++;
	}
	if (!empty($this->request->data("$inputIndex.AchRepCost.id"))) {
		$tableRow .= $this->Form->hidden("$inputIndex.AchRepCost.id");
	}
	$tableRow .= $this->Form->hidden("$inputIndex.AchRepCost.user_compensation_profile_id", ['value' => $compensationId]);
	$tableRow .= $this->Form->hidden("$inputIndex.AchRepCost.ach_provider_id", ['value' => $id]);
	$tableRow .= $this->Html->tableCells(array(
		h($name),
		$this->Form->input("$inputIndex.AchRepCost.rep_monthly_cost", $gridInputOptions),
		$this->Form->input("$inputIndex.AchRepCost.rep_rate_pct", $gridInputOptions),
		$this->Form->input("$inputIndex.AchRepCost.rep_per_item", $gridInputOptions),
	));

	$tableRows[] = $tableRow;
	$inputIndex = null;
}
$tableBody = $this->Html->tag('tbody', implode('', $tableRows));
echo $this->Html->tag('table', $tableHeader . $tableBody, array('class' => 'table'));
$cancelRedirect = ['controller' => 'Users', 'action' => 'view', $userId];
echo $this->Form->defaultButtons(null, $cancelRedirect);
echo $this->Form->end();
