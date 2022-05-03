<?php
echo $this->AssetCompress->css('custom-form-inputs', array(
	'raw' => (bool)Configure::read('debug')
));
$this->Html->addCrumb(__('Merchants Export Report'), array(
	'plugin' => false,
	'controller' => $this->name,
	'action' => $this->action
));
echo $this->Form->createFilterForm('MultipurposeReport', ['type' => 'post']);
echo $this->Form->input('active',
	array(
		'label' => __('Status:'),
		'options' => array(
			1 => __('Active'),
			0 => __('Inactive'),
			2 => __('All')
		),
		'empty' => '--',
		'default' => 1
	)
);
echo $this->Form->input('entity_id', ['label' => 'Company', 'empty' => 'All']);
echo $this->Form->hidden('cl_date_time');
echo $this->Form->end(array(
	'label' => __('Generate'), 
	'before' => $this->Form->toggleSwitch('mask_secured_data', array('label_text' => 'Hide Sensitive data', 'checked' => true)),
	'after' =>'<span id="warningMsg" style="position:absolute;z-index:999"></span>',
	'class' => 'btn btn-default', 'div' => array('class' => 'form-group'), 'id' => 'generateReportBtn'));


?>
<script>

$(document).ready(function(){
	$('#MultipurposeReportMaskSecuredData').click(function(){
		if ($('#MultipurposeReportMaskSecuredData').is(":checked") == false) {
			$('#generateReportBtn').hide();
			$('#warningMsg').html("<span class='alert alert-danger'><span class='glyphicon glyphicon-warning-sign' style='font-size:14pt'>&nbsp;</span><strong>You are about to export unsecured confidential data! Do you agree to store it in a secure location and to not share it through any unencrypred or unsecured channels?<strong> <button id='proceedUnsecureExport' class='btn btn-success' onClick='agreedProceed()'>I Agree</button></span>");
		} else {
			agreedProceed();
		}
	});
	$('#generateReportBtn').on('click', function(){
		const d = new Date();
		var timestampNow = d.getFullYear() + '-'+( d.getMonth()+1) + '-'+ d.getDate() + '_'+ d.getHours() + '_'+ d.getMinutes();
		$('#MultipurposeReportClDateTime').val(timestampNow);
	});
});
function agreedProceed() {
	$('#generateReportBtn').show();
	$('#warningMsg').html('');
}
</script>














