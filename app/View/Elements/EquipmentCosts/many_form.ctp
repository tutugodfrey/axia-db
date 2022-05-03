<?php
echo $this->Form->create('EquipmentCost');
echo "<table>";
echo $this->element('EquipmentCosts/grid_table_headers');

$tableCells = array();
foreach (Hash::get($userEquipment, 'EquipmentCost') as $index => $equipmentCost) {
	$equipmentItemName = Hash::get($equipmentCost, 'EquipmentItem.equipment_item_description');

	$firstCell = h($equipmentItemName);
	//add the hidden inputs to de first cell
	$firstCell .= $this->Form->htmlInput("EquipmentCost.{$index}.id", array(
		'value' => Hash::get($this->request->data, "EquipmentCost.{$index}.id"),
		'type' => 'hidden'
	));
	$firstCell .= $this->Form->htmlInput("EquipmentCost.{$index}.user_compensation_profile_id", array(
		'value' => Hash::get($this->request->data, "UserCompensationProfile.id"),
		'type' => 'hidden'
	));
	$firstCell .= $this->Form->htmlInput("EquipmentCost.{$index}.equipment_item_id", array(
		'value' => Hash::get($this->request->data, "EquipmentCost.{$index}.equipment_item_id"),
		'type' => 'hidden'
	));
	$tableCellRBACContent = array(
			$firstCell,
			$this->Form->htmlInput("EquipmentCost.{$index}.rep_cost", array(
				'value' => Hash::get($this->request->data, "EquipmentCost.{$index}.rep_cost")
			)),
			$this->Number->currency(Hash::get($this->request->data, "EquipmentCost.{$index}.partner_cost"), 'USD', array('after' => false)),
		);
	if ($this->Rbac->isPermitted('app/actions/EquipmentCosts/view/module/trueCost', true)){
		$tableCellRBACContent[] = $this->Number->currency(Hash::get($this->request->data, "EquipmentCost.{$index}.EquipmentItem.equipment_item_true_price"), 'USD', array('after' => false));
	}
	$tableCells[] = $tableCellRBACContent;
}
echo $this->Html->tableCells($tableCells);

echo "</table>";
$cancelRedirect = ['controller' => 'Users', 'action' => 'view', Hash::get($userEquipment, 'User.id')];
echo $this->Form->defaultButtons(null, $cancelRedirect);
echo $this->Form->end();
