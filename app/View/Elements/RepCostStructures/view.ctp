<div class="row rep-cost-structures table-responsive" id="RepCostStuctures">
	<div class="col-md-12">
		<div class="contentModuleTitle panel-heading bg-success">
			<?php
			echo $this->Html->sectionHeader(__('Rep Cost Structures'));
			?>
		</div>
		<table class="table">
			<?php
			$costStructCells = array(
				$this->element('GatewayRepCosts/view'),
				$this->element('RepBetNetworkMonthlyCosts/view'),
				$this->element('AchRepCosts/view'),
			);

			echo $this->Html->tableCells($costStructCells);

			$costStructCells = array(
				$this->element('PaymentFusionRepCosts/view'),
				$this->element('WebAchRepCosts/view'),
				$this->element('AddlAmexRepCosts/view')
			);
			echo $this->Html->tableCells($costStructCells);
			?>
		</table>
	</div>
</div>
