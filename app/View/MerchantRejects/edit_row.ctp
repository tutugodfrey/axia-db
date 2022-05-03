<?php
$isReportTable = ($rejectLinePath === 'CurrentMerchantRejectLine');
?>
<tr data-is-reject="1">
	<td colspan="<?php echo $isReportTable ? '19' : '16'; ?>" class="ajax-inner">
		<?php
		echo $this->Html->tag('div', __('Edit Merchant Reject'), array('class' => 'contrTitle roundEdges'));
		echo $this->Form->create('MerchantReject', array(
			'inputDefaults' => array(
				'label' => false,
				'div' => false,
				'wrapInput' => false,
				'step' => 2
			)
		));
		echo '<table>';
		$merchantId = Hash::get($merchantReject, 'MerchantReject.merchant_id');
		echo $this->Form->hidden('id');
		echo $this->Form->hidden("{$rejectLinePath}.id");
		echo $this->Form->hidden('merchant_id', array(
			'value' => $merchantId,
		));

		$trace = $this->Form->input('trace', array('label' => __('Trace')));
		$rejectDate = $this->Form->input('reject_date', array('type' => 'compactDate', 'label' => __('Date')));
		$rejectType = $this->Form->input('merchant_reject_type_id', array('label' => __('Type')));
		$rejectCode = $this->Form->input('code', array(
			'options' => $merchantRejectCodes,
			'label' => __('Code'),
		));
		$amount = $this->Form->input('amount', array('class' => 'form-control input-amount', 'label' => __('Amount')));
		$rejectFee = $this->Html->tag('label', __('Reject Fee')) . $this->Html->tag('div', Hash::get($merchantReject, "{$rejectLinePath}.fee") ?: '-');
		$submittedAmount = $this->Html->tag('label', __('Submitted Amount')) . $this->Html->tag('div', '-');
		$status = $this->Html->tag('label', __('Status')) . '<div>' . Hash::get($merchantRejectStatuses, Hash::get($merchantReject, "{$rejectLinePath}.merchant_reject_status_id")) . '</div>';
		$statusDate = $this->Html->tag('label', __('Status Date')) . '<div>' . $this->Time->date(Hash::get($merchantReject, "{$rejectLinePath}.status_date")) . '</div>';
		$notes = $this->Form->input("{$rejectLinePath}.notes", array('type' => 'text', 'label' => __('Notes')));
		$recurrances = $this->Form->input('merchant_reject_recurrance_id', array('label' => __('Recurrance')));
		$open = $this->Html->tag('label', __('Open')) . $this->MerchantReject->openStatusInput('MerchantReject.open');
		$lossAxia = $this->Form->input('loss_axia', array('label' => __('Loss Axia')));
		$lossMgr1 = $this->Form->input('loss_mgr1', array('label' => __('Loss Mgr1')));
		$lossMgr2 = $this->Form->input('loss_mgr2', array('label' => __('Loss Mgr2')));
		$lossRep = $this->Form->input('loss_rep', array('label' => __('Loss Rep')));

		$actions = $this->Html->tag('label', __('Actions'));
		$actions .= '<div>';
		$actions .= $this->Html->link(
			__('Cancel'),
			'#',
			array(
				'data-target' => Router::url(array(
					'plugin' => false,
					'controller' => 'merchantRejects',
					'action' => 'cancelRow',
					Hash::get($merchantReject, 'MerchantReject.id'),
					$isReportTable
				)),
				'class' => 'cancel-merchant-rejects btn',
			)
		);
		$actions .= $this->Html->link(__('Save'), '#MerchantRejectEditRowForm', array(
			'class' => 'btn btn-primary submit-merchant-rejects',
		));
		$actions .= '</div>';

		$tableCells = array();
		$tableCells[] = $trace;
		if ($isReportTable) {
			$tableCells[] = $this->Html->tag('label', __('Merchant ID')) . '<div>' . h(Hash::get($merchantReject, 'Merchant.merchant_mid')) . '</div>';
			$tableCells[] = $this->Html->tag('label', __('DBA')) . '<div>' . h(Hash::get($merchantReject, 'Merchant.merchant_dba')) . '</div>';
			$tableCells[] = $this->Html->tag('label', __('Rep')) . '<div>' . Hash::get($merchantReject, 'Merchant.User.initials') . '</div>';
		}
		$tableCells[] = $rejectDate;
		$tableCells[] = $rejectType;
		$tableCells[] = $rejectCode;
		$tableCells[] = $amount;
		$tableCells[] = $rejectFee;
		$tableCells[] = $submittedAmount;
		$tableCells[] = $status;
		$tableCells[] = $statusDate;
		$tableCells[] = $notes;
		$tableCells[] = $recurrances;
		$tableCells[] = $open;
		$tableCells[] = $lossAxia;
		$tableCells[] = $lossMgr1;
		$tableCells[] = $lossMgr2;
		$tableCells[] = $lossRep;
		$tableCells[] = $actions;
		echo $this->Html->tableCells($tableCells);
		echo '</table>';
		echo $this->Form->end(); ?>
	</td>
</tr>