<?php
echo $this->Form->create('MerchantCancellation', array(
	'inputDefaults' => array(
		'div' => array('class' => 'form-group'),
		'wrapInput' => 'col col-md-3',
		'class' => 'form-control',
		'label' => array('class' => 'col col-md-1')
	),
	'class' => 'form-horizontal'
));

echo $this->Form->hidden('merchant_id', array('value' => $merchant['Merchant']['id']));
echo $this->Form->dmyDate('date_submitted', array('type' => 'date'));
echo $this->Form->dmyDate('date_completed', array('type' => 'date'));
echo $this->Form->dmyDate('date_inactive', array('label' => 'Date of last Activity', 'type' => 'date'));
echo $this->Form->input('fee_charged');
echo $this->Form->input('status', array('options' => array(
	GUIbuilderComponent::STATUS_PENDING => GUIbuilderComponent::getStatusLabel(GUIbuilderComponent::STATUS_PENDING), 
	GUIbuilderComponent::STATUS_COMPLETED => GUIbuilderComponent::getStatusLabel(GUIbuilderComponent::STATUS_COMPLETED))
));
echo $this->Form->input('reason', array('label' => 'Details'));

echo $this->Form->input('merchant_cancellation_subreason_id', array('options' => $subReasons, 'empty' => '--', 'label' => 'Reason', 'id' => 'subReasonDD'));
echo $this->Form->input('merchant_cancellation_subreason', array('label' => array('text' => 'Reason Details', 'id' => 'srLabel'), 'id' => 'subreasonField'));
echo $this->Form->input('exclude_from_attrition', array(
		'wrapInput' => 'col col-md-2 col-md-offset-1',
		'label' => array('class' => 'col col-md-12', 'text' => 'Exclude from Attrition Ratio'),
		'class' => false));
echo $this->Form->end(array('label' => 'Save', 'class' => 'btn btn-success', 'div' => array('class' => 'form-group col-md-12')));
echo $this->AssetCompress->script('merchants', array('raw' => (bool)Configure::read('debug')));
?>