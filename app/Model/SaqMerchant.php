<?php

App::uses('AppModel', 'Model');

/**
 * SaqMerchant Model
 *
 * @property Merchant $Merchant
 * @property AxiaCompliant $AxiaCompliant
 * @property PciBillingHistory $PciBillingHistory
 * @property PciComplianceStatusLog $PciComplianceStatusLog
 * @property Reminder $Reminder
 * @property SaqPrequalification $SaqPrequalification
 * @property SaqMerchantSurveyXref $SaqMerchantSurveyXref
 * @property PciCompliance $PciCompliance
 * @property SaqMerchantPciEmailSent $SaqMerchantPciEmailSent
 */
class SaqMerchant extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'merchant_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'required' => true
			),
		),
		'merchant_name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'required' => true
			)
		),
		'merchant_email' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'required' => false
			),
			'email' => array(
				'rule' => array('email'),
				'required' => true
			)
		)
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Merchant'
	);

/**
 * hasOne associations
 *
 * @var array
 */
	public $hasOne = array(
		'LastSaqPrequalification' => array(
			'className' => 'SaqPrequalification',
			'foreignKey' => 'saq_merchant_id',
			'order' => array(
				'LastSaqPrequalification.date_completed' => 'DESC'
			)
		),
		'LastSaqMerchantSurveyXref' => array(
			'className' => 'SaqMerchantSurveyXref',
			'foreignKey' => 'saq_merchant_id',
			'order' => array(
				'LastSaqMerchantSurveyXref.datecomplete' => 'DESC'
			)
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'SaqPrequalification' => array(
			'className' => 'SaqPrequalification',
			'foreignKey' => 'saq_merchant_id',
			'dependent' => false
		),
		'SaqMerchantSurveyXref' => array(
			'className' => 'SaqMerchantSurveyXref',
			'foreignKey' => 'saq_merchant_id',
			'dependent' => false
		),
		'PciCompliance' => array(
			'className' => 'PciCompliance',
			'foreignKey' => 'saq_merchant_id',
			'dependent' => false
		),
		'SaqMerchantPciEmailSent' => array(
			'className' => 'SaqMerchantPciEmailSent',
			'foreignKey' => 'saq_merchant_id',
			'dependent' => false
		)
	);
/**
 * beforeSave callback
 *
 * @param array $options options param required by callback
 * @return void
 */
	public function beforeSave($options = array()) {
		if (!empty($this->data[$this->alias]['merchant_name'])) {
            $this->data[$this->alias]['merchant_name'] = $this->removeAnyMarkUp($this->data[$this->alias]['merchant_name']);
        }if (!empty($this->data[$this->alias]['merchant_email'])) {
            $this->data[$this->alias]['merchant_email'] = $this->removeAnyMarkUp($this->data[$this->alias]['merchant_email']);
        }
	}
/**
 * SaqMerchant edit
 * 
 * @param array $data data to be saved
 * @throws NotFoundException
 * @throws BadRequestException
 * @return bool
 */
	public function edit($data) {
		if (empty($data) || !isset($data[$this->alias]) || !isset($data[$this->alias]['id'])) {
			return false;
		}

		if (isset($data[$this->alias]['SaqPrequalification'])) {
			unset($data[$this->alias]['SaqPrequalification']);
		}

		$this->id = $data[$this->alias]['id'];

		if (!$this->id) {
			throw new NotFoundException(__('We don\'t have the given SaqMerchant id: %s', $this->id));
		}

		$this->set(array($this->alias => $data[$this->alias]));

		if (!$this->validates()) {
			throw new BadRequestException(__('The data that you are trying to save is not valid!'));
		}

		return (bool)$this->save($data[$this->alias]);
	}
}