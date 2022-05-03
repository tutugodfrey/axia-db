<?php
$isAdd = ($this->request->params['action'] === 'add');
if ($isAdd) {
	$viewTitle = __('Add Merchant Reject line');
	$formId = '#MerchantRejectLineAddForm';
} else {
	$viewTitle = __('Edit Merchant Reject line');
	$formId = '#MerchantRejectLineEditRowForm';
}

?>
<tr>
	<td colspan="16" class="ajax-inner">
		<?php
		echo $this->Html->tag('div', $viewTitle, array('class' => 'contrTitle roundEdges'));
		echo $this->Form->create('MerchantRejectLine', array(
			'inputDefaults' => array(
				'label' => false,
				'div' => false,
				'wrapInput' => false,
				'step' => 2
			),
		));
		echo '<table>';

		$merchantId = Hash::get($merchantReject, 'MerchantReject.merchant_id');

		if (!$isAdd) {
			echo $this->Form->hidden('id');
		}
		echo $this->Form->hidden('merchant_reject_id');
		echo $this->Form->hidden('MerchantReject.id');
		echo $this->Form->hidden('MerchantReject.merchant_id');

		$trace = $this->Html->tag('label', __('Trace')) . '<div>-</div>';
		$rejectDate = $this->Html->tag('label', __('Date')) . '<div>-</div>';
		$rejectType = $this->Html->tag('label', __('Type')) . '<div>-</div>';
		$rejectCode = $this->Html->tag('label', __('Code')) . '<div>-</div>';
		$amount = $this->Html->tag('label', __('Amount')) . '<div>-</div>';
		$rejectFee = $this->Form->input('fee', array('label' => __('Reject Fee')));
		$submittedAmount = $this->Html->tag('label', __('Submitted amount')) . '<div>-</div>';
		$status = $this->Form->input('merchant_reject_status_id', array(
			'label' => __('Status'),
			'options' => $merchantRejectStatuses
		));
		$statusDate = $this->Form->input('status_date', array('type' => 'compactDate', 'label' => __('Status Date')));
		$notes = $this->Form->input('notes', array(
			'label' => __('Notes'),
			'type' => 'text'
		));
		$recurrance = $this->Form->input('MerchantReject.merchant_reject_recurrance_id', array(
			'label' => __('Recurrance'),
			'options' => $merchantRejectRecurrances
		));
		$open = $this->Html->tag('label', __('Open')) . $this->MerchantReject->openStatusInput('MerchantReject.open');
		$lossAxia = $this->Html->tag('label', __('Loss Axia')) . '<div>-</div>';
		$lossMgr1 = $this->Html->tag('label', __('Loss Mgr1')) . '<div>-</div>';
		$lossMgr2 = $this->Html->tag('label', __('Loss Mgr2')) . '<div>-</div>';
		$lossRep = $this->Html->tag('label', __('Loss Rep')) . '<div>-</div>';

		$actions = $this->Html->tag('label', __('Actions'));
		$actions .= '<div>';
		$actions .= $this->Html->link(
			__('Cancel'),
			'#',
			array(
				'data-target' => Router::url(array(
					'plugin' => false,
					'controller' => 'merchantRejectLines',
					'action' => 'cancelRow',
					Hash::get($merchantReject, 'MerchantRejectLine.id'),
				)),
				'class' => 'cancel-merchant-rejects btn',
			)
		);
		$actions .= $this->Html->link(__('Save'), $formId, array(
			'class' => 'btn btn-primary submit-merchant-rejects',
		));
		$actions .= '</div>';

		$tableCells[] = array(
			$trace,
			$rejectDate,
			$rejectType,
			$rejectCode,
			$amount,
			$rejectFee,
			$submittedAmount,
			$status,
			$statusDate,
			$notes,
			$recurrance,
			$open,
			$lossAxia,
			$lossMgr1,
			$lossMgr2,
			$lossRep,
			$actions
		);
		echo $this->Html->tableCells($tableCells);
		echo '</table>';

		echo $this->Form->end(); ?>
	</td>
</tr>
