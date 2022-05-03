<?php

$this->extend('/BaseViews/base');
echo $this->element('UserParameters/view', array('userParameters' => Hash::get($user, 'UserParameter')));
echo '<hr/>';
echo $this->element('CommissionFees/view', array('commissionFees' => Hash::get($user, 'CommissionFee')));
echo '<hr/>';
echo $this->element('ResidualVolumeTiers/residual_grid');
echo '<hr/>';
echo $this->element('AppStatuses/view');
echo '<hr/>';
echo $this->element('Bets/view_by_user');
echo $this->element('EquipmentCosts/view');
echo $this->element('ResidualTimeFactors/residual_time_grid');
echo $this->element('AttritionRatios/view', array('attritionRatios' => Hash::get($user, 'AttritionRatio')));

// Script to update profile options checkbox values from ResidualVolumeTiers and ResidualTimeFactors
$this->start('script'); ?>
<script type="text/javascript">
	var profileOptionSelectors = '#UserIsProfileOption1, #UserIsProfileOption2';

	$(profileOptionSelectors).change(function() {
		$(profileOptionSelectors).prop('disabled', true);

		// both options cant be checked at the same time
		var comparisonInputId = 'UserIsProfileOption1';
		if ($(this).prop('id') == comparisonInputId) {
			comparisonInputId = 'UserIsProfileOption2';
		}
		if ($(this).prop('checked')) {
			$('#' + comparisonInputId).prop('checked', false);
		}

		$.ajax({
			url: '<?php echo Router::url(array('controller' => 'users', 'action' => 'updateProfileOptions'), true); ?>',
			type: 'POST',
			data: {
				User: {
					id: '<?php echo Hash::get($user, 'User.id'); ?>',
					// convert boolean value to int to save in the database
					is_profile_option_1: $('#UserIsProfileOption1').prop('checked') ? 1 : 0,
					is_profile_option_2: $('#UserIsProfileOption2').prop('checked') ? 1 : 0,
				}
			}
		}).fail(function() {
			alert('<?php echo __('The profile options could not be updated'); ?>');
		}).always(function() {
			$(profileOptionSelectors).prop('disabled', false);
		});
	});
</script>
<?php
$this->end();
