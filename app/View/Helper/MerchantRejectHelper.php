<?php

App::uses('BoostCakeHtmlHelper', 'BoostCake.View/Helper');

/**
 * Custom helper for merchant reject reports
 */
class MerchantRejectHelper extends BoostCakeHtmlHelper {

	public $helpers = array('Form');

/**
 * Display the open status from a merchant reject
 *
 * @param bool $openStatus With the current merchant reject status
 *
 * @return string with the HTML code
 */
	public function showOpenStatus($openStatus) {
		if ($openStatus) {
			return $this->tag('span', __('open'), array('class' => 'label label-primary'));
		} else {
			return $this->tag('span', __('closed'), array('class' => 'label label-danger'));
		}
	}

/**
 * Display the open status input for a merchant reject
 *
 * @param string $fieldName This should be "Modelname.fieldname"
 * @param array $options To modify the default options
 *
 * @return string with the formated date, null if the date variable is empty
 */
	public function openStatusInput($fieldName, $options = array()) {
		$defaultOptions = array(
			'type' => 'radio',
			'legend' => false,
			'options' => array(
				1 => __('Open') . '<br/>',
				0 => __('Closed')
			),
		);
		return $this->tag('div', $this->Form->input($fieldName, Hash::merge($defaultOptions, $options)), array('class' => 'open-status-input-block'));
	}

/**
 * Display the delete button for merchant reject lines
 *
 * @param mixed $url Url for the delete action
 * @param array $options To modify the default options
 *
 * @return string with the formated date, null if the date variable is empty
 */
	public function ajaxDelete($url, $options = array()) {
		$image = $this->image('/img/icon_trash.gif', array(
			'title' => __('Delete'),
			'class' => 'icon'
		));
		$defaultOptions = array(
			'escape' => false,
			'class' => 'delete-merchant-rejects',
			'data-target' => Router::url($url),
			'confirm' => __('Are you sure you want to delete the reject line?')
		);

		return $this->link($image, '#', Hash::merge($defaultOptions, $options));
	}
}
