<?php

if ($this->Rbac->isPermitted('MaintenanceDashboards/delete')) {
	$headers[] = ''; //add spacer header for the delete column
}

echo $this->Html->tag('table', 
	$this->Html->tableHeaders($headers) . 
	$this->MaintenanceDashboard->getTableContent($modelData, $modelName),
	['class' => ['table table-bordered table-hover']]);
 ?>
