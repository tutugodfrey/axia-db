<?php
App::uses('ModelBehavior', 'Model');
App::uses('Validation', 'Utility');

/**
 * Behavior to manage the filter conditions by date ranges based on year and month parameters
 *
 * Provide methods that return filter conditions to can be used with Searchable behavior
 */
class SearchByMonthYearBehavior extends ModelBehavior {

/**
 * settings
 *
 * @var array
 */
	public $settings = [];

/**
 * Default settings
 * - `fulldateFieldName`: model field with the full date to filter.
 *		If false, 'year_field' and 'month_field' will be used instead.
 * - `yearFieldName': model field with the year to filter
 * - `monthFieldName': model field with the month to filter
 *
 * Array with the paths to obtain the search parameters to be applied
 * - `searchParams` => [
 *		- `fromYearPath`: Path in the search data array to obtain the "from year"h
 *		- `fromMonthPath`: Path in the search data array to obtain the "from month"
 *		- `endYearPath`: Path in the search data array to obtain the "end year"
 *		- `endMonthPath`: Path in the search data array to obtain the "end month"
 *	]
 * @var array
 */
	protected $_defaults = [
		'fulldateFieldName' => false,
		'yearFieldName' => 'year',
		'monthFieldName' => 'month',

		'searchParams' => [
			'fromYearPath' => 'from_date.year',
			'fromMonthPath' => 'from_date.month',
			'endYearPath' => 'end_date.year',
			'endMonthPath' => 'end_date.month',
		]
	];

/**
 * Model configuration
 *
 * @param Model $Model Model
 * @param array $config Config parameters
 * @return void
 */
	public function setup(Model $Model, $config = []) {
		parent::setup($Model, $config);
		$this->settings[$Model->alias] = Hash::merge($this->_defaults, $config);
	}

/**
 * Validates behavior settings
 *
 * @param Model $Model Model
 * @return void
 * @throws OutOfBoundsException When a configured field does not exist in the model
 */
	protected function _validateSettings(Model $Model) {
		$fulldateField = Hash::get($this->settings, "{$Model->alias}.fulldateFieldName");
		if (!empty($fulldateField)) {
			if (!$Model->hasField($fulldateField)) {
				throw new OutOfBoundsException(__('The fulldateFieldName \'%s\' does not exists in model %s', $fulldateField, $Model->alias));
			}
		} else {
			$yearField = Hash::get($this->settings, "{$Model->alias}.yearFieldName");
			if (!$Model->hasField($yearField)) {
				throw new OutOfBoundsException(__('The yearFieldName \'%s\' does not exists in model %s', $yearField, $Model->alias));
			}
			$monthField = Hash::get($this->settings, "{$Model->alias}.monthFieldName");
			if (!$Model->hasField($monthField)) {
				throw new OutOfBoundsException(__('The monthFieldName \'%s\' does not exists in model %s', $monthField, $Model->alias));
			}
		}
	}

/**
 * Given a year and a month, check if it's a valid date
 *
 * @param Model $Model Model
 * @param string $year Year to check
 * @param int|string $month Month to check
 * @return bool
 */
	protected function _checkValidDate(Model $Model, $year, $month) {
		$date = "{$year}-{$month}";
		return Validation::date($date, 'ym');
	}

/**
 * Makes sure the month value has two digits for correct sql comparisons
 *
 * @param Model $Model Model
 * @param int|string $month Month to parse
 * @return string
 */
	protected function _parseMonth(Model $Model, $month) {
		if ($month < 10) {
			$month = '0' . (int)$month;
		}
		return $month;
	}

/**
 * Utility function to format the starting date of a range given the year and the month for sql conditions
 *
 * @param Model $Model Model
 * @param int|string $year Start year
 * @param int|string $month Start month
 * @throws OutOfBoundsException
 * @return string
 */
	public function buildStartDate(Model $Model, $year, $month) {
		$month = $this->_parseMonth($Model, $month);
		if (!$this->_checkValidDate($Model, $year, $month)) {
			throw new OutOfBoundsException(__('Invalid date'));
		}
		return "{$year}-{$month}-01";
	}

/**
 * Utility function to format the ending date of a range given the year and the month for sql conditions
 * The 't' date format parameter return the number of days in the month (also can be used as the last day of the month)
 *
 * @param Model $Model Model
 * @param int|string $year End year
 * @param int|string $month End month
 * @return string
 */
	public function buildEndDate(Model $Model, $year, $month) {
		$date = new DateTime($this->buildStartDate($Model, $year, $month));
		return $date->format('Y-m-t');
	}

/**
 * Get model alias for the query conditions
 *
 * @param Model $Model Model
 * @param string $queryAlias Model alias to use on the query replacing the default model alias
 * @return array conditions array
 */
	protected function _getQueryAlias(Model $Model, $queryAlias) {
		if (!empty($queryAlias) && is_string($queryAlias)) {
			return $queryAlias;
		}

		return $Model->alias;
	}

/**
 * Date start conditions for searchable behavior
 * Depending on configuration, will use fulldate or month/year filters
 *
 * @param Model $Model Model
 * @param array $data Data submitted for search args
 * @param string $queryAlias Model alias to use on the query replacing the default model alias
 * @return array conditions array
 */
	public function dateStartConditions(Model $Model, $data = [], $queryAlias = null) {
		$this->_validateSettings($Model);

		if (!empty($this->settings[$Model->alias]['fulldateFieldName'])) {
			return $this->_fulldateStartConditions($Model, $data, $queryAlias);
		}
		return $this->_monthYearStartConditions($Model, $data, $queryAlias);
	}

/**
 * Date start conditions for fulldate fields
 *
 * @param Model $Model Model
 * @param array $data Data submitted for search args
 * @param string $queryAlias Model alias to use on the query replacing the default model alias
 * @return array conditions array
 */
	protected function _fulldateStartConditions(Model $Model, $data = [], $queryAlias = null) {
		$year = Hash::get($data, $this->settings[$Model->alias]['searchParams']['fromYearPath']);
		$month = Hash::get($data, $this->settings[$Model->alias]['searchParams']['fromMonthPath']);
		$fulldateField = $this->settings[$Model->alias]['fulldateFieldName'];
		$modelAlias = $this->_getQueryAlias($Model, $queryAlias);
		return [
			"{$modelAlias}.{$fulldateField} >=" => $this->buildStartDate($Model, $year, $month),
		];
	}

/**
 * Date start conditions for month and year fields
 *
 * @param Model $Model Model
 * @param array $data Data submitted for search args
 * @param string $queryAlias Model alias to use on the query replacing the default model alias
 * @return array conditions array
 */
	protected function _monthYearStartConditions(Model $Model, $data = [], $queryAlias = null) {
		$year = Hash::get($data, $this->settings[$Model->alias]['searchParams']['fromYearPath']);
		$month = Hash::get($data, $this->settings[$Model->alias]['searchParams']['fromMonthPath']);
		$fromDate = $this->buildStartDate($Model, $year, $month);

		$yearField = $this->settings[$Model->alias]['yearFieldName'];
		$monthField = $this->settings[$Model->alias]['monthFieldName'];
		$modelAlias = $this->_getQueryAlias($Model, $queryAlias);
		return "make_date({$modelAlias}.{$yearField}::integer, {$modelAlias}.{$monthField}::integer, 1) >= '{$fromDate}'";
	}

/**
 * Date end conditions for searchable behavior
 * Depending on configuration, will use fulldate or month/year filters
 *
 * @param Model $Model Model
 * @param array $data Data submitted for search args
 * @param string $queryAlias Model alias to use on the query replacing the default model alias
 * @return array conditions array
 */
	public function dateEndConditions(Model $Model, $data = [], $queryAlias = null) {
		$this->_validateSettings($Model);

		if (!empty($this->settings[$Model->alias]['fulldateFieldName'])) {
			return $this->_fulldateEndConditions($Model, $data, $queryAlias);
		}
		return $this->_monthYearEndConditions($Model, $data, $queryAlias);
	}

/**
 * Date end conditions for fulldate fields
 *
 * @param Model $Model Model
 * @param array $data Data submitted for search args
 * @param string $queryAlias Model alias to use on the query replacing the default model alias
 * @return array conditions array
 */
	protected function _fulldateEndConditions(Model $Model, $data = [], $queryAlias = null) {
		$year = Hash::get($data, $this->settings[$Model->alias]['searchParams']['endYearPath']);
		$month = Hash::get($data, $this->settings[$Model->alias]['searchParams']['endMonthPath']);
		$fulldateField = $this->settings[$Model->alias]['fulldateFieldName'];
		$modelAlias = $this->_getQueryAlias($Model, $queryAlias);
		return [
			"{$modelAlias}.{$fulldateField} <=" => $this->buildEndDate($Model, $year, $month),
		];
	}

/**
 * Date end conditions for month and year fields
 *
 * @param Model $Model Model
 * @param array $data Data submitted for search args
 * @param string $queryAlias Model alias to use on the query replacing the default model alias
 * @return array conditions array
 */
	protected function _monthYearEndConditions(Model $Model, $data = [], $queryAlias = null) {
		$year = Hash::get($data, $this->settings[$Model->alias]['searchParams']['endYearPath']);
		$month = Hash::get($data, $this->settings[$Model->alias]['searchParams']['endMonthPath']);
		$endDate = $this->buildEndDate($Model, $year, $month);

		$yearField = $this->settings[$Model->alias]['yearFieldName'];
		$monthField = $this->settings[$Model->alias]['monthFieldName'];
		$modelAlias = $this->_getQueryAlias($Model, $queryAlias);
		return "make_date({$modelAlias}.{$yearField}::integer, {$modelAlias}.{$monthField}::integer, 28) <= '{$endDate}'";
	}
}
