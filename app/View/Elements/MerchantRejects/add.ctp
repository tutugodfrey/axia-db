<div class="row">
	<div class="col-xs-12">
		<!-- Toggle -->
		<button class="btn btn-default" type="button" data-toggle="collapse" data-target="#formMerchantRejectAdd"
				aria-expanded="false" aria-controls="formMerchantRejectAdd">
			<?php echo $this->Html->image('plus-12x12.gif'); ?>
			<?php echo __('Add Reject'); ?>
		</button>
	</div>

	<!-- Form -->
	<div id="formMerchantRejectAdd" class="col-xs-12 collapse">
		<?php
		$url = array('controller' => 'merchant_rejects', 'action' => 'add');
		if (!empty($fixedMerchantId)) {
			$url[] = $fixedMerchantId;
		}
		echo $this->Form->create('MerchantReject',
			array(
				'url' => $url,
				'inputDefaults' => array(
					'div' => 'form-group col-xs-2',
					'label' => array('class' => 'control-label'),
					'wrapInput' => false,
					'class' => 'form-control',
					'step' => 2
				),
				'class' => 'well well-sm'
			)
		);
		echo $this->Form->hidden('FirstMerchantRejectLine.merchant_reject_status_id', array(
			'value' => MerchantRejectStatus::STATUS_RECEIVED
		));
		?>
		<div class="row">
			<?php
			echo $this->Form->input('trace', array("type" => "number", "step" => "1", "min" => "0"));

			if (!empty($fixedMerchantId)) {
				echo $this->Form->hidden('MerchantReject.merchant_id', array(
					'value' => $fixedMerchantId,
				));
			} else {
				echo $this->Form->input('MerchantReject.merchant_id', array('type' => 'text'));
			}
			echo $this->Form->input('reject_date', array(
				'id' => 'merchantRejectAddDate',
				'div' => array('class' => 'form-group col-xs-3 date-input')
			));
			echo $this->Form->input('merchant_reject_type_id', array('class' => 'form-control medium'));
			echo $this->Form->input('merchant_reject_recurrance_id', array('class' => 'form-control medium', 'label' => 'Reject Recurrance'));
			?>
		</div>
		<div class="row">
			<?php
				echo $this->Form->input('code', array(
					'options' => $merchantRejectCodes,
					'div' => array('class' => 'form-group col-xs-1')
				));
				echo $this->Form->input('amount', array('step' =>'any', 'div' => array('class' => 'form-group col-xs-1')));
				echo $this->Form->input('FirstMerchantRejectLine.notes', array(
					'type' => 'text',
					'div' => array('class' => 'form-group col-xs-3')
				));
				echo $this->MerchantReject->openStatusInput('open', array(
					'legend' => __('Status'),
					'div' => array('class' => 'open-status form-group col-xs-2')
				));
				echo $this->Form->input('loss_axia', array('step' =>'any', 'div' => array('class' => 'form-group col-xs-1')));
				echo $this->Form->input('loss_mgr1', array('step' =>'any', 'div' => array('class' => 'form-group col-xs-1')));
				echo $this->Form->input('loss_mgr2', array('step' =>'any', 'div' => array('class' => 'form-group col-xs-1')));
				echo $this->Form->input('loss_rep', array('step' =>'any', 'div' => array('class' => 'form-group col-xs-1')));
			?>
		</div>
		<div class="row">
			<div class="col-xs-12 text-right">
				<?php
				echo $this->Html->link(__('Cancel'), '#formMerchantRejectAdd', array(
					'class' => 'cancel-link',
					'data-toggle' => 'collapse',
					'aria-expanded' => 'false',
					'aria-controls' => 'formMerchantRejectAdd'
				));
				echo $this->Form->submit(__('Add New Reject'), array(
					'class' => 'btn btn-primary',
					'div' => false,
				));
				?>
			</div>
		</div>
		<?php echo $this->Form->end(); ?>
	</div>
</div>
