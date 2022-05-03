<?php
	$this->Html->addCrumb(__('Merchant Products Report'), [
		'plugin' => false,
		'controller' => $this->name,
		'action' => $this->action
	]);

	$title = __('Merchant Products Report');
?>
<input type="hidden" id="thisViewTitle" value="<?php echo $title; ?>" />
 <?php	
	$exportLinks = [];
	if (!empty($reportData)) {
		$icon = $this->Csv->icon(null, [
			'title' => __('Export Report'),
			'class' => 'icon'
		]);
		$exportLinks[] = $this->Html->link($icon, "#", [
			'onClick' => "exportTableToCSV('merchantProductReport.csv', 'mprTable')",
			'escape' => false
		]);
	}
echo $this->element('MerchantProductsReport/filter_form', compact('exportLinks')); 
if (!empty($reportData)) {

$headers = [
	$this->Paginator->sort('Merchant.merchant_mid','MID'),
	$this->Paginator->sort('Merchant.merchant_dba','DBA'),
	'Client ID',
	'Rep',
	'Date Approved',
	'Visa Products', 'Mastercard Products', 'Discover Products', 'American Express', 'Debit Products', 'EBT Products', 
		'Other Products', 'Visa BET', 'MasterCard BET', 'Discover BET', 'American Express BET', 'Debit BET', 'EBT BET', 'Rep/manager not paid<br/>Discover Discount/Settled Items',
		'Company'
	];
foreach ($reportData as $data) {
	$rows[] = [
		h(Hash::get($data, 'Merchant.merchant_mid')),
		h(Hash::get($data, 'Merchant.merchant_dba')),
		h(Hash::get($data, 'Client.client_id_global')),
		h(Hash::get($data, 'user.full_name')),
		$this->Time->format('m/d/Y h:i A', Hash::get($data, 'UwStatusMerchantXref.datetime')) ,
		[str_replace(', ', '<br>', h(Hash::get($data, 'VisaProduct.names'))), ['class' => 'nowrap']],
		[str_replace(', ', '<br>', h(Hash::get($data, 'MasterCardProduct.names'))), ['class' => 'nowrap']],
		[str_replace(', ', '<br>', h(Hash::get($data, 'DiscoverProduct.names'))), ['class' => 'nowrap']],
		[str_replace(', ', '<br>', h(Hash::get($data, 'AmexProduct.names'))), ['class' => 'nowrap']],
		[str_replace(', ', '<br>', h(Hash::get($data, 'DebitProduct.names'))), ['class' => 'nowrap']],
		[str_replace(', ', '<br>', h(Hash::get($data, 'EbtProduct.names'))), ['class' => 'nowrap']],
		[str_replace(', ', '<br>', h(Hash::get($data, 'OtherProduct.names'))), ['class' => 'nowrap']],
		h(Hash::get($data, 'VisaBet.name')),
		h(Hash::get($data, 'MasterCardBet.name')),
		h(Hash::get($data, 'DiscoverBet.name')),
		h(Hash::get($data, 'AmexBet.name')),
		h(Hash::get($data, 'DebitBet.name')),
		h(Hash::get($data, 'EbtBet.name')),
		(Hash::get($data, 'MerchantPricing.ds_user_not_paid') == true)? 'Not paid': 'Paid',
		h(Hash::get($data, 'Entity.entity_acronym')),
	];
}


echo $this->element('pagination');
echo $this->Html->tag('table',
	$this->Html->tableHeaders($headers) .
	$this->Html->tableCells($rows),
	['id' =>'mprTable', 'class' => 'table table-condensed table-striped table-hover']);
}
?>


<?php
echo $this->AssetCompress->script('reports', [
		'raw' => (bool)Configure::read('debug')
	]);