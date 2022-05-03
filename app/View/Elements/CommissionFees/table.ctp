<?php
/**
 * The table can show a view of the values or an edit table with inputs
 * - posible values for $action: {'view', 'edit'}
 */

$action = isset($action) ? $action : 'view';
?>

<table class="table">
	<?php
	$tableHeaders = array(
		'',
		'',
		array(__('Application fees') => array('colspan' => 2)),
		array(__('Non application fees') => array('colspan' => 2)),
	);

	$tableCells = array(
		array(
			'',
			__('Do not display/calculate'),
			__('Percent of profit'),
			__('Percent of loss'),
			__('Percent of profit'),
			__('Percent of loss'),
		),
	);

	$userId = Hash::get($commissionFees, 'User.id');
	$inputIndex = 0;
	// the user rep commision fees has associated_user_id as "null"
	$userCommissionFee = array();
	foreach ($commissionFees['CommissionFee'] as $commisionFee) {
		if (empty($commisionFee['associated_user_id'])) {
			$userCommissionFee = $commisionFee;
			break;
		}
	}
	$roleLablel = !empty($commissionFees['PartnerUser']['id'])?' - Partner Rep':' - User Rep';
	$rowLabel = h(Hash::get($commissionFees, 'User.user_first_name') . ' ' . Hash::get($commissionFees, 'User.user_last_name') . $roleLablel);
	if ($action == 'edit') {
		// get the data from request if there is a validation error
		if ($this->request->is('post') || $this->request->is('put')) {
			foreach (Hash::get($this->request->data, 'CommissionFee') as $commisionFee) {
				if (!Hash::get($commisionFee, 'associated_user_id')) {
					$userCommissionFee = $commisionFee;
					break;
				}
			}
		}
		// add the hiden inputs to the first column
		$firstColumnContent = $rowLabel;
		if (!empty($userCommissionFee)) {
			$firstColumnContent .= $this->Form->input("{$inputIndex }.id", array(
				'type' => 'hidden',
				'value' => Hash::get($userCommissionFee, 'id')
			));
		}
		$firstColumnContent .= $this->Form->input("{$inputIndex }.associated_user_id", array(
			'type' => 'hidden',
			'value' => null
		));
		$firstColumnContent .= $this->Form->input("{$inputIndex }.user_compensation_profile_id", array(
			'type' => 'hidden',
			'value' => Hash::get($commissionFees, 'UserCompensationProfile.id')
		));

		$tableCells[] = array(
			$firstColumnContent,
			$this->Form->input("{$inputIndex }.is_do_not_display", array(
				'label' => false,
				'type' => 'checkbox',
				'checked' => Hash::get($userCommissionFee, 'is_do_not_display')
			)),
			$this->Form->input("{$inputIndex }.app_fee_profit", array(
				'label' => false,
				'value' => Hash::get($userCommissionFee, 'app_fee_profit')
			)),
			$this->Form->input("{$inputIndex }.app_fee_loss", array(
				'label' => false,
				'value' => Hash::get($userCommissionFee, 'app_fee_loss')
			)),
			$this->Form->input("{$inputIndex }.non_app_fee_profit", array(
				'label' => false,
				'value' => Hash::get($userCommissionFee, 'non_app_fee_profit')
			)),
			$this->Form->input("{$inputIndex }.non_app_fee_loss", array(
				'label' => false,
				'value' => Hash::get($userCommissionFee, 'non_app_fee_loss')
			)),
		);
	} else {
		$tableCells[] = array(
			$rowLabel,
			$this->Html->checkboxValue(Hash::get($userCommissionFee, 'is_do_not_display'), null, null, null),
			$this->Html->formatToPercentage(Hash::get($userCommissionFee, 'app_fee_profit')),
			$this->Html->formatToPercentage(Hash::get($userCommissionFee, 'app_fee_loss')),
			$this->Html->formatToPercentage(Hash::get($userCommissionFee, 'non_app_fee_profit')),
			$this->Html->formatToPercentage(Hash::get($userCommissionFee, 'non_app_fee_loss')),
		);
	}

	// Get the associated users and the commission fees
	$commisionAssociatedRoles = array(
		Configure::read('AssociatedUserRoles.PartnerRep.label'),
		Configure::read('AssociatedUserRoles.SalesManager.label'),
	);
	foreach ($commisionAssociatedRoles as $roleLabel) {
		$roleUsers = Hash::extract($commissionFees, "UserCompensationAssociation.{n}[role={$roleLabel}]");
		if(!empty($roleUsers)){
			foreach ($roleUsers as $associatedUser) {
				$inputIndex++;
				$associatedUserId = Hash::get($associatedUser, 'associated_user_id');
				$associatedUserCommissions = Hash::extract($commissionFees, "CommissionFee.{n}[associated_user_id={$associatedUserId}]");

				$permissionLevel = h(Hash::get($associatedUser, 'permission_level'));
				$rowLabel = h(Hash::get($associatedUser, 'UserAssociated.fullname') . " - {$roleLabel} ({$permissionLevel})");
				if ($action == 'edit') {
					// get the data from request if there is a validation error
					if ($this->request->is('post') || $this->request->is('put')) {
						$associatedUserCommissions = Hash::extract($this->request->data, "CommissionFee.{n}[associated_user_id={$associatedUserId}]");
					}

					// add the hiden inputs to the first column
					$firstColumnContent = $rowLabel;
					if (!empty($associatedUserCommissions)) {
						$firstColumnContent .= $this->Form->input("{$inputIndex }.id", array(
							'type' => 'hidden',
							'value' => Hash::get($associatedUserCommissions, '0.id')
						));
					}
					$firstColumnContent .= $this->Form->input("{$inputIndex }.associated_user_id", array(
						'type' => 'hidden',
						'value' => $associatedUserId
					));
					$firstColumnContent .= $this->Form->input("{$inputIndex }.user_compensation_profile_id", array(
						'type' => 'hidden',
						'value' => Hash::get($commissionFees, 'UserCompensationProfile.id')
					));

					$tableCells[] = array(
						$firstColumnContent,
						$this->Form->input("{$inputIndex }.is_do_not_display", array(
							'label' => false,
							'type' => 'checkbox',
							'checked' => Hash::get($associatedUserCommissions, '0.is_do_not_display')
						)),
						$this->Form->input("{$inputIndex }.app_fee_profit", array(
							'label' => false,
							'value' => Hash::get($associatedUserCommissions, '0.app_fee_profit')
						)),
						$this->Form->input("{$inputIndex }.app_fee_loss", array(
							'label' => false,
							'value' => Hash::get($associatedUserCommissions, '0.app_fee_loss')
						)),
						$this->Form->input("{$inputIndex }.non_app_fee_profit", array(
							'label' => false,
							'value' => Hash::get($associatedUserCommissions, '0.non_app_fee_profit')
						)),
						$this->Form->input("{$inputIndex }.non_app_fee_loss", array(
							'label' => false, 'value' => Hash::get($associatedUserCommissions, '0.non_app_fee_loss')
						)),
					);
				} else {
					$tableCells[] = array(
						$rowLabel,
						$this->Html->checkboxValue(Hash::get($associatedUserCommissions, '0.is_do_not_display'), null, null, null),
						$this->Html->formatToPercentage(Hash::get($associatedUserCommissions, '0.app_fee_profit')),
						$this->Html->formatToPercentage(Hash::get($associatedUserCommissions, '0.app_fee_loss')),
						$this->Html->formatToPercentage(Hash::get($associatedUserCommissions, '0.non_app_fee_profit')),
						$this->Html->formatToPercentage(Hash::get($associatedUserCommissions, '0.non_app_fee_loss')),
					);
				}
			}
		}
	}

	echo $this->Html->tableHeaders($tableHeaders);
	echo $this->Html->tableCells($tableCells);
	?>
</table>
