<?php
if ($modelName !== 'MerchantAchReason') {
	$assocDataFieldSettings = ['after' => "<br/><small class='text-muted text-danger'>Changes to this field may have negative effects.<br/>Request change from webmaster if needed.</small>"];
	$assocDataFieldSettings['disabled'] = 'disabled';
}
$fieldsAreCoustomizable = $this->request->data('ProductsServicesType.class_identifier') === Configure::read("App.productClasses.p_set.classId");
foreach ($fieldTypes as $fieldName => $type) {
	if (($fieldName === 'id' && empty($this->request->data("$modelName.id"))) || substr($fieldName, -7) === '_id_old' || substr($fieldName, -4) === '_old') {
		continue;
	}
	//For existing model records prevent editing/changing foreing key or associated model data.
	if (($modelName === 'MerchantAchReason' && $fieldName === 'non_taxable_reason_id') || (substr($fieldName, -3) === '_id' && !empty($this->request->data("$modelName.$fieldName")) && !empty($this->request->data("$modelName.id")))) {
		if ($modelName === 'MerchantAchReason' && $fieldName === 'non_taxable_reason_id') {
			$assocDataFieldSettings['label'] = 'Global Default Option for Non-Taxable reason.';
			$assocDataFieldSettings['empty'] = 'Select a default non taxable reason';
		}
		echo $this->Form->input("$modelName.$fieldName", $assocDataFieldSettings);
		continue;
	}
	//Special cases with ProductsServicesType model unserialized custom_labels
	if ($modelName === 'ProductsServicesType' && $fieldName === 'custom_labels' && $fieldsAreCoustomizable) {
		echo $this->MaintenanceDashboard->customLabelInputs($productSettingFields);
	} elseif ($modelName === 'ProductsServicesType' && $fieldName === 'class_identifier') {
		if (empty($this->request->data('ProductsServicesType.id'))) {
			//All new products being added will get the same class_identifier going forward
			$attributes = ['value' => Configure::read("App.productClasses.p_set.classId")];
			echo $this->MaintenanceDashboard->customLabelInputs($productSettingFields);
			echo $this->Form->hidden("$modelName.$fieldName", ['value' => Configure::read("App.productClasses.p_set.classId")]);
		}
	} elseif ($modelName === 'ProductsServicesType' && $fieldName === 'products_services_description') {
		if (empty($this->request->data('ProductsServicesType.id'))) {
			echo $this->Form->input("$modelName.$fieldName");
		} else {
			echo $this->Form->hidden("$modelName.$fieldName");
			echo $this->Html->tag('span', $this->request->data("$modelName.$fieldName"), ["style" => "font-size:13pt", "class" => "text-info strong"]);
		}
	} elseif ($modelName === 'MerchantAchReason' && $fieldName === 'accounting_report_col_alias') {
		echo $this->Form->input("$modelName.$fieldName", ['label' => 'Mapped Accounting Report column', 'empty' => '--none--']);
	} else {
		if ($fieldName !== 'custom_labels') {
			echo $this->Form->input("$modelName.$fieldName");
		}
	}
}