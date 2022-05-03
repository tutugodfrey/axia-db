<?php
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
		echo $this->Form->createFilterForm('CommissionPricing');
		if ($this->action == 'commission_multiple_analysis') {
			echo $this->Form->input('products_services_type_id', [
				'label' => __('Product'),
				'empty' => __('All products'),
			]);
		}
		echo $this->Form->complexUserInput('user_id', ["class" => "single col col-xs-12", "style" => "min-width:200px"]);
		echo $this->Form->input('merchant_dba', [
			'label' => __('Merchant DBA')
		]);
		if (!empty($partners)) {
			echo $this->Form->input('partners', [
				'options' => $partners,
				'label' => __('Partner'),
				'empty' => __('--'),
				"style" => "max-width:200px"
			]);
		}
		echo $this->element('Forms/OrganizationDrilldown');
		if ($this->action == 'commission_multiple_analysis') {
			echo '<div></div>';
			echo $this->Form->input('date_type');
		}
		echo $this->Form->input('from_date', [
			'label' => ($this->action == 'commission_multiple_analysis')? 'Month/Year' : 'Went Live on:',
			'type' => 'date',
			'dateFormat' => 'MY',
			'maxYear' => date('Y')
		]);
		$resMonths = range(1, 18);
		echo $this->Form->input('res_months', ['wrapInput' => 'col-md-offset-2', 'label' => 'Residual Months', 'options' => array_combine($resMonths, $resMonths)]);
		echo $this->Form->submit(__('Generate'), [
			'div' => ['class' => 'form-group'],
			'class' => 'btn btn-default'
		]);
		echo $this->Form->end();
		?>
	</div>
</div>