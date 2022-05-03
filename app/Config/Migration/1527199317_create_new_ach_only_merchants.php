<?php
class CreateNewAchOnlyMerchants extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'create_new_ach_only_merchants';

	public $achMerchants = [
		'Don Sellazzo' => [
			['merchant_mid' => '83639', 'merchant_dba' => 'Mercy Health St Vincent Medical Center LLC', 'ach_per_item_fee' => 0.20, 'ach_rate' => 0.25, 'ach_statement_fee' => 5.00],
			['merchant_mid' => '83640', 'merchant_dba' => 'Mercy Health St Vincent Medical Center LLC', 'ach_per_item_fee' => 0.20, 'ach_rate' => 0.25, 'ach_statement_fee' => 5.00],
			['merchant_mid' => '83641', 'merchant_dba' => 'Mercy Health St Vincent Medical Center LLC', 'ach_per_item_fee' => 0.20, 'ach_rate' => 0, 'ach_statement_fee' => 5.00],
			['merchant_mid' => '83642', 'merchant_dba' => 'Baycare Clinic LLP', 'ach_per_item_fee' => 0.20, 'ach_rate' => 0.25, 'ach_statement_fee' => 20.00],
			['merchant_mid' => '83643', 'merchant_dba' => 'Blessing Walk-In Clinic LLC', 'ach_per_item_fee' => 0.40, 'ach_rate' => 0, 'ach_statement_fee' => 5.00],
			['merchant_mid' => '83709', 'merchant_dba' => 'Mercy Health - Tiffin Hospital LLC', 'ach_per_item_fee' => 0.20, 'ach_rate' => 0, 'ach_statement_fee' => 5.00],
			['merchant_mid' => '83710', 'merchant_dba' => 'Mercy Health - Willard Hospital LLC', 'ach_per_item_fee' => 0.20, 'ach_rate' => 0, 'ach_statement_fee' => 5.00],
			['merchant_mid' => '83711', 'merchant_dba' => 'Blessing Hospital', 'ach_per_item_fee' => 0.40, 'ach_rate' => 0, 'ach_statement_fee' => 5.00],
			['merchant_mid' => '83712', 'merchant_dba' => 'Mercy Health Defiance Hospital LLC', 'ach_per_item_fee' => 0.20, 'ach_rate' => 0, 'ach_statement_fee' => 5.00],
			['merchant_mid' => '83713', 'merchant_dba' => 'Mercy Health Defiance Hospital LLC', 'ach_per_item_fee' => 0.20, 'ach_rate' => 0, 'ach_statement_fee' => 5.00],
			['merchant_mid' => '83714', 'merchant_dba' => 'Mercy Health', 'ach_per_item_fee' => 0.20, 'ach_rate' => 0, 'ach_statement_fee' => 5.00],
			['merchant_mid' => '83715', 'merchant_dba' => 'Mercy Health St Anne Hospital LLC', 'ach_per_item_fee' => 0.20, 'ach_rate' => 0, 'ach_statement_fee' => 5.00],
			['merchant_mid' => '83716', 'merchant_dba' => 'Mercy Health St Anne Hospital LLC', 'ach_per_item_fee' => 0.20, 'ach_rate' => 0, 'ach_statement_fee' => 5.00],
			['merchant_mid' => '83717', 'merchant_dba' => 'Mercy Health St Anne Hospital LLC', 'ach_per_item_fee' => 0.20, 'ach_rate' => 0, 'ach_statement_fee' => 5.00],
			['merchant_mid' => '83718', 'merchant_dba' => 'Mercy Health St Anne Hospital LLC', 'ach_per_item_fee' => 0.20, 'ach_rate' => 0, 'ach_statement_fee' => 5.00],
			['merchant_mid' => '83719', 'merchant_dba' => 'Mercy Health St Vincent Medical Center LLC', 'ach_per_item_fee' => 0.20, 'ach_rate' => 0, 'ach_statement_fee' => 5.00],
			['merchant_mid' => '83720', 'merchant_dba' => 'Mercy Health St Vincent Medical Center LLC', 'ach_per_item_fee' => 0.20, 'ach_rate' => 0, 'ach_statement_fee' => 5.00],
			['merchant_mid' => '83721', 'merchant_dba' => 'Mercy Health St Vincent Medical Center LLC', 'ach_per_item_fee' => 0.20, 'ach_rate' => 0, 'ach_statement_fee' => 5.00],
			['merchant_mid' => '83722', 'merchant_dba' => 'Mercy Health St Vincent Medical Center LLC', 'ach_per_item_fee' => 0.20, 'ach_rate' => 0, 'ach_statement_fee' => 5.00],
			['merchant_mid' => '83723', 'merchant_dba' => 'Mercy Health St Vincent Medical Center LLC', 'ach_per_item_fee' => 0.20, 'ach_rate' => 0, 'ach_statement_fee' => 5.00],
			['merchant_mid' => '83724', 'merchant_dba' => 'Mercy Health St Vincent Medical Center LLC', 'ach_per_item_fee' => 0.20, 'ach_rate' => 0, 'ach_statement_fee' => 5.00],
			['merchant_mid' => '83725', 'merchant_dba' => 'Mercy Health St Vincent Medical Center LLC', 'ach_per_item_fee' => 0.20, 'ach_rate' => 0, 'ach_statement_fee' => 5.00],
			['merchant_mid' => '83726', 'merchant_dba' => 'Mercy Health St Vincent Medical Center LLC', 'ach_per_item_fee' => 0.20, 'ach_rate' => 0, 'ach_statement_fee' => 5.00],
			['merchant_mid' => '83727', 'merchant_dba' => 'Mercy Health St Vincent Medical Center LLC', 'ach_per_item_fee' => 0.20, 'ach_rate' => 0, 'ach_statement_fee' => 5.00],
			['merchant_mid' => '83728', 'merchant_dba' => 'Mercy Health St Vincent Medical Center LLC', 'ach_per_item_fee' => 0.20, 'ach_rate' => 0, 'ach_statement_fee' => 5.00],
			['merchant_mid' => '83729', 'merchant_dba' => 'The Christ Hospital', 'ach_per_item_fee' => 0.40, 'ach_rate' => 0, 'ach_statement_fee' => 5.00],
			['merchant_mid' => '83730', 'merchant_dba' => 'The Christ Hospital', 'ach_per_item_fee' => 0.40, 'ach_rate' => 0, 'ach_statement_fee' => 5.00],
			['merchant_mid' => '83731', 'merchant_dba' => 'The Christ Hospital', 'ach_per_item_fee' => 0.40, 'ach_rate' => 0, 'ach_statement_fee' => 5.00],
			['merchant_mid' => '83732', 'merchant_dba' => 'The Christ Hospital', 'ach_per_item_fee' => 0.40, 'ach_rate' => 0, 'ach_statement_fee' => 5.00],
			['merchant_mid' => '83733', 'merchant_dba' => 'The Christ Hospital', 'ach_per_item_fee' => 0.40, 'ach_rate' => 0, 'ach_statement_fee' => 5.00],
			['merchant_mid' => '83734', 'merchant_dba' => 'The Christ Hospital', 'ach_per_item_fee' => 0.40, 'ach_rate' => 0, 'ach_statement_fee' => 5.00],
			['merchant_mid' => '83735', 'merchant_dba' => 'The Christ Hospital', 'ach_per_item_fee' => 0.40, 'ach_rate' => 0.25, 'ach_statement_fee' => 5.00],
			['merchant_mid' => '83736', 'merchant_dba' => 'The Christ Hospital', 'ach_per_item_fee' => 0.40, 'ach_rate' => 0, 'ach_statement_fee' => 5.00],
			['merchant_mid' => '83737', 'merchant_dba' => 'The Christ Hospital', 'ach_per_item_fee' => 0.40, 'ach_rate' => 0, 'ach_statement_fee' => 5.00],
			['merchant_mid' => '83738', 'merchant_dba' => 'The Christ Hospital', 'ach_per_item_fee' => 0.40, 'ach_rate' => 0, 'ach_statement_fee' => 5.00],
			['merchant_mid' => '83739', 'merchant_dba' => 'Mercy Health Physicians Cincinnati LLC', 'ach_per_item_fee' => 0.20, 'ach_rate' => 0, 'ach_statement_fee' => 5.00],
			['merchant_mid' => '83741', 'merchant_dba' => 'Mercy Health Physicians Lorain LLC', 'ach_per_item_fee' => 0.20, 'ach_rate' => 0, 'ach_statement_fee' => 5.00],
			['merchant_mid' => '83742', 'merchant_dba' => 'Mercy Health Physicians Youngstown LLC', 'ach_per_item_fee' => 0.20, 'ach_rate' => 0, 'ach_statement_fee' => 5.00],
			['merchant_mid' => '83743', 'merchant_dba' => 'Mercy Health Physicians North LLC', 'ach_per_item_fee' => 0.20, 'ach_rate' => 0, 'ach_statement_fee' => 5.00],
			['merchant_mid' => '83744', 'merchant_dba' => 'Mercy Health', 'ach_per_item_fee' => 0.20, 'ach_rate' => 0, 'ach_statement_fee' => 5.00],
			['merchant_mid' => '83745', 'merchant_dba' => 'St Ritas Professional Services LLC', 'ach_per_item_fee' => 0.20, 'ach_rate' => 0, 'ach_statement_fee' => 5.00]
		],
		'Laurie Wooley' => [
			['merchant_mid' => '83746', 'merchant_dba' => 'RCHP - Ottumwa LLC', 'ach_per_item_fee' => 0.20, 'ach_rate' => 0, 'ach_statement_fee' => 20.00],
			['merchant_mid' => '83747', 'merchant_dba' => 'Essent PRMC L.P.', 'ach_per_item_fee' => 0.20, 'ach_rate' => 0, 'ach_statement_fee' => 20.00],
			['merchant_mid' => '83748', 'merchant_dba' => 'RCHP - Florence LLC', 'ach_per_item_fee' => 0.55, 'ach_rate' => 0, 'ach_statement_fee' => 20.00]
		]
	];

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
 * @throws Exception
 */
	public function after($direction) {
		if ($direction === 'up') {
			$this->Merchant = ClassRegistry::init('Merchant');
			$this->User = ClassRegistry::init('User');
			$this->Ach = $this->generateModel('Ach');
			$partner = $this->User->find('first', ['fields' => ['id'], 'conditions' => [$this->User->getFullNameVirtualField('User') => "Experian Health"]]);
			$user = $this->User->find('first', ['fields' => ['id', 'user_first_name'], 'conditions' => [$this->User->getFullNameVirtualField('User') => 'Don Sellazzo']]);
			if (empty($user) || empty($partner)) {
				throw new Exception("User not found!");
			}
			foreach ($this->achMerchants['Don Sellazzo'] as $newMerch) {
				$updates = $this->__setNewData($newMerch, $user, $partner);
				$this->Merchant->saveAssociated($updates, ['validate' => false]);
			}
			$user = $this->User->find('first', ['fields' => ['id', 'user_first_name'], 'conditions' => [$this->User->getFullNameVirtualField('User') => 'Laurie Wooley']]);
			if (empty($user)) {
				throw new Exception("User not found!");
			}
			foreach ($this->achMerchants['Laurie Wooley'] as $newMerch) {
				$updates = $this->__setNewData($newMerch, $user, $partner);
				$this->Merchant->saveAssociated($updates, ['validate' => false]);
			}
		}
		return true;
	}

/**
 * __setNewData
 * Utility method to set data to save
 *
 * @param array $data merchant data
 * @param array $userData user data
 * @param array $partnerData partner data
 */
	private function __setNewData($data, $userData, $partnerData) {
		$merchantId = $this->Merchant->field('id', ['merchant_mid' => $data['merchant_mid']]);
		$achId = null;

		$newData = [
			'Merchant' => [
				'merchant_mid' => $data['merchant_mid'],
				'merchant_dba' => $data['merchant_dba'],
				'user_id' => $userData['User']['id'],
				'partner_id' => $partnerData['User']['id']
			],
			'Ach' => [
				'ach_mid' => $data['merchant_mid'],
				'ach_per_item_fee' => $data['ach_per_item_fee'],
				'ach_rate' => $data['ach_rate'],
				'ach_statement_fee' => $data['ach_statement_fee'],
			]
		];
		if (!empty($merchantId)) {
			$achId = $this->Ach->field('id', ['merchant_id' => $merchantId]);
			$newData['Merchant']['id'] = $merchantId;
			$newData['Ach']['merchant_id'] = $merchantId;
		}
		if (!empty($achId)) {
			$newData['Ach']['id'] = $achId;
		}
		return $newData;
	}
}
