<div class="row">
	<div class="col-md-12">
		<?php
		if (!$isEditLog) {
			$changeRequestSubmitClass = 'btn btn-primary';
			if ($userCanApproveChanges) {
				echo $this->Form->submit(__('Save and Approve'), array('div' => false, 'class' => 'btn btn-primary', 'name' => MerchantChange::EDIT_APPROVED));
				$changeRequestSubmitClass = 'btn btn-default';
			}
			echo $this->Form->submit(__('Save for later'), array('div' => false, 'class' => $changeRequestSubmitClass, 'name' => MerchantChange::EDIT_PENDING));
		} else {
			if (Hash::get($this->request->data, 'MerchantNote.0.general_status') === MerchantNote::STATUS_PENDING) {
				echo $this->Form->submit(__('Save changes'), array('div' => false, 'class' => 'btn btn-primary', 'name' => MerchantChange::EDIT_LOG));
			} else { ?>
				<div class="alert alert-danger">
					<strong><?php echo __('This change has been approved or rejected and can not be edited.'); ?> <strong>
				</div	>
				<p>
					<?php
					echo $this->Html->link(__('Return to approval screen'), array(
						'plugin' => false,
						'controller' => 'merchant_notes',
						'action' => 'edit',
						Hash::get($this->request->data, 'MerchantNote.0.id')
					));
					?>
				</p>
			<?php
			}
		}
		?>
	</div>
</div>
