
<?php
$this->Html->addCrumb(__('Gross Profit Report'), array(
	'plugin' => false,
	'controller' => $this->name,
	'action' => $this->action
));

$viewTitle = __('Gross Profit Estimate');
?>
<input type="hidden" id="thisViewTitle" value="<?php echo $viewTitle; ?>" />

<?php
// --- Export to CSV links
$exportLinks = [];
if (!empty($grossProfitEstimates) || !empty($reportRollUpData)) {
	$exportLinks[] = $this->Csv->exportLink($this->Csv->icon(null, [
			'title' => __('Export Report'),
			'class' => 'icon'
		]), [
		'plugin' => false,
		'controller' => 'CommissionPricings',
		'action' => 'grossProfitReport',
		'?' => $this->request->query,
	]);
}

echo $this->element('Layout/Reports/filter_form', compact('exportLinks'));
if (!empty($reportRollUpData)) :
	$sampleRecord = reset($reportRollUpData);
	$sampleRecord = reset($sampleRecord);
	$sampleRecord = Hash::get($sampleRecord, 'grossProfitEstimates.0');
 ?>

<div class="well well-sm">
	<span id ="colControlls" style="position:absolute;left:500px;z-index:2">
		<table style='max-width: fit-content;'>
		<tr>
			<th>Sort By:&nbsp;&nbsp;</th>
			<th><?php echo $this->Paginator->sort("Merchant.merchant_mid", "MID", ['class' => 'btn btn-xs btn-default']); ?></th>
			<th><?php echo $this->Paginator->sort("Merchant.merchant_dba", "DBA", ['class' => 'btn btn-xs btn-default']); ?></th>
			<th><?php echo $this->Paginator->sort("ProductsServicesType.products_services_description", "Product", ['class' => 'btn btn-xs btn-default']); ?></th>
			<?php if(array_key_exists('sub_date', $sampleRecord)): ?>
			<th><?php echo $this->Paginator->sort("TimelineSub.timeline_date_completed", "Sub. Date", ['class' => 'btn btn-xs btn-default']); ?></th>
			<?php endif; ?>
			<?php if(array_key_exists('days_to_approved', $sampleRecord)): ?>
			<th><?php echo $this->Paginator->sort("days_to_approved", "Days To Approved", ['class' => 'btn btn-xs btn-default']); ?></th>
			<?php endif; ?>
			<?php if(array_key_exists('app_date', $sampleRecord)): ?>
			<th><?php echo $this->Paginator->sort("TimelineApp.timeline_date_completed", "Approved Date", ['class' => 'btn btn-xs btn-default']); ?></th>
			<?php endif; ?>
			<?php if(array_key_exists('days_to_installed', $sampleRecord)): ?>
			<th><?php echo $this->Paginator->sort("days_to_installed", "Days To Go-Live", ['class' => 'btn btn-xs btn-default']); ?></th>
			<?php endif; ?>
			<?php if(array_key_exists('days_app_to_inst', $sampleRecord)): ?>
			<th><?php echo $this->Paginator->sort("days_app_to_inst", "Days Appr to Go-Live", ['class' => 'btn btn-xs btn-default']); ?></th>
			<?php endif; ?>
			<?php if(array_key_exists('install_date', $sampleRecord)): ?>
			<th><?php echo $this->Paginator->sort("TimelineIns.timeline_date_completed", "Go-Live Date", ['class' => 'btn btn-xs btn-default']); ?></th>
			<?php endif; ?>
		</tr>
	</table>
	</span>
	<br/>
</div>
<?php
endif;
?>
<script>
	$('#CommissionPricingUserId').selectize();
	//Auto select remembered option after form submission
	$('#CommissionPricingUserId')[0].selectize.setValue('<?php echo htmlspecialchars($this->request->data("CommissionPricing.user_id"));?>');
	$('div.selectize-control').attr('style', 'min-width:200px');
</script>
<div class="row">
	<div id="gross-profit-estimate-block" class="col-xs-12 reportTables">
		<?php
		if ($getRolledUpData == false) {
			echo $this->element('CommissionPricings/gross_profit_report');			
		} else {
			echo $this->element('CommissionPricings/gpr_rolled_up');
		}
		?>
	</div>
</div>

<?php
// --- Export to CSV links
$exportLinks = [];
if (!empty($grossProfitEstimates) || !empty($reportRollUpData)) {
	$exportLinks[] = $this->Csv->exportLink($this->Csv->icon(), [
		'plugin' => false,
		'controller' => 'CommissionPricings',
		'action' => 'grossProfitReport',
		'?' => $this->request->query,
	]);
}

if (!empty($exportLinks)): ?>
	<div class="row">
		<div class="col-xs-12">
			<?php echo $this->element('Layout/reportFooter', compact('exportLinks')); ?>
		</div>
	</div>
<script>
var leftOffset = parseInt($("#colControlls").css('left'));
	var rolledUpPanelTitleOffset = parseInt($("[name='merchPanelTitle']").first().css('left'));
	$("#colControlls .desc, #colControlls .asc").addClass('btn-success strong');
	$(window).scroll(function(){
	    $('#colControlls').css({
	        'left': $(this).scrollLeft() + leftOffset
	    });
	    $("[name='merchPanelTitle']").css({
	        'left': $(this).scrollLeft() + rolledUpPanelTitleOffset
	    });
	});
</script>
<?php
endif;
