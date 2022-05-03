<div class="col-md-12">
	<div class="contentModuleTitle panel-heading bg-success">
		<?php
		echo $this->Html->sectionHeader(__('Equipment List'));		
		if ($this->Rbac->isPermitted('EquipmentCosts/editMany')) {
			$editUrl = array(
				'controller' => 'EquipmentCosts',
				'action' => 'editMany', Hash::get($userEquipment, 'User.id'), Hash::get($userEquipment, 'UserCompensationProfile.id'), $partnerUserId
			);
			echo $this->Html->link(
					$this->Html->editIcon('', array('title' => h('Edit Equipment List'))),
					$editUrl,
					array('target' => '_blank', 'escape' => false)
				);
			echo $this->AxiaHtml->ajaxContentRefresh('EquipmentCosts', 'ajaxView', [$compensationId, $partnerUserId], 'EqCostsMainContainer');
		}
		?>
	</div>
	<table class="table">
		<?php
		echo $this->element('EquipmentCosts/grid_table_headers');
		$userId = Hash::get($userEquipment, 'User.id');
		$equipmentCostsCells = array();
		foreach (Hash::get($userEquipment, 'EquipmentCost') as $equipmentCost) {
			$rowRBAC = array(
				h(__(Hash::get($equipmentCost, 'EquipmentItem.equipment_item_description'))),
				$this->Number->currency(Hash::get($equipmentCost, "rep_cost")),
				$this->Number->currency(Hash::get($equipmentCost, "partner_cost")),
			);
			if ($this->Rbac->isPermitted('app/actions/EquipmentCosts/view/module/trueCost', true)){
				$rowRBAC[] = $this->Number->currency(Hash::get($equipmentCost, "EquipmentItem.equipment_item_true_price"));
			}
			$equipmentCostsCells[] = $rowRBAC;
		}
		echo $this->Html->tableCells($equipmentCostsCells);
		?>
	</table>
</div>