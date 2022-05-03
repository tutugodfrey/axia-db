<?php
App::uses('Component', 'Controller');

class CsvHandlerComponent extends Component {
/**
 * csv_to_array method
 *
 * Converts a csv file to array.<br />
 * The file must have a row-column structure where the first line/row of the csv file contains all the csv headers,<br />
 * which will be used as the keys for all the arrays that are created for subsequent non-empty lines of values in the file.<br />
 * An array will be created for each non-empty file lines and appended as a new indexed element to the end of the return array.
 *
 * @param string $fileName (Required) the auto generated (tmp_name) file name, NOT the actual file name.
 * @param string $delimeter (Optional) if provided this will be used
 * @return mixed array/boolean False if no data was found in the file or a two dimentional array with a top indexed dimmention.
 */
	public function csv_to_array($fileName, $delimeter = null) {
		//Open file
		$file = new SplFileObject($fileName);
		if (isset($delimeter)) {
			$file->setCsvControl($delimeter);
		}
		$csvHeaders = $file->fgetcsv();
		while (!$file->eof()) {
			$csvValues = $file->fgetcsv();
			//Iff current CSV line does not return an empty array then add it to the data set
			if (count(Hash::filter($csvValues)) > 0) {
				array_walk($csvValues, 'trim');
				//Build data structure superset
				$csvDataSet[] = array_combine($csvHeaders, $csvValues);
			}
		}
		//Close file
		unset($file);

		if (empty($csvDataSet)) {
			return false;
		}
		return $csvDataSet;
	}

/** saveCsvStrToFile method
 * Saves a delimited string as a csv file
 *
 * @param string $fileName
 * @param type $csvStr
 * @return boolean
 */
	function saveCsvStrToFile($fileName, $csvStr) {
		$this->autoRender = false;
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename=' . $fileName . '.csv');
		header('Content-Length: ' . strlen($csvStr));
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		echo($csvStr);
	}

}
?>