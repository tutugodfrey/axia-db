<?php
$appStsHeaders = array(
		  __('App Status Options'),
		  __('Rep Cost'));

/*Check permissions*/
if($this->Rbac->isPermitted('app/actions/AppStatuses/view/module/adminModules', true))
		$appStsHeaders[] = __('Axia Cost');

$appStsHeaders[] = __('Rep Expedite Cost');

/*Check permissions*/
if($this->Rbac->isPermitted('app/actions/AppStatuses/view/module/adminModules', true)){
	$appStsHeaders[] = __('Axia Expedite Cost (TSYS)');
	$appStsHeaders[] = __('Axia Expedite Cost (Sage)');
}

echo $this->Html->tableHeaders($appStsHeaders);
