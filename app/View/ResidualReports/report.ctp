<?php
	$this->Html->addCrumb(__('Residual Report'), array(
		'plugin' => false,
		'controller' => $this->name,
		'action' => $this->action
	));

	$title = __('Residual Report');
?>
<input type="hidden" id="thisViewTitle" value="<?php echo $title; ?>" />
 <?php	
	$exportLinks = [];
	if (!empty($residualReports) || !empty($residualRollUpData)) {
		$icon = $this->Csv->icon(null, [
			'title' => __('Export Residual Report'),
			'class' => 'icon'
		]);
		$exportLinks[] = $this->Csv->exportLink($icon, 
			array_merge(
				[
					'plugin' => false,
					'controller' => 'ResidualReports',
					'action' => 'report',
					'ext' => 'csv',
					'?' => $this->request->query
				],
				Hash::extract($this->request->params, 'paging.ResidualReport.options'))
			);
	}
?>
<?php echo $this->element('ResidualReports/filter_form', compact('exportLinks')); 
if ((!empty($residualReports) || !empty($residualRollUpData)) && $resultCount < ResidualReport::MAX_RESULTS) : ?>
<div class="well well-sm">
	<span id ="colControlls" style="position:absolute;left:500px;z-index:2">
		<table>
		<tr>
			<th>Sort By:&nbsp;&nbsp;</th>
			<th><?php echo $this->Paginator->sort("Merchant.merchant_mid", "MID", ['class' => 'btn btn-xs btn-default']); ?></th>
			<th><?php echo $this->Paginator->sort("Merchant.merchant_dba", "DBA", ['class' => 'btn btn-xs btn-default']); ?>&nbsp;&nbsp;</th>
			<th>
				<label>Show/Hide Columns: &nbsp;&nbsp;</label>
				<button type="button" id="repColBtn" class="btn btn-default btn-xs" onClick="displayToggleObjByName('colGroup1')">Rep Data</button>
			 	<button type="button" id="smColBtn" class="btn btn-default btn-xs" onClick="displayToggleObjByName('colGroup3')">SM Data</button>
			 	<button type="button" id="sm2ColBtn" class="btn btn-default btn-xs" onClick="displayToggleObjByName('colGroup4')">SM2 Data</button>
			 	<button type="button" id="prtnrColBtn" class="btn btn-default btn-xs" onClick="displayToggleObjByName('colGroup5')">Partner Data</button>
			 	<button type="button" id="refColBtn" class="btn btn-default btn-xs" onClick="displayToggleObjByName('colGroup6')">Referrer Data</button>
			 	<button type="button" id="resColBtn" class="btn btn-default btn-xs" onClick="displayToggleObjByName('colGroup7')">Reseller Data</button>
			</th>
		</tr>
	</table>
	</span>
	<br/>
</div>
<?php
endif;
?>
<div class="row">
	<div id="commission-multiple-block" class="col-xs-12 reportTables">
		<?php
		echo $this->element('CommissionReports/commission_multiple');
		?>
	</div>
</div>
<div class="row">
	<div class="col-xs-12">
		<?php echo $this->element($reportElementPath); ?>
	</div>
</div>
 <?php	
	$exportLinks = [];
	if ($resultCount < ResidualReport::MAX_RESULTS && (!empty($residualReports) || !empty($residualRollUpData))) {
		$icon = $this->Csv->icon(null, [
			'title' => __('Export Residual Report'),
		]);
		$exportLinks[] = $this->Csv->exportLink($icon, array_merge(
				[
					'plugin' => false,
					'controller' => 'ResidualReports',
					'action' => 'report',
					'ext' => 'csv',
					'?' => $this->request->query
				],
				Hash::extract($this->request->params, 'paging.ResidualReport.options')));
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
var rolledUpPanelTitleOffset = parseInt($("[name='merchPanelTitle']").first().css('left'));
$(window).scroll(function(){
    $('#colControlls').css({
        'left': $(this).scrollLeft() + leftOffset
    });
    $("[name='merchPanelTitle']").css({
        'left': $(this).scrollLeft() + rolledUpPanelTitleOffset
    });
});

$(document).ready(function() {
	$("#colControlls .desc, #colControlls .asc, [name='sortableCol'].desc, [name='sortableCol'].asc").addClass('btn-success strong');
	$("#repColBtn, #smColBtn, #sm2ColBtn, #prtnrColBtn, #refColBtn, #resColBtn").on('click', function(){
		if($(this).hasClass('btn-default')) {
			$(this).removeClass('btn-default');
			$(this).addClass('btn-info');
		} else {
			$(this).removeClass('btn-info');
			$(this).addClass('btn-default');
		}
	});
	if ($("[name='colGroup1']").first().length === 0){
		$("#repColBtn").remove();	
	}
	if ($("[name='colGroup3']").first().length === 0){
		$("#smColBtn").remove();	
	}
	if ($("[name='colGroup4']").first().length === 0){
		$("#sm2ColBtn").remove();	
	}
	if ($("[name='colGroup5']").first().length === 0){
		$("#prtnrColBtn").remove();	
	}
	if ($("[name='colGroup6']").first().length === 0){
		$("#refColBtn").remove();	
	}
	if ($("[name='colGroup7']").first().length === 0){
		$("#resColBtn").remove();
	}
});
</script>