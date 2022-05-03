<?php
// --- Export to CSV links
$exportLinks = [];
if (!empty($commissionMultiples)) {
	$icon = $this->Csv->icon(null, [
		'title' => __('Export Commission Multiple'),
		'class' => 'icon'
	]);
	$exportLinks[] = $this->Csv->exportLink($icon, [
		'plugin' => false,
		'controller' => 'CommissionReports',
		'action' => 'exportCommissionMultiple',
		'?' => $this->request->query
	]);
}
if (!empty($commissionReports)) {
	$icon = $this->Csv->icon(null, [
		'title' => __('Export Commission Report'),
		'class' => 'icon'
	]);
	$exportLinks[] = $this->Csv->exportLink($icon, [
		'plugin' => false,
		'controller' => 'CommissionReports',
		'action' => 'exportCommissionReport',
		'?' => $this->request->query
	]);
}

$this->Html->addCrumb(__('Commission Reports'), array(
	'plugin' => false,
	'controller' => $this->name,
	'action' => $this->action
));

$viewTitle = __('Commission Report');
?>
<input type="hidden" id="thisViewTitle" value="<?php echo $viewTitle; ?>" />

<?php
	echo $this->element('Layout/Reports/filter_form', compact('exportLinks'));
?>
<script>
	$('#CommissionReportUserId').selectize();
	//Auto select remembered option after form submission
	$('#CommissionReportUserId')[0].selectize.setValue('<?php echo htmlspecialchars($this->request->data("CommissionReport.user_id"));?>');
	$('div.selectize-control').attr('style', 'min-width:200px');
</script>
<div class="row">
	<div class="col-xs-12">
		<table class="table-commission-income" cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td id="income-block" class="threeColumnGridCell">
					<?php
					echo $this->element('CommissionReports/income', ['income' => $income]);
					?>
				</td>
				<td  id="rep-adjustments-block" class="threeColumnGridCell alignBottom">
					<?php
					echo $this->element('CommissionReports/rep_adjustments');
					?>
				</td>
				<td  id="net-income-block" class="threeColumnGridCell alignBottom">
					<?php
					echo $this->element('CommissionReports/net_income', ['netIncome' => $netIncome]);
					?>
				</td>
			</tr>
		</table>
	</div>
</div>

<div class="row">
	<div id="commission-reports-block" class="col-xs-12 reportTables">
		<?php
		echo $this->element('CommissionReports/commission_reports');
		?>
	</div>

</div>

<div class="row">
	<div id="commission-multiple-block" class="col-xs-12 reportTables">
		<?php
		echo $this->element('CommissionReports/commission_multiple');
		?>
	</div>
</div>

<?php
// --- Export to CSV links
$exportLinks = [];
if (!empty($commissionMultiples)) {
	$icon = $this->Csv->icon(null, [
		'title' => __('Export Commission Multiple'),
	]);
	$exportLinks[] = $this->Csv->exportLink($icon, [
		'plugin' => false,
		'controller' => 'CommissionReports',
		'action' => 'exportCommissionMultiple',
		'?' => $this->request->query
	]);
}
if (!empty($commissionReports)) {
	$icon = $this->Csv->icon(null, [
		'title' => __('Export Commission Report'),
	]);
	$exportLinks[] = $this->Csv->exportLink($icon, [
		'plugin' => false,
		'controller' => 'CommissionReports',
		'action' => 'exportCommissionReport',
		'?' => $this->request->query
	]);
}

if (!empty($exportLinks)): ?>
	<div class="row">
		<div class="col-xs-12">
			<?php echo $this->element('Layout/reportFooter', compact('exportLinks')); ?>
		</div>
	</div>
<?php
endif;