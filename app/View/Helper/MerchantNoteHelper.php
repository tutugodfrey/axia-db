<?php

App::uses('AppHelper', 'View/Helper');

/**
 * Custom helper for merchant notes
 */
class MerchantNoteHelper extends AppHelper {

/**
 * Set default checkbox options for correct display at MerchantNote forms
 *
 * @param type $fieldName field name
 * @param type $options options to overwrite the defaults
 *
 * @return string with the checkbox input html
 */
	public function checkbox($fieldName, $options = array()) {
		$defaultOptions = array(
			'type' => 'checkbox',
			'label' => array('class' => false),
			'wrapInput' => false,
			'class' => 'merchant-note-checkbox'
		);

		$labelOptions = Hash::get($options, 'label');
		if (is_string($labelOptions)) {
			$options['label'] = array('text' => $labelOptions);
		}
		$options = Hash::merge($defaultOptions, $options);

		return $this->Form->input($fieldName, $options);
	}

/**
 * Display the input to select the MerchantNote status
 *
 * @param type $fieldName field name
 * @param type $options options to overwrite the defaults
 *
 * @return string with the checkbox input html
 */
	public function statusInput($fieldName, $options = array()) {
		$defaultOptions = array(
			'type' => 'radio',
			'legend' => false,
			'options' => array(
				GUIbuilderComponent::STATUS_PENDING => $this->Html->showStatus(GUIbuilderComponent::STATUS_PENDING),
				GUIbuilderComponent::STATUS_COMPLETED => $this->Html->showStatus(GUIbuilderComponent::STATUS_COMPLETED),
			),
			'default' => GUIbuilderComponent::STATUS_PENDING,
		);

		return $this->Form->input($fieldName, Hash::merge($defaultOptions, $options));
	}

/**
 * Return the url to the list of merchant notes.
 * Filter parameters can be applied to the url
 *
 * @param string $merchantId Merchant id
 * @param array $parameters Merchant Note filter parameters
 *
 * @return string
 */
	public function getMerchantNotesUrl($merchantId, $parameters = array()) {
		return array(
			'plugin' => false,
			'controller' => 'merchants',
			'action' => 'notes',
			$merchantId,
			'?' => $parameters
		);
	}
/**
 * Returns a formated date, and if a timestamp is in the Date Time string param then a formated time is also returned
 * Return Format with ti'M d, Y g:i a'
 *
 * @param string $dateTime Merchant id
 * @return string Return Format Jan 01, 2025 11:00am time excluded if param is only a date.
 */
	public function noteDateTime($dateTime) {
		if (empty($dateTime)) {
			return null;
		}
		$date = new DateTime($dateTime);
		$format = (preg_match("/00:00:00/", $dateTime) === 1)? 'M d, Y' : 'M d, Y g:i a';
		return $date->format($format);
		// return $this->Time->format($tFormat, $dateTime);
	}

/**
 * Creates link button element to be used to add a note using ajax methods.
 * The button will be created without a onclick event handler, that should be attached from a JS file.
 * The link button will have a default element id of  
 *
 * @param string $labelStr text to display on the button
 * @param string $elemId Merchant id
 * @param string $toolTipStr the text for alt attribute for the button image
 * @param array $settings settings for link button element
 * @return string the HTML link button
 */
	public function addAjaxNoteButton($labelStr, $elemId = '', $toolTipStr = '', $settings = []) {
		if (empty($labelStr)) {
			$labelStr = "Add Note ";
		}
		if (empty($elemId)) {
			$elemId = 'add_note_ajax_1';
		}
		$defaultSettings = array('id' => $elemId, 'class' => 'btn-success btn-xs', 'escape' => false);
		if (!empty($settings)) {
			$settings = array_merge($defaultSettings, $settings);
		}

		$label = $labelStr . $this->Html->image("newNote.png", array('class' => 'icon', "title" => $toolTipStr));

		return $this->Html->link($label, '#', $settings);
	}
}
