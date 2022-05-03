<?php

App::uses('AppModel', 'Model');

/**
 * SaqPrequalification Model
 *
 * @property SaqMerchant $SaqMerchant
 */
class SaqPrequalification extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = [
		'saq_merchant_id' => [
			'uuid' => [
				'rule' => ['uuid'],
				'required' => true
			]
		],
		'result' => [
			'notBlank' => [
				'rule' => ['notBlank'],
				'required' => true
			]
		]
	];

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = [
		'SaqMerchant'
	];

/**
 * Validation types
 *
 * @var array
 */
	protected $_validationTypes = [
		'A' => 'A',
		'B' => 'B',
		'C' => 'C',
		'C-VT' => 'C-VT',
		'D' => 'D'
	];

/**
 * Retrive the validation types list
 *
 * @return array
 */
	public function getListOfValidationTypes() {
		return $this->_validationTypes;
	}

/**
 * Add or Edit one SaqPrequalification based in the saq_merchant_id and date_completed
 *
 * @param array $data data to be save
 * @throws BadRequestException
 * @return bool
 */
	public function addOrEdit($data) {
		$this->set($data);

		if (!$this->validates()) {
			throw new BadRequestException(__('The data that you are trying to save is not valid!'));
		}

		$preQualification = $this->getBySaqMerchantIdAndDateCompleted(
			$data[$this->alias]['saq_merchant_id'],
			$data[$this->alias]['date_completed']
		);

		if (empty($preQualification)) {
			$this->create();
		}

		return (bool)$this->save($data);
	}

/**
 * Get one pre qualification using the saq_merchant_id and the date_completed
 *
 * @param string|int $saqMerchantId saqMerchant id
 * @param string $dateCompleted date completed
 * @return mixed
 */
	public function getBySaqMerchantIdAndDateCompleted($saqMerchantId, $dateCompleted) {
		$dateCompleted = new DateTime($dateCompleted);

		return $this->find(
			'first',
			[
				'contain' => [],
				'conditions' => [
					'SaqPrequalification.saq_merchant_id' => $saqMerchantId,
					'SaqPrequalification.date_completed' => $dateCompleted->format('Y-m-d H:i:s'),
				]
			]
		);
	}

/**
 * Check if we should save the data
 *
 * In case that index of model is given and at least a:
 * result and date_completed we should save and validate
 *
 * @param array $data data to be save
 * @return bool
 */
	public function shouldSave($data) {
		return isset($data[$this->alias])
			&& isset($data[$this->alias]['result'])
			&& isset($data[$this->alias]['date_completed'])
			&& !empty($data[$this->alias]['result'])
			&& !empty($data[$this->alias]['date_completed']);
	}
}
