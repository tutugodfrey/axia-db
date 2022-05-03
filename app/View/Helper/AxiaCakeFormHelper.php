<?php

App::uses('BoostCakeFormHelper', 'BoostCake.View/Helper');

/**
 * App Custom Form Helper
 */
class AxiaCakeFormHelper extends BoostCakeFormHelper {

/**
 * Default form options
 * @var array
 */
	public $defaults = array(
		'form' => array(
			'inputDefaults' => array(
				'div' => 'form-group',
				'wrapInput' => 'col col-md-7',
				'class' => 'form-control',
				'label' => array(
					'class' => 'col col-md-5 control-label'
				),
			),
			'class' => 'form-horizontal',
		),
		'checkbox' => array(
			'wrapInput' => 'col col-md-7 col-md-offset-5',
			'label' => null,
			'class' => null,
		),
		'date' => array(
			'before' => '<div class="date-input">',
			'separator' => '',
			'after' => '</div>',
		),
		'password' => array(
			'value' => '',
		),
	);

/**
 * Shortcut to return format datetime input
 *
 * @param string $fieldName field name
 * @param string $dateFormat date format
 * @param string $timeFormat time format
 * @param array $attributes attributes
 * @return string datetime input tag
 */
	public function dateTime($fieldName, $dateFormat = 'DMY', $timeFormat = '12', $attributes = array()) {
		if (!isset($attributes['monthNames'])) {
			$attributes['monthNames'] = array(
				'01' => __('Jan'),
				'02' => __('Feb'),
				'03' => __('Mar'),
				'04' => __('Apr'),
				'05' => __('May'),
				'06' => __('Jun'),
				'07' => __('Jul'),
				'08' => __('Aug'),
				'09' => __('Sep'),
				'10' => __('Oct'),
				'11' => __('Nov'),
				'12' => __('Dec'),
			);
		}

		return parent::dateTime($fieldName, $dateFormat, $timeFormat, $attributes);
	}

/**
 * Set default form options for styling forms
 *
 * @param type $model model
 * @param type $options options
 * @return string form tag
 */
	public function create($model = null, $options = array()) {
		return parent::create($model, Hash::merge($this->defaults['form'], $options));
	}

/**
 * Set default checkbox options for correct display
 *
 * @param type $fieldName field name
 * @param type $options options
 * @return string input tag
 */
	public function input($fieldName, $options = array()) {
		$inputOptions = $options;
		$type = Hash::get($options, 'type');
		if (in_array($type, array_keys($this->defaults))) {
			$inputOptions = Hash::merge($this->defaults[$type], $options);
		}
		return parent::input($fieldName, $inputOptions);
	}

/**
 * Display default Cancel / Submit buttons
 *
 * @param string $cancelId Id to the cancel button
 * @param mixed $cancelRedirectUrl URL string or array of location to redirect on cancell
 * @return string button tags
 */
	public function defaultButtons($cancelId = null, $cancelRedirectUrl = null) {
		$html = '<div class="form-group">';
		$html .= '<div class="col col-md-9 col-md-offset-3">';
		if (empty($cancelId)) {
			$cancelId = CakeText::uuid();
		}
		$cancellOptns = array(
			'class' => 'btn btn-danger',
			'id' => $cancelId
		);
		if (empty($cancelRedirectUrl)) {
			$cancelRedirectUrl = 'javascript:void(0);';
			$cancellOptns['onclick'] = 'history.go(-1);';
		}
		$html .= $this->Html->link(__('Cancel'), $cancelRedirectUrl, $cancellOptns);
		$html .= $this->submit(__('Submit'), array(
			'div' => false,
			'class' => 'btn btn-success'
				)
		);
		$html .= '</div>';
		$html .= '</div>';
		return $html;
	}

/**
 * Display an input created with a HTML code string
 *
 * @param string $fieldName field name
 * @param integer $options options
 * @return string html input tag
 */
	public function htmlInput($fieldName, $options = array()) {
		$nameParts = explode('.', $fieldName);
		$inputId = '';
		$inputName = 'data';
		foreach ($nameParts as $namePart) {
			$inputId .= Inflector::camelize($namePart);
			$inputName .= "[{$namePart}]";
		}

		$defaultOptions = array(
			'class' => 'form-control',
			'step' => 'any',
			'type' => 'number',
		);
		$options = Hash::merge($defaultOptions, $options);
		$inputOptions = '';
		foreach ($options as $name => $value) {
			$inputOptions .= " {$name}='{$value}'";
		}

		return "<input name='{$inputName}' id='{$inputId}' {$inputOptions}>";
	}

/**
 * compactDate input renderer
 *
 * @param string $fieldName field name
 * @param array $options options
 * @return string html input tag
 */
	public function compactDate($fieldName, $options) {
		$dateFormat = 'MDY';
		$options['separator'] = '';
		$currentClass = Hash::get($options, 'class');
		$options['class'] = $currentClass . ' input-compact';
		return '<div>' . $this->dateTime($fieldName, $dateFormat, null, $options) . '</div>';
	}

/**
 * Create a form with the default input options for filters
 *
 * @param string $model Model name
 * @param array $options Form options
 *
 * @return string html form tag
 */
	public function createFilterForm($model = null, $options = array()) {
		$defaultOptions = array(
			'inputDefaults' => array(
				'div' => 'form-group',
				'label' => array('class' => 'col col-xs-12 control-label'),
				'wrapInput' => false,
				'class' => 'form-control'
			),
			'type' => 'get',
			'class' => 'well well-sm form-inline'
		);
		return parent::create($model, hash::merge($defaultOptions, $options));
	}

/**
 * Create a a complex user input that shows Entity -> Parent User -> User
 *
 * @param type $fieldName field name
 * @param type $options options
 * @return string input tag
 */
	public function complexUserInput($fieldName, $options = array()) {
		$defaultOptions = array(
			'type' => 'select',
			'label' => __('Entity/Rep'),
			'empty' => false,
			'required' => false
		);
		//Set default option if empty and is allowed access to this RBAC Module (admins only)
		if ($this->_View->Rbac->isPermitted('app/actions/Users/view/module/defaultValueUserFilterModule', true) && empty($this->request->query('user_id'))) {
			$defaultOptions['empty'] = true;
			$defaultOptions['value'] = User::PREFIX_ENTITY . ':' . Entity::AX_PAY_ID;
		}

		return parent::input($fieldName, hash::merge($defaultOptions, $options));
	}

/**
 * dmyDate input renderer
 *
 * @param string $fieldName field name
 * @param array $options options
 * @return string html input tag
 */
	public function dmyDate($fieldName, $options = array()) {
		$defaultOptions = array(
			'maxYear' => date('Y') + 1,
			'style' => "font-size:8pt",
			'dateFormat' => 'MDY',
			'empty' => true
		);
		$inputOptions = $options;
		$type = Hash::get($options, 'type');
		if (in_array($type, array_keys($this->defaults))) {
			$inputOptions = Hash::merge($this->defaults[$type], $options);
		}
		return parent::input($fieldName, array_merge($defaultOptions, $inputOptions));
	}

/**
 * toggleSwitch input renderer
 * Renders a slider toggle on/off switch input
 * (Requires css styles defined in webroot/css/custom-input.css)
 *
 * Supported options for label position in relation to the input switch are "top", "left", "right".
 * Default label position is top
 * 
 * @param string $fieldName field name
 * @param array $options options
 * @return string html input tag
 */
	public function toggleSwitch($fieldName, $options = array()) {
		$defaultOptions = array(
			'label_position' => 'top',
			'label_text' => Inflector::humanize($fieldName),
		);
		$initStateOn = false;
		if (Hash::get($options, 'checked') == 'checked' || Hash::get($options, 'checked') == true) {
			$initStateOn = true;
		}
		$options = array_merge($defaultOptions, $options);

		$labelTextHtml = '<strong class="text-success small" style="vertical-align: top;">';
		$labelTextHtml .= ($options['label_position'] === 'top')? "{$options['label_text']}<br/></strong>" : " {$options['label_text']} </strong>";

		$inputHtml = '<span class="input-sm text-center">';
		$inputHtml .= '<label class="control-label">';
		$inputHtml .= ($options['label_position'] !== 'right')? $labelTextHtml : null;
		$inputHtml .= '<label class="switch">';
		$inputHtml .= $this->checkbox($fieldName, array('label' => false, 'wrapInput' => false, 'div' => false, 'class' => false, 'checked' => $initStateOn));
		$inputHtml .= '<span class="slider round" name="sliderControlObj"></span>';
		$inputHtml .= '</label>';
		$inputHtml .= ($options['label_position'] === 'right')? $labelTextHtml : null;
		$inputHtml .= '</label>';
		$inputHtml .= '</span>';

		return $inputHtml;
	}

}
