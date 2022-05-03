<?php
if (!isset($exportLinks)) {
	$exportLinks = null;
}

if ($this->Rbac->isPermitted('app/actions/Dashboards/view/module/sysCsvExport', true)){
	// If no export links are passed, allow to call the current url as csv file
	if (empty($exportLinks)) {
		echo $this->Csv->exportLink();
	} else {
		foreach ($exportLinks as $exportLink) {
			echo $exportLink;
		}
	}
}
		