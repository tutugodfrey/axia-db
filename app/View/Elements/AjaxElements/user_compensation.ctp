<?php

$this->extend('/BaseViews/base');
echo '<hr/>';
echo $this->element('Users/associated_view');
echo '<hr/>';
if(!$isManagerUcp && $this->Rbac->isPermitted('app/actions/UserParameters/view/module/readModule', true)){
	echo $this->element('UserParameters/view');
	echo '<hr/>';
}
if(!$isManagerUcp && $this->Rbac->isPermitted('app/actions/CommissionFees/view/module/readModule', true)){
	echo $this->element('CommissionFees/view');
	echo '<hr/>';
}
if(!$isManagerUcp && $this->Rbac->isPermitted('app/actions/ResidualVolumeTiers/view/module/readModule', true)){
	echo $this->element('ResidualVolumeTiers/residual_grid');
	echo '<hr/>';
}
if(!$isManagerUcp && $this->Rbac->isPermitted('app/actions/ResidualTimeFactors/view/module/readModule', true)){
	echo $this->element('ResidualTimeFactors/residual_time_grid');
	echo '<hr/>';
}
if(!$isManagerUcp && $this->Rbac->isPermitted('app/actions/AppStatuses/view/module/readModule', true)){
	echo $this->element('AppStatuses/view');
	echo '<hr/>';
}
if($this->Rbac->isPermitted('app/actions/Bets/view/module/readModule', true)){
	echo $this->element('Bets/view_by_user');
	echo '<hr/>';
}
if($this->Rbac->isPermitted('app/actions/EquipmentCosts/view/module/readModule', true)){
	echo $this->element('EquipmentCosts/view');
}
if(!$isManagerUcp && $this->Rbac->isPermitted('app/actions/AttritionRatios/view/module/readModule', true)){
	echo $this->element('AttritionRatios/view', array('attritionRatios' => Hash::get($user, 'AttritionRatio')));
	echo '<hr/>';
}
if($this->Rbac->isPermitted('app/actions/UserCompensationProfiles/view/module/RepCostStructureModule', true)){
	echo $this->element('RepCostStructures/view');
}
if($this->Rbac->isPermitted('app/actions/UserCompensationProfiles/view/module/ProductSettingModule', true)){
	echo $this->element('RepProductSettings/view');
}
// Script to update profile options checkbox values from ResidualVolumeTiers and ResidualTimeFactors
?>
<script type="text/javascript">
function ucpOptionControl () {
	var profileOptionSelectors = '#UserCompensationProfileIsProfileOption1, #UserCompensationProfileIsProfileOption2';
	//Set default value if no options are checked
	if ($('#UserCompensationProfileIsProfileOption1').prop('checked') == false && $('#UserCompensationProfileIsProfileOption2').prop('checked') == false) {
		$('#UserCompensationProfileIsProfileOption1').prop('checked', true);
		//Save
		saveOption();
	}
	//Disable checked option to prevent user from unchecking both
	if ($('#UserCompensationProfileIsProfileOption1').prop('checked')) {
		$('#UserCompensationProfileIsProfileOption1').prop('disabled', true);
	} else if($('#UserCompensationProfileIsProfileOption2').prop('checked')) {
		$('#UserCompensationProfileIsProfileOption2').prop('disabled', true)
	}
	$(profileOptionSelectors).change(function() {

		// both options cant be checked at the same time
		var comparisonInputId = 'UserCompensationProfileIsProfileOption1';
		if ($(this).prop('id') == comparisonInputId) {
			comparisonInputId = 'UserCompensationProfileIsProfileOption2';
		}
		if ($(this).prop('checked')) {
			//Disable currrent option
			$(this).prop('disabled', true);
			//Uncheck the other one and enable it.
			$('#' + comparisonInputId).prop('checked', false);
			$('#' + comparisonInputId).prop('disabled', false);
		} 

		saveOption();
	});
}
function saveOption() {
	$.ajax({
		url: '<?php echo Router::url(array('controller' => 'UserCompensationProfiles', 'action' => 'updateProfileOptions'), true); ?>',
		type: 'POST',
		data: {
			UserCompensationProfile: {
				id: '<?php echo Hash::get($user, 'UserCompensationProfile.id'); ?>',
				// convert boolean value to int to save in the database
				is_profile_option_1: $('#UserCompensationProfileIsProfileOption1').prop('checked') ? 1 : 0,
				is_profile_option_2: $('#UserCompensationProfileIsProfileOption2').prop('checked') ? 1 : 0,
			}
		}
	}).fail(function() {
		alert('<?php echo __('The profile options could not be updated'); ?>');
	});
}
$(document).ready(function() {
	var IS_MANAGER_UCP = <?php echo (int)$isManagerUcp; ?>;

	$.when(
		//Prioritize ajax calls, first perform all the faster small-data ajax requests
		renderContentAJAX('Users', 'ajaxAssociatedView', <?php echo "'$compId/$partnerUserId'"; ?>, 'assocViewMainContainer'),
		renderContentAJAX('RepMonthlyCosts', 'ajaxView', <?php echo "'{$user['User']['id']}/$compId/$partnerUserId'"; ?>, 'RepMonthlyCost'),
		renderContentAJAX('GatewayCostStructures', 'ajaxView', <?php echo "'{$user['User']['id']}/$compId/$partnerUserId'"; ?>, 'GwCostStructuresContainer'),
		renderContentAJAX('AchRepCosts', 'ajaxView', <?php echo "'{$user['User']['id']}/$compId/$partnerUserId'"; ?>, 'AchRepCost'),
		renderContentAJAX('PaymentFusionRepCosts', 'ajaxView', <?php echo "'{$user['User']['id']}/$compId/$partnerUserId'"; ?>, 'PaymentFusionRepCost'),
		renderContentAJAX('WebAchRepCosts', 'ajaxView', <?php echo "'{$user['User']['id']}/$compId/$partnerUserId'"; ?>, 'WebAchRepCost'),
		renderContentAJAX('AddlAmexRepCosts', 'ajaxView', <?php echo "'{$user['User']['id']}/$compId/$partnerUserId'"; ?>, 'AddlAmexRepCost'),
		renderContentAJAX('RepProductSettings', 'ajaxView', <?php echo "'{$user['User']['id']}/$compId/$partnerUserId'"; ?>, 'RepProductSettings'),
		renderContentAJAX('EquipmentCosts', 'ajaxView', <?php echo "'$compId/$partnerUserId'"; ?>, 'EqCostsMainContainer')
		).done(function() {
		//when done make the big-data requests which take much longer iff this is not a manager UCP
		if (IS_MANAGER_UCP == 0) {
			renderContentAJAX('CommissionFees', 'ajaxView', <?php echo "'$compId/$partnerUserId'"; ?>, 'CommissionFeesMainContent'),
			renderContentAJAX('AppStatuses', 'ajaxView', <?php echo "'$compId/$partnerUserId'"; ?>, 'appStatusesMainContainer'),
			renderContentAJAX('AttritionRatios', 'ajaxView', <?php echo "'{$user['User']['id']}/$compId/$partnerUserId'"; ?>, 'AttritionRatiosMainContainer')
			renderContentAJAX('UserParameters', 'ajaxView', <?php echo "'{$user['User']['id']}/$compId/$partnerUserId'"; ?>, 'UserParamsViewMainContainer');
			renderContentAJAX('ResidualVolumeTiers', 'ajaxView', <?php echo "'{$user['User']['id']}/$compId/$partnerUserId'"; ?>, 'residualVolumeTiersContainer');
			renderContentAJAX('ResidualTimeFactors', 'ajaxView', <?php echo "'{$user['User']['id']}/$compId/$partnerUserId'"; ?>, 'residualTimeFactorsContainer');
			//Add ajax Request complete listener to attach event handlers to content that takes longer to load
			$(document).ajaxComplete(function(event, xhr, settings) {
				if (settings.url.indexOf("ResidualVolumeTiers/ajaxView") > 0 || settings.url.indexOf("ResidualTimeFactors/ajaxView") > 0) {
					//remove all previosly attached event handlers to prevent event handler duplication
					$('#UserCompensationProfileIsProfileOption1, #UserCompensationProfileIsProfileOption2').off();
					ucpOptionControl();
				}
			});
		}
	});
});
</script>
