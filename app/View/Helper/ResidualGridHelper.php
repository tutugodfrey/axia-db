<?php

App::uses('RepProfileHelper', 'View/Helper');

/**
 * Custom Helper
 * Extending the rep profile helper with specific params
 */
class ResidualGridHelper extends RepProfileHelper {

/**
 * @var array default settings to be merged with current settings
 * Type.* will be the path expression to retrieve several fields for the type, example UserParameterType.*
 * valueTypeCheckbox should match the type for the checkbox values
 * valueModelAlias is the alias for the model used for values, example UserParameter
 */
	protected $_defaultSettings = array(
		'Type.id' => 'ResidualParameterType.id',
		'Type.type' => 'ResidualParameterType.type',
		'Type.name' => 'ResidualParameterType.name',
		'Type.value_type' => 'ResidualParameterType.value_type',
		'Type.is_multiple' => 'ResidualParameterType.is_multiple',
		'typeSimple' => ResidualParameterType::TYPE_SIMPLE,
		'typeManager' => ResidualParameterType::TYPE_MANAGER,
		'typePartner' => ResidualParameterType::TYPE_PARTNER,
		'valueTypeCheckbox' => ResidualParameterType::VALUE_TYPE_CHECKBOX,
		'valueTypeNumber' => ResidualParameterType::VALUE_TYPE_NUMBER,
		'valueTypePercentage' => ResidualParameterType::VALUE_TYPE_PERCENTAGE,
		'valueModelAlias' => 'ResidualParameter',
		'parameterTypeField' => 'residual_parameter_type_id',
		//settings for the residual tier table
		'TierTable.model' => 'ResidualVolumeTier',
		'TierTable.definedTiers' => ResidualVolumeTier::DEFINED_TIERS,
		'TierTable.overrideTier' => ResidualVolumeTier::OVERRIDE_TIER,
	);

/**
 * Constructor
 *
 * @param \View $View View object
 * @param array $settings Configuration array
 */
	public function __construct(View $View, $settings = array()) {
		return parent::__construct($View, Hash::merge($this->_defaultSettings, $settings));
	}

/**
 * Display the parameter header according to the type
 *
 * @param array $valueModelHeader Column information
 * @return string with the header text
 */
	protected function _getParameterHeader($valueModelHeader) {
		$type = Hash::get($valueModelHeader, $this->settings['Type.type']);
		if ($type === $this->settings['typeManager']) {
			if (Hash::get($valueModelHeader, $this->settings['Type.is_multiple'])) {
				return h(Hash::get($valueModelHeader, 'UserAssociated.user_last_name') . ' Multiple');
			} else {
				return h(Hash::get($valueModelHeader, 'UserAssociated.user_last_name') . ' %');
			}
		} elseif ($type === $this->settings['typePartner']) {
			if (Hash::get($valueModelHeader, $this->settings['Type.is_multiple'])) {
				return h(Hash::get($valueModelHeader, 'UserAssociated.user_last_name') . ' Multiple');
			} else {
				return h(Hash::get($valueModelHeader, 'UserAssociated.user_last_name') . ' %');
			}
		} else {
			return Hash::get($valueModelHeader, $this->settings['Type.name']);
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
		// first column for products
		$headerTiers = array('');
		$headerUsers = array('');
		$tier = 1;
		$column = 1;
		$lastTierEndColumn = 1;
		// will count the managers and partners for each tier
		$managersCount = 0;
		$partnersCount = 0;

		foreach ($valueModelHeaders as $valueModelHeader) {
			switch (Hash::get($valueModelHeader, $this->settings['Type.type'])) {
				// Managers columns
				case $this->settings['typeManager']:
					$managersCount++;
					break;
				// Partners columns
				case $this->settings['typePartner']:
					$partnersCount++;
					break;
			}

			// Create a new header for each tier
			if ($tier != $valueModelHeader['Tier']) {
				$headerTiers[] = array(
					$this->_getTierLabel($tier) => array('colspan' => $column - $lastTierEndColumn, 'class' => 'text-center')
				);

				// Create user headers for each tier
				$headerUsers[] = array(__('User') => array('colspan' => 2, 'class' => 'text-center'));
				if ($managersCount > 0) {
					$headerUsers[] = array(__('Managers') => array('colspan' => $managersCount, 'class' => 'text-center'));
					$managersCount = 0;
				}
				if ($partnersCount > 0) {
					$headerUsers[] = array(__('Partners') => array('colspan' => $partnersCount, 'class' => 'text-center'));
					$partnersCount = 0;
				}

				$tier = $valueModelHeader['Tier'];
				$lastTierEndColumn = $column;
			}

			$column++;
		}
		// Create the header for the last tier
		$headerTiers[] = array(
			$this->_getTierLabel($tier) => array('colspan' => $column - $lastTierEndColumn, 'class' => 'text-center')
		);
		// Create user headers for the last tier
		$headerUsers[] = array(__('User') => array('colspan' => 2, 'class' => 'text-center'));
		if ($managersCount > 0) {
			$headerUsers[] = array(__('Managers') => array('colspan' => $managersCount, 'class' => 'text-center'));
		}
		if ($partnersCount > 0) {
			$headerUsers[] = array(__('Partners') => array('colspan' => $partnersCount, 'class' => 'text-center'));
		}
		return array($headerTiers, $headerUsers);
	}
/**
 * Display the parameter value formatted according to the type
 *
 * @param array $tier tier value
 * @return string header label
 */
	protected function _getTierLabel($tier) {
		if ($tier === $this->settings['TierTable.overrideTier']) {
			return __('Manual override');
		}
		return __('Tier %s', $tier);
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
		} elseif ($valueType === $this->settings['valueTypePercentage']) {
			return $this->formatToPercentage($value);
		} else {
			return $this->Number->format($value);
		}
	}

/**
 * Utility to extract a is_multiple value and default to 0 if not found
 *
 * @param array $valueModelHeader Column information
 * @return int
 */
	protected function _getIsMultiple($valueModelHeader) {
		return Hash::get($valueModelHeader, $this->settings['Type.is_multiple']);
	}

/**
 * Find the residual parameter data for a specific header
 *
 * @param array $productsServicesType Product Service information
 * @param array $valueModelHeader Column information
 * @return array userParameterData
 */
	protected function _getValueModel($productsServicesType, $valueModelHeader) {
		$type = Hash::get($valueModelHeader, $this->settings['Type.type']);
		$tier = Hash::get($valueModelHeader, 'Tier');
		$associatedUserId = Hash::get($valueModelHeader, 'AssociatedUser.associated_user_id');
		$productsServicesTypeId = Hash::get($productsServicesType, 'ProductsServicesType.id');
		$isMultiple = $this->_getIsMultiple($valueModelHeader);
		$value = null;

		foreach ($valueModelHeader[$this->settings['valueModelAlias']] as $idx => $data) {
			if (!is_numeric($idx)) {
				return null;
			}
			if ($data['products_services_type_id'] === $productsServicesTypeId && $data['tier'] === $tier) {
				if ($type === $this->settings['typeSimple'] && $data['is_multiple'] == $isMultiple) {
					$value = $data;
					break;
				} elseif ($type === $this->settings['typeManager'] && $data['associated_user_id'] === $associatedUserId) {
					$value = $data;
					break;
				} elseif ($type === $this->settings['typePartner'] && $data['associated_user_id'] === $associatedUserId && $data['is_multiple'] == $isMultiple) {
					$value = $data;
					break;
				}
			}
		}
		return $value;
	}

/**
 * Display the model not common inputs for the current model
 *
 * @param array $valueModelHeader Column information
 * @param int $inputIndex Index on the array for saveMany
 * @return string with the html code of the imputs
 */
	protected function _getNotCommonInputs($valueModelHeader, $inputIndex) {
		$html = '';

		//input for tier
		$html .= $this->Form->input($this->settings['valueModelAlias'] . ".{$inputIndex}.tier", array(
			'type' => 'hidden',
			'value' => Hash::get($valueModelHeader, 'Tier'),
			'label' => false,
		));

		return $html;
	}

/**
 * Function to display the values from the "tier table" with the same format
 *
 * @param array $value Cell value
 * @param bool $nbsp to return a non-breaking space if $value is null
 * @return mixed
 */
	public function formatTierTableValue($value, $nbsp = true) {
		if ($value !== null) {
			return CakeNumber::precision($value, 2);
		} else {
			return ($nbsp) ? '&nbsp;' : null;
		}
	}

/**
 * Return an array with the headers for the tier table
 *
 * @return array
 */
	protected function _getTierTableHeaders() {
		return array(
			__('Tier'),
			__('Minimum Vol.'),
			__('Minimum GP'),
			__('Maximum Vol.'),
			__('Maximum GP')
		);
	}

/**
 * Add the tier table cell values to an existing row array
 *
 * @param array &$row With the row values
 * @param int $tier Tier number
 * @param array $tierTableData Tier table data
 * @return void
 */
	protected function _addTierTableRowValues(&$row, $tier, $tierTableData) {
		$row[] = $this->formatTierTableValue(Hash::get($tierTableData, "tier{$tier}_minimum_volume"));
		$row[] = $this->formatTierTableValue(Hash::get($tierTableData, "tier{$tier}_minimum_gp"));
		$row[] = $this->formatTierTableValue(Hash::get($tierTableData, "tier{$tier}_maximum_volume"));
		$row[] = $this->formatTierTableValue(Hash::get($tierTableData, "tier{$tier}_maximum_gp"));
	}

/**
 * Add the tier table cell inputs to an existing row array
 *
 * @param array &$row With the row values
 * @param int $tier Tier number
 * @return void
 */
	protected function _addTierTableRowInputs(&$row, $tier) {
		$tierTableModel = $this->settings['TierTable.model'];

		$row[] = $this->Form->input("{$tierTableModel}.tier{$tier}_minimum_volume", array('label' => false, 'wrapInput' => 'col col-md-7'));
		$row[] = $this->Form->input("{$tierTableModel}.tier{$tier}_minimum_gp", array('label' => false, 'wrapInput' => 'col col-md-7'));
		$row[] = $this->Form->input("{$tierTableModel}.tier{$tier}_maximum_volume", array('label' => false, 'wrapInput' => 'col col-md-7'));
		$row[] = $this->Form->input("{$tierTableModel}.tier{$tier}_maximum_gp", array('label' => false, 'wrapInput' => 'col col-md-7'));
	}

/**
 * Display the residual tier table
 *
 * @param string $action can be "view" or "edit"
 * @param array $tierTableData with the table values (only needed for "view")
 * @return string with html
 */
	public function tierTable($action = 'view', $tierTableData = null) {
		$headers = $this->_getTierTableHeaders();

		$definedTiers = $this->settings['TierTable.definedTiers'];

		$tableCells = array();
		for ($tier = 1; $tier <= $definedTiers; $tier++) {
			$row = array();
			$row[] = __('Tier') . ' ' . $tier;

			if ($action == 'edit') {
				$this->_addTierTableRowInputs($row, $tier);
			} else {
				$this->_addTierTableRowValues($row, $tier, $tierTableData);
			}
			$tableCells[] = $row;
		}

		echo $this->tableHeaders($headers);
		echo $this->tableCells($tableCells);
	}
}
