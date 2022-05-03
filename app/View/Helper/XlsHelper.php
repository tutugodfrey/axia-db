<?php

App::uses('AppHelper', 'Helper');
App::import('Vendor', 'PHPExcel/PHPExcel');

class XlsHelper extends AppHelper {

	/**
	 * Current sheet
	 * 
	 * @var PHPExcel
	 */
	protected $_sheet;

	/**
	 * Set the PHPExcel
	 * 
	 * @param PHPExcel $sheet
	 * 
	 * @return void
	 */
	public function setSheet(PHPExcel $sheet) {
		$this->_sheet = $sheet;
	}

	/**
	 * Get the sheet object
	 * 
	 * @return PHPExcel
	 */
	public function getSheet() {
		if (is_null($this->_sheet)) {
			$this->__loadPHPExcel();
			$this->setSheet(new PHPExcel());
		}

		return $this->_sheet;
	}

	/**
	 * Load the PHPExcel lib
	 * 
	 * @throws CakeException When the PHPExcel is not at vendor
	 * 
	 * @return void
	 */
	private function __loadPHPExcel() {
		if (!class_exists('PHPExcel')) {
			throw new CakeException(__('Vendor class PHPExcel not found!'));
		}
	}

	/**
	 * Call the PHPExcel lib
	 *
	 * @param string $method
	 * @param array $args
	 * @throws BadMethodCallException
	 * @return mixed
	 */
	public function __call($method, $args) {
		if (!method_exists($this->getSheet(), $method)) {
			throw new BadMethodCallException(__('The given method %s does not exists', $method));
		}

		return call_user_func_array(array($this->getSheet(), $method), $args);
	}

	/**
	 * Get the XLS generated
	 *
	 * @param string $str
	 * @return string XLS generated
	 */
	public function output($str) {
		ob_start();

		$objWriter = PHPExcel_IOFactory::createWriter($this->getSheet(), 'Excel2007');
		$objWriter->save('php://output');
		$this->getSheet()->disconnectWorksheets();

		return ob_get_clean();
	}

}
