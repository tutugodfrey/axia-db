<?php

App::uses('AppModel', 'Model');

class MaintenanceDashboard extends AppModel {

/**
 * Name
 *
 * @var string $name
 * @access public
 */
	public $name = 'MaintenanceDashboard';

	public $useTable = false;

/**
 * ModelNames
 * List of Maintainance-requiring models
 *
 * @var array $modelNames
 * 
 */
	public $modelNames = array(
		"BackEndNetwork",
		"BetNetwork",
		"BetTable",
		"CancellationFee",
		"CardType",
		"Client",
		"DebitAcquirer",
		"Entity",
		"Gateway",
		"Groups",
		"MerchantAchAppStatus",
		"MerchantAchBillingOption",
		"MerchantAchReason",
		"MerchantAcquirer",
		"MerchantBin",
		"MerchantCancellationSubreason",
		"MerchantRejectType",
		"MerchantType",
		"MerchantUwFinalApproved",
		"MerchantUwFinalStatus",
		"Network",
		"NonTaxableReason",
		"Organization",
		"OriginalAcquirer",
		"ProductCategory",
		"ProductFeature",
		"ProductsServicesType",
		"RateStructure",
		"Region",
		"SponsorBank",
		"Subregion",
		"TimelineItem",
		"UwApprovalinfo",
		"UwInfodoc",
		"UwStatus",
		"Vendor",
	);

/**
 * getModelOptions
 *
 * @return array
 */
	public function getModelOptions() {
		asort($this->modelNames);
		$content = array_map("Inflector::underscore", $this->modelNames);
		$content = array_map("Inflector::humanize", $content);
		$content = array_map("Inflector::pluralize", $content);
		return array_combine($this->modelNames, $content);
	}

/**
 * getContentData
 *
 * @param string $modelName Mode Name
 * @return array containing data to be used on the view
 */
	public function getContentData($modelName) {
		$Model = ClassRegistry::init($modelName);
		$headers = $this->_getHumanizedColumns(array_keys($Model->getColumnTypes()));
		$modelData = $this->getModelData($modelName);
		return compact('headers', 'modelData', 'modelName');
	}

/**
 * getContentData
 *
 * @param string $modelName Mode Name
 * @param string $id a record id that corresponds to $modelName
 * @return array containing data to be used on the view
 */
	public function getModelData($modelName, $id = '') {
		$Model = ClassRegistry::init($modelName);
		if (empty($id)) {
			$assocModels = $Model->getAssociated('belongsTo');
			$sortCol = $Model->displayField;
			if (defined($Model->alias . "::SORT_FIELD")) {
				$sortCol = $modelName::SORT_FIELD;
			}
			$settings = ['order' => ["{$Model->alias}.$sortCol ASC"], 'contain' => $assocModels];
			$findType = 'all';
		} else {
			$settings = ['conditions' => ["$modelName.id" => $id]];
			$findType = 'first';
		}
		return $Model->find($findType, $settings);
	}

/**
 * _getHumanizedColumns
 *
 * @param array $underscoredFields indexed array of undersorized model fields
 * @return array of human readable model fields without id or old legacy fields
 */
	protected function _getHumanizedColumns(array $underscoredFields) {
		$humanCols = array_map("Inflector::humanize", $underscoredFields);
		//Remove id and ol id fields and normalize foreing key column names
		foreach ($humanCols as $idx => $name) {
			if ($name === 'Id' || stripos($name, "Id Old") !== false || substr($name, - 4) === " Old") {
				unset($humanCols[$idx]);
			}
			if (substr($name, - 3) === " Id") {
				$humanCols[$idx] = substr($name, 0, strlen($name) - 3);
			}
		}
		return $humanCols;
	}

/**
 *	getEditViewData 
 *
 * @param string $modelName Mode Name
 * @return array containing data to be used on the edit mode view
 */
	public function getEditViewData($modelName) {
		$Model = ClassRegistry::init($modelName);

		//Handle special case for unserialized data in ProductsServicesType.custom_fields
		if ($modelName === 'ProductsServicesType') {
			//The custom fields belong to the related ProductSetting model for any given product
			$viewData['productSettingFields'] = array_keys(ClassRegistry::init('ProductSetting')->getColumnTypes());
		}

		$assocModels = $Model->getAssociated('belongsTo');
		//create a properly named variable to be used as drop down for foreingKeys
		$viewData['modelName'] = $modelName;
		$viewData['fieldTypes'] = $Model->getColumnTypes();
		if (!empty($assocModels)) {
			//Build AssociateModel data if this Model has a foreingKey.
			foreach ($assocModels as $name) {
				$AssociatedModel = ClassRegistry::init($name);
				$varName = Inflector::variable($AssociatedModel->useTable);
				$viewData[$varName] = $AssociatedModel->getList();
			}
		}
		if ($modelName === 'MerchantAchReason') {
			$MerchantAch = ClassRegistry::init('MerchantAch');
			foreach ($MerchantAch->arLabels as $colAlias => $colMetaData) {
				if (Hash::get($colMetaData, 'mapped_column')) {
					$colAliases[$colAlias] = $colMetaData['text'];
				}
			}
		$viewData['accountingReportColAliases'] = $colAliases;
		}
		return $viewData;
	}

/**
 *	sainitizeData 
 *	Can perform various data sanitation procedures depending on which data is passed
 *
 * @param array &$requestData reference to data submited from edit view
 * @return void
 */
	public function sainitizeData(&$requestData) {
		if (array_key_exists('ProductsServicesType', $requestData)) {
			if (empty(Hash::filter(Hash::extract($requestData, 'ProductsServicesType.custom_labels')))) {
				unset($requestData['ProductsServicesType']['custom_labels']);
			} else {
				$requestData['ProductsServicesType']['custom_labels'] = serialize($requestData['ProductsServicesType']['custom_labels']);
			}
		}
	}

/**
 * saveCustomSortModelData 
 * Saves data of models that behave as Lists. Those models have a custom defined sort order sequence which must be maintained properly
 * to keep a correct sequence and avoid duplicates in the sequence
 * 
 * @param array $model model instance
 * @param array $requestData data submited from edit view
 * @return void
 * @throws Exception
 */
	public function saveCustomSortModelData(Model $model, $requestData) {
		//Models that behave as list must have a member constant defined with the name of the table field used for the custom sort sequence
		//That const is expected to be Model::SORT_FIELD
		if (!defined($model->alias . "::SORT_FIELD")) {
			throw new Exception("Unable to accurately save record position! Expected member constant {$model->alias}::SORT_FIELD is not defined.");
		}
		$modelName = $model->alias;
		//Only update/set the record position iff the defined position field is in the data
		if (array_key_exists($modelName::SORT_FIELD, $requestData[$modelName])) {
			$itemPosition = $requestData[$modelName][$modelName::SORT_FIELD];
			//Behavior requires id key to be set
			$requestData[$modelName]['id'] = Hash::get($requestData, "$modelName.id");
			$model->set($requestData[$modelName]);
			$model->insertAt($itemPosition);
			$model->fixListOrder();

			return true;
		} else {
			if ($model->save($requestData[$modelName])) {
				return true;
			} else {
				throw new Exception("Failed to save $modelName data" . implode(', ', Hash::flatten($model->validationErrors)));
			}
		}
		return false;
	}

/**
 *	_updateLog 
 *	Logs data that is deleted
 *
 * @param string $id an id that belongs to modelName
 * @param string $modelName name of a model that contains the record id
 * @return boolean Will return false if something goes wrong logging the data to be deleted
 */
	protected function _updateLog($id, $Model) {
		$dataToLog = $Model->find('first', ['conditions' => ['id' => $id]]);
		if (empty($dataToLog[$Model->alias])) {
			return false;
		}
		$LogableLog = ClassRegistry::init('LoggableLog');
		$newLog = $LogableLog->find('first', ['conditions' => ['foreign_key' => $id, 'model' => $Model->alias, 'action' => 'adminMaintenanceDelete']]);

		$serializedData = serialize($dataToLog);
		$newLog['LoggableLog']['model'] = $Model->alias;
		$newLog['LoggableLog']['foreign_key'] = $id;
		$newLog['LoggableLog']['action'] = 'adminMaintenanceDelete';
		$newLog['LoggableLog']['content'] = $serializedData;

		$LogableLog->create();
		if (!$LogableLog->save($newLog)) {
			return false;
		}
		return true;
	}

/**
 *	delete 
 *	Deletes data from the model passed
 *
 * @param string $id an id that belongs to modelName
 * @param string $modelName name of a model that contains the record id
 * @return boolean Will return false if something goes wrong deleting data
 */
	public function deleteAndLog($id, $modelName) {
		$Model = ClassRegistry::init($modelName);
		$Model->id = $id;
		if (!$Model->exists()) {
			return false;
		}

		$dataSource = $Model->getDataSource();
		$dataSource->begin();
		if (!$this->_updateLog($id, $Model) || !$Model->deleteAll(['id' => $id])) {
			$dataSource->rollback();
			return false;
		}
		//Fix the record list sequence after deletion iff the model has a custom order column defined
		if (defined($Model->alias . "::SORT_FIELD")) {
			$Model->fixListOrder();
		}
		$dataSource->commit();
		return true;
	}

/**
 *	countAffectedRecords
 *	Checks if any Model foreingKeys have records with the given id.
 *	Returns a count of associated Model Records that contain the id in the parameter as foreingKey.
 *
 * @param string $id a record id that belongs to nodelName
 * @param string $modelName the name of the model that has the record with $id param
 * @return integer
 */
	public function countAffectedRecords($id, $modelName) {
		$allModels = App::objects('model');
		$foreingKey = Inflector::underscore($modelName) . '_id';
		$count = 0;
		foreach ($allModels as $assocModelName) {
			$AssociateModel = ClassRegistry::init($assocModelName);
			try {
				if ($AssociateModel->hasField($foreingKey) && $AssociateModel->hasAny([$foreingKey => $id])) {
					$count += $AssociateModel->find('count', [ 'conditions' => [$foreingKey => $id]]);
				}
			} catch (MissingTableException $e) {
				//Ignore errors resulting from Models that do not use tables
			}
		}
		ClassRegistry::flush();
		return $count;
	}

/**
 * getLoggedDeletion
 *
 * @param string $foreignId the id that belongs to the model from which data was deleted
 * @param string $modelName name of the model from which a data deletion was logged
 * @return boolean | array 
 */
	public function getDeletedFromLog($foreignId, $modelName) {
		$LogableLog = ClassRegistry::init('LoggableLog');
		$deletedContent = $LogableLog->field('content', ['foreign_key' => $foreignId, 'model' => $modelName, 'action' => 'adminMaintenanceDelete']);
		if (empty($deletedContent)) {
			return false;
		}
		return unserialize($deletedContent);
	}

/**
 *	undoDelete 
 *	Recovers data that was deleted
 *
 * @param string $id an id that belongs to modelName
 * @param string $modelName name of a model that contains the record id
 * @return boolean
 * @throws Exception
 * @throws OutOfBoundsException
 */
	public function undoDelete($id, $modelName) {
		$result = $this->getDeletedFromLog($id, $modelName);
		if ($result === false) {
			throw new OutOfBoundsException("Failed to undo: unable to find backed up data from which to restore!");
		}
		$Model = ClassRegistry::init($modelName);
		$Model->create();
		if (!$Model->save($result)) {
			throw new Exception("Error occurred restoring data from back up. Please Try again");
		}
		return true;
	}

}
