<?php

App::uses('AppModel', 'Model');

/**
 * Subregion Model
 *
 */
class Subregion extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'message' => 'This field cannot be blank'
			),
			'input_has_only_valid_chars' => array(
				'rule' => array('inputHasOnlyValidChars'),
				'message' => 'Special characters (i.e "<>`()[]"... etc) are not permitted!',
				'required' => true,
				'allowEmpty' => false,
			)
		),
		'region_id' => array(
			'validOrgRegionPair' => array(
				'rule' => array('validOrgRegionPairs'),
				'message' => "Region doesn't belong to selected organization!"
			),
		),
	);

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Organization' => array(
			'className' => 'Organization',
			'foreignKey' => 'organization_id',
		),
		'Region' => array(
			'className' => 'Region',
			'foreignKey' => 'region_id',
		),
	);

/**
 * validOrgRegionPairs
 * Custom validation rule checks whether the selected Organization and Region correctly paired
 * 
 * @param array $check an associated model's Region id
 * @return array list of subregions that belong to the passed region
 */
	public function validOrgRegionPairs($check) {
		$orgId = $this->data['Subregion']['organization_id'];
		$regionId = $this->data['Subregion']['region_id'];
		return $this->Region->hasAny(['id' => $regionId, 'organization_id' => $orgId]);
	}
/**
 * getByRegion
 * Returns a list of subregions that belongTo the passed Region.id param
 * 
 * @param string $regionId an associated model's Region id
 * @return array list of subregions that belong to the passed region
 */
	public function getByRegion($regionId) {
		return $this->find('list', ['conditions' => ['region_id' => $regionId]]);
	}
}
