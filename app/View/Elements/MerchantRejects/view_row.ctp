<?php
$isReportTable = false;
if (isset($rejectLinePath)) {
	$isReportTable = ($rejectLinePath === 'CurrentMerchantRejectLine');
}

$status = h(Hash::get($rejectLine, 'MerchantRejectStatus.name'));
$rejectLine = Hash::check($rejectLine, 'MerchantRejectLine') ? Hash::get($rejectLine, 'MerchantRejectLine') : $rejectLine;
$trace = null;
$rejectDate = null;
$rejectType = null;
$rejectCode = null;
$rejectAmount = null;
$rejectFee = !empty($rejectLine['fee']) ? h($this->Number->currency($rejectLine['fee'])) : '-';
$submittedAmount = '-';
$statusDate = '-';
$notes = '-';
$recurrance = null;
$open = null;
$lossAxia = null;
$lossMgr1 = null;
$lossMgr2 = null;
$lossRep = null;

// The first line merge the merchant reject info with the first reject line
if ($firstLine) {
	$trace = array(
		h(Hash::get($merchantReject, 'MerchantReject.trace')),
		array(
			'data-id' => $this->uuid('link', array(
				'plugin' => null,
				'controller' => 'MerchantRejects',
				'action' => 'edit',
				Hash::get($merchantReject, 'MerchantReject.id'),
			)),
		)
	);
	$rejectDate = $this->Time->date(Hash::get($merchantReject, 'MerchantReject.reject_date'));
	$rejectType = h(Hash::get($merchantRejectTypes, Hash::get($merchantReject, 'MerchantReject.merchant_reject_type_id')));
	$rejectCode = h(Hash::get($merchantReject, 'MerchantReject.code'));
	$rejectAmount = h($this->Number->currency(Hash::get($merchantReject, 'MerchantReject.amount')));
	$recurrance = h(Hash::get($merchantReject, 'MerchantRejectRecurrance.name'));
	$open = $this->MerchantReject->showOpenStatus(Hash::get($merchantReject, 'MerchantReject.open'));
	$lossAxia = Hash::get($merchantReject, 'MerchantReject.loss_axia');
	$lossAxia = !empty($lossAxia) ? h($this->Number->currency($lossAxia)) : '-';
	$lossMgr1 = Hash::get($merchantReject, 'MerchantReject.loss_mgr1');
	$lossMgr1 = !empty($lossMgr1) ? h($this->Number->currency($lossMgr1)) : '-';
	$lossMgr2 = Hash::get($merchantReject, 'MerchantReject.loss_mgr2');
	$lossMgr2 = !empty($lossMgr2) ? h($this->Number->currency($lossMgr2)) : '-';
	$lossRep = Hash::get($merchantReject, 'MerchantReject.loss_rep');
	$lossRep = !empty($lossRep) ? h($this->Number->currency($lossRep)) : '-';

	$editUrl = array(
		'plugin' => null,
		'controller' => 'MerchantRejects',
		'action' => 'editRow',
		Hash::get($merchantReject, 'MerchantReject.id'),
	);
	if ($isReportTable) {
		$editUrl[] = 1;
		$submittedAmount = h($this->Number->currency(Hash::get($rejectLine, 'submitted_amount')));
		$statusDate = $this->Time->date(Hash::get($rejectLine, 'status_date'));
	}
	if ($this->Rbac->isPermitted('MerchantRejects/add') && $this->Rbac->isPermitted('MerchantRejects/edit') && $this->Rbac->isPermitted('MerchantRejects/delete')) {
		$actionsContent = $this->Html->editImageLink(
			'#',
			array(
				'class' => 'edit-merchant-rejects',
				'data-target' => Router::url($editUrl)
			)
		);
		if (!$isReportTable) {
			$actionsContent .= $this->Html->link(
				$this->Html->image('/img/icon_plus.gif', array(
					'title' => __('Add Reject Line'),
					'class' => 'icon'
				)),
				'#',
				array(
					'escape' => false,
					'class' => 'add-merchant-reject-lines',
					'data-target' => Router::url(array(
						'plugin' => null,
						'controller' => 'MerchantRejectLines',
						'action' => 'add',
						Hash::get($merchantReject, 'MerchantReject.id'),
					))
				)
			);
			$actionsContent .= $this->MerchantReject->ajaxDelete(
				array(
					'controller' => 'MerchantRejects',
					'action' => 'delete',
					$merchantReject['MerchantReject']['id']
				),
				array(
					'confirm' => __('Are you sure you want to delete %s?', $merchantReject['MerchantReject']['trace'])
				)
			);
		}
	}
} else {

	$trace = array(
		null,
		array(
			'data-id' => $this->uuid('link', array(
				'plugin' => null,
				'controller' => 'MerchantRejectLines',
				'action' => 'edit',
				Hash::get($rejectLine, '.id'),
			))
		)
	);
	$submittedAmount = h($this->Number->currency(Hash::get($rejectLine, 'submitted_amount')));
	$statusDate = $this->Time->date(Hash::get($rejectLine, 'status_date'));
	$rejectLineId = Hash::get($rejectLine, 'id');
	$recurrance = h(Hash::get($merchantReject, 'MerchantRejectRecurrance.name'));
	$recurrance = !empty($recurrance) ? $recurrance : h(Hash::get($merchantReject, 'MerchantReject.MerchantRejectRecurrance.name'));
	if ($this->Rbac->isPermitted('MerchantRejects/add') && $this->Rbac->isPermitted('MerchantRejects/edit') && $this->Rbac->isPermitted('MerchantRejects/delete')) {
		$actionsContent = $this->Html->editImageLink(
			'#',
			array(
				'class' => 'edit-merchant-rejects',
				'data-target' => Router::url(array(
					'plugin' => null,
					'controller' => 'MerchantRejectLines',
					'action' => 'editRow',
					$rejectLineId,
				))
			)
		);
		$actionsContent .= $this->MerchantReject->ajaxDelete(
			array(
				'controller' => 'MerchantRejectLines',
				'action' => 'delete',
				$rejectLineId
			),
			array(
				'confirm' => __('Are you sure you want to delete %s?', $rejectLineId)
			)
		);
	}
}

