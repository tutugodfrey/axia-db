<div class="row">
	<div class="col-md-12">
		<div class="contentModuleTitle panel-heading bg-success">
			<?php
			echo $this->Html->sectionHeader(__('Rep & Manager Time Grid'));
			if ($this->Rbac->isPermitted('ResidualTimeFactors/editResidualTimeGrid')) {
				$editUrl = array(
					'controller' => 'ResidualTimeFactors',
					'action' => 'editResidualTimeGrid', Hash::get($user, 'User.id'), $compensationId, $partnerUserId
				);
				echo $this->Html->link(
						$this->Html->editIcon('', array('title' => h('Edit Rep and Manager Time Grid'))),
						$editUrl,
						array('target' => '_blank', 'escape' => false)
					);
				echo $this->AxiaHtml->ajaxContentRefresh('ResidualTimeFactors', 'ajaxView', [$user['User']['id'], $compensationId, $partnerUserId], 'residualTimeFactorsContainer');
			}
			echo $this->Form->input('UserCompensationProfile.is_profile_option_2', array(
				'label' => __('Option 2'),
				'type' => 'checkbox',
				'checked' => Hash::get($residualTimeFact, 'UserCompensationProfile.is_profile_option_2')
			));
			?>
		</div>
		<div class="col-md-6">
			<?php echo $this->element('ResidualTimeFactors/view', array('residualTimeFactor' => Hash::get($residualTimeFact, 'ResidualTimeFactor'))); ?>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?php echo $this->element('ResidualTimeParameters/view'); ?>
	</div>
</div>