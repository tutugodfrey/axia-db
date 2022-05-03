<div class="col-md-12">
		<div class="contentModuleTitle">
			<?php
			echo $this->Html->sectionHeader(__('Rep Monthly Costs'));
			if($this->Rbac->isPermitted('RepMonthlyCosts/editMany')){
				$editUrl = array(
					'controller' => 'RepMonthlyCosts',
					'action' => 'editMany', Hash::get($user, 'User.id'), $compensationId, $partnerUserId
				);
				echo $this->Html->link(
						$this->Html->editIcon('', array('title' => h('Edit Rep Monthly Costs'))),
						$editUrl,
						array('target' => '_blank', 'escape' => false)
					);
				echo $this->AxiaHtml->ajaxContentRefresh('RepMonthlyCosts', 'ajaxView', [$user['User']['id'], $compensationId, $partnerUserId], 'RepMonthlyCost');
			}
			?>
		</div>
		<table class="table">
		<?php
			echo $this->element('RepBetNetworkMonthlyCosts/grid_table_headers');
			$repCostCells = array();
			foreach ($betNetworks as $betNid => $networkName) {
				$creditCost =  $this->Number->currency(Hash::get(Hash::extract($repMonthlyCosts['RepMonthlyCost'],"{n}.RepMonthlyCost[bet_network_id = $betNid].credit_cost"), '0'), 'USD2dec');
				$debitCost =  $this->Number->currency(Hash::get(Hash::extract($repMonthlyCosts['RepMonthlyCost'],"{n}.RepMonthlyCost[bet_network_id = $betNid].debit_cost"), '0'), 'USD2dec');
				$ebtCost =  $this->Number->currency(Hash::get(Hash::extract($repMonthlyCosts['RepMonthlyCost'],"{n}.RepMonthlyCost[bet_network_id = $betNid].ebt_cost"), '0'), 'USD2dec');
				$repCostCells[] = array(h($networkName), $creditCost, $debitCost, $ebtCost);
			}
			echo $this->Html->tableCells($repCostCells);
		?>
		</table>
	</div>