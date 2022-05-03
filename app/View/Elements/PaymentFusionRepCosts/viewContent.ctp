<div class="col-md-12">
	<div class="contentModuleTitle">
		<?php
		echo $this->Html->sectionHeader(__('Payment Fusion Rep Costs'));
		if($this->Rbac->isPermitted('PaymentFusionRepCosts/edit')){
			$editUrl = array(
				'controller' => 'PaymentFusionRepCosts',
				'action' => 'edit', Hash::get($user, 'User.id'), $compensationId, $partnerUserId
			);
			echo $this->Html->link(
					$this->Html->editIcon('', array('title' => h('Edit Payment Fusion Rep Costs'))),
					$editUrl,
					array('target' => '_blank', 'escape' => false)
				);
			echo $this->AxiaHtml->ajaxContentRefresh('PaymentFusionRepCosts', 'ajaxView', [$user['User']['id'], $compensationId, $partnerUserId], 'PaymentFusionRepCost');
		}
		?>
	</div>
	<table class="table">
	<?php
		$cells = array();
		$perItem =  $this->Number->currency(Hash::get($paymentFusionCosts, 'PaymentFusionRepCost.rep_per_item', 'USD3dec'));
		$monthly =  $this->Number->currency(Hash::get($paymentFusionCosts, 'PaymentFusionRepCost.rep_monthly_cost', 'USD3dec'));

		$cells[] = array(" Rep Per Item Cost", $perItem);
		$cells[] = array(" Rep Monthly Cost", $monthly);
		$cells[] = array(" Standard Device Cost", $this->Number->currency(Hash::get($paymentFusionCosts, 'PaymentFusionRepCost.standard_device_cost', 'USD3dec')));
		$cells[] = array(" VP2PE Device Cost", $this->Number->currency(Hash::get($paymentFusionCosts, 'PaymentFusionRepCost.vp2pe_device_cost', 'USD3dec')));
		$cells[] = array(" PFCC Device Cost", $this->Number->currency(Hash::get($paymentFusionCosts, 'PaymentFusionRepCost.pfcc_device_cost', 'USD3dec')));
		$cells[] = array(" VP2PE & PFCC Device Cost", $this->Number->currency(Hash::get($paymentFusionCosts, 'PaymentFusionRepCost.vp2pe_pfcc_device_cost', 'USD3dec')));
		echo $this->Html->tableCells($cells);
	?>
	</table>
</div>