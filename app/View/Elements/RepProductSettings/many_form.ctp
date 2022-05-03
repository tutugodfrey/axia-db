<?php
echo $this->element('Layout/generalizedViewTitle');
echo $this->extend('/BaseViews/base');
echo $this->Form->create('RepProductSetting');
$gridInputOptions = array(
	'label' => false,
	'type' => 'number',
	"step" => ".001",
);
$dataCount = count(Hash::extract($this->request->data, '{n}.RepProductSetting'));
$inputIndex = null;
$tableHeader = $this->element('RepProductSettings/grid_table_headers');
$tableRows = array();

foreach (Hash::extract($prodsWithSettings, '{n}.ProductsServicesType') as $product) {
	$tableRow = ''; //reset table Html string
	foreach ($this->request->data as $index => $repCost) {
		if ($repCost['RepProductSetting']['products_services_type_id'] === $product['id']) {
			$inputIndex = $index;
			break;
		}
	}
	if (is_null($inputIndex)) {
		$inputIndex = $dataCount;
		$dataCount++;
	}
	if (!empty($this->request->data("$inputIndex.RepProductSetting.id"))) {
		$tableRow .= $this->Form->hidden("$inputIndex.RepProductSetting.id");
	}
	$tableRow .= $this->Form->hidden("$inputIndex.RepProductSetting.user_compensation_profile_id", ['value' => $compensationId]);
	$tableRow .= $this->Form->hidden("$inputIndex.RepProductSetting.products_services_type_id", ['value' => $product['id']]);
	$tableRow .= $this->Html->tableCells(array(
		h($product['products_services_description']),
		$this->Form->input("$inputIndex.RepProductSetting.rep_monthly_cost", $gridInputOptions),
		$this->Form->input("$inputIndex.RepProductSetting.rep_per_item", $gridInputOptions),
		$this->Form->input("$inputIndex.RepProductSetting.provider_device_cost", $gridInputOptions)
	));
	$tableRows[] = $tableRow;
	$inputIndex = null;
}
$tableBody = $this->Html->tag('tbody', implode('', $tableRows));
echo $this->Html->tag('table', $tableHeader . $tableBody, array('class' => 'table'));
$cancelRedirect = ['controller' => 'Users', 'action' => 'view', $userId];
echo $this->Form->defaultButtons(null, $cancelRedirect);
echo $this->Form->end();
