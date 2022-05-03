<?php
	$this->Html->addCrumb(__('Profitability Report'), array(
		'plugin' => false,
		'controller' => $this->name,
		'action' => $this->action
	));

	$title = __('Profitability Report');
?>
<input type="hidden" id="thisViewTitle" value="<?php echo $title; ?>" />
 <?php	
	$exportLinks = [];
	if (!empty($profitReports)) {
		$icon = $this->Csv->icon(null, [
			'title' => __('Export Profitability Report'),
			'class' => 'icon'
		]);
		$exportLinks[] = $this->Csv->exportLink($icon, [
			'plugin' => false,
			'controller' => $this->name,
			'action' => 'report',
			'ext' => 'csv',
			'?' => $this->request->query
		]);
	}
?>
<?php echo $this->element('ProfitabilityReport/filter_form', compact('exportLinks')); 
if (!empty($profitReports)) : ?>
<div class="well well-sm">
	<span id ="colControlls" style="position:absolute;left:500px;z-index:2">
		<label>Show/Hide Columns: &nbsp;&nbsp;</label>
		<button type="button" id="gpTotalsColBtn" class="btn btn-default btn-xs" onClick="displayToggleObjByName('colPrGroup1')"><span class='glyphicon glyphicon-th'></span> Gross Profit Columns</button>
	</span><br/>
</div>
<?php
endif;
?>
<div class="row">
	<div class="col-xs-12 reportTables">
		<?php echo $this->element('ProfitabilityReport/content'); ?>
	</div>
</div>
 <?php	
	$exportLinks = [];
	if (!empty($profitReports)) {
		$icon = $this->Csv->icon(null, [
			'title' => __('Export Profitability Report'),
		]);
		$exportLinks[] = $this->Csv->exportLink($icon, [
			'plugin' => false,
			'controller' => $this->name,
			'action' => 'report',
			'ext' => 'csv',
			'?' => $this->request->query
		]);
	}
?>
<?php
if (!empty($exportLinks)): ?>
	<div class="row">
		<div class="col-xs-12">
			<?php echo $this->element('Layout/reportFooter', compact('exportLinks')); ?>
		</div>
	</div>
<?php
endif;
?>
<script>

var leftOffset = parseInt($("#colControlls").css('left'));
$(window).scroll(function(){
    $('#colControlls').css({
        'left': $(this).scrollLeft() + leftOffset
    });
});

$(document).ready(function() {
	$("#gpTotalsColBtn").on('click', function(){
		if($(this).hasClass('btn-success')) {
			$(this).removeClass('btn-success');
			$(this).addClass('btn-default');
		} else {
			$(this).removeClass('btn-default');
			$(this).addClass('btn-success');
		}
	});
	if ($("[name='colPrGroup1']").first().length === 0){
		$("#gpTotalsColBtn").remove();	
	}
});
</script>