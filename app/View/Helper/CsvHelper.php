<?php
App::uses('AppHelper', 'Helper');

/**
 * Helper to create complex Csv views instead of using CsvView plugin defaults
 */
class CsvHelper extends AppHelper {

/**
 * @var array default settings to be merged with current settings
 */
	protected $_defaultSettings = [
		'delimiter' => ',',
		'enclosure' => '"',
		'newLine' => "\n",
	];

/**
 * Constructor
 *
 * @param \View $View View object
 * @param array $settings Configuration array
 */
	public function __construct(\View $View, $settings = []) {
		return parent::__construct($View, Hash::merge($this->_defaultSettings, $settings));
	}

/**
 * Return an empty csv line
 *
 * @return string
 */
	public function emptyRow() {
		return $this->settings['newLine'];
	}

/**
 * Convert an array into a csv row string
 *
 * @param array $rowData Row data
 * @return string
 */
	public function row($rowData) {
		if (empty($rowData)) {
			return $this->emptyRow();
		}
		$enclosure = $this->settings['enclosure'];

		foreach ($rowData as &$value) {
			if (is_numeric($value) && (strlen($value) >= 15)) {
				$value = "={$enclosure}{$value}{$enclosure}";
			} elseif (is_string($value)) {
				// Escape posible double quotation marks in the value
				$value = $enclosure . preg_replace('/"/', '""', $value) . $enclosure;
			}
		}
		return implode($this->settings['delimiter'], $rowData) . $this->settings['newLine'];
	}

/**
 * Convert an array of rows into a csv string
 *
 * @param array $rows Array with several rows for the csv
 * @return string
 */
	public function rows($rows) {
		if (empty($rows)) {
			return $this->emptyRow();
		}

		$csvString = '';
		foreach ((array)$rows as $row) {
			$csvString .= $this->row($row);
		}
		return $csvString;
	}

/**
 * Display csv file icon
 *
 * @param string $path Image path
 * @param array $options Image options
 * @return string
 */
	public function icon($path = null, $options = []) {
		if (empty($path)) {
			$path = 'csv.gif';
		}

		$defaultOptions = [
			'data-toggle' => 'tooltip',
			'data-placement' => 'top',
			'title' => __('Export to CSV'),
			'class' => 'icon pull-right'
		];
		return $this->Html->image($path, Hash::merge($defaultOptions, $options));
	}

/**
 * Display a link to export data into a csv file
 *
 * @param string $title Link title
 * @param mixed $url Link url
 * @param array $options Link options
 * @return string
 */
	public function exportLink($title = null, $url = null, $options = []) {
		if (empty($title)) {
			$title = $this->icon();
		}
		if (empty($url)) {
			if (!empty($this->request->query)) {
				$csvOutputParam = "&output=csv";
			} else {
				$csvOutputParam = "/csv";
			}
			$url = $this->request->here() . $csvOutputParam;
		}

		if (is_string($url)) {
			$url = Router::parse($url);
		}
		$defaultUrlParams = [
			'ext' => 'csv',
		];
		$url = Hash::merge($defaultUrlParams, $url);

		$defaultOptions = [
			'escape' => false,
			'confirm' => __('Export to csv?'),
		];
		$options = Hash::merge($defaultOptions, $options);
		return $this->Form->postLink($title, $url, $options);
	}
}
