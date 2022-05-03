<?php
echo $this->Element('HelpDocs/noteInfo', ['noteText' => __("To mass-update many user compensation profiles' BET data select either UCP types OR specific UCPs to update. Then select BET(s) and enter the BET costs to be updated/created in the grid.<br>
	Only fields that are not blank will be used to perform the mass update, to remove existing non-zero amounts simply enter zeros in the fields you wish to update and the amounts will be updated with zeros <strong>(Blank fields are ignored)</strong>")]);

echo $this->Form->create('Bet', ['url' => ['action' => "mass_update"]]);
echo $this->Html->tag('div', 
	$this->Form->label('ucp_selection_a', 'Select User Compensation Profile Types to update:', ['class' => 'col-sm-12 col-md-12']), ['class' => 'form-group']) ;

echo $this->Form->input('UserCompensationProfile.default_ucps_ckbx', [
	'type' => 'checkbox',
	'div' => false,
	'wrapInput' => 'col-md-2 col-sm-3',
	'label' => ['text' => '<strong>Default Reps UCPs</strong>', 'class' => 'col-md-12 col-sm-12 list-group-item active']]);
echo $this->Form->input('UserCompensationProfile.partner_reps_ucps_ckbx', [
	'type' => 'checkbox',
	'div' => false,
	'wrapInput' => 'col-md-2 col-sm-3',
	'label' => ['text' => '<strong>PartnerReps UCPs</strong>', 'class' => 'col-md-12 col-sm-12 list-group-item list-group-item-success']]);
echo $this->Form->input('UserCompensationProfile.manager_ucps_ckbx', [
	'type' => 'checkbox',
	'div' => false,
	'wrapInput' => 'col-md-2 col-sm-3',
	'label' => ['text' => '<strong>Manager UCPs</strong>', 'class' => 'col-md-12 col-sm-12 list-group-item']]);
echo $this->Form->input('UserCompensationProfile.manager2_ucps_ckbx', [
	'type' => 'checkbox',
	'div' => false,
	'wrapInput' => 'col-md-2 col-sm-3',
	'label' => ['text' => '<strong>Manager 2 UCPs</strong>', 'class' => 'col-md-12 col-sm-12 list-group-item']]);

echo $this->Html->tag('div',
	$this->Form->label('ucp_selection_b', '-OR-<br>Select specific UCP(s) to update', ['class' => 'col-sm-12 col-md-12']), ['class' => 'form-group']) ;
echo $this->Html->tag('div', null, ['class' => 'form-group']);
echo $this->Form->input('User.ids', [
	'div' => false,
	'options' => $usersList,
	'multiple' => true,
	'wrapInput' => 'col-md-2 col-sm-4',
	'style' => 'min-height:500px',
	'label' => ['text' => 'Select User(s)', 'class' => 'col-md-1 col-sm-2']
	]);

echo $this->Form->input('UserCompensationProfile.ucp_ids', [
	'div' => false,
	'options' => [],
	'multiple' => true,
	'required' => false, //validation for this field is handled dynamically on the model
	'wrapInput' => 'col-md-2 col-sm-4',
	'style' => 'min-height:500px',
	'label' => ['text' => 'Select UCP(s)', 'class' => 'col-md-1 col-sm-2']
	]);
echo $this->Html->tag('/div');
echo $this->Form->input('BetTable.bet_ids', [
	'options' => $betTable,
	'style' => 'min-height:300px',
	'multiple' => true,
	'wrapInput' => 'col-md-2 col-sm-3',
	'label' => ['text' => 'Select Bet(s) to update', 'class' => 'col-md-1 col-sm-2']
	]);
echo $this->Element('HelpDocs/noteWarning', ['noteText' => __("<strong>IMPORTANT: Data will be added and/or updated depending on whether users already have BET costs assigned in their corresponding BET costs tables. New data will be added to any user who currently does not already have a BET cost value within any of the fields containing data bellow.</strong>")]);
?>
<table class="table">
	<?php
	echo $this->element('Bets/table_headers');
	echo $this->element('Bets/many_form_fields');
	?>
</table>
<?php
echo $this->Form->defaultButtons(null, ['controller' => 'MaintenanceDashboards', 'action' => 'main_menu']);
echo $this->Form->end();
if (empty(Hash::filter($this->validationErrors))) {
	echo $this->Html->tag('div', $this->element('AjaxElements/Admin/bg_processes_tracker'));
}
?>

<script>
if ($("#UserIds option:selected").length > 0) {
	getUcpList();
}
$("input[id^='Bet']").prop('required', false);
$("input[id$='UcpsCkbx']").click(function(event){
	$("#UserIds option:selected").prop("selected", false);
	$('#UserCompensationProfileUcpIds').empty();
});
$("#UserIds").bind("change", function(event) {
	$("input[id$='UcpsCkbx']").prop('checked', false); //uncheck all UCP type checkboxes
	getUcpList();
	return false;
});

function updateUcpSelect(compProfiles) {
	$('#UserCompensationProfileUcpIds').empty();
		 $.each(compProfiles, function (index) {
			var optgroup = $('<optgroup>');
			optgroup.prop('label', index);


			 $.each(compProfiles[index], function (v, ucpType) {
				var option = $("<option></option>");
				option.val(v);
				option.text(ucpType);
				option.prop('title', ucpType);;
				optgroup.append(option);
			 });
			 $("#UserCompensationProfileUcpIds").append(optgroup);

		 });
}

function getUcpList() {
	$.ajax({
		async: true,
		data: $("#UserIds").serialize(),
		type: "post",
		url: "\/Bets\/getListGroupedByUser",
		dataType: "json",
		success: function(data) {
			updateUcpSelect(data);
		},
		error: function(data) {
			if (data.status === 403) {
				location.reload();
			}
		}
	});
}
</script>