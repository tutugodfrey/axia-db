<?php
App::uses('RepProfileHelper', 'View/Helper');
App::uses('UserParameterType', 'Model');

/**
 * Custom Helper
 * Extending the rep profile helper with specific params
 */
class UserParameterHelper extends RepProfileHelper {

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
		'valueTypeCheckbox' => UserParameterType::VALUE_TYPE_CHECKBOX,
		'typeSimple' => UserParameterType::TYPE_SIMPLE,
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
}
