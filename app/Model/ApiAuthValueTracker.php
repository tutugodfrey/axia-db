<?php

App::uses('AppModel', 'Model');

/**
 * ApiAuthValueTracker Model
 *
 */
class ApiAuthValueTracker extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array();

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * beforeSave
 *
 * @param array $options options array
 * @return boolean
 */
	public function afterSave($created,$options = array()) {
		$storedDate = date_format(date_modify(new DateTime(date("Y-m-d")), '-1 month'), 'Y-m-d');
		$staleCount = $this->find('count', array(
			'conditions' => array(
				'nonce_value_used' => true,
				"modified < '$storedDate'"
			)
		));
		if ($staleCount > 0) {
			$this->deleteAll(array('nonce_value_used' => true, "modified < '$storedDate'"), false);
		}
		return true;
	}
}
