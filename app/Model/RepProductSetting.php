<?php

App::uses('AppModel', 'Model');

class RepProductSetting extends AppModel {

/**
 * Name
 *
 * @var string $name
 * @access public
 */
	public $name = 'RepProductSetting';

/**
 * Find methods
 *
 * @var array
 */
	public $findMethods = array('byCompProfile' => true);

/**
 * belongsTo association
 *
 * @var array $belongsTo 
 * @access public
 */
	public $belongsTo = array(
		'UserCompensationProfile' => array(
			'className' => 'UserCompensationProfile',
			'foreignKey' => 'user_compensation_profile_id',
		),
		'ProductsServicesType' => array(
			'className' => 'ProductsServicesType',
			'foreignKey' => 'products_services_type_id',
		)
	);

/**
 * Custom finder 
 *
 * Returns all records associated with the provided user_compensation_profile_id
 *
 * @param string $state State
 * @param array $query query arguments
 * @param array $results Results from query
 * @throws Exception
 * @return array Modified query OR results of query
 */
	protected function _findByCompProfile($state, $query, $results = array()) {
		if (empty($query['conditions']['RepProductSetting.user_compensation_profile_id']) && empty($query['conditions']['user_compensation_profile_id'])) {
			throw new Exception(__('User Compensation Profile Id in conditions is required for custom find byCompProfile.'));
		}

		if ($state === 'before') {
			$query['contain'] = array('ProductsServicesType');
			$query['order'] = array("ProductsServicesType.products_services_description ASC");
			return $query;
		}

		if ($state === 'after' && empty($results)) {
			$prodsList = $this->ProductsServicesType->find('list', array(
				'conditions' => array('ProductsServicesType.class_identifier' => Configure::read('App.productClasses.p_set.classId')),
				'order' => array("ProductsServicesType.products_services_description ASC")));
			foreach ($prodsList as $key => $pName) {
				$results[] = array(
					'RepProductSetting' => array(
							'user_compensation_profile_id' => $query['conditions']['RepProductSetting.user_compensation_profile_id'],
							'products_services_type_id' => $key,
						),
					'ProductsServicesType' => array(
							'products_services_description' => $pName
						)
				);
			}
		}
		return $results;
	}
}
