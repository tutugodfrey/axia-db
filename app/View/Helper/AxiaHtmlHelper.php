<?php

App::uses('BoostCakeHtmlHelper', 'BoostCake.View/Helper');
// App::uses('RbacHelper', 'Rbac.View/Helper');
App::uses('CakeNumber', 'Utility');
App::uses('FlagStatusLogicComponent', 'Controller/Component');
App::uses('GUIbuilderComponent', 'Controller/Component');

/**
 * App Custom Form Helper
 */
class AxiaHtmlHelper extends BoostCakeHtmlHelper {

	const CHECKED_MARK = '&#9745;';
	const NOT_CHECKED_MARK = '&#9744;';
	const TOGGLE_TABLE_MULTIPLE_HEADER_ROWS = 'multipleHeaderRows';

/**
 * Minimum of rows to show the table paginator
 *
 * @var int
 */
	const SHOW_PAGINATOR_MINIMUM_ROWS = 20;

/**
 *
 * @var colGroups
 */
	public $colGroups = [
		'prGpColGroup' => 'colPrGroup1', // profitability report collapsible column group
		'repColGroup' => 'colGroup1',
		'pRepColGroup' => 'colGroup2',
		'smColGroup' => 'colGroup3',
		'sm2ColGroup' => 'colGroup4',
		'prtnrColGroup' => 'colGroup5',
		'refColGroup' => 'colGroup6',
		'resColGroup' => 'colGroup7',
	];

/**
 * Display a checkbox value, based on the $check status, will render the label and the checked or notChecked value
 *
 * @param mixed $check to test against
 * @param string $label will be translated using __()
 * @param string $checkedValue to render if the check is true will be translated using __()
 * @param string $notCheckedValue to render if the check is false will be translated using __()
 * @return string;
 */
	public function checkboxValue($check, $label = '', $checkedValue = '', $notCheckedValue = '') {
		$out = '';
		if (!empty($label)) {
			$out = $this->tag('b', __($label)) . ': ';
		}
		if ($check) {
			$out .= "<span class='glyphicon glyphicon-check text-success'></span>" . ' ' . __($checkedValue);
		} else {
			$out .= "<span class='glyphicon glyphicon-unchecked text-muted'></span>" . ' ' . __($notCheckedValue);
		}
		return $out;
	}

/**
 * Display a table, using toggle buttons to show/hide specific columns
 *
 * @param type $headers Table headers
 * @param type $cells Table cells
 * @param type $toggles Toggles columns
 * @param type $options Table options
 * @return string
 */
	public function toggleTable($headers = array(), $cells = array(), $toggles = array(), $options = array()) {
		if (empty($headers) && empty($cells)) {
			return '';
		}
		$html = '';
		$html .= $this->_processToggles($toggles, $headers, $cells, $options);
		$html .= '<table class="toggle-table table-responsive table-bordered table-striped">';
		if (Hash::get($options, self::TOGGLE_TABLE_MULTIPLE_HEADER_ROWS) === true) {
			foreach ($headers as $headersRow) {
				$html .= $this->tableHeaders($headersRow);
			}
		} else {
			$html .= $this->tableHeaders($headers);
		}
		$html .= $this->tableCells($cells);
		$html .= '</table>';
		$html .= $this->script('toggle-table', array('inline' => false));
		return $html;
	}

/**
 * Process the toggles: add a toggle button for each, inject classes to allow hide/show columns
 *
 * @param array $toggles array of label, column index, example
 * 			$toggles = array(
 *				array(
 *					'label' => 'label2',
 *					'column' => 1,
 *				)
 *			);
 *
 * @param type &$headers Table headers
 * @param type &$cells Table cells
 * @param type &$options Table options
 * @todo fix buttons inside form to preventDefault
 * @return string
 */
	protected function _processToggles($toggles, &$headers, &$cells, &$options) {
		$html = '';
		foreach ($toggles as $toggle) {
			$toggleId = CakeText::uuid();
			$html .= '<div class="row">';
			$html .= '<div class="col-md-2 btn-group btn-toggle toggle-table-controls">';
			$html .= '<button class="btn btn-default" data-toggle="collapse" data-target=".' . $toggleId . '">On</button>';
			$html .= '<button class="btn btn-primary active" data-toggle="collapse" data-target=".' . $toggleId . '">Off</button>';
			$html .= '</div>';
			$html .= '<div class="col-md-10 toggle-table-controls-label">';
			$html .= $this->tag('label', h($toggle['label']));
			$html .= '</div>';
			$html .= '</div>';
			//add class to headers we need to check if there are multiple headers rows or only 1
			if (Hash::get($options, self::TOGGLE_TABLE_MULTIPLE_HEADER_ROWS) === true) {
				//@todo
			} else {
				$column = $toggle['column'];
				if (isset($headers[$column])) {
					if (is_array($headers[$column])) {
						$headers[$column]['class'] = Hash::get($headers[$column], 'class') . ' collapse ' . $toggleId;
					} else {
						$headers[$column] = array($headers[$column] => array('class' => $toggleId . ' collapse'));
					}
				}
			}
			//add class to cells
			foreach ($cells as &$cellRow) {
				if (isset($cellRow[$column])) {
					if (is_array($cellRow[$column])) {
						$cellRow[$column][1] = Hash::merge($cellRow[$column][1], array('class' => Hash::get($cellRow[$column][1], 'class') . ' collapse ' . $toggleId));
					} else {
						$cellRow[$column] = array($cellRow[$column], array('class' => $toggleId . ' collapse'));
					}
				}
			}
		}
		return $html;
	}

/**
 * Display a section header with an edit "pencil" icon
 *
 * @param string $label Section title
 * @param array $url Image url
 * @return string
 */
	public function sectionHeader($label = '', $url = array()) {
		$html = '';
		$html .= h($label);
		if (!empty($url)) {
			$options = array(
				'title' => h($label),
				'url' => $url,
			);
			$html .= $this->editImage(array('title' => h($label), 'url' => $url));
		}
		return $html;
	}

/**
 * Display the edit icon
 *
 * @param string $imagePath Image path
 * @param array $options options array for the image
 * @return string
 */
	public function editIcon($imagePath = null, $options = array()) {
		$defaultOptions = array(
			"class" => "icon",
		);
		$options = array_merge($defaultOptions, $options);
		if (empty($imagePath)) {
			$imagePath = 'editPencil.gif';
		}

		return $this->image($imagePath, $options);
	}

/**
 * Display an edit "pencil" icon
 *
 * @param array $options options array for the image
 * @param array $imgFile optional image file name
 * @return string
 */
	public function editImage($options = array(), $imgFile = '') {
		$defaultOptions = array(
			'title' => __('Edit'),
			'alt' => __('Edit'),
			'class' => 'icon',
		);
		$imgFile = (empty($imgFile))? 'editPencil.gif' : $imgFile;
		if (is_array($options)) {
			$options = array_merge($defaultOptions, $options);
		}
		return $this->image($imgFile, $options);
	}

/**
 * Display a decrypting "eye" icon
 *
 * @param string $actionPath path to use for ajax request to decrypt value. Must start with '/Users/checkDecrypPassword/ ... '
 * @param array $options options array for the image
 * @return string
 */
	public function modalDecryptIcon($actionPath, $options = array()) {
		$defaultOptions = array(
			"title" => "Show full number",
			"class" => "glyphicon glyphicon-eye-open",
			"data-toggle" => "modal",
			"data-target" => "#myModal",
			'onClick' => "renderContentAJAX('', '', '', 'ModalContainer', '" . $actionPath . "')");
		if (is_array($options)) {
			$options = array_merge($defaultOptions, $options);
		}
		return $this->link("", "javascript:void(0)", $options);
	}

/**
 * Display an edit "pencil" icon link
 *
 * @param array $url link url
 * @param array $options options passed to link()
 * @param array $imgFile optional image file name
 * @return string Html tag for an edit link
 */
	public function editImageLink($url, $options = array(), $imgFile = '') {
		$defaults = array(
			'escape' => false,
		);
		$options = Hash::merge($defaults, $options);
		return $this->link($this->editImage(array(), $imgFile), $url, $options);
	}

/**
 * Display a percentage number with 2 precision digits and the symbol "%" at the end
 *
 * @param array $value Percentage value
 * @param string $precision Number precision
 * @param bool $nbsp to return a non-breaking space if $value is null
 * @return string
 */
	public function formatToPercentage($value, $precision = 2, $nbsp = true) {
		if ($value !== null) {
			return CakeNumber::toPercentage($value, $precision);
		} else {
			return ($nbsp) ? '&nbsp;' : null;
		}
	}

/**
 * Set the breadcrumbs trail to the current action
 *
 * @param object(CakeRequest) $requestParams with the info of the current request
 * @param array $data with aditional info for the link labels
 * @param array $refererText to change the default text of the referer url (the current controller name)
 * @param array $refererUrl to change the default referer url (the index action to the current controller)
 * @return string
 */
	public function setBreadcrumbs($requestParams, $data = array(), $refererText = null, $refererUrl = array()) {
		// Reset the previous defined crumb trail
		$this->_crumbs = array();

		$plugin = false;
		if (!empty($requestParams['plugin'])) {
			$plugin = $requestParams['plugin'];
		}

		if (empty($refererText)) {
			$refererText = $requestParams['controller'];
		}

		if (empty($refererUrl)) {
			$refererUrl = array(
				'plugin' => $plugin,
				'controller' => $requestParams['controller'],
				'action' => 'index'
			);
		}
		$this->addCrumb($refererText, $refererUrl);

		$linkText = $requestParams['action'];
		if (isset($data['User'])) {
			$linkText .= ' ' . $data['User']['user_first_name'] . ' ' . $data['User']['user_last_name'];
		}
		if (isset($refererUrl['controller']) && ($refererUrl['controller'] != $requestParams['controller'])) {
			// Add the controller name to the text when the referer controller is not the same as the current action controller
			$linkText = $requestParams['controller'] . ' ' . $linkText;
		}
		$this->addCrumb($linkText, $this->params->here);
	}

/**
 * Display the breadcrumb trail
 *
 * @return string with the HTML of the breadcrumb trail
 */
	public function displayCrumbs() {
		$options = array(
			'text' => 'Home',
			'url' => array('controller' => 'dashboards', 'action' => 'home'),
			'escape' => false
		);
		return $this->getCrumbs($this->image('right-arrow.jpg'), $options);
	}

/**
 * Display the paginator if the number of rows is greater than the limit
 *
 * Useful when you want to display a paginator on top and other on the bottom of the table,
 * but want to hide one if there are few rows
 *
 * @param int $totalRows Total rows to display on the table
 * @param int $minimumRows Minimum of rows to display the paginator
 * @return null|string with the paginator html
 */
	public function showPaginator($totalRows, $minimumRows = self::SHOW_PAGINATOR_MINIMUM_ROWS) {
		if ($totalRows >= $minimumRows) {
			return $this->_View->element('pagination');
		}
		return null;
	}

/**
 * Display a status that several application entities can use
 *
 * @param string $status status to display
 * @param bool/string $addLabel Add the status label next to the icon.
 *  If its true, display the default label. Can display a custom label passing a string
 * @param array $imageOptions Options for the status icon
 *
 * @return string with the HTML code
 */
	public function showStatus($status, $addLabel = true, $imageOptions = array()) {
		if (empty($status)) {
			return null;
		}
		$defaultImageOptions = array(
			'class' => 'icon'
		);
		$imageOptions = Hash::merge($defaultImageOptions, $imageOptions);

		$html = $this->image(FlagStatusLogicComponent::getThisStatusFlag($status), $imageOptions);
		if ($addLabel) {
			if (is_string($addLabel)) {
				$label = $addLabel;
			} else {
				$label = GUIbuilderComponent::getStatusLabel($status);
			}
			$html .= "&nbsp;{$label}";
		}
		return $html;
	}
/**
 * Display product elements if they exist in Layout/Merchant/ProductsAndServices.
 * The element name must match the product name minus spaces, periods or any other delimeters i.e.:
 * "Axia Product Name" -> AxiaProductName.ctp
 * Similar to camel case except when all or part of the Product Name is in all caps.
 *
 * @param array $mProducts containing Merchant data with associated product models' data and associated ProductsServicesType data
 * @return string HTML code
 */
	public function renderProductElements(array $mProducts) {
		if (empty($mProducts["ProductsAndService"]) || empty(Hash::extract($mProducts, "ProductsAndService.{n}.ProductsServicesType.id"))) {
			return null;
		}

		$elmntCount = 1;
		$html = '';
		foreach ($mProducts['ProductsAndService'] as $productType) {
			$prodClassName = Configure::read("App.productClasses.{$productType['ProductsServicesType']['class_identifier']}.className");
			$productName = $productType['ProductsServicesType']['products_services_description'];
			$panelBodyHtmlId = str_shuffle("ABC0123456789");
			$elementVars = array(
				"customLabels" => $productType['ProductsServicesType']['custom_labels'],
				"panelBodyHtmlId" => $panelBodyHtmlId);
			if ($prodClassName === 'ProductSetting') {
				if (empty($mProducts['ProductSetting'])) {
					continue;
				}
				$viewElement = $prodClassName;
				$psettId = Hash::extract($mProducts, "ProductSetting.{n}[products_services_type_id={$productType['ProductsServicesType']['id']}]");
				$elementVars['productSetting'] = array_pop($psettId);
			} else {
				/* Replace all spaces periods and dashes with underscore */
				$regExPattern = '/(\s+|\.+|\-+)/';
				$viewElement = preg_replace($regExPattern, '_', $productName);
				$viewElement = Inflector::camelize($viewElement);
			}

			/* There are more products than there is elements */
			if ($this->_View->elementExists('Layout/Merchant/ProductsAndServices/' . $viewElement)) {
				$html .= '<div class="col-md-6">
					<div class="panel panel-primary">
						<div class="contrTitle">
							<span class="panel-title">
								<a href="javascript:void(0)">';
								$html .= $this->tag('span', null, ['onClick' => "objSlider('$panelBodyHtmlId', 500);rotateThis(this, 180, 500)", 'class' => 'glyphicon glyphicon-chevron-up']);
								$html .= $this->tag('/span');
								$html .= '</a>';
								$html .= h($productName) . "&nbsp";
								$html .= '<span class="pull-right">';
									if ($this->_View->Rbac->isPermitted('ProductsAndServices/delete')) {
										$html .= $this->Form->postLink($this->Html->image("green_orb.gif", array(
													"data-toggle" => "tooltip", "data-placement" => "top", "data-original-title" => "Deactivate $productName",
													"class" => "icon",
													'onmouseover' => 'this.src=\'/img/red_orb.png\'', 'onmouseout' => 'this.src=\'/img/green_orb.gif\'')), array(
														'controller' => 'ProductsAndServices',
														'action' => 'delete',
														$productType['id'],
														$mProducts['Merchant']['id']
													),
													array('escape' => false, 'confirm' => __('Are you sure you want to remove %s?', h($productName))));
									}
						$html .= '</span>';
					$html .= '</span>';
				$html .= '</div>';
				$html .= $this->tag('div',
					$this->_View->element('Layout/Merchant/ProductsAndServices/' . $viewElement, $elementVars),
					['id' => $panelBodyHtmlId, 'class' => 'panel-body']);
				$html .= '</div>
					</div>';
				//Insert bootstrap css element break-point every two iterations to make sure elemens line up in pairs
				if ($elmntCount % 2 === 0) {
					$html .= "<div class='clearfix'></div>";
				}
				$elmntCount++;
			}
		}
		return $html;
	}

/**
 * ajaxContentRefresh
 * Outputs an action link that can refresh any content that was render using ajax.
 * Important: This content refresher link must be contained within and be part of the content being refreshed.
 * This is useful when the data being diaplayed was changed elsewere.
 *
 * @param string $controller Controller name
 * @param string $ajaxAction Action that handles the ajax request and accepts the argumants in $args array
 * @param array $args Arguments to pass to the Controller action
 * @param string $htmlElementId the id of element where the updated HTML will be injected during the ajax response
 * @param string $loadSeqMessage an optional string to display as the load sequence while wating for the ajax request response to complete.
 *								The loa
 * @return string HTML code
 */
	public function ajaxContentRefresh($controller, $ajaxAction, array $args, $htmlElementId, $loadSeqMessage = null) {
		$paramStr = implode('/', $args);
		if (empty($loadSeqMessage)) {
			$loadSeqMessage = ' - Refreshing ...';
		}
		echo $this->Html->link('<img src="/img/Button-Refresh.png" data-toggle="tooltip" data-placement= "bottom" title= "Refresh Data">', 'javascript:void(0)',
			['escape' => false,
			'onClick' => "renderContentAJAX('$controller', '$ajaxAction', '$paramStr', '$htmlElementId'); appendHTMLContent(this, ' $loadSeqMessage <img src=/img/indicator.gif>', 1)"]);
	}

/**
 * getColGroups
 *
 * @return array
 */
	public function getColGroups() {
		return $this->colGroups;
	}
/**
 * getCollapsibleCel
 *
 * @param string $reportKey the key used when the report data was generated
 * @param atring $reportVal the value used when the report data was generated
 * @return array with table cell properties  
 */
	public function getCollapsibleCel($reportKey, $reportVal = null) {
		$colGroups = $this->colGroups;
		switch ($reportKey) {
			case 'pr_total_partner_gp':
			case 'pr_total_sm_gp':
			case 'pr_total_sm2_gp':
			case 'pr_total_referrer_gp':
			case 'pr_total_reseller_gp':
			case 'pr_total_rep_gp':
				return [$reportKey => [$reportVal, ['name' => $colGroups['prGpColGroup'], 'class' => 'hidden']]];
			case 'r_per_item_fee':
			case 'r_statement_fee':
			case 'rep_gross_profit':
			case 'rep_pct_of_gross':
			case 'r_profit_pct':
			case 'r_rate_pct':
				return [$reportKey => [$reportVal, ['name' => $colGroups['repColGroup'], 'class' => 'hidden']]];
			case 'manager_rate':
			case 'manager_per_item_fee':
			case 'manager_statement_fee':
			case 'manager_gross_profit':
			case 'manager_profit_pct':
			case 'manager_pct_of_gross':
				return [$reportKey => [$reportVal, ['name' => $colGroups['smColGroup'], 'class' => 'hidden']]];
			case 'manager2_rate':
			case 'manager2_per_item_fee':
			case 'manager2_statement_fee':
			case 'manager2_gross_profit':
			case 'manager_profit_pct_secondary':
			case 'manager2_pct_of_gross':
				return [$reportKey => [$reportVal, ['name' => $colGroups['sm2ColGroup'], 'class' => 'hidden']]];
			case 'partner_rate';
			case 'partner_per_item_fee';
			case 'partner_statement_fee';
			case 'partner_gross_profit';
			case 'partner_pct_of_gross';
			case 'partner_profit_pct';
				return [$reportKey => [$reportVal, ['name' => $colGroups['prtnrColGroup'], 'class' => 'hidden']]];
			case 'referrer_rate':
			case 'referrer_per_item_fee':
			case 'referrer_statement_fee':
			case 'referrer_gross_profit':
			case 'refer_profit_pct':
			case 'referer_pct_of_gross':
				return [$reportKey => [$reportVal, ['name' => $colGroups['refColGroup'], 'class' => 'hidden']]];
			case 'reseller_rate':
			case 'reseller_per_item_fee':
			case 'reseller_statement_fee':
			case 'reseller_gross_profit':
			case 'res_profit_pct':
			case 'reseller_pct_of_gross':
				return [$reportKey => [$reportVal, ['name' => $colGroups['resColGroup'], 'class' => 'hidden']]];
			default:
				return [$reportKey => $reportVal];
		}
	}
/**
 * formatPhone
 * Formats a phone number by adding dashes ###-###-#### 
 *
 * @param atring $phoneStr a phone nubmer as string
 * @return string the formatted phone number
 */
	public function formattedPhone($phoneStr) {
		if (empty($phoneStr)) {
			return '';
		}
		return h(preg_replace('~(\d{3})[^\d]*(\d{3})[^\d]*(\d{4})$~', '$1-$2-$3', $phoneStr));
	}
}
