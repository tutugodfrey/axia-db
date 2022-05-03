<?php

$RBACtableHeaders = array(
		__('Rep Cost'),
		__('Partner Cost')
	);

if ($this->Rbac->isPermitted('app/actions/EquipmentCosts/view/module/trueCost', true)){
		$RBACtableHeaders[] = __('True Cost');
	}

echo $this->Html->tableHeaders(array_merge(array(__('Items')), $RBACtableHeaders));
