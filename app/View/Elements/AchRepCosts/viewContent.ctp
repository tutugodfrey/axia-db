<div class="col-md-12">
	<div class="contentModuleTitle">
		<?php
		echo $this->Html->sectionHeader(__('ACH Costs'));
		if($this->Rbac->isPermitted('AchRepCosts/editMany')){
			$editUrl = array(
				'controller' => 'AchRepCosts',
				'action' => 'editMany', Hash::get($user, 'User.id'), $compensationId, $partnerUserId
			);
			echo $this->Html->link(
					$this->Html->editIcon('', array('title' => __('Edit ACH Costs'))),
					$editUrl,
					array('target' => '_blank', 'escape' => false)
				);
			echo $this->AxiaHtml->ajaxContentRefresh('AchRepCosts', 'ajaxView', [$user['User']['id'], $compensationId, $partnerUserId], 'AchRepCost');
		}
		?>
	</div>
	<table class="table">
	<?php
		echo $this->element('AchRepCosts/grid_table_headers');
		$achCostCells = array();
		foreach ($achProviers as $pId => $providerName) {
			$achRate =  $this->Number->toPercentage(Hash::get(Hash::extract($achRepCosts['AchRepCost'],"{n}.AchRepCost[ach_provider_id = $pId].rep_rate_pct"), '0'));
			$achPerItem =  $this->Number->currency(Hash::get(Hash::extract($achRepCosts['AchRepCost'],"{n}.AchRepCost[ach_provider_id = $pId].rep_per_item"), '0'), 'USD3dec');
			$achMonthly =  $this->Number->currency(Hash::get(Hash::extract($achRepCosts['AchRepCost'],"{n}.AchRepCost[ach_provider_id = $pId].rep_monthly_cost"), '0'), 'USD3dec');
			$achCostCells[] = array(h($providerName), $achMonthly, $achRate, $achPerItem);
		}
		echo $this->Html->tableCells($achCostCells);
	?>
	</table>
</div>