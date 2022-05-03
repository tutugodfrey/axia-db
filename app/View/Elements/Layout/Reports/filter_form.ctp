<?php
if ($this->action === 'grossProfitReport') {
	echo $this->AssetCompress->css('custom-form-inputs', array(
			'raw' => (bool)Configure::read('debug')
		));
}
echo $this->element('Layout/selectizeAssets');
if (!isset($formModel)) {
	$formModel = Inflector::singularize($this->name);
}
?>
<div class="row">
	<div class="col-xs-12">
		<?php
		if (!empty($exportLinks)) {
			echo '<span class="pull-left well-sm">';
			echo "<strong>Export Data:</strong><br>";
			echo $this->element('Layout/exportCsvUi', compact('exportLinks'));
			echo '</span>';
		}
		?>
		<?php
		echo $this->Form->createFilterForm($formModel);
		if ($this->request->controller !== 'CommissionReports') {
			echo $this->Form->input('products_services_type_id', [
				'label' => __('Product'),
				'empty' => __('All products'),
			]);
		}
		echo $this->Form->complexUserInput('user_id', array("class" => "single col col-xs-12", "style" => "min-width:200px"));
		//CommissionReport model is the only one that creates the $partbers variable and when curent user is a partner this will be empty and we do not want to display if
		if (isset($partners) && !empty($partners)) {
			echo $this->Form->input('partners', array('style' => 'max-width:200px', 'label' => __('Partner'), 'empty' => true));
		}
		echo $this->Form->input('merchant_dba', [
			'label' => __('Merchant')
		]);
		echo $this->element('Forms/OrganizationDrilldown');
		// echo "<div></div>";
		echo $this->Form->input('from_date', [
			'type' => 'date',
			'dateFormat' => 'MY',
			'maxYear' => date('Y')
		]);
		echo $this->Form->input('end_date', [
			'type' => 'date',
			'dateFormat' => 'MY',
			'maxYear' => date('Y')
		]);
		echo $this->Form->submit(__('Generate'), [
			'div' => ['class' => 'form-group'],
			'before' => ($this->action === 'grossProfitReport')? $this->Form->toggleSwitch('roll_up_view', array('label_text' => 'Rollup View On/Off', 'label_position' => 'top')) : null,
			'class' => 'btn btn-default'
		]);
		echo $this->Form->end();
		?>
	</div>
</div>