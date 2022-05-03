<?php
echo $this->element('Layout/generalizedViewTitle');
echo $this->extend('/BaseViews/base');
echo $this->Form->create('RepMonthlyCost');
$gridInputOptions = array(
	'label' => false,
	'type' => 'number',
	"step" => ".001",
);
$dataCount = count(Hash::extract($this->request->data, '{n}.RepMonthlyCost'));
$inputIndex = null;
$tableHeader = $this->element('RepBetNetworkMonthlyCosts/grid_table_headers');
$tableRows = array();
foreach ($betNetworks as $id => $name) {
	$tableRow = '';
	foreach ($this->request->data as $index => $repCost) {
		if ($repCost['RepMonthlyCost']['bet_network_id'] === $id) {
			$inputIndex = $index;
			break;
		}
	}
	if (is_null($inputIndex)) {
		$inputIndex = $dataCount;
		$dataCount++;
	}

	if (!empty($this->request->data("$inputIndex.RepMonthlyCost.id"))) {
		$tableRow .= $this->Form->hidden("$inputIndex.RepMonthlyCost.id");
	}
	$tableRow .= $this->Form->hidden("$inputIndex.RepMonthlyCost.user_compensation_profile_id", ['value' => $compensationId]);
	$tableRow .= $this->Form->hidden("$inputIndex.RepMonthlyCost.bet_network_id", ['value' => $id]);
	$tableRow .= $this->Html->tableCells(array(
		h($name),
		$this->Form->input("$inputIndex.RepMonthlyCost.credit_cost", $gridInputOptions),
		$this->Form->input("$inputIndex.RepMonthlyCost.debit_cost", $gridInputOptions),
		$this->Form->input("$inputIndex.RepMonthlyCost.ebt_cost", $gridInputOptions)
	));
	$tableRows[] = $tableRow;
	$inputIndex = null;
}
$tableBody = $this->Html->tag('tbody', implode('', $tableRows));
echo $this->Html->tag('table', $tableHeader . $tableBody, array('class' => 'table'));
$cancelRedirect = ['controller' => 'Users', 'action' => 'view', $userId];
echo $this->Form->defaultButtons(null, $cancelRedirect);
echo $this->Form->end();
