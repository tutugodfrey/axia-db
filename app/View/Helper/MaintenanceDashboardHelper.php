<?php

App::uses('AppHelper', 'View/Helper');

/**
 * Custom helper for merchant notes
 */
class MaintenanceDashboardHelper extends AppHelper {

/**
 * Display database table data content
 *
 * @param array $fieldNames containing all field names from ProductSettings model
 * @return string with html
 */
	public function customLabelInputs($fieldNames) {
		//Spacer
		echo $this->Html->tag('div', "<br />");
		$title = __("The following inputs should only be used when the product supports custom field labels. If you set this product's labeles here, they will be displayed accordingly in all merchant profiles that have this product.") .
				"<br>" .
				__("To use a different label, enter it in the text field. The new label must be consistent with the original purpose of the field, which is indicated by the original label name shown in bold letters.") . "<span class='text-danger'> (This has no effect on products that don't support custom labels.)</span>";
		$panelTitle = $this->Html->tag('div', $title, ["class" => "panel-heading"]);
		$fields = '';
		foreach ($fieldNames as $name) {
			if ($name !== 'id' && substr($name, - 3) !== '_id') {
				$fields .= $this->Form->input("ProductsServicesType.custom_labels.$name", ['placeholder' => 'Enter Custom Label Name']);
			}
		}

		$pBody = $this->Html->tag('div', $fields, ["class" => "panel-body"]);
		echo $this->Html->tag('div', $panelTitle . $pBody, ["class" => "panel panel-info"]);
	}

/**
 * Display database table data content
 *
 * @param array $data containing all table data
 * @param string $modelName name of the model used to retrieved the data
 * @return string with html
 */
	public function getTableContent($data = null, $modelName = '') {
		$records = Hash::extract($data, "{n}.$modelName");
		$assocData = Hash::remove($data, "{n}.$modelName");
		if (empty($records)) {
			return "<div class = 'text-center alert alert-danger'><h5>" . __("Error: No records were returned from Model $modelName!") . "</h5></div>";
		}

		echo "<h6><strong>" . __("Select a Record to edit:") . "</strong></h6>";
		$tableCells = [];
		foreach ($records as $record) {
			$row = [];
			$recordId = Hash::get($record, 'id');
			unset($record['id']);
			foreach ($record as $key => $val) {
				if (stripos($key, "id_old") !== false || substr($key, -4) === '_old') {
					continue;
				}

				if (is_array($val)) {
					$val = join($val, ',');
				}
				//Check ForeingKeys
				if (!empty($assocData) && substr($key, -3) === '_id') {
					$assocRecord = Hash::extract($assocData, "{n}.{s}[id=$val]");
					$assocRecord = array_pop($assocRecord);

					if (!empty($assocRecord)) {
						unset($assocRecord['id']);
						//If there is a desciptive name use it otherwise show all associated data
						if (array_key_exists('products_services_description', $assocRecord)) {
							$val = $assocRecord['products_services_description'];
						} else {
							$val = (array_key_exists('name', $assocRecord))? $assocRecord['name'] : join($assocRecord, ' - ');
						}
					}
				}
				$row[] = $this->Html->link($val, ["controller" => "MaintenanceDashboards", 'action' => 'edit', $recordId, $modelName]);
			}
			if ($this->_View->Rbac->isPermitted('MaintenanceDashboards/delete')) {
				$row[] = $this->Form->postLink($this->Html->image("/img/redx.png", array("title" => "Delete record", "class" => "icon")),
					array('controller' => 'MaintenanceDashboards', 'action' => 'delete', $recordId, $modelName),
					array('escape' => false, 'confirm' => __('Permanently delete record? (This cannot be undone!)')));
			}
			$tableCells[] = $row;
		}
		return $this->Html->tableCells($tableCells);
	}
}
