<?php /* Drop breadcrumb */
$this->Html->addCrumb('Last Activity Reports ' . $this->action, '/' . $this->name);
?>
<input type="hidden" id="thisViewTitle" value="<?php echo __('Last Activity Report'); ?> List"/>
<h1><?php echo __('Search Last Activity Report'); ?></h1>
<?php
//Load form imput control plugin
echo $this->element('Layout/selectizeAssets');
if (!empty($lastDepositReports)) {
	$icon = $this->Csv->icon(null, [
		'title' => __('Export Report'),
		'class' => 'icon'
	]);
	$exportLinks[] = $this->Csv->exportLink($icon, [
		'plugin' => false,
		'controller' => 'lastDepositReports',
		'action' => 'exportToCsv',
		'?' => $this->request->query
	]);
	echo '<span class="pull-left well-sm">';
	echo "<strong>Export Data:</strong><br>";
	echo $this->element('Layout/exportCsvUi', compact('exportLinks'));
	echo '</span>';
}

echo $this->Form->create('LastDepositReport', array(
	'inputDefaults' => array(
		'div' => 'form-group',
		'label' => array('class' => 'col col-md-12 control-label'),
		'wrapInput' => false,
		'class' => 'form-control',
		'required' => false
	),
	'type' => 'get',
	'class' => 'well well-sm form-inline'
));
?>
<?php
echo $this->Form->complexUserInput('user_id', array("class" => "single col col-xs-12", "style" => "min-width:200px"));
echo $this->Form->input('merchant', array('label' => 'Merchant'));
echo $this->Form->input('active', array('options' => array(1 => 'Active', 0 => 'Inactive', null => 'All')));
echo $this->element('Forms/OrganizationDrilldown');
echo $this->Form->end(array('label' => 'Search', 'class' => 'btn btn-default', 'div' => array('class' => 'form-group')));
?>
<div class="reportTables">
	<?php 
	echo $this->element('pagination');
	echo $this->element('LastDepositReports/content');
	 ?>
</div>
<script>
	$('#LastDepositReportUserId').selectize();
	$('#LastDepositReportUserId')[0].selectize.setValue('<?php echo htmlspecialchars($this->request->data("LastDepositReport.user_id"));?>');
	$('div.selectize-control').attr('style', 'min-width:200px');
</script>