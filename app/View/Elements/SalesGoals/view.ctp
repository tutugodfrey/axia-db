<div class="row sales-goals table-responsive">
	<div class="col-md-12">
		<div class="contentModuleTitle">
			<?php
			$editUrl = ($this->Rbac->IsPermitted('Users/edit')) ? array(
				'controller' => 'SalesGoals',
				'action' => 'editMany', Hash::get($user, 'User.id')
				) : null;
			echo $this->Html->sectionHeader(__('Account Goals'), $editUrl);
			?>
		</div>
		<table class="table">
			<?php
			echo $this->element('SalesGoals/grid_table_headers');
			$salesGoalsCells = array();
			$calInfo = cal_info(CAL_GREGORIAN);
			foreach ($calInfo['months'] as $monthIndex => $month) {
				$data = Hash::extract($salesGoals, "{n}[goal_month=$monthIndex]");
				$salesGoalsRow = array(
					__($month),
					$this->Number->currency(Hash::get($data, "0.goal_accounts")),
					$this->Number->currency(Hash::get($data, "0.goal_volume")),
					$this->Number->currency(Hash::get($data, "0.goal_profits")),
					$this->Number->currency(Hash::get($data, "0.goal_statements")),
					$this->Number->currency(Hash::get($data, "0.goal_calls")),
				);
				$salesGoalsCells[] = $salesGoalsRow;
			}
			echo $this->Html->tableCells($salesGoalsCells);
			?>
		</table>
	</div>
</div>