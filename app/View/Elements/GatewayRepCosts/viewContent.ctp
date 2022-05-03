<div class="col-md-12">
	<div class="contentModuleTitle">
		<?php
		echo $this->Html->sectionHeader(__('Rep Gateway Costs'));
		if($this->Rbac->isPermitted('GatewayCostStructures/editMany')){
			$editUrl = array(
				'controller' => 'GatewayCostStructures',
				'action' => 'editMany', Hash::get($user, 'User.id'), $compensationId, $partnerUserId
			);
			echo $this->Html->link(
					$this->Html->editIcon('', array('title' => h('Edit Rep Gateway Costs'))),
					$editUrl,
					array('target' => '_blank', 'escape' => false)
				);
			echo $this->AxiaHtml->ajaxContentRefresh('GatewayCostStructures', 'ajaxView', [$user['User']['id'], $compensationId, $partnerUserId], 'GwCostStructuresContainer');
		}
		?>
	</div>
	<table class="table">
	<?php
		echo $this->element('GatewayRepCosts/grid_table_headers');
		$repCostCells = array();
		foreach ($gateways as $gwid => $gatewayName) {
			$monthlyCost =  $this->Number->currency(Hash::get(Hash::extract($gwrepCost['GatewayCostStructure'],"{n}.GatewayCostStructure[gateway_id = $gwid].rep_monthly_cost"), '0'), 'USD2dec');
			$repPctCost =  $this->Number->toPercentage(Hash::get(Hash::extract($gwrepCost['GatewayCostStructure'],"{n}.GatewayCostStructure[gateway_id = $gwid].rep_rate_pct"), '0'));
			$piCost =  $this->Number->currency(Hash::get(Hash::extract($gwrepCost['GatewayCostStructure'],"{n}.GatewayCostStructure[gateway_id = $gwid].rep_per_item"), '0'), 'USD3dec');
			$repCostCells[] = array(h($gatewayName), $monthlyCost, $repPctCost, $piCost);
		}
		echo $this->Html->tableCells($repCostCells);
	?>
	</table>
</div>