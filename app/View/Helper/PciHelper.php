<?php
App::uses('AppHelper', 'Helper');

class PciHelper extends AppHelper {

/**
 * Get the validation type
 * 
 * @param array $pci PCI data
 * @return string Validation type or false
 */
	public function getValidationType($pci) {
		if (!empty($pci['SaqControlScan']['saq_type'])) {
			return $pci['SaqControlScan']['saq_type'];
		}

		if (!empty($pci['MerchantPci']['saq_type'])) {
			return $pci['MerchantPci']['saq_type'];
		}

		return $this->none();
	}

/**
 * Check if user has complete the qualification
 * 
 * @param array $pci PCI data
 * @return bool true if complete otherwise false
 */
	public function hasCompleteQualification($pci) {
		return !empty($pci['SaqMerchant']['id'])
			&& !empty($pci['SaqMerchant']['LastSaqPrequalification']['date_completed'])
			&& empty($pci['SaqControlScan']['creation_date']);
	}

/**
 * Format the date for the PCI format used
 * 
 * @param string $date the date to format
 * @return string date formated
 */
	public function dateFormat($date) {
		return date('M j, Y', strtotime($date));
	}

/**
 * Check if PCI has control scan date
 * 
 * @param array $pci PCI data
 * @return bool
 */
	public function hasControlScanDate($pci) {
		return !empty($pci['SaqControlScan']['creation_date']);
	}

/**
 * Check if PCI has complete SAQ
 * 
 * @param array $pci PCI data
 * @return bool true if complete otherwise false
 */
	public function hasCompleteSaq($pci) {
		return !empty($pci['SaqMerchant']['LastSaqMerchantSurveyXref']);
	}

/**
 * getEmailLabelById method
 * 
 * @param array $data PCI data
 * @return string|bool
 */
	public function getEmailLabelById($data) {
		return SaqMerchantPciEmail::getLabelById($data['saq_merchant_pci_email_id']);
	}

/**
 * none method this method will return an element with 'none' as value
 *
 * @return string
 */
	public function none() {
		return '<em>None</em>';
	}

/**
 * Retrive saq complete date
 * 
 * @param array $data PCI data
 * @return string|bool
 */
	public function getSAQCompletedDate($data) {
		if (!empty($data['MerchantPci']['saq_completed_date'])) {
			return $data['MerchantPci']['saq_completed_date'];
		}

		if (!empty($data['SaqControlScan']['first_scan_date'])) {
			return $data['SaqControlScan']['first_scan_date'];
		}

		if (!empty($data['SaqMerchant']['id'])) {
			return $data['SaqMerchant']['LastSaqMerchantSurveyXref']['datecomplete'];
		}

		return false;
	}
}
