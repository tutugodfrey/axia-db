<div class="col-md-12">
	<div class="contentModuleTitle">
		<?php
		echo $this->Html->sectionHeader(__('Additional Amex Costs'));
		if($this->Rbac->isPermitted('AddlAmexRepCosts/edit')){
			$editUrl = array(
				'controller' => 'AddlAmexRepCosts',
				'action' => 'edit', Hash::get($user, 'User.id'), $compensationId, $partnerUserId
			);
			echo $this->Html->link(
					$this->Html->editIcon('', array('title' => h('Edit Additional Amex Costs'))),
					$editUrl,
					array('target' => '_blank', 'escape' => false)
				);
			echo $this->AxiaHtml->ajaxContentRefresh('AddlAmexRepCosts', 'ajaxView', [$user['User']['id'], $compensationId, $partnerUserId], 'AddlAmexRepCost');
		}
		?>
	</div>
	<table class="table">
	<?php
		$amexCostCells = array();
		$convFee =  $this->Number->currency(Hash::get($amexRepCosts, 'AddlAmexRepCost.conversion_fee'), 'USD3dec');
		$sysFee =  $this->Number->currency(Hash::get($amexRepCosts, 'AddlAmexRepCost.sys_processing_fee'), 'USD3dec');
		$amexCostCells[] = array("Conversion Fee", $convFee);
		$amexCostCells[] = array("System Processing Fee", $sysFee);
		echo $this->Html->tableCells($amexCostCells);
	?>
	</table>
</div>