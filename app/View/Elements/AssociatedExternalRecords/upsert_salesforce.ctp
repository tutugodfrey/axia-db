
<div class="row">
	<div class="col-md-12">
		<span id="sf_upsert_request_msgs">
			<?php 
				echo '<div class="alert alert-info">' . __("Important Note! These fields enable this merchant's account data to sync with salesforce, incorrect input may cause sync to fail.", true) . "</div>";
			?>
		</span>
	</div>
</div>

<?php
echo $this->Form->create('AssociatedExternalRecord', array(
	'novalidate' => true,
	'inputDefaults' => array(
				'div' => 'form-group',
				'label' => array(
					'class' => 'col col-md-4 col col-md-4'
				),
				'wrapInput' => 'col-md-8',
				'class' => 'form-control'
			),
	'class' => 'form-horizontal'
));

echo $this->Form->hidden("AssociatedExternalRecord.id");
echo $this->Form->hidden("AssociatedExternalRecord.external_system_name");
echo $this->Form->hidden("AssociatedExternalRecord.merchant_id");

foreach ($this->request->data("ExternalRecordField") as $f_idx => $externalField) {
	echo $this->Form->hidden("ExternalRecordField.$f_idx.id");
	echo $this->Form->hidden("ExternalRecordField.$f_idx.merchant_id");
	echo $this->Form->hidden("ExternalRecordField.$f_idx.api_field_name");
	echo $this->Form->hidden("ExternalRecordField.$f_idx.field_name");
	$inputSettings = array('label' => $this->request->data("ExternalRecordField.$f_idx.field_name"));
	if ($this->request->data("ExternalRecordField.$f_idx.field_name") == SalesForce::ACCOUNT_ID || $this->request->data("ExternalRecordField.$f_idx.field_name") == SalesForce::OPPTY_ID) {
		$inputSettings['after'] = "<div class= 'small strong text-center text-info'>Hint: IDs are case sensitive must be 18 characters. i.e.: 0011R00002KJBQBQA5</div>";
		$inputSettings['id'] = ($this->request->data("ExternalRecordField.$f_idx.field_name") == SalesForce::ACCOUNT_ID)? 'AcctIdValField' : 'OpptyIdValField';
		if ($this->request->data("ExternalRecordField.$f_idx.field_name") == SalesForce::ACCOUNT_ID) {
			$inputSettings['required'] = true;
			$hasDeletableData = (!empty($this->request->data("ExternalRecordField.$f_idx.id")));
		}
	}
	echo $this->Form->input("ExternalRecordField.$f_idx.value", $inputSettings);
}
$deleteAllButton = '';
if ($this->Rbac->isPermitted('AssociatedExternalRecords/deleteAll') && $hasDeletableData) {
	$deleteAllButton = $this->Form->postLink('Delete All <span class="glyphicon glyphicon-trash"></span>', 
		array('controller' => 'AssociatedExternalRecords', 'action' => 'deleteAll', $this->request->data('AssociatedExternalRecord.id')), 
		array('escape' => false, 'block' => 'deleteAllPostLinkForm', 'class' => 'btn btn-xs btn-danger', 'confirm' => "Delete all Salesforce data from this merchant?\nThis cannot be undone, are you sure?")
	);
}

echo $this->Html->tag('div', 
	$deleteAllButton
	. $this->Form->button('Cancel', array('id' => 'cancelSfBtn', 'type' => 'button','class' => 'btn btn-xs btn-default'))
	. '<img src="/img/indicator.gif" id="saveSpinnnerOnSubmit" style="margin-left:15px;display:none" />'
	. $this->Form->button('Save', array('id' => 'saveSfBtn', 'type' => 'submit','class' => 'btn btn-xs btn-success')),
	array('class' => 'form-group text-center', 'style' => 'padding-left:15px')
);

echo $this->Form->end();

if ((!empty($deleteAllButton))) {
	echo $this->fetch('deleteAllPostLinkForm');
}
?>
<script>
$(document).ready(function(){
	$('#cancelSfBtn').on('click',  function(){
		$('#ext_data_sf').html(' ');
		$('#sf_data_read_mode').show();
	});
	$('#saveSfBtn').on('click', function(e) {
		e.preventDefault();
		if (validateSFIdVal() === false){
			return false;
		}
		$('#saveSfBtn').hide();
		$('#saveSpinnnerOnSubmit').show();
		var formData = $('#AssociatedExternalRecordUpsertForm').serialize();
		$.ajax({
			type: "POST",
			url: '/AssociatedExternalRecords/upsert',
			data: formData,
			dataType: 'html',
			success: function(data) { 
				//check if json was returned
				try {
					result = JSON.parse(data);
					if (result.success) {
						location.reload();
					}
				} catch (e) {
					$('#ext_data_sf').html(data);
					return;
				}
			},
			error: function(data) {
				/*If user session expired the server will return a Forbidden status 403
				 *Refreshing the page will redirect the user to the login page thus preventing it from inserting into the DOM*/
				$('#saveSfBtn').show();
				$('#saveSpinnnerOnSubmit').hide();
				if (data.status===403){                     
					location.reload();
				} else {
					$("#sf_upsert_request_msgs").html('<div class="alert alert-danger">Unexpected Error! Please try again</div>');
				}
			}
		});  
	});
});
function validateSFIdVal() {
	$msg = '';

	let sfIdVal = jQuery("#AcctIdValField").val();
	let pattern = /^[0-9a-zA-Z]{15}([0-9a-zA-Z]{3})?$/;
	if (jQuery("#AcctIdValField").val().length !== 18 || pattern.test(sfIdVal) === false) {
		$msg += '<li>Invalid Account ID! Must be 18 alphanumeric characters case sensitive.</li>';
	}

	if (jQuery("#OpptyIdValField").length){
		sfIdVal = jQuery("#OpptyIdValField").val();
		//Oportunity ID can be blank but must be 18
		if (jQuery("#OpptyIdValField").val().length > 0 && jQuery("#OpptyIdValField").val().length !== 18) {
			$msg += '<li>Opportunity ID must be 18 characters</li>';
		} else if (jQuery("#OpptyIdValField").val().length == 18 && pattern.test(sfIdVal) === false) {
			$msg += '<li>Invalid Opportunity ID! Must be 18 alphanumeric characters case sensitive.</li>';
		}
	}

	if ($msg === '') {
		return true;
	}
	$msg = '<div class="alert alert-danger"><ul>'+$msg+'</ul></div>';
	$("#sf_upsert_request_msgs").html($msg);
	return false;
}
</script>