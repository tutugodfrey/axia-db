<div class="col-md-12">
	<div class="contentModuleTitle">
		<?php
		echo $this->Html->sectionHeader(__('Web ACH Rep Costs'));
		if($this->Rbac->isPermitted('WebAchRepCosts/edit')){
			$editUrl = array(
				'controller' => 'WebAchRepCosts',
				'action' => 'edit', Hash::get($user, 'User.id'), $compensationId, $partnerUserId
			);
			echo $this->Html->link(
					$this->Html->editIcon('', array('title' => h('Edit Web ACH Rep Costs'))),
					$editUrl,
					array('target' => '_blank', 'escape' => false)
				);
			echo $this->AxiaHtml->ajaxContentRefresh('WebAchRepCosts', 'ajaxView', [$user['User']['id'], $compensationId, $partnerUserId], 'WebAchRepCost');
		}
		?>
	</div>
	<table class="table">
	<?php
		$achCostCells = array();
		$achRate =  $this->Number->toPercentage(Hash::get($webAchCosts, 'WebAchRepCost.rep_rate_pct'));
		$achPerItem =  $this->Number->currency(Hash::get($webAchCosts, 'WebAchRepCost.rep_per_item', 'USD3dec'));
		$achMonthly =  $this->Number->currency(Hash::get($webAchCosts, 'WebAchRepCost.rep_monthly_cost', 'USD3dec'));
		$achCostCells[] = array(" Rep Cost %", $achRate);
		$achCostCells[] = array(" Rep Per Item Cost", $achPerItem);
		$achCostCells[] = array(" Rep Monthly Cost", $achMonthly);
		echo $this->Html->tableCells($achCostCells);
	?>
	</table>
</div>