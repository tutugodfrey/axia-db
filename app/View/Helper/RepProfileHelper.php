<?php

App::uses('AxiaHtmlHelper', 'View/Helper');
App::uses('ResidualParameterType', 'Model');

/**
 * Custom Helper
 */
class RepProfileHelper extends AxiaHtmlHelper {

/**
 *
 * @var array
 */
	public $helpers = array(
		'Number',
		'Form'
	);

/**
 * @var array default settings to be merged with current settings
 * Type.* will be the path expression to retrieve several fields for the type, example UserParameterType.*
 * valueTypeCheckbox should match the type for the checkbox values
 * valueModelAlias is the alias for the model used for values, example UserParameter
 */
	protected $_defaultSettings = array(
		'Type.id' => 'UserParameterType.id',
		'Type.type' => 'UserParameterType.type',
		'Type.name' => 'UserParameterType.name',
		'Type.value_type' => 'UserParameterType.value_type',
		'valueTypeCheckbox' => 0, //UserParameterType::VALUE_TYPE_CHECKBOX,
		'typeSimple' => 0, //UserParameterType::TYPE_SIMPLE,
		'valueModelAlias' => 'UserParameter',
		'parameterTypeField' => 'user_parameter_type_id',
	);

/**
 * Constructor
 *
 * @param \View $View View object
 * @param array $settings Configuration array
 */
	public function __construct(\View $View, $settings = array()) {
		return parent::__construct($View, Hash::merge($this->_defaultSettings, $settings));
	}

/**
 * Display a toggable table view for User Parameters
 *
 * @param array $valueModelHeaders of Type and related value model
 * @param array $productsServicesTypes of ProductServiceType
 * @return string html
 */
	public function view($valueModelHeaders, $productsServicesTypes) {
		$headers = $this->_getTableHeaders($valueModelHeaders);
		$cells = array();
		//rows = products
		foreach ($productsServicesTypes as $productsServicesType) {
			$row = $this->_viewRow($productsServicesType, $valueModelHeaders);
			$cells[] = $row;
		}
		$toggles = array();
		return $this->toggleTable($headers, $cells, $toggles, array(parent::TOGGLE_TABLE_MULTIPLE_HEADER_ROWS => true));
	}

/**
 * Create static table headers for both edit & view grids
 *
 * @param array $valueModelHeaders Columns Information
 * @return string html
 */
	protected function _getTableHeaders($valueModelHeaders) {
		$topHeaders = $this->_getTableTopHeaders($valueModelHeaders);
		$headers = array(__('Products'));
		foreach ($valueModelHeaders as $valueModelHeader) {
			$headers[] = $this->_getParameterHeader($valueModelHeader);
		}
		$topHeaders[] = $headers;
		return $topHeaders;
	}

/**
 * Display the parameter header according to the type
 *
 * @param array $valueModelHeader Column Information
 * @return string with the header text
 */
	protected function _getParameterHeader($valueModelHeader) {
		$type = Hash::get($valueModelHeader, $this->settings['Type.type']);
		if ($type === $this->settings['typeSimple']) {
			//simple types are associated only to the current user, display the name
			return h(Hash::get($valueModelHeader, $this->settings['Type.name']));
		} else {
			return h(Hash::get($valueModelHeader, 'MerchantAcquirer.acquirer'));
		}
	}

/**
 * Get extra headers to group and display headers using collspans
 * Will return 2 row of headers to be added to the top of the table
 *
 * @param array $valueModelHeaders Column information
 * @return array
 */
	protected function _getTableTopHeaders($valueModelHeaders) {
		$merchantAcquirerIds = Hash::extract($valueModelHeaders, '{n}.MerchantAcquirer.id');
		$countMerchantAcquirers = count(array_unique($merchantAcquirerIds));
		$associatedUserIds = Hash::extract($valueModelHeaders, '{n}.AssociatedUser.associated_user_id');
		$uniqueUserIds = array_unique($associatedUserIds);
		$associatedUserCount = count($uniqueUserIds);

		//Row with the user names
		$headerUsers = array(
			'', //products
			array(__('User Parameters') => array('colspan' => 3 + $countMerchantAcquirers, 'class' => 'text-center')),
		);
		$headerTypes = array(
			'', //products
			'',
			'',
			'',
		);
		foreach ($uniqueUserIds as $userId) {
			$name = Hash::extract($valueModelHeaders, "{n}.UserAssociated[id=$userId].user_last_name");
			$name = reset($name);
			if (!empty($name)) {
				$name .= ' ' . __('Parameters');
				$headerUsers[] = array(h($name) => array('colspan' => $countMerchantAcquirers, 'class' => 'text-center'));
			}
			$headerTypes[] = array(__('% of Gross Profit') => array('colspan' => $countMerchantAcquirers, 'class' => 'text-center'));
		}
		$headerTypes[] = array('' => array('colspan' => $associatedUserCount, 'class' => 'text-center'));

		return array($headerUsers, $headerTypes);
	}

/**
 * Display 1 row, for a specific product
 *
 * @param array $productsServicesType Product service information
 * @param array $valueModelHeaders Columns information
 * @return array 1 row
 */
	protected function _viewRow($productsServicesType, $valueModelHeaders) {
		//first column = product name
		$row = array(h(Hash::get($productsServicesType, 'ProductsServicesType.products_services_description')));
		//rest of the columns, get the values
		foreach ($valueModelHeaders as $valueModelHeader) {
			$value = 0;
			$valueType = Hash::get($valueModelHeader, $this->settings['Type.value_type']);
			$valueModel = $this->_getValueModel($productsServicesType, $valueModelHeader);
			if (!empty($valueModel)) {
				$value = Hash::get($valueModel, 'value');
			}

			$row[] = $this->_getParameterValue($value, $valueType);
		}
		return $row;
	}

/**
 * Display the parameter value formatted according to the type
 *
 * @param array $value Cell value
 * @param array $valueType Value type
 * @return mixed parameter value
 */
	protected function _getParameterValue($value, $valueType) {
		if ($valueType === $this->settings['valueTypeCheckbox']) {
			return $this->checkboxValue($value);
		} else {
			return $this->Number->format($value);
		}

		//need to know if there are subtypes
	}

/**
 * Find the user parameter data for a specific header
 *
 * @param array $productsServicesType Product service information
 * @param array $valueModelHeader Column information
 * @return array userParameterData
 */
	protected function _getValueModel($productsServicesType, $valueModelHeader) {
		$type = Hash::get($valueModelHeader, $this->settings['Type.type']);
		$productsServicesTypeId = Hash::get($productsServicesType, 'ProductsServicesType.id');
		if (is_null($productsServicesTypeId)) {
			return null;
		}
		$merchantAcquirerId = Hash::get($valueModelHeader, 'MerchantAcquirer.id');
		$associatedUserId = Hash::get($valueModelHeader, 'AssociatedUser.associated_user_id');
		$isMultiple = $this->_getIsMultiple($valueModelHeader);
		$value = null;
		if ($type === $this->settings['typeSimple']) {
			//simple value, get it. Only 1 ValueModel present
			//get the user parameter matching current product Id
			$values = Hash::extract($valueModelHeader, $this->settings['valueModelAlias'] . ".{n}[products_services_type_id=$productsServicesTypeId]");
			if (!empty($values)) {
				$value = array_pop($values);
			}
		} else {
			foreach ($valueModelHeader[$this->settings['valueModelAlias']] as $idx => $data) {
				if (!is_numeric($idx)) {
					return null;
				}
				if ($data['products_services_type_id'] === $productsServicesTypeId &&
					$data['merchant_acquirer_id'] === $merchantAcquirerId &&
					$data['associated_user_id'] === $associatedUserId &&
					$data['is_multiple'] == $isMultiple) {
					$value = $data;
					break;//there will always be only one match
				}
			}
			if (empty($value)) {
				$value = null;
			}
		}
		return $value;
	}

/**
 * Utility to extract a is_multiple value and default to 0 if not found
 *
 * @param type $valueModelHeader Column information
 * @return int
 */
	protected function _getIsMultiple($valueModelHeader) {
		$isMultiple = Hash::get($valueModelHeader, 'AssociatedUser.is_multiple');
		if (is_null($isMultiple)) {
			$isMultiple = 0;
		}
		return $isMultiple;
	}

/**
 * Display edit toggle table based on user parameter headers and types
 * Simple types will be displayed as a checkbox
 * Complex types will be displayed depending of their type
 *
 * @param array $valueModelHeaders of Type and related valueModel
 * @param array $productsServicesTypes of ProductServiceType
 * @return string html
 */
	public function edit($valueModelHeaders, $productsServicesTypes) {
		$headers = $this->_getTableHeaders($valueModelHeaders);
		$cells = array();
		//inputIndex used to assing an index to the valueModel.index for the saveMany
		$inputIndex = 0;
		//rows = products
		foreach ($productsServicesTypes as $productsServicesType) {
			$row = $this->_editRow($productsServicesType, $valueModelHeaders, $inputIndex);
			$cells[] = $row;
		}
		$toggles = array();
		return $this->toggleTable($headers, $cells, $toggles, array(parent::TOGGLE_TABLE_MULTIPLE_HEADER_ROWS => true));
	}

/**
 * Display 1 related to a product, and 1 column per each userParameterHeader
 * Based on the type of the header, checkbox or input will be used
 *
 * @param type $productsServicesType Product service information
 * @param type $valueModelHeaders Columns information
 * @param int &$inputIndex Index in the array for saveMany
 * @return array 1 row
 */
	protected function _editRow($productsServicesType, $valueModelHeaders, &$inputIndex = 0) {
		//first column = product name
		$row = array(Hash::get($productsServicesType, 'ProductsServicesType.products_services_description'));
		//rest of the columns, get the values
		foreach ($valueModelHeaders as $valueModelHeader) {
			$value = 0;
			$valueTypeId = null;
			$rowContent = '';
			$valueType = Hash::get($valueModelHeader, $this->settings['Type.value_type']);
			//we get the correct user parameter, checking if simple or complex type
			$valueModel = $this->_getValueModel($productsServicesType, $valueModelHeader);
			//update if there is a previously saved value
			if (!empty($valueModel)) {
				//now we display the value
				$value = Hash::get($valueModel, 'value');
				$id = Hash::get($valueModel, 'id');
				//adding a hidden input to hold the id and update the valueModel instead of saving a new one
				$rowContent .= $this->Form->input($this->settings['valueModelAlias'] . ".$inputIndex.id", array(
					'type' => 'hidden',
					'value' => $id,
				));
				$valueTypeId = Hash::get($valueModel, $this->settings['parameterTypeField']);
			}

			//add other hidden id fields to be saved with the field value
			//input for the Type
			$rowContent .= $this->Form->input($this->settings['valueModelAlias'] . ".$inputIndex." . $this->settings['parameterTypeField'], array(
				'type' => 'hidden',
				'value' => Hash::get($valueModelHeader, $this->settings['Type.id']),
				'label' => false,
			));
			//input for the UserCompensationProfile
			$rowContent .= $this->Form->input($this->settings['valueModelAlias'] . ".$inputIndex.user_compensation_profile_id", array(
				'type' => 'hidden',
				'value' => Hash::get($this->request->data, 'UserCompensationProfile.id'),
				'label' => false,
			));
			$rowContent .= $this->Form->input($this->settings['valueModelAlias'] . ".$inputIndex.products_services_type_id", array(
				'type' => 'hidden',
				'value' => Hash::get($productsServicesType, 'ProductsServicesType.id'),
				'label' => false,
			));
			//input for the AssociatedUser, generae HTML iff there is an associated user set
			if (!empty(Hash::get($valueModelHeader, 'AssociatedUser.associated_user_id'))) {
				$rowContent .= $this->Form->input($this->settings['valueModelAlias'] . ".$inputIndex.associated_user_id", array(
					'type' => 'hidden',
					'value' => Hash::get($valueModelHeader, 'AssociatedUser.associated_user_id'),
					'label' => false,
				));
			}
			if (empty($valueTypeId)) {
				$valueTypeId = Hash::get($valueModelHeader, $this->settings['Type.id']);
			}
			$rowContent .= $this->Form->input($this->settings['valueModelAlias'] . ".$inputIndex.is_multiple", array(
				'type' => 'hidden',
				'value' => (int)($valueTypeId === ResidualParameterType::R_MULTIPLE || $valueTypeId === ResidualParameterType::PR_MULTIPLE || $valueTypeId === ResidualParameterType::MGR_MULTIPLE),
				'label' => false,
			));

			$rowContent .= $this->_getNotCommonInputs($valueModelHeader, $inputIndex);

			//check if the value was submited before
			if ($this->request->is('post') || $this->request->is('put')) {
				$value = Hash::get($this->request->data, $this->settings['valueModelAlias'] . ".$inputIndex.value");
			}
			//the visible input, to allow editing values in the grid
			if ($valueType === $this->settings['valueTypeCheckbox']) {
				//input for the value
				$rowContent .= $this->Form->input($this->settings['valueModelAlias'] . ".$inputIndex.value", array(
					'type' => 'checkbox',
					//Add a spacer div to prevent the text input div from z-index overlapping this checbox on some Chrome and FF browsers
					'div' => array('class' => 'col col-md-1'),
					'checked' => (bool)$value,
					'label' => false,
				));
			} else {
				if (!empty($valueTypeId) && $valueTypeId !== ResidualParameterType::R_MULTIPLE && $valueTypeId !== ResidualParameterType::PR_MULTIPLE && $valueTypeId !== ResidualParameterType::MGR_MULTIPLE) {
					$value = CakeNumber::precision($value, 3);
				}
				$rowContent .= $this->Form->input($this->settings['valueModelAlias'] . ".$inputIndex.value", array(
					'type' => 'text',
					'value' => $value,
					'label' => false,
				));
			}

			$row[] = $rowContent;
			//need to know if there are subtypes
			$inputIndex++;
		}
		return $row;
	}

/**
 * Display the model not common inputs for the current model
 *
 * @param array $valueModelHeader Column information
 * @param array $inputIndex Index in the array for save many
 * @return string with the html code of the imputs
 */
	protected function _getNotCommonInputs($valueModelHeader, $inputIndex) {
		$html = '';

		//input for the MerchantAcquirer
		$html .= $this->Form->input($this->settings['valueModelAlias'] . ".{$inputIndex}.merchant_acquirer_id", array(
			'type' => 'hidden',
			'value' => Hash::get($valueModelHeader, 'MerchantAcquirer.id'),
			'label' => false,
		));

		return $html;
	}

}
