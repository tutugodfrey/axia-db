<div class="row">
	<div class="col-md-12">
		<div class="contentModuleTitle panel-heading bg-success">
			<?php
			echo $this->Html->sectionHeader(__('Rep & Manager Residual Grid'));
			if($this->Rbac->isPermitted('ResidualVolumeTiers/editResidualGrid')){
				$editUrl = array(
						'controller' => 'ResidualVolumeTiers',
						'action' => 'editResidualGrid', Hash::get($user, 'User.id'), $compensationId, $partnerUserId
				);
				echo $this->Html->link(
						$this->Html->editIcon('', array('title' => h('Edit Rep and Manager Residual Grid'))),
						$editUrl,
						array('target' => '_blank', 'escape' => false)
					);
				echo $this->AxiaHtml->ajaxContentRefresh('ResidualVolumeTiers', 'ajaxView', [$user['User']['id'], $compensationId, $partnerUserId], 'residualVolumeTiersContainer');
			}
			echo $this->Form->input('UserCompensationProfile.is_profile_option_1', array(
				'label' => __('Option 1'),
				'type' => 'checkbox',
				'checked' => Hash::get($residualVolTiers, 'UserCompensationProfile.is_profile_option_1')
			));
			?>
		</div>
		<div class="col-md-6">
			<?php echo $this->element('ResidualVolumeTiers/view', array('residualVolumeTier' => Hash::get($residualVolTiers, 'ResidualVolumeTier'))); ?>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?php echo $this->element('ResidualParameters/view'); ?>
	</div>
</div>