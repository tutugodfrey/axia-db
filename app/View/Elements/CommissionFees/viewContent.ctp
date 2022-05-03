	<div class="col-md-12">
		<div class="contentModuleTitle panel-heading bg-success">
			<?php
			echo $this->Html->sectionHeader(__('Commissions'));
			if($this->Rbac->isPermitted('CommissionFees/editMany')){
				$url = array(
					'controller' => 'CommissionFees',
					'action' => 'editMany', Hash::get($commissionFees, 'User.id'), Hash::get($commissionFees, 'UserCompensationProfile.id'), $partnerUserId
				);
				echo $this->Html->link(
						$this->Html->editIcon(),
						$url,
						array('target' => '_blank', 'escape' => false));
				echo $this->AxiaHtml->ajaxContentRefresh('CommissionFees', 'ajaxView', [$compensationId, $partnerUserId], 'CommissionFeesMainContent');
			}
			?>
		</div>
		<div class="col-md-12">
			<?php
			echo $this->element('CommissionFees/table', array(
				'commissionFees' => $commissionFees,
				'action' => 'view'
			));
			?>
		</div>
	</div>