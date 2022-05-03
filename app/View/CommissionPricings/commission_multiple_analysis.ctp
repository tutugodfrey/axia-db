
<?php
$this->Html->addCrumb(__('Commission Multiple Analysis Report'), array(
	'plugin' => false,
	'controller' => $this->name,
	'action' => $this->action
));

$viewTitle = __('Commission Multiple Analysis Report');
?>
<input type="hidden" id="thisViewTitle" value="<?php echo $viewTitle; ?>" />

<?php
// --- Export to CSV links
$exportLinks = [];
if (!empty($commissionMultiples) && !empty($actualResiduals)) {
	$exportLinks[] = $this->Csv->exportLink($this->Csv->icon(null, [
			'title' => __('Export Report'),
			'class' => 'icon'
		]), [
		'plugin' => false,
		'controller' => 'CommissionPricings',
		'action' => 'commission_multiple_analysis',
		'?' => $this->request->query,
	]);
}

echo $this->element('Layout/Reports/gpa_filter_form', compact('exportLinks')); ?>
<script>
	$('#CommissionPricingUserId').selectize();
	//Auto select remembered option after form submission
	$('#CommissionPricingUserId')[0].selectize.setValue('<?php echo htmlspecialchars($this->request->data("CommissionPricing.user_id"));?>');
	$('div.selectize-control').attr('style', 'min-width:200px');
</script>
<div class="row">
	<div class="col-xs-12 reportTables">
		<?php
		if (!empty($commissionMultiples) && !empty($actualResiduals)) {
			echo $this->element('CommissionPricings/multi_analysis');
		}
		?>
	</div>
</div>

<?php
// --- Export to CSV links
$exportLinks = [];
if (!empty($commissionMultiples) && !empty($actualResiduals)) {
	$exportLinks[] = $this->Csv->exportLink($this->Csv->icon(), [
		'plugin' => false,
		'controller' => 'CommissionPricings',
		'action' => 'gp_analysis',
		'?' => $this->request->query,
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
