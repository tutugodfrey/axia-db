<?php

App::uses('AppModel', 'Model');

/**
 * Bet Model
 *
 * @property BetNetwork $BetNetwork
 * @property BetTable $BetTable
 * @property CardType $CardType
 * @property UserCompensationProfile $UserCompensationProfile
 */
class Bet extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'bet_network_id' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'message' => 'bet_network_id is required',
			)
		),
		'bet_table_id' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'message' => 'bet_table_id is required',
			)
		),
		'card_type_id' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'message' => 'card_type_id is required',
			)
		),
		'user_compensation_profile_id' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'message' => 'user_compensation_profile_id is required',
			)
		),
		'pct_cost' => array(
			'validPercentage' => array(
				'rule' => 'validPercentage',
				'allowEmpty' => true
			)
		),
		'pi_cost' => array(
			'validAmount' => array(
				'rule' => 'validAmount',
				'message' => 'pi_cost must be a valid amount number',
				'allowEmpty' => true
			)
		),
		'additional_pct' => array(
			'validPercentage' => array(
				'rule' => 'validPercentage',
				'allowEmpty' => true
			)
		),
		'sales_cost' => array(
			'validAmount' => array(
				'rule' => 'validAmount',
				'message' => 'sales_cost must be a valid amount number',
				'allowEmpty' => true
			)
		),
		'dial_sales_cost' => array(
			'validAmount' => array(
				'rule' => 'validAmount',
				'message' => 'dial_sales_cost must be a valid amount number',
				'allowEmpty' => true
			)
		),
		'non_dial_sales_cost' => array(
			'validAmount' => array(
				'rule' => 'validAmount',
				'message' => 'non_dial_sales_cost must be a valid amount number',
				'allowEmpty' => true
			)
		),
		'auth_cost' => array(
			'validAmount' => array(
				'rule' => 'validAmount',
				'message' => 'auth_cost must be a valid amount number',
				'allowEmpty' => true
			)
		),
		'dial_auth_cost' => array(
			'validAmount' => array(
				'rule' => 'validAmount',
				'message' => 'dial_auth_cost must be a valid amount number',
				'allowEmpty' => true
			)
		),
		'non_dial_auth_cost' => array(
			'validAmount' => array(
				'rule' => 'validAmount',
				'message' => 'non_dial_auth_cost must be a valid amount number',
				'allowEmpty' => true
			)
		),
		'settlement_cost' => array(
			'validAmount' => array(
				'rule' => 'validAmount',
				'message' => 'settlement_cost must be a valid amount number',
				'allowEmpty' => true
			)
		),
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'BetNetwork',
		'BetTable',
		'CardType',
		'UserCompensationProfile',
	);

/**
 * This method overrides parent method
 * Validates a percentage value (float) with a precision of 4
 *
 * @param array $check percentage value
 * @throws \RuntimeException Thrown when the regex had an error, see return values here http://php.net/manual/en/function.preg-match.php
 * @return bool
 */
	public function validPercentage($check) {
		if (empty($check)) {
			return false;
		}

		$value = reset($check);
		$errorMessage = __('Must be a valid percentage value with a maximum precision of 4');

		if ((float)$value > 100.000 ||
			(float)$value < 0 ||
			is_bool($value)
		) {
			return $errorMessage;
		}

		$result = preg_match('/^(?:100|\d{0,3})(?:\.\d{1,4})?$/', $value);
		if ($result === 1) {
			return true;
		}

		return $errorMessage;
	}

/**
 * Return the bets filtered by CardType, BetTable and User
 *
 * @param string $cardTypeId Card type id
 * @param string $betTableId Bet Table id
 * @param string $compensationId User profile compensation Id
 * @return array
 */
	public function getFilteredBets($cardTypeId, $betTableId, $compensationId) {
		$options = array(
			'conditions' => array(
				"{$this->alias}.card_type_id" => $cardTypeId,
				"{$this->alias}.bet_table_id" => $betTableId,
				"{$this->alias}.user_compensation_profile_id" => $compensationId,
			),
		);
		return $this->find('all', $options);
	}

/**
 * getUserBets
 * Return all the bets that correspond to a a User's UserCompensationProfile
 *
 * @param string $compensationId User profile compensation Id
 * @return array
 */
	public function getUserBets($compensationId) {
		return $this->find('all', array(
			'conditions' => array(
				"{$this->alias}.user_compensation_profile_id" => $compensationId,
			),
		));
	}

