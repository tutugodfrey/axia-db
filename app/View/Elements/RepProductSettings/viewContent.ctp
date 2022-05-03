<div class="col-md-12">
	<div class="contentModuleTitle panel-heading bg-success">
		<?php
		echo $this->Html->sectionHeader(__('Rep Products Settings'));
		if($this->Rbac->isPermitted('RepProductSettings/editMany')){
			$editUrl = array(
				'controller' => 'RepProductSettings',
				'action' => 'editMany', Hash::get($user, 'User.id'), $compensationId, $partnerUserId
			);
			echo $this->Html->link(
					$this->Html->editIcon('', array('title' => h('Edit Rep Products Costs Settings'))),
					$editUrl,
					array('target' => '_blank', 'escape' => false)
				);
			echo $this->AxiaHtml->ajaxContentRefresh('RepProductSettings', 'ajaxView', [$user['User']['id'], $compensationId, $partnerUserId], 'RepProductSettings');
		}
		?>
	</div>
	<table class="table">
	<?php
		echo $this->element('RepProductSettings/grid_table_headers');
		$repCostCells = array();
		foreach (Hash::extract($prodsWithSettings, '{n}.ProductsServicesType') as $pDat) {
			$monthlyCost =  $this->Number->currency(Hash::get(Hash::extract($repProdSettings['RepProductSetting'],"{n}.RepProductSetting[products_services_type_id = {$pDat['id']}].rep_monthly_cost"), '0'), 'USD2dec');
			$piCost =  $this->Number->currency(Hash::get(Hash::extract($repProdSettings['RepProductSetting'],"{n}.RepProductSetting[products_services_type_id = {$pDat['id']}].rep_per_item"), '0'), 'USD3dec');
			$provDevCost =  $this->Number->currency(Hash::get(Hash::extract($repProdSettings['RepProductSetting'],"{n}.RepProductSetting[products_services_type_id = {$pDat['id']}].provider_device_cost"), '0'), 'USD2dec');
			$repCostCells[] = array(h($pDat['products_services_description']), $monthlyCost, $piCost, $provDevCost);
		}
		echo $this->Html->tableCells($repCostCells);
	?>
	</table>
</div>