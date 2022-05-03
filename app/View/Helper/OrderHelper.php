<?php

App::uses('AppHelper', 'View/Helper');
App::uses('FlagStatusLogicComponent', 'Controller/Component');
App::uses('GUIbuilderComponent', 'Controller/Component');

/**
 * Custom helper for Orders
 */
class OrderHelper extends AppHelper {

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
			'hiddenField' => false,
			'legend' => false,
			'wrapInput' => 'form-control input-sm',
			'div' => false,
			'options' => array(
				GUIbuilderComponent::STATUS_PENDING => $this->Html->showStatus(GUIbuilderComponent::STATUS_PENDING) . ' ',
				GUIbuilderComponent::STATUS_INVOICED => $this->Html->showStatus(GUIbuilderComponent::STATUS_INVOICED) . ' ',
				GUIbuilderComponent::STATUS_PAID => $this->Html->showStatus(GUIbuilderComponent::STATUS_PAID) . ' ',
			),
			'default' => GUIbuilderComponent::STATUS_PENDING,
		);

		return $this->Form->input($fieldName, Hash::merge($defaultOptions, $options));
	}
}
