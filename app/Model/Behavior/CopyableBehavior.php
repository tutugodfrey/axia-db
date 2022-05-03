<?php

App::uses('ModelBehavior', 'Model');

/**
 * This behavior will copy one or many records based on conditions.
 *
 * You have to implement a callback in your model to make this work. Implement
 * a method called Model::copyTargetExists($targetData) that will check if the
 * target already exists. The $targetData argument is the array of conditions
 * passed to the behaviors copyRecord() method as 2nd argument. Return false or
 * the array data of the existing record if found.
 *
 * @todo Needs to be finished
 */
class CopyableBehavior extends ModelBehavior {

/**
 * settings indexed by model name.
 *
 * @var array
 */
	public $settings = array();

/**
 * Errors
 *
 * @var array
 */
	public $errors = array();

/**
 *
 */
	public $savedRecords = array();

/**
 * Default settings
 *
 * @var array
 */
	protected $_defaults = array(
		'overwrite' => false,
		'create' => true
	);

/**
 * Default values to merge into the copied records
 *
 * @var array
 */
	public $defaultValues = array();

/**
 * Configuration of model
 *
 * @param Model $Model Model attached to the behavior
 * @param array $config Configuration options
 * @throws RuntimeException
 * @return void
 */
	public function setup(Model $Model, $config = array()) {
		$this->settings[$Model->alias] = array_merge($this->_defaults, $config);

		if ($this->settings[$Model->alias]['create'] === true && $this->settings[$Model->alias]['overwrite'] === true) {
			throw new RuntimeException(__('Setting "overwrite" and "create" can not be true at the same time!'));
		}
	}

/**
 * Copy one or several records
 *
 * @param Model $Model Model attached to the behavior
 * @param int|string|array $source Find options of the source data
 * @param array $targetValues Values to overwrite at the target data
 * @param bool $many Allow to copy several records
 * @return bool false if one or more records failed to save
 */
	public function copyRecord(Model $Model, $source, $targetValues, $many = false) {
		$this->errors = array();
		$this->savedRecords = array();

		$findMethod = 'first';
		if ($many === true) {
			$findMethod = 'all';
		}

		$sourceData = $this->findSource($Model, $source, $findMethod);
		if ($many === false) {
			$this->writeCopy($Model, $targetValues, $sourceData);
		} else {
			foreach ($sourceData as $result) {
				$this->writeCopy($Model, $targetValues, $result);
			}
		}

		return (empty($this->errors));
	}

/**
 * Find the source records to copy
 *
 * @param Model $Model Model attached to the behavior
 * @param int|string|array $source Find options of the source data
 * @param string $findMethod Finder type
 * @throws RuntimeException
 * @throws NotFoundException
 * @return array
 */
	public function findSource(Model $Model, $source, $findMethod = 'first') {
		if (!in_array($findMethod, array('first', 'all'))) {
			throw new RuntimeException(__('Invalid find type, only "all" and "first" are allowed!'));
		}

		if (is_string($source) || is_int($source)) {
			$source = array(
				'contain' => array(),
				'conditions' => array(
					$Model->alias . '.' . $Model->primaryKey => $source
				)
			);
		}

		$result = $Model->find($findMethod, $source);

		if (empty($result)) {
			throw new NotFoundException(__('Could not find source record(s)!'));
		}

		return $result;
	}

/**
 * Merge the copy default values
 *
 * @param Model $Model Model attached to the behavior
 * @param array $data Data to copy
 * @param array $defaults Default values
 * @return array
 */
	public function mergeCopyDefaults(Model $Model, $data, $defaults = array()) {
		if (empty($defaults)) {
			$defaults = $this->defaultValues;
		}
		return Set::merge($data, $defaults);
	}

/**
 * Prepare the data to copy and save it in the database
 *
 * @param Model $Model Model attached to the behavior
 * @param array $targetValues Values to overwrite at the target data
 * @param array $sourceData Source data
 * @return bool
 */
	public function writeCopy(Model $Model, $targetValues, $sourceData) {
		unset($sourceData[$Model->alias][$Model->primaryKey]);

		$sourceData = $this->mergeCopyDefaults($Model, $sourceData);

		$targetData = array();
		$result = $this->copyTargetExists($Model, $targetValues, $sourceData);
		if ($result) {
			$targetData = $result;
		}

		if ($this->settings[$Model->alias]['create'] === true && $this->settings[$Model->alias]['overwrite'] === false) {
			$Model->create();
		}

		if ($this->settings[$Model->alias]['overwrite'] === true && $this->settings[$Model->alias]['create'] === false) {
			$sourceData = Set::merge($targetData, $sourceData);
		}

		if (!empty($targetData) && $this->settings[$Model->alias]['overwrite'] === false) {
			$this->errors[] = array(
				'data' => $sourceData,
				'targetData' => $targetData,
				'validation' => __('Target already exists')
			);
			return false;
		}

		return $this->saveCopy($Model, Set::merge($sourceData, $targetValues));
	}

/**
 * Save the data in the database
 *
 * @param Model $Model Model attached to the behavior
 * @param array $sourceData Data to save
 * @return bool
 */
	public function saveCopy(Model $Model, $sourceData) {
		$result = $Model->save($sourceData);
		if ($result === false) {
			$this->errors[] = array(
				'data' => $sourceData,
				'validation' => $Model->validationErrors
			);
			return false;
		}
		$result[$Model->alias][$Model->primaryKey] = $Model->getLastInsertID();
		$this->savedRecords[] = $result;
		return true;
	}

/**
 * Return the errors from the copy process
 *
 * @param Model $Model Model attached to the behavior
 * @return array
 */
	public function getCopyErrors(Model $Model) {
		return $this->errors;
	}

/**
 * copyTargetExists
 *
 * @param Model $Model Model attached to the behavior
 * @param array $targetValues Values to overwrite at the target data
 * @param array $sourceData Source data
 * @throws RuntimeException
 * @return mixed
 */
	public function copyTargetExists(Model $Model, $targetValues, $sourceData) {
		if (method_exists($Model, 'copyTargetExists')) {
			return $Model->copyTargetExists($targetValues, $sourceData);
		} else {
			throw new RuntimeException(__('Your model must implement method copyTargetExists($target)'));
		}
	}
}
