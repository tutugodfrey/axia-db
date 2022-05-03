<?php /* Drop breadcrumb */
$this->Html->addCrumb('Data Collection ' . $this->action, '/' . $this->name . '/' . $this->action);
?>
<input type="hidden" id="thisViewTitle" value="<?php echo __('Data Collection Report'); ?>"/>
<?php

$exportLinks = [];
if (!empty($reportData)) {
	$icon = $this->Csv->icon(null, [
		'title' => __('Export Report'),
		'class' => 'icon'
	]);
	$exportLinks[] = $this->Csv->exportLink($icon, 
		[
			'plugin' => false,
			'controller' => $this->name,
			'action' => $this->action,
			'ext' => 'csv',
			'?' => $this->request->query
		]
	);
}
echo $this->Element('Layout/Reports/data_collection_filters', compact('exportLinks')); 
?>
<div id ="reportContent">
<?php 
	if ($rollUpLayout) {
		echo $this->Element('ImportedDataCollections/grouped_format_content');
	} else {
		echo $this->Element('ImportedDataCollections/content');
	}
?>
</div>