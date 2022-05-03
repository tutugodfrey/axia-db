<?php
App::uses('UserParameterType', 'Model');
class RemovedOrphanedRecordsAssociatedWithUcpData extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'removed_orphaned_records_associated_with_ucp_data';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
		),
		'down' => array(
		),
	);

/**
 * After migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function after($direction) {
		if ($direction === "up") {
			$models = [
				'CommissionFee' => $this->generateModel('CommissionFee'),
				'ResidualParameter' => $this->generateModel('ResidualParameter'),
				'ResidualTimeParameter' => $this->generateModel('ResidualTimeParameter'),
				'UserParameter' => $this->generateModel('UserParameter'),
			];

			foreach ($models as $key => $model) {
				if ($key === 'UserParameter') {
					//Skip because query structure is different for this one
					continue;
				}
				$recIdsToDelete = $model->find('list', [
					'fields' => ["{$model->alias}.id"],
					'conditions' => [
						"{$model->alias}.associated_user_id IS NOT NULL",
						"AssociatedUser.associated_user_id IS NULL",
					],
					'joins' => [
						[
							'table' => 'associated_users',
							'alias' => 'AssociatedUser',
							'type' => 'left',
							'conditions' => [
								"AssociatedUser.user_compensation_profile_id = {$model->alias}.user_compensation_profile_id",
								"{$model->alias}.associated_user_id = AssociatedUser.associated_user_id"
							]
						]
					]

				]);
				$model->deleteAll(['id' => $recIdsToDelete], false, false);
				echo $model->getAffectedRows() . " {$model->alias} ophaned records deleted\n";
			}

			$recIdsToDelete = $models['UserParameter']->find('list', [
				'fields' => ["UserParameter.id"],
				'conditions' => [
					"UserParameter.user_parameter_type_id NOT IN" => [UserParameterType::DONT_DISPLAY, UserParameterType::TIER_PROD, UserParameterType::ENABLED_FOR_REP],
					"UserParameter.associated_user_id IS NOT NULL",
					"AssociatedUser.associated_user_id IS NULL",
				],
				'joins' => [
						[
							'table' => 'user_compensation_profiles',
							'alias' => 'UserCompensationProfile',
							'type' => 'INNER',
							'conditions' => [
								"UserParameter.user_compensation_profile_id = UserCompensationProfile.id",
								//We dont want to delete records that are associated with self or the user that owns the UCP
								"UserParameter.associated_user_id != UserCompensationProfile.user_id"
							]
						],
						[
						'table' => 'associated_users',
						'alias' => 'AssociatedUser',
						'type' => 'left',
						'conditions' => [
							"AssociatedUser.user_compensation_profile_id = UserParameter.user_compensation_profile_id",
							"UserParameter.associated_user_id = AssociatedUser.associated_user_id"
						]
					]
				]

			]);
			echo $models['UserParameter']->getAffectedRows() . " {$models['UserParameter']->alias} ophaned records deleted\n";
			//Update every record remove values from fields that should be empty
			$models['UserParameter']->updateAll(
				[
					'associated_user_id' => null,
					'merchant_acquirer_id' => null,
				],
				[
					"user_parameter_type_id IN" => [UserParameterType::DONT_DISPLAY, UserParameterType::TIER_PROD, UserParameterType::ENABLED_FOR_REP]
				]
			);
			echo $models['UserParameter']->getAffectedRows() . " {$models['UserParameter']->alias} records updated\n";
		}
		return true;
	}
}