/**
 * updateMany
 * Updates many BETs for many users 
 * This method can be called directily but it should be backgrounded unsing CakeResque (or similar plugin) if invoked from a controller view
 * since it may take a while to update all data.
 *
 * @param array $params conditions and criteria to perform the update submitted form the mass_edit_form view from
 * @return array containing data about the outcome of the update
 */
	public function updateMany($params) {
		try {
			//Check all required keys are precent
			if (!isset($params['BetTable']) || !isset($params['UserCompensationProfile']) || !isset($params['Bet']) || !isset($params['BetTable']['bet_ids']) ||
				!isset($params['UserCompensationProfile']['default_ucps_ckbx'])|| !isset($params['UserCompensationProfile']['partner_reps_ucps_ckbx']) || !isset($params['UserCompensationProfile']['manager2_ucps_ckbx']) ||
				!isset($params['UserCompensationProfile']['manager_ucps_ckbx']) || !array_key_exists('ucp_ids', $params['UserCompensationProfile'])) {
				throw new InvalidArgumentException('Missing or invalid argument supplied for method Bet::updateMany()');
			}

			$result = [
				'job_name' => 'User BETs Cost Update',
				'result' => true,
				'start_time' => date('Y-m-d H:i:s'), // date time format yyyy-mm-dd hh::mm:ss
				'end_time' => null, // date time format yyyy-mm-dd hh::mm:ss
				'recordsAdded' => false,
				'log' => [
					'products' => [], //single dimentional array of products selected for archive
					'errors' => [], //single dimentional indexed array errors
					'optional_msgs' => [], //single dimentional indexed array of additional optional messages
				]
			];
			$dataSource = $this->getDataSource();
			$dataSource->begin();
			$ucpConditions = [];
			if (empty($params['UserCompensationProfile']['ucp_ids'])) {
				$typesSelected = ($params['UserCompensationProfile']['default_ucps_ckbx'] + $params['UserCompensationProfile']['partner_reps_ucps_ckbx'] +
				$params['UserCompensationProfile']['manager_ucps_ckbx'] + $params['UserCompensationProfile']['manager2_ucps_ckbx']);
				if ($typesSelected > 1) {
					if ($params['UserCompensationProfile']['default_ucps_ckbx'] == 1) {
						$ucpConditions['OR'][] = 'UserCompensationProfile.is_default = true';
					}
					if ($params['UserCompensationProfile']['partner_reps_ucps_ckbx'] == 1) {
						$ucpConditions['OR'][] = 'UserCompensationProfile.partner_user_id IS NOT NULL';
					}
					if ($params['UserCompensationProfile']['manager_ucps_ckbx'] == 1) {
						$ucpConditions['OR'][] = 'UserCompensationProfile.role_id = (SELECT id from roles where name = ' . "'" . User::ROLE_SM . "')";
					}
					if ($params['UserCompensationProfile']['manager2_ucps_ckbx'] == 1) {
						$ucpConditions['OR'][] = 'UserCompensationProfile.role_id = (SELECT id from roles where name = ' . "'" . User::ROLE_SM2 . "')";
					}
				} elseif ($typesSelected == 1) {
					if ($params['UserCompensationProfile']['default_ucps_ckbx'] == 1) {
						$ucpConditions[] = 'UserCompensationProfile.is_default = true';
					} elseif ($params['UserCompensationProfile']['partner_reps_ucps_ckbx'] == 1) {
						$ucpConditions[] = 'UserCompensationProfile.partner_user_id IS NOT NULL';
					} elseif ($params['UserCompensationProfile']['manager_ucps_ckbx'] == 1) {
						$ucpConditions[] = 'UserCompensationProfile.role_id = (SELECT id from roles where name = ' . "'" . User::ROLE_SM . "')";
					} elseif ($params['UserCompensationProfile']['manager2_ucps_ckbx'] == 1) {
						$ucpConditions[] = 'UserCompensationProfile.role_id = (SELECT id from roles where name = ' . "'" . User::ROLE_SM2 . "')";
					}
				}//get ids of UCPs to be updated
				$compIds = $this->UserCompensationProfile->find('list', ['conditions' => $ucpConditions, 'fields' => ['id']]);
			} else {
				$compIds = $params['UserCompensationProfile']['ucp_ids'];
			}
			//remove blank entries, we only care about entries containing data
			$params['Bet'] = Hash::filter($params['Bet']);
			//get card type ids
			$betsCardTypes = $this->BetTable->find('list', ['fields' => ['id', 'card_type_id'], 'conditions' => ['id' => $params['BetTable']['bet_ids']]]);

			foreach($compIds as $ucpId) {
				$updates = [];
				foreach($params['BetTable']['bet_ids'] as $betTableId) {
					foreach ($params['Bet'] as $dataTemplate) {
						if (count($dataTemplate) === 1 && !empty($dataTemplate['bet_network_id'])) {
							continue;
						}

						$data = [
							'bet_network_id' => $dataTemplate['bet_network_id'],
							'bet_table_id' => $betTableId,
							'card_type_id' => $betsCardTypes[$betTableId],
							'user_compensation_profile_id' => $ucpId,
						];

						//Find any existing records matching data as conditions
						$id = $this->field('id', $data);
						if ($id != false) {
							$data['id'] = $id;
						}
						$updates[] = array_merge($data, $dataTemplate);
					}
					
				}

				if (!$this->saveMany($updates)) {
					throw new Exception('Unexpected error ocurred while saving updates!');
				}
			}
			$dataSource->commit();
			$result['recordsAdded'] = true;
			$result['end_time'] = date('Y-m-d H:i:s');

		} catch (Exception $e) {
			$dataSource->rollback();
			$result['result'] = false;
			$result['errors'][] = $e->getMessage() . " line " . $e->getLine() . "\n" . $e->getTraceAsString();
			$result['end_time'] = date('Y-m-d H:i:s');
		}
		return $result;
	}
