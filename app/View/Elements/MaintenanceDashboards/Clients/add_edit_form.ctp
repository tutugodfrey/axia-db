<?php
echo $this->Form->input('Client.id');
echo $this->Form->input('Client.client_id_to_verify', array(
	'label' => 'Client ID:',
	'value' => $this->request->data('Client.client_id_global'),
	'type' => 'number',
	'maxlength' => 8,
	'required' => true,
	'oninput' => 'validateClientId(this)',
	'after' => '<div class="small" id="clIdValidationMsgs"></div>'
));

echo $this->Form->hidden('Client.client_id_global');
echo $this->Form->hidden('Client.client_name_global');
echo $this->Html->tag('div',null, ['class' => 'form-group']);
echo $this->Html->tag('label', 'Client Name', ['class' => 'col col-xs-12 control-label']);
echo !empty($this->request->data['Client']['id'])? '<span id="clientNameDisp"class="form-control" style="background: lightgrey;font-size: 10pt;">' . $this->request->data('Client.client_name_global') . '</span>': '<span id="clientNameDisp"class="form-control" style="background: lightgrey;font-size: 10pt;">N/A</span>';
echo $this->Html->tag('/div');
?>
<script type="text/javascript">
	function validateClientId(inputObj) {
		inputObj.value=inputObj.value.slice(0,inputObj.maxLength);
		jQuery("#clIdValidationMsgs").html('');
		jQuery("#ClientClientIdGlobal").val('');
		jQuery("#ClientClientNameGlobal").val('');
		errMsg = '<span class="text-danger bg-danger" id="clIdValidationError">';
		if (jQuery("#ClientClientIdToVerify").val().length === 8) {
			jQuery('#MaintenanceDashboardFormSubmitBtn').attr('disabled', 'disabled');
			jQuery('#MaintenanceDashboardFormSubmitBtn').val('Validating...');
			jQuery("#clIdValidationMsgs").html('<div class="progress small"><div class="small progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%">Validating ID from SalesForce...</div> </div>');
			jQuery.ajax({
				type: "POST",
				url: "/Merchants/validate_client_id/"+ jQuery("#ClientClientIdToVerify").val(),
				dataType: 'html',
				success: function(result) {
					responseData = JSON.parse(result);
					if (responseData.valid === false) {
						jQuery("#clIdValidationMsgs").html(errMsg + "Invalid ID not found in SalesForce! Enter an existing AxiaMed client ID</span>");
					} else {
						msgHtml = '<span id="clIdValidated" class="bg-success text-center"><strong>This Client ID is valid!</strong></span> <span class="glyphicon glyphicon-ok text-success"></span>';

						jQuery("#clIdValidationMsgs").html(msgHtml);
						jQuery("#clientNameDisp").text(responseData.Client_Name__c);
						jQuery("#ClientClientIdGlobal").val(responseData.Client_ID__c);
						jQuery("#ClientClientNameGlobal").val(responseData.Client_Name__c);
					}
				},
				error: function(data) {
					/*If user session expired the server will return a Unathorized status 401
				 	*Refreshing the page will redirect the user to the login page*/
					if (data.status === 401) {
					 	location.reload();
					}
					if (data.status === 500) {
						jQuery("#clIdValidationMsgs").html(errMsg + "Something went wrong validating Client ID! Try again later.</span>");
					}
					
				},
				cache: false
			}).always(function(){
				jQuery('#MaintenanceDashboardFormSubmitBtn').removeAttr('disabled');
				jQuery('#MaintenanceDashboardFormSubmitBtn').val('Submit');
			});
		} else {
			jQuery("#clIdValidationMsgs").html(errMsg + "Invalid ID! Must be at least 8 digits!</span>");
		}
	}
	</script>