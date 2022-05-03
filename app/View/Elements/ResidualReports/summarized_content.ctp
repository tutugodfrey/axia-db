<?php
$exportLinks = [];
$icon = $this->Csv->icon(null, [
	'title' => __('Export Residual Report'),
	'class' => 'icon'
]);
$exportLinks[] = $this->Csv->exportLink($icon, [
	'plugin' => false,
	'controller' => 'ResidualReports',
	'action' => 'report',
	'ext' => 'csv',
	'?' => $this->request->query
]);

echo '<span class="pull-left well-sm text-danger">';
echo "<strong>The report requested contans $resultCount records, which is not recommended to be displayed in a browser.</strong><br>";
echo "<strong>Below are the summarized totals of the records found.<br/>";
echo "<span class='text-info'>The full report can be downloaded here ";
echo $this->element('Layout/exportCsvUi', compact('exportLinks'));
echo ' (may take a while).</strong></span></span>';

$headers = ['Category', 'Number of Items', 'Volume', 'Profit', 'Reps Profit Amount', 'Referers Profit Amount', 'Resellers Profit Amount', '1st Managers Profit Amount', '2nd Managers Profit Amount'];
$cells = [
	[	//row 
		"<strong class='text-info'>Totals</strong>", 
		Hash::get($totals, 'r_items'), 
		$this->Number->currency(Hash::get($totals, 'r_volume'), 'USD3dec'), 
		$this->Number->currency(Hash::get($totals, 'total_profit'), 'USD3dec'), 
		$this->Number->currency(Hash::get($totals, 'r_profit_amount'), 'USD3dec'), 
		$this->Number->currency(Hash::get($totals, 'refer_profit_amount'), 'USD3dec'), 
		$this->Number->currency(Hash::get($totals, 'res_profit_amount'), 'USD3dec'), 
		$this->Number->currency(Hash::get($totals, 'manager_profit_amount'), 'USD3dec'), 
		$this->Number->currency(Hash::get($totals, 'manager_profit_amount_secondary'), 'USD3dec')
	],
	["<strong class='text-info'>Non referral partner total</strong>", '', $this->Number->currency(Hash::get($totalsNonReferrals, 'r_volume'), 'USD3dec'),'','','','','','',],
	["<strong class='text-info'>Referral partner total</strong>", '', $this->Number->currency(Hash::get($totalsReferrals, 'r_volume'), 'USD3dec'),'','','','','','',]
];
echo $this->Html->tag('table',
	$this->Html->tableHeaders($headers, ['class' => 'nowrap'], ['class' => 'nowrap']) .
	$this->Html->tableCells($cells, ['class' => 'nowrap'], ['class' => 'nowrap']),
	['class' => ['table table-stiped table-hover']]
);
