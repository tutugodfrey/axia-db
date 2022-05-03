<?php
App::uses('ResidualGridHelper', 'View/Helper');

/**
 * Custom Helper
 * Extending the ResidualGrid helper with specific params
 */
class ResidualTimeGridHelper extends ResidualGridHelper {

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
		'valueModelAlias' => 'ResidualTimeParameter',
		'parameterTypeField' => 'residual_parameter_type_id',
		//settings for the residual tier table
		'TierTable.model' => 'ResidualTimeFactor',
		'TierTable.definedTiers' => ResidualTimeFactor::DEFINED_TIERS,
		'TierTable.overrideTier' => null,
	);

/**
 * Function to display the values from the "tier table" with the same format
 *
 * @param array $value Cell value
 * @param bool $nbsp to return a non-breaking space if $value is null
 * @return mixed
 */
	public function formatTierTableValue($value, $nbsp = true) {
		if ($value !== null) {
			return h($value);
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
			__('Begin Months'),
			__('End Months'),
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
		$row[] = $this->formatTierTableValue(Hash::get($tierTableData, "tier{$tier}_begin_month"));
		$row[] = $this->formatTierTableValue(Hash::get($tierTableData, "tier{$tier}_end_month"));
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

		$row[] = $this->Form->input("{$tierTableModel}.tier{$tier}_begin_month", array('label' => false, 'wrapInput' => 'col col-md-7'));
		$row[] = $this->Form->input("{$tierTableModel}.tier{$tier}_end_month", array('label' => false, 'wrapInput' => 'col col-md-7'));
	}
}
