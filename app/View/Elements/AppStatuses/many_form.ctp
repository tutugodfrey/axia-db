<?php

echo $this->Form->create('AppStatus');
$compensationId = $this->request->data('UserCompensationProfile.id');
$gridInputOptions = array(
		  'label' => false,
);
echo "<table>";
echo $this->element('AppStatuses/grid_table_headers');

$newDatIdx = count($this->request->data('AppStatus'));
//to keep track of the saveMany items
foreach ($merchantAchAppStatuses as $merchantAchAppStatusId => $merchantAchAppStatus) {
	foreach ($this->request->data('AppStatus') as $yIdx => $data) {
		if ($data['merchant_ach_app_status_id'] === $merchantAchAppStatusId) {
			$xIdx = $yIdx;
			break;
		} else {
			$xIdx = null;
		}
	}

	if (!isset($xIdx)) {
		$xIdx = $newDatIdx;
		$newDatIdx ++;
	}

	echo $this->Form->hidden("AppStatus.$xIdx.id");
	echo $this->Form->hidden("AppStatus.$xIdx.user_compensation_profile_id", array('value' => $compensationId));
	echo $this->Form->hidden("AppStatus.$xIdx.merchant_ach_app_status_id", array('value' => $merchantAchAppStatusId));

	echo $this->Html->tableCells(array(
			  h($merchantAchAppStatus),
			  $this->Form->input("AppStatus.$xIdx.rep_cost", $gridInputOptions),
			  $this->Form->input("AppStatus.$xIdx.axia_cost", $gridInputOptions),
			  $this->Form->input("AppStatus.$xIdx.rep_expedite_cost", $gridInputOptions),
			  $this->Form->input("AppStatus.$xIdx.axia_expedite_cost_tsys", $gridInputOptions),
			  $this->Form->input("AppStatus.$xIdx.axia_expedite_cost_sage", $gridInputOptions),
	));
	$xIdx = null;
}

echo "</table>";
$cancelRedirect = ['controller' => 'Users', 'action' => 'view', Hash::get($this->request->data, 'User.id')];
echo $this->Form->defaultButtons(null, $cancelRedirect);
echo $this->Form->end();