$tableCells = array();
$tableCells[] = $trace;
// The order of the columns are diferent at "index" and "report"
if ($isReportTable) {
	$merchantId = Hash::get($merchantReject, 'Merchant.id');
	$tableCells[] = $this->Html->link(
		Hash::get($merchantReject, 'Merchant.merchant_mid'),
		array(
			'plugin' => false,
			'controller' => 'merchants',
			'action' => 'view',
			$merchantId
		)
	);
	$tableCells[] = $this->Html->link(
		Hash::get($merchantReject, 'Merchant.merchant_dba'),
		array(
			'plugin' => false,
			'controller' => 'merchants',
			'action' => 'view',
			$merchantId
		)
	);

	$tableCells[] = Hash::get($merchantReject, 'Merchant.User.initials');
	$tableCells[] = $rejectDate;
	$tableCells[] = $rejectType;
	$tableCells[] = $rejectCode;
	$tableCells[] = $rejectAmount;
	$tableCells[] = $status;
	$tableCells[] = $rejectFee;
	$tableCells[] = $submittedAmount;
	$tableCells[] = $statusDate;
	$tableCells[] = h(Hash::get($rejectLine, 'notes'));
} else {
	$tableCells[] = $rejectDate;
	$tableCells[] = $rejectType;
	$tableCells[] = $rejectCode;
	$tableCells[] = $rejectAmount;
	$tableCells[] = $rejectFee;
	$tableCells[] = $submittedAmount;
	$tableCells[] = $status;
	$tableCells[] = $statusDate;
	$tableCells[] = h(Hash::get($rejectLine, 'notes'));
	$tableCells[] = h($recurrance);
}

$tableCells[] = $open;
if ($this->Rbac->isPermitted('app/actions/MerchantRejects/view/module/axLoss', true)) {
	$tableCells[] = $lossAxia;
}
if ($this->Rbac->isPermitted('app/actions/MerchantRejects/view/module/smLoss', true)) {
	$tableCells[] = $lossMgr1;
}
if ($this->Rbac->isPermitted('app/actions/MerchantRejects/view/module/sm2Loss', true)) {
	$tableCells[] = $lossMgr2;
}
$tableCells[] = $lossRep;
if ($this->Rbac->isPermitted('MerchantRejects/add') && $this->Rbac->isPermitted('MerchantRejects/edit') && $this->Rbac->isPermitted('MerchantRejects/delete')) {
	$tableCells[] = $actionsContent;
}

$trOptions = array();
if ($firstLine) {
	$trOptions['data-is-reject'] = '1';
	$trOptions['update-target'] = Router::url(array(
		'plugin' => false,
		'controller' => 'merchantRejects',
		'action' => 'cancelRow',
		Hash::get($merchantReject, 'MerchantReject.id'),
	));
}
// Add the options in even AND odd, since we cant know what row it will be
echo $this->Html->tableCells(array($tableCells), $trOptions, $trOptions);
