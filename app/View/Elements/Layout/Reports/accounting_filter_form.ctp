<?php
echo $this->element('Layout/selectizeAssets');
?>
<div>
		<?php
		if (!empty($exportLinks)) {
			echo '<span class="pull-left well-sm">';
			echo "<strong>Export Data:</strong><br>";
			echo $this->element('Layout/exportCsvUi', compact('exportLinks'));
			echo '</span>';
		}
		?>
		<?php
		echo $this->Form->createFilterForm(Inflector::singularize($this->name));
		echo '<div>';
		echo $this->Form->complexUserInput('user_id', array("class" => "single col col-xs-12", "style" => "min-width:200px"));
		//CommissionReport model is the only one that creates the $partbers variable and when curent user is a partner this will be empty and we do not want to display if
		if (isset($partners) && !empty($partners)) {
			echo $this->Form->input('partners', array('style' => 'max-width:200px', 'label' => __('Partner'), 'empty' => true));
		}
		echo $this->Form->input('dba_mid', [
			'label' => __('Merchant')
		]);
		echo $this->element('Forms/OrganizationDrilldown');
		echo "</div>";

		echo "<div>";
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
		echo $this->Form->input('status', array('empty' => 'All'));
		echo $this->Form->submit(__('Generate'), [
			'div' => ['class' => 'form-group'],
			'before' => ($this->action === 'grossProfitReport')? $this->Form->toggleSwitch('roll_up_view', array('label_text' => 'Rollup View On/Off', 'label_position' => 'top')) : null,
			'class' => 'btn btn-default'
		]);
		echo "</div>";
		echo $this->Form->end();
		?>
</div>