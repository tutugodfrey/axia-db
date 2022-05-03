<?php
App::import('Behavior', 'Loggable.Loggable');

/**
 * Customize Loggable Plugin Behavior
 */
class ChangeRequestBehavior extends LoggableBehavior {

	const LOG_SCOPE = 'ChangeRequestBehavior';

/**
 * Add aditional default settings to Loggable behavior
 *
 * @param Model $model Model using this behavior
 * @param array $config Configuration settings for $model
 *
 * @return void
 */
	public function setup(Model $model, $config = array()) {
		$default = array(
			'approveChanges' => array(
				/**
				 * Array key to store the save options passed on "newChangeRequest" method
				 * This options will be serialized with the content, and used on
				 * the approvedChange method
				 */
				'saveOptionsKey' => 'saveOptions'
			),
		);
		$config = Hash::merge($default, $config);
		parent::setup($model, $config);
	}

/**
 * Callback function to get the SourceId
 *
 * Overwrite in the model if needed
 *
 * @param Model $model The model
 *
 * @return int The ID of relational Source
 */
	public function getSourceId(Model $model) {
		return AuthComponent::user('id');
	}

/**
 * After Save CakePHP callback
 *
 * Overwrite Loggable to dont save on creation data
 *
 * @param Model $model The model
 * @param bool $created True if it's create action
 * @param array $options Options array
 *
 * @return void
 */
	public function afterSave(Model $model, $created = true, $options = array()) {
		return true;
	}

/**
 * Before Save CakePHP callback
 *
 * Overwrite Loggable to dont save changes on edit.
 * Logs will be created with newChangeRequest
 *
 * @param Model $model The model
 * @param array $options Option parameters
 *
 * @return void
 */
	public function beforeSave(Model $model, $options = array()) {
		return true;
	}

/**
 * Get the key in the content array where the options for the approvedChanges save is stored
 *
 * @param Model $model The model
 *
 * @return array
 */
	protected function _getSaveOptionsKey(Model $model) {
		return Hash::get($this->settings, "{$model->alias}.approveChanges.saveOptionsKey");
	}

/**
 * Remove the defined exclude paths from the $data array
 *
 * @param array $data Array with the data to validate
 * @param array $options Change Request options
 *
 * @return bool
 */
	protected function _excludePaths($data, $options = array()) {
		$excludePaths = (array)Hash::get($options, 'excludePaths');
		foreach ($excludePaths as $path) {
			$data = Hash::remove($data, $path);
		}
		return $data;
	}

/**
 * Validate the data to save
 *
 * @param Model $model Model using this behavior
 * @param array $data Array with the data to validate
 * @param array $options Change Request options
 *
 * @return bool
 */
	protected function _validateData(Model $model, $data, $options = array()) {
		$dataToSave = $this->_excludePaths($data, $options);
		return $model->validateAssociated($dataToSave, $options);
	}

/**
 * Save the data to a LoggableLog instead of modify the Model record
 *
 * @param Model $model Model using this behavior
 * @param array $data Array with the changes to save
 * @param array $options Change Request options
 * @param string $viewAction name of the Controller's action where the change request originated
 *
 * @throws OutOfBoundsException Missing Merchant Note data
 * @return bool
 */
	public function newChangeRequest(Model $model, $data = array(), $options = array(), $viewAction = null) {
		if (!isset($data['MerchantNote'][0])) {
			throw new OutOfBoundsException(__('Missing Merchant Note data'));
		}
		$defaultOptions = array(
			/**
			 * If the parameters for the option associateLog are passed,
			 * "associateLog.model" will be unset from the data to be saved to the Log,
			 * this data will be saved normaly with the addition of the log record id at "associateLogId.field".
			 * This is useful to create a record with the related change log,
			 * for example, create the MerchantNote with the log id associated at "loggable_log_id"
			 */
			'associateLog' => array(
				'model' => null,
				'field' => null,
			),
			// Array paths from the request->data array to exclude on the approve "save"
			'excludePaths' => array(),
		);
		$options = Hash::merge($defaultOptions, $options);

		if (!$this->_validateData($model, $data, $options)) {
			return false;
		}

		$associateLogData = $this->_getAssociateModelData($model, $data, $options);
		$serializedData = $this->_serializeData($model, $data, $associateLogData, $options);
		//Saving multiple records is not fully supported by this behavior.
		//The following is a temporary fix needed to set the LoggableLogs.foreingKey when tryng to saveMany data or saveAssociated data
		if ($model->id === false) {
			//For a hasMany data structure with the model name set first id as the model id
			$foreingId = Hash::get($data, $model->alias . ".0.id");
			//For a hasMany data structure with indexed data and model name if still empty
			$foreingId = empty($foreingId)? Hash::get($data, "0." . $model->alias . ".id") : $foreingId;
			//For a hasMany data structure without the model name if still empty
			$foreingId = empty($foreingId)? Hash::get($data, "0.id") : $foreingId;
			//If data is for a saveMany but not hasMany association use the model name and id passed for the save if still empty
			$foreingId = empty($foreingId)? Hash::get($data, $model->alias . ".id") : $foreingId;

			if (empty($foreingId)) {
				throw new OutOfBoundsException(__('Data to be saved is not supported foreingKey cannot be set'));
			}
			$model->id = $foreingId;
		}

		if (!$this->_saveLog($model, 'newChangeRequest', $serializedData, $viewAction)) {
			return false;
		}

		return $this->_updateAssociateModel($model, $model->LoggableLog->id, $associateLogData, $options);
	}

/**
 * Edit the Model serialized data saved in loggable_logs
 *
 * @param Model $model Model using this behavior
 * @param string $id Id of the log with the serialized data to edit
 * @param array $newData Array with the new data to save
 *
 * @throws OutOfBoundException if LoggableLog not found
 * @return bool
 */
	public function editChangeRequest(Model $model, $id, $newData = array()) {
		if (empty($id)) {
			return false;
		}

		$loggedData = $this->getLog($model, $id);

		$options = (array)Hash::get($loggedData, $this->_getSaveOptionsKey($model, $loggedData));

		if (!$this->_validateData($model, $newData, $options)) {
			return false;
		}

		$associateLogData = $this->_getAssociateModelData($model, $newData, $options);
		$serializedData = $this->_serializeData($model, $newData, $associateLogData, $options);

		// Update the log record
		$loggedData['LoggableLog']['content'] = $serializedData;
		$loggedData['LoggableLog']['action'] = 'editChangeRequest';
		$loggedData['LoggableLog']['source_id'] = $model->getSourceId();
		$model->LoggableLog->create();
		if (!$model->LoggableLog->save($loggedData)) {
			return false;
		}
		return $this->_updateAssociateModel($model, $model->LoggableLog->id, $associateLogData, $options);
	}

/**
 * Get the data of model that will be saved with the log Id
 *
 * @param Model $model Model using this behavior
 * @param array $data Array with the changes to save
 * @param array $options Save options
 *
 * @return bool
 */
	protected function _getAssociateModelData(Model $model, $data = array(), $options = array()) {
		$associateLogData = null;
		$associateLogModel = Hash::get($options, 'associateLog.model');
		$associateLogField = Hash::get($options, 'associateLog.field');
		if (!empty($associateLogModel) && !empty($associateLogField) && ($associateLogModel != $model->alias)) {
			$associateLogData = Hash::get($data, $associateLogModel);
			if (Hash::numeric(array_keys($associateLogData))) {
				$associateLogData = $associateLogData[0];
			}
		}

		return $associateLogData;
	}

/**
 * Get the data of model that will be saved with the log Id
 *
 * @param Model $model Model using this behavior
 * @param string $logId Log record id
 * @param array $associateLogData Data to not include in the log and will be saved with the log id
 * @param array $options Log options
 *
 * @return bool
 */
	protected function _updateAssociateModel(Model $model, $logId, $associateLogData = array(), $options = array()) {
		if (!empty($associateLogData)) {
			$associateModel = Hash::get($options, 'associateLog.model');
			if (!isset($associateLogData[$associateModel])) {
				$associateLogData[$associateModel] = $associateLogData;
			}
			$associateLogData[$associateModel][Hash::get($options, 'associateLog.field')] = $logId;

			$associateModel = ClassRegistry::init("$associateModel");
			$associateModel->create();
			if (!$associateModel->save($associateLogData)) {
				$model->LoggableLog->delete($logId);
				return false;
			}
		}
		return true;
	}

/**
 * Serialize the Model data and the "save options" to store it in the log
 *
 * @param Model $model Model using this behavior
 * @param array $data Array with the changes to save
 * @param array $associateLogData Data to not include in the log and will be saved with the log id
 * @param array $options Save options
 *
 * @return bool
 */
	protected function _serializeData(Model $model, $data = array(), $associateLogData = array(), $options = array()) {
		if (!empty($associateLogData)) {
			unset($data[Hash::get($options, 'associateLog.model')]);
		}

		// Store the save options to be applied at "approvedChanges"
		$saveOptionsKey = $this->_getSaveOptionsKey($model);
		if (!empty($saveOptionsKey)) {
			$data[$saveOptionsKey] = $options;
		}

		return $model->serialize($data);
	}

/**
 * Save the data to to the model and/or associated models.
 * This method can perform a bulkSave in addition to all CakePHP save methods.
 * A bulk save will occur if "Request.isBulkedData => true" was set in the logged data to save. Also, a bulk save will ocurr
 * if the data to save was strucured with BOTH indexed keys (as in a saveMany) and also with Model names as keys.
 * To saveAssociated or saveMany use the standarized structure required for each corresponding save method.
 *
 * @param Model $model Model using this behavior
 * @param string $id Id of the loggable_log record
 * @throws OutOfBoundException if LoggableLog not found
 * @return bool
 */
	public function approveChange(Model $model, $id) {
		$loggedData = $this->getLog($model, $id);
		$saveOptions = (array)Hash::get($loggedData, $this->_getSaveOptionsKey($model));
		$data = Hash::get($loggedData, 'Unserialized');
		$dataToSave = $this->_excludePaths($data, $saveOptions);
		$dataSource = $model->getDataSource();
		//Check if data contains both indexed and Model data
		$keys = array_keys($dataToSave);
		$allModels = App::objects('model');
		$isModel = false;
		$isNumeric = false;
		$isBulkData = (Hash::get($data, "Request.isBulkedData"));
		if (is_null($isBulkData)) {
			foreach ($keys as $key) {
				if ($isModel === false) {
					$isModel = in_array($key, $allModels);
				}
				if ($isNumeric === false) {
					$isNumeric = is_numeric($key);
				}
				if ($isNumeric && $isModel) {
					$isBulkData = true;
					break;
				} else {
					$isBulkData = false;
				}
			}
		}
		$dataSource->begin();
		if ($isBulkData) {
			$isSaved = $this->_saveBulk($model, $dataToSave);
		} else {
			$model->create();
			$isSaved = $model->saveAll($dataToSave, $saveOptions);
		}

		if (!$isSaved) {
			$dataSource->rollback();
			$this->log('approveChange - Could not save the logged data ', self::LOG_SCOPE);
			$this->log($model->validationErrors, self::LOG_SCOPE);
			$this->log($loggedData, self::LOG_SCOPE);
			return $isSaved;
		}
		$dataSource->commit();
		return $isSaved;
	}

/**
 * Saves the bulk of data passed.
 * The $model passed will be regarded as the "main" model for this save, any numerical indexes that contain data to be saved will be considered to
 * be for this main model and it's associated models within each index as in a saveMany data structure.
 * Non-indexed data must have their corresponding ModelName as the key in order to use it to save its data. Additionally,
 * non-indexed data will be saved individually from the main $model.
 *
 * @param Model $model The model
 * @param array $bulkDataToSave the bulk of data
 * @return boolean
 */
	protected function _saveBulk($model, $bulkDataToSave) {
		$isSaved = true; //set initially true
		$allModels = App::objects('model');
		foreach ($bulkDataToSave as $key => $modelData) {
			//Check if the key is an existing model name and data is not empty
			if (!is_numeric($key) && in_array($key, $allModels) && !empty($modelData)) {
				$relatedModel = ClassRegistry::init($key);
				$relatedModel->create();
				$isSaved *= $relatedModel->saveAll($modelData, array('deep' => true));
				unset($bulkDataToSave[$key]);
			}
		}
		//check if there is indexed $model data to save as saveMany
		if (!empty(Hash::extract($bulkDataToSave, "{n}." . $model->alias)) || !empty(Hash::extract($bulkDataToSave, '{n}'))) {
			$isSaved *= $model->saveAll($bulkDataToSave, array('deep' => true));
		}
		ClassRegistry::flush();
		return (bool)$isSaved;
	}

/**
 * Retrives one log to you
 *
 * Overwrite the Loggable function to get more information
 *
 * @param Model $model The model
 * @param int $id Id of the loggable_log record
 *
 * @throws OutOfBoundsException if LoggableLog not found
 * @return array
 */
	public function getLog(Model $model, $id) {
		$log = $model->LoggableLog->find('first', array(
			'conditions' => array(
				'LoggableLog.id' => $id
			),
			'recursive' => -1
		));
		if (empty($log)) {
			throw new OutOfBoundsException(__('Missing LoggableLog with id %s', $id));
		}

		return $this->_prepareData($model, $log);
	}

/**
 * Prepare the data to use
 *
 * @param Model $model The model
 * @param array $log Log entry
 *
 * @return array
 */
	protected function _prepareData(Model $model, $log) {
		$content = Hash::get($log, 'LoggableLog.content');
		$saveOptionsKey = $this->_getSaveOptionsKey($model);

		$log['Unserialized'] = $model->unserialize($content);
		if (!empty($saveOptionsKey)) {
			$log[$saveOptionsKey] = (array)Hash::get($log, "Unserialized.{$saveOptionsKey}");
			unset($log['Unserialized'][$saveOptionsKey]);
		}

		return $log;
	}

/**
 * Merge the logged data with the model record data
 * The related models with many records will be replaced instead of merged
 *
 * @param Model $model The model
 * @param int $id Id of the loggable_log record
 * @param array $originalData Model record original data
 *
 * @throws OutOfBoundsException if LoggableLog not found
 * @return array
 */
	public function mergeLoggedData(Model $model, $id, $originalData = array()) {
		$loggableLog = $this->getLog($model, $id);
		$unserializedData = Hash::get($loggableLog, 'Unserialized');
		$mergedData = $originalData;
		// Iterate the logged models to merge the data
		if (is_array($unserializedData)) {
			foreach ($unserializedData as $key => $data) {
				if (!isset($originalData[$key])) {
					$mergedData[$key] = $data;
				} else {
					if (!is_array($data)) {
						throw new OutOfBoundsException("Serialized data must contain the model alias, an array was expected");
					}
					// The models with many records will be replaced instead of merged
					if (Hash::numeric(array_keys($data))) {
						$mergedData[$key] = $data;
					} else {
						//Using a recursive merge will cause deeper associated hasMany data array with numerical indices to become corrupted
						//therefore any possible data at the third dim and beyond needs to be replaced with non-recursive merge
						$mergedData[$key] = array_merge($originalData[$key], $data);
					}
				}
			}
		}
		return $mergedData;
	}
}