/**
 * validateMassUpdate
 * Dynamically adds new vaidation rules and evaluates them on fields and that data sumbmitted to perform a mass update
 *
 * @param array $data form data submitted via view from
 * @return boolean
 */
	public function validateMassUpdate($data) {
		$BetTableValidator = $this->BetTable->validator();
		$UcpValidator = $this->UserCompensationProfile->validator();

		$BetTableValidator['bet_ids'] = array(
			'is_bet_selected' => array(
			'rule' => array('multiple', array('min' => 1)),
				'message' => 'Please select at least one BET'
			),
		);
		if (($data['UserCompensationProfile']['default_ucps_ckbx'] + $data['UserCompensationProfile']['partner_reps_ucps_ckbx'] + $data['UserCompensationProfile']['manager_ucps_ckbx'] + $data['UserCompensationProfile']['manager2_ucps_ckbx']) == 0) {
			if (empty($data['UserCompensationProfile']['ucp_ids'])) {
				$UcpValidator['default_ucps_ckbx'] = array(
					'is_default_ucp' => array(
						'rule' => array('comparison', '==', '1'),
						'message' => 'At least one UCP type must be checked'
					)
				);
				$UcpValidator['partner_reps_ucps_ckbx'] = array(
					'is_partner_rep_ucp' => array(
						'rule' => array('comparison', '==', '1'),
						'message' => 'At least one UCP type must be checked'
					)
				);
				$UcpValidator['manager_ucps_ckbx'] = array(
					'is_sm_ucp' => array(
						'rule' => array('comparison', '==', '1'),
						'message' => 'At least one UCP type must be checked'
					)
				);
				$UcpValidator['manager2_ucps_ckbx'] = array(
					'is_sm2_ucp' => array(
						'rule' => array('comparison', '==', '1'),
						'message' => 'At least one UCP type must be checked'
					)
				);
			}
			$UcpValidator['ucp_ids'] = array(
				'is_ucp_selected' => array(
					'rule' => array('multiple', array('min' => 1)),
					'message' => 'You must select user(s) and at least one User Compensation Profile from this list'
				)
			);
		}
		$betValidationErrors = [];
		//if after removing the bet_network_id there is no Bet data add validation rule
		if (empty(Hash::filter(Hash::get(Hash::remove($data, 'Bet.{n}.bet_network_id'), 'Bet', [])))) {
			$betErrMsg = 'At least one of these fields must contain a valid value';

			$this->validator()->getField('pct_cost')->getRule('validPercentage')->allowEmpty = false;
			$this->validator()->getField('pct_cost')->getRule('validPercentage')->message = $betErrMsg;
			$this->validator()->getField('pi_cost')->getRule('validAmount')->allowEmpty = false;
			$this->validator()->getField('pi_cost')->getRule('validAmount')->message = $betErrMsg;
			$this->validator()->getField('additional_pct')->getRule('validPercentage')->allowEmpty = false;
			$this->validator()->getField('additional_pct')->getRule('validPercentage')->message = $betErrMsg;
			$this->validator()->getField('sales_cost')->getRule('validAmount')->allowEmpty = false;
			$this->validator()->getField('sales_cost')->getRule('validAmount')->message = $betErrMsg;
			$this->validator()->getField('dial_sales_cost')->getRule('validAmount')->allowEmpty = false;
			$this->validator()->getField('dial_sales_cost')->getRule('validAmount')->message = $betErrMsg;
			$this->validator()->getField('non_dial_sales_cost')->getRule('validAmount')->allowEmpty = false;
			$this->validator()->getField('non_dial_sales_cost')->getRule('validAmount')->message = $betErrMsg;
			$this->validator()->getField('auth_cost')->getRule('validAmount')->allowEmpty = false;
			$this->validator()->getField('auth_cost')->getRule('validAmount')->message = $betErrMsg;
			$this->validator()->getField('dial_auth_cost')->getRule('validAmount')->allowEmpty = false;
			$this->validator()->getField('dial_auth_cost')->getRule('validAmount')->message = $betErrMsg;
			$this->validator()->getField('non_dial_auth_cost')->getRule('validAmount')->allowEmpty = false;
			$this->validator()->getField('non_dial_auth_cost')->getRule('validAmount')->message = $betErrMsg;
			$this->validator()->getField('settlement_cost')->getRule('validAmount')->allowEmpty = false;
			$this->validator()->getField('settlement_cost')->getRule('validAmount')->message = $betErrMsg;
		}
		$this->saveAll($data['Bet'], array('validate' => 'only'));
		$betValidationErrors = $this->validationErrors;
		$this->saveAll($data, array('validate' => 'only'));
		if (!empty(Hash::filter($betValidationErrors))) {
			$this->validationErrors = array_merge($betValidationErrors, $this->validationErrors);
		}
		return empty($this->validationErrors);
	}
}
